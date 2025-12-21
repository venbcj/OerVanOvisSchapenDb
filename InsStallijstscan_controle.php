<?php

require_once("autoload.php");

$versie = '05-08-2023'; /* kopie gemaaky van InsAanvoer 
op 21-8-2023 heeft Rina het volgende verzocht
- Geboren niet verplicht, alleen als er een melding naar RVO moet.
- Geslacht niet verplicht behalve als er voor moeder (ooi) of vader (ram) wordt gekozen
- Generatie verplicht, omdat een lam in een verblijf wordt geplaatst.
- Registratie verplicht als er een melding naar RVO moet.
- Misschien nog het Ras toevoegen? */
$versie = '31-12-2023'; /* and h.skip = 0 toegevoegd bij tblHistorie */
$versie = "11-03-2024"; /* Bij geneste query uit 
join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId) gewijzgd naar
join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
I.v.m. historie van stalId 22623. Dit dier is eerst verkocht en met terugwerkende kracht geplaatst in verblijf Afmest 1 */
$versie = "13-12-2024"; /* Niet gescande dieren onderaan gezet en link naar deze dieren toegevoegd. Ook controle en foutmeldingen samengevoegd, zie onjuist */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */
$versie = '15-07-2025'; /* Veld/keuzelijst ubn toegevoegd */

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
$titel = 'Inlezen Stallijstcontrole';
$file = "InsStallijstscan_controle.php";
include "login.php"; ?>

            <TD valign = "top">
