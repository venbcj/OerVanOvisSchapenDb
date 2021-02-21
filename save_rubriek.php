<!-- 23-10-2015 : gemaakt -->

<?php
/* toegepast in :
	- Componenten.php */
	
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

    if ($key == 'chkActief' && !empty($value)) {  $fldActief = $value; }
     else if ($key == 'chkActief' && empty($value)) {  $fldActief = '0' ; }
	 
	if ($key == 'chkSalber' /*&& !empty($value)*/) {  $fldSalber = $value; }
     /*else if ($key == 'chkSalber' && empty($value)) {  $fldSalber = '0' ; }*/
	 

	
}
/*
echo $updId."<br/>";
echo $fldActief."<br/>";*/

$Update_Rubriek = "
update tblRubriekuser
set actief = '$fldActief', sal = ".$fldSalber."
where rubuId = '$updId' ";
		mysqli_query($db,$Update_Rubriek) or die (mysqli_error($db));

}




								

						
    






					
	
	
						}
}
?>
					
	