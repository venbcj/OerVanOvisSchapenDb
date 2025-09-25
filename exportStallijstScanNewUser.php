<?php 
//13-8-2023 : Bestand gemaakt. Bron : https://www.codexworld.com/export-data-to-excel-in-php/

// Laad het databaseconfiguratiebestand 
include_once 'connect_db.php'; 
 
// Inclusief XLSX-generatorbibliotheek 
require_once 'PhpXlsxGenerator.php'; 
 
$lidId = $_GET['pst'];

// Excel-bestandsnaam om te downloaden 
$fileName = "StallijstScan_" . date('Y-m-d') . ".xlsx"; 
 
// Definieer kolomnamen  
$excelData[] = array('Datum', 'Levensnummer', 'ras', 'geslacht', 'Verwerkt'); 
 
// Haal records op uit de database en sla ze op in een array 
$query = $db->query("
SELECT date_format(rd.datum,'%d-%m-%Y') datum, rd.levensnummer levnr_rd, r.ras ras_rd, rd.geslacht geslacht_rd, verwerkt
FROM impAgrident rd
 join tblRas r on (r.rasId = rd.rasId)
WHERE actId = 21 and lidId = '" . mysqli_real_escape_string($db,$lidId) . "'
"); 
if($query->num_rows > 0){ 
    while($row = $query->fetch_assoc()){ 
        //$status = ($row['verwerkt'] == 1)?'Active':'Inactive'; 
        $datum = $row['datum']; 
        $levnr = $row['levnr_rd'];
        $ras = $row['ras_rd'];
        $geslacht = $row['geslacht_rd'];
        $verwerkt = ($row['verwerkt'] == 1)?'Ja':'Nee';


        $lineData  = array( $datum, $levnr, $ras, $geslacht, $verwerkt ); 
        $excelData[] = $lineData; 
    } 
} 
 
// Gegevens exporteren naar Excel en downloaden als xlsx-bestand 
$xlsx = CodexWorld\PhpXlsxGenerator::fromArray( $excelData ); 
$xlsx->downloadAs($fileName);

exit; 
 
?>
