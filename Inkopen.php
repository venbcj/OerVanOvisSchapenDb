<?php

require_once("autoload.php");
$versie = '17-2-14'; /*insInkat = ln['vrbat']*(_POST['txtBstat']); gewijzigd naar insInkat = _POST['txtBstat']; zodat de totale hoeveelheid kan worden ingevoerd bij inkoop ipv het totale aantal / verbruikeenheid in te voeren.*/
$versie = '27-11-2014'; /*chargenr toegevoegd.*/
$versie = '8-3-2015'; /*Login toegevoegd */
$versie = '20-12-2015'; /* Inkoop ook toegevoegd aan tblOpgaaf indien module financieel in gebruik */
$versie = '16-6-2018'; /* Bedrag bij ingekochte artikelen wijzigbaar. Bedrag bij inkoop niet verplicht. unction verplicht() toegevoegd */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '30-12-2018'; /* javascript toegevoegd tbv eenheid artikel wijzigen */
$versie = '7-4-2019'; /* Prijs in tblOpgaaf incl. btw gemaakt */
$versie = '11-7-2020'; /* € gewijzigd in &euro; 1-8-2020 : kalender toegevoegd */
$versie = '28-11-2020'; /* 28-11-2020 velde chkDel toegevoegd */
$versie = '26-8-2021'; /* O.b.v. javascript inkopen per jaartal verborgen en zichtbaar gemaakt */
$versie = '17-1-2022'; /* Btw 0% en javascript verplicht() toegevoegd. SQL beveiligd met quotes */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */
Session::start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Inkoop</title>
    <style type="text/css">
        .selectt {
           /* color: #fff;
            padding: 30px;*/
            display: none;
            /*margin-top: 30px;
            width: 60%;
            background: grey;*/
            font-size: 12px;
        }
    </style>
</head>
<body>
<?php
$titel = 'Inkopen';
$file = "Inkopen.php";
include "login.php";
?>
            <TD align = "center" valign = "top">
