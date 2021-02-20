<?php /*27-2-2020 bestand gekopieerd van impGeboortes.php 
8-3-2020 Onderdeel gemaakt van impReaderAgrident.php  
3-7-2020 : Gegevens reader opgeslagen in 1 tabel impAgrident 
26-1-2021 : Transponder toegevoegd 
12-02-2021 : Controle Lambar in Newreader_keuzelijsten.php weggehaald en hier toegevoegd. SQL beveiligd met quotes */

		//$input = $inhoud; // php://input is de rauwe data. nl. het json bestand.

		/*$input = '';*/
		//$data = $input; 
		//var_dump($data) ;
		//var_dump( $data ->glossary->GlossDiv->title) ;
		
		//if(!empty($data)) { echo '$DATA = '; // als $data bestaat

$velden = array('ActId', 'Datum', 'Transponder', 'Levensnummer', 'Reden', 'MoederTransponder', 'Moeder', 'Gewicht', 'HokId' );

$cnt_velden = count($velden);

			 foreach($inhoud as $index => $waarde) {			 	
			
		
// Inlezen record
for($h = 0; $h < $cnt_velden; $h++) { // Er zijn 8 elementen

	if($h == 0) { $insert_qry = " INSERT INTO impAgrident SET "; }
	


if($waarde -> {$velden[$h]} == "" || $waarde -> {$velden[$h]} == "0") {  $insert_qry .= "$velden[$h] = NULL, "; }
else { $insert_qry .= "$velden[$h] = '" . mysqli_real_escape_string($db, $waarde -> {$velden[$h]} ) . "', "; }


} // for($h = 0;

$insert_qry .= ' lidId = ' . mysqli_real_escape_string($db,$lidid) . ';';

echo $insert_qry; mysqli_query($db,$insert_qry) or die (mysqli_error($db));

unset($insert_qry);
// update record t.b.v. verblijf lambar
$zoek_lambar_record = mysqli_query($db,"
SELECT max(Id) Id
FROM impAgrident
WHERE actId = 16 and isnull(hokId) and lidId = '".mysqli_real_escape_string($db,$lidid)."' and isnull(verwerkt)
") or die (mysqli_error($db));
while ($rec = mysqli_fetch_assoc($zoek_lambar_record)) {	$lbarId = $rec['Id'];	}

if(isset($lbarId)) {
$zoek_Lambar = mysqli_query($db,"
SELECT hokId
FROM tblHok
WHERE hoknr = 'Lambar' and lidId = '".mysqli_real_escape_string($db,$lidid)."'
") or die (mysqli_error($db));
while ($h = mysqli_fetch_assoc($zoek_Lambar)) {	$hokId = $h['hokId'];	}

if(!isset($hokId)) {

	$insert_tblHok = "INSERT INTO tblHok set hoknr = 'Lambar', lidId = '".mysqli_real_escape_string($db,$lidid)."' ";
	mysqli_query($db,$insert_tblHok) or die (mysqli_error($db));

	$zoek_Lambar = mysqli_query($db,"
	SELECT hokId
	FROM tblHok
	WHERE hoknr = 'Lambar' and lidId = '".mysqli_real_escape_string($db,$lidid)."'
	") or die (mysqli_error($db));
	while ($h = mysqli_fetch_assoc($zoek_Lambar)) {	$hokId = $h['hokId'];	}
}

$update_impAgrident = "UPDATE impAgrident SET hokId = '".mysqli_real_escape_string($db,$hokId)."' WHERE Id = '".mysqli_real_escape_string($db,$lbarId) ."' ";

mysqli_query($db,$update_impAgrident) or die (mysqli_error($db));
}
// Einde update recorde t.b.v. verblijf lambar
// Einde Inlezen record

			 } // Einde foreach($data .....

?>