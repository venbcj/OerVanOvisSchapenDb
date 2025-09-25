<?php 
/* 04-09-2023 : Bestand gekopieerd van exportStallijstScanNewUser.php.
30-12-2023 : and h.skip = 0 toegevoegd aan tblHistorie */

// Laad het databaseconfiguratiebestand 
include_once 'connect_db.php'; 
 
// Inclusief XLSX-generatorbibliotheek 
require_once 'PhpXlsxGenerator.php'; 

$lidId = $_GET['pst'];

// Bepalen aantal karakters werknr 
$result = mysqli_query ($db,"SELECT kar_werknr FROM tblLeden WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."';") or die (mysqli_error($db));
    while ($row = mysqli_fetch_assoc($result))
        { $Karwerk = $row['kar_werknr']; }
        
// Excel-bestandsnaam om te downloaden 
$fileName = "Stallijst_" . date('Y-m-d') . ".xlsx"; 
 
// Definieer kolomnamen  
$excelData[] = array('Werknr', 'Levensnummer', 'Geboren', 'Geslacht', 'Generatie'); 
 
// Haal records op uit de database en sla ze op in een array 
$query = $db->query("
SELECT s.levensnummer, right(s.levensnummer, $Karwerk) werknum, s.transponder, date_format(hg.datum,'%d-%m-%Y') gebdm, s.geslacht, prnt.datum aanw, scan.dag
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 left join tblHistorie hg on (st.stalId = hg.stalId and hg.actId = 1 and hg.skip = 0) 
 left join (
    SELECT st.schaapId, datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId) 
 left join (
    SELECT contr_scan.schaapId, date_format(datum,'%d-%m-%Y') dag
    FROM tblHistorie h
    join (
        SELECT max(hisId) hismx, schaapId
        FROM tblHistorie h
         join tblStal st on (h.stalId = st.stalId)
        WHERE actId = 22 and h.skip = 0 and lidId = '".mysqli_real_escape_string($db,$lidId)."'
        GROUP BY schaapId
    ) contr_scan on (contr_scan.hismx = h.hisId)
 ) scan on (scan.schaapId = s.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(st.rel_best)
ORDER BY right(s.levensnummer, $Karwerk)
"); 
if($query->num_rows > 0){ 
    while($row = $query->fetch_assoc()){ 
    $werknr = $row['werknum'];
    $levnr = $row['levensnummer'];
    $gebdm = $row['gebdm'];
    $geslacht = $row['geslacht']; 
    $aanw = $row['aanw']; 
    $lstScan = $row['dag']; 
    if(isset($aanw)) {if($geslacht == 'ooi') { $fase = 'moeder'; } else if($geslacht == 'ram') { $fase = 'vader'; } } else {$fase = 'lam'; }


        $lineData  = array($werknr, $levnr, $gebdm, $geslacht, $fase); 
        $excelData[] = $lineData; 
    } 
} 
 
// Gegevens exporteren naar Excel en downloaden als xlsx-bestand 
$xlsx = CodexWorld\PhpXlsxGenerator::fromArray( $excelData ); 
$xlsx->downloadAs($fileName);

exit; 
 
?>
