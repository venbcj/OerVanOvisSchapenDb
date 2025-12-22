<?php

require_once("autoload.php");

$versie = '13-11-2016'; /* Aangemaakt als kopie van insAanvoer. 
schaap 100214520769 gewijzigd in */
$versie = '20-3-2018';  /* Meerdere pagina's gemaakt 12-5-2018 : if(isset(data)) toegevoegd. Als alle records zijn verwerkt bestaat data nl. niet meer !! */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '10-11-2018'; /* Inlezen darcht herzien. Rekening gehouden met worp laatste 183 en alleen ooien en rammen op stallijst !! */
$versie = '20-1-2019'; /* alles aan- en uitzetten met javascript */
$versie = '24-4-2020'; /* url Javascript libary aangepast */
$versie = '18-12-2021'; /* Onderscheid gemaakt tussen reader Agrident en Biocontrol */
$versie = '03-02-2023'; /* Werking javascript verbeterd */
$versie = '31-12-2023'; /* and h.skip = 0 toegevoegd bij tblHistorie */
$versie = '23-11-2024'; /* In keuzelijst moeder- en vaderdieren  uitgeschaarde dieren wel tonen. zoek_moeder aangevuld met or h.actId = 10 en include vw_kzlOoien werd nergens toegepast en daarom verwijderd */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */

 Session::start();
 ?>
<!DOCTYPE html>
<html>
<head>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Registratie</title>

<style type="text/css">
.selectt {
  /* color: #fff;
   padding: 30px;*/
   display: "inline-block";
   /*margin-top: 30px;
   width: 60%;
   background: grey;*/
   font-size: 12px;
}
</style>

</head>
<body>

<?php
$titel = 'Inlezen Dracht';
$file = "InsDracht.php";
include "login.php"; ?>

          <TD valign = "top">
<?php
$array_readId = array(); // Aanmaken array. Array waar Id's uit tabel impAgrident worden toegevoegd. Alleen die waarvan de moeder is gedekt door een vader. Zie hier verderop.
if (Auth::is_logged_in()) {
    $volwas_gateway = new VolwasGateway();

include "vw_kzlOoien.php";

If (isset($_POST['knpInsert_']))  {
    include "post_readerDracht.php"; #Deze include moet voor de vervversing in de functie header()
    }

// Array tbv javascript om vader automatisch te tonen
$array_vader_uit_koppel = $volwas_gateway->zoek_laatste_dekkingen_met_vader_zonder_werpdatum($lidId, $Karwerk);

// Array tbv javascript om laatste werpdatum te tonen mist deze binnen X dagen valt
// BCB: typfout in variabelenaam; ik zie dit nergens gebruikt worden
$array_werpdatum_moeer = $volwas_gateway->zoek_laatste_werpdatum($lidId, $Karwerk);

include "insdracht.js.php";
$velden = "rd.Id, rd.datum, rd.moeder, mdr.schaapId mdrId, rd.drachtig, rd.grootte";

$impagrident_gateway = new ImpAgridentGateway();
$tabel = $impagrident_gateway->getInsDrachtFrom();
$WHERE = $impagrident_gateway->getInsDrachtWhere($lidId);

include "paginas.php";
$data = $paginator->fetch_data($velden, "ORDER BY str_to_date(rd.datum,'%d/%m/%Y'), rd.Id");

?>

<table border = 0>
<tr> <form action="InsDracht.php" method = "post">
 <td colspan = 2 style = "font-size : 13px;"> 
  <input type = "submit" name = "knpVervers_" value = "Verversen"></td>
 <td colspan = 2 align = center style = "font-size : 14px;"><?php 
echo $paginator->show_page_numbers(); ?></td>
 <td colspan = 3 align = left style = "font-size : 13px;"> Regels Per Pagina: <?php echo $paginator->show_rpp(); ?> </td>
 <td colspan = 2 align = 'right'><input type = "submit" name = "knpInsert_" value = "Inlezen"> </td>
</tr>
<tr valign = bottom style = "font-size : 12px;">
 <th>Inlezen<br><b style = "font-size : 10px;">Ja/Nee</b><br> <input type="checkbox" id="selectall" checked /> <hr></th>
 <th>Verwij-<br>deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
 <th>Dracht<br>datum<hr></th>
 <th>Moeder<hr></th>
 <th>Vader<hr></th>
 <th>Drachtig<hr></th>
 <th>Worpgrootte<hr></th>
 <th><hr></th>

</tr>

<?php
if($modtech == 1) {
    $schaap_gateway = new SchaapGateway();
    $zoek_moederdieren = $schaap_gateway->zoek_moederdieren_183($lidId, $Karwerk);

$index = 0; 
while ($mdr = $zoek_moederdieren->fetch_assoc()) { 
   $mdrkey[$index] = $mdr['schaapId'];
   $wnrOoi[$index] = $mdr['werknr'];
   $index++; 
} 
unset($index); 
// EINDE Declaratie MOEDERDIER

// Declaratie VADERDIER  ALLEEN OP STAL tussen nu en de afgelopen 2 maanden
$zoek_vaderdieren = $schaap_gateway->zoek_vaderdieren($lidId, $Karwerk);
$index = 0; 
while ($vdr = $zoek_vaderdieren->fetch_assoc()) { 
   $vdrkey[$index] = $vdr['schaapId'];
   $wrknrRam[$index] = $vdr['werknr'];
   $index++; 
} 
unset($index); 
// EINDE Declaratie VADERDIER
}

/**********************************
 **     DUBBELE DRACHT ZOEKEN        **
 **********************************/ 
$array_dub = array();

    if (isset($_POST['knpVervers_']) ) {




$array_rec = array();

foreach($_POST as $key => $value) {
    
    $array_rec[Url::getIdFromKey($key)][Url::getNameFromKey($key)] = $value;
}
foreach($array_rec as $recId => $id) {

// Id ophalen
#echo $recId.'<br>'; 
//var_dump($array_rec);
// Einde Id ophalen
   unset($keuzelOoi);

 foreach($id as $key => $value) {
    
    if ($key == 'kzlOoi' && !empty($value)) { /*echo '$keuzelOoi = '.$value.'<br>';*/ $keuzelOoi = $value; } // betreft schaapId ooi

}

$array_dub[] = $keuzelOoi;

}

}
else {
if(isset($data))  { foreach($data as $key => $array)
    {
    
    $array_dub[] = $array['mdrId']; // schaapId uit tblStal o.b.v. moeder uit reader

}
}

}


/*$array = array(12,43,66,21,56,43,43,78,78,100,43,43,43,21);*/
$vals = array_count_values($array_dub);
//echo 'No. of NON Duplicate Items: '.count($vals).'<br><br>';
/*print_r($vals);*/

/****************************************
 **     EINDE DUBBELE DRACHT ZOEKEN        **
 ****************************************/ 

$array_readId = array(); // Aanmaken array. Array waar Id's uit tabel impAgrident worden toegevoegd. Alleen die waarvan de moeder is gedekt door een vader. Zie hier verderop.

if(isset($data))  {    foreach($data as $key => $array)
    {
        $var = $array['datum'];
    $date = str_replace('/', '-', $var);
    $datum = date('d-m-Y', strtotime($date));
    //$makeday = date_create($date); $day = date_format($makeday, 'Y-m-d');
    
    $Id = $array['Id']; // Id uit tabel impAgrident
    $moeder_rd = $array['moeder']; // levensnummer moeder uit reader
    $mdrId_db = $array['mdrId']; // schaapId uit tblStal

    $drachtig_rd = $array['drachtig'];
    $grootte_rd = $array['grootte'];


if (isset($_POST['knpVervers_']) ) {

    $txtDatum = $_POST["txtDatum_$Id"]; 
    //$makeday = strtotime($txtDatum); $day = date_format($makeday, 'Y-m-d');
    $kzlOoi = $_POST["kzlOoi_$Id"]; if(!empty($kzlOoi)) { unset($moeder_rd); }
    $kzlRam = $_POST["kzlRam_$Id"];
    $kzlDrachtig = $_POST["kzlDracht_$Id"];
    $txtGrootte = $_POST["txtGrootte_$Id"];
}
else { 

    $txtDatum = $datum;
    $kzlOoi = $mdrId_db;
    $kzlDrachtig = $drachtig_rd;
    $txtGrootte = $grootte_rd;

}

// Zoek vader uit laatste dekkingen o.b.v. ooi uit de reader
unset($vdrId_db);
unset($ram_db);

[$vdrId_db, $ram_db] = $volwas_gateway->zoek_vader_uit_laatste_dekkingen($kzlOoi, $Karwerk);

// als vader uit laatste dekking (vdrId_db) wordt gevonden, per record uit tabel impAgrident,
// dan wordt het betreffende readId toegevoegd aan de array $array_readId.
if(isset($vdrId_db)) {
    $array_readId[] = $Id;
}

// Controleren of ingelezen waardes worden gevonden .
$cnt_ooien = $vals[$kzlOoi];
$stal_gateway = new StalGateway();
$afv_status_mdr = $stal_gateway->zoek_afvoerstatus_mdr($lidId, $kzlOoi);
$act_volwId = $volwas_gateway->zoek_laatste_dekking_van_ooi($lidId, $kzlOoi);

[$dmdracht, $drachtdm] = $volwas_gateway->zoek_drachtdatum($act_volwId);
$date_dracht = date_create($dmdracht);
$date_worp = date_create($dmwerp);
$verschil_drachtdm_worp = date_diff($date_dracht, $date_worp);
$dagen_verschil_worp     = $verschil_drachtdm_worp->days;

unset($dagen_verschil_worp); // TODO: (BV) waarom al dat gereken hiervoor, als je de waarde meteen weggooit?

[$dmwerp, $werpdm] = $volwas_gateway->zoek_laatste_werpdatum_dracht($kzlOoi);
$date_dracht = date_create($dmdracht);
$date_worp = date_create($dmwerp);
$verschil_drachtdm_worp = date_diff($date_dracht, $date_worp);
$dagen_verschil_worp     = $verschil_drachtdm_worp->days;

unset($onjuist);
unset($color);

if (!isset($mdrId_db) && !isset($_POST['knpVervers_']) ) { $color = 'red'; $onjuist = 'Ooi '.$moeder_rd.' onbekend'; }
else if (empty($kzlOoi) && isset($_POST['knpVervers_']))  { $color = 'red'; $onjuist = 'Moederdier is onbekend.'; }
else if ($kzlDrachtig == 0)     { $color = 'blue'; $onjuist = ''; } // Drachting is nee
else if ($cnt_ooien > 1 )       { $color = 'blue'; $onjuist = "Dubbele registratie."; }
else if (isset($dmdracht))      { $color = 'red'; $onjuist = 'Deze ooi is reeds drachtig per '.$drachtdm; }
else if(isset($dagen_verschil_worp) && $dagen_verschil_worp > 0 && $dagen_verschil_worp < 183) { $color = 'red'; $onjuist = 'Deze ooi heeft op '.$werpdm.' nog geworpen. Een ooi kan 1x per half jaar werpen.'; }
else if (isset($afv_status_mdr))   { $color = 'red'; $onjuist = 'Ooi '.$moeder_rd.' is '.$afv_status_mdr; }
else if (empty($txtDatum))         { $color = 'red'; $onjuist = 'De drachtdatum is onbekend'; }

    if (isset($onjuist)) {    $oke = 0;    } else {    $oke = 1;    } // $oke kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.
// EINDE Controleren of ingelezen waardes worden gevonden . 

unset($cbDel);
     if (isset($_POST['knpVervers_']) && $_POST["laatsteOke_$Id"] == 0 && $oke == 1) /* Als onvolledig is gewijzigd naar volledig juist */ {$cbKies = 1; $cbDel = 0; }
else if (isset($_POST['knpVervers_'])) { $cbKies = $_POST["chbKies_$Id"];  $cbDel = $_POST["chbDel_$Id"]; } 
   else { $cbKies = $oke; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet 

   if ($kzlDrachtig == 0) { $cbKies = 0; $cbDel = 1; } // Drachtig is Nee ?>

<!--    **************************************
        **            OPMAAK  GEGEVENS            **
        ************************************** -->

<tr style = "font-size:14px;">
 <td align = center>
<?php ##echo $Id; ?>
    <!-- <input type = hidden size = 1 name = <?php echo "chbKies_$Id"; ?> value = 0 > --> <!-- hiddden -->
    <input type = checkbox           name = <?php echo "chbKies_$Id"; ?> value = 1 
      <?php echo $cbKies == 1 ? 'checked' : ''; /* Als voorwaarde goed zijn of checkbox is aangevinkt */

      if ($oke == 0) /*Als voorwaarde niet klopt */ { ?> disabled <?php } else { ?> class="checkall" <?php } /* class="checkall" zorgt dat alles kan worden uit- of aangevinkt*/ ?> >
    <input type = hidden size = 1 name = <?php echo "laatsteOke_$Id"; ?> value = <?php echo $oke; ?> > <!-- hiddden -->
 </td>
 <td align = center>
    <!-- <input type = hidden size = 1 name = <?php echo "chbDel_$Id"; ?> value = 0 > -->
    <input type = checkbox class="delete" name = <?php echo "chbDel_$Id"; ?> value = 1 <?php if(isset($cbDel)) { echo $cbDel == 1 ? 'checked' : ''; } ?> >
 </td>
 <td>
     <input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtDatum_$Id"; ?> value = <?php echo $txtDatum; ?> >
 </td>

 <?php $width = 25+(8*$Karwerk) ; ?>

 <td style = "font-size : 11px;">
<!-- KZLMOEDER -->
 <select id= <?php echo "ooi_$Id"; ?> onchange = <?php echo "toon_dracht(".$Id.")"; ?> style= "width:<?php echo $width; ?>; font-size:12px;" name = <?php echo "kzlOoi_$Id"; ?> >
  <option></option>
<?php    $count = count($wnrOoi);
for ($i = 0; $i < $count; $i++){

    $opties = array($mdrkey[$i]=>$wnrOoi[$i]);
            foreach($opties as $key => $waarde)
            {
  if ((!isset($_POST['knpVervers_']) && $mdrId_db == $key) || (isset($_POST["kzlOoi_$Id"]) && $_POST["kzlOoi_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
}
?> </select> 
    <!-- EINDE KZLMOEDER --> 
 </td>
 <td width = <?php echo $width; ?> > 
 <div id= <?php echo "dbRam_$Id"; ?> align = "center" > <?php echo $ram_db; ?> </div> <!-- dit toont het vaderdier bij laden van de pagina -->
    <!-- KZLVADER -->
 <select style= "width:<?php echo $width; ?>; font-size:12px;" id= <?php echo "ram_$Id"; ?> class= "<?php echo $Id; ?> selectt" name = <?php echo "kzlRam_$Id"; ?> >
 <option></option>    
<?php    $count = count($wrknrRam);
for ($i = 0; $i < $count; $i++){

        
    $opties= array($vdrkey[$i]=>$wrknrRam[$i]);
            foreach ($opties as $key => $waarde)
            {
  if ((!isset($_POST['knpVervers_']) && $vdrId_db == $vdrkey[$i]) || (isset($_POST["kzlRam_$Id"]) && $_POST["kzlRam_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }    
            }
        
} ?>
 </select><p id= <?php echo "result_ram_$Id"; ?> align = "center" ></p> <!-- dit toont het vaderdier na wijzigen van het moederdier -->
    <!-- EINDE KZLVADER -->

 </td>
 <td>
    <!-- KZLDRACHTIG -->
    <select style="width:50; font-size:12px;" name= <?php echo "kzlDracht_$Id"; ?> >
<?php 
$opties = array('Nee', 'Ja');
foreach ( $opties as $key => $waarde)
{
   $keuze = '';
   if((!isset($_POST['knpVervers_']) && $drachtig_rd == $key) || (isset($_POST["kzlDracht_$Id"]) && $_POST["kzlDracht_$Id"] == $key))
   {
   echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
 } else {
   echo '<option value="' . $key . '">' . $waarde . '</option>';
   }
} ?>
</select>
    <!-- EINDE KZLDRACHTIG -->
 </td>
 <td align="center">
    <input type = "text" size = 1 style = "font-size : 11px; text-align : right;" name = <?php echo "txtGrootte_$Id"; ?> value = <?php echo $txtGrootte; ?> >
 </td>
 
 <td style = "color: <?php echo $color; ?> ; font-size:12px; " >  

<div id= <?php echo "bericht_$Id"; ?> > <?php if (isset($onjuist)) { echo $onjuist; } ?> </div> 

 </td>
 <td></td>    
 <td></td> 
</tr>
<!--    **************************************
    **    EINDE OPMAAK GEGEVENS    **
    ************************************** -->

<?php 
} 
} //einde if(isset($data)) ?>
</table>
</form> 



</TD>
<?php
include "menu1.php"; } ?>
</tr>

</table>

<?php
include "select-all.js.php";
include "insdracht-2.js.php";
?>
</body>
</html>
