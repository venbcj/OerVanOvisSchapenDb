<!-- 17-11-2014 include "Maak_Request.php"; toegevoegd 
28-2-2017 :  Ras en gewicht niet veplicht gemaakt 
20-4-2017 : gezorgd dat $fldKg bestaat als module technisch nvt is 
29-6-2017 : unset($aanwas) toegevoegd nadat moeder is afgevoerd en het volgende dier is een lam 
15-2-2019 : if(!isset($stalId)) { echo $fldLevnr.' staat niet meer op de stallijst !'; } toegevoegd
27-6-2020 : reden afvoer toegevoegd 
13-7-2020 : impVerplaatsing gewijzigd in impAgrident 16-7 : unset($hisId); toegevoegd omdat bij Marcel 29 van de 32 dezelfde hisId's zijn opgeslagen bij reqId 905 -->

<?php /*
include "url.php";

include "passw.php";
Include "connect_db.php";*/ //Deze include zit ook in login.php maar binnen InsAfvoeren.php is include"login.php"; nog niet gepasseerd. Hier laten staan dus.

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
	//if ($key == 'txtId' ) { /*echo $key.'='.$value.' ';*/ $fldId = $value; }	

  if ($key == 'chbkies' /*&& $value == 1*/ ) 	{ /*$box = $value ; */ $fldKies = $value; }

  if ($key == 'chbDel' /*&& $value == 0*/ ) 	{ $fldDel = $value; }


	if ($key == 'txtAfvoerdag' && !empty($value)) { $dag = date_create($value); $valuedatum =  date_format($dag, 'Y-m-d'); 
									/*echo $key.'='.$valuedatum.' ';*/ $fldDag = $valuedatum; }
	
	if ($key == 'txtlevafl' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldLevnr = $value; }	
	
	if ($key == 'txtKg' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldKg = str_replace(',', '.', $value); }
	 else if ($key == 'txtKg' && empty($value)) { /*echo $key.'='.$value.' ';*/ $fldKg = 'NULL'; } if(!isset($fldKg)) { $fldKg = 'NULL'; } /*Als module technisch nvt is */

	if ($key == 'kzlBest' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldBest = $value; }

	if ($key == 'kzlReden' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldReduId = $value; }

	 
									}

