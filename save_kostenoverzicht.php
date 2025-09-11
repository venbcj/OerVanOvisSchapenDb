<?php /* 07-01-2025 : gemaakt */



foreach($_POST as $fldname => $fldvalue) {  //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    
    $multip_array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;  // Opbouwen van een Multidimensional array met 2 indexen. [Id] [naamveld] en een waarde nl. de veldwaarde. 
}

foreach($multip_array as $recId => $id) {
#echo '<br>'.'$recId = '.$recId.'<br>'; #/#

unset($fldTerug);
   
 foreach($id as $key => $value) {

 	//$fldLiq = 0;
    if ($key == 'chbTerug') {  $fldTerug = $value; /*echo $key.'='.$value."<br/>";*/ }  

}

//if(!isset($fldTerug)) { $fldTerug = 0; }

if(isset($fldTerug)) {

$update_tblOpgaaf = "UPDATE tblOpgaaf SET his = NULL WHERE opgId = '".mysqli_real_escape_string($db,$recId)."' ";
		//echo '$update_tblOpgaaf = '.$update_tblOpgaaf.'<br>';
		mysqli_query($db,$update_tblOpgaaf) or die (mysqli_error($db));	
		}

}
?>
					
	