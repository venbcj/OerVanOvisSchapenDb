<!-- 09-11-2024 : Kopie gemaakt van post_readerAfv.php -->

<?php 
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

unset($fldKies);
unset($fldDel);
unset($fldDay);
unset($fldBest);

// Id ophalen
//echo $recId.'<br>'; 
// Einde Id ophalen

	
  foreach($id as $key => $value) {
	//if ($key == 'txtId' ) { /*echo $key.'='.$value.' ';*/ $fldId = $value; }	

  if ($key == 'chbkies' /*&& $value == 1*/ ) 	{ /*$box = $value ; */ $fldKies = $value; }

  if ($key == 'chbDel' /*&& $value == 0*/ ) 	{ $fldDel = $value; }


	if ($key == 'txtAfvoerdag' && !empty($value)) { $dag = date_create($value); $valuedatum =  date_format($dag, 'Y-m-d'); 
									/*echo $key.'='.$valuedatum.' ';*/ $fldDay = $valuedatum; }	

	if ($key == 'kzlBest' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldBest = $value; }

	 
									}
// Als checkboxen niet bestaan
if(!isset($fldKies)) { $fldKies = 0; }
if(!isset($fldDel)) { $fldDel = 0; }

//echo '<br>$fldKies = '.$fldKies.', $fldDel = '.$fldDel.'<br><br>';

// (extra) controle of readerregel reeds is verwerkt. Voor als de pagina 2x wordt verstuurd bij fouten op de pagina
unset($verwerkt);

$zoek_readerRegel_verwerkt = mysqli_query($db,"
SELECT verwerkt
FROM impAgrident
WHERE Id = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db)); 

while($verw = mysqli_fetch_array($zoek_readerRegel_verwerkt))
{ $verwerkt = $verw['verwerkt']; }
// Einde (extra) controle of readerregel reeds is verwerkt.


/**** AFVOER REGISTREREN ****/

if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt)) {

//if($recId > 0) {
// CONTROLE op alle verplichten velden bij afvoer
if (isset($fldDay) && isset($fldBest) )
{
$zoek_levensnummer = mysqli_query($db,"
SELECT levensnummer
FROM impAgrident
WHERE Id = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));
	while ($zl = mysqli_fetch_assoc($zoek_levensnummer)) { $fldLevnr = $zl['levensnummer']; }

$zoek_schaapId = mysqli_query($db,"
SELECT schaapId
FROM tblSchaap
WHERE levensnummer = '".mysqli_real_escape_string($db,$fldLevnr)."'
") or die (mysqli_error($db));
	while ($sId = mysqli_fetch_assoc($zoek_schaapId)) { $schaapId = $sId['schaapId']; }
		
$zoek_stalId = mysqli_query($db,"
SELECT stalId
FROM tblStal
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and isnull(rel_best)
") or die (mysqli_error($db));
		while ($stId = mysqli_fetch_assoc($zoek_stalId)) { $stalId = $stId['stalId']; }

if(!isset($stalId)) { echo $fldLevnr.' staat niet meer op de stallijst !'; }
else {	

$insert_tblHistorie = "
INSERT INTO tblHistorie 
set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$fldDay)."', actId = 10 ";

	/*echo $insert_tblHistorie.'<br>';*/	mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));

unset($hisId);
	 
// Update tblStal
$update_tblStal = "UPDATE tblStal
set rel_best = '".mysqli_real_escape_string($db,$fldBest)."'
WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."' ";
/*echo $update_tblStal.'<br>';*/	mysqli_query($db,$update_tblStal) or die (mysqli_error($db));
// Einde Update tblStal

//if($reader == 'Agrident') {
$updateReader = "UPDATE impAgrident SET verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' " ;
/*	}
	else {		
$updateReader = "UPDATE impReader set verwerkt = 1 WHERE readId = '".mysqli_real_escape_string($db,$recId)."' ";
}*/
/*echo $updateReader.'<br>';*/	mysqli_query($db,$updateReader) or die (mysqli_error($db));	

if ($modmeld == 1 ) {

	if(!isset($hisId)) {
$zoek_hisId = mysqli_query($db,"
SELECT hisId
FROM tblHistorie
WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."' and actId = 10 and skip = 0
") or die (mysqli_error($db));
		while ( $hId = mysqli_fetch_assoc ($zoek_hisId)) { $hisId = $hId['hisId']; }
	}

$Melding = 'AFV';
include "maak_request.php";
}
} // Einde else van if(!isset($stalId))
} // Einde if ( isset($fldDay) && isset($fldBest) )
// EINDE CONTROLE op alle verplichten velden bij afvoer
						  
} // Einde if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt))


/**** Einde AFVOER REGISTREREN ****/


/**** VERWIJDEREN ****/


if ($fldKies == 0 && $fldDel == 1) {


    $updateReader = "UPDATE impAgrident SET verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' " ;

/*echo $updateReader.'<br>';*/  mysqli_query($db,$updateReader) or die (mysqli_error($db));
										
	}
										
/**** Einde VERWIJDEREN ****/  




	}

?>
					
	