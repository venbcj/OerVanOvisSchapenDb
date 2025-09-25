<?php 
/*06-10-2024 : Bestand gekopieerd van exportGroeiSchaap.php. */


// Laad het databaseconfiguratiebestand 
include_once 'connect_db.php'; 
 
// Inclusief XLSX-generatorbibliotheek 
require_once 'PhpXlsxGenerator.php'; 

$lidId = $_GET['pst'];
$kolomkopxls = $_GET['show']; 
$where = $_GET['where'];

// Bepalen aantal karakters werknr
$result = mysqli_query ($db,"SELECT kar_werknr FROM tblLeden WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."';") or die (mysqli_error($db));
    while ($row = mysqli_fetch_assoc($result))
        { $Karwerk = $row['kar_werknr']; }
        
// Excel-bestandsnaam om te downloaden 
$fileName = "GroeiWeging_" . date('Y-m-d') . ".xlsx"; 
 
// Definieer kolomnamen  
if($kolomkopxls == 'T') { $kolomkop = 'Totale groei'; }
else if($kolomkopxls == 'G') { $kolomkop = 'Gem groei per dag'; }

$excelData[] = array('Datum', 'Actie', 'Moeder', 'Werknr', 'Geslacht', 'Generatie', 'Gewicht', $kolomkop);
 
// Haal records op uit de database en sla ze op in een array 
$query = $db->query("
SELECT date_format(h.datum,'%d-%m-%Y') datum, h.datum date, a.actie, right(mdr.levensnummer, $Karwerk) moeder, s.schaapId, right(s.levensnummer, $Karwerk) werknum, s.geslacht, prnt.datum aanw, h.kg
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (st.stalId = h.stalId) 
 join tblActie a on (h.actId = a.actId)
 left join (
    SELECT st.schaapId, datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId) 
 left join tblVolwas v on (v.volwId = s.volwId)
 left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(st.rel_best) and h.kg is not null and h.skip = 0 ".$where. "
ORDER BY h.datum desc, h.actId, right(mdr.levensnummer, $Karwerk), right(s.levensnummer, $Karwerk), h.hisId
"); 
if($query->num_rows > 0){ 
    while($row = $query->fetch_assoc()){ 

$levnr_vorig = $levnr;

 $levnr = $row['levensnummer']; if($levnr_vorig == $levnr) { $levnr_nu = ''; } else { $levnr_nu = $levnr; unset($kg1); unset($date1); unset($actie1); }
    $date = $row['date'];          if(!isset($date1)) { $date1 = $date; }
    $datum = $row['datum']; 
    $actie = $row['actie'];        if(!isset($actie1)) { $actie1 = $actie; }
    $moeder = $row['moeder'];
    $schaapId = $row['schaapId'];
    $werknr = $row['werknum'];
    $geslacht = $row['geslacht']; 
    $aanw = $row['aanw']; 
    $kg = $row['kg'];              if(!isset($kg1)) { $kg1 = $kg; }       

    if(isset($aanw)) { if($geslacht == 'ooi') { $fase = 'moeder'; } else if($geslacht == 'ram') { $fase = 'vader'; } } else {$fase = 'lam'; } 

$date_2 = strtotime($date); // Betreft $datum_toon

// Zoek vorige weging
unset($vorige_weging);

$zoek_vorige_weging = mysqli_query($db,"
SELECT max(hisId) vorige_weging
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
WHERE st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and h.datum < '".mysqli_real_escape_string($db,$date)."' and h.kg is not null
") or die (mysqli_error($db));

while($zvw = mysqli_fetch_array($zoek_vorige_weging))
        { $vorige_weging = $zvw['vorige_weging']; }

if(isset($vorige_weging)) { 


$zoek_actie_vorige_weging = mysqli_query($db,"
SELECT h.actId, actie, h.datum, kg
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
 join tblActie a on (h.actId = a.actId)
WHERE h.hisId = '".mysqli_real_escape_string($db,$vorige_weging)."'
") or die (mysqli_error($db));

while($zavw = mysqli_fetch_array($zoek_actie_vorige_weging))
        { $vorige_actId = $zavw['actId']; 
          $vorige_actie = $zavw['actie']; if($vorige_actId == 9) { $vorige_actie = 'vorige tussenweging'; }
          $vorige_date = $zavw['datum']; 
          $vorige_kg = $zavw['kg']; }
}

$date_1 = strtotime($vorige_date); //time(); // or your date as well. Betreft vorige weegdatum
$datediff = $date_2 - $date_1;

$dagen = round($datediff / (60 * 60 * 24));

if($kolomkopxls == 'T') { $factor = $dagen/$dagen; }
else if($kolomkopxls == 'G') { $factor = $dagen; }

if(isset($vorige_weging)) { $berekening = round((($kg - $vorige_kg) / $factor),2).' kg in '.$dagen.' dagen vanaf '.strtolower($vorige_actie); }

// Einde Zoek vorige weging

        $lineData  = array($datum, $actie, $moeder, $werknr, $geslacht, $fase, $kg, $berekening); 
        $excelData[] = $lineData; 
    } 
} 
 
// Gegevens exporteren naar Excel en downloaden als xlsx-bestand 
$xlsx = CodexWorld\PhpXlsxGenerator::fromArray( $excelData ); 
$xlsx->downloadAs($fileName);

exit; 
 
?>
