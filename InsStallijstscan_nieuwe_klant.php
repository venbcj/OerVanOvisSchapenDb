<?php

require_once("autoload.php");

$versie = '05-08-2023'; /* kopie gemaaky van InsAanvoer */
$versie = '26-12-2023'; /* Een schaap mag alleen in een verblijf worden geplaatst als in de database een speendatum bestaat of kan worden bepaald aan de hand van de geboortedatum */
$versie = '13-12-2024'; /* Controle en foutmeldingen samengevoegd, zie onjuist */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */
$versie = '15-07-2025'; /* Veld ubn toegevoegd. Per deze versie kan een gebruiker meerdere ubn's hebben */

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Registratie</title>
</head>
<body>

<?php
$titel = 'Eenmalig inlezen stallijst nieuwe klanten';
$file = "InsStallijstscan_nieuwe_klant.php";
include "login.php"; ?>

		<TD valign = "top">
<?php
if (Auth::is_logged_in()) {

if ($modmeld == 1 ) { include "maak_request_func.php"; }

If (isset($_POST['knpInsert_']))  {
	include "post_readerStalscan.php";
	}

$zoek_laatste_scandag = mysqli_query($db,"
SELECT ingescand
FROM tblLeden
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' 
") or die (mysqli_error($db));

	while ( $zls = mysqli_fetch_assoc ($zoek_laatste_scandag)) { $lstScan = $zls['ingescand']; }

$lstScanDag = date('d-m-Y', strtotime($lstScan));

if($lstScan >= $today) { //$today is gedeclareerd in bsisfuncties.php
$inleesstatus = "Je hebt t/m " . $lstScanDag . " de mogelijkheid de stallijst in te lezen";
} else {
$inleesstatus = "Je had t/m " . $lstScanDag . " de mogelijkheid de stallijst in te lezen. <br> Verwijder de gegevens door op 'inlezen' te klikken of raadpleeg de beheerder.";
}

$velden = "rd.actId, rd.Id readId, rd.datum, rd.ubnId ubnId_rd, rd.levensnummer levnr_rd, rd.rasId rasId_rd, rd.geslacht geslacht_rd, rd.hokId hokId_rd, rd.doelId, dup.dubbelen, ho.hoknr hoknr_rd";

$tabel = "
impAgrident rd
 left join (
 	SELECT rd.Id, count(dup.Id) dubbelen
	FROM impAgrident rd
	 join impAgrident dup on (rd.lidId = dup.lidId and rd.levensnummer = dup.levensnummer and rd.Id <> dup.Id and rd.actId = dup.actId and isnull(dup.verwerkt))
	WHERE rd.actId = 21
	GROUP BY rd.Id
 ) dup on (rd.Id = dup.Id)
 left join tblHok ho on (rd.hokId = ho.hokId)
";

$WHERE = "WHERE rd.lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and rd.actId = 21 and isnull(rd.verwerkt)";

include "paginas.php";

$data = $page_nums->fetch_data($velden, "ORDER BY actId desc, rd.datum, rd.Id"); ?>

<form action="InsStallijstscan_nieuwe_klant.php" method = "post">
<table border = 0>
<tr> 
 <td colspan = 2 style = "font-size : 13px;"> 
  <input type = "submit" name = "knpVervers_" value = "Verversen"></td>
 <td colspan = 2 align = "center" style = "font-size : 14px;"><?php 
echo $page_numbers; ?></td>
 <td colspan = 3 align = left style = "font-size : 13px;"> Regels Per Pagina: <?php echo $kzlRpp; ?> </td>
 <td align = 'left'> <input type = "submit" name = "knpInsert_" value = "Inlezen">&nbsp &nbsp </td>
 <td  style = "font-size : 12px;"> 
 </td>
 <td align="right" >
 		<a href="exportStallijstScanNewUser.php?pst=<?php echo $lidId; ?>'"> Export-xlsx </a>

 </td>
</tr>
<tr style = "font-size : 12px;">
 <th valign = bottom >Inlezen<br><b style = "font-size : 10px;">Ja/Nee</b><br> <input type="checkbox" id="selectall" checked /> <hr></th>
 <th valign = bottom >Verwij-<br>deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
 <th valign = bottom >Scan<br>datum<hr></th>
 <th valign = bottom >Ubn<hr></th>
 <th valign = bottom >Levensnummer<hr></th>
 <th valign = bottom >Geboorte datum<hr></th>
 <th valign = bottom >Speen datum<hr></th>

 <th valign = bottom >Ras<hr></th>
 <th valign = bottom >Geslacht<hr></th>
 <th valign = bottom >Generatie<hr></th>
 <th valign = bottom >Gespeend<hr></th>
 <th valign = bottom >
<?php if($modtech == 1) { ?> Verblijf<hr> <?php } ?>
</th>


<td colspan = 2 align="center" style = "font-size : 18px; color : blue;"> <?php echo $inleesstatus; ?> </td>
</tr>

<?php
// Declaratie ubn
$declaratie_kzlUbn = mysqli_query($db,"
SELECT ubnId, ubn
FROM tblUbn
WHERE lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and actief = 1
ORDER BY ubn
") or die (mysqli_error($db));  

$index = 0; 
while ($du = mysqli_fetch_array($declaratie_kzlUbn)) 
{
   $ubnId[$index] = $du['ubnId']; 
   $ubnnm[$index] = $du['ubn'];
   $ubnRaak[$index] = $du['ubnId'];
   $index++; 
}
unset($index);
// Einde Declaratie ubn

// Declaratie ras
$qryRassen = "
SELECT r.rasId, r.ras, lower(coalesce(isnull(ru.scan),'6karakters')) scan
FROM tblRas r
 join tblRasuser ru on (r.rasId = ru.rasId)
WHERE ru.lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and r.actief = 1 and ru.actief = 1
ORDER BY ras
"; 
$RAS = mysqli_query($db,$qryRassen) or die (mysqli_error($db)); 

$index = 0; 
while ($ras = mysqli_fetch_array($RAS)) 
{
   $rasId[$index] = $ras['rasId']; 
   $rasnm[$index] = $ras['ras'];
   $rasRaak[$index] = $ras['rasId'];
   $index++; 
}
unset($index);

//dan het volgende:
$count = count($rasId); 
/*
echo "<select name=\"kzlRas_Id\">"; 
for ($i = 0; $i <= $count; $i++) 
{ 
    echo "<option value=\"$rasId[$i]\">$rasnm[$i]</option>"; 
} 
echo "</select>";*/
// EINDE Declaratie ras

if($modtech == 1) {

// Declaratie HOKNUMMER			// lower(if(isnull(scan),'6karakters',scan)) zorgt ervoor dat $raak nooit leeg is. Anders worden legen velden gevonden in legen velden binnen impReader.
$qryHoknummer = mysqli_query($db,"
SELECT hokId, hoknr, lower(if(isnull(scan),'6karakters',scan)) scan
FROM tblHok
WHERE lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and actief = 1
ORDER BY hoknr
") or die (mysqli_error($db));

$index = 0; 
while ($hknr = mysqli_fetch_assoc($qryHoknummer)) 
{ 
   $hoknId[$index] = $hknr['hokId']; 
   $hoknum[$index] = $hknr['hoknr'];
   $hokRaak[$index] = $hknr['scan'];   if($reader == 'Agrident') { $hokRaak[$index] = $hknr['hokId']; }
   $index++; 
} 
unset($index);
// EINDE Declaratie HOKNUMMER
}



if(isset($data))  {	

//echo count($data);

	foreach($data as $key => $array)
	{
 unset($fase_db);

		$var = $array['datum'];
$date = str_replace('/', '-', $var);
//$gebdatum = date('d-m-Y', strtotime($date)-365*60*60*24);
$datum = date('d-m-Y', strtotime($date));
	
	$Id = $array['readId'];
	$ubnId_rd = $array['ubnId_rd'];
	$levnr_rd = $array['levnr_rd']; //if (strlen($levnr_rd)== 11) {$levnr_rd = '0'.$array['levnr'];}
	$levnr_dupl = $array['dubbelen']; // twee keer in reader bestand
	$sekse_rd = $array['geslacht_rd'];
	$hokId_rd = $array['hokId_rd'];
	$hoknr_rd = $array['hoknr_rd'];
	$doelId_rd = $array['doelId']; if($doelId_rd == 1) { $fase_rd = 'lam'; } else { if($sekse_rd == 'ooi') {$fase_rd = 'moeder';} else { $fase_rd = 'vader';} }
	$rasId_rd = $array['rasId_rd'];

unset($schaapId);

$schaapId_st = zoek_schaapId_in_stallijst($lidId,$levnr_rd);

if(!isset($schaapId_st)) {

$schaapId_db = zoek_schaapId_in_database($levnr_rd);
$schaapId = $schaapId_db;
}
else
{
	$schaapId = $schaapId_st;
}

unset($gebdag_db);
unset($spndag);
unset($txtSpeendm);
unset($geslacht_db);
unset($spndag_db);
unset($aanwas_db);
unset($fase_db);
unset($ras_db);

if(isset($schaapId)) {
#echo $schaapId.'<br>';

$zoek_levnr_db = "
SELECT gebdag, spndag_geb, geslacht, spndag, his_aanw, r.ras
FROM tblSchaap s
 left join (
 	SELECT schaapId, date_format(h.datum,'%d-%m-%Y') gebdag, date_format(date_add(h.datum,interval 49 day),'%d-%m-%Y') spndag_geb
 	FROM tblHistorie h
 	 join tblStal st on (st.stalId = h.stalId)
 	WHERE actId = 1 and h.skip = 0
 	) geb on (geb.schaapId = s.schaapId)
 left join (
 	SELECT schaapId, date_format(h.datum,'%d-%m-%Y') spndag
 	FROM tblHistorie h
 	 join tblStal st on (st.stalId = h.stalId)
 	WHERE actId = 4 and h.skip = 0
 	) spn on (spn.schaapId = s.schaapId)
  left join (
 	SELECT schaapId, hisId his_aanw
 	FROM tblHistorie h
 	 join tblStal st on (st.stalId = h.stalId)
 	WHERE actId = 3 and h.skip = 0
 	) aanw on (aanw.schaapId = s.schaapId)
 left join tblRas r on (r.rasId = s.rasId)
WHERE s.schaapId = '" . mysqli_real_escape_string($db,$schaapId) . "'
";

 $zoek_levnr_db = mysqli_query($db,$zoek_levnr_db) or die (mysqli_error($db));

while ($zld = mysqli_fetch_assoc($zoek_levnr_db)) 
{ 
  $gebdag_db = $zld['gebdag'];
  $spndag_geb = $zld['spndag_geb'];  
	$geslacht_db = $zld['geslacht'];
	$spndag_db = $zld['spndag']; if(!isset($spndag_db) && isset($gebdag_db)) { $spndag = $spndag_geb; $txtSpeendm = $spndag_geb; } else { $spndag = $spndag_db; }
	$aanwas_db = $zld['his_aanw']; if( isset($aanwas_db) && $geslacht_db == 'ooi') { $fase_db = 'moeder'; } else if( isset($aanwas_db) && $geslacht_db == 'ram') { $fase_db = 'vader'; } else { $fase_db = 'lam'; }
	$ras_db = $zld['ras']; 
}
//} Einde if($levnr_stal == 0)

} // Einde if(isset($schaapId_db))

// Controleren of ingelezen waardes worden gevonden .
if (isset($_POST['knpVervers_'])) {
	$txtScandm = $_POST["txtScandm_$Id"];
	$kzlUbn = $_POST["kzlUbn_$Id"];
	$txtGebdm  = $_POST["txtGebdm_$Id"]; if(!empty($txtGebdm)) { $txtDmgeb = date_format(date_create($txtGebdm), 'Y-m-d'); }
	$txtSpeendm  = $_POST["txtSpeendm_$Id"]; 
	$kzlRas = $_POST["kzlRas_$Id"]; 
	$kzlSekse = $_POST["kzlSekse_$Id"]; 
	$fase_rd = $_POST["kzlFase_$Id"];	
	if($modtech == 1) { $kzlHok = $_POST["kzlHok_$Id"]; }

	if(!empty($txtGebdm) && empty($txtSpeendm)) {
		$txtSpeendm = date('d-m-Y', strtotime($txtDmgeb. ' + 49 days')); 

$verschil_speendatum_vandaag = date_diff(date_create($txtDmgeb), date_create($today)); // $today is gedeclareerd in basisfunctie.php

if($verschil_speendatum_vandaag->days < 49) {
echo '$verschil_speendatum_vandaag = '.$verschil_speendatum_vandaag->days.'<br>'; }

	} 
}
else {
	$txtScandm = $datum;
	$kzlUbn = $ubnId_rd;
	$kzlRas = $rasId_rd;
	$kzlSekse = $sekse_rd;
	if($modtech == 1) { $kzlHok = $hokId_rd; }
}

$date2 = eerste_datum_na_geboortedatum($schaapId);
$datum2 = date_format(date_create($date2), 'd-m-Y');

unset($onjuist);

if ($lstScan < $today) { $onjuist = ""; } # De datum ingescand in tblLeden mag niet zijn gepasseerd.

if (empty($txtScandm))							{ $color = 'red'; $onjuist = "Datum is onbekend."; }
else if (empty($kzlUbn) )						{ $color = 'red'; $onjuist = "Ubn is onbekend."; }
else if (isset($levnr_dupl) ) 					{ $color = 'blue'; $onjuist = "Dubbel in de reader."; }
else if (isset($schaapId_st) ) 					{ $color = 'red'; $onjuist = "Dit levensnummer staat al op de stallijst."; }
else if (isset($levnr_rd) && strlen($levnr_rd) <> 12) { $color = 'red'; $onjuist = "Levensnummer geen 12 karakters."; }  
else if (Validate::numeriek($levnr_rd) == 1) 			{ $color = 'red'; $onjuist = "Levensnummer bevat een letter."; }
else if (!isset($schaapId_db) && empty($kzlSekse)) 				{ $color = 'red'; $onjuist = "Het geslacht is verplicht."; }
else if (!isset($schaapId_db) && $fase_rd == 'moeder' && $kzlSekse =='ram') 	{ $color = 'red'; $onjuist = "generatie en geslacht is tegenstrijdig."; }
else if (!isset($schaapId_db) && $fase_rd == 'vader' && $kzlSekse =='ooi') 	{ $color = 'red'; $onjuist = "generatie en geslacht is tegenstrijdig."; }
else if ($fase_rd == 'lam' && empty($txtGebdm)) { $color = 'red'; $onjuist = "Geboortedatum is onbekend."; }
else if ($fase_rd == 'lam' && empty($kzlHok) && $modtech == 1) { $color = 'red'; $onjuist = "Verblijf is onbekend."; }
else if (!isset($schaapId_db) && empty($fase_rd)) { $color = 'red'; $onjuist = "Generatie is onbekend."; }




if(isset($onjuist)) {	$oke = 0; } else { $oke = 1; } // $oke kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.
// EINDE Controleren of ingelezen waardes worden gevonden .  

/* Als onvolledig is gewijzigd naar volledig juist */
	 if (isset($_POST['knpVervers_']) && $_POST["laatsteOke_$Id"] == 0 && $oke == 1)  {$cbKies = 1; $cbDel = $_POST["chbDel_$Id"]; }
else if (isset($_POST['knpVervers_'])) { $cbKies = $_POST["chbkies_$Id"];  $cbDel = $_POST["chbDel_$Id"]; } 
   else { $cbKies = $oke; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet 


if($lstScan < $today) { $cbDel = 1; } ?>

<!--	**************************************
		**	  	 OPMAAK  GEGEVENS			**
		************************************** -->

<tr style = "font-size:14px;">
 <td align = "center"> 

	<input type = hidden size = 1 name = <?php echo "chbkies_$Id"; ?> value = 0 > <!-- hiddden -->
	<input type = checkbox 		  name = <?php echo "chbkies_$Id"; ?> value = 1 
	  <?php echo $cbKies == 1 ? 'checked' : ''; /* Als voorwaarde goed zijn of checkbox is aangevinkt */

	  if ($oke == 0) /*Als voorwaarde niet klopt */ { ?> disabled <?php } else { ?> class="checkall" <?php } /* class="checkall" zorgt dat alles kan worden uit- of aangevinkt*/ ?> >
	<input type = hidden size = 1 name = <?php echo "laatsteOke_$Id"; ?> value = <?php echo $oke; ?> > <!-- hiddden -->
 </td>
 <td align = "center">
	<input type = hidden size = 1 name = <?php echo "chbDel_$Id"; ?> value = 0 >
	<input type = checkbox class="delete" name = <?php echo "chbDel_$Id"; ?> value = 1 <?php if(isset($cbDel)) { echo $cbDel == 1 ? 'checked' : ''; } ?> >
 </td>
 <td>
	<input type = "text" size = 8 style = "font-size : 11px;" name = <?php echo "txtScandm_$Id"; ?> value = <?php echo $txtScandm; ?> >
 </td>
 <td style = "font-size : 11px;">	
<!-- KZLUBN -->
 <select style="width:65;" <?php echo " name=\"kzlUbn_$Id\" "; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php	$count = count($ubnId);	
for ($i = 0; $i < $count; $i++){

	$opties = array($ubnId[$i]=>$ubnnm[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((!isset($_POST['knpVervers_']) && $ubnId_rd == $ubnRaak[$i]) || (isset($_POST["kzlUbn_$Id"]) && $_POST["kzlUbn_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
} ?>
 </select>
	 <!-- EINDE KZLUBN -->
 </td>
<?php if (strlen($levnr_rd) == 12 && Validate::numeriek($levnr_rd) <> 1) { ?> 
 <td>
<?php echo $levnr_rd; } else { ?> <td style = "color : red;" > <?php echo $levnr_rd; } ?>
<!-- <input type = "hidden" name = <p??hp echo " \"txtlevgeb_$Id\" value = \"$levnr_rd\" ;"?> size = 9 style = "font-size : 9px;"> -->
 </td>
<!-- Geboortedatum -->
  <td align="center">
<?php if(!isset($schaapId_st) && !isset($gebdag_db)) { ?>
	<input type = "text" size = 8 style = "font-size : 11px;" name = <?php echo "txtGebdm_$Id"; ?> value = <?php echo $txtGebdm; ?> >
<?php } 
else if(isset($gebdag_db)) { echo $gebdag_db; }?>
 </td>

 <!-- Speendatum -->
  <td align="center">
<?php if(!isset($schaapId_st) && !isset($spndag_db)) { /*Er mag geen speendatum vasliggen in de database */ ?>
	<input type = "text" size = 8 style = "font-size : 11px;" name = <?php echo "txtSpeendm_$Id"; ?> value = <?php echo $txtSpeendm; ?> >
<?php } 
else if(isset($spndag)) { echo $spndag; } ?>
 </td>
 
<?php 

if(isset($schaapId)) { ?> 
 <td align="center"> <?php echo $ras_db;
  } else { ?> 
 <td style = "font-size : 11px;">	
<!-- KZLRAS -->
 <select style="width:65;" <?php echo " name=\"kzlRas_$Id\" "; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php	$count = count($rasId);	
for ($i = 0; $i < $count; $i++){

	$opties = array($rasId[$i]=>$rasnm[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((!isset($_POST['knpVervers_']) && $rasId_rd == $rasRaak[$i]) || (isset($_POST["kzlRas_$Id"]) && $_POST["kzlRas_$Id"] == $key)){
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
<?php 

if(isset($schaapId)) { ?> 
 <td align="center"> <?php echo $sekse_db;
  } else { ?> 
 <td style = "font-size : 11px;">
<!-- KZLGESLACHT --> 
<select <?php echo " name=\"kzlSekse_$Id\" "; ?> style="width:59; font-size:13px;">

<?php  echo "$row[geslacht]";
$opties = array('' => '', 'ooi' => 'ooi', 'ram' => 'ram');
foreach ( $opties as $key => $waarde)
{
   if((!isset($_POST['knpVervers_']) && $sekse_rd == $key) || (isset($_POST["kzlSekse_$Id"]) && $_POST["kzlSekse_$Id"] == $key) ) {
   echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
 } else {
   echo '<option value"' . $key . '">' . $waarde . '</option>';
   }
}

	?> </select> <!-- EINDE KZLGESLACHT -->
<?php } ?>
 </td>
 <?php 

if(isset($schaapId)) { ?> 
 <td align="center"> <?php echo $fase_db;
  } else { ?> 
 <td style="width:59; font-size:13px;" >
<!-- KZLGENERATIE --> 
<?php //echo "$fase_rd"; ?>
<select <?php echo " name=\"kzlFase_$Id\" "; ?> >

<?php  
$opties = array('' => '', 'lam' => 'lam', 'moeder' => 'moeder', 'vader' => 'vader');
foreach ( $opties as $key => $waarde)
{
   if((!isset($_POST['knpVervers_']) && $fase_rd == $key) || (isset($_POST["kzlFase_$Id"]) && $_POST["kzlFase_$Id"] == $key) ) {
   echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
 } else {
   echo '<option value"' . $key . '">' . $waarde . '</option>';
   }
}

	?> </select> <!-- EINDE KZLGENERATIE -->
<?php } ?>
 </td>
 <?php 
 unset($cbSpeen);
	 if (($fase_rd == 'moeder' || $fase_rd == 'vader' || isset($spndag)) )  { $cbSpeen = 1; }
else if (isset($_POST['knpVervers_'])) { $cbKies = $_POST["chbkies_$Id"];  $cbDel = $_POST["chbDel_$Id"]; } 
   //else { $cbKies = $oke; }
 ?>
 <td align="center">
 	<input type = checkbox class="speen" name = <?php echo "chbSpeen_$Id"; ?> value = 1 <?php if(isset($cbSpeen)) { echo $cbSpeen == 1 ? 'checked' : ''; } 
 	if ($fase_rd == 'moeder' || $fase_rd == 'vader' || isset($spndag)) /*Als voorwaarde niet klopt */ { ?> disabled <?php } ?> >
 </td>
 <td>

<?php 
/*Alleen als de speendatum bekend is kan een volwassen schaap in een verblijf worden gezet. Bij een lam moet een keuze worden gemaakt of het dier reeds is gespeend. Zo ja dan zal ook hier eerst de speendtum bekend moeten zijn voor het schaap in een verblijf kan worden gezet. 
Is de geboortedatum ingevuld en de speendatum niet dan wordt de speendatum automatisch berekend (geboortedatum + 49 dagen) mits deze niet in de toekomst ligt. */

if( $modtech == 1 && !isset($schaapId_st) && (!empty($txtGebdm) || isset($spndag) /*Als in de database een speendatum bestaat / kan worden bepaald o.b.v. geboortedatum*/ ) )  { ?>
<!-- KZLHOKNR --> 
 <select style="width:65;" <?php echo " name=\"kzlHok_$Id\" "; ?> value = "" style = "font-size:12px;">
  <option></option>

<?php	$count = count($hoknum);
for ($i = 0; $i < $count; $i++){

	$opties = array($hoknId[$i]=>$hoknum[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((!isset($_POST['knpVervers_']) && $hokId_rd == $hokRaak[$i]) || (isset($_POST["kzlHok_$Id"]) && $_POST["kzlHok_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}
?>	</select>
<!-- EINDE KZLHOKNR --> 
<?php } else { echo $hoknr_rd; } ?>
 </td>


 <td style = "color : <?php echo $color; ?> ; font-size : 11px;"> <?php if(isset($onjuist)) { echo $onjuist; } ?>
<!-- EINDE Als levensnummer uniek is EN 12 karakters lang is EN geen letter bevat --> 
 </td> 
 <td></td>
</tr>
<!--	**************************************
	**	EINDE OPMAAK GEGEVENS	**
	************************************** -->

<?php } 
} //einde if(isset($data)) ?>
</table>
</form> 



</TD>
<?php
include "menu1.php"; } ?>
</tr>

</table>

</body>
</html>
<script language="javascript">
$(function(){

	// add multiple select / deselect functionality
	$("#selectall").click(function () {
		  $('.checkall').attr('checked', this.checked);
	});

	// if all checkbox are selected, check the selectall checkbox
	// and viceversa
	$(".checkall").click(function(){

		if($(".checkall").length == $(".checkall:checked").length) {
			$("#selectall").attr("checked", "checked");
		} else {
			$("#selectall").removeAttr("checked");
		}

	});
});

$(function(){

	// add multiple select / deselect functionality
	$("#selectall_del").click(function () {
		  $('.delete').attr('checked', this.checked);
	});

	// if all checkbox are selected, check the selectall_del checkbox
	// and viceversa
	$(".delete").click(function(){

		if($(".delete").length == $(".delete:checked").length) {
			$("#selectall_del").attr("checked", "checked");
		} else {
			$("#selectall_del").removeAttr("checked");
		}

	});
});
</script>