if ($fldKies == 1 && $fldDel == 0) {

//if($recId > 0) {
// CONTROLE op alle verplichten velden bij afvoer
if ( isset($fldDag) && isset($fldBest) )
{
$zoek_schaapId = mysqli_query($db,"
SELECT schaapId
FROM tblSchaap
WHERE levensnummer = '".mysqli_real_escape_string($db,$fldLevnr)."'
") or die (mysqli_error($db));
	while ($sId = mysqli_fetch_assoc($zoek_schaapId)) { $schaapId = $sId['schaapId']; }
		
$zoek_stalId = mysqli_query($db,"
SELECT stalId
FROM tblStal
WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." and schaapId = ".mysqli_real_escape_string($db,$schaapId)." and isnull(rel_best)
") or die (mysqli_error($db));
		while ($stId = mysqli_fetch_assoc($zoek_stalId)) { $stalId = $stId['stalId']; }

if(!isset($stalId)) { echo $fldLevnr.' staat niet meer op de stallijst !'; }
else {
$zoek_aanwas = mysqli_query($db,"
SELECT hisId
FROM tblHistorie
WHERE stalId = ".mysqli_real_escape_string($db,$stalId)." and actId = 3
") or die (mysqli_error($db));
	while ($awId = mysqli_fetch_assoc($zoek_aanwas)) { $aanwas = $awId['hisId']; }
	
if(isset($aanwas)) { $actId = 13; } else { $actId = 12; } unset($aanwas);
	
if(isset($fldKg) && $fldKg <> 'NULL') { // bij lammeren of moeder- en vaderdieren
$insert_tblHistorie = "
INSERT INTO tblHistorie 
set stalId = ".mysqli_real_escape_string($db,$stalId).", datum = '".mysqli_real_escape_string($db,$fldDag)."', kg = '".mysqli_real_escape_string($db,$fldKg)."', actId = ".mysqli_real_escape_string($db,$actId)." ";
}

else if(isset($fldKg) && $fldKg == 'NULL') { // bij lammeren of moeder- en vaderdieren
$insert_tblHistorie = "
INSERT INTO tblHistorie 
set stalId = ".mysqli_real_escape_string($db,$stalId).", datum = '".mysqli_real_escape_string($db,$fldDag)."', kg = ".mysqli_real_escape_string($db,$fldKg).", actId = ".mysqli_real_escape_string($db,$actId)." ";
}

	/*echo $insert_tblHistorie.'<br>';*/	mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));

unset($hisId);
// INLEZEN REDEN AFVOER
if(isset($fldReduId)) {

$zoek_hisId = mysqli_query($db,"
SELECT hisId
FROM tblHistorie
WHERE stalId = ".$stalId." and actId = ".$actId." and skip = 0
") or die (mysqli_error($db));
		while ( $hId = mysqli_fetch_assoc ($zoek_hisId)) { $hisId = $hId['hisId']; }

$update_tblHistorie = "UPDATE tblHistorie
set reduId = ".mysqli_real_escape_string($db,$fldReduId)."
WHERE hisId = ".mysqli_real_escape_string($db,$hisId)." ";
/*echo $update_tblHistorie.'<br>';*/	mysqli_query($db,$update_tblHistorie) or die (mysqli_error($db));

}
// Einde INLEZEN REDEN AFVOER
	 
// Update tblStal
$update_tblStal = "UPDATE tblStal
set rel_best = ".mysqli_real_escape_string($db,$fldBest)."
WHERE stalId = ".mysqli_real_escape_string($db,$stalId)." ";
/*echo $update_tblStal.'<br>';*/	mysqli_query($db,$update_tblStal) or die (mysqli_error($db));
// Einde Update tblStal

if($reader == 'Agrident') {
$updateReader = "UPDATE impAgrident SET verwerkt = 1 WHERE Id = ".mysqli_real_escape_string($db,$recId)." " ;
	}
	else {		
$updateReader = "UPDATE impReader set verwerkt = 1 WHERE readId = ".mysqli_real_escape_string($db,$recId)." ";
}
/*echo $updateReader.'<br>';*/	mysqli_query($db,$updateReader) or die (mysqli_error($db));	

if ($modmeld == 1 ) {

	if(!isset($hisId)) {
$zoek_hisId = mysqli_query($db,"
SELECT hisId
FROM tblHistorie
WHERE stalId = ".$stalId." and actId = ".$actId." and skip = 0
") or die (mysqli_error($db));
		while ( $hId = mysqli_fetch_assoc ($zoek_hisId)) { $hisId = $hId['hisId']; }
	}

$Melding = 'AFV';
include "maak_request.php";
}
} // Einde else isset($stalId)
} // Einde if ( isset($fldDag) && isset($fldBest) )
// EINDE CONTROLE op alle verplichten velden bij afvoer
						
  unset($fldDag);
  unset($fldKg);
  unset($fldBest);  
  unset($fldReduId);  
}





if ($fldKies == 0 && $fldDel == 1) {


	if($reader == 'Agrident') {
    $updateReader = "UPDATE impAgrident SET verwerkt = 1 WHERE Id = ".mysqli_real_escape_string($db,$recId)." " ;
	}
	else {
    $updateReader = "UPDATE impReader set verwerkt = 1 WHERE readId = ".mysqli_real_escape_string($db,$recId)." " ;
	}
/*echo $updateReader.'<br>';*/ mysqli_query($db,$updateReader) or die (mysqli_error($db));
										
	}
										
    




	}

?>
					
	