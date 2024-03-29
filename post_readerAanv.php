<!-- 11-8-2014 : veld type gewijzigd in fase 
 23-11-2014 include "Maak_Request.php"; toegevoegd 
 9-11-2016 controle op bestaand levensnummer toegevoegd ( isset($schaapId) ) 
 18-1-2017 : Query's aangepast n.a.v. nieuwe tblDoel	22-1-2017 : tblBezetting gewijzigd naar tblBezet 
 1-2-2017 : Halsnummer toegevoegd
11-2-2017 : Mogelijkheid moeders en vaders aan hokken toevoegen 
28-2-2017 :  Ras en gewicht niet veplicht gemaakt 
9-7-2020 : Onderscheid gemaakt tussen reader Agrident en Biocontrol 
5-5-2021 : isset($verwerkt) toegevoegd om dubbele invoer te voorkomen. SQL beveiligd met quotes. Verschil tussen kiezen of verwijderen herschreven 
26-11-2022 Invoer geboortedatum toegevoegd -->

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
	
 unset($fldGebdag);

  foreach($id as $key => $value) {

if ($key == 'chbkies')   { $fldKies = $value; }
if ($key == 'chbDel')   { $fldDel = $value; }

if ($key == 'txtaanwdm' && !empty($value)) { $dag = date_create($value); $valuedag =  date_format($dag, 'Y-m-d'); 
								$flddag = $valuedag; }

if ($key == 'kzlKleur' && !empty($value)) {  $fldKleur = $value; }
 else if ($key == 'txtKleur' && empty($value)) {  $fldKleur = '' ; }

if ($key == 'txtHnr' && !empty($value)) {  $fldHnr = $value; }
 else if ($key == 'txtHnr' && empty($value)) {  $fldHnr = '' ; }
 
if ($key == 'kzlras' && !empty($value)) {  $fldRas = $value; }
 else if ($key == 'kzlras' && empty($value)) {  $fldRas = '' ; }

if ($key == 'kzlsekse' && !empty($value)) {  $fldSekse = $value; }
 else if ($key == 'kzlsekse' && empty($value)) {  $fldSekse = 'NULL' ; }

if ($key == 'kzlFase' && !empty($value)) {  $fldFase = $value; }
 else if ($key == 'kzlFase' && empty($value)) {  $fldFase = 'NULL' ; }

	 	if($fldFase == 'moeder' && $fldSekse == 'NULL') { $fldSekse = 'ooi'; }
 	else if($fldFase == 'vader' && $fldSekse == 'NULL') { $fldSekse = 'ram'; }
 
 if ($key == 'txtkg' && !empty($value)) {  $fldKg = str_replace(',', '.', $value); }
 else if ($key == 'txtkg' && empty($value)) {  $fldKg = ''; }	

 if ($key == 'txtGebdm' && !empty($value)) { $gebDag = date_create($value); $valueGebdag =  date_format($gebDag, 'Y-m-d'); 
								$fldGebdag = $valueGebdag; }

if ($key == 'kzlooi' && !empty($value)) {  $fldMoeder = $value; }
 else if ($key == 'kzlooi' && empty($value)) {  $fldMoeder = ''; }

if ($key == 'kzlhok' && !empty($value)) {  $fldHok = $value; }

if ($key == 'kzlherk' && !empty($value)) {  $fldHerk = $value; }
 else if ($key == 'kzlherk' && empty($value)) {  $fldHerk = '' ; }
	 
									}

// (extra) controle of readerregel reeds is verwerkt. Voor als de pagina 2x wordt verstuurd bij fouten op de pagina
unset($verwerkt);
if($reader == 'Agrident') {
$zoek_readerRegel_verwerkt = mysqli_query($db,"
SELECT verwerkt
FROM impAgrident
WHERE Id = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db)); 
}
else {
$zoek_readerRegel_verwerkt = mysqli_query($db,"
SELECT verwerkt
FROM impReader
WHERE readId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));
}
while($verw = mysqli_fetch_array($zoek_readerRegel_verwerkt))
{ $verwerkt = $verw['verwerkt']; }
// Einde (extra) controle of readerregel reeds is verwerkt.

if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt)) { // isset($verwerkt) is een extra controle om dubbele invoer te voorkomen

// Levensnummer ophalen
if($reader == 'Agrident') {
$zoek_levnr = mysqli_query($db,"
SELECT levensnummer levnr_aanv, transponder FROM impAgrident WHERE Id = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));
}
else {
$zoek_levnr = mysqli_query($db,"
SELECT levnr_aanv, NULL transponder FROM impReader WHERE readId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));
}
	while ( $lv = mysqli_fetch_assoc($zoek_levnr)) { $dbLevnr = $lv['levnr_aanv']; $transp_rd = $lv['transponder']; }



