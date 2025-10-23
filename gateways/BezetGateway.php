<?php

class BezetGateway extends Gateway {

    public function zoek_verblijven_ingebruik_zonder_speendm($lidId) {
       $vw = $this->db->query("
SELECT count(distinct hokId) aant
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
 left join 
 (
    SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
     left join (
        SELECT st.schaapId, h.datum dmspn
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 4
     ) spn on (spn.schaapId = st.schaapId)
     left join (
        SELECT st.schaapId, h.datum dmprnt
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3
     ) prnt on (prnt.schaapId = st.schaapId)
    WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
     and h1.datum <= coalesce(dmspn, coalesce(dmprnt,'2200-01-01'))
    GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (b.hisId = uit.hisv)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and isnull(uit.bezId) and isnull(spn.schaapId) and isnull(prnt.schaapId) and h.skip = 0
");
if ($vw->num_rows == 0) {
    return 0;
}
return $vw->fetch_array()['aant'];
    }

    public function zoek_verblijven_ingebruik_met_speendm($lidId) {
        $vw = $this->db->query("
SELECT count(distinct hokId) aant
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
 left join 
 (
    SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (b.hisId = uit.hisv)
 join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and isnull(uit.bezId) and isnull(prnt.schaapId) and h.skip = 0
");
if ($vw->num_rows == 0) {
    return 0;
}
return $vw->fetch_array()['aant'];
    }

    public function zoek_schapen_zonder_verblijf($lidId) {
       $vw = $this->db->query("
SELECT count(hin.schaapId) aantin
FROM (
    SELECT st.schaapId, max(hisId) hisId
    FROM tblStal st
     join tblUbn u on (st.ubnId = u.ubnId)
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (a.actId = h.actId) 
    WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and isnull(st.rel_best) and a.aan = 1 and h.skip = 0
    GROUP BY st.schaapId
 ) hin
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
    SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = hin.schaapId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = hin.schaapId)
WHERE (isnull(b.hokId) or uit.hist is not null)
");

while($row = $vw->fetch_assoc()) { $zVerb = $row['aantin']; }
return $zVerb;
    }

// Zoek alle verblijven die in gebruik zijn 
/* Toelichting per union :
    schaap (doelgroep 1) zat in hok voor afsluitdm of zit na afsluitdam erin en zit in beide gevallen er nog steeds in
    schaap (doelgroep 1) is uit het hok gegaan na afsluitdatum doelgroep 1
    schaap (doelgroep 2) zat in hok voor afsluitdm of zit na afsluitdam erin en zit in beide gevallen er nog steeds in
    schaap (doelgroep 2) is uit het hok gegaan na afsluitdatum doelgroep 2    
    schaap met aanwasdatum zit nu in hok 
    schaap met aanwasdatum is uit het hok gegaan na afsluitdatum doelgroep 3 */
    public function zoek_verblijven_in_gebruik($lidId) {
$vw = $this->db->query("
SELECT h.hokId, h.hoknr, count(distinct schaap_geb) maxgeb, count(distinct schaap_spn) maxspn, count(distinct schaap_prnt) maxprnt, min(dmin) eerste_in, max(dmuit) laatste_uit
FROM (
    SELECT b.hokId, st.schaapId schaap_geb, NULL schaap_spn, NULL schaap_prnt, h.datum dmin, NULL dmuit
    FROM tblBezet b
     join tblHistorie h on (b.hisId = h.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
     left join 
     (
        SELECT b.bezId, min(h2.hisId) hist
        FROM tblBezet b
         join tblHistorie h1 on (b.hisId = h1.hisId)
         join tblActie a1 on (a1.actId = h1.actId)
         join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
         join tblActie a2 on (a2.actId = h2.actId)
         join tblStal st on (h1.stalId = st.stalId)
         join tblUbn u on (st.ubnId = u.ubnId)
        WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
        GROUP BY b.bezId
     ) uit on (uit.bezId = b.bezId)
     left join (
        SELECT st.schaapId, h.datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 4 and h.skip = 0
     ) spn on (spn.schaapId = st.schaapId)
     left join (
        SELECT st.schaapId, h.datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
     ) prnt on (prnt.schaapId = st.schaapId)
    WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and isnull(uit.bezId)
    and isnull(spn.schaapId)
    and isnull(prnt.schaapId)
     and h.skip = 0

    UNION

    SELECT b.hokId, st.schaapId schaap_geb, NULL schaap_spn, NULL schaap_prnt, h.datum dmin, ht.datum dmuit
    FROM tblBezet b
     join tblHistorie h on (h.hisId = b.hisId)
     join 
     (
        SELECT b.bezId, min(h2.hisId) hist
        FROM tblBezet b
         join tblHistorie h1 on (b.hisId = h1.hisId)
         join tblActie a1 on (a1.actId = h1.actId)
         join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
         join tblActie a2 on (a2.actId = h2.actId)
         join tblStal st on (h1.stalId = st.stalId)
         join tblUbn u on (st.ubnId = u.ubnId)
        WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h1.actId != 2
        GROUP BY b.bezId
     ) uit on (uit.bezId = b.bezId)
     join tblHistorie ht on (ht.hisId = uit.hist)
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
     left join (
        SELECT h.hisId, st.schaapId, h.datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 4 and h.skip = 0
     ) spn on (spn.schaapId = st.schaapId)
     left join (
        SELECT st.schaapId, h.datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
     ) prnt on (prnt.schaapId = st.schaapId)
     left join (
        SELECT p.hokId, max(p.dmafsluit) dmstop
        FROM tblPeriode p
         join tblHok h on (h.hokId = p.hokId)
        WHERE h.lidId = '".$this->db->real_escape_string($lidId)."' and p.doelId = 1 and dmafsluit is not null
        GROUP BY p.hokId
     ) endgeb on (endgeb.hokId = b.hokId)
    WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and ht.datum > coalesce(dmstop,'1973-09-11') 
     and ( isnull(spn.schaapId)  or (spn.datum  > coalesce(dmstop,'1973-09-11') and 
             ( h.datum < spn.datum || (h.datum = spn.datum && h.hisId < spn.hisId) ) )
          )
     and ( isnull(prnt.schaapId) or (prnt.datum > coalesce(dmstop,'1973-09-11') and h.datum < prnt.datum) )
     and h.skip = 0

    UNION

    SELECT b.hokId, NULL schaap_geb, st.schaapId schaap_spn, NULL schaap_prnt, h.datum dmin, NULL dmuit
    FROM tblBezet b
     join tblHistorie h on (b.hisId = h.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
     left join 
     (
        SELECT b.bezId, min(h2.hisId) hist
        FROM tblBezet b
         join tblHistorie h1 on (b.hisId = h1.hisId)
         join tblActie a1 on (a1.actId = h1.actId)
         join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
         join tblActie a2 on (a2.actId = h2.actId)
         join tblStal st on (h1.stalId = st.stalId)
         join tblUbn u on (st.ubnId = u.ubnId)
        WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h1.actId != 2
        GROUP BY b.bezId
     ) uit on (uit.bezId = b.bezId)
     join (
        SELECT st.schaapId, h.datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 4 and h.skip = 0
     ) spn on (spn.schaapId = st.schaapId)
     left join (
        SELECT st.schaapId, h.datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
     ) prnt on (prnt.schaapId = st.schaapId)
    WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and isnull(uit.bezId)
    and (isnull(prnt.schaapId) or h.datum < prnt.datum)
    and h.skip = 0

    UNION

    SELECT b.hokId, NULL schaap_geb, st.schaapId schaap_spn, NULL schaap_prnt, h.datum dmin, ht.datum dmuit
    FROM tblBezet b
     join tblHistorie h on (h.hisId = b.hisId)
     join 
     (
        SELECT b.bezId, min(h2.hisId) hist
        FROM tblBezet b
         join tblHistorie h1 on (b.hisId = h1.hisId)
         join tblActie a1 on (a1.actId = h1.actId)
         join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
         join tblActie a2 on (a2.actId = h2.actId)
         join tblStal st on (h1.stalId = st.stalId)
         join tblUbn u on (st.ubnId = u.ubnId)
        WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
        GROUP BY b.bezId
     ) uit on (uit.bezId = b.bezId)
     join tblHistorie ht on (ht.hisId = uit.hist)
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
     join (
        SELECT h.hisId, st.schaapId, h.datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 4 and h.skip = 0
     ) spn on (spn.schaapId = st.schaapId)
     left join (
        SELECT st.schaapId, h.datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
     ) prnt on (prnt.schaapId = st.schaapId)
     left join (
        SELECT p.hokId, max(p.dmafsluit) dmstop
        FROM tblPeriode p
         join tblHok h on (h.hokId = p.hokId)
        WHERE h.lidId = '".$this->db->real_escape_string($lidId)."' and p.doelId = 2 and dmafsluit is not null
        GROUP BY p.hokId
     ) endspn on (endspn.hokId = b.hokId)
    WHERE u.lidId = '".$this->db->real_escape_string($lidId)."'
    -- 9-1-2019 weggehaald and (isnull(prnt.schaapId) or prnt.datum > coalesce(dmstop,'1973-09-11'))
     and ht.datum > coalesce(dmstop,'1973-09-11') 
     and (h.datum > spn.datum || (h.datum = spn.datum && h.hisId >= spn.hisId) )
     and (isnull(prnt.schaapId) or h.datum < prnt.datum)
     and h.skip = 0

    UNION

    SELECT b.hokId, NULL schaap_geb, NULL schaap_spn, st.schaapId schaap_prnt, h.datum dmin, NULL dmuit
    FROM tblBezet b
     join tblHistorie h on (b.hisId = h.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
     left join 
     (
        SELECT b.bezId, min(h2.hisId) hist
        FROM tblBezet b
         join tblHistorie h1 on (b.hisId = h1.hisId)
         join tblActie a1 on (a1.actId = h1.actId)
         join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
         join tblActie a2 on (a2.actId = h2.actId)
         join tblStal st on (h1.stalId = st.stalId)
         join tblUbn u on (st.ubnId = u.ubnId)
        WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
        GROUP BY b.bezId
     ) uit on (uit.bezId = b.bezId)
     join (
        SELECT st.schaapId
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
     ) prnt on (prnt.schaapId = st.schaapId)
    WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and isnull(uit.bezId) and h.skip = 0

    UNION

    SELECT b.hokId, NULL schaap_geb, NULL schaap_spn, st.schaapId schaap_prnt, h.datum dmin, ht.datum dmuit
    FROM tblBezet b
     join tblHistorie h on (h.hisId = b.hisId)
     join 
     (
        SELECT b.bezId, min(h2.hisId) hist
        FROM tblBezet b
         join tblHistorie h1 on (b.hisId = h1.hisId)
         join tblActie a1 on (a1.actId = h1.actId)
         join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
         join tblActie a2 on (a2.actId = h2.actId)
         join tblStal st on (h1.stalId = st.stalId)
         join tblUbn u on (st.ubnId = u.ubnId)
        WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
        GROUP BY b.bezId, st.schaapId, h1.hisId
     ) uit on (uit.bezId = b.bezId)
     join tblHistorie ht on (ht.hisId = uit.hist)
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
     join (
        SELECT h.hisId, st.schaapId, h.datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
     ) prnt on (prnt.schaapId = st.schaapId)
     left join (
        SELECT p.hokId, max(p.dmafsluit) dmstop
        FROM tblPeriode p
         join tblHok h on (h.hokId = p.hokId)
        WHERE h.lidId = '".$this->db->real_escape_string($lidId)."' and p.doelId = 3 and dmafsluit is not null
        GROUP BY p.hokId
     ) endspn on (endspn.hokId = b.hokId)
    WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and ht.datum > coalesce(dmstop,'1973-09-11') 
     and (h.datum > prnt.datum || (h.datum = prnt.datum && h.hisId >= prnt.hisId) ) and h.skip = 0
 ) ingebr
 join tblHok h on (ingebr.hokId = h.hokId)
GROUP BY h.hokId, h.hoknr
ORDER BY hoknr
");
return $vw;
    }

    public function zoek_nu_in_verblijf_geb($hokId) {
$vw = $this->db->query("
SELECT count(b.bezId) aantin
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join 
 (
    SELECT b.bezId, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE b.hokId = '".$this->db->real_escape_string($hokId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".$this->db->real_escape_string($hokId)."' and isnull(uit.bezId)
and isnull(spn.schaapId)
and isnull(prnt.schaapId)
and h.skip = 0
");
if ($vw->num_rows == 0) {
    return null;
}
return $vw->fetch_row()[0];
    }

    public function zoek_nu_in_verblijf_spn($hokId) {
$vw = $this->db->query("
SELECT count(b.bezId) aantin
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join 
 (
    SELECT b.bezId, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE b.hokId = '".$this->db->real_escape_string($hokId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".$this->db->real_escape_string($hokId)."' and isnull(uit.bezId)
and isnull(prnt.schaapId)
and h.skip = 0
");
if ($vw->num_rows == 0) {
    return null;
}
return $vw->fetch_row()[0];
    }

    public function zoek_nu_in_verblijf_prnt($hokId) {
       $vw = $this->db->query("
SELECT count(distinct(st.schaapId)) aantin
FROM tblStal st
 join tblHistorie h on (h.stalId = st.stalId)
 join tblBezet b on (b.hisId = h.hisId)
 left join (
    SELECT b.bezId, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
    WHERE b.hokId = '".$this->db->real_escape_string($hokId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, h1.hisId
 ) uit on (b.bezId = uit.bezId)
 join (
    SELECT schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".$this->db->real_escape_string($hokId)."' and isnull(uit.bezId) and h.skip = 0
");
if ($vw->num_rows == 0) {
    return null;
}
return $vw->fetch_row()[0];
    }

    public function zoek_verlaten_geb_excl_overpl_en_uitval($hokId, $dmstopgeb) {
$vw = $this->db->query("
SELECT count(uit.bezId) aantuit
FROM tblBezet b
 join tblHistorie h on (h.hisId = b.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join 
 (
    SELECT b.bezId, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE b.hokId = '".$this->db->real_escape_string($hokId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.bezId = b.bezId)
 join tblHistorie ht on (ht.hisId = uit.hist)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".$this->db->real_escape_string($hokId)."' and ht.datum > '".$this->db->real_escape_string($dmstopgeb)."' and ht.actId != 5 and ht.actId != 14
and (isnull(spn.schaapId) or ht.datum = spn.datum)
and (isnull(prnt.schaapId) or ht.datum < prnt.datum)
and h.skip = 0
");
if ($vw->num_rows == 0) {
    return null;
}
return $vw->fetch_row()[0];
    }

    public function zoek_verlaten_spn_excl_overpl_en_uitval($hokId, $dmstopspn) {
       $vw = $this->db->query("
SELECT count(b.bezId) aantuit
FROM tblBezet b
 join tblHistorie h on (h.hisId = b.hisId)
 join tblStal st on (h.stalId = st.stalId)
 left join 
 (
    SELECT b.bezId, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE b.hokId = '".$this->db->real_escape_string($hokId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 left join tblHistorie ht on (ht.hisId = uit.hist)
 join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".$this->db->real_escape_string($hokId)."' 
and ((isnull(ht.datum) and prnt.schaapId is not null) or ht.datum > '".$this->db->real_escape_string($dmstopspn)."')
and (isnull(ht.actId) or (ht.actId != 4 and ht.actId != 5 and ht.actId != 14))
and (ht.datum >= spn.datum or (isnull(uit.bezId) and prnt.schaapId is not null and h.datum < spn.datum))

and h.skip = 0
");
if ($vw->num_rows == 0) {
    return null;
}
return $vw->fetch_row()[0];
    }

    public function zoek_overplaatsing_geb($hokId, $dmstopgeb) {
$vw = $this->db->query("
SELECT count(uit.bezId) aant
FROM tblBezet b
 join tblHistorie h on (h.hisId = b.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join 
 (
    SELECT b.bezId, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE b.hokId = '".$this->db->real_escape_string($hokId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 join tblHistorie ht on (ht.hisId = uit.hist)
 left join (
    SELECT st.schaapId, h.hisId his_spn, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".$this->db->real_escape_string($hokId)."' and ht.actId = 5
  and (
    ht.datum > '".$this->db->real_escape_string($dmstopgeb)."'
    or (ht.datum = '".$this->db->real_escape_string($dmstopgeb)."'
        and h.datum = '".$this->db->real_escape_string($dmstopgeb)."'
        and h.hisId < ht.hisId)
  )
and (isnull(spn.schaapId) or ht.datum < spn.datum or (ht.datum = spn.datum and his_spn > hist))
and (isnull(prnt.schaapId) or ht.datum < prnt.datum)
and h.skip = 0
");
if ($vw->num_rows == 0) {
    return null;
}
return $vw->fetch_row()[0];
    }   

    public function zoek_overplaatsing_spn($hokId, $dmstopspn) {
       $vw = $this->db->query("
SELECT count(uit.bezId) aant
FROM tblBezet b
 join tblHistorie h on (h.hisId = b.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join 
 (
    SELECT b.bezId, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE b.hokId = '".$this->db->real_escape_string($hokId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 join tblHistorie ht on (ht.hisId = uit.hist)
 join (
    SELECT st.schaapId, h.hisId his_spn, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".$this->db->real_escape_string($hokId)."' and ht.actId = 5
 and (ht.datum > '".$this->db->real_escape_string($dmstopspn)."' or (ht.datum = '".$this->db->real_escape_string($dmstopspn)."' and h.datum = '".$this->db->real_escape_string($dmstopspn /* or (ht.datum = spn.datum and his_spn < hist) is voor als speendatum == overplaatsing en overplaatsing heeft eerder plaatsgevonden */)."' and h.hisId < ht.hisId))
and (ht.datum > spn.datum or (ht.datum = spn.datum and his_spn < hist))
and (isnull(prnt.schaapId) or h.datum < prnt.datum)
and h.skip = 0
");
if ($vw->num_rows == 0) {
    return null;
}
return $vw->fetch_row()[0];
    }

    public function zoek_overleden_geb($hokId, $dmstopgeb) {
$vw = $this->db->query("
SELECT count(uit.bezId) aantuit
FROM tblBezet b
 join 
 (
    SELECT b.bezId, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE b.hokId = '".$this->db->real_escape_string($hokId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 join tblHistorie ht on (ht.hisId = uit.hist)
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".$this->db->real_escape_string($hokId)."' and ht.actId = 14
 and ht.datum > '".$this->db->real_escape_string($dmstopgeb)."'
 and isnull(spn.schaapId)
 and isnull(prnt.schaapId)
 and h.skip = 0
");
if ($vw->num_rows == 0) {
    return null;
}
return $vw->fetch_row()[0];
    }

    public function zoek_overleden_spn($hokId, $dmstopspn) {
$vw = $this->db->query("
SELECT count(uit.bezId) aantuit
FROM tblBezet b
 join 
 (
    SELECT b.bezId, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE b.hokId = '".$this->db->real_escape_string($hokId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 join tblHistorie ht on (ht.hisId = uit.hist)
 join tblHistorie h on (h.hisId = b.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".$this->db->real_escape_string($hokId)."' and ht.actId = 14
 and ht.datum > '".$this->db->real_escape_string($dmstopspn)."'
 and isnull(prnt.schaapId)
 and h.skip = 0
");
if ($vw->num_rows == 0) {
    return null;
}
return $vw->fetch_row()[0];
    }

    public function zoek_moeders_van_lam($hokId) {
      $vw = $this->db->query("
SELECT count(distinct v.mdrId) aantmdr
FROM tblBezet b
 left join 
 (
    SELECT b.bezId, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE b.hokId = '".$this->db->real_escape_string($hokId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
 join tblSchaap s on (st.schaapId = s.schaapId)
 join tblVolwas v on (s.volwId = v.volwId)
WHERE b.hokId = '".$this->db->real_escape_string($hokId)."' and isnull(uit.bezId)
 and isnull(spn.schaapId)
 and isnull(prnt.schaapId)
 and h.skip = 0
");
if ($vw->num_rows == 0) {
    return null;
}
return $vw->fetch_row()[0];
}

    public function aantal_laatste_dekkingen_van_moeders_uit_gekozen_verblijf_met_laatste_dekkingen_met_gekozen_vader($txtDay, $kzlHok, $kzlVdr) {
       $vw = $this->db->query("
SELECT count(mdrs.mdrId) aant, datediff('".$this->db->real_escape_string($txtDay)."', h.datum) verschil
FROM (
    SELECT st.schaapId mdrId
    FROM tblBezet b
     join tblHistorie h on (b.hisId = h.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join tblSchaap s on (st.schaapId = s.schaapId)
     left join 
     (
        SELECT b.bezId, min(h2.hisId) hist
        FROM tblBezet b
         join tblHistorie h1 on (b.hisId = h1.hisId)
         join tblActie a1 on (a1.actId = h1.actId)
         join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
         join tblActie a2 on (a2.actId = h2.actId)
         join tblStal st on (h1.stalId = st.stalId)
        WHERE b.hokId = '".$this->db->real_escape_string($kzlHok)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
        GROUP BY b.bezId
     ) uit on (uit.bezId = b.bezId)
     join (
        SELECT st.schaapId
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
     ) prnt on (prnt.schaapId = st.schaapId)
    WHERE s.geslacht = 'ooi' and b.hokId = '".$this->db->real_escape_string($kzlHok)."' and isnull(uit.bezId) and h.skip = 0
 ) mdrs
 join (
    SELECT v.mdrId, max(v.volwId) mxvolwId
    FROM tblVolwas v
     join tblHistorie h on (v.hisId = h.hisId)
    WHERE v.vdrId = '". $this->db->real_escape_string($kzlVdr) ."' and h.skip = 0
    GROUP BY v.mdrId
 ) vmax_mdr_met_vdr on (mdrs.mdrId = vmax_mdr_met_vdr.mdrId)
 join (
    SELECT mdrId, max(volwId) mxvolwId
    FROM tblVolwas
    GROUP BY mdrId 
 ) vmax_mdr on (vmax_mdr_met_vdr.mxvolwId = vmax_mdr.mxvolwId)
 join tblVolwas v on (vmax_mdr.mxvolwId = v.volwId)
 join tblHistorie h on (h.hisId = v.hisId)
 GROUP BY h.datum
 HAVING (count(mdrs.mdrId)) >= 5
");
    return $vw->fetch_row();
    }

public function zoek_moeders_in_verblijf($kzlHok) {
   return $this->db->query("
SELECT st.schaapId mdrId
    FROM tblBezet b
     join tblHistorie h on (b.hisId = h.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join tblSchaap s on (st.schaapId = s.schaapId)
     left join 
     (
        SELECT b.bezId, min(h2.hisId) hist
        FROM tblBezet b
         join tblHistorie h1 on (b.hisId = h1.hisId)
         join tblActie a1 on (a1.actId = h1.actId)
         join tblHistorie h2 on (h1.stalId = h2.stalId
             and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
         join tblActie a2 on (a2.actId = h2.actId)
         join tblStal st on (h1.stalId = st.stalId)
        WHERE b.hokId = '".$this->db->real_escape_string($kzlHok)."'
         and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
        GROUP BY b.bezId
     ) uit on (uit.bezId = b.bezId)
     join (
        SELECT st.schaapId
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
     ) prnt on (prnt.schaapId = st.schaapId)
    WHERE s.geslacht = 'ooi'
 and b.hokId = '".$this->db->real_escape_string($kzlHok)."'
 and isnull(uit.bezId)
 and h.skip = 0
");
}

public function schaap_gegevens($lidId, $hokId, $dmbegin, $dmeind, $dagkg, $filterDoel) {
    return $this->db->query("
SELECT s.levensnummer, his_in.datum dmin, date_format(his_in.datum,'%d-%m-%Y') indm,
 coalesce(his_uit.datum,'".$this->db->real_escape_string($dmeind)."') dmuit,
 date_format(coalesce(his_uit.datum,'".$this->db->real_escape_string($dmeind)."'),'%d-%m-%Y') uitdm,
datediff(coalesce(his_uit.datum,'".$this->db->real_escape_string($dmeind)."'),his_in.datum) dgn,
round(datediff(coalesce(his_uit.datum,'".$this->db->real_escape_string($dmeind)."'),his_in.datum)*".$this->db->real_escape_string($dagkg).",2) kg
FROM tblBezet b
 join (
     SELECT h.hisId, h.stalId, '".$this->db->real_escape_string($dmbegin)."' datum
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
      join tblUbn u on (st.ubnId = u.ubnId)
      join tblBezet alleen_his_uit_bez on (alleen_his_uit_bez.hisId = h.hisId)
     WHERE h.skip = 0 and u.lidId = '".$this->db->real_escape_string($lidId)."' and h.datum < '".$this->db->real_escape_string($dmbegin)."'
     union 
     SELECT h.hisId, h.stalId, h.datum
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
      join tblUbn u on (st.ubnId = u.ubnId)
      join tblBezet alleen_his_uit_bez on (alleen_his_uit_bez.hisId = h.hisId)
     WHERE h.skip = 0 and u.lidId = '".$this->db->real_escape_string($lidId)."' and h.datum >= '".$this->db->real_escape_string($dmbegin)."'
 ) his_in on (his_in.hisId = b.hisId)

 join tblStal st on (st.stalId = his_in.stalId)
 join tblSchaap s on (st.schaapId = s.schaapId)
 left join 
     (
        SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
        FROM tblBezet b
         join tblHistorie h1 on (b.hisId = h1.hisId)
         join tblActie a1 on (a1.actId = h1.actId)
         join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
         join tblActie a2 on (a2.actId = h2.actId)
         join tblStal st on (h1.stalId = st.stalId)
         join tblUbn u on (st.ubnId = u.ubnId)
        WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
        GROUP BY b.bezId, st.schaapId, h1.hisId
     ) uit on (uit.hisv = b.hisId)
 left join (
     SELECT h.hisId, h.stalId, '".$this->db->real_escape_string($dmeind)."' datum
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
      join tblUbn u on (st.ubnId = u.ubnId)
     WHERE h.skip = 0 and u.lidId = '".$this->db->real_escape_string($lidId)."' and h.datum > '".$this->db->real_escape_string($dmeind)."'
     union 
     SELECT h.hisId, h.stalId, h.datum
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
      join tblUbn u on (st.ubnId = u.ubnId)
     WHERE h.skip = 0 and u.lidId = '".$this->db->real_escape_string($lidId)."' and h.datum <= '".$this->db->real_escape_string($dmeind)."'
 ) his_uit on (his_uit.hisId = uit.hist)

 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblUbn u on (st.ubnId = u.ubnId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4 and h.skip = 0 and u.lidId = '".$this->db->real_escape_string($lidId)."'
 ) spn on (spn.schaapId = st.schaapId)
  left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblUbn u on (st.ubnId = u.ubnId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0 and u.lidId = '".$this->db->real_escape_string($lidId)."'
 ) prn on (prn.schaapId = st.schaapId)
 join tblPeriode p on (p.hokId = b.hokId and p.dmafsluit = '".$this->db->real_escape_string($dmeind)."')
WHERE b.hokId = '".$this->db->real_escape_string($hokId)."'
 and his_in.datum < '".$this->db->real_escape_string($dmeind)."'
 and (isnull(uit.bezId) or his_uit.datum > '".$this->db->real_escape_string($dmbegin)."')
 and p.doelId = $_POST[kzlDoel_] ".$this->db->real_escape_string($filterDoel)."
ORDER BY dmin, dmuit
");
    }

public function insert($hisId, $hokId) {
    $this->db->query("INSERT INTO tblBezet
 set hisId = '".$this->db->real_escape_string($hisId)."', hokId = '".$this->db->real_escape_string($hokId). "' ");
}

}
