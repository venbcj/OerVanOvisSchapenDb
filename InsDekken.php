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

If (isset($_POST['knpInsert_']))  {
    //include url
    include "post_readerDekken.php"; #Deze include moet voor de vervversing in de functie header()
    }

unset($vdrId_rd);
//if($reader == 'Agrident') {
$velden = "rd.Id Id, rd.datum, rd.moeder, mdr.schaapId mdrId, rd.vdrId, vdr.vader";

$tabel = "
impAgrident rd
 left join (
     SELECT s.schaapId, s.levensnummer
     FROM tblSchaap s
      join tblStal st on (s.schaapId = st.schaapId)
     WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
     ) mdr on (mdr.levensnummer = rd.moeder)
 left join (
     SELECT s.schaapId, s.levensnummer vader
     FROM tblSchaap s
     ) vdr on (vdr.schaapId = rd.vdrId)
";

$WHERE = "WHERE rd.lidId = '".mysqli_real_escape_string($db,$lidId)."' and rd.actId = 18 and isnull(verwerkt) ";

include "paginas.php";
$data = $page_nums->fetch_data($velden, "ORDER BY date_format(rd.datum,'%d/%m/%Y'), rd.Id");

?>

<table border = 0>
<tr> <form action="InsDekken.php" method = "post">
 <td colspan = 2 style = "font-size : 13px;"> 
  <input type = "submit" name = "knpVervers_" value = "Verversen"></td>
 <td colspan = 2 align = center style = "font-size : 14px;"><?php 
