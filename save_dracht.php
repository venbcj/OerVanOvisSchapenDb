<?php
/* 21-10-2018 gemaakt 
17-02-2021 : SQL beveiligd met quotes */

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
#echo '<br>'.'$recId = '.$recId.'<br>';

if(!empty($recId)) {

foreach($id as $key => $value) {

	if ($key == 'kzlRamUpd' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $updRam = $value; } else if ($key == 'kzlRamUpd' && empty($value)) { $updRam = ''; }
	if ($key == 'kzlDrachtUpd') /* Leeg is gelijk aan 0 !! */ {  $updDracht = $value; }
	if ($key == 'chbDel') { $delete = 1; }
		
									}
$zoek_dracht_database = mysqli_query($db,"
SELECT vdrId, drachtig
FROM tblVolwas
WHERE volwId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));

while ($dra = mysqli_fetch_assoc($zoek_dracht_database)) {
	$vdr_db = $dra['vdrId'];
	$dracht_db = $dra['drachtig']; // Leeg is gelijk aan 0 !!
}

// Ram wijzigen
if($vdr_db <> $updRam) {

$updateRam = "UPDATE tblVolwas SET vdrId = ".db_null_input($updRam)." WHERE volwId = '".mysqli_real_escape_string($db,$recId)."' ";	
/*echo $updateRam.'<br>';*/	mysqli_query($db,$updateRam) or die (mysqli_error($db));
	
		}

// Drachtig wijzigen
if($dracht_db <> $updDracht) {

$updateDracht = "UPDATE tblVolwas SET drachtig = ".db_null_input($updDracht)." WHERE volwId = '".mysqli_real_escape_string($db,$recId)."' ";	
/*echo $updateDracht.'<br>';*/		mysqli_query($db,$updateDracht) or die (mysqli_error($db));
	
		}

// Dracht verwijderen
if(isset($delete)) {

$delete_dracht = "DELETE FROM tblVolwas WHERE volwId = '".mysqli_real_escape_string($db,$recId). "' " ;
/*echo $delete_dracht.'<br>';*/		mysqli_query($db,$delete_dracht) or die (mysqli_error($db));

unset($delete);
}

	} // Einde if(!empty($recId))

	}

?>
					
	