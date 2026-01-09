<?php

require_once("autoload.php");
/*
<!-- 16-11-2013 : kzlVerbruikseenheid ook te wijzigen na inkoop. Eenheid wordt nl. opgeslagen in tblInkoop
 8-3-2015 Login toegevoegd
 14-11-2015 naamwijziging van Medicijnen naar Medicijnenbestand
 30-11-2015 : Spatie in registratienumer mogelijk gemaakt -->
 */
$versie = '1-8-2017'; /* Rubriek toegevoegd incl. save_artikel.php */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '7-4-2019'; /* Btw gewijzigd van 6 naar 9% */
$versie = '5-7-2020'; /* Veld Per gewicht toegevoegd en wachtdagen gesplitst in vlees en melk */
$versie = '9-8-2020'; /* Veld naamreader toegevoegd */
$versie = '14-11-2020'; /* De knop activeren van per medicijn vervangen door checkbox. Met de knop werden alle medicijnen geactiveerd. 15-11 : Eenheid niet meer te wijzigen na eerste inkoop */
$versie = '17-1-2022'; /* Btw 0% en javascript verplicht() toegevoegd. SQL beveiligd met quotes */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */
$versie = '23-04-2025'; /* De letters kg achter het veld geplaaats i.p.v. er onder */
Session::start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Inkoop</title>
</head>
<body>
<?php
$titel = 'Medicijnen';
$file = "Medicijnen.php";
include "login.php";
?>
                <TD valign = "top">
<?php
if (Auth::is_logged_in()) {
    if ($modtech == 1) {
        include "validate-medicijnen.js.php";
        if (isset($_POST['knpSave_'])) {
            include "save_artikel.php";
        }
        //*******************
        // NIEUWE INVOER
        //*******************
        if (isset($_POST['knpInsert_'])) {
            $eenheid_gateway = new EenheidGateway();
            $dubbel = $eenheid_gateway->controle($lidId, $_POST['insNaam_']);
            if (!empty($dubbel) && $dubbel >= 1) {
                $fout = "Dit medicijn bestaat al.";
            } else {
                if (empty($_POST["insNaam_"])) {
                    $insNaam = "NULL";
                } else {
                    $insNaam = "'$_POST[insNaam_]'";
                    $Artikel = $_POST['insNaam_'];
                }
                if (!isset($_POST["insPres_"]) || empty($_POST["insPres_"])) {
                    $insPres = $Artikel;
                } else {
                    $insPres = $_POST[insPres_];
                }
                if (empty($_POST['insRegnr_'])) {
                    $insRegnr = "regnr = NULL";
                } else {
                    $insRegnr = "regnr = '$_POST[insRegnr_]' ";
                }
                if (empty($_POST['insStdat_'])) {
                    $insStdat = "stdat = NULL";
                } else {
                    $insStdat = "stdat = '$_POST[insStdat_]' ";
                }
                if (empty($_POST['insNhd_'])) {
                    $insNhd = "NULL";
                } else {
                    $insNhd = "'$_POST[insNhd_]'";
                }
                if (empty($_POST['insGewicht_'])) {
                    $insKg = "NULL";
                } else {
                    $insKg = "'$_POST[insGewicht_]'";
                }
                if (empty($_POST['insBtw_'])) {
                    $insBtw = "NULL";
                } else {
                    $insBtw = "'$_POST[insBtw_]'";
                }
                if (empty($_POST['insRelatie_'])) {
                    $insRelatie = "NULL";
                } else {
                    $insRelatie = "'$_POST[insRelatie_]'";
                }
                if (empty($_POST['insWdgnV_'])) {
                    $inswdgn_v = "NULL";
                } else {
                    $inswdgn_v = " '$_POST[insWdgnV_]' ";
                }
                if (empty($_POST['insWdgnM_'])) {
                    $inswdgn_m = "NULL";
                } else {
                    $inswdgn_m = " '$_POST[insWdgnM_]' ";
                }
                if ($modfin == 1) {
                    if (empty($_POST['insRubriek_'])) {
                        $insRubriek = "NULL";
                    } else {
                        $insRubriek = "'$_POST[insRubriek_]'";
                    }
                } else {
                    $insRubriek = "NULL";
                }
                $readernaam = substr($insPres, 0, 20);
                $artikel_gateway = new ArtikelGateway();
                $count = $artikel_gateway->zoek_readernaam($lidId, $readernaam);
                if ($count > 0) {
                    $d = 0;
                    $readernaam = getReadername($db, $lidId, $insPres, $d);
                }
                $artikel_gateway = new ArtikelGateway();
                $insert_tblArtikel = $artikel_gateway->insert_tblArtikel($insNaam, $readernaam, $insRegnr, $insStdat, $insNhd, $insKg, $insBtw, $insRelatie, $inswdgn_v, $inswdgn_m, $insRubriek);
            }
        }
        //*****************************
        //** ARTIKELEN IN GEBRUIK
        //*****************************
?>
<form action="Medicijnen.php" method="post" >
<table border= 0 align = "left" >
<tr>
 <td colspan = 9 > <b>Medicijnen in gebruik :</b> </td>
 <td colspan = 2 align=right><input type = "submit" name="knpSave_" value = "Opslaan" style = "font-size:12px;"></td>
</tr>
<tr style = "font-size:12px;" align = "center" valign = "bottom">
 <th width = 200 >Omschrijving *</th>
<?php
        if ($reader == 'Agrident') {
?>
     <th>Presentatie reader</th>
<?php
        }
?>
 <th>Registratienr</th>
 <th width = 20>&nbsp&nbspStand. &nbsp&nbspaantal</th>
 <th>Eenheid *</th>
 <th width = 80>per gewicht</th>
 <th>Btw</th>
 <th>Leverancier</th>
 <th>Wachtdagen <br> vlees &nbsp&nbsp melk</th>
<?php
        if ($modfin == 1) {
?>
     <th>Rubriek **</th>
<?php
        }
?>
 <th>Actief</th>
</tr>
<?php
        $eenheid_gateway = new EenheidGateway();
        $loop = $eenheid_gateway->actieve_artikelen($lidId);
        while ($lus = $loop->fetch_assoc()) {
            $Id = $lus['artId'];
            $eenheid_gateway = new EenheidGateway();
            $qryArtikel = $eenheid_gateway->qryArtikel($Id);
            while ($row = $qryArtikel->fetch_assoc()) {
                $soort = $row['soort'];
                $pil = $row['naam'];
                $naamreader = $row['pres'];
                $stdat = $row['stdat'];
                $enhuId = $row['enhuId'];
                $eenhd = $row['eenheid'];
                $perkg = $row['perkg'];
                $btw = $row['btw'];
                $regnr = $row['regnr'];
                $relId = $row['relId'];
                $rubuId = $row['rubuId'];
                $wdgn_v = $row['wdgn_v'];
                $wdgn_m = $row['wdgn_m'];
                $actief = $row['actief'];
                $inkoop_gateway = new InkoopGateway();
                $pil_ingekocht = $inkoop_gateway->pil_ingekocht($Id);
                $ing = $pil_ingekocht->fetch_assoc();
                $rows_inkoop = $ing['aant'];
                // EINDE Bepalen of artikel al is ingekocht
?>
<tr style = "font-size:12px;">
 <td style = "font-size : 14px;">
<?php
                // Veld Omschrijving (al dan niet te wijzigen)
                if ($rows_inkoop > 0) {
                    echo $pil;
                } else {
?>
            <input type= "text" name=
<?php echo "txtNaam_$Id"; ?>
 size = 30 value =
<?php echo " '$pil' "; ?>
 style = "font-size:12px;" >
<?php
                }
                // EINDE  Veld Omschrijving (al dan niet te wijzigen)
?>
 </td>
<?php
                if ($reader == 'Agrident') {
?>
         <td><!--Naam reader -->
        <input type= "text" name=
<?php echo "txtPres_$Id"; ?>
 size = 17 style = "font-size:12px;" value =
<?php echo "'" . $naamreader . "'" ; ?>
 >
         </td>
<?php
                }
?>
 <td><!--Registratienummer -->
<input type= "text" name=
<?php echo "txtRegnr_$Id"; ?>
 style = "font-size:12px;" value =
<?php echo "'" . $regnr . "'" ; ?>
 >
 </td>
 <td>
<!-- Standaard verbruiksaantal -->
<input type= "text" name=
<?php echo "txtStdat_$Id"; ?>
 size = 4 style = "font-size:12px; text-align : right;" title = "Standaard hoeveelheid per toedienen" value =
<?php echo $stdat; ?>
 >
 </td>
 <td>
<?php
                // kzlVerbruikseenheid (al dan niet te wijzigen)
                if ($rows_inkoop > 0) {
                    echo $eenhd;
                } else {
                    $eenheid_gateway = new EenheidGateway();
                    $qryEenheid = $eenheid_gateway->qryEenheid($lidId);
                    $index = 0;
                    while ($eh = $qryEenheid->fetch_array()) {
                        $eenhId[$index] = $eh['enhuId'];
                        $eenhnm[$index] = $eh['eenheid'];
                        $index++;
                    }
                    unset($index);
                    // EINDE DECLARATIE EENHEID
?>
         <select style="width:50;" name=
<?php echo "kzlNhd_$Id"; ?>
 value = "" style = "font-size:12px;">
          <option></option>
<?php
                    $count = count($eenhId);
                    for ($i = 0; $i < $count; $i++) {
                        $opties = array($eenhId[$i] => $eenhnm[$i]);
                        foreach ($opties as $key => $waarde) {
                            if ((!isset($_POST['knpSave_']) && $enhuId == $eenhId[$i]) || (isset($_POST["kzlNhd_$Id"]) && $_POST["kzlNhd_$Id"] == $key)) {
                                echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
                            } else {
                                echo '<option value="' . $key . '" >' . $waarde . '</option>';
                            }
                        }
                    }
?>
</select>
<?php
                }
                // EINDE  kzlVerbruikseenheid (al dan niet te wijzigen)
?>
 </td>
 <td> <input type="text" name=
<?php echo "txtGewicht_$Id"; ?>
 size = 1 style = "font-size:12px;" value =
<?php echo "'" . $perkg . "'" ; ?>
 > kg </td>
 <td>
<?php
                // kzlBtw bij wijzigen
                $opties = array('1' => '0%', '9' => '9%', '21' => '21%');
?>
 <select name=
<?php echo "kzlBtw_$Id"; ?>
 style= "width:50;" style = "font-size:12px;">
<?php
                foreach ($opties as $key => $waarde) {
                    if ((!isset($_POST['knpSave_']) && $btw == $key) || (isset($_POST["kzlBtw_$Id"]) && $_POST["kzlBtw_$Id"] == $key)) {
                        echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
                    } else {
                        echo '<option value="' . $key . '">' . $waarde . '</option>';
                    }
                }
?>
    </select>
 </td>
<!-- EINDE kzlBtw bij wijzigen -->
<?php
                $partij_gateway = new PartijGateway();
                $qryLevcier = $partij_gateway->findLeverancier($lidId);
                $index = 0;
                $relnm = [];
                while ($rel = $qryLevcier->fetch_array()) {
                    $rel_Id[$index] = $rel['relId'];
                    $relnm[$index] = $rel['naam'];
                    $index++;
                }
                unset($index);
                // EINDE Declaratie LEVERANCIER
?>
<!-- kzlLeverancier bij wijzigen -->
 <td>
 <select style= "width:110;" name=
<?php echo "kzlRelatie_$Id"; ?>
 value = "" style = "font-size:12px;">
  <option></option>
<?php
                $count = count($relnm);
                for ($i = 0; $i < $count; $i++) {
                    $opties = array($rel_Id[$i] => $relnm[$i]);
                    foreach ($opties as $key => $waarde) {
                        if ((!isset($_POST['knpSave_']) && $relId == $rel_Id[$i]) || (isset($_POST["kzlRelatie_$Id"]) && $_POST["kzlRelatie_$Id"] == $key)) {
                            echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
                        } else {
                            echo '<option value="' . $key . '" >' . $waarde . '</option>';
                        }
                    }
                }
?>
</select>
 </td>
<!-- EINDE kzlLeverancier bij wijzigen
wachtdagen vlees bij wijzigen -->
 <td>
    <input type= "text" name=
<?php echo "txtWdgnV_$Id"; ?>
 size = 1 style = "font-size:12px; text-align : right;" title = "Aantal wachtdagen vlees" value =
<?php echo $wdgn_v; ?>
 >
<!--EINDE wachtdagen vlees bij wijzigen
wachtdagen melk bij wijzigen -->
    <input type= "text" name=
<?php echo "txtWdgnM_$Id"; ?>
 size = 1 style = "font-size:12px; text-align : right;" title = "Aantal wachtdagen melk" value =
<?php echo $wdgn_m; ?>
 >
<!--EINDE wachtdagen melk bij wijzigen -->
 </td>
<?php
                if ($modfin == 1) {
                    $rubriek_gateway = new RubriekGateway();
                    $qryRubriek = $rubriek_gateway->qryRubriek($lidId);
                    $index = 0;
                    $rubnm = [];
                    while ($rub = $qryRubriek->fetch_array()) {
                        $rubId[$index] = $rub['rubuId'];
                        $rubnm[$index] = $rub['rubriek'];
                        $index++;
                    }
                    unset($index);
                    // EINDE Declaratie RUBRIEK
?>
        <!-- KZLRUBRIEK bij wijzigen-->
        <td>
         <select style="width:140;" name=
<?php echo "kzlRubriek_$Id"; ?>
 value = "" style = "font-size:12px;">
          <option></option>
<?php
                    $count = count($rubnm);
                    for ($i = 0; $i < $count; $i++) {
                        $opties = array($rubId[$i] => $rubnm[$i]);
                        foreach ($opties as $key => $waarde) {
                            if ((!isset($_POST['knpSave_']) && $rubuId == $rubId[$i]) || (isset($_POST["kzlRubriek_$Id"]) && $_POST["kzlRubriek_$Id"] == $key)) {
                                echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
                            } else {
                                echo '<option value="' . $key . '" >' . $waarde . '</option>';
                            }
                        }
                    }
?>
        </select>
        <!-- EINDE KZLRUBRIEK bij wijzigen -->
         </td>
<?php
                }
?>
 <td> <input type = "checkbox" name =
<?php echo "chkActief_$Id"; ?>
 id="c1" value="1"
<?php echo $actief == 1 ? 'checked' : '';
?>
 title = "Is medicijn te gebruiken ja/nee ?"> </td>
<?php
            }
?>
 <td></td>
</tr>
<?php
        }
?>
 </td></tr>
<tr><td colspan = 6 style= "font-size : 12px";><sub>* Eenmaal ingekocht niet meer wijzigbaar !
<?php
        if ($modfin == 1) {
?>
 <br>** t.b.v. automatisch aanmaken factuur na inkopen !
<?php
        }
?>
 </sub></td></tr>
<tr><td colspan = 15><hr></td></tr>
<!--
*************************************
** EINDE ARTIKELEN IN GEBRUIK
*************************************
*********************************
 VELDEN TBV NIEUWE INVOER
********************************* -->
<tr>
 <td colspan = 6 style = "font-size:12px;"><i> Nieuw medicijn : </i></td>
 <td colspan = 9 style = "font-size:12px;" align="right" ><input type = "submit" onfocus="verplicht()" name="knpInsert_" value = "Toevoegen" style = "font-size:10px;"></td>
</tr>
<tr>
 <td><input type="text" id="artikel" name= "insNaam_" size = 30 value = '' maxlength = 50></td>
<?php
        if ($reader == 'Agrident') {
?>
     <td><input type= "text" name= "insPres_" value = "" size = 15 style = "font-size:12px;" ></td>
<?php
        }
?>
 <td><input type= "text" name= "insRegnr_" value = "" size = 17 style = "font-size:12px;" ></td>
 <td><input type= "text" id="standaard" name= "insStdat_" value = 1 size = 1 style = "text-align : right; font-size:12px;" title = "Standaard hoeveelheid per toedienen"></td>
 <td>
<?php
        $eenheid_gateway = new EenheidGateway();
        $newvrb = $eenheid_gateway->newvrb($lidId);
?>
 <select style= "width:50;" id="eenheid" name= "insNhd_" >
 <option></option>
<?php
        while ($lijn = $newvrb->fetch_array()) {
            $opties = array($lijn['enhuId'] => $lijn['eenheid']);
            foreach ($opties as $key => $waarde) {
                $keuze = '';
                if (isset($_POST['insNhd_']) && $_POST['insNhd_'] == $key) {
                    $keuze = ' selected ';
                }
                echo '<option value="' . $key . '" ' . $keuze . '>' . $waarde . '</option>';
            }
        }
?>
 </select>
 </td>
 <td><input type= "text" name= "insGewicht_" size = 1 style = "text-align : right; font-size:12px;" title = "per kg dier (bijv 5 kg)"> kg </td>
 <td>
<?php
        // kzl btw bij nieuwe invoer
        $opties = array('' => '', '1' => '0%', '9' => '9%', '21' => '21%');
?>
 <select name= "insBtw_" id="btw" style= "width:50;">
<?php
        foreach ($opties as $key => $waarde) {
            $keuze = '';
            if (isset($_POST['insBtw_']) && $_POST['insBtw_'] == $key) {
                $keuze = ' selected ';
            }
            echo '<option value="' . $key . '" ' . $keuze . '>' . $waarde . '</option>';
        }
?>
 </select>
 </td>
 <td>
<?php
        $partij_gateway = new PartijGateway();
        $newcrediteur = $partij_gateway->findLeverancier($lidId);
?>
 <select name= "insRelatie_" style= "width:110;" >
 <option> </option>
<?php
        while ($regel = $newcrediteur->fetch_array()) {
            $opties = array($regel['relId'] => $regel['naam']);
            foreach ($opties as $key => $waarde) {
                $keuze = '';
                if (isset($_POST['insRelatie_']) && $_POST['insRelatie_'] == $key) {
                    $keuze = ' selected ';
                }
                echo '<option value="' . $key . '" ' . $keuze . '>' . $waarde . '</option>';
            }
        }
?>
 </select>
 </td>
 <td><input type="text" name=
<?php echo "insWdgnV_"; ?>
 value = "" size = 1 style = "font-size:12px; text-align : right;" title = "Aantal wachtdagen vlees">
  <input type="text" name=
<?php echo "insWdgnM_"; ?>
 value = "" size = 1 style = "font-size:12px; text-align : right;" title = "Aantal wachtdagen melk">
 </td>
<?php
        if ($modfin == 1) {
?>
 <td>
<!-- KZLRUBRIEK bij nieuwe invoer -->
<?php
            $rubriek_gateway = new RubriekGateway();
            $newRubriek = $rubriek_gateway->qryRubriek($lidId);
?>
 <select style="width:140;" name= "insRubriek_" value = "" style = "font-size:12px;">
  <option></option>
<?php
            while ($nwrub = $newRubriek->fetch_array()) {
                $opties = array($nwrub['rubuId'] => $nwrub['rubriek']);
                foreach ($opties as $key => $waarde) {
                    $keuze = '';
                    if (isset($_POST['insRubriek_']) && $_POST['insRubriek_'] == $key) {
                        echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
                    } else {
                        echo '<option value="' . $key . '" >' . $waarde . '</option>';
                    }
                }
            }
?>
</select>
<!-- EINDE KZLRUBRIEK bij nieuwe invoer -->
 </td>
<?php
        }
?>
 <td><input type="checkbox" name="boxActief_" id="c2"
<?php
        if (true) {
            echo "checked";
        }
?>
 disabled ></td>
<?php
?>
 </tr>
<!--
*********************************
 EINDE  VELDEN TBV NIEUWE INVOER
*********************************     -->
<tr><td colspan = 15><hr></td></tr>
<?php
        $eenheid_gateway = new EenheidGateway();
        $niet_actief = $eenheid_gateway->Niet_in_gebruik($lidId);
        if ($niet_actief > 0) {
?>
<tr>
 <td colspan = 4 height = 80 valign = "bottom">
 <b>Medicijnen niet in gebruik:</b>
 </td>
</tr>
<tr style = "font-size:12px;" valign = "bottom">
 <th align = "left" >Omschrijving</th>
 <th></th>
 <th align = "left" >Registratienr</th>
  <th>Stand. aantal</th>
 <th>Eenheid</th>
 <th>per gewicht</th>
 <th>Btw</th>
 <th>Leverancier</th>
 <th>Wachtdagen <br> vlees &nbsp&nbsp melk</th>
<?php
            if ($modfin == 1) {
?>
         <th>Rubriek</th>
<?php
            }
?>
 <th>Actief</th>
</tr>
<?php
            $eenheid_gateway = new EenheidGateway();
            $loop = $eenheid_gateway->inactieve_pillen($lidId);
            while ($lus = $loop->fetch_assoc()) {
                $Id = $lus['artId'];
                $eenheid_gateway = new EenheidGateway();
                $qryArtikel = $eenheid_gateway->qryArtikel_med($Id);
                while ($row = $qryArtikel->fetch_assoc()) {
                    $soort = $row['soort'];
                    $pil = $row['naam'];
                    $stdat = $row['stdat'];
                    $enhuId = $row['enhuId'];
                    $eenhd = $row['eenheid'];
                    $perkg = $row['perkg'];
                    $btw = $row['btw'];
                    $regnr = $row['regnr'];
                    $relatie = $row['relatie'];
                    $wdgn_v = $row['wdgn_v'];
                    $wdgn_m = $row['wdgn_m'];
                    $rubriek = $row['rubriek'];
                    $actief = $row['actief'];
?>
            <tr style = "font-size:12px;">
            <td style = "font-size : 14px;">
<?php echo $pil; ?>
</td>
     <td width = 1></td>
     <td>
<?php echo $regnr ; /* Registratienummer */ ?>
    </td>
     <td align = "center" >
<?php echo $stdat; /* Standaard verbruiksaantal */ ?>
 </td>
     <td align = "center" >
<?php echo $eenhd; /* Verbruikseenheid */ ?>
 </td>
     <td align = "center" >
<?php echo $perkg . ' kg'; /* Per gewicht */ ?>
 </td>
     <td align = "center" >
<?php echo $btw; // Btw ?>
 </td>
     <td>
<?php
        if (isset($relatie)) {
            echo $relatie;
        }
        //Leverancier
?>
 </td>
     <td align = "center">
<?php echo $wdgn_v . '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp' . $wdgn_m; //wachtdagen ?>
 </td>
     <td align = "center">
<?php echo $rubriek; //Rubriek ?>
 </td>
     <td align = "center">
         <input type = "checkbox" name =
<?php echo "chkActief_$Id"; ?>
 id="c1" value="1"
<?php echo $actief == 1 ? 'checked' : ''; ?>
 title = "Is medicijn te gebruiken ja/nee ?">
     </td>
     <td></td>
    </tr>
<?php
                }
?>
     </td>
    </tr>
    <!--
    *************************************
    ** EINDE ARTIKELEN NIET IN GEBRUIK
    ************************************* -->
<?php
            }
?>
<tr><td colspan = 15><hr></td></tr>
<?php
        }
?>
</table>
</form>
    </TD>
<?php
    } else {
?>
 <img src='medicijnen_php.jpg' width='900' height='500'/>
<?php
    }
    include "menuInkoop.php";
}
?>
</body>
</html>
