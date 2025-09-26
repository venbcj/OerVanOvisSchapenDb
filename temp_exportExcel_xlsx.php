<?php 
//13-8-2023 : Bestand gemaakt. Bron : https://www.codexworld.com/export-data-to-excel-in-php/

// Laad het databaseconfiguratiebestand 
include_once 'connect_db.php'; 
 
// Inclusief XLSX-generatorbibliotheek 
require_once 'PhpXlsxGenerator.php'; 
 
// Excel-bestandsnaam om te downloaden 
$fileName = "members-data_" . date('Y-m-d') . ".xlsx"; 
 
// Definieer kolomnamen 
//$excelData [] = array( 'ID' , 'FIRST NAME' , 'LAST NAME' , 'EMAIL' , 'GENDER' , 'COUNTRY' , ); 
$excelData[] = array('Datum', 'Levensnummer', 'ras', 'geslacht'); 
 
// Haal records op uit de database en sla ze op in een array 
$query = $db->query("
SELECT date_format(rd.datum,'%d-%m-%Y') datum, rd.levensnummer levnr_rd, r.ras ras_rd, rd.geslacht geslacht_rd
FROM impAgrident rd
 join tblRas r on (r.rasId = rd.rasId)
WHERE actId = 21 and lidId = 21
"); 
if($query->num_rows > 0){ 
    while($row = $query->fetch_assoc()){ 
        //$status = ($row['verwerkt'] == 1)?'Active':'Inactive'; 
        $datum = $row['datum']; 
        $levnr = $row['levnr_rd'];
        $ras = $row['ras_rd'];
        $geslacht = $row['geslacht_rd'];


        $lineData  = array( $datum, $levnr, $ras, $geslacht ); 
        $excelData[] = $lineData; 
    } 
} 
 
// Gegevens exporteren naar Excel en downloaden als xlsx-bestand 
$xlsx = CodexWorld\PhpXlsxGenerator::fromArray( $excelData ); 
$xlsx->downloadAs($fileName);

exit; 
 
?>
