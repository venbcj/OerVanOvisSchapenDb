<!-- 11-8-2014 : veld type gewijzigd in fase 
 23-11-2014 include "Maak_Request.php"; toegevoegd 
 9-11-2016 controle op bestaand levensnummer toegevoegd ( isset($schaapId) ) 
 18-1-2017 : Query's aangepast n.a.v. nieuwe tblDoel	22-1-2017 : tblBezetting gewijzigd naar tblBezet 
 1-2-2017 : Halsnummer toegevoegd
11-2-2017 : Mogelijkheid moeders en vaders aan hokken toevoegen 
28-2-2017 :  Ras en gewicht niet veplicht gemaakt 
9-7-2020 : Onderscheid gemaakt tussen reader Agrident en Biocontrol -->

<?php
/* post_readerGeb.php toegepast in :
	- InsUitval.php */
	
/*include "url.php";

include "passw.php";*/
//Include "connect_db.php"; //Deze include zit ook in login.php maar binnen InsAanvoer.php is include"login.php"; nog niet gepasseerd. Hier laten staan dus.

function getNameFromKey($key) {
    $array = explode('_', $key);
    return $array[0];
}

function getIdFromKey($key) {
    $array = explode('_', $key);
    return $array[1];
}

$array = array();

foreach($_POST as $key => $value) {
    
    $array[getIdFromKey($key)][getNameFromKey($key)] = $value;
}
foreach($array as $recId => $id) {

// Id ophalen
//echo $recId.'<br>'; 
// Einde Id ophalen
   
 foreach($id as $key => $value) {
 if ($key == 'chbkies' && $value == 1 ) 	{ /* Alleen als checkbox chbkies de waarde 1 heeft  /*echo $key.'='.$value.' ';*/  $box = $value ;
 foreach($id as $key => $value) {
 if ($key == 'chbDel' && $value == 0 ) 	{ /* Alleen als checkbox Del de waarde 0 heeft  /*echo $key.'='.$value.' ';*/ ;
	
  foreach($id as $key => $value) {

if ($key == 'txtaanwdm' && !empty($value)) { $dag = date_create($value); $valuedag =  date_format($dag, 'Y-m-d'); 
								$flddag = $valuedag; }

if ($key == 'kzlKleur' && !empty($value)) {  $fldKleur = $value; }

if ($key == 'txtHnr' && !empty($value)) {  $fldHnr = $value; }
 else if ($key == 'txtHnr' && empty($value)) {  $fldHnr = 'NULL' ; }
 
if ($key == 'kzlras' && !empty($value)) {  $fldras = $value; }
 else if ($key == 'kzlras' && empty($value)) {  $fldras = 'NULL' ; }

if ($key == 'kzlsekse' && !empty($value)) {  $fldsekse = $value; }
 else if ($key == 'kzlsekse' && empty($value)) {  $fldsekse = 'NULL' ; }

if ($key == 'kzlFase' && !empty($value)) {  $fldfase = $value; }
 else if ($key == 'kzlFase' && empty($value)) {  $fldfase = 'NULL' ; }

	 	if($fldfase == 'moeder' && $fldsekse == 'NULL') { $fldsekse = 'ooi'; }
 	else if($fldfase == 'vader' && $fldsekse == 'NULL') { $fldsekse = 'ram'; }
 
 if ($key == 'txtkg' && !empty($value)) {  $fldkg = str_replace(',', '.', $value); }
 else if ($key == 'txtkg' && empty($value)) {  $fldkg = 'NULL'; }	

if ($key == 'kzlooi' && !empty($value)) {  $fldmoeder = $value; }
 else if ($key == 'kzlooi' && empty($value)) {  $fldmoeder = 'NULL'; }

if ($key == 'kzlhok' && !empty($value)) {  $fldhok = $value; }

if ($key == 'kzlherk' && !empty($value)) {  $fldherk = $value; }
 else if ($key == 'kzlherk' && empty($value)) {  $fldherk = 'NULL' ; }
	 
									}
// Levensnummer ophalen
if($reader == 'Agrident') {
$zoek_levnr = mysqli_query($db,"
SELECT levensnummer levnr_aanv FROM impAgrident WHERE Id = ".mysqli_real_escape_string($db,$recId)."
") or die (mysqli_error($db));
}
else {
$zoek_levnr = mysqli_query($db,"
SELECT levnr_aanv FROM impReader WHERE readId = ".mysqli_real_escape_string($db,$recId)."
") or die (mysqli_error($db));
}
	while ( $lv = mysqli_fetch_assoc($zoek_levnr)) { $dbLevnr = $lv['levnr_aanv']; }
// CONTROLE op alle verplichten velden bij AANVOER MOEDER- EN VADERDIEREN
if (isset($flddag) && isset($dbLevnr) && (($fldfase == 'moeder' && $fldsekse == 'ooi') || ($fldfase == 'vader' && $fldsekse == 'ram') ) )
{

$zoek_schaapId = mysqli_query($db,"
SELECT schaapId FROM tblSchaap WHERE levensnummer = '".mysqli_real_escape_string($db,$dbLevnr)."'
") or die (mysqli_error($db));
	while ( $sId = mysqli_fetch_assoc($zoek_schaapId)) { $schaapId = $sId['schaapId']; }
if(!isset($schaapId)) {
// Insert tblSchapen
	$insert_tblSchaap = "INSERT INTO tblSchaap set levensnummer = '".$dbLevnr."', rasId = ".$fldras.", geslacht = '".$fldsekse."' ";	
/*echo $insert_tblSchaap.'<br>';*/		mysqli_query($db,$insert_tblSchaap) or die (mysqli_error($db));	
// Einde Insert tblSchapen

$zoek_schaapId = mysqli_query($db,"
SELECT schaapId FROM tblSchaap WHERE levensnummer = '".mysqli_real_escape_string($db,$dbLevnr)."' ") or die (mysqli_error($db));
	while ( $sId = mysqli_fetch_assoc ($zoek_schaapId)) { $schaapId = $sId['schaapId']; }
}
// Insert tblStal
If(isset($fldKleur))	{
	$insert_tblStal= "INSERT INTO tblStal set lidId = ".mysqli_real_escape_string($db,$lidId).", schaapId = ".mysqli_real_escape_string($db,$schaapId).", kleur = '".mysqli_real_escape_string($db,$fldKleur)."', halsnr = ".mysqli_real_escape_string($db,$fldHnr).", rel_herk = ".mysqli_real_escape_string($db,$fldherk)." ";
}
else {
	$insert_tblStal= "INSERT INTO tblStal set lidId = ".mysqli_real_escape_string($db,$lidId).", schaapId = ".mysqli_real_escape_string($db,$schaapId).", halsnr = ".mysqli_real_escape_string($db,$fldHnr).", rel_herk = ".mysqli_real_escape_string($db,$fldherk)." ";
}
/*echo $insert_tblStal.'<br>';*/		mysqli_query($db,$insert_tblStal) or die (mysqli_error($db));
// Einde Insert tblStal
// Insert tblHistorie
	$zoek_stalId = mysqli_query($db,"SELECT stalId FROM tblStal WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." and schaapId = ".mysqli_real_escape_string($db,$schaapId)." and isnull(rel_best) ") or die (mysqli_error($db));
		while ( $stId = mysqli_fetch_assoc ($zoek_stalId)) { $stalId = $stId['stalId']; }
	
	$insert_tblHistorie_aank = "INSERT INTO tblHistorie set stalId = ".$stalId.", datum = '".$flddag."', actId = 2 ";
/*echo $insert_tblHistorie_aank.'<br>';*/		mysqli_query($db,$insert_tblHistorie_aank) or die (mysqli_error($db));

if(isset($fldhok)) { 
$zoek_hisId = mysqli_query($db,"SELECT hisId FROM tblHistorie WHERE stalId = ".mysqli_real_escape_string($db,$stalId)." and actId = 2 ") or die (mysqli_error($db));
	while ( $aanvId = mysqli_fetch_assoc ($zoek_hisId)) { $hisId_aanv = $aanvId['hisId']; }

	$insert_tblBezet = "INSERT INTO tblBezet set hisId = ".mysqli_real_escape_string($db,$hisId_aanv).", hokId = ".mysqli_real_escape_string($db,$fldhok)." ";
/*echo $insert_tblBezet.'<br>';*/		mysqli_query($db,$insert_tblBezet) or die (mysqli_error($db));
	}

$zoek_aanwas_hisId = mysqli_query($db,"
SELECT hisId
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE st.schaapId = ".mysqli_real_escape_string($db,$schaapId)." and actId = 3
") or die (mysqli_error($db));
	while ( $haId = mysqli_fetch_assoc ($zoek_aanwas_hisId)) { $aanwId = $haId['hisId']; }

if(!isset($aanwId)) {	
	$insert_tblHistorie_aanw = "INSERT INTO tblHistorie set stalId = ".$stalId.", datum = '".$flddag."', actId = 3 ";
/*echo $insert_tblHistorie_aanw.'<br>';*/		mysqli_query($db,$insert_tblHistorie_aanw) or die (mysqli_error($db));
}
// Einde Insert tblHistorie

if($reader == 'Agrident') {
	$updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = ".mysqli_real_escape_string($db,$recId)." ";
}
else {
	$updateReader = "UPDATE impReader set verwerkt = 1 WHERE readId = ".mysqli_real_escape_string($db,$recId)." ";
}
/*echo $updateReader.'<br>';*/		mysqli_query($db,$updateReader) or die (mysqli_error($db));

if ($modmeld == 1 ) {	// Insert tblMeldingen
$zoek_hisId = mysqli_query($db,"SELECT hisId FROM tblHistorie WHERE stalId = ".$stalId." and actId = 2 ") or die (mysqli_error($db));
		while ( $hId = mysqli_fetch_assoc ($zoek_hisId)) { $hisId = $hId['hisId']; }
$Melding = 'AAN';
include "maak_request.php";
	// Einde Insert tblMeldingen	
}		
unset($schaapId); }
// EINDE CONTROLE op alle verplichten velden bij aanvoer moederdieren

// CONTROLE op alle verplichten velden bij AANVOER LAMMEREN
if ( ($modtech == 1 && isset($flddag) && isset($dbLevnr) && isset($fldras) && isset($fldsekse) && $fldfase == 'lam' && isset($fldkg) && isset($fldhok) )
 ||
	($modtech == 0 && isset($flddag) && isset($dbLevnr) && isset($fldras) && isset($fldsekse) && $fldfase == 'lam') )
{
$zoek_schaapId = mysqli_query($db,"
SELECT schaapId FROM tblSchaap WHERE levensnummer = '".mysqli_real_escape_string($db,$dbLevnr)."'
") or die (mysqli_error($db));
	while ( $sId = mysqli_fetch_assoc($zoek_schaapId)) { $schaapId = $sId['schaapId']; echo $schaapId.'<br>'; }
	
if(!isset($schaapId)) {
	if($modtech == 0) { $volwId = 'NULL'; $fldkg = 'NULL'; }
	else {
	$insert_tblVolwas = "INSERT INTO tblVolwas set readId = ".mysqli_real_escape_string($db,$recId).", mdrId = ".mysqli_real_escape_string($db,$fldmoeder)." ";/*$recId als Relatie met impReader. Handig om te weten */
/*echo $insert_tblVolwas.'<br>';*/		mysqli_query($db,$insert_tblVolwas) or die (mysqli_error($db));	

$zoek_volwId = mysqli_query($db,"
SELECT volwId FROM tblVolwas WHERE readId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));
	while ( $vId = mysqli_fetch_assoc($zoek_volwId)) { $volwId = $vId['volwId']; }
	}
	
	$insert_tblSchaap = "INSERT INTO tblSchaap set levensnummer = '".mysqli_real_escape_string($db,$dbLevnr)."', rasId = ".mysqli_real_escape_string($db,$fldras).", geslacht = '".mysqli_real_escape_string($db,$fldsekse)."', volwId = ".mysqli_real_escape_string($db,$volwId)." ";	
/*echo $insert_tblSchaap.'<br>';*/		mysqli_query($db,$insert_tblSchaap) or die (mysqli_error($db));	
	 
	$zoek_schaapId = mysqli_query($db,"SELECT schaapId FROM tblSchaap WHERE levensnummer = '".mysqli_real_escape_string($db,$dbLevnr)."' ") or die (mysqli_error($db));
		while ( $sId = mysqli_fetch_assoc ($zoek_schaapId)) { $schaapId = $sId['schaapId']; }
}
	
	$insert_tblStal = "INSERT INTO tblStal set lidId = ".mysqli_real_escape_string($db,$lidId).", schaapId = ".mysqli_real_escape_string($db,$schaapId).", rel_herk = ".mysqli_real_escape_string($db,$fldherk)." ";
/*echo 'lam '.$insert_tblStal.'<br>';*/		mysqli_query($db,$insert_tblStal) or die (mysqli_error($db));

	$zoek_stalId = mysqli_query($db,"SELECT stalId FROM tblStal WHERE schaapId = ".mysqli_real_escape_string($db,$schaapId)." and isnull(rel_best) ") or die (mysqli_error($db));
		while ( $stId = mysqli_fetch_assoc ($zoek_stalId)) { $stalId = $stId['stalId']; }
	
	$insert_tblHistorie_aank = "INSERT INTO tblHistorie set stalId = ".mysqli_real_escape_string($db,$stalId).", datum = '".mysqli_real_escape_string($db,$flddag)."', kg = '".mysqli_real_escape_string($db,$fldkg)."', actId = 2 ";
/*echo $insert_tblHistorie_aank.'<br>';*/		mysqli_query($db,$insert_tblHistorie_aank) or die (mysqli_error($db));

// $zoek_hisId is voor tblBezet én tblMelding
	$zoek_hisId = mysqli_query($db,"SELECT hisId FROM tblHistorie WHERE stalId = ".mysqli_real_escape_string($db,$stalId)." and actId = 2 ") or die (mysqli_error($db));
		while ( $hId = mysqli_fetch_assoc ($zoek_hisId)) { $hisId = $hId['hisId']; }
		
if($modtech == 1) {
// Insert tblBezet
/*$zoek_periId = mysqli_query($db,"SELECT periId FROM tblPeriode WHERE hokId = ".mysqli_real_escape_string($db,$fldhok)." and doelId = 1 and isnull(dmafsluit) ") or die (mysqli_error($db));
	while ( $pId = mysqli_fetch_assoc ($zoek_periId)) { $periId = $pId['periId']; }
			
if(!isset($periId))	{
	$insert_tblPeriode = "INSERT INTO tblPeriode set hokId = ".$fldhok.", doelId = 1 ";
/*echo $insert_tblPeriode.'<br>';*/			/*mysqli_query($db,$insert_tblPeriode) or die (mysqli_error($db));

$zoek_periId = mysqli_query($db,"SELECT periId FROM tblPeriode WHERE hokId = ".$fldhok." and doelId = 1 and isnull(dmafsluit) ") or die (mysqli_error($db));
	while ( $pId = mysqli_fetch_assoc ($zoek_periId)) { $periId = $pId['periId']; }
		}*/
	
	$insert_tblBezet = "INSERT INTO tblBezet set hokId = ".mysqli_real_escape_string($db,$fldhok).", hisId = ".mysqli_real_escape_string($db,$hisId)." " ;
/*echo $insert_tblBezet.'<br>';*/		mysqli_query($db,$insert_tblBezet) or die (mysqli_error($db));	
// Einde Insert tblBezet			
	}

if($reader == 'Agrident') {
	$updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = ".mysqli_real_escape_string($db,$recId)." ";
}
else {
	$updateReader = "UPDATE impReader set verwerkt = 1 WHERE readId = ".mysqli_real_escape_string($db,$recId)." ";
}
/*echo $updateReader.'<br>';*/		mysqli_query($db,$updateReader) or die (mysqli_error($db));

if ($modmeld == 1 ) {		// Insert tblMeldingen
$Melding = 'AAN';
include "maak_request.php";
	// Einde Insert tblMeldingen
}	
unset($schaapId);	
unset($periId);	}
// EINDE CONTROLE op alle verplichten velden bij aankoop lammeren

										} // EINDE Alleen als checkbox Del de waarde 0 heeft 
	}
										} // EINDE Alleen als checkbox chbkies de waarde 1 heeft
    }
	
	
						
 foreach($id as $key => $value) {
 if ($key == 'chbkies' && $value == 0 ) 	{ /* Alleen als checkbox chbkies de waarde 0 heeft  /*echo $key.'='.$value.' ';*/  $box = $value ;
 foreach($id as $key => $value) {
 if ($key == 'chbDel' && $value == 1 ) 	{ /* Alleen als checkbox Del de waarde 1 heeft  /*echo $key.'='.$value.' ';*/ ;
	
  foreach($id as $key => $value) {	
	
if($reader == 'Agrident') {
    $updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = ".mysqli_real_escape_string($db,$recId)." " ;
}
else {
	$updateReader = "UPDATE impReader set verwerkt = 1 WHERE readId = ".mysqli_real_escape_string($db,$recId)." " ;
}
/*echo $updateReader.'<br>';*/		mysqli_query($db,$updateReader) or die (mysqli_error($db));
	}

										} // EINDE Alleen als checkbox Del de waarde 1 heeft 
	}
										} // EINDE Alleen als checkbox chbkies de waarde 0 heeft
    }



unset($dbLevnr);
	}
?>
					
	