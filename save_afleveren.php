<?php
/* 22-11-2015 gemaakt 
15-02-2017 : gewicht niet verplicht gemaakt en (extra) controle op 'maximale datum uit historie' verwijderd 
06-06-2018 : kg met komma wordt omgezet naar kg met punt 
01-01-2024 :  sql beveiligd met quotes en db_null_input() 
20-02-2025 Hidden velden in HokAfleveren.php verwijderd en hier lege checkboxen gedefinieerd ondanks dat het niet nodig is! */

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

foreach($_POST as $fldname => $fldvalue) {
    
    $multip_array[getIdFromKey($fldname)][getNameFromKey($fldname)] = $fldvalue;
}
foreach($multip_array as $recId => $id) {

unset($fldKies);
unset($updDag);
unset($updLevnr);
unset($updKg);
unset($updRelId);

foreach($id as $key => $value) {
 	if ($key == 'chbkies' && $value == 1) 	{ /*echo $key.'='.$value.' ';*/  $fldKies = $value; }

	if ($key == 'txtDatum' ) { $dag = date_create($value); $updDag =  date_format($dag, 'Y-m-d');  }
	
	if ($key == 'txtLevnr' && !empty($value)) { $updLevnr = $value; }
	
	if ($key == 'txtKg' && !empty($value)) { $updKg = str_replace(',', '.', $value); }

	if ($key == 'kzlRel' && !empty($value)) { $updRelId = $value; }
		
									}

if(!isset($fldKies)) { $fldKies = 0; }

// CONTROLE op alle verplichten velden bij spenen lam
if ($fldKies == 1 && !empty($updDag) && !empty($updRelId))
{
/*
echo "Datum = ".$updDag.'<br>' ; 
echo "Kg = ".$updKg.'<br>' ; 
echo "relatId = ".$updRelId.'<br><br>' ; */

$zoek_stalId = mysqli_query($db,"
SELECT stalId
FROM tblStal st
WHERE isnull(st.rel_best) and st.schaapId = '".mysqli_real_escape_string($db,$recId)."' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die(mysqli_error($db));

while ($st = mysqli_fetch_assoc($zoek_stalId)) { $stalId = $st['stalId']; }

	$insert_tblHistorie = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$updDag)."', kg = ".db_null_input($updKg).", actId = '".mysqli_real_escape_string($db,$actId)."' ";
/*echo $insert_tblHistorie.'<br>';*/	mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));

if ($modmeld == 1 ) {
$Melding = 'AFV';
$afvoerd = $updDag;

$zoek_hisId = mysqli_query($db,"
SELECT max(hisId) hisId
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = 
'".mysqli_real_escape_string($db,$actId)."'
") or die(mysqli_error($db));

while ($per = mysqli_fetch_assoc($zoek_hisId)) { $hisId = $per['hisId']; }

include "maak_request.php";

}
	$update_tblStal = "UPDATE tblStal set rel_best = '".mysqli_real_escape_string($db,$updRelId)."' WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."' ";	
		mysqli_query($db,$update_tblStal) or die (mysqli_error($db));

}
// EINDE CONTROLE op alle verplichten velden bij spenen lam


			} ?>
					
	