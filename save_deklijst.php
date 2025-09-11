<?php
/*29-12-2023 : sql beveiligd 
09-03-2025 : In Deklijst.php veld txtId_Id verwijderd en hier recId gedefinieerd*/


function getNaamFromKey($string) {
    $split_naam = explode('_', $string);
    return $split_naam[0];
}


foreach($_POST as $fldname => $fldvalue) {  //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    
    $multip_array[Url::getIdFromKey($fldname)][getNaamFromKey($fldname)] = $fldvalue;  // Opbouwen van een Multidimensional array met 2 indexen. [Id] [naamveld] en een waarde nl. de veldwaarde. 
}
foreach($multip_array as $recId => $id) {  


unset($flddekat);
unset($fldwerpat);

foreach($id as $key => $value) {  

	if ($key == 'txtDekat') 	{ $flddekat = $value; }
}

if($recId > 0) {

$zoek_prognose_weken = mysqli_query($db,"
SELECT dekat
FROM tblDeklijst 
WHERE dekId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));

	while($zpw = mysqli_fetch_assoc($zoek_prognose_weken))
	{	
		$dekat_db = $zpw['dekat'];
	}

// Bijwerken dekaantal
if($flddekat <> $dekat_db) {
	$update_Dekat = "UPDATE tblDeklijst SET dekat = ".db_null_input($flddekat)." WHERE dekId = '".mysqli_real_escape_string($db,$recId)."' ";
		mysqli_query($db,$update_Dekat) or die (mysqli_error($db));
 }


}
}
?>
					
	