<?php
if (Auth::is_logged_in()) {
    $impagrident_gateway = new ImpAgridentGateway();

If (isset($_POST['knpInsert_']))  {
    include "post_readerStalscan.php";
    }

$velden = "rd.actId, rd.Id readId, rd.datum, rd.levensnummer levnr_rd, coalesce(r.ras,'onbekend') ras, stal.lidId, stal.ubn, stal.geslacht,  
 ouder.ouder,
 af.actie,
 hk.hoknr,
stal.levensnummer levnr_stal, hg.gebdm, dup.dubbelen ";

    $tabel = $impagrident_gateway->getInsStallijstscanFrom();
    $WHERE = $impagrident_gateway->getInsStallijstscanWhere($lidId);

include "paginas.php";
$data = $paginator->fetch_data($velden, "ORDER BY s.lidId asc, dup.dubbelen desc, actId desc, rd.datum, rd.Id");

?>
<form action="InsStallijstscan_controle.php" method = "post">
<?php
$nieuw = $impagrident_gateway->aantal_niet_op_stallijst($lidId);
?>

<table border = 0> 
<tr> 
 <td colspan = 2 style = "font-size : 13px;"> 
  <input type = "submit" name = "knpVervers_" value = "Verversen"></td>
 <td colspan = 2 align = "center" style = "font-size : 14px;"><?php 
echo $paginator->show_page_numbers(); ?></td>
 <td colspan = 3 align = left style = "font-size : 13px;"> Regels Per Pagina: <?php echo $paginator->show_rpp(); ?> </td>
 <td align = 'right'> <input type = "submit" name = "knpInsert_" value = "Inlezen">&nbsp &nbsp </td>
 <td colspan= 2 style = "font-size : 12px;"><b style = "color : red;">!</b> = waarde uit reader niet herkend. <br><br> * Melding RVO maken Ja/Nee
 </td>
 <td></td>
 <td> <a href="#NietGescand" style="font-size : 12px; color:blue" > Niet gescande schapen</a> </td>
</tr>
<tr style = "font-size : 12px;">
 <th valign = bottom >Inlezen<br><b style = "font-size : 10px;">Ja/Nee</b><br> <input type="checkbox" id="selectall" checked /> <hr></th>
 <th valign = bottom >Verwij-<br>deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
 <th valign = bottom >Scan<br>datum<hr></th>
 <th valign = bottom >Ubn<hr></th>
 <th valign = bottom >Levensnummer<hr></th>

 <th valign = bottom >Geboren<hr></th>
 <th valign = bottom >Geslacht<hr></th>
 <th valign = bottom >Generatie<hr></th>
  <th valign = bottom >Ras<hr></th>
 <?php if($modtech == 1) { ?>
  <th valign = bottom >Verblijf<hr></th>
 <?php } if($nieuw > 0) { ?>
 <th valign = bottom >Registratie<hr></th>
 <th width="75" valign = bottom style = "font-size : 11px;">RVO*<hr></th>
<?php } ?>
 <td valign = "center" align="center" >
         <a href="exportStallijstScanControle.php?pst=<?php echo $lidId; ?>'"> Export-xlsx </a>

 </td>
</tr>

<?php
// Declaratie ubn
    $ubn_gateway = new UbnGateway();
$declaratie_kzlUbn = $ubn_gateway->lijst($lidId);
$index = 0; 
while ($du = $declaratie_kzlUbn->fetch_array()) {
   $ubnId[$index] = $du['ubnId']; 
   $ubnnm[$index] = $du['ubn'];
   $index++; 
}
unset($index);
// Einde Declaratie ubn

// Declaratie ras
$ras_gateway = new RasGateway();
$RAS = $ras_gateway->rassen($lidId);
$index = 0; 
while ($ras = $RAS->fetch_array()) {
   $rasId[$index] = $ras['rasId']; 
   $rasnm[$index] = $ras['ras'];
   $index++; 
}
unset($index);
// EINDE Declaratie ras

if($modtech == 1) {

// Declaratie HOKNUMMER            // lower(if(isnull(scan),'6karakters',scan)) zorgt ervoor dat $raak nooit leeg is. Anders worden legen velden gevonden in legen velden binnen impReader.
    $hok_gateway = new HokGateway();
$qryHoknummer = $hok_gateway->kzlHok($lidId);

$index = 0; 
while ($hknr = $qryHoknummer->fetch_assoc()) { 
   $hoknId[$index] = $hknr['hokId']; 
   $hoknum[$index] = $hknr['hoknr'];
   $index++; 
} 
unset($index);
// EINDE Declaratie HOKNUMMER
}


// Declaratie ACTIE
$actie_gateway = new ActieGateway();
$qryActie = $actie_gateway->getListOp1();
$index = 0; 
while ($qa = $qryActie->fetch_assoc()) { 
   $actieId[$index] = $qa['actId']; 
   $actnm[$index] = $qa['actie'];
   $index++; 
} 
unset($index);
// EINDE Declaratie ACTIE

if(isset($data))  {    

//echo count($data);

    foreach($data as $key => $array)
    {
        $scandate = $array['datum'];
$date = str_replace('/', '-', $scandate);
//$gebdatum = date('d-m-Y', strtotime($date)-365*60*60*24);
$scandatum = date('d-m-Y', strtotime($date));
    
    $Id = $array['readId'];
    $ubn_st = $array['ubn']; // ubn van dier dat op de stal aanwezig is
    $levnr_rd = $array['levnr_rd']; //if (strlen($levnr_rd)== 11) {$levnr_rd = '0'.$array['levnr'];}
    $levnr_dupl = $array['dubbelen']; // twee keer in reader bestand
    $levnr_stal = $array['levnr_stal'];
    $ras_stal = $array['ras'];
    $eigenDier = $array['lidId']; // Als levensnummer bestaat maar niet op eigen stallijst dan bestaat lidId niet
    $geslacht_stal = $array['geslacht'];
    $ouder = $array['ouder']; if(isset($ouder) && $geslacht_stal == 'ooi') { $fase = 'moeder'; } else if(isset($ouder) && $geslacht_stal == 'ram') { $fase = 'vader'; } else { $fase = 'lam'; }
     $afvoer = $array['actie'];
     $verblijf = $array['hoknr'];
     $gebdm = $array['gebdm'];


unset($schaapId_db);
unset($gebdag_db);
unset($geslacht_db);
unset($aanwas_db);
unset($fase_db);
unset($ras_db);

if($levnr_stal == 0) {

    $schaap_gateway = new SchaapGateway();
    $zoek_levnr_db = $schaap_gateway->zoek_levnr($levnr_rd);
    while ($zld = $zoek_levnr->fetch_assoc()) {
        $schaapId_db = $zld['schaapId'];
        $gebdag_db = $zld['gebdag'];
        $geslacht_db = $zld['geslacht'];
        $aanwas_db = $zld['his_aanw']; if( isset($aanwas_db) && $geslacht_db == 'ooi') { $fase_db = 'moeder'; } else if( isset($aanwas_db) && $geslacht_db == 'ram') { $fase_db = 'vader'; } else { $fase_db = 'lam'; }
        $ras_db = $zld['ras']; 
    }


}

unset($txtDmgeb);

// Controleren of ingelezen waardes worden gevonden .
$kzlRas = $ras_stal; /*$kzlOoi = $mdr_db;*/ 
$kzlHok = $hok_db;  
if (isset($_POST['knpVervers_'])) {
$scandatum = $_POST["txtScandm_$Id"]; 
$txtGebdm  = $_POST["txtGebdm_$Id"]; if(!empty($txtGebdm)) { $txtDmgeb = date_format(date_create($txtGebdm), 'Y-m-d'); }
//$kzlRas = $_POST["kzlras_$Id"]; 
$kzlSekse = $_POST["kzlSekse_$Id"]; 
$kzlFase = $_POST["kzlFase_$Id"]; //echo $kzlFase.'<br>';
$kzlActie = $_POST["kzlActie_$Id"]; //echo $kzlActie.'<br>'.'<br>';
if($modtech == 1) { $kzlHok = $_POST["kzlHok_$Id"]; }
     }

$date2 = eerste_datum_na_geboortedatum($schaapId_db);
$datum2 = date_format(date_create($date2), 'd-m-Y');

unset($onjuist);
unset($color);

if (isset($levnr_dupl))                 { $color = 'blue'; $onjuist = "Dubbel in de reader."; }
else if (empty($scandatum))             { $color = 'red'; $onjuist = "De datum is onbekend."; }
else if (isset($afvoer))                 { $color = 'red'; $onjuist = "Dit schaap is ".strtolower($afvoer)."."; }  
else if (isset($levnr_rd) && strlen($levnr_rd) <> 12) { $color = 'red'; $onjuist = "Levensnummer geen 12 karakters."; }  
else if (Validate::numeriek($levnr_rd) == 1)     { $color = 'red'; $onjuist = "Levensnummer bevat een letter."; } 
else if (isset($txtDmgeb) && isset($date2) && $date2 < $txtDmgeb)    { $color = 'red'; $onjuist = "De geboortedatum mag niet na ".$datum2." liggen."; }
else if ($levnr_stal == 0) { // Als het levensnummer niet op de stallijst staat

    if($kzlActie == 1 && empty($txtGebdm)) { $color = 'red'; $onjuist = "De geboortedatum is onbekend."; }
    else if(!isset($schaapId_db) && empty($kzlFase)) { $color = 'red'; $onjuist = "De generatie is onbekend."; }
    else if($kzlFase == 'lam' && empty($kzlHok)) { $color = 'red'; $onjuist = "Het verblijf is onbekend."; }
    else if(empty($kzlActie))             { $color = 'red'; $onjuist = "De registratie is onbekend."; }
    else if($kzlActie == 1 && ($kzlFase == 'moeder' || $kzlFase == 'vader') ) { $color = 'red'; $onjuist = "De generatie en registratie is tegenstrijdig."; }
    else if($kzlSekse == 'ooi' && $kzlFase == 'vader') { $color = 'red'; $onjuist = "Het geslacht en generatie is tegenstrijdig."; }
    else if($kzlSekse == 'ram' && $kzlFase == 'moeder') { $color = 'red'; $onjuist = "Het geslacht en generatie is tegenstrijdig."; }

} // Einde else if ($levnr_stal == 0)

if(isset($onjuist)) { $oke = 0; } else { $oke = 1; } // $oke kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.
// EINDE Controleren of ingelezen waardes worden gevonden .  

     if (isset($_POST['knpVervers_']) && $_POST["laatsteOke_$Id"] == 0 && $oke == 1) /* Als onvolledig is gewijzigd naar volledig juist */ {$cbKies = 1; $cbDel = $_POST["chbDel_$Id"]; }
else if (isset($_POST['knpVervers_'])) { $cbKies = $_POST["chbkies_$Id"];  $cbDel = $_POST["chbDel_$Id"]; } 
   else { $cbKies = $oke; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet
 ?>

<!--    **************************************
        **           OPMAAK  GEGEVENS            **
        ************************************** -->

<tr style = "font-size:14px;">
 <td align = "center"> 

    <input type = hidden size = 1 name = <?php echo "chbkies_$Id"; ?> value = 0 > <!-- hiddden -->
    <input type = checkbox           name = <?php echo "chbkies_$Id"; ?> value = 1 
      <?php echo $cbKies == 1 ? 'checked' : ''; /* Als voorwaarde goed zijn of checkbox is aangevinkt */

      if ($oke == 0) /*Als voorwaarde niet klopt */ { ?> disabled <?php } else { ?> class="checkall" <?php } /* class="checkall" zorgt dat alles kan worden uit- of aangevinkt*/ ?> >
    <input type = hidden size = 1 name = <?php echo "laatsteOke_$Id"; ?> value = <?php echo $oke; ?> > <!-- hiddden -->
 </td>
 <td align = "center">
    <input type = hidden size = 1 name = <?php echo "chbDel_$Id"; ?> value = 0 >
    <input type = checkbox class="delete" name = <?php echo "chbDel_$Id"; ?> value = 1 <?php if(isset($cbDel)) { echo $cbDel == 1 ? 'checked' : ''; } ?> >
 </td>
 <td>
<?php if (isset($_POST['knpVervers_'])) { $scandatum = $_POST["txtScandm_$Id"]; } ?>
    <input type = "text" size = 8 style = "font-size : 11px;" name = <?php echo "txtScandm_$Id"; ?> value = <?php echo $scandatum; ?> >
 </td>
 <td align="center">
<?php if(isset($ubn_st)) { echo $ubn_st; } else { ?>
    <!-- KZLUBN -->
 <select style="width:65;" <?php echo " name=\"kzlUbn_$Id\" "; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php    $count = count($ubnId);    
for ($i = 0; $i < $count; $i++){

    $opties = array($ubnId[$i]=>$ubnnm[$i]);
            foreach($opties as $key => $waarde)
            {
  if ((!isset($_POST['knpVervers_']) && $ubnId_rd == $ubnId[$i]) || (isset($_POST["kzlUbn_$Id"]) && $_POST["kzlUbn_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
} ?>
 </select>
     <!-- EINDE KZLUBN -->
<?php } ?>
 </td>
<?php if ($levnr_stal > 0 && strlen($levnr_rd) == 12 && Validate::numeriek($levnr_rd) <> 1) { ?> 
 <td style = "text-align:center;" width= 100>
<?php echo $levnr_rd; } else { ?> <td style = "text-align:center; color : red;" > <?php echo $levnr_rd; } ?>
<!-- <input type = "hidden" name = <p??hp echo " \"txtlevgeb_$Id\" value = \"$levnr_rd\" ;"?> size = 9 style = "font-size : 9px;"> -->
 </td>
 <!--Geboorte datum -->
  <td style = "text-align:center;" width= 80>
     <?php if($levnr_stal > 0) { echo $gebdm; } else if (isset($gebdag_db)) { echo $gebdag_db; } else { ?>
         <input type = "text" size = 8 style = "font-size : 11px;" name = <?php echo "txtGebdm_$Id"; ?> value = <?php echo $txtGebdm; ?> >
     <?php } ?>
 </td>
 
 <td style = "text-align:center;" width= 80>
 <?php if($levnr_stal > 0) { echo $geslacht_stal; } else if(isset($geslacht_db)) { echo $geslacht_db; } else { ?>
<!-- KZLGESLACHT --> 
<select <?php echo " name=\"kzlSekse_$Id\" "; ?> style="width:59; font-size:13px;">

<?php
$opties = array('' => '', 'ooi' => 'ooi', 'ram' => 'ram');
foreach ( $opties as $key => $waarde)
{
   if((!isset($_POST['knpVervers_']) && $kzlSekse == $key) || (isset($_POST["kzlSekse_$Id"]) && $_POST["kzlSekse_$Id"] == $key) ) {
   echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
 } else {
   echo '<option value="' . $key . '">' . $waarde . '</option>';
   }
}

    ?> </select> <!-- EINDE KZLGESLACHT -->
<?php } ?>
 </td>
 <td style = "text-align:center; font-size:13px;" width= 80>
<?php if($levnr_stal > 0) { echo $fase; } else if(isset($fase_db)) { echo $fase_db; } else { ?>     

<!-- KZLGENERATIE --> 
<?php //echo $kzlFase; ?>
<select <?php echo " name=\"kzlFase_$Id\" "; ?> >

<?php  
$opties = array('' => '', 'lam' => 'lam', 'moeder' => 'moeder', 'vader' => 'vader');
foreach ( $opties as $key => $waarde)
{
   if((!isset($_POST['knpVervers_']) && $kzlFase == $key) || (isset($_POST["kzlFase_$Id"]) && $_POST["kzlFase_$Id"] == $key) ) {
   echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
 } else {
   echo '<option value="' . $key . '">' . $waarde . '</option>';
   }
}

    ?> </select> <!-- EINDE KZLGENERATIE -->
    <?php } ?>
 </td>

 <td align="center" > <?php 

if($levnr_stal > 0) { echo $ras_stal; } else { ?>

<!-- KZLRAS -->
 <select style="width:65;" <?php echo " name=\"kzlRas_$Id\" "; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php    $count = count($rasId);    
for ($i = 0; $i < $count; $i++){

    $opties = array($rasId[$i]=>$rasnm[$i]);
            foreach($opties as $key => $waarde)
            {
  if ((!isset($_POST['knpVervers_']) && $ras_rd == $rasId[$i]) || (isset($_POST["kzlRas_$Id"]) && $_POST["kzlRas_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
}

 ?> </select>
<?php } ?>
     <!-- EINDE KZLRAS -->
 </td>

<?php if($modtech == 1) { ?>
 <td style = "font-size : 11px;" align="center">

 <?php if(isset($verblijf)) { echo $verblijf; } else { ?>

 <!-- KZLHOKNR --> 
 <select style="width:68;" <?php echo " name=\"kzlHok_$Id\" "; ?> value = "" style = "font-size:12px;">
  <option></option>

<?php    $count = count($hoknum);
for ($i = 0; $i < $count; $i++){

    $opties = array($hoknId[$i]=>$hoknum[$i]);
            foreach($opties as $key => $waarde)
            {
  if ((!isset($_POST['knpVervers_']) && $hok_rd == $hoknId[$i]) || (isset($_POST["kzlHok_$Id"]) && $_POST["kzlHok_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
}
?>    </select>

<?php } ?>
 </td> <!-- EINDE KZLHOKNR -->
<?php }

 if(!isset($eigenDier)) { ?>
 <td style = "font-size : 11px;"> 

<!-- KZLACTIE --> 
 <select style="width:150;" <?php echo " name=\"kzlActie_$Id\" "; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php $count = count($actnm);
for ($i = 0; $i < $count; $i++){

    $opties = array($actieId[$i]=>$actnm[$i]);
            foreach($opties as $key => $waarde)
            {
  if ((isset($_POST["kzlActie_$Id"]) && $_POST["kzlActie_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
}

 ?> </select> <!-- EINDE KZLACTIE -->
</td>
<td align = "center" > 
    <?php if (isset($_POST['knpVervers_'])) { $cbRvo = $_POST["chbRvo_$Id"]; } ?>
 <input type = checkbox           name = <?php echo "chbRvo_$Id"; ?> value = 1 
      <?php echo $cbRvo == 1 ? 'checked' : ''; ?> >

 </td> 
 <td style = "color : <?php echo $color; ?> ; font-size : 13px;">
<?php if(isset($onjuist)) { echo $onjuist; } ?>
 </td>
<?php } ?>
</tr>
<!--    **************************************
    **    EINDE OPMAAK GEGEVENS    **
    ************************************** -->

<?php } 
} //einde if(isset($data)) ?>

</td>
<td id = "NietGescand" valign= "top"> </td>

<?php 
$aant = $schaap_gateway->zoek_aantal_niet_gescand($lidId);

if($aant > 0) { $alles = 'Nee'; $tekst = 'Schapen op de stallijst die niet in bovenstaande lijst voorkomen.'; }
else { $alles = 'Ja'; $tekst = 'De hele stallijst is gescand.'; }

?>

<tr height = 100 > 
 <td colspan="5" width="350" align="center" valign="bottom"> <?php echo $tekst; ?> </td>
</tr>

<?php  if($alles == 'Nee') { ?>
<tr valign = bottom style = "font-size : 12px;">
 <th>Levensnummer<hr></th>
 <th>Geboren<hr></th>
 <th>Geslacht<hr></th>
 <th>Generatie<hr></th>
 <th>Laatste<br> controle<hr></th>
</tr>
 <?php
$zoek_niet_gescande_schapen = $schaap_gateway->zoek_niet_gescande_schapen($lidId);
while ($zngs = $zoek_niet_gescande_schapen->fetch_assoc()) {
    $levnr_rest = $zngs['levensnummer']; 
    $gebdm_rest = $zngs['gebdm']; 
    $geslacht_rest = $zngs['geslacht']; 
    $aanwas_rest = $zngs['aanwasId']; if( isset($aanwas_rest) && $geslacht_rest == 'ooi') { $fase_rest = 'moeder'; } else if( isset($aanwas_rest) && $geslacht_rest == 'ram') { $fase_rest = 'vader'; } else { $fase_rest = 'lam'; }
    $last_datum = $zngs['lastdm']; 
?>

<tr style = "font-size:12px;" align="center">
<td> <?php echo $levnr_rest; ?> </td>
<td> <?php echo $gebdm_rest; ?> </td>
<td> <?php echo $geslacht_rest; ?> </td>
<td> <?php echo $fase_rest; ?> </td>
<td> <?php echo $last_datum; ?> </td>
</tr>
<?php
}
?>

<?php } // EInde if($alles == 'Nee') ?>

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
