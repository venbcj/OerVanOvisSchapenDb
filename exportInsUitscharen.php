<?php 
/*06-10-2024 : Bestand gekopieerd van exportinsAfvoer.php. */


// Laad het databaseconfiguratiebestand 
include_once 'connect_db.php'; 
 
// Inclusief XLSX-generatorbibliotheek 
require_once 'PhpXlsxGenerator.php'; 

$lidId = $_GET['pst'];

// Bepalen aantal karakters werknr
$result = mysqli_query($db,"SELECT kar_werknr FROM tblLeden WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."';") or die (mysqli_error($db));
    while ($row = mysqli_fetch_assoc($result))
        { $Karwerk = $row['kar_werknr']; }
        
// Excel-bestandsnaam om te downloaden 
$fileName = "Inlezen_uitscharen_" . date('Y-m-d') . ".xlsx";
 
// Definieer kolomnamen 

$excelData[] = array('Uitschaardatum', 'Werknr', 'Levensnummer', 'Bestemming', 'Generatie', 'Rest_wachtdagen');

// Haal records op uit de database en sla ze op in een array 
$query = $db->query("
SELECT rd.Id readId, rd.datum, right(rd.levensnummer,".mysqli_real_escape_string($db,$Karwerk).") werknr, rd.levensnummer levnr, rd.ubn ubn_afv, r.ubn ctrubn, r.naam bestemming, rd.reden redId_rd, s.schaapId, s.geslacht, ouder.datum dmaanw, lower(haf.actie) actie, haf.af, hs.datum dmspeen, ak.datum dmaankoop, date_format(max.datummax_afv,'%d-%m-%Y') maxdatum_afv, max.datummax_afv, b.bezId
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
     WHERE h.actId = 4 and h.skip = 0
 ) hs on (hs.schaapId = s.schaapId)
 left join (
    SELECT st.schaapId, h.datum
     FROM tblStal st
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE h.actId = 3 and h.skip = 0
 ) ouder on (ouder.schaapId = s.schaapId)
 left join (
    SELECT levensnummer, max(datum) datum 
    FROM tblSchaap s
     join tblStal st on (st.schaapId = s.schaapId)
     join tblHistorie h on (h.stalId = st.stalId)
    WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = 2 and h.skip = 0
    GROUP BY levensnummer
 ) ak on (ak.levensnummer = rd.levensnummer)
 left join (
    SELECT schaapId, max(datum) datummax_afv
    FROM (
        SELECT s.schaapId, h.datum, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
        WHERE a.actId = 1 and h.skip = 0 and s.levensnummer is not null

        Union

        SELECT s.schaapId, h.datum, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
        WHERE a.actId = 2 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'

        Union

        SELECT s.schaapId, h.datum, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
        WHERE (a.actId = 5 or a.actId = 8 or a.actId = 9 or a.actId = 12 or a.actId = 13 or a.actId = 14) and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'

        Union

        SELECT s.schaapId, h.datum, a.actie, h.actId, h.skip
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

        SELECT s.schaapId, h.datum, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
        WHERE a.actId = 4 and h.skip = 0

        Union

        SELECT  mdr.schaapId, min(h.datum) datum, 'Eerste worp' actie, NULL, 0 skip
        FROM tblSchaap mdr
         join tblVolwas v on (mdr.schaapId = v.mdrId)
         join tblSchaap lam on (v.volwId = lam.volwId)
         join tblStal st on (st.schaapId = lam.schaapId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 1 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
        GROUP BY mdr.schaapId

        Union

        SELECT mdr.schaapId, max(h.datum) datum, 'Laatste worp' actie, NULL, 0 skip
        FROM tblSchaap mdr
         join tblVolwas v on (mdr.schaapId = v.mdrId)
         join tblSchaap lam on (v.volwId = lam.volwId)
         join tblStal st on (st.schaapId = lam.schaapId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 1 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
        GROUP BY mdr.schaapId, h.actId
        HAVING (max(h.datum) > min(h.datum))

        Union

        SELECT s.schaapId, p.dmafsluit datum, 'Gevoerd' actie, NULL , h.skip
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
 left join (
    SELECT p.lidId, p.ubn, p.naam
    FROM tblPartij p
     join tblRelatie r on (p.partId = r.partId)
    WHERE p.actief = 1 and r.relatie = 'deb' and r.actief = 1
 ) r on(r.ubn = rd.ubn and r.lidId = rd.lidId)
 left join (
    SELECT max(b.bezId) bezId, s.levensnummer
    FROM tblBezet b
     join tblHistorie h on (b.hisId = h.hisId)
     join tblStal st on (h.stalId = st.stalId)
     join tblSchaap s on (st.schaapId = s.schaapId)
    WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.skip = 0
    GROUP BY s.levensnummer
 ) b on (rd.levensnummer = b.levensnummer)
WHERE rd.lidId = '".mysqli_real_escape_string($db,$lidId)."' and rd.actId = 10 and isnull(rd.verwerkt)
ORDER BY right(rd.levensnummer,".mysqli_real_escape_string($db,$Karwerk).")

"); 

if($query->num_rows > 0){ 
    while($row = $query->fetch_assoc()){ 

$levnr_vorig = $levnr;

    $Id = $row['readId'];
    $date = $row['datum'];
    $werknr = $row['werknr'];
    $levnr = $row['levnr'];
    $ubnbest = $row['ubn_afv'];
    $ubn_db = $row['ctrubn'];
    $bestemming = $row['bestemming'];
    $redId_rd = $row['redId_rd'];
    $schaapId = $row['schaapId'];
    $geslacht = $row['geslacht'];
    $dmaanw = $row['dmaanw']; if(isset($dmaanw)) { if($geslacht == 'ooi') {$fase = 'moederdier'; } else if($geslacht == 'ram') { $fase = 'vaderdier';} } 
                                else { $fase = 'lam';}
    $status = $row['actie'];
    $af = $row['af']; if(isset($af) && $af == 1) { $status = $status; } else { $status = $fase; }
    $speen = $row['dmspeen'];
    $aank = $row['dmaankoop'];
    $bezet = $row['bezId'];
    $dmmax_bij_afvoer = $row['datummax_afv'];
    $maxdm_bij_afvoer = $row['maxdatum_afv'];

    $geslacht = $row['geslacht']; if(isset($dmaanw)) { if($geslacht == 'ooi') {$fase = 'moeder'; } else if($geslacht == 'ram') {$fase = 'vader'; }} else { $fase = 'lam'; }
     
// Wachtdagen bepalen
if(isset($schaapId)) {
$zoek_pil = mysqli_query($db,"
SELECT date_format(h.datum,'%d-%m-%Y') datum, art.naam, DATEDIFF( (h.datum + interval art.wdgn_v day), '".mysqli_real_escape_string($db,$date)."') resterend
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblActie a on (a.actId = h.actId)
 left join tblNuttig n on (h.hisId = n.hisId)
 left join tblInkoop i on (i.inkId = n.inkId)
 left join tblArtikel art on (i.artId = art.artId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = 8 and h.skip = 0
 and '".mysqli_real_escape_string($db,$date)."' < (h.datum + interval art.wdgn_v day)
") or die (mysqli_error($db));  

$vandaag = date('Y-m-d');
        while($row = mysqli_fetch_array($zoek_pil))
        { $pildm = $row['datum']; 
          $pil = $row['naam']; 
          $wdgn_v = $row['resterend']; }
}
// Einde Wachtdagen bepalen


    if(!isset($schaapId))                     { $bericht = 'Levensnummer onbekend.'; }
    else if($status == 'overleden' || $status == 'afgeleverd')   { $bericht = "Dit schaap is reeds $status."; } 
    else if(isset($fase) && $date < $dmmax_bij_afvoer)   { $bericht = "Datum ligt voor $maxdm_bij_afvoer."; } 
    else if($modtech == 1 && !isset($speen) && !isset($aank)) { $bericht = "Dit schaap heeft nog geen speendatum."; }
    else if(isset($wdgn_v)) { $bericht = $pildm.' - '.$pil; } unset($wdgn_v);
    //else if($modtech == 1 && !isset($aank) && !isset($bezet)) { $bericht = "Dit schaap heeft nog geen aankoopdatum."; } 
    unset($status);



        $lineData  = array($date, $werknr, $levnr, $bestemming, $fase, $wdgn_v, $bericht); 
        $excelData[] = $lineData; 
    } 
 }

// Gegevens exporteren naar Excel en downloaden als xlsx-bestand 
$xlsx = CodexWorld\PhpXlsxGenerator::fromArray( $excelData ); 
$xlsx->downloadAs($fileName);
