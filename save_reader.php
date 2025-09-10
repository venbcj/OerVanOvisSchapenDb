<!-- 11-8-2014 : veld type gewijzigd in fase
 16-11-2014 include maak_request toegevoegd -->

<?php
/* post_readerGeb.php toegepast in :
	- InsGeboortes.php */
	
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

	     if ($key == 'txtId' ) { $updId = $value; }    

    if ($key == 'txtDatum' ) { $dag = date_create($value); $flddag =  date_format($dag, 'Y-m-d'); 
                                      }

    if ($key == 'txtLevnr' && !empty($value)) {  $fldLevnr = $value; }    
     else if ($key == 'txtLevnr' && empty($value)) {  $fldLevnr= 'NULL' ; }
    
    if ($key == 'txtNaam' && !empty($value)) {  $fldNaam = $value; }
     else if ($key == 'txtNaam' && empty($value)) {  $fldNaam = 'NULL' ; }
	 
    if ($key == 'kzlRas' && !empty($value)) {  $RasId = $value; }
     else if ($key == 'kzlRas' && empty($value)) {  $RasId = 'NULL' ; }
	
}
	
echo $updId."<br/>";
echo $flddag."<br/>";
echo $fldLevnr."<br/>";
echo $RasId."<br/>";
}




								

						
    






					
	
	
						}
}
?>
					
	
