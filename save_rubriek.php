<!-- 23-10-2015 : gemaakt -->
<?php
/* toegepast in :
	- Componenten.php */
	


foreach($_POST as $fldname => $fldvalue) {  //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    
    $multip_array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;  // Opbouwen van een Multidimensional array met 2 indexen. [Id] [naamveld] en een waarde nl. de veldwaarde. 
}
foreach($multip_array as $recId => $id) {  
unset($fldActief); 
unset($fldSalber);

#echo '<br>'.'$recId = '.$recId.'<br>';

if(!empty($recId)) {


foreach($id as $key => $value) {

    if ($key == 'chkActief' ) {  $fldActief = $value;  }
	 
	if ($key == 'chkSalber' ) {  $fldSalber = $value; }
	else { $fldSalber = 0; }

	
}

$Update_Rubriek = "
UPDATE tblRubriekuser
SET actief = '". mysqli_real_escape_string($db,$fldActief) ."', sal = '". mysqli_real_escape_string($db,$fldSalber) ."'
WHERE rubuId = '".mysqli_real_escape_string($db,$recId)."' ";

		mysqli_query($db,$Update_Rubriek) or die (mysqli_error($db));

//echo $Update_Rubriek.'<br>';

}


	
						
}
?>
					
	