echo $page_numbers; ?></td>
 <td colspan = 3 align = left style = "font-size : 13px;"> Regels Per Pagina: <?php echo $kzlRpp; ?> </td>
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
// Declaratie MOEDERDIER alleen op stal en niet geworpen laatste 60 dagen
$zoek_moederdieren = mysqli_query($db,"
SELECT st.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk) werknr
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 left join (
     SELECT stalId, hisId
     FROM tblHistorie h
      join tblActie a on (h.actId = a.actId)
     WHERE a.af = 1 and h.actId != 10 and h.skip = 0
 ) haf on (haf.stalId = st.stalId)
 join (
     SELECT schaapId
     FROM tblStal st
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.geslacht = 'ooi' and isnull(haf.hisId)
ORDER BY right(s.levensnummer,$Karwerk)
") or die (mysqli_error($db));


$index = 0; 
while ($mdr = mysqli_fetch_assoc($zoek_moederdieren)) 
{ 
   $mdrkey[$index] = $mdr['schaapId'];
   $wnrOoi[$index] = $mdr['werknr'];
   $index++; 
} 
unset($index); 
// EINDE Declaratie MOEDERDIER

// Declaratie VADERDIER  ALLEEN OP STAL tussen nu en de afgelopen 2 maanden
$zoek_vaderdieren = mysqli_query($db,"
SELECT st.schaapId, right(s.levensnummer,$Karwerk) werknr
FROM tblSchaap s 
 join tblStal st on (st.schaapId = s.schaapId)
 left join (
    SELECT stalId, hisId, datum
     FROM tblHistorie h
      join tblActie a on (h.actId = a.actId)
     WHERE a.af = 1 and h.actId != 10 and h.skip = 0
 ) haf on (haf.stalId = st.stalId)
 join (
     SELECT schaapId
     FROM tblStal st
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.geslacht = 'ram' and ( isnull(haf.hisId) or date_add(haf.datum,interval 2 month) > CURRENT_DATE() )
ORDER BY right(levensnummer,$Karwerk)
") or die (mysqli_error($db)); 


$index = 0; 
while ($vdr = mysqli_fetch_assoc($zoek_vaderdieren)) 
{ 
   $vdrkey[$index] = $vdr['schaapId'];
   $wrknrRam[$index] = $vdr['werknr'];
   $index++; 
} 
unset($index); 
// EINDE Declaratie VADERDIER
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

if(!empty($kzlOoi)) {
$zoek_moeder = mysqli_query($db,"
SELECT levensnummer
FROM tblSchaap s
WHERE s.schaapId = '".mysqli_real_escape_string($db,$kzlOoi)."'
") or die (mysqli_error($db));

while ($moe = mysqli_fetch_assoc($zoek_moeder)) { $moeder_db = $moe['levensnummer']; }

$zoek_laatste_dekking_van_ooi = mysqli_query($db,"
SELECT v.mdrId, max(v.volwId) volwId
FROM tblVolwas v
 left join (
        SELECT hisId
        FROM tblHistorie h
         join tblStal st on (st.stalId = h.stalId)
        WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$kzlOoi)."'
 ) hv on (hv.hisId = v.hisId)
 left join (
        SELECT d.volwId, date_format(h.datum,'%d-%m-%Y') drachtdatum
        FROM tblDracht d 
     join tblHistorie h on (h.hisId = d.hisId)
     join tblStal st on (st.stalId = h.stalId)
    WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$kzlOoi)."'
 ) d on (v.volwId = d.volwId)
 left join tblSchaap k on (k.volwId = v.volwId)
 left join (
    SELECT s.schaapId
    FROM tblSchaap s
     join tblStal st on (s.schaapId = st.schaapId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) ha on (k.schaapId = ha.schaapId)
WHERE (hv.hisId is not null or d.volwId is not null) and isnull(ha.schaapId) and v.mdrId = '".mysqli_real_escape_string($db,$kzlOoi)."'
GROUP BY v.mdrId
") or die (mysqli_error($db));

while ( $zad = mysqli_fetch_assoc($zoek_laatste_dekking_van_ooi)) { $act_volwId = $zad['volwId']; }

unset($dmdracht);

$zoek_drachtdatum = mysqli_query($db,"
SELECT h.datum dmdracht, date_format(h.datum,'%d-%m-%Y') drachtdm
FROM tblVolwas v
 join tblDracht d on (v.volwId = d.volwId)
 join tblHistorie h on (d.hisId = h.hisId)
WHERE h.skip = 0 and v.volwId = '".mysqli_real_escape_string($db,$act_volwId)."'
") or die (mysqli_error($db));

while ($zddm = mysqli_fetch_assoc($zoek_drachtdatum)) { $dmdracht = $zddm['dmdracht']; $drachtdm = $zddm['drachtdm']; }


unset($lst_volwId);
unset($dmwerp);
unset($dagen_verschil_worp);

$zoek_vader_laatste_dekkingen = mysqli_query($db,"
SELECT right(levensnummer,".$Karwerk.") werknr
FROM tblSchaap vdr
 join tblVolwas v on (v.vdrId = vdr.schaapId)
WHERE v.volwId = '".mysqli_real_escape_string($db,$act_volwId)."'
") or die (mysqli_error($db));

while ($zvd = mysqli_fetch_assoc($zoek_vader_laatste_dekkingen)) { $vdr_worp = $zvd['werknr']; }

$zoek_laatste_worp = mysqli_query($db,"
SELECT max(v.volwId) volwId
FROM tblVolwas v
 join tblSchaap l on (l.volwId = v.volwId)
 left join tblSchaap k on (k.volwId = v.volwId)
 left join (
    SELECT s.schaapId
    FROM tblSchaap s
     join tblStal st on (s.schaapId = st.schaapId)
    join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) ha on (k.schaapId = ha.schaapId)
WHERE v.mdrId = '".mysqli_real_escape_string($db,$kzlOoi)."' and isnull(ha.schaapId)
") or die (mysqli_error($db));

while ($zlw = mysqli_fetch_assoc($zoek_laatste_worp)) { $lst_volwId = $zlw['volwId']; }

if(isset($lst_volwId)) {
$zoek_werpdatum = mysqli_query($db,"
SELECT h.datum, date_format(h.datum,'%d-%m-%Y') werpdm
FROM tblVolwas v
 join tblSchaap l on (l.volwId = v.volwId)
 join tblStal st on (l.schaapId = st.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
WHERE h.actId = 1 and h.skip = 0 and v.volwId = '".mysqli_real_escape_string($db,$lst_volwId)."'
") or die (mysqli_error($db));

while ($zwd = mysqli_fetch_assoc($zoek_werpdatum)) { $dmwerp = $zwd['datum']; $werpdm = $zwd['werpdm']; }

$date_dracht = date_create($dmdracht);
$date_worp = date_create($dmwerp);

$verschil_drachtdm_worp = date_diff($date_dracht, $date_worp);
$dagen_verschil_worp     = $verschil_drachtdm_worp->days;

}

unset($afv_status_mdr);
$zoek_afvoerstatus_mdr = mysqli_query($db,"
SELECT lower(a.actie) actie
FROM tblStal st
 join (
     SELECT max(stalId) stalId
     FROM tblStal
     WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and schaapId = '".mysqli_real_escape_string($db,$kzlOoi)."'
 ) maxst on (maxst.stalId = st.stalId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblActie a on (a.actId = h.actId)
WHERE a.af = 1 and h.actId != 10 and h.skip = 0
") or die (mysqli_error($db));

while ($sm = mysqli_fetch_assoc($zoek_afvoerstatus_mdr)) 
{ 
   $afv_status_mdr = $sm['actie'];
}


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
