<!-- 17-11-2016; Aangemaakt als kopie van post_readerAanv.php 
10-11-2018 invoer nieuwe schapen verwijderd (incl. ras dus) i.v.m. alleen ooien en rammen van stallijst 
7-5-2021 : isset($verwerkt) toegevoegd om dubbele invoer te voorkomen. Verschil tussen kiezen of verwijderen herschreven. SQL beveiligd met quotes. -->

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

  if ($key == 'chbkies')   { $fldKies = $value; }
  if ($key == 'chbDel')    { $fldDel = $value; }

	if ($key == 'txtDracdm' && !empty($value)) { $dag = date_create($value); $valuedag =  date_format($dag, 'Y-m-d'); 
									$flddag = $valuedag; }
	
	if ($key == 'kzlOoi' && !empty($value)) { /*echo '$fldOoi = '.$value.'<br>';*/ $fldOoi = $value; } // betreft schaapId

	if ($key == 'kzlRam' && !empty($value)) { /*echo '$fldRam = '.$value.'<br>';*/ $fldRam = $value; } // betreft levensnummer

	if ($key == 'kzlDracht' && !empty($value)) {  $fldDracht = $value; }
	 
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
if(isset($fldOoi) && isset($fldRam) && isset($flddag)) {


// Dracht binnen laatste 183 dagen mag geen worp hebben. Dit is reeds uitgesloten in InsDracht.php Alleen gedekte moeders zoeken volstaat hier dus
$zoek_dracht_183dgn = mysqli_query($db,"
SELECT volwId
FROM tblVolwas v
WHERE date_add(v.datum,interval 183 day) > CURRENT_DATE() and v.mdrId = '".mysqli_real_escape_string($db,$fldOoi)."'
") or die (mysqli_error($db));
	while ( $dra = mysqli_fetch_assoc($zoek_dracht_183dgn)) { $volwId = $dra['volwId']; }


if (isset($volwId)) { 

	$deleteDracht = "DELETE FROM tblVolwas WHERE volwId = '".mysqli_real_escape_string($db,$volwId)."' ";
/*echo $deleteDracht.'<br>';*/		mysqli_query($db,$deleteDracht) or die (mysqli_error($db));
}

	$insert_tblVolwas = "INSERT INTO tblVolwas SET readId = '".mysqli_real_escape_string($db,$recId)."', datum = '".mysqli_real_escape_string($db,$flddag)."', mdrId = '".mysqli_real_escape_string($db,$fldOoi)."', vdrId = '".mysqli_real_escape_string($db,$recId)."', drachtig = '".mysqli_real_escape_string($db,$recId)."' ";	
/*echo $insert_tblVolwas.'<br>';*/		mysqli_query($db,$insert_tblVolwas) or die (mysqli_error($db));	

		$updateReader = "UPDATE impReader SET verwerkt = 1 WHERE readId = '".mysqli_real_escape_string($db,$recId)."' ";
/*echo $updateReader.'<br>';*/		mysqli_query($db,$updateReader) or die (mysqli_error($db));


unset($fldOoi); unset($fldRam); unset($volwId);
// EINDE CONTROLE op alle verplichten velden 

}  // Einde if(isset($fldOoi) && isset($fldRam) && isset($flddag))

} // Einde if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt))

	
 if($fldKies == 0 && $fldDel == 1) {	
	
    $updateReader = "UPDATE impReader SET verwerkt = 1 WHERE readId = '".mysqli_real_escape_string($db,$recId)."' " ;
/*echo $updateReader.'<br>';*/		mysqli_query($db,$updateReader) or die (mysqli_error($db));
	}





unset($fldlevnr);
	}
?>
					
	