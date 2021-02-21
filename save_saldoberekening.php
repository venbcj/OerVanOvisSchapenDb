<!-- 15-11-2015 : gemaakt -->

<?php
/* toegepast in :
	- Ras.php */
	
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


 /*foreach($id as $key => $value) {*/


/*if($key == 'txtId') {*/
foreach($id as $key => $value) {

	//if ($key == 'txtId' ) { $updId = $value; /*echo $key.'='.$value."<br/>";*/}    

    if ($key == 'txtElem' && !empty($value)) {  $fldElem = str_replace(',', '.', $value);}
	else if ($key == 'txtElem' && empty($value)) { $fldElem = 'NULL'; }
	
    if ($key == 'txtRubriek' && !empty($value)) {  $fldRub = str_replace(',', '.', $value);} 
	else if ($key == 'txtRubriek' && empty($value)) { $fldRub = 'NULL'; }
  	
	if ($key == 'txtRubat' && !empty($value) && $value > 0) {  $fldRubat = str_replace(',', '.', $value); /*echo $key.'='.$value."<br/>";*/ } 
	else if ($key == 'txtRubat' && (empty($value) || $value == 0)) { $fldRubat = 'NULL'; }
	 

	
}
/*
 echo $updId."<br/>";
					echo $fldUitv."<br/>";
					echo $fldPil."<br/>";*/
if(isset($fldElem)) { //$fldElem kan niet bestaan als alle componenten niet actief zijn of niet voor saldoberekening zijn aangevinkt 					
	$update_tblSalber = "update tblSalber set waarde = ".mysqli_real_escape_string($db,$fldElem)." WHERE salbId = ".mysqli_real_escape_string($db,$recId)." ";
/*echo $update_tblSalber.'<br>';*/		mysqli_query($db,$update_tblSalber) or die (mysqli_error($db));  
 }
 
if(isset($fldRub)) { //$fldRub kan niet bestaan als alle rubrieken niet actief zijn of niet voor saldoberekening zijn aangevinkt 					
	$update_tblSalber = "update tblSalber set waarde = ".mysqli_real_escape_string($db,$fldRub)." WHERE salbId = ".mysqli_real_escape_string($db,$recId)." ";
/*echo $update_tblSalber.'<br>';*/		mysqli_query($db,$update_tblSalber) or die (mysqli_error($db));  
 } 

if(isset($fldRubat)) { //$fldRubat kan niet bestaan als alle rubrieken niet actief zijn of niet voor saldoberekening zijn aangevinkt 					
	$update_tblSalber = "update tblSalber set aantal = ".mysqli_real_escape_string($db,$fldRubat)." WHERE salbId = ".mysqli_real_escape_string($db,$recId)." ";
/*echo $update_tblSalber.'<br>';*/		mysqli_query($db,$update_tblSalber) or die (mysqli_error($db));  
 unset($fldRubat); } 
 

#}




	
	
						#}
}
?>
					
	