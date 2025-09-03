<!-- 19-12-2021; Aangemaakt als kopie van post_readerDracht.php  -->

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

  if ($key == 'chbKies')   { $fldKies = $value; }
  if ($key == 'chbDel')    { $fldDel = $value; }

	if ($key == 'txtDatum' && !empty($value)) { $dag = date_create($value); $valuedag =  date_format($dag, 'Y-m-d'); 
									$fldDag = $valuedag; }
	
	if ($key == 'kzlOoi' && !empty($value)) { /*echo '$fldOoi = '.$value.'<br>';*/ $fldOoi = $value; } // betreft schaapId ooi

	if ($key == 'kzlRam' && !empty($value)) { /*echo '$fldRam = '.$value.'<br>';*/ $fldRam = $value; } // betreft schaapId ram
	 
									}
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

if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt)) { // isset($verwerkt) is een extra controle om dubbele invoer te voorkomen


// CONTROLE op alle verplichten velden 
if(isset($fldDag) && isset($fldOoi)) {

// De ooi mag binnen laatste 183 dagen geen worp hebben.

$zoek_stalId = mysqli_query($db,"
SELECT stalId
FROM tblStal
WHERE schaapId = '".mysqli_real_escape_string($db,$fldOoi)."' and lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(rel_best)
") or die (mysqli_error($db));

while($zs = mysqli_fetch_assoc($zoek_stalId)) { $stalId = $zs['stalId']; }

	$insert_tblHistorie = "INSERT INTO tblHistorie SET stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$fldDag)."', actId = 18 ";	
/*echo $insert_tblHistorie.'<br>';*/		mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));

$zoek_hisId = mysqli_query($db,"
SELECT max(hisId) hisId
FROM tblHistorie
WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."' and datum = '".mysqli_real_escape_string($db,$fldDag)."' and actId = 18
") or die (mysqli_error($db));

while($zh = mysqli_fetch_assoc($zoek_hisId)) { $hisId = $zh['hisId']; }

	$insert_tblVolwas = "INSERT INTO tblVolwas SET readId = '".mysqli_real_escape_string($db,$recId)."', hisId = '".mysqli_real_escape_string($db,$hisId)."', mdrId = '".mysqli_real_escape_string($db,$fldOoi)."', vdrId = ". db_null_input($fldRam);	
/*echo $insert_tblVolwas.'<br>';*/		mysqli_query($db,$insert_tblVolwas) or die (mysqli_error($db));	

		$updateReader = "UPDATE impAgrident SET verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' ";
/*echo $updateReader.'<br>';*/		mysqli_query($db,$updateReader) or die (mysqli_error($db));


unset($fldOoi); unset($fldRam);
// EINDE CONTROLE op alle verplichten velden 

}  // Einde if(isset($fldOoi) && isset($fldRam) && isset($fldDag))

} // Einde if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt))

	
 if($fldKies == 0 && $fldDel == 1) {	
	
    $updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' " ;
/*echo $updateReader.'<br>';*/		mysqli_query($db,$updateReader) or die (mysqli_error($db));
	}





unset($fldlevnr);
	}
?>
					
	