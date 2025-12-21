<?php

require_once("autoload.php");


$versie = '18-12-2021'; /* Gekopieerd van insDracht.php */
$versie = '02-03-2023'; /* zoek_vader_laatste_dekkingen toegevoegd */
$versie = '31-12-2023'; /* and h.skip = 0 toegevoegd bij tblHistorie */
$versie = '07-09-2024'; /* Periode tussen werpen en dekken teruggebracht naar 60 i.p.v. 183 dagen */
$versie = '23-11-2024'; /* In keuzelijst moeder- en vaderdieren  uitgeschaarde dieren wel tonen. zoek_afvoerstatus_mdr aangevuld met h.actId != 10 */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */

 Session::start();
 ?>
<!DOCTYPE html>
<html>
<head>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Registratie</title>
</head>
<body>

<?php
$titel = 'Inlezen Dekken'; 
$file = "InsDekken.php";
include "login.php"; ?>

        <TD valign = "top">
<?php
if (Auth::is_logged_in()) {
    $schaap_gateway = new SchaapGateway();
    $impagrident_gateway = new ImpAgridentGateway();

If (isset($_POST['knpInsert_']))  {
    include "post_readerDekken.php"; #Deze include moet voor de vervversing in de functie header()
    }

unset($vdrId_rd);
$velden = "rd.Id Id, rd.datum, rd.moeder, mdr.schaapId mdrId, rd.vdrId, vdr.vader";

$tabel = $impagrident_gateway->getInsDekkenFrom();
$WHERE = $impagrident_gateway->getInsDekkenWhere($lidId);

include "paginas.php";
$data = $paginator->fetch_data($velden, "ORDER BY date_format(rd.datum,'%d/%m/%Y'), rd.Id");

?>

<table border = 0>
<tr> <form action="InsDekken.php" method = "post">
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
 <th>Dekdatum<hr></th>
 <th>Moeder<hr></th>
 <th>Vader<hr></th>
 <th><hr></th>

</tr>

<?php
if($modtech == 1) {
    $zoek_moederdieren = $schaap_gateway->zoek_moederdieren($lidId, $Karwerk);

$index = 0; 
while ($mdr = $zoek_moederdieren->fetch_assoc()) { 
   $mdrkey[$index] = $mdr['schaapId'];
   $wnrOoi[$index] = $mdr['werknr'];
   $index++; 
} 
unset($index); 

$zoek_vaderdieren = $schaap_gateway->zoek_vaderdieren($lidId, $Karwerk);

$index = 0; 
while ($vdr = $zoek_vaderdieren->fetch_assoc()) { 
   $vdrkey[$index] = $vdr['schaapId'];
   $wrknrRam[$index] = $vdr['werknr'];
   $index++; 
} 
unset($index); 
}

/*************************************
 **     DUBBELE DEKKINGEN ZOEKEN        **
 *************************************/ 
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

    unset($kzlOoi);
    unset($kzlRam);
   
 foreach($id as $key => $value) {
    
    if ($key == 'kzlOoi' && !empty($value)) { /*echo '$kzlOoi = '.$value.'<br>';*/ $kzlOoi = $value; } // betreft schaapId ooi
    if ($key == 'kzlRam' ) { /*echo '$kzlOoi = '.$value.'<br>';*/ $kzlRam = $value; } // betreft schaapId ooi

}

$array_dub[] = $kzlOoi.'_'.$kzlRam;

}

}
else {
if(isset($data))  { foreach($data as $key => $array)
    {
    
    $mdrId_rd = $array['mdrId']; // schaapId uit tblStal o.b.v. moeder uit reader
    $vdrId_rd = $array['vdrId']; // schaapId uit reader

    $array_dub[] = $mdrId_rd.'_'.$vdrId_rd;

}
}

}


/*$array = array(12,43,66,21,56,43,43,78,78,100,43,43,43,21);*/
$vals = array_count_values($array_dub);
//echo 'No. of NON Duplicate Items: '.count($vals).'<br><br>';
/*print_r($vals);
echo '<br>';*/

