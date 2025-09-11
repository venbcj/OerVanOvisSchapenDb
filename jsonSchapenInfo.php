<?php /* 28-05-2023 : gemaakt 
31-12-2023 and h.skip = 0 toegevoegd aan tblHistorie 
13-04-2025 Laatste dekdatum en dekram toegevoegd */

include "connect_db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    exit();
}
else
{  // Begin van else

$headers = getallheaders(); // geef in een array ook headers terug die ik naar de server heb gestuurd in eerste instantie
//var_dump($headers);
//echo is_array($string) ? 'dit is een array' : 'dit is geen array';
    

if (!isset($headers['Authorization'])) { // Als in de headers geen index 'Autorization voorkomt'
    http_response_code(401); // Unauthorized
    Echo 'authorization header bestaat niet.';
    exit;
} else 
{

    $authorization = explode ( " ", $headers['Authorization'] );
 
     if (count($authorization) == 2 && trim($authorization[0]) == "Bearer" && strlen(trim($authorization[1])) == 64) {

        $zoek_lidId = mysqli_query($db, "SELECT lidId FROM tblLeden WHERE readerkey = '".mysqli_real_escape_string($db,$authorization[1])."'" ) or die(mysqli_error($db));

        $result = mysqli_fetch_array($zoek_lidId);

        if($result){
           $lidid = $result['lidId'];
        } else {
            http_response_code(401); // Unauthorized
            echo 'via authorization header wordt de gebruiker niet gevonden.';
            exit;
        }

    } else {
        http_response_code(401); // Unauthorized
        echo 'authorization header heeft niet de juiste opmaak.';
        exit;
    }
}
 
// $lidid = 3;

switch ($_SERVER['REQUEST_METHOD']) { // Switch
    case 'GET':      

$result = mysqli_query($db,"
SELECT kar_werknr
FROM tblLeden
WHERE lidId = '".mysqli_real_escape_string($db,$lidid)."';
") or die (mysqli_error($db));

    while ($row = mysqli_fetch_assoc($result))     { $Karwerk = $row['kar_werknr']; }

$zoek_info = mysqli_query($db,"
SELECT concat(transponder,levensnummer) tran, coalesce(s.geslacht,'Onbekend') geslacht, coalesce(r.ras,'Onbekend') ras, coalesce(ldek.dekdm_max,'n.v.t.') lastdek, coalesce(dram.werknr,'n.v.t.') dekram, coalesce(lw.worp,'n.v.t.') lastworp, coalesce(lw.werpdm,'n.v.t.') lastwerpdm,
    coalesce(aant_d, 0) aant_d,
    coalesce(aant_w, 0) aant_w,
    coalesce(round(aant_lam/aant_w,2),0) gemWorp, 
    coalesce(aant_lam, 0) aant_lam,
    coalesce(round((1-coalesce(aant_dood, 0)/coalesce(aant_lam,0))*100,2),0) PercLevend,
    coalesce(maxworp, 0) maxworp,
    coalesce(aantalmaxworp, 0) aantalmaxworp

FROM tblSchaap s
 left join tblRas r on (s.rasId = r.rasId)
 join tblStal st on (s.schaapId = st.schaapId)
 left join (
     SELECT st.schaapId, max(h.datum) datum, date_format(max(h.datum),'%d-%m-%Y') dekdm_max
     FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
      join tblVolwas v on (v.hisId = h.hisId)
     WHERE h.skip = 0 and lidId = '".mysqli_real_escape_string($db,$lidid)."'
     GROUP BY st.schaapId
 ) ldek on (ldek.schaapId = s.schaapId)
 left join (
     SELECT max(volwId) mvolwId, h.datum
     FROM tblVolwas v
      join tblHistorie h on (v.hisId = h.hisId)
      join tblStal st on (h.stalId = st.stalId)
     WHERE h.skip = 0 and lidId = '".mysqli_real_escape_string($db,$lidid)."'
     GROUP BY h.datum
 ) lstVId on (lstVId.datum = ldek.datum)
 left join (
     SELECT v.volwId, right(levensnummer, $Karwerk) werknr
     FROM tblSchaap s
      join tblStal st on (s.schaapId = st.schaapId)
      join tblVolwas v on (v.vdrId = s.schaapId) 
     WHERE lidId = '".mysqli_real_escape_string($db,$lidid)."'
 ) dram on (dram.volwId = lstVId.mvolwId)
 left join (
     SELECT v.mdrId, count(l.volwId) worp, date_format(max(h.datum),'%d-%m-%Y') werpdm
    FROM (
        SELECT max(volwId) volwId, mdrId
        FROM tblVolwas v
         join tblStal st on (st.schaapId = v.mdrId)
        WHERE isnull(rel_best) and lidId = '".mysqli_real_escape_string($db,$lidid)."'
        GROUP BY mdrId
    ) v
     join tblSchaap l on (l.volwId = v.volwId)
     join tblStal st on (st.schaapId = l.schaapId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 1 and h.skip = 0
    GROUP BY v.mdrId
     ) lw on (lw.mdrId = s.schaapId)
 left join (
    SELECT count(hisId) aant_d, mdrId
    FROM tblVolwas v
     join tblStal st on (st.schaapId = v.mdrId)
    WHERE isnull(rel_best) and lidId = '".mysqli_real_escape_string($db,$lidid)."'
    GROUP BY mdrId
 ) dekat on (dekat.mdrId = s.schaapId)
  left join (
    SELECT count(DISTINCT(v.volwId)) aant_w, mdrId
    FROM tblVolwas v
     join tblStal st on (st.schaapId = v.mdrId)
     join tblSchaap s on (s.volwId = v.volwId)
    WHERE isnull(rel_best) and lidId = '".mysqli_real_escape_string($db,$lidid)."'
    GROUP BY mdrId
 ) w on (w.mdrId = s.schaapId)
 left join (
    SELECT count(s.schaapId) aant_lam, mdrId
    FROM tblSchaap s 
     join tblVolwas v on (s.volwId = v.volwId)
     join tblStal st on (st.schaapId = v.mdrId)
    WHERE isnull(rel_best) and lidId = '".mysqli_real_escape_string($db,$lidid)."'
    GROUP BY mdrId
 ) lm on (w.mdrId = lm.mdrId)
 left join (
    SELECT count(s.schaapId) aant_levend_niet_in_gebruik, mdrId
    FROM tblSchaap s 
     join tblVolwas v on (s.volwId = v.volwId)
     join tblStal st on (st.schaapId = v.mdrId)
    WHERE isnull(rel_best) and lidId = '".mysqli_real_escape_string($db,$lidid)."' and levensnummer is not null
    GROUP BY mdrId
 ) le on (w.mdrId = le.mdrId)
 left join (
    SELECT coalesce(count(s.schaapId),0) aant_dood, mdrId
    FROM tblSchaap s 
     join tblVolwas v on (s.volwId = v.volwId)
     join tblStal st on (st.schaapId = v.mdrId)
    WHERE isnull(rel_best) and lidId = '".mysqli_real_escape_string($db,$lidid)."' and isnull(levensnummer)
    GROUP BY mdrId
 ) d on (w.mdrId = d.mdrId)
 left join (
     SELECT mw.mdrId, maxworp, count(wgr.volwId) aantalmaxworp
    FROM (
        SELECT max(worp) maxworp, mdrId
        FROM (
            SELECT s.volwId, count(s.schaapId) worp, mdrId
            FROM tblSchaap s 
             join tblVolwas v on (s.volwId = v.volwId)
             join tblStal st on (st.schaapId = v.mdrId)
            WHERE isnull(rel_best) and lidId = '".mysqli_real_escape_string($db,$lidid)."'
            GROUP BY s.volwId, mdrId
         ) wgr
        GROUP BY mdrId
     ) mw
     join (
        SELECT s.volwId, count(s.schaapId) worp, mdrId
        FROM tblSchaap s 
         join tblVolwas v on (s.volwId = v.volwId)
         join tblStal st on (st.schaapId = v.mdrId)
        WHERE isnull(rel_best) and lidId = '".mysqli_real_escape_string($db,$lidid)."'
        GROUP BY s.volwId, mdrId
     ) wgr on (mw.mdrId = wgr.mdrId and mw.maxworp = wgr.worp)
    GROUP BY mw.mdrId, maxworp
 ) amw on (w.mdrId = amw.mdrId)

WHERE isnull(st.rel_best) and lidId = '".mysqli_real_escape_string($db,$lidid)."' and s.transponder is not null
") or die (mysqli_error($db)); 

$rows = mysqli_num_rows($zoek_info);



if(isset($zoek_info) && $rows > 0) {
    while($zi = mysqli_fetch_array($zoek_info))
        {
            $geslacht = $zi['geslacht'];
            $lastdek = $zi['lastdek']; if($geslacht == 'ram') { $lastdek = 'n.v.t.'; }
            $lastdekram = $zi['dekram']; if($geslacht == 'ram') { $lastdek = 'n.v.t.'; }
            $lastworp = $zi['lastworp']; if($geslacht == 'ram') { $lastworp = 'n.v.t.'; }
            $lastwerpdm = $zi['lastwerpdm']; if($geslacht == 'ram') { $lastwerpdm = '00-00-0000'; }

            $aantDek = $zi['aant_d']; if($geslacht == 'ram') { $aantDek = 'n.v.t.'; }    
            $aantWorp = $zi['aant_w']; if($geslacht == 'ram') { $aantWorp = 'n.v.t.'; }    
            $gemWorp = $zi['gemWorp']; if($geslacht == 'ram') { $gemWorp = 'n.v.t.'; }    
            $aantLam = $zi['aant_lam']; if($geslacht == 'ram') { $aantLam = 'n.v.t.'; }    
            //$aantLevend = $zi['aant_levend']; if($geslacht == 'ram') { $aantLevend = 'n.v.t.'; }    
            //$aantDood = $zi['aant_dood']; if($geslacht == 'ram') { $aantDood = 'n.v.t.'; }    
            $PercLevend = $zi['PercLevend']; if($geslacht == 'ram') { $PercLevend = 'n.v.t.'; }    
            $maxWorp = $zi['maxworp']; if($geslacht == 'ram') { $maxWorp = 'n.v.t.'; }    
            $aantMaxWorp = $zi['aantalmaxworp']; if($geslacht == 'ram') { $aantMaxWorp = 'n.v.t.'; }    


            $opties[] = array('Transponder' => $zi['tran'], 'Geslacht' => $geslacht, 'Ras' => $zi['ras'], 'Laatstedek' => $lastdek, 'Laatstedekram' => $lastdekram, 'Laatsteworp' => $lastworp, 'Laatstewerpdm' => $lastwerpdm, 'Dekaantal' => $aantDek, 'Worpaantal' => $aantWorp, 'Gemiddeldeworp' => $gemWorp, 'Lamaantal' => $aantLam, 'PercLevend' => $PercLevend, 'Maxworp' => $maxWorp, 'Aantmaxworp' => $aantMaxWorp);

            }
}



$vb = json_encode($opties);
echo $vb;



         break;
    default:
        http_response_code(405); // Methode niet toegestaan
        exit;
    
} // Einde Switch
//echo json_encode(array("Result" => "Tweede goede resultaat "));
http_response_code(200); // Ok alles is goed

 
} // Einde Begin van else
?>