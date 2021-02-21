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

    if ($key == 'chbLiq') {  $fldLiq = $value; /*echo $key.'='.$value."<br/>";*/}
    if ($key == 'ctrLiq') {  $ctrLiq = $value; /*echo $key.'='.$value."<br/>";*/}
	
    if ($key == 'kzlRubr') {  $fldRubr = $value; /*echo $key.'='.$value."<br/>"; */ }	
    if ($key == 'txtDatum') {  $fldDatum = $value; /*echo $key.'='.$value."<br/>"; */ $date = date_create($value); $fldDate = date_format($date, 'Y-m-d'); } 	
    if ($key == 'txtBedrag') {  $fldBedrag = $value; /*echo $key.'='.$value."<br/>"; */ }  	
    if ($key == 'txtToel') {  $fldToel = $value; /*echo $key.'='.$value."<br/>"; */ }  	 
	
    if ($key == 'chbArch') {  $fldArch = $value; /*echo $key.'='.$value."<br/>"; */ }  	   	 
    if ($key == 'chbDel') {  $fldDel = $value; /*echo $key.'='.$value."<br/>"; */ }  	 

	 

	
}


if($fldLiq <> $ctrLiq) { 
	$query_aanuit = "UPDATE tblOpgaaf SET liq = '$fldLiq' WHERE opgId = '$updId' "; 
		mysqli_query($db,$query_aanuit) or die (mysqli_error($db)); }
 
if($fldLiq == 1) { /*echo $fldRubr."<br/>";
					echo $fldDate."<br/>";
					echo $fldBedrag."<br/>";
					echo $fldToel."<br/>";*/
					
	$update_tblOpgaaf = "UPDATE tblOpgaaf SET rubuId = '$fldRubr', datum = '$fldDate', bedrag = '$fldBedrag', toel = '$fldToel' WHERE opgId = '$updId' "; 
		mysqli_query($db,$update_tblOpgaaf) or die (mysqli_error($db));   
 } 


if($fldArch == 1) {


$update_record = "UPDATE tblOpgaaf SET his = 1 WHERE opgId = '$updId' ";
		mysqli_query($db,$update_record) or die (mysqli_error($db));	
		}


if($fldDel == 1) {
$Delete_record = "DELETE FROM tblOpgaaf WHERE opgId = '$updId' ";
		mysqli_query($db,$Delete_record) or die (mysqli_error($db));	}

}

//}




								

						
    






					
	
	
						}
}
?>
					
	