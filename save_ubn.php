<?php
/* 03-07-2025 : Bestand gemaakt als kopie van save_hok.php  */

function getNaamFromKey($string) {
    $split_naam = explode('_', $string);
    return $split_naam[0];
}

function getIdFromKey($string) {
    $split_Id = explode('_', $string); 
    return $split_Id[1];
}

foreach($_POST as $fldname => $fldvalue) {  //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    
    $multip_array[getIdFromKey($fldname)][getNaamFromKey($fldname)] = $fldvalue;  // Opbouwen van een Multidimensional array met 2 indexen. [Id] [naamveld] en een waarde nl. de veldwaarde. 
}
foreach($multip_array as $recId => $id) {
unset($fldActief);
unset($fldDelete);
unset($fldAdres);
unset($fldPlaats);

#echo '<br>'.'$recId = '.$recId.'<br>';

if($recId > 0) {
	
foreach($id as $key => $value) {

	if ($key == 'chbActief' ) { $fldActief = $value; /*echo $key.'='.$value."<br/>";*/ }

	if ($key == 'chbDel' ) { $fldDelete = $value; /*echo $key.'='.$value."<br/>";*/ }
	if ($key == 'txtAdres' /*&& !empty($value)*/) { $fldAdres = $value; /*echo $key.'='.$value."<br/>";*/ }
	if ($key == 'txtPlaats') { $fldPlaats = $value; /*echo $key.'='.$value."<br/>";*/ }

								}

if(!isset($fldActief)) { $fldActief = 0; }

//echo '<br>'.'$fldActief = '.$fldActief.'<br>';



$zoek_db_waardes = mysqli_query($db,"
SELECT adres, plaats, actief
FROM tblUbn
WHERE ubnId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));


while($zdw = mysqli_fetch_assoc($zoek_db_waardes))
	{ $Adres_db = $zdw['adres'];
	  $Plaats_db = $zdw['plaats'];
	  $Actief_db = $zdw['actief']; }


if(isset($fldAdres) && $fldAdres <> $Adres_db) { // isset($fldAdres) is nodig als een inactief ubn weer actief wordt gemaakt en een adres heeft. 

	$updateAdres = "UPDATE tblUbn SET adres = ". db_null_input($fldAdres) ." WHERE ubnId = '".mysqli_real_escape_string($db,$recId)."' ";
		mysqli_query($db,$updateAdres) or die (mysqli_error($db));
}

if(isset($fldPlaats) && $fldPlaats <> $Plaats_db) {

	$updatePlaats = "UPDATE tblUbn SET plaats = ". db_null_input($fldPlaats) ." WHERE ubnId = '".mysqli_real_escape_string($db,$recId)."' ";
		mysqli_query($db,$updatePlaats) or die (mysqli_error($db));
}

if($fldActief <> $Actief_db) {

	$updateUbn = "UPDATE tblUbn SET actief = '". mysqli_real_escape_string($db,$fldActief) ."' WHERE ubnId = '".mysqli_real_escape_string($db,$recId)."' ";
		mysqli_query($db,$updateUbn) or die (mysqli_error($db));
}

if(isset($fldDelete)) {

	$deleteUbn = "DELETE FROM tblUbn WHERE ubnId = '".mysqli_real_escape_string($db,$recId)."' ";
		mysqli_query($db,$deleteUbn) or die (mysqli_error($db));
}

	
	
	} // Einde if($recId > 0)

} // Einde foreach($multip_array as $recId => $id)

?>