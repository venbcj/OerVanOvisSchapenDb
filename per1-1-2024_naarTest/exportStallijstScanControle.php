<?php 
/* 13-8-2023 : Bestand gemaakt. Bron : https://www.codexworld.com/export-data-to-excel-in-php/
30-12-2023 and h.skip = 0 toegevoegd aan tblHistorie */

// Laad het databaseconfiguratiebestand 
include_once 'connect_db.php'; 
 
// Inclusief XLSX-generatorbibliotheek 
require_once 'PhpXlsxGenerator.php'; 

$lidId = $_GET['pst'];
 
// Excel-bestandsnaam om te downloaden 
$fileName = "StallijstScan_" . date('Y-m-d') . ".xlsx"; 
 
// Definieer kolomnamen  
$excelData[] = array('Datum', 'Levensnummer', 'geboren', 'geslacht', 'Verwerkt'); 

$zoek_laatste_controle = mysqli_query($db,"
SELECT max(datum) date
FROM impAgrident
WHERE actId = 22 and lidId = '" . mysqli_real_escape_string($db,$lidId) . "'
") or die (mysqli_error($db));

    while ( $zlc = mysqli_fetch_assoc($zoek_laatste_controle)) { $date = $zlc['date']; }
 
// Haal records op uit de database en sla ze op in een array 
$query = $db->query("
SELECT date_format(rd.datum,'%d-%m-%Y') datum, rd.levensnummer levnr_rd, gebdatum, s.geslacht geslacht, verwerkt
FROM impAgrident rd
 left join tblSchaap s on (s.levensnummer = rd.levensnummer)
 left join (
 	SELECT st.schaapId, date_format(h.datum,'%d-%m-%Y') gebdatum
 	FROM tblHistorie h
 	 join tblStal st on (h.stalId = st.stalId)
 	WHERE actId = 1 and h.skip = 0
 ) geb on (geb.schaapId = s.schaapId)
WHERE actId = 22 and lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and rd.datum = '" . mysqli_real_escape_string($db,$date) . "'
"); 
if($query->num_rows > 0){ 
    while($row = $query->fetch_assoc()){ 
        //$status = ($row['verwerkt'] == 1)?'Active':'Inactive'; 
        $datum = $row['datum']; 
        $levnr = $row['levnr_rd'];
        $gebdatum = $row['gebdatum'];
        $geslacht = $row['geslacht_rd'];
        $verwerkt = ($row['verwerkt'] == 1)?'Ja':'Nee';


        $lineData  = array( $datum, $levnr, $gebdatum, $geslacht, $verwerkt ); 
        $excelData[] = $lineData; 
    } 
} 
 
// Gegevens exporteren naar Excel en downloaden als xlsx-bestand 
$xlsx = CodexWorld\PhpXlsxGenerator::fromArray( $excelData ); 
$xlsx->downloadAs($fileName);

exit; 
 
?>
