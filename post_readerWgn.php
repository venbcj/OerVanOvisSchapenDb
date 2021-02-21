<?php
/* 3-9-2017 aangemaakt */

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
 if ($key == 'chbkies' && $value == 1 ) 	{ /* Alleen als checkbox chbkies de waarde 1 heeft  /*echo $key.'='.$value.' ';*/  $box = $value ;
 foreach($id as $key => $value) {
 if ($key == 'chbDel' && $value == 0 ) 	{ /* Alleen als checkbox Del de waarde 0 heeft  /*echo $key.'='.$value.' ';*/ ;
	
  foreach($id as $key => $value) {
	if ($key == 'txtWeegdag' && !empty($value)) { $dag = date_create($value); $flddag =  date_format($dag, 'Y-m-d');  }
	
	if ($key == 'txtKg' && !empty($value)) { $fldkg = str_replace(',', '.', $value); }
		
									}

// CONTROLE op alle verplichten velden
if ( isset($flddag) && isset($fldkg) )
{
$zoek_levensnummer = mysqli_query($db,"
select levnr_weeg
from impReader
where readId = ".mysqli_real_escape_string($db,$recId)." 
") or die (mysqli_error($db));
	while ($lvn = mysqli_fetch_assoc($zoek_levensnummer)) { $levnr = $lvn['levnr_weeg']; }
	
$zoek_schaapId = mysqli_query($db,"select schaapId from tblSchaap where levensnummer = '".$levnr."' ") or die (mysqli_error($db));
	while ($sId = mysqli_fetch_assoc($zoek_schaapId)) { $schaapId = $sId['schaapId']; }
	
$zoek_stalId = mysqli_query($db,"
SELECT stalId
from tblStal
where lidId = ".mysqli_real_escape_string($db,$lidId)." and schaapId = ".$schaapId." and isnull(rel_best)
") or die (mysqli_error($db));
	while ($stId = mysqli_fetch_assoc($zoek_stalId)) { $stalId = $stId['stalId']; }


	$insert_tblHistorie = "insert into tblHistorie set stalId = ".$stalId.", datum = '".$flddag."', kg = ".$fldkg.", actId = 9 ";
/*echo $insert_tblHistorie.'<br>';*/		mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));
unset ($fldkg);

	
	$updateReader	=	"UPDATE impReader SET verwerkt = 1 WHERE readId = ".mysqli_real_escape_string($db,$recId)." ";
/*echo '$updateReader = '.$updateReader.'<br>';*/		mysqli_query($db,$updateReader) or die (mysqli_error($db));	

}
// EINDE CONTROLE op alle verplichten velden




										} // EINDE Alleen als checkbox Del de waarde 0 heeft 
	}
										} // EINDE Alleen als checkbox chbkies de waarde 1 heeft
    }


 foreach($id as $key => $value) {
 if ($key == 'chbkies' && $value == 0 ) 	{ /* Alleen als checkbox chbkies de waarde 0 heeft  /*echo $key.'='.$value.' ';*/  $box = $value ;
 foreach($id as $key => $value) {
 if ($key == 'chbDel' && $value == 1 ) 	{ /* Alleen als checkbox Del de waarde 1 heeft  /*echo $key.'='.$value.' ';*/ ;
	
  foreach($id as $key => $value) {	
	
    $updateReader = "UPDATE impReader SET verwerkt = 1 WHERE readId = ".mysqli_real_escape_string($db,$recId)." " ;
	mysqli_query($db,$updateReader) or die (mysqli_error($db));
	}

										} // EINDE Alleen als checkbox Del de waarde 1 heeft 
	}
										} // EINDE Alleen als checkbox chbkies de waarde 0 heeft
    }	
	
	
	
	}

?>
					
	