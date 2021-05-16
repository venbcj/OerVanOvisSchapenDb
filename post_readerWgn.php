<?php
/* 3-9-2017 aangemaakt 
8-5-2021 : isset($verwerkt) toegevoegd om dubbele invoer te voorkomen. Verschil tussen kiezen of verwijderen herschreven. SQL beveiligd met quotes */

include "url.php";

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
//echo '$recId = '.$recId.'<br>'; 
// Einde Id ophalen
	
  foreach($id as $key => $value) {

  	if ($key == 'chbkies') { $fldKies = $value; }
  	if ($key == 'chbDel') { $fldDel = $value; }

	if ($key == 'txtWeegdag' && !empty($value)) { $dag = date_create($value); $flddag =  date_format($dag, 'Y-m-d');  }
	
	if ($key == 'txtKg' && !empty($value)) { $fldkg = str_replace(',', '.', $value); }
		
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

// CONTROLE op alle verplichten velden
if ( isset($flddag) && isset($fldkg) )
{
$zoek_levensnummer = mysqli_query($db,"
SELECT levnr_weeg
FROM impReader
WHERE readId = '".mysqli_real_escape_string($db,$recId)."' 
") or die (mysqli_error($db));
	while ($lvn = mysqli_fetch_assoc($zoek_levensnummer)) { $levnr = $lvn['levnr_weeg']; }
	
$zoek_schaapId = mysqli_query($db,"
SELECT schaapId
FROM tblSchaap
WHERE levensnummer = '".mysqli_real_escape_string($db,$levnr)."'
") or die (mysqli_error($db));
	while ($sId = mysqli_fetch_assoc($zoek_schaapId)) { $schaapId = $sId['schaapId']; }
	
$zoek_stalId = mysqli_query($db,"
SELECT stalId
FROM tblStal
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and isnull(rel_best)
") or die (mysqli_error($db));
	while ($stId = mysqli_fetch_assoc($zoek_stalId)) { $stalId = $stId['stalId']; }


	$insert_tblHistorie = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$flddag)."', kg = '".mysqli_real_escape_string($db,$fldkg)."', actId = 9 ";
/*echo $insert_tblHistorie.'<br>';*/		mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));
unset ($fldkg);

	
	$updateReader	=	"UPDATE impReader SET verwerkt = 1 WHERE readId = '".mysqli_real_escape_string($db,$recId)."' ";
/*echo '$updateReader = '.$updateReader.'<br>';*/		mysqli_query($db,$updateReader) or die (mysqli_error($db));	

}
// EINDE CONTROLE op alle verplichten velden

} // Einde if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt))


	
if ($fldKies == 0 && $fldDel == 1) {	
	
    $updateReader = "UPDATE impReader SET verwerkt = 1 WHERE readId = '".mysqli_real_escape_string($db,$recId)."' " ;
	mysqli_query($db,$updateReader) or die (mysqli_error($db));
	}
	
	
	
	}

?>
					
	