<?php


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
foreach($multip_array as $id) {  


 foreach($id as $key => $value) { 


if($key == 'txtId') {
foreach($id as $key => $value) {

	     if ($key == 'txtId' ) { $updId = $value; /*echo $key.'='.$value."<br/>";*/}    

	if ($key == 'txtDekat' && !empty($value) ) { $flddekat = $value; } else if ($key == 'txtDekat' && empty($value)) {  $flddekat = 'NULL' ; }
    if ($key == 'ctrDekat' ) { $ctrdekat = $value; }

    if ($key == 'txtWerpat' && !empty($value) ) { $fldwerpat = $value; }  else if ($key == 'txtWerpat' && empty($value)) {  $fldwerpat = 'NULL' ; }
    if ($key == 'ctrWerpat' ) { $ctrwerpat = $value; }
}
/*
echo $updId."<br/>";
echo $flddekat."<br/>";*/
// Bijwerken dekaantal
if(isset($flddekat) && $flddekat <> $ctrdekat) {
	$update_Dekat = "UPDATE tblDeklijst SET dekat = ".$flddekat." WHERE dekId = '$updId' ";
		mysqli_query($db,$update_Dekat) or die (mysqli_error($db));
 }

 
// Bijwerken dekaantal
if(isset($fldwerpat) && $fldwerpat <> $ctrwerpat) {
	$update_Werpat = "UPDATE tblDeklijst SET werpat = ".$fldwerpat." WHERE dekId = '$updId' ";
		mysqli_query($db,$update_Werpat) or die (mysqli_error($db));
 }
 
}
}
}
?>
					
	