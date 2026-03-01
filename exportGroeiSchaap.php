<?php 
/*04-09-2023 : Bestand gekopieerd van exportStallijstScanNewUser.php.
 10-03-2024 : Filter op worp periode toegevoegd in Groeiresultaat.php en filter meenemen naar Excel gemaakt 
 06-10-2024 : Bestandsnaam Excel hernoemd   */


// Laad het databaseconfiguratiebestand 
include_once 'connect_db.php'; 
 
// Inclusief XLSX-generatorbibliotheek 
require_once 'PhpXlsxGenerator.php'; 

$lidId = $_GET['pst'];
$where = $_GET['where'];

// Bepalen aantal karakters werknr 
$result = mysqli_query ($db,"SELECT kar_werknr FROM tblLeden WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."';") or die (mysqli_error($db));
    while ($row = mysqli_fetch_assoc($result))
        { $Karwerk = $row['kar_werknr']; }
        
// Excel-bestandsnaam om te downloaden 
$fileName = "GroeiSchaap_" . date('Y-m-d') . ".xlsx"; 
 
// Definieer kolomnamen  
$excelData[] = array('Moeder', 'Levensnummer', 'Werknr', 'Geslacht', 'Generatie', 'Gewicht', 'Datum', 'Actie','Gem groei per dag');
 
// Haal records op uit de database en sla ze op in een array 
$query = $db->query("
SELECT right(mdr.levensnummer, $Karwerk) moeder, s.levensnummer, right(s.levensnummer, $Karwerk) werknum, s.geslacht, prnt.datum aanw, h.kg, h.datum date, date_format(h.datum,'%d-%m-%Y') datum, a.actie
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (st.stalId = h.stalId) 
  join tblActie a on (h.actId = a.actId)
 left join (
    SELECT st.schaapId, datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 1 and h.skip = 0
 ) hg on (hg.schaapId = s.schaapId)
 left join (
    SELECT st.schaapId, datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId)
 left join tblVolwas v on (v.volwId = s.volwId)
 left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(st.rel_best) and h.kg is not null and h.skip = 0
 " . $where . "
ORDER BY right(mdr.levensnummer, $Karwerk), right(s.levensnummer, $Karwerk), h.hisId
"); 
if($query->num_rows > 0){ 
    while($row = $query->fetch_assoc()){ 

$levnr_vorig = $levnr;

 $levnr = $row['levensnummer']; if($levnr_vorig == $levnr) { $levnr_nu = ''; } else { $levnr_nu = $levnr; unset($kg1); unset($date1); unset($actie1); }
    $moeder = $row['moeder'];
    $werknr = $row['werknum'];
    $geslacht = $row['geslacht']; 
    $aanw = $row['aanw']; 
    $kg = $row['kg'];              if(!isset($kg1)) { $kg1 = $kg; }
    $date = $row['date'];          if(!isset($date1)) { $date1 = $date; }
    $datum = $row['datum'];        
    $actie = $row['actie'];        if(!isset($actie1)) { $actie1 = $actie; }
    if(isset($aanw)) {if($geslacht == 'ooi') { $fase = 'moeder'; } else if($geslacht == 'ram') { $fase = 'vader'; } } else {$fase = 'lam'; } 

$date_1 = strtotime($date1); //time(); // or your date as well
$date_2 = strtotime($date);
$datediff = $date_2 - $date_1;

$dagen = round($datediff / (60 * 60 * 24));

if($dagen > 0) { $groei = round((($kg - $kg1) / $dagen),2).' kg in '.$dagen.' dagen vanaf '.strtolower($actie1); } else { unset($groei); }

        $lineData  = array($moeder, $levnr_nu, $werknr, $geslacht, $fase, $kg, $datum, $actie, $groei); 
        $excelData[] = $lineData; 
    } 
} 
 
// Gegevens exporteren naar Excel en downloaden als xlsx-bestand 
$xlsx = CodexWorld\PhpXlsxGenerator::fromArray( $excelData ); 
$xlsx->downloadAs($fileName);
