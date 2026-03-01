<?php 
/*06-10-2024 : Bestand gekopieerd van exportGroeiSchaap.php. */


// Laad het databaseconfiguratiebestand 
include_once 'connect_db.php'; 
 
// Inclusief XLSX-generatorbibliotheek 
require_once 'PhpXlsxGenerator.php'; 

$lidId = $_GET['pst'];
$lid_gateway = new LidGateway();
$Karwerk = $lid_gateway->zoek_karwerk($lidId);
        
// Excel-bestandsnaam om te downloaden 
$fileName = "Inlezen_afvoer_" . date('Y-m-d') . ".xlsx";
 
// Definieer kolomnamen 

$excelData[] = array('Afvoerdatum', 'Werknr', 'Levensnummer', 'Gewicht', 'Bestemming', 'Reden', 'Generatie', 'Rest_wachtdagen');

// Haal records op uit de database en sla ze op in een array 
$query = $db->query("
SELECT rd.datum, right(rd.levensnummer,".mysqli_real_escape_string($db,$Karwerk).") werknr, rd.levensnummer levnr, rd.gewicht kg, p.naam bestemming, r.reden, s.schaapId, s.geslacht,
    lower(haf.actie) actie, ouder.datum dmaanw,
    date_format(max.datummax_afv,'%d-%m-%Y') maxdatum_afv, max.datummax_afv, date_format(max.datummax_kg,'%d-%m-%Y') maxdatum_kg, max.datummax_kg
FROM impAgrident rd
 left join (
    SELECT s.schaapId, s.levensnummer, s.geslacht
     FROM tblSchaap s
      join tblStal st on (st.schaapId = s.schaapId)
     WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
     GROUP BY s.schaapId, s.levensnummer, s.geslacht
 ) s on (s.levensnummer = rd.levensnummer)
 left join (
    SELECT st.schaapId, h.hisId, a.actie, a.af
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (h.actId = a.actId)
    WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a.af = 1 and h.skip = 0
 ) haf on (s.schaapId = haf.schaapId)
 left join (
    SELECT st.schaapId, h.datum
     FROM tblStal st
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE h.actId = 3 and h.skip = 0
 ) ouder on (ouder.schaapId = s.schaapId)
 left join (
    SELECT p.lidId, p.ubn, p.naam
    FROM tblPartij p
     join tblRelatie r on (p.partId = r.partId)
    WHERE p.actief = 1 and r.relatie = 'deb' and r.actief = 1
 ) p on(p.ubn = rd.ubn and p.lidId = rd.lidId)
 left join tblReden r on (r.redId = rd.reden)
 left join (
    SELECT schaapId, max(datum) datummax_afv, max(datum_kg) datummax_kg
    FROM (
        SELECT s.schaapId, h.datum, h.datum datum_kg, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
        WHERE a.actId = 1 and h.skip = 0 and s.levensnummer is not null

        Union

        SELECT s.schaapId, h.datum, h.datum datum_kg, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
        WHERE a.actId = 2 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'

        Union

        SELECT s.schaapId, h.datum, NULL datum_kg, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
        WHERE (a.actId = 5 or a.actId = 8 or a.actId = 9 or a.actId = 12 or a.actId = 13 or a.actId = 14) and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'

        Union

        SELECT s.schaapId, h.datum, NULL datum_kg, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
         left join 
         (
            SELECT s.schaapId, h.actId, h.datum 
            FROM tblSchaap s
             join tblStal st on (st.schaapId = s.schaapId)
             join tblHistorie h on (h.stalId = st.stalId) 
            WHERE actId = 2 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
         ) koop on (s.schaapId = koop.schaapId and koop.datum <= h.datum)
        WHERE a.actId = 3 and h.skip = 0 and (isnull(koop.datum) or koop.datum < h.datum) and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'

        Union

        SELECT s.schaapId, h.datum, NULL datum_kg, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
        WHERE a.actId = 4 and h.skip = 0

        Union

        SELECT  mdr.schaapId, min(h.datum) datum, NULL datum_kg, 'Eerste worp' actie, NULL, 0 skip
        FROM tblSchaap mdr
         join tblVolwas v on (mdr.schaapId = v.mdrId)
         join tblSchaap lam on (v.volwId = lam.volwId)
         join tblStal st on (st.schaapId = lam.schaapId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 1 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
        GROUP BY mdr.schaapId

        Union

        SELECT mdr.schaapId, max(h.datum) datum, NULL datum_kg, 'Laatste worp' actie, NULL, 0 skip
        FROM tblSchaap mdr
         join tblVolwas v on (mdr.schaapId = v.mdrId)
         join tblSchaap lam on (v.volwId = lam.volwId)
         join tblStal st on (st.schaapId = lam.schaapId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 1 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
        GROUP BY mdr.schaapId, h.actId
        HAVING (max(h.datum) > min(h.datum))

        Union

        SELECT s.schaapId, p.dmafsluit datum, NULL datum_kg, 'Gevoerd' actie, NULL , h.skip
        FROM tblVoeding vd
         join tblPeriode p on (p.periId = vd.periId)
         join tblBezet b on (b.periId = p.periId)
         join tblHistorie h on (h.hisId = b.hisId)
         join tblStal st on (st.stalId = h.stalId)
         join tblSchaap s on (s.schaapId = st.schaapId)
        WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' 
        GROUP BY s.schaapId, p.dmafsluit
    ) sd
    GROUP BY schaapId
 ) max on (s.schaapId = max.schaapId)
WHERE rd.lidId = '".mysqli_real_escape_string($db,$lidId)."' and rd.actId = 12 and isnull(rd.verwerkt) and (p.lidId = '".mysqli_real_escape_string($db,$lidId)."' or isnull(p.lidId))
ORDER BY right(rd.levensnummer,".mysqli_real_escape_string($db,$Karwerk).")

"); 

if($query->num_rows > 0){ 
    while($row = $query->fetch_assoc()){ 

$levnr_vorig = $levnr;

    $schaapId = $row['schaapId'];
    $date = $row['datum'];
    $werknr = $row['werknr'];
    $levnr = $row['levnr'];
    $kg = $row['kg'];
    $bestemming = $row['bestemming'];
    $reden = $row['reden'];
    $dmaanw = $row['dmaanw'];
    $status = $row['actie'];
    $dmmax_bij_afvoer = $row['datummax_afv'];
    $dmmax_bij_wegen = $row['datummax_kg'];
    $maxdm_bij_afvoer = $row['maxdatum_afv'];
    $maxdm_bij_wegen = $row['maxdatum_kg'];

    $geslacht = $row['geslacht']; if(isset($dmaanw)) { if($geslacht == 'ooi') {$fase = 'moeder'; } else if($geslacht == 'ram') {$fase = 'vader'; }} else { $fase = 'lam'; }
     
// Wachtdagen bepalen
if(isset($schaapId)) {
$schaap_gateway = new SchaapGateway();
[ $pildm, $pil, $wdgn_v ] = $schaap_gateway->zoek_pil($date, $lidId, $schaapId);
$vandaag = date('Y-m-d');
}
// Einde Wachtdagen bepalen

    if(!isset($schaapId))                     { $bericht = 'Levensnummer onbekend.'; }
    else if($status == 'overleden' || $status == 'afgeleverd')   { $bericht = "Dit schaap is reeds $status."; } 
    else if(isset($fase) && $date < $dmmax_bij_afvoer)   { $bericht = "Datum ligt voor $maxdm_bij_afvoer."; } 
    else if($date < $dmmax_bij_wegen)   { $bericht = "Datum ligt voor $maxdm_bij_wegen."; } 
    else if($modtech == 1 && !isset($speen) && !isset($aank)) { $bericht = "Dit schaap heeft nog geen speendatum."; }
    else if(isset($wdgn_v)) { $bericht = $pildm.' - '.$pil; } unset($wdgn_v);
    //else if($modtech == 1 && !isset($aank) && !isset($bezet)) { $bericht = "Dit schaap heeft nog geen aankoopdatum."; } 
    unset($status);



        $lineData  = array($date, $werknr, $levnr, $kg, $bestemming, $reden, $fase, $wdgn_v, $bericht); 
        $excelData[] = $lineData; 
    } 
 }
 
// Gegevens exporteren naar Excel en downloaden als xlsx-bestand 
$xlsx = CodexWorld\PhpXlsxGenerator::fromArray( $excelData ); 
$xlsx->downloadAs($fileName);
