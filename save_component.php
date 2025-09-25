<!-- 23-10-2015 : gemaakt 
29-12-2023 sql beveiligd 
07-03-2025 Hidden velden in Componenten.php verwijderd en hier lege checkboxen gedefinieerd -->

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

foreach($multip_array as $recId => $id) {  
//echo '<br>'.'$recId = '.$recId.'<br>';

unset($fldWaarde);
unset($fldActief);
unset($fldSalber);

foreach($id as $key => $value) {  

    if ($key == 'txtWaarde' && !empty($value)) {  $fldWaarde = $value; }    
     /*else if ($key == 'txtWaarde' && empty($value)) {  $fldWaarde= 'NULL' ; }*/
    
    if ($key == 'chkActief') {  $fldActief = $value; }
	
	if ($key == 'chkSalber') {  $fldSalber = $value; }
	 

	
}

if(!isset($fldActief)) {  $fldActief = '0' ; }
if(!isset($fldSalber)) {  $fldSalber = '0' ; }
/*
echo $fldWaarde."<br/>";
echo $fldActief."<br/>";*/

if($recId > 0) {

$Update_Element = "
UPDATE tblElementuser
SET waarde = ".db_null_input($fldWaarde).", actief = '".mysqli_real_escape_string($db,$fldActief)."', sal = '".mysqli_real_escape_string($db,$fldSalber)."'
WHERE elemuId = '".mysqli_real_escape_string($db,$recId)."' ";
/*echo $Update_Element.'<br>';*/		mysqli_query($db,$Update_Element) or die (mysqli_error($db));


} 

} ?>
					
	