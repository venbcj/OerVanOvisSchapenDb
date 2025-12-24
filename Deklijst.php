<?php

require_once("autoload.php");

$versie = '19-10-2016';
$versie = '5-11-2016'; /* include func_euro toegevoegd */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '06-02-2022'; /* SQL beveiligd met quotes */
$versie = '07-05-2023'; /* De beide variabele dek_bedrag) <> euro_format(liq_bedrag in de controle letop was niet altijd 2 decimale. Dit is aangepast met de functie  euro_format() */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD align = "center" valign = "top"> 31-12-24 include login voor include header gezet */
$versie = '09-03-2025'; /* veld txtId_Id verwijderd <input type = hidden name = <?php echo "txtId_Id"; ?> size = 1 value = <?php echo Id; ?> >*/
Session::start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Financieel</title>
</head>
<body>
<?php
$titel = 'Deklijst';
$file = "Deklijst.php";
include "login.php"; ?>
        <TD align = "center" valign = "top">
<?php
if (Auth::is_logged_in()) {
    if ($modfin == 1) {
        $deklijst_gateway = new DeklijstGateway();
        $element_gateway = new ElementGateway();
        $liquiditeit_gateway = new LiquiditeitGateway();
        $rubriek_gateway = new RubriekGateway();
        $historie_gateway = new HistorieGateway();
        $opgaaf_gateway = new OpgaafGateway();
        $schaap_gateway = new SchaapGateway();
        $stal_gateway = new StalGateway();
        $volwas_gateway = new VolwasGateway();
        require_once "func_euro.php";
        $lastjaar = $deklijst_gateway->zoek_laatste_jaar($lidId);
        if (isset($lastjaar)) {
            $new_jaar = $lastjaar + 1;
        } else {
            $new_jaar = date('Y');
        }
        if (isset($_POST['kzlJaar_'])) {
            $kzlJaar = $_POST['kzlJaar_'];
        } elseif (isset($lastjaar) && $lastjaar < date('Y')) {
            $kzlJaar = $lastjaar;
        } else {
            $kzlJaar = date('Y');
        }
        if (isset($_POST['knpCreate_'])) {
            $year = $new_jaar;
            include "create_deklijst.php";
        }
        //  3 Componenten ophalen : Prijs per lam, worpgrootte en sterfte
/**************
    Prognose
***************/
        [$prijs_nm, $prijs_val] = $element_gateway->zoek_prijs_lam($lidId);
        [$worp_nm, $worp_val] = $element_gateway->zoek_worpgrootte($lidId);
        [$sterf_nm, $sterf_val] = $element_gateway->zoek_sterfte($lidId);
/********************
    Einde Prognose
*********************/
        // Einde 3 Componenten ophalen : Prijs per lam, worpgrootte en sterfte
        if (isset($_POST['knpSave_'])) {
            include "save_deklijst.php";
            $prijs = $prijs_val;
            $worp = $worp_val;
            $sterf = $sterf_val;
            $kzlJaar = $_POST['kzlJaar_'];
            // Toevoegen jaar in tblLiquiditeit indien noodzakelijk
            $maxjaar = $deklijst_gateway->zoek_max_dekjaar($lidId, $kzlJaar);
            $exist_jaar = $liquiditeit_gateway->zoek_jaar($lidId, $maxjaar);
            if ($exist_jaar == 0) { // Toevoegen elke eerste van de maand van het jaar $maxjaar per rubuId. Dus zo'n 600 records (50 x 12)
                $zoek_rubuId = $rubriek_gateway->zoek_rubuId($lidId);
                while ($rub = $zoek_rubuId->fetch_assoc()) {
                    $rubuId = $rub['rubuId'];
                    for ($i = 1; $i <= 12; $i++) {
                        $firstMonth = $maxjaar . '-' . $i . '-01'; // loop van 12 strings jaar - maandnr - 01
                        $date = date_create($firstMonth);
                        $day = date_format($date, 'Y-m-d'); // datum formaat
                        $liquiditeit_gateway->insert($rubuId, $day);
                    }
                }
            }
            // Einde Toevoegen jaar in tblLiquiditeit indien noodzakelijk
            // Jaar-maand uit tblDeklijst ophalen incl. totaal aan dekaantallen
            $zoek_afvoermaanden = $deklijst_gateway->zoek_afvoermaanden($lidId, $kzlJaar);
            while ($month = $zoek_afvoermaanden->fetch_assoc()) {
                $jr_mnd = $month['afvmnd'];
                $werptot_p = ($month['dektot'] * $worp) - ($month['dektot'] * $sterf / 100);
                $day = $jr_mnd . '-01';
                $bedrag = $werptot_p * $prijs;
                // Jaar-maand uit tblDeklijst ophalen incl. totaal aan dekaantallen
                $liquiditeit_gateway->update_datum_bedrag($lidId, $day, $bedrag);
            }
        } // Einde if(isset($_POST['knpSave_']))
        //  3 Componenten ophalen : Prijs per lam, worpgrootte en sterfte
/**************
    Realiteit
***************/
        // Prijs per lam realiteit
        $jaaraflevering = $historie_gateway->zoek_aantal_afleveren_per_jaar($lidId, $kzlJaar);
        $rubuId = $rubriek_gateway->zoek_rubriek_verkooplammeren($lidId);
        $jaaropbrengst = $opgaaf_gateway->jaaropbrengst($rubuId, $kzlJaar);
        if ($jaaraflevering > 0) {
            $prijs_per_lam_r = round($jaaropbrengst / $jaaraflevering, 2);
        } else {
            $prijs_per_lam_r = 0;
        }
        // Einde Prijs per lam realiteit
        // Worpgrootte realiteit
        $jaarworp = $schaap_gateway->jaarworp($lidId, $kzlJaar);
        $jaargeboortes = $stal_gateway->jaargeboortes($lidId, $kzlJaar);
        if ($jaarworp > 0) {
            $worpgrootte_r = round($jaargeboortes / $jaarworp, 2);
        } else {
            $worpgrootte_r = 0;
        }
        // Einde Worpgrootte realiteit
        // Sterfte lammeren realiteit
        $jaarsterfte = $stal_gateway->jaarsterfte($lidId, $kzlJaar);
        if ($jaargeboortes > 0) {
            $sterfte_r = round($jaarsterfte / $jaargeboortes * 100, 2);
        } else {
            $sterfte_r = 0;
        }
        // Einde Sterfte lammeren realiteit
/********************
    Einde Realiteit
*********************/
        //  Einde 3 Componenten ophalen : Prijs per lam, worpgrootte en sterfte
        // Controle of saldo deklijst gelijk is aan saldo Liquiditeit
        $year = $deklijst_gateway->zoek_dekjaar($lidId, $kzlJaar);
        $dektot_p = $year['dektot'];
        $werptot_p = ($year['dektot'] * $worp_val) - ($year['dektot'] * $sterf_val / 100);
        $dek_bedrag = ($werptot_p * $prijs_val);
        $liq_bedrag = ($year['bedrag']);
        // Einde Controle of saldo deklijst gelijk is aan saldo Liquiditeit
        if (($kzlJaar >= Date('Y') && euro_format($dek_bedrag) <> euro_format($liq_bedrag)) || ( $dtb == "k36098_bvdvschapendbs" && $lidId == 1 )) {
            $letop = "De Liquiditeit wijkt af van de prognose deklijst." . '<br>' . "Klik op 'Opslaan' om liquiditeit bij te werken met deze deklijst.";
        }
        // Declaratie kzlJaar
        $kzl_jaar = $deklijst_gateway->kzlJaar($lidId);
        $index = 0;
        $jaarNr = [];
        while ($kzljr = $kzl_jaar->fetch_assoc()) {
            $jaarNr[$index] = $kzljr['jaar'];
            $jaarRaak[$index] = $kzlJaar;
            $index++;
        }
        // Einde Declaratie kzlJaar
?>
<form method="post">
<table border="0"> <!-- Tabel Prognose en Realiteit -->
<tr>
 <td colspan="4">Deklijst aanmaken : </td>
 <td colspan="5">
    <input type = submit name = 'knpCreate_' value = <?php echo $new_jaar ; ?> >
 </td>
 <td colspan="4" align= "center";><b style = "font-size:18px;" >Jaar</b>
        <!-- KZLJAAR -->
         <select style="width:65;" name= "kzlJaar_" >
<?php
        $count = count($jaarNr);
        $Id = ''; // @TODO: #0004200 variabele lijkt overbodig
        for ($i = 0; $i < $count; $i++) {
            $opties = array($jaarNr[$i] => $jaarNr[$i]);
            foreach ($opties as $key => $waarde) {
                if ((!isset($_POST['knpToon_']) && $jaarRaak[$i] == $key) || (isset($_POST["kzlJaar_$Id"]) && $_POST["kzlJaar_$Id"] == $key)) {
                    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
                } else {
                    echo '<option value="' . $key . '" >' . $waarde . '</option>';
                }
            }
        }
?>
 </select>
             <!-- EINDE KZLJAAR -->
        <input type = submit name = "knpToon_" value = 'Toon' >
 </td>
</tr>
<tr>
 <td colspan = 9 align = center valign = "top" style = "color : 'red'; " >
<?php
        if (isset($letop)) {
            echo $letop . '<br>';
        } ?>
 </td>
</tr>
<tr>
    <td colspan="20"><hr></td>
</tr>
<tr>
 <td colspan="2">
<?php
            if ($kzlJaar >= Date('Y') || ( $dtb == "k36098_bvdvschapendbs" && $lidId == 1 )) {
?>
<input type="submit" name = "knpSave_" value= "Opslaan" >
<?php
            }
        if ($dtb == "k36098_bvdvschapendbs" && $lidId == 1 && $kzlJaar < Date('Y')) {
            echo "Voorgaande jaren zijn normaal gesproken niet te wijzigen. ";
        }
?>
 </td>
 <td colspan="6" height="70" align="center" valign="center" style = "font-size:30px;"><b> Prognose </b>
 </td>
 <td colspan="3"> <?php
        echo $prijs_nm . " &euro; " . $prijs_val . "<br>";
        echo $worp_nm . '  ' . $worp_val . "<br>";
        echo $sterf_nm . ' ' . $sterf_val . ' % ' . "<br>"; ?>
 </td>
 <td rowspan="120" style = "text-align:center; border-left: 2px solid;"></td> <!-- verticale scheidingslijn -->
 <td colspan="7" height="70" align="center" valign="center" style = "font-size:30px;"><b> Realiteit </b>
 </td>
 <td colspan="2"> <b style = "font-size:12px;"> <?php
        echo 'Boekjaar ' . $kzlJaar . '<br>'; ?> </b> <?php
            echo $prijs_nm . " &euro; " . $prijs_per_lam_r . "<br>";
        echo $worp_nm . '  ' . $worpgrootte_r . "<br>";
        echo $sterf_nm . ' ' . $sterfte_r . ' % ' . "<br>";?>
 </td>
</tr>
<tr> <!-- Regel om eerste 2 kolommen op te splitsen in 3 kolommen zoadt KnpSave_ tot halverwege kolom Dekdatum valt -->
 <td width= 30></td> <!-- Kolom Dekweek prognose -->
 <td width= 30></td> <!-- Kolom Dekdatum prognose -->
 <td width= 30></td> <!-- Kolom Dekdatum prognose -->
 <td></td> <!-- Kolom Aantal dekkingen prognose -->
 <td width= 40></td> <!-- Kolom Werpweek prognose -->
 <td width= 60></td> <!-- Kolom Werpdatum prognose -->
 <td></td> <!-- Kolom Aantal lamm. prognose -->
 <td ></td> <!-- Kolom Speendatum prognose -->
 <td width= 60></td> <!-- Kolom Afleverdatum prognose -->
 <td width= 80></td> <!-- Kolom Prognose 2022 -->
 <td></td> <!-- extra kolom om ruimte te creëren -->
 <td width= 30></td> <!-- Kolom Dekweek realiteit -->
 <td width= 60></td> <!-- Kolom Week van ... dekdatum realiteit -->
 <td></td> <!-- Kolom Aantal dekkingen realiteit -->
 <td width= 40></td> <!-- Kolom Werpweek realiteit -->
 <!-- <td width= 80></td> --> <!-- Kolom Week van ... werpdatum realiteit -->
 <td ></td> <!-- Kolom Aantal lamm. realiteit -->
 <td width= 30></td> <!-- Kolom Aflever week realiteit -->
 <td width= 60></td> <!-- Kolom Aantal afl. realiteit -->
 <td width= 90></td> <!-- Kolom Realiteit 2022 -->
</tr>
<tr style = "font-size:12px;">
 <th style = "text-align:center;"valign= bottom ;> Dek week <hr></th>
 <th colspan="2" style = "text-align:center;"valign= bottom ;>Dekdatum<hr></th>
 <th style = "text-align:center;"valign= 'bottom' ;>Aantal<br>dekkingen<hr></th>
 <th style = "text-align:center;"valign= bottom ;> Werp week <hr></th>
 <th style = "text-align:center;"valign= bottom ;>Werpdatum<hr></th>
 <th style = "text-align:center;"valign= bottom ;>Aantal<br>lamm.<hr></th>
 <th style = "text-align:center;"valign= bottom ;>Speendatum<hr></th>
 <th style = "text-align:center;"valign= bottom ;>Afleverdatum<hr></th>
 <th style = "text-align:center;"valign= bottom ;>Prognose <?php echo $kzlJaar; ?> <hr></th>
 <th> </th>
 <th style = "text-align:center;" valign= bottom ; > Dek week <hr></th>
 <th style = "text-align:center;"valign= bottom ;>Week van ...<hr></th>
 <th style = "text-align:center;"valign= 'bottom' ;>Aantal<br>dekkingen<hr></th>
 <th style = "text-align:center;"valign= bottom ;> Werp week <hr></th>
 <!-- <th style = "text-align:center;"valign= bottom ;>Week van ...<hr></th> -->
 <th style = "text-align:center;"valign= bottom ;>Aantal<br>lamm.<hr></th>
 <th style = "text-align:center;"valign= bottom ;>Afl. week<hr></th>
 <th style = "text-align:center;"valign= bottom ;>Aantal<br>afl.<hr></th>
 <th style = "text-align:center;"valign= bottom ;>Opbrengst <?php echo $kzlJaar; ?> <hr></th>
</tr>
<?php
        //Als 1 januari niet in week 1 valt moet de laatste week van het vorige jaar worden getoond.
        // De eerste maandag van het jaar kan na 1 januari liggen. Er kan voor die eerste maandag in dat jaar al wel dekkingen zijn gerealiseerd en moeten ook worden getoond.
/****************************************
    Realisatie voor de eerste volle week
****************************************/
        $dmdek1 = $deklijst_gateway->zoek_eerste_datum_week1($lidId, $kzlJaar);
        $aantal_voor_week1 = $volwas_gateway->zoek_dekkingen_voor_week1($lidId, $kzlJaar, $dmdek1);
        if ($aantal_voor_week1 > 0) {
            // In sql is maandag 31-12-2018 week 52. In php week 01 vandaar de week van oudjaarsdag bepaald in php
            $vorig_jaar = $kzlJaar - 1;
            $oudjaarsdag = $vorig_jaar . '/12/31';
            $last_week = date_format(date_create($oudjaarsdag), 'W');
            ##echo 'week 31-12-'.$vorig_jaar.' = '.date_format(date_create($oudjaarsdag),'W').'<br>';
            if ($last_week == '01') { // bijv. maandag 31-12-2018
                $lastYearWeek = $kzlJaar . $last_week;
            } else {
                $lastYearWeek = $kzlJaar . '00';
            }
            $nieuwjaarsdag = date('d-m-Y', strtotime($oudjaarsdag . ' + 1 days'));
            $werpdm_r_week0 = date('d-m-Y', strtotime($nieuwjaarsdag . ' + 145 days'));
            $wweek_r_week0 = date_format(date_create($werpdm_r_week0), 'W');
            $wJaarWeek0 = $kzlJaar . $wweek_r_week0;
            $werpdate_0 = new DateTime($werpdm_r_week0);
            $maandag_van_werpdatum0 = $werpdate_0->modify('last sunday +1 day')->format('d-m-Y'); // De maandag van de berekende werpdatum
            $afvdm_r_week0 = date('d-m-Y', strtotime($nieuwjaarsdag . ' + 275 days'));
            $aweek_r_week0 = date_format(date_create($afvdm_r_week0), 'W');
            $aJaarWeek0 = $kzlJaar . $aweek_r_week0;
            $dekat_r_week0 = $volwas_gateway->zoek_aantal_dekkingen($lidId, $lastYearWeek);
            $werpat_r_week0 = $historie_gateway->zoek_aantal_lammeren($lidId, $wJaarWeek0);
            $afvat_r_week0 = $historie_gateway->zoek_aantal_afvoer($lidId, $aJaarWeek0);
?>
<tr style = "font-size:13px;" height = 25>
 <td align="center"><?php echo $last_week; ?></td>
 <td colspan="2"></td> <!-- Dekdatum prognose -->
 <td></td> <!-- Dekaantal prognose -->
 <td></td> <!-- Werpweek prognose -->
 <td></td> <!-- Werpdatum prognose -->
 <td></td> <!-- Aantal lamm. prognose -->
 <td></td> <!-- Speendatum prognose -->
 <td></td> <!-- Afleverdatum prognose -->
 <td></td> <!-- Prognose 2019 -->
 <td></td> <!-- extra kolom om ruimte te creëren -->
 <td align="center"><?php echo $last_week; ?></td>
 <td><?php echo $nieuwjaarsdag; ?></td>
 <td align="center"><?php echo $dekat_r_week0; ?></td>
 <td align="center"><?php echo $wweek_r_week0; ?></td>
 <!-- <td align="center"><?php echo $maandag_van_werpdatum0; ?></td> -->
 <td align="center"><?php echo $werpat_r_week0; ?></td>
 <td align="center"><?php echo $aweek_r_week0; ?></td>
 <td align="center"><?php echo $afvat_r_week0; ?></td>
</tr>
<?php
        } // Einde if($aantal_voor_week1 > 0)
/**********************************************
    EINDE Realisatie voor de eerste volle week
***********************************************/
        $maandnm = array('','januari','februari','maart','april','mei','juni','juli','augustus','september','oktober','november','december');
        $zoek_dekmaanden = $deklijst_gateway->zoek_dekmaanden($lidId, $kzlJaar);
        while ($month = $zoek_dekmaanden->fetch_assoc()) {
            $mndnr = $month['mndnr'];
            $dek_jaarmaand = $kzlJaar . $mndnr;
            $zoek_dekweken = $deklijst_gateway->zoek_dekweken($lidId, $kzlJaar, $mndnr);
            while ($zdw = $zoek_dekweken->fetch_assoc()) {
                $dekmaandag = $zdw['dmdek'];
                // converteren naar week levert in sql fouten op als 1 januari op dinsdag valt. Zie 01-01-2019.
                // In sql is dit week 0 en in php week 1 !! Vandaar de loop $zoek_dekweken doorlopen o.b.v. de datums (maandagen) in tblDeklijst
    /*****************************
            Prognose per week
    ******************************/
                // Prognose per week ophalen en tonen
                $zoek_prognose_weken = $deklijst_gateway->zoek_prognose_weken($lidId, $kzlJaar, $dekmaandag);
                while ($week = $zoek_prognose_weken->fetch_assoc()) {
                    $Id = $week['dekId'];
                    $dweek_p = date_format(date_create($week['dmdek']), 'W');
                    $dekdm_p = date_format(date_create($week['dmdek']), 'd-m-Y');
                    $dekat_p = $week['dekat'];
                    $wweek_p = date_format(date_create($week['dmwerp']), 'W');
                    $werpdm_p = date_format(date_create($week['dmwerp']), 'd-m-Y');
                    $werpat_p = ($dekat_p * $worp_val) - ($dekat_p * $sterf_val / 100);
                    $speendm = date_format(date_create($week['dmspeen']), 'd-m-Y');
                    $afvoerdm = date_format(date_create($week['dmafvoer']), 'd-m-Y');
                    $afvmnd = $week['afvmnd'];
?>
<tr style = "font-size:13px;">  <!-- Start Regels prognose en realisatie per week-->
 <td align = center>
                    <?php echo $dweek_p; ?>
 </td>
 <td colspan="2"><?php echo $dekdm_p; ?></td>
 <td align = center>
     <input type = text name = <?php echo "txtDekat_$Id"; ?> size = 1 style = "text-align : right;" value = <?php echo $dekat_p; ?> >
 </td> <!--hiddden-->
 <td align = center><?php echo $wweek_p; ?></td>
 <td align = center><?php echo $werpdm_p; ?></td>
 <td align = center><?php echo $werpat_p; ?> </td>
 <td align = center><?php echo $speendm; ?></td>
 <td align = center><?php echo $afvoerdm; ?></td>
 <td align = center></td>
 <td></td> <!-- extra kolom om ruimte te creëren -->
<?php
                }
                // Prognose per week ophalen en tonen
    /*****************************
        Einde Prognose per week
    ******************************/
    /*****************************
            Realisatie per week
    ******************************/
                $zoek_realisatie_weken = $deklijst_gateway->zoek_realisatie_weken($lidId, $kzlJaar, $dekmaandag);
                while ($zdw = $zoek_realisatie_weken->fetch_assoc()) {
                    $dekdm_r = $zdw['dekdm'];
                    $dweek_r = date_format(date_create($zdw['dmdek']), 'W');
                    $dek_jaarweek = $kzlJaar . $dweek_r;
                    $werp_jaarweek = $zdw['werpjaarweek'];
                    $wweek_r = date_format(date_create($zdw['dmwerp']), 'W');
                    $werpdate = new DateTime($zdw['dmwerp']);
                    $maandag_van_werpdatum = $werpdate->modify('last sunday +1 day')->format('d-m-Y'); // De maandag van de berekende werpdatum
                    $afv_jaarweek = $zdw['afvjaarweek'];
                    $aweek_r = date_format(date_create($zdw['dmafvoer']), 'W');
                    $dekat_r = $volwas_gateway->zoek_aantal_dekkingen_per_week($lidId, $dek_jaarweek);
                    $werpat_r = $historie_gateway->zoek_aantal_geboortes_per_week($lidId, $werp_jaarweek);
                    $afvat_r = $historie_gateway->zoek_aantal_afvoer_per_week($lidId, $afv_jaarweek);
?>
 <td align="center"><?php echo $dweek_r; ?></td>
 <td><?php echo $dekdm_r; ?></td>
 <td align="center"><?php echo $dekat_r; ?></td>
 <td align="center"><?php echo $wweek_r; ?></td>
<!-- <td align="center"><?php echo $maandag_van_werpdatum; ?></td> -->
 <td align="center"><?php echo $werpat_r; ?></td>
 <td align="center"><?php echo $aweek_r; ?></td>
 <td align="center"><?php echo $afvat_r; ?></td>
<?php
                } // Einde while($zdw = mysqli_fetch_assoc($zoek_realisatie_weken))
    /*****************************
        EINDE Realisatie per week
    ******************************/
            } // Einde $zoek_dekweken
?>
</tr> <!-- Einde Regels prognose en realisatie per week-->
<tr>
 <td><hr></td> <!-- Dekweek prognose -->
 <td colspan="2" ><hr></td> <!-- Dekdatum prognose -->
 <td ><hr></td> <!-- Dekaantal prognose -->
 <td colspan="2" ><hr></td> <!-- Werpweek prognose en Werpdatum prognose-->
 <td ><hr></td> <!-- Aantal lamm. prognose -->
 <td ><hr></td> <!-- Speendatum prognose -->
 <td ><hr></td> <!-- Afleverdatum prognose -->
 <td ><hr></td> <!-- Prognose 2019 -->
 <td></td> <!-- extra kolom om ruimte te creëren -->
 <td><hr></td> <!-- Dekweek realiteit -->
 <td ><hr></td> <!-- Week van ... dekdatum realiteit -->
 <td ><hr></td> <!-- Aantal dekkingen realiteit -->
 <td colspan="1" ><hr></td> <!-- Werpweek en Week van ... Werpdatum realiteit -->
 <td ><hr></td> <!-- Aantal lamm.  realiteit -->
 <td ><hr></td> <!-- Afleverweek realiteit -->
 <td ><hr></td> <!-- Aantal afl. realiteit -->
 <td ><hr></td> <!-- Realiteit 2022 -->
</tr>
<?php
    /*****************************
             Prognose per maand
    ******************************/
            // Gegevens per maand ophalen incl. uit tblLiquiditeit
            $zoek_maandtotalen_prognose = $deklijst_gateway->zoek_maandtotalen_prognose($lidId, $kzlJaar, $mndnr);
            while ($month = $zoek_maandtotalen_prognose->fetch_assoc()) {
                $dektot_p = $month['dektot'];
                $werptot_p = ($month['dektot'] * $worp_val) - ($month['dektot'] * $sterf_val / 100);
?>
<tr height = 50 valign = 'top'> <!-- Regel totalen per maand -->
 <td><b>Totaal </b></td>
 <td colspan="2" align = center><b><?php echo $maandnm[$mndnr]; ?></b></td>
 <td align = center ><b><?php echo $dektot_p; ?> </b></td>
 <td colspan="2"></td>
 <td align = center><b><?php echo $werptot_p; ?></b></td>
 <td></td>
 <td align = center><b><?php echo $maandnm[$afvmnd]; ?></b></td>
 <td align = 'right'><b> <?php echo euro_format($werptot_p * $prijs_val); ?> </b></td>
 <td></td> <!-- extra kolom om ruimte te creëren -->
<?php
    /*****************************
         Einde Prognose per maand
    ******************************/
            }
    /*****************************
             Realisatie per maand
    ******************************/
            // DEKKINGEN
            // Eerste en laatste dekweek per maand bepalen
            $eerste_weeknr_dekmaand = date("W", strtotime("first monday of $kzlJaar-$mndnr")); // eerste weekknr van de maand o.b.v. eerste maandag van de maand.
            $eerste_dag_maand = '01-' . $mndnr . '-' . $kzlJaar;
            $laatste_dag_dekmaand = last_day_of_month($eerste_dag_maand);
            $laatst_weeknr_dekmaand = date_format(date_create($laatste_dag_dekmaand), 'W'); //laatste_weeknummer van de maand
            $Dweekvan = $kzlJaar . $eerste_weeknr_dekmaand;
            $Dweektot = $kzlJaar . $laatst_weeknr_dekmaand;
            // Einde Eerste en laatste dekweek per maand bepalen
            $dektot_r = $volwas_gateway->zoek_aantal_dekkingen_per_maand($lidId, $Dweekvan, $Dweektot);
            if ($mndnr == 1 && isset($dekat_r_week0)) {
                $dektot_r = $dektot_r + $dekat_r_week0;
            }
            // Einde DEKKINGEN
            // WORPEN
            // Eerste en laatste werpweek per maand bepalen
            $eerste_maandag_dekmaand = date("Y-m-d", strtotime("first monday of $kzlJaar-$mndnr"));  // Eerste maandag van de dekmaand
            $werpdag_obv_dekdatum = date("Y-m-d", strtotime($eerste_maandag_dekmaand . '+ 145 days'));
            $firstdate = new DateTime($werpdag_obv_dekdatum);
            $eerste_maandag_werpmaand = $firstdate->modify('last sunday +1 day')->format('Y-m-d');
            $jaar_eerste_weeknr_werpmaand = date_format(date_create($eerste_maandag_werpmaand), 'Y');
            $eerste_weeknr_werpmaand = date_format(date_create($eerste_maandag_werpmaand), 'W');
            $lastdate = new DateTime($laatste_dag_dekmaand);
            $laatste_maandag_dekmaand = $lastdate->modify('last sunday +1 day')->format('Y-m-d');
            $laatste_werpdag_obv_dekmaand = date("Y-m-d", strtotime($laatste_maandag_dekmaand . '+ 145 days'));
            $jaar_laatst_weeknr_werpmaand = date_format(date_create($laatste_werpdag_obv_dekmaand), 'Y'); //laatste_weeknummer van de maand
            $laatst_weeknr_werpmaand = date_format(date_create($laatste_werpdag_obv_dekmaand), 'W'); //laatste_weeknummer van de maand
            if ($mndnr == 1 && isset($wweek_r_week0)) {
                $Wweekvan = $jaar_eerste_weeknr_werpmaand . $wweek_r_week0;
            } else {
                $Wweekvan = $jaar_eerste_weeknr_werpmaand . $eerste_weeknr_werpmaand;
            }
            $Wweektot = $jaar_laatst_weeknr_werpmaand . $laatst_weeknr_werpmaand;
            // Einde Eerste en laatste werpweek per maand bepalen
            $werptot_r = $historie_gateway->zoek_aantal_lammeren_per_maand($lidId, $Wweekvan, $Wweektot);
            if ($mndnr == 1 && $werpat_r_week0) {
                $werptot_r = $werptot_r + $werpat_r_week0;
            }
            // Einde WORPEN
            // AFLEVEREN
            // Eerste en laatste afleverweek per maand bepalen
            $eerste_maandag_dekmaand = date("Y-m-d", strtotime("first monday of $kzlJaar-$mndnr"));  // Eerste maandag van de dekmaand
            $afleverdag_obv_dekdatum = date("Y-m-d", strtotime($eerste_maandag_dekmaand . '+ 275 days'));
            $firstdate = new DateTime($afleverdag_obv_dekdatum);
            $eerste_maandag_aflevermaand = $firstdate->modify('last sunday +1 day')->format('Y-m-d');
            $jaar_eerste_weeknr_aflevermaand = date_format(date_create($eerste_maandag_aflevermaand), 'Y');
            $eerste_weeknr_aflevermaand = date_format(date_create($eerste_maandag_aflevermaand), 'W');
            $lastdate = new DateTime($laatste_dag_dekmaand);
            $laatste_maandag_dekmaand = $lastdate->modify('last sunday +1 day')->format('Y-m-d');
            $laatste_afleverdag_obv_dekmaand = date("Y-m-d", strtotime($laatste_maandag_dekmaand . '+ 275 days'));
            $jaar_laatst_weeknr_aflevermaand = date_format(date_create($laatste_afleverdag_obv_dekmaand), 'Y'); //laatste_weeknummer van de maand
            $laatst_weeknr_aflevermaand = date_format(date_create($laatste_afleverdag_obv_dekmaand), 'W'); //laatste_weeknummer van de maand
            if ($mndnr == 4 && $jaar_eerste_weeknr_aflevermaand == $kzlJaar) {
                $jaar_eerste_weeknr_aflevermaand = $jaar_eerste_weeknr_aflevermaand + 1;
                $Afweekvan = $jaar_eerste_weeknr_aflevermaand . $eerste_weeknr_aflevermaand;
            } else {
                $Afweekvan = $jaar_eerste_weeknr_aflevermaand . $eerste_weeknr_aflevermaand;
            }
            $Afweektot = $jaar_laatst_weeknr_aflevermaand . $laatst_weeknr_aflevermaand;
            // Einde Eerste en laatste afleverweek per maand bepalen
            $afltot_r = $historie_gateway->zoek_aantal_afleveren_per_maand($lidId, $Afweekvan, $Afweektot);
            $opbrengst = $opgaaf_gateway->zoek_afleverbedrag_per_maand($rubuId, $Afweekvan, $Afweektot);
            // EINDE AFLEVEREN
?>
 <td><b>Totaal </b></td>
 <td align = center><b><?php echo $maandnm[$mndnr]; ?></b></td>
 <td align = center ><b><?php echo $dektot_r; ?> </b></td>
 <td colspan="1"></td>
 <td align = center><b><?php echo $werptot_r; ?></b></td>
 <td></td> <!-- Afleverweek realiteit -->
 <td align = center><b><?php echo $afltot_r; ?></b></td>
 <td align = 'right'><b> <?php echo euro_format($opbrengst); ?> </b></td>
 <td align = center><b><?php echo $maandnm[$afvmnd] . ' ' . $jaar_eerste_weeknr_aflevermaand; ?></b></td>
</tr>  <!-- Einde Regel totalen per maand -->
<?php
    /*****************************
        EINDE Realisatie per maand
    ******************************/
        } // Einde $zoek_dekmaanden
?>
</table> <!-- Einde Tabel Prognose en Realiteit -->
</form>
    </TD>
<?php
    } else {
?>
            <img src='deklijst_php.jpg'  width='970' height='550'>
<?php
    }
    include "menuFinance.php";
}
?>
</tr>
</table>
</body>
</html>
