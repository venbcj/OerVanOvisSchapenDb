<!-- 4-7-2020 : Gekopieerd van post_readerAdop.php 
	21-9-2020 OMN moet VMD zijn 
1-02-2021 Transponder toegevoegd -->

<?php
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

	if ($key == 'txtDag' && !empty($value)) { $dag = date_create($value); $valuedate =  date_format($dag, 'Y-m-d'); 
									/*echo $key.'='.$valuedate.' ';*/ $fldDay = $valuedate; }

									}

// CONTROLE op alle verplichten velden bij omnummeren lam
if ( isset($fldDay))
{
	
$zoek_old_levensnummer = mysqli_query($db,"
SELECT rd.levensnummer levnr
FROM impAgrident rd
WHERE rd.Id = ".mysqli_real_escape_string($db,$recId)."
") or die (mysqli_error($db));
	while ($dl = mysqli_fetch_assoc($zoek_old_levensnummer)) { $levnr_old = $dl['levnr']; }
//echo '$levnr = '.$levnr.'<br>';

$zoek_new_levensnummer = mysqli_query($db,"
SELECT rd.nieuw_nummer levnr, nieuw_transponder tran
FROM impAgrident rd
WHERE rd.Id = ".mysqli_real_escape_string($db,$recId)."
") or die (mysqli_error($db));
	while ($nl = mysqli_fetch_assoc($zoek_new_levensnummer)) { $levnr_new = $nl['levnr'];  $tran_new = $nl['tran']; }


$zoek_stalId = mysqli_query($db,"
SELECT stalId, s.schaapId
FROM tblStal st
 join tblSchaap s on (st.schaapId = s.schaapId)
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and s.levensnummer = '".mysqli_real_escape_string($db,$levnr_old)."' and isnull(st.rel_best)
") or die (mysqli_error($db));
	while ($st = mysqli_fetch_assoc($zoek_stalId)) { $stalId = $st['stalId']; $schaapId = $st['schaapId']; }
//echo '$stalId = '.$stalId.'<br>';

	
	$insert_tblHistorie = "INSERT INTO tblHistorie set stalId = ".mysqli_real_escape_string($db,$stalId).", datum = '".mysqli_real_escape_string($db,$fldDay)."', actId = 17, oud_nummer = '".mysqli_real_escape_string($db,$levnr_old)."' ";
		mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));

	$uupdate_tblSchaap = "UPDATE tblSchaap set levensnummer = '".mysqli_real_escape_string($db,$levnr_new)."', transponder = " . db_null_input($tran_new) . "  WHERE schaapId = ".mysqli_real_escape_string($db,$schaapId);
		mysqli_query($db,$uupdate_tblSchaap) or die (mysqli_error($db));

if($modmeld == 1) {
$zoek_hisId = mysqli_query($db,"
SELECT max(hisId) hisId
FROM tblHistorie
WHERE stalId = ".mysqli_real_escape_string($db,$stalId)." and actId = 17
") or die (mysqli_error($db));
	while ($zh = mysqli_fetch_assoc($zoek_hisId)) { $hisId = $zh['hisId']; }

$Melding = 'VMD';
include "maak_request.php";

}

	$updateReader = "UPDATE impAgrident SET verwerkt = 1 WHERE Id = ".mysqli_real_escape_string($db,$recId)." ";
		mysqli_query($db,$updateReader) or die (mysqli_error($db));	
}
// EINDE CONTROLE op alle verplichten velden bij omnummeren lam
		
										} // EINDE Alleen als checkbox Del de waarde 0 heeft  
    }

										} // EINDE Alleen als checkbox chbkies de waarde 1 heeft
	}

 foreach($id as $key => $value) {
 if ($key == 'chbkies' && $value == 0 ) 	{ /* Alleen als checkbox chbkies de waarde 0 heeft  /*echo $key.'='.$value.' ';*/  $box = $value ;
 foreach($id as $key => $value) {
 if ($key == 'chbDel' && $value == 1 ) 	{ /* Alleen als checkbox Del de waarde 1 heeft  /*echo $key.'='.$value.' ';*/ ;
	

    $updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = ".mysqli_real_escape_string($db,$recId)." " ;
	mysqli_query($db,$updateReader) or die (mysqli_error($db));

										} // EINDE Alleen als checkbox Del de waarde 1 heeft 
	}
										} // EINDE Alleen als checkbox chbkies de waarde 0 heeft
    }


	
						}
?>
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
	