<!-- 17-11-2016; Aangemaakt als kopie van post_readerAanv.php 
10-11-2018 invoer nieuwe schapen verwijderd (incl. ras dus) i.v.m. alleen ooien en rammen van stallijst -->

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

// Id ophalen
#echo $recId.'<br>'; 
// Einde Id ophalen
   
 foreach($id as $key => $value) {
 if ($key == 'chbkies' && $value == 1 ) 	{ /* Alleen als checkbox chbkies de waarde 1 heeft  /*echo $key.'='.$value.' ';*/  $box = $value ;
 foreach($id as $key => $value) {
 if ($key == 'chbDel' && $value == 0 ) 	{ /* Alleen als checkbox Del de waarde 0 heeft  /*echo $key.'='.$value.' ';*/ ;
	
  foreach($id as $key => $value) {

	if ($key == 'txtDracdm' && !empty($value)) { $dag = date_create($value); $valuedag =  date_format($dag, 'Y-m-d'); 
									$flddag = $valuedag; }
	
	if ($key == 'kzlOoi' && !empty($value)) { /*echo '$fldOoi = '.$value.'<br>';*/ $fldOoi = $value; } // betreft schaapId

	if ($key == 'kzlRam' && !empty($value)) { /*echo '$fldRam = '.$value.'<br>';*/ $fldRam = $value; } // betreft levensnummer

	if ($key == 'kzlDracht' && !empty($value)) {  $fldDracht = $value; }
	 
									}


// CONTROLE op alle verplichten velden 
if(isset($fldOoi) && isset($fldRam) && isset($flddag)) {


// Dracht binnen laatste 183 dagen mag geen worp hebben. Dit is reeds uitgesloten in InsDracht.php Alleen gedekte moeders zoeken volstaat hier dus
$zoek_dracht_183dgn = mysqli_query($db,"
select volwId
from tblVolwas v
where date_add(v.datum,interval 183 day) > CURRENT_DATE() and v.mdrId = ".mysqli_real_escape_string($db,$fldOoi)."
") or die (mysqli_error($db));
	while ( $dra = mysqli_fetch_assoc($zoek_dracht_183dgn)) { $volwId = $dra['volwId']; }


if (isset($volwId)) { 

	$deleteDracht = "DELETE FROM tblVolwas WHERE volwId = ".mysqli_real_escape_string($db,$volwId)." ";
/*echo $deleteDracht.'<br>';*/		mysqli_query($db,$deleteDracht) or die (mysqli_error($db));
}

	$insert_tblVolwas = "INSERT INTO tblVolwas SET readId = ".mysqli_real_escape_string($db,$recId).", datum = '".mysqli_real_escape_string($db,$flddag)."', mdrId = ".mysqli_real_escape_string($db,$fldOoi).", vdrId = ".mysqli_real_escape_string($db,$fldRam).", drachtig = ".mysqli_real_escape_string($db,$fldDracht)." ";	
/*echo $insert_tblVolwas.'<br>';*/		mysqli_query($db,$insert_tblVolwas) or die (mysqli_error($db));	

		$updateReader = "UPDATE impReader SET verwerkt = 1 WHERE readId = ".mysqli_real_escape_string($db,$recId)." ";
/*echo $updateReader.'<br>';*/		mysqli_query($db,$updateReader) or die (mysqli_error($db));


unset($fldOoi); unset($fldRam); unset($volwId);
// EINDE CONTROLE op alle verplichten velden 

}  // Einde if(isset($fldOoi) && isset($fldRam))
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
/*echo $updateReader.'<br>';*/		mysqli_query($db,$updateReader) or die (mysqli_error($db));
	}

										} // EINDE Alleen als checkbox Del de waarde 1 heeft 
	}
										} // EINDE Alleen als checkbox chbkies de waarde 0 heeft
    }



unset($fldlevnr);
	}
?>
					
	