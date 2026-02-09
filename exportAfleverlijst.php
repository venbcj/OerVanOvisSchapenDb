<?php 
/* 19-03-2025 : Bestand gekopieerd van exportStallijst.php. */

// Laad het databaseconfiguratiebestand 
include_once 'connect_db.php'; 
 
// Inclusief XLSX-generatorbibliotheek 
require_once 'PhpXlsxGenerator.php'; 

$lidId = $_GET['pst'];
$relId = $_GET['best'];
$afvDate = $_GET['date'];

// Bepalen aantal karakters werknr 
$result = mysqli_query ($db,"SELECT kar_werknr FROM tblLeden WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."';") or die (mysqli_error($db));
    while ($row = mysqli_fetch_assoc($result))
        { $Karwerk = $row['kar_werknr']; }
        
// Excel-bestandsnaam om te downloaden 
$fileName = "Afleverlijst_" . date('Y-m-d') . ".xlsx"; 
 
// Definieer kolomnamen  
$excelData[] = array('Levensnummer', 'Werknr', 'Gewicht', 'Medicijn', 'Datum toepassing', 'Wachtdagen'); 
 
// Haal records op uit de database en sla ze op in een array 
$query = $db->query("
SELECT st.lidId, s.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk) werknr, h.kg, pil.datum, pil.naam, pil.wdgn_v
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 join tblActie a on (h.actId = a.actId)
 left join (
    SELECT s.schaapId, date_format(h.datum,'%d-%m-%Y') datum, art.naam, art.wdgn_v
    FROM tblSchaap s 
     join tblStal st on (st.schaapId = s.schaapId)
     join tblHistorie h on (h.stalId = st.stalId)
     join tblNuttig n on (h.hisId = n.hisId)
     join tblInkoop i on (i.inkId = n.inkId)
     join tblArtikel art on (i.artId = art.artId) 
    WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = 8 and h.skip = 0 and (h.datum + interval art.wdgn_v day) >= sysdate()
) pil on (st.schaapId = pil.schaapId)
WHERE h.datum = '".mysqli_real_escape_string($db,$afvDate)."' and st.rel_best = '".mysqli_real_escape_string($db,$relId)."' and a.af = 1 and h.skip = 0
ORDER BY right(s.levensnummer,$Karwerk)
"); 
if($query->num_rows > 0){ 
    while($row = $query->fetch_assoc()){
    $levnr = $row['levensnummer'];
    $werknr = $row['werknr'];
    $gewicht = $row['kg'];
    $medicijn = $row['naam']; 
    $datum_pil = $row['datum']; 
    $wdgn = $row['wdgn_v'];


        $lineData  = array($levnr, $werknr, $gewicht, $medicijn, $datum_pil, $wdgn); 
        $excelData[] = $lineData; 
    } 
} 
 
// Gegevens exporteren naar Excel en downloaden als xlsx-bestand 
$xlsx = CodexWorld\PhpXlsxGenerator::fromArray( $excelData ); 
$xlsx->downloadAs($fileName);

exit; 
 
?>
