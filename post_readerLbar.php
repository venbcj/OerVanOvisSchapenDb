<!-- 3-9-2016 : sql beveiligd
20-1-2017 : Query's aangepast n.a.v. nieuwe tblDoel en hidden velden in insOverplaats.php verwijderd en codering hier aangepast 		22-1-2017 : tblBezetting gewijzigd naar tblBezet 
28-6-2017 : insert tblPeriode verwijderd Priode wordt sinds 12-2-2017 niet meer opgeslagen in tblBezet.
11-6-2020 : onderscheid gemaakt tussen reader Agrident en Biocontrol
13-7-2020 : impVerplaatsing gewijzigd in impAgrident -->

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

	if ($key == 'txtDag' ) { $dag = date_create($value); $valuedatum =  date_format($dag, 'Y-m-d'); 
									/*echo $key.'='.$valuedatum.' ';*/ $fldDag = $valuedatum; }

	if ($key == 'txtKg' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldKg = $value; }

									}

// CONTROLE op alle verplichten velden bij overplaatsen lam
if ( !empty($fldDag))
{


$zoek_Lambar = mysqli_query($db,"
SELECT hokId
FROM tblHok
WHERE hoknr = 'Lambar' and lidId = ".mysqli_real_escape_string($db,$lidId)."
") or die (mysqli_error($db));

	while ($lb = mysqli_fetch_assoc($zoek_Lambar)) { $hokId = $lb['hokId']; }

$zoek_levensnummer = mysqli_query($db,"
SELECT rd.levensnummer levnr
FROM impAgrident rd
WHERE rd.Id = ".mysqli_real_escape_string($db,$recId)."
") or die (mysqli_error($db));
	while ($ln = mysqli_fetch_assoc($zoek_levensnummer)) { $levnr = $ln['levnr']; }

$zoek_stalId = mysqli_query($db,"
SELECT stalId
FROM tblStal st
 join tblSchaap s on (st.schaapId = s.schaapId)
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and s.levensnummer = '".mysqli_real_escape_string($db,$levnr)."' and isnull(st.rel_best)
") or die (mysqli_error($db));
	while ($st = mysqli_fetch_assoc($zoek_stalId)) { $stalId = $st['stalId']; }
//echo '$stalId = '.$stalId.'<br>';
	
	$insert_tblHistorie = "INSERT INTO tblHistorie set stalId = ".mysqli_real_escape_string($db,$stalId).", datum = '".mysqli_real_escape_string($db,$fldDag)."', actId = 16 ";
		mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));

$zoek_hisId = mysqli_query($db,"
SELECT max(hisId) hisId
FROM tblHistorie h 
 join tblStal st on (h.stalId = st.stalId)
WHERE st.stalId = ".mysqli_real_escape_string($db,$stalId)." and actId = 16
") or die (mysqli_error($db));
	while ($hi = mysqli_fetch_assoc($zoek_hisId)) { $hisId = $hi['hisId']; }

if(isset($fldKg)) {
	$update_tblHistorie = "UPDATE tblHistorie set kg = ".mysqli_real_escape_string($db,$fldKg)." WHERE hisId = ".mysqli_real_escape_string($db,$hisId)." ";
		mysqli_query($db,$update_tblHistorie) or die (mysqli_error($db));
}

		
	$insert_tblBezet = "INSERT INTO tblBezet set hisId = ".mysqli_real_escape_string($db,$hisId).", hokId = ".mysqli_real_escape_string($db,$hokId)." ";
/*echo $insert_tblBezet.'<br>';*/		mysqli_query($db,$insert_tblBezet) or die (mysqli_error($db));


	$updateReader = "UPDATE impAgrident SET verwerkt = 1 WHERE Id = ".mysqli_real_escape_string($db,$recId)." ";

		mysqli_query($db,$updateReader) or die (mysqli_error($db));	
}
// EINDE CONTROLE op alle verplichten velden bij spenen lam
		
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
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
	