/*******************************************
 **     EINDE DUBBELE DEKKINGEN ZOEKEN        **
 *******************************************/ 

if(isset($data))  { foreach($data as $key => $array)
    {
        $var = $array['datum'];
$date = str_replace('/', '-', $var);
$datum = date('d-m-Y', strtotime($date)); #echo '$datum = '.$datum.'<br>';
    
    $Id = $array['Id'];
    $moeder_rd = $array['moeder']; // levensnummer uit reader
    $mdrId_rd = $array['mdrId']; // schaapId uit tblStal o.b.v. moeder uit reader

    $vdrId_rd = $array['vdrId']; // schaapId uit reader
    $vader_rd = $array['vader']; // levensnummer ram o.b.v. schaapId uit reader


// Controleren of ingelezen waardes worden gevonden .
if (isset($_POST['knpVervers_']) ) {

    $txtDatum = $_POST["txtDatum_$Id"]; 
    $kzlOoi = $_POST["kzlOoi_$Id"]; 
    $kzlRam = $_POST["kzlRam_$Id"]; 
}
else { 
    $txtDatum = $datum;
    $kzlOoi = $mdrId_rd;
    $kzlRam = $vdrId_rd;
}


//echo '$kzlOoi._.$kzlRam = '.$kzlOoi.'_'.$kzlRam.'<br>';
$cnt_ooien = $vals[$kzlOoi.'_'.$kzlRam];

if (!empty($kzlOoi)) {
    $moeder_db = $schaap_gateway->zoek_bestaand_levensnummer($kzlOoi);
    $act_volwId = $schaap_gateway->zoek_laatste_dekking_van_ooi($lidId, $kzlOoi);
    $volwas_gateway = new VolwasGateway();
    [$dmdracht, $drachtdm] = $volwas_gateway->zoek_drachtdatum($act_volwId);
    unset($lst_volwId);
    unset($dmwerp);
    unset($dagen_verschil_worp);
    $vdr_worp = $schaap_gateway->zoek_vader_laatste_dekkingen($act_volwId, $Karwerk);
    $lst_volwId = $volwas_gateway->zoek_laatste_worp($kzlOoi);
    if (isset($lst_volwId)) {
        [$dmwerp, $werpdm] = $volwas_gateway->zoek_werpdatum($lst_volwId);
        $date_dracht = date_create($dmdracht);
        $date_worp = date_create($dmwerp);
        $verschil_drachtdm_worp = date_diff($date_dracht, $date_worp);
        $dagen_verschil_worp     = $verschil_drachtdm_worp->days;
    }
    $stal_gateway = new StalGateway();
    $afv_status_mdr = $stal_gateway->zoek_afvoerstatus_mdr($lidId, $kzlOoi);
} // Einde if(!empty($kzlOoi)) 

unset($onjuist);
unset($color);

if (!isset($moeder_db) || empty($kzlOoi))    { $color = 'red'; $onjuist = 'Ooi '.$moeder_rd.' onbekend'; }
 else if ($cnt_ooien > 1 )                     { $color = 'blue'; $onjuist = "Dubbel in de reader."; }
 else if (isset($dmdracht))                 { $color = 'red'; $onjuist = 'Deze ooi is reeds drachtig per '.$drachtdm; }
 else if(isset($dagen_verschil_worp) && $dagen_verschil_worp < 60) { $color = 'red'; $onjuist = 'Deze ooi heeft op '.$werpdm.' nog geworpen. Een ooi kan 1x per 2 maanden werpen.'; } // moederdier heeft laatste 60 dagen al gelammerd
 else if (isset($afv_status_mdr))                      { $color = 'red'; $onjuist = 'Ooi '.$moeder_db.' is '.$afv_status_mdr; }
 else if (!isset($txtDatum) && empty($txtDatum))                      { $color = 'red'; $onjuist = 'De datum is onbekend.'; }


    if (isset($onjuist)) {    $oke = 0;    } else {    $oke = 1;    } // $oke kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.
// EINDE Controleren of ingelezen waardes worden gevonden . 

     if (isset($_POST['knpVervers_']) && $_POST["laatsteOke_$Id"] == 0 && $oke == 1) /* Als onvolledig is gewijzigd naar volledig juist */ {$cbKies = 1; $cbDel = $_POST["chbDel_$Id"]; }
else if (isset($_POST['knpVervers_'])) { $cbKies = $_POST["chbKies_$Id"];  $cbDel = $_POST["chbDel_$Id"]; } 
   else { $cbKies = $oke; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet ?>

<!--    **************************************
        **            OPMAAK  GEGEVENS            **
        ************************************** -->

<tr style = "font-size:14px;">
 <td align = center>

    <input type = hidden size = 1 name = <?php echo "chbKies_$Id"; ?> value = 0 > <!-- hiddden -->
    <input type = checkbox           name = <?php echo "chbKies_$Id"; ?> value = 1 
      <?php echo $cbKies == 1 ? 'checked' : ''; /* Als voorwaarde goed zijn of checkbox is aangevinkt */

      if ($oke == 0) /*Als voorwaarde niet klopt */ { ?> disabled <?php } else { ?> class="checkall" <?php } /* class="checkall" zorgt dat alles kan worden uit- of aangevinkt*/ ?> >
    <input type = hidden size = 1 name = <?php echo "laatsteOke_$Id"; ?> value = <?php echo $oke; ?> > <!-- hiddden -->
 </td>
 <td align = center>
    <input type = hidden size = 1 name = <?php echo "chbDel_$Id"; ?> value = 0 >
    <input type = checkbox class="delete" name = <?php echo "chbDel_$Id"; ?> value = 1 <?php if(isset($cbDel)) { echo $cbDel == 1 ? 'checked' : ''; } ?> >
 </td>
 <td>
     <input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtDatum_$Id"; ?> value = <?php echo $txtDatum; ?> >
 </td>

 <?php $width = 25+(8*$Karwerk) ; ?>
 
 <td style = "font-size : 11px;">
<!-- KZLMOEDER -->
 <select style= "width:<?php echo $width; ?>; font-size:12px;" name = <?php echo "kzlOoi_$Id"; ?> >
  <option></option>
<?php    $count = count($wnrOoi);
for ($i = 0; $i < $count; $i++){

    $opties = array($mdrkey[$i]=>$wnrOoi[$i]);
            foreach($opties as $key => $waarde)
            {
  if ((!isset($_POST['knpVervers_']) && $mdrId_rd == $key) || (isset($_POST["kzlOoi_$Id"]) && $_POST["kzlOoi_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
}
?> </select> 
    <!-- EINDE KZLMOEDER --> 
 </td>
 <td> 
     <?php if(isset($vdr_worp)) { echo $vdr_worp; } else { ?>
    <!-- KZLVADER -->
 <select style= "width:<?php echo $width; ?>; font-size:12px;" name = <?php echo "kzlRam_$Id"; ?> >
 <option></option>    
<?php    $count = count($wrknrRam);
for ($i = 0; $i < $count; $i++){

        
    $opties= array($vdrkey[$i]=>$wrknrRam[$i]);
            foreach ($opties as $key => $waarde)
            {
  if ((!isset($_POST['knpVervers_']) && $vdrId_rd == $vdrkey[$i]) || (isset($_POST["kzlRam_$Id"]) && $_POST["kzlRam_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }    
            }
        
} ?>
 </select>
    <!-- EINDE KZLVADER -->
<?php } // Einde if(isset($vdr_worp)) ?>
 </td>
 <td style = "color: <?php echo $color; ?> ; font-size:12px; " > <?php 

 if (isset($onjuist)) { echo $onjuist; } ?>

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
include "menu1.php"; }

include "select-all.js.php";
?>
</tr>

</table>

</body>
</html>