// CONTROLE op alle verplichten velden bij AANVOER MOEDER- EN VADERDIEREN
if (isset($flddag) && isset($dbLevnr) && (($fldFase == 'moeder' && $fldSekse == 'ooi') || ($fldFase == 'vader' && $fldSekse == 'ram') ) )
{

$zoek_schaapId = mysqli_query($db,"
SELECT schaapId, transponder FROM tblSchaap WHERE levensnummer = '".mysqli_real_escape_string($db,$dbLevnr)."'
") or die (mysqli_error($db));
	while ( $sId = mysqli_fetch_assoc($zoek_schaapId)) { $schaapId = $sId['schaapId']; $transp_db = $sId['transponder'];}
if(!isset($schaapId)) {
// Insert tblSchapen
	$insert_tblSchaap = "INSERT INTO tblSchaap set levensnummer = '".$dbLevnr."', rasId = " . db_null_input($fldRas) . ", geslacht = '".$fldSekse."' ";	
/*echo $insert_tblSchaap.'<br>';*/		mysqli_query($db,$insert_tblSchaap) or die (mysqli_error($db));	
// Einde Insert tblSchapen

$zoek_schaapId = mysqli_query($db,"
SELECT schaapId FROM tblSchaap WHERE levensnummer = '".mysqli_real_escape_string($db,$dbLevnr)."' ") or die (mysqli_error($db));
	while ( $sId = mysqli_fetch_assoc ($zoek_schaapId)) { $schaapId = $sId['schaapId']; }
}
// Transpondernummer inlezen
if(!isset($transp_db) && isset($transp_rd)) {
	$update_tblSchaap = "UPDATE tblSchaap set transponder = '".mysqli_real_escape_string($db,$transp_rd)."' ";	
/*echo $update_tblSchaap.'<br>';*/		mysqli_query($db,$update_tblSchaap) or die (mysqli_error($db));	
}
// Einde Transpondernummer inlezen

// Insert tblStal
	$insert_tblStal= "INSERT INTO tblStal set lidId = '".mysqli_real_escape_string($db,$lidId)."', schaapId = '".mysqli_real_escape_string($db,$schaapId)."', kleur = " . db_null_input($fldKleur) . ", halsnr = " . db_null_input($fldHnr) . ", rel_herk = " . db_null_input($fldHerk) . " ";

/*echo $insert_tblStal.'<br>';*/		mysqli_query($db,$insert_tblStal) or die (mysqli_error($db));
// Einde Insert tblStal

// Insert tblHistorie
	$zoek_stalId = mysqli_query($db,"SELECT stalId FROM tblStal WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and isnull(rel_best) ") or die (mysqli_error($db));
		while ( $stId = mysqli_fetch_assoc ($zoek_stalId)) { $stalId = $stId['stalId']; }

  // Insert geboorte datum
if(isset($fldGebdag)) {
	$insert_tblHistorie_geboren = "INSERT INTO tblHistorie set stalId = ".$stalId.", datum = '".$fldGebdag."', actId = 1 ";
/*echo $insert_tblHistorie_geboren.'<br>';*/		mysqli_query($db,$insert_tblHistorie_geboren) or die (mysqli_error($db));
}
  // Einde Insert geboorte datum
  // Insert aanvoer	
	$insert_tblHistorie_aank = "INSERT INTO tblHistorie set stalId = ".$stalId.", datum = '".$flddag."', actId = 2 ";
/*echo $insert_tblHistorie_aank.'<br>';*/		mysqli_query($db,$insert_tblHistorie_aank) or die (mysqli_error($db));

if(isset($fldHok)) { 
$zoek_hisId = mysqli_query($db,"SELECT hisId FROM tblHistorie WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."' and actId = 2 ") or die (mysqli_error($db));
	while ( $aanvId = mysqli_fetch_assoc ($zoek_hisId)) { $hisId_aanv = $aanvId['hisId']; }

	$insert_tblBezet = "INSERT INTO tblBezet set hisId = '".mysqli_real_escape_string($db,$hisId_aanv)."', hokId = '".mysqli_real_escape_string($db,$fldHok)."' ";
/*echo $insert_tblBezet.'<br>';*/		mysqli_query($db,$insert_tblBezet) or die (mysqli_error($db));
	}
  // Einde Insert aanvoer

  // Insert aanwas
$zoek_aanwas_hisId = mysqli_query($db,"
SELECT hisId
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and actId = 3
") or die (mysqli_error($db));
	while ( $haId = mysqli_fetch_assoc ($zoek_aanwas_hisId)) { $aanwId = $haId['hisId']; }

if(!isset($aanwId)) {	
	$insert_tblHistorie_aanw = "INSERT INTO tblHistorie set stalId = ".$stalId.", datum = '".$flddag."', actId = 3 ";
/*echo $insert_tblHistorie_aanw.'<br>';*/		mysqli_query($db,$insert_tblHistorie_aanw) or die (mysqli_error($db));
}
  // Einde Insert aanwas

// Einde Insert tblHistorie

if($reader == 'Agrident') {
	$updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' ";
}
else {
	$updateReader = "UPDATE impReader set verwerkt = 1 WHERE readId = '".mysqli_real_escape_string($db,$recId)."' ";
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
if (
 isset($flddag) && isset($dbLevnr) && isset($fldRas) && isset($fldSekse) && $fldFase == 'lam' && 
 ( ($modtech == 1 && isset($fldKg) && isset($fldHok)) || ($modtech == 0) )
)
{
$zoek_schaapId = mysqli_query($db,"
SELECT schaapId FROM tblSchaap WHERE levensnummer = '".mysqli_real_escape_string($db,$dbLevnr)."'
") or die (mysqli_error($db));
	while ( $sId = mysqli_fetch_assoc($zoek_schaapId)) { $schaapId = $sId['schaapId']; /*echo $schaapId.'<br>';*/ }
	
if(!isset($schaapId)) { // Als lam nog niet bestaat in tblSchaap
	if($modtech == 1) /*{ $volwId = 'NULL'; $fldKg = 'NULL'; }
	else*/ {
	$insert_tblVolwas = "INSERT INTO tblVolwas set readId = '".mysqli_real_escape_string($db,$recId)."', mdrId = " . db_null_input($fldMoeder) . " ";/*$recId als Relatie met impReader. Handig om te weten */
/*echo $insert_tblVolwas.'<br>';*/		mysqli_query($db,$insert_tblVolwas) or die (mysqli_error($db));	

$zoek_volwId = mysqli_query($db,"
SELECT volwId FROM tblVolwas WHERE readId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));
	while ( $vId = mysqli_fetch_assoc($zoek_volwId)) { $volwId = $vId['volwId']; }
	}
	
	$insert_tblSchaap = "INSERT INTO tblSchaap set levensnummer = '".mysqli_real_escape_string($db,$dbLevnr)."', rasId = " . db_null_input($fldRas) . ", geslacht = '".mysqli_real_escape_string($db,$fldSekse)."', volwId = '".mysqli_real_escape_string($db,$volwId)."' ";	
/*echo $insert_tblSchaap.'<br>';*/		mysqli_query($db,$insert_tblSchaap) or die (mysqli_error($db));	
	 
	$zoek_schaapId = mysqli_query($db,"SELECT schaapId FROM tblSchaap WHERE levensnummer = '".mysqli_real_escape_string($db,$dbLevnr)."' ") or die (mysqli_error($db));
		while ( $sId = mysqli_fetch_assoc ($zoek_schaapId)) { $schaapId = $sId['schaapId']; }
}
	
	$insert_tblStal = "INSERT INTO tblStal set lidId = '".mysqli_real_escape_string($db,$lidId)."', schaapId = '".mysqli_real_escape_string($db,$schaapId)."', rel_herk = " . db_null_input($fldHerk) . " ";
/*echo 'lam '.$insert_tblStal.'<br>';*/		mysqli_query($db,$insert_tblStal) or die (mysqli_error($db));

	$zoek_stalId = mysqli_query($db,"SELECT stalId FROM tblStal WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."', schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and isnull(rel_best) ") or die (mysqli_error($db));
		while ( $stId = mysqli_fetch_assoc ($zoek_stalId)) { $stalId = $stId['stalId']; }
	
	$insert_tblHistorie_aank = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$flddag)."', kg = " . db_null_input($fldKg) . ", actId = 2 ";
/*echo $insert_tblHistorie_aank.'<br>';*/		mysqli_query($db,$insert_tblHistorie_aank) or die (mysqli_error($db));

// $zoek_hisId is voor tblBezet �n tblMelding
	$zoek_hisId = mysqli_query($db,"SELECT hisId FROM tblHistorie WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."' and actId = 2 ") or die (mysqli_error($db));
		while ( $hId = mysqli_fetch_assoc ($zoek_hisId)) { $hisId = $hId['hisId']; }
		
if($modtech == 1) {
	
	$insert_tblBezet = "INSERT INTO tblBezet set hokId = '".mysqli_real_escape_string($db,$fldHok)."', hisId = '".mysqli_real_escape_string($db,$hisId)."' " ;
/*echo $insert_tblBezet.'<br>';*/		mysqli_query($db,$insert_tblBezet) or die (mysqli_error($db));	
// Einde Insert tblBezet			
	}

if($reader == 'Agrident') {
	$updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' ";
}
else {
	$updateReader = "UPDATE impReader set verwerkt = 1 WHERE readId = '".mysqli_real_escape_string($db,$recId)."' ";
}
/*echo $updateReader.'<br>';*/		mysqli_query($db,$updateReader) or die (mysqli_error($db));

if ($modmeld == 1 ) {		// Insert tblMeldingen
$Melding = 'AAN';
include "maak_request.php";
	// Einde Insert tblMeldingen
}	
unset($schaapId);	
unset($periId);	
}
// EINDE CONTROLE op alle verplichten velden bij aankoop lammeren

	
	} // Einde if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt))					

	
  if($fldKies == 0 && $fldDel == 1) {	
	
if($reader == 'Agrident') {
    $updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' " ;
}
else {
	$updateReader = "UPDATE impReader set verwerkt = 1 WHERE readId = '".mysqli_real_escape_string($db,$recId)."' " ;
}
/*echo $updateReader.'<br>';*/		mysqli_query($db,$updateReader) or die (mysqli_error($db));
	}





unset($dbLevnr);
	} // Einde foreach($array as $recId => $id)
?>
					
	