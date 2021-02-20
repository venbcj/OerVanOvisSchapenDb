<?php /*15-11-2020 bestand gekopieerd van impGroepsgeboorte.php en diverse imp... bestanden teruggebracht naar dit ene bestand 
 */


$cnt_velden = count($velden);

			 foreach($inhoud as $index => $waarde) {			 	
			
//var_dump($waarde);		
// Inlezen record
for($h = 0; $h < $cnt_velden; $h++) { // Er zijn 3 elementen

	if($h == 0) { $insert_qry = " INSERT INTO impAgrident SET "; }
	


if($waarde -> {$velden[$h]} == "" || $waarde -> {$velden[$h]} == "0") {  $insert_qry .= "$velden[$h] = NULL, "; }
else { $insert_qry .= "$velden[$h] = '" . mysqli_real_escape_string($db, $waarde -> {$velden[$h]} ) . "', "; }


} // for($h = 0;

$insert_qry .= ' lidId = ' . mysqli_real_escape_string($db,$lidid) . ';';

echo $insert_qry; mysqli_query($db,$insert_qry) or die (mysqli_error($db));

unset($insert_qry);
// Einde Inlezen record

			 } // Einde foreach($data .....

?>