<?php
if (Auth::is_logged_in()) {
    if ($modtech == 1) {
        include "kalender.php";
        $eenheid_gateway = new EenheidGateway();
        $newvoer = $eenheid_gateway->newvoer($lidId);
        $array_eenheid = [];
        while ($lin = $newvoer->fetch_array()) {
            $array_eenheid[$lin['artId']] = $lin['eenheid'];
        }
        // verwacht $array_eenheid
        include "validate-inkopen.js.php";
        if (isset($_POST['knpSave_'])) {
            include "save_inkoop.php";
        }
        $input = $_POST; // experiment --BCB
        //*******************
        // NIEUWE INVOER POSTEN
        //*******************
        $inkprijs = '';
        if (isset($_POST['knpAantal_'])) {
            $inkdatum = $input['txtInkdm_'] ?? null;
            $txtCharge = $input['txtCharge_'] ?? null;
            $inkwaarde = $input['txtInkat_'] ?? null;
            $inkprijs = $input['txtPrijs_'] ?? null;
        }
        if (isset($_POST['knpInsert_'])) {
            $eenheid_gateway = new EenheidGateway();
            $k_nhd = $eenheid_gateway->keuze_eenhd($lidId, $input['txtArtikel_']);
            if (empty($input['txtPrijs_'])) {
                $insPrijs = 'NULL';
            } else {
                $txtPrijs = $input['txtPrijs_'];
                $insPrijs = str_replace(',', '.', $txtPrijs);
            }
            if (empty($input['txtInkdm_'])) {
                $insInkdm = "inkdm = NULL";
            } else {
                $dateink = date_create($input['txtInkdm_']);
                $insInkdm = date_format($dateink, 'Y-m-d');
            }
            if (!empty($input['txtCharge_'])) {
                $insCharge = $input['txtCharge_'];
            }
            if (!empty($input['txtArtikel_'])) {
                $insVoer = $input['txtArtikel_'];
            }
            $insInkat = $input['txtInkat_'];
            $artikel_gateway = new ArtikelGateway();
            $ophalen_waardes = $artikel_gateway->ophalen_waardes($insVoer);
            while ($ln = $ophalen_waardes->fetch_assoc()) {
                $enhuId = $ln['enhuId'];
                $insBtw = $ln['btw'];
                $rubuId = $ln['rubuId'];
                $relatie = $ln['naam'];
                if (empty($ln['relId'])) {
                    $insRc = "NULL";
                } else {
                    $insRc = "$ln[relId]";
                }
            }
            $inkoop_gateway = new InkoopGateway();
            $inkoop_gateway->insert_tblInkoop($insInkdm, $insVoer, $insCharge, $insInkat, $enhuId, $insPrijs, $insBtw, $insRc);
            if ($modfin == 1 && isset($rubuId)) {
                if ($insBtw > 1) {
                    $btwBedrag = $insPrijs * $insBtw / 100;
                } else {
                    $btwBedrag = 0;
                }
                // @fragile
                $PrijsInclBtw = $insPrijs + $btwBedrag;
                $opgaaf_gateway = new OpgaafGateway();
                $opgaaf_gateway->insert_tblOpgaaf($rubuId, $insInkdm, $PrijsInclBtw, $relatie);
            }
        } 
?>
<table border= 0><tr><td>
<form action="Inkopen.php" method="post" >
<!--*********************************
         NIEUWE INVOER VELDEN
    ********************************* -->
<table border= 0>
<tr><td colspan = 3 style = "font-size:13px;"><i> Nieuwe inkoop : </i></td></tr>
<tr style =  "font-size:12px;" valign =  "bottom">
 <td>Inkoopdatum<hr></td>
 <td>Omschrijving<hr></td>
 <td>Chargenummer<hr></td>
 <td colspan = 2> Aantal <hr></td>
 <td colspan = 2 width = 50 align = center>Totaalprijs excl. btw<hr></td>
</tr>
<tr>
<td><input id = "datepicker1" type="text" name = "txtInkdm_" size = 8 value = <?php if (isset($inkdatum)) {
echo $inkdatum;
        } 
?> ></td>
 <td>
<?php
        // kzlvoer bij nieuwe invoer
        $newvoer->data_seek(0);
?>
 <select style= "width:280;" name = "txtArtikel_" id = "artikel" onchange = "eenheid_artikel()" >
 <option> </option>
<?php        while ($lijn = $newvoer->fetch_array()) {
$name = $lijn['naam'];
if ($lijn['soort'] == 'pil') {
    $getal = "&nbsp per $lijn[stdat]";
    $eenheid = $lijn['heid'];
} else {
    $getal = '';
    $eenheid = '';
}
$cijf = str_replace('.00', '', $getal);
$wrde = "$name$cijf$eenheid";
$opties = array($lijn['artId'] => $wrde);
foreach ($opties as $key => $waarde) {
    $keuze = '';
    if (isset($_POST['txtArtikel_']) && $_POST['txtArtikel_'] == $key) {
        $keuze = ' selected ';
    }
    echo '<option value="' . $key . '" ' . $keuze . '>' . $waarde . '</option>';
}
        } 
?>
 </select>
</td>
<td><input type= "text" name = "txtCharge_" size = 14 value = <?php if (isset($txtcharge)) { echo $txtcharge; } ?> ></td>
    <td><input type= "text" id="hoeveelheid" name = "txtInkat_" size = 3 value = <?php if (!isset($inkwaarde)) { $inkwaarde = 1; } echo $inkwaarde; ?> title = "Totale hoeveelheid ingekocht">
</td>
<td>
<p  id="aantal" > </p>
</td>
<td>
&euro;
</td>
<td><input type= "text" id="prijs" name = "txtPrijs_" size = 3  title = "Prijs totale hoeveelheid" <?php echo "value = $inkprijs "; ?> ></td>
<td colspan = 2><input type = "submit" name = "knpInsert_" onfocus = "verplicht()" value = "Toevoegen" style = "font-size:10px;"></td></tr>
<tr><td colspan = 15><hr></td></tr>
</table>
<!--*********************************
        EINDE NIEUWE INVOER VELDEN
    ********************************* -->
</td></tr><tr><td>
<!--*****************************
             WIJZIGEN VOER
    ***************************** -->
 <table border= 0 align = "left" >
 <tr>
  <td colspan =  16 > <b>Inkopen :</b>
  </td>
  <td align="center" ><input type = "submit" name = "knpSave_" value = "Opslaan" style = "font-size:14px" >
 </td>
</tr>
<?php
        $current_year = date("Y");
        $eenheid_gateway = new EenheidGateway();
        $group_jaar = $eenheid_gateway->group_jaar($lidId);
        while ($lus = $group_jaar->fetch_assoc()) {
            $jaar = $lus['jaar'];
?>
<tr>
 <td colspan="9">
 <input type="checkbox" name="jaartalCheckbox" value= <?php echo $jaar; if ($jaar == $current_year) { ?> checked <?php } ?> >
 <?php echo $jaar; ?>
            </td>
                <td class= "<?php echo $jaar; ?> selectt" >
                Stuksprijs
                </td>
                </tr>
                <tr style =  "font-size:12px;" valign =  "bottom" class= "<?php echo $jaar; ?> selectt" >
                <th>Inkoopdatum<hr></th>
                <th></th>
                <th>Omschrijving<hr></th>
                <th></th>
                <th>Chargenummer<hr></th>
                <th></th>
                <th colspan = 2>Aantal<hr></th>
                <th></th>
                <th width = 50>(excl.)<hr></th>
                <th></th>
                <th>Prijs (excl.)<hr></th>
                <th></th>
                <th>Btw<hr></th>
                <th></th>
                <th>Leverancier<hr></th>
                <th>Verwijder<hr></th>
                </tr>
<?php
            $array_btw = array(1 => '0%', 9 => '9%', 21 => '21%');
            $inkoop_gateway = new InkoopGateway();
            $query = $inkoop_gateway->inkopen_query($lidId, $jaar);
            while ($row = $query->fetch_assoc()) {
                $inkid = $row['inkId'];
                $inkdm = $row['inkdm'];
                $dmink = $row['dmink'];
                $naam = $row['naam'];
                $charge = $row['chargenr'];
                $bstat = $row['inkat'];
                $eenhd = $row['eenheid'];
                $stprijs = $row['stprijs'];
                $prijs = $row['prijs'];
                $btw_db = $row['btw'];
                $btw = $array_btw[$btw_db];
                $rc = $row['crediteur'];
                $nutId = $row['nutId'];
                $voedId = $row['voedId'];
?>
<tr class= "<?php echo $jaar; ?> selectt" >
 <td align = center style = "font-size:12px;"><?php echo $inkdm; ?></td><td width = "1"></td>
 <td style = "font-size:16px;"><?php echo "$naam";?></td>
 <td width = "1"></td>
 <td style = "font-size:16px;"><?php echo "$charge";?></td>
 <td width = "1"></td>
 <td align = right style = "font-size:16px;"><?php echo $bstat;?></td>
 <td align = left style = "font-size:16px;"><?php echo "$eenhd";?></td>
 <td width = "1"></td>
 <td align = right > &euro;&nbsp <?php echo $stprijs;?> </td>
 <td width = "1"></td>
 <td align = right > &euro;&nbsp <input type = text name = <?php echo "txtPrijs_$inkid"; ?> size = 4 style = "font-size:11px; text-align:right;" value = <?php    echo $prijs;?> ></td>
 <td width = "1"></td>
 <td align = center style = "font-size:12px;"><?php if (!empty($btw)) { echo $btw; } ?></td>
 <td width = "1"></td>
 <td align = center style = "font-size:14px;"><?php echo "$rc";?></td>
 <td align = center style = "font-size:14px;"><?php if (!isset($nutId) && !isset($voedId)) { ?>
<!--<button class=btn btn-sm btn-danger delete_class id= <?php echo $inkid; ?> >Verwijder inkoop</button> -->
      <input type = "checkbox" name= <?php echo "chkDel_$inkid"; ?> value = "Verwijder inkoop" style = "font-size:9px" >
                                              <?php } ?></td>
</tr>
            <?php    } ?>
<tr class= "<?php echo $jaar; ?> selectt" ><td height="50"></td></tr>
        <?php    } ?>
</td></tr>
</table>
<!--*****************************
         EINDE WIJZIGEN VOER
    ***************************** -->
</form>
<td><tr></table>
    </TD>
<?php
    } else {
?>
    <img src='Inkopen_php.jpg'  width='970' height='550'/> <?php
    }
    include "menuInkoop.php";
}
$urls['delete'] = 'delete_inkoop.php';
include "inkopen.js.php";
?>
</body>
</html>
