<!-- 23-10-2015 : gemaakt 
5-11-2016 : multip_array array uitgebreid met maandnr en typeveld. Dit heeft geleid naar 1 update statament i.p.v. 12
		Ook controlevelden verwijderd (dus ctrJan, ctrFeb enz.... ) 
14-2-2021 : Komma vervangen door punt. SQL in hoofdletters en beveiligd met quotes 
11-03-2025 : $multip_array met 4 indexen teruggebracht naar 3 indexen. wel of niet hidden veld is verwijderd als index. Een hidden veld wordt hier niet meer uitgesloten. In liquiditeit.php wordt het hidden veld txtM niet meer getoond dus bestaat txtM hier niet meer -->

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

function getMndFromKey($string) {
    $split_mnd = explode('_', $string); 
    return $split_mnd[2];
}

foreach($_POST as $fldname => $fldvalue) {  //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array

    $multip_array[getMndFromKey($fldname)][getIdFromKey($fldname)][getNaamFromKey($fldname)] = $fldvalue;  // Opbouwen van een Multidimensional array met 3 indexen. [$i] [Id] [naamveld] en een waarde nl. de veldwaarde. 
}
foreach($multip_array as $mnd => $id) {
// Mndnr ophalen
//echo '=============== Maand = '.$mnd.'<br>'; 
// Einde Mndnr ophalen

foreach($id as $rubuId => $id) {
// rubuId ophalen
//echo 'rubuId = '.$rubuId.'<br>'; 
// Einde rubuId ophalen

 foreach($id as $key => $value) {

unset($fldBedrag);
    if ($key == 'txtM' && !empty($value)) 	{  $fldBedrag = str_replace(',', '.', $value); }

$zoek_bedrag = mysqli_query($db,"
SELECT bedrag
FROM tblLiquiditeit
WHERE rubuId = '" . mysqli_real_escape_string($db,$rubuId) . "' and month(datum) = '" . mysqli_real_escape_string($db,$mnd) . "' and year(datum) = '" . mysqli_real_escape_string($db,$toon_jaar) . "'
")	or die (mysqli_error($db));
	while ($bdr = mysqli_fetch_assoc($zoek_bedrag)) { 
		$bedrag = $bdr['bedrag']; } 

		//if(!isset($bedrag)) { $bedrag = ''; }

if($fldBedrag <> $bedrag) { 
$Update_Liquiditeit_Bedrag = "UPDATE tblLiquiditeit SET bedrag = " . db_null_input($fldBedrag) . " WHERE rubuId = '" . mysqli_real_escape_string($db,$rubuId) . "' and month(datum) = '" . mysqli_real_escape_string($db,$mnd) . "' and year(datum) = '" . mysqli_real_escape_string($db,$toon_jaar) . "' ";
/*echo $Update_Liquiditeit_Bedrag.' - '.$mnd.' - '.$bedrag.'<br>';*/
		mysqli_query($db,$Update_Liquiditeit_Bedrag) or die (mysqli_error($db));	
}

	
						}
}
}

?>
					
	