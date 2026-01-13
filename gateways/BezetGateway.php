<?php

class BezetGateway extends Gateway {

    public function zoek_verblijven_ingebruik_zonder_speendm($lidId) {
        return $this->first_field(<<<SQL
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
    WHERE u.lidId = :lidId and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
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
WHERE u.lidId = :lidId and isnull(uit.bezId) and isnull(spn.schaapId) and isnull(prnt.schaapId) and h.skip = 0
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function zoek_verblijven_ingebruik_met_speendm($lidId) {
        return $this->first_field(<<<SQL
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
    WHERE u.lidId = :lidId and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
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
WHERE u.lidId = :lidId and isnull(uit.bezId) and isnull(prnt.schaapId) and h.skip = 0
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function zoek_schapen_zonder_verblijf($lidId) {
       return $this->first_field(<<<SQL
SELECT count(hin.schaapId) aantin
FROM (
    SELECT st.schaapId, max(hisId) hisId
    FROM tblStal st
     join tblUbn u on (st.ubnId = u.ubnId)
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (a.actId = h.actId) 
    WHERE u.lidId = :lidId and isnull(st.rel_best) and a.aan = 1 and h.skip = 0
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
    WHERE u.lidId = :lidId and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
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
SQL
       , [[':lidId', $lidId, Type::INT]]
       );
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
        return $this->run_query(<<<SQL
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
        WHERE u.lidId = :lidId and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
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
    WHERE u.lidId = :lidId and isnull(uit.bezId)
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
        WHERE u.lidId = :lidId and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h1.actId != 2
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
        WHERE h.lidId = :lidId and p.doelId = 1 and dmafsluit is not null
        GROUP BY p.hokId
     ) endgeb on (endgeb.hokId = b.hokId)
    WHERE u.lidId = :lidId and ht.datum > coalesce(dmstop,'1973-09-11') 
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
        WHERE u.lidId = :lidId and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h1.actId != 2
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
    WHERE u.lidId = :lidId and isnull(uit.bezId)
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
        WHERE u.lidId = :lidId and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
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
        WHERE h.lidId = :lidId and p.doelId = 2 and dmafsluit is not null
        GROUP BY p.hokId
     ) endspn on (endspn.hokId = b.hokId)
    WHERE u.lidId = :lidId
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
        WHERE u.lidId = :lidId and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
        GROUP BY b.bezId
     ) uit on (uit.bezId = b.bezId)
     join (
        SELECT st.schaapId
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
     ) prnt on (prnt.schaapId = st.schaapId)
    WHERE u.lidId = :lidId and isnull(uit.bezId) and h.skip = 0

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
        WHERE u.lidId = :lidId and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
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
        WHERE h.lidId = :lidId and p.doelId = 3 and dmafsluit is not null
        GROUP BY p.hokId
     ) endspn on (endspn.hokId = b.hokId)
    WHERE u.lidId = :lidId and ht.datum > coalesce(dmstop,'1973-09-11') 
     and (h.datum > prnt.datum || (h.datum = prnt.datum && h.hisId >= prnt.hisId) ) and h.skip = 0
 ) ingebr
 join tblHok h on (ingebr.hokId = h.hokId)
GROUP BY h.hokId, h.hoknr
ORDER BY hoknr
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function zoek_nu_in_verblijf_geb_spn($hokId) {
        return $this->first_field(
            <<<SQL
SELECT count(b.bezId) aantin
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join 
 (
    SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId
 and ((h1.datum < h2.datum) or (h1.datum = h2.datum
 and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE b.hokId = :hokId
 and a2.uit = 1
 and h1.skip = 0
 and h2.skip = 0
    GROUP BY b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = :hokId
 and h.skip = 0
 and isnull(uit.bezId)
 and isnull(prnt.schaapId)
SQL
        , [[':hokId', $hokId, Type::INT]]
        );
    }

    // uit Bezet
    // (iets met dezelfde titel staat ook in Hoklijsten. Is het dezelfde query?)
    public function zoek_nu_in_verblijf_prnt($hokId) {
        return $this->first_field(<<<SQL
SELECT count(distinct(st.schaapId)) aantin
FROM tblStal st
 join tblHistorie h on (h.stalId = st.stalId)
 join tblBezet b on (b.hisId = h.hisId)
 left join (
    SELECT b.bezId, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId
 and ((h1.datum < h2.datum) or (h1.datum = h2.datum
 and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
    WHERE b.hokId = :hokId
 and a2.uit = 1
 and h1.skip = 0
 and h2.skip = 0
    GROUP BY b.bezId, h1.hisId
 ) uit on (b.bezId = uit.bezId)
 join (
    SELECT schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = :hokId
 and isnull(uit.bezId)
 and h.skip = 0
SQL
        , [[':hokId', $hokId, Type::INT]]
        );
    }

    // uit HokOverpl
    public function zoek_nu_in_verblijf_parent($hokId) {
        return $this->first_field(
            <<<SQL
SELECT count(b.hisId) aantin
FROM (
    SELECT b.hisId, b.hokId
    FROM tblBezet b
     join tblHistorie h on (b.hisId = h.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join (
        SELECT st.schaapId, h.hisId, h.datum
        FROM tblStal st
        join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3
 and h.skip = 0
    ) prnt on (prnt.schaapId = st.schaapId)
    WHERE b.hokId = :hokId
 and h.skip = 0
 and h.datum >= prnt.datum
 ) b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join 
 (
    SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId
 and ((h1.datum < h2.datum) or (h1.datum = h2.datum
 and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE b.hokId = :hokId
 and a2.uit = 1
 and h1.skip = 0
 and h2.skip = 0
 and h2.actId != 3
    GROUP BY b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = :hokId
 and isnull(uit.bezId)
SQL
        , [[':hokId', $hokId, Type::INT]]
        );
    }

    public function zoek_verlaten_geb_excl_overpl_en_uitval($hokId, $dmstopgeb) {
        return $this->first_field(<<<SQL
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
    WHERE b.hokId = :hokId and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
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
WHERE b.hokId = :hokId and ht.datum > :dmstopgeb and ht.actId != 5 and ht.actId != 14
and (isnull(spn.schaapId) or ht.datum = spn.datum)
and (isnull(prnt.schaapId) or ht.datum < prnt.datum)
and h.skip = 0
SQL
        , [[':hokId', $hokId, Type::INT], [':dmstopgeb', $dmstopgeb, Type::DATE]]
        );
    }

    public function zoek_verlaten_spn_excl_overpl_en_uitval($hokId, $dmstopspn) {
        return $this->first_field(<<<SQL
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
    WHERE b.hokId = :hokId and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
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
WHERE b.hokId = :hokId 
and ((isnull(ht.datum) and prnt.schaapId is not null) or ht.datum > :dmstopspn)
and (isnull(ht.actId) or (ht.actId != 4 and ht.actId != 5 and ht.actId != 14))
and (ht.datum >= spn.datum or (isnull(uit.bezId) and prnt.schaapId is not null and h.datum < spn.datum))
and h.skip = 0
SQL
        , [[':hokId', $hokId, Type::INT], [':dmstopspn', $dmstopspn, Type::DATE]]
        );
    }

    public function zoek_overplaatsing_geb($hokId, $dmstopgeb) {
        return $this->first_field(<<<SQL
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
    WHERE b.hokId = :hokId and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
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
WHERE b.hokId = :hokId and ht.actId = 5
  and (
    ht.datum > :dmstopgeb
    or (ht.datum = :dmstopgeb
        and h.datum = :dmstopgeb
        and h.hisId < ht.hisId)
  )
and (isnull(spn.schaapId) or ht.datum < spn.datum or (ht.datum = spn.datum and his_spn > hist))
and (isnull(prnt.schaapId) or ht.datum < prnt.datum)
and h.skip = 0
SQL
        , [[':hokId', $hokId, Type::INT], [':dmstopgeb', $dmstopgeb, Type::DATE]]
        );
    }   

    public function zoek_overplaatsing_spn($hokId, $dmstopspn) {
        return $this->first_field(<<<SQL
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
    WHERE b.hokId = :hokId and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
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
WHERE b.hokId = :hokId and ht.actId = 5
 and (ht.datum > :dmstopspn or (ht.datum = :dmstopspn and h.datum = :dmstopspn and h.hisId < ht.hisId))
and (ht.datum > spn.datum or (ht.datum = spn.datum and his_spn < hist))
and (isnull(prnt.schaapId) or h.datum < prnt.datum)
and h.skip = 0
SQL
        , [[':hokId', $hokId, Type::INT], [':dmstopspn', $dmstopspn, Type::DATE]]
        );
    }

    public function zoek_overleden_geb($hokId, $dmstopgeb) {
       return $this->first_field(<<<SQL
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
    WHERE b.hokId = :hokId and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
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
WHERE b.hokId = :hokId and ht.actId = 14
 and ht.datum > :dmstopgeb
 and isnull(spn.schaapId)
 and isnull(prnt.schaapId)
 and h.skip = 0
SQL
        , [[':hokId', $hokId, Type::INT], [':dmstopgeb', $dmstopgeb, Type::DATE]]
       );
    }

    public function zoek_overleden_spn($hokId, $dmstopspn) {
        return $this->first_field(<<<SQL
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
    WHERE b.hokId = :hokId and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
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
WHERE b.hokId = :hokId and ht.actId = 14
 and ht.datum > :dmstopspn
 and isnull(prnt.schaapId)
 and h.skip = 0
SQL
        , [[':hokId', $hokId, Type::INT], [':dmstopspn', $dmstopspn, Type::DATE]]
        );
    }

    public function zoek_moeders_van_lam($hokId) {
        return $this->first_field(<<<SQL
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
    WHERE b.hokId = :hokId and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
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
WHERE b.hokId = :hokId and isnull(uit.bezId)
 and isnull(spn.schaapId)
 and isnull(prnt.schaapId)
 and h.skip = 0
SQL
        , [
            [':hokId', $hokId, Type::INT],
        ]
        );
    }

    public function aantal_laatste_dekkingen_van_moeders_uit_gekozen_verblijf_met_laatste_dekkingen_met_gekozen_vader($txtDay, $kzlHok, $kzlVdr) {
        return $this->first_row(<<<SQL
SELECT count(mdrs.mdrId) aant, datediff(:txtDay, h.datum) verschil
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
        WHERE b.hokId = :kzlHok and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
        GROUP BY b.bezId
     ) uit on (uit.bezId = b.bezId)
     join (
        SELECT st.schaapId
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
     ) prnt on (prnt.schaapId = st.schaapId)
    WHERE s.geslacht = 'ooi' and b.hokId = :kzlHok and isnull(uit.bezId) and h.skip = 0
 ) mdrs
 join (
    SELECT v.mdrId, max(v.volwId) mxvolwId
    FROM tblVolwas v
     join tblHistorie h on (v.hisId = h.hisId)
    WHERE v.vdrId = :kzlVdr and h.skip = 0
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
SQL
        , [
            [':txtDay', $txtDay],
            [':kzlHok', $kzlHok, Type::INT],
            [':kzlVdr', $kzlVdr, Type::INT],
        ]
        , [null, null]
        );
    }

    public function zoek_moeders_in_verblijf($kzlHok) {
        return $this->run_query(<<<SQL
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
        WHERE b.hokId = :kzlHok
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
 and b.hokId = :kzlHok
 and isnull(uit.bezId)
 and h.skip = 0
SQL
 , [[':kzlHok', $kzlHok, Type::INT]]
        );
    }

    public function schaap_gegevens($lidId, $hokId, $dmbegin, $dmeind, $dagkg, $filterDoel, $doelId) {
        return $this->run_query(<<<SQL
SELECT s.levensnummer, his_in.datum dmin, date_format(his_in.datum,'%d-%m-%Y') indm,
 coalesce(his_uit.datum, :dmeind) dmuit,
 date_format(coalesce(his_uit.datum, :dmeind),'%d-%m-%Y') uitdm,
datediff(coalesce(his_uit.datum, :dmeind),his_in.datum) dgn,
round(datediff(coalesce(his_uit.datum, :dmeind),his_in.datum) * :dagkg, 2) kg
FROM tblBezet b
 join (
     SELECT h.hisId, h.stalId, :dmbegin datum
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
      join tblUbn u on (st.ubnId = u.ubnId)
      join tblBezet alleen_his_uit_bez on (alleen_his_uit_bez.hisId = h.hisId)
     WHERE h.skip = 0 and u.lidId = :lidId and h.datum < :dmbegin
     union 
     SELECT h.hisId, h.stalId, h.datum
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
      join tblUbn u on (st.ubnId = u.ubnId)
      join tblBezet alleen_his_uit_bez on (alleen_his_uit_bez.hisId = h.hisId)
     WHERE h.skip = 0 and u.lidId = :lidId and h.datum >= :dmbegin
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
        WHERE u.lidId = :lidId and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
        GROUP BY b.bezId, st.schaapId, h1.hisId
     ) uit on (uit.hisv = b.hisId)
 left join (
     SELECT h.hisId, h.stalId, :dmeind datum
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
      join tblUbn u on (st.ubnId = u.ubnId)
     WHERE h.skip = 0 and u.lidId = :lidId and h.datum > :dmeind
     union 
     SELECT h.hisId, h.stalId, h.datum
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
      join tblUbn u on (st.ubnId = u.ubnId)
     WHERE h.skip = 0 and u.lidId = :lidId and h.datum <= :dmeind
 ) his_uit on (his_uit.hisId = uit.hist)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblUbn u on (st.ubnId = u.ubnId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4 and h.skip = 0 and u.lidId = :lidId
 ) spn on (spn.schaapId = st.schaapId)
  left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblUbn u on (st.ubnId = u.ubnId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0 and u.lidId = :lidId
 ) prn on (prn.schaapId = st.schaapId)
 join tblPeriode p on (p.hokId = b.hokId and p.dmafsluit = :dmeind)
WHERE b.hokId = :hokId
 and his_in.datum < :dmeind
 and (isnull(uit.bezId) or his_uit.datum > :dmbegin)
 and p.doelId = :doelId $filterDoel
ORDER BY dmin, dmuit
SQL
        , [
            [':lidId', $lidId, Type::INT],
            [':hokId', $hokId, Type::INT],
            [':doelId', $doelId, Type::INT],
            [':dagkg', $dagkg, Type::FLOAT],
            [':dmbegin', $dmbegin, Type::DATE],
            [':dmeind', $dmeind, Type::DATE],
        ]);
    }

    public function insert($hisId, $hokId) {
        $this->run_query(
            <<<SQL
INSERT INTO tblBezet
 set hisId = :hisId, hokId = :hokId
SQL
        , [[':hisId', $hisId, Type::INT], [':hokId', $hokId, Type::INT]]
        );
    }

    public function zoek_verblijven($lidId) {
        return $this->run_query(<<<SQL
SELECT b.hokId, hk.hoknr, count(b.bezId) nu
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblHok hk on (hk.hokId = b.hokId)
 left join (
    SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE st.lidId = :lidId and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, h1.hisId
 ) uit on (b.bezId = uit.bezId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE hk.lidId = :lidId and isnull(uit.bezId) and h.skip = 0
GROUP BY b.hokId, hk.hoknr
ORDER BY hk.hoknr
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function getHokAfleverenFrom() {
        return <<<SQL
tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblBezet b on (b.hisId = h.hisId)
 left join (
    SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE st.lidId = :lidId
 and a2.uit = 1
 and h1.skip = 0
 and h2.skip = 0
 and b.hokId = :hokId
    GROUP BY b.bezId, h1.hisId
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
SQL;
    }

    public function getHokAfleverenWhere($pagina, $fase, $lidId, $hokId) {
        $where = "WHERE b.hokId = :hokId and isnull(uit.bezId) and h.skip = 0";
        switch ($pagina) {
        case 'Afleveren':
            $where .= " and spn.schaapId is not null and prnt.schaapId is null";
            break;
        case 'Verkopen':
            $where .= " and prnt.schaapId is not null";
            break;
        case 'Uitscharen':
            switch ($fase) {
            case 3:
                $where .= " and prnt.schaapId is not null";
                break;
            case 1:
                $where .= " and prnt.schaapId is null";
                break;
            }
        }
        return [
            $where,
            [
                [':hokId', $hokId, Type::INT],
                [':lidId', $lidId, Type::INT],
            ]
        ];
    }

    public function zoek_periode_met_aantal_schapen($lidId, $hokId, $dmafsl, $dmStartPeriode, $fase_tijdens_betreden_verblijf) {
        return $this->run_query(
            <<<SQL
SELECT min(h.datum) dmEerste_in, date_format(min(h.datum),'%d-%m-%Y') eerste_inDm,
 date_format(max(ht.datum),'%d-%m-%Y') laatste_uit, count(distinct(st.schaapId)) aant_schapen,
 count(b.bezId) aant_beweging
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
 left join 
 (
    SELECT b.bezId, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId
 and ((h1.datum < h2.datum) or (h1.datum = h2.datum
 and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE st.lidId = :lidId
 and a2.uit = 1
 and h1.skip = 0
 and h2.skip = 0
 and h1.actId != 2
    GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 left join tblHistorie ht on (ht.hisId = uit.hist)
 left join (
     SELECT schaapId, datum
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
     WHERE actId = 4
 and skip = 0
 ) spn on (st.schaapId = spn.schaapId)
 left join (
     SELECT schaapId, datum
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
     WHERE actId = 3
 and skip = 0
 ) prnt on (st.schaapId = prnt.schaapId)
WHERE b.hokId = :hokId
 and h.skip = 0
 and $fase_tijdens_betreden_verblijf
 and (h.datum < :datum
 and (isnull(ht.datum) or ht.datum > :startdatum))
SQL
        , [
            [':lidId', $lidId, Type::INT],
            [':hokId', $hokId, Type::INT],
            [':datum', $dmafsl],
            [':startdatum', $dmStartPeriode],
        ]
        );
    }

    public function zoek_inhoud_periode($lidId, $hokId, $dmafsl, $dmStartPeriode, $fase_tijdens_betreden_verblijf, $Karwerk) {
        return $this->run_query(
            <<<SQL
SELECT right(s.levensnummer,$Karwerk) werknr, r.ras, s.geslacht, date_format(h.datum,'%Y%m%d') indm_sort,
 date_format(h.datum,'%d-%m-%Y') indm, date_format(ht.datum,'%Y%m%d') uitdm_sort,
 date_format(ht.datum,'%d-%m-%Y') uitdm, datediff(ht.datum, h.datum) schpdgn, h.kg kgin, ht.kg kguit,
 round((ht.kg-h.kg)/datediff(ht.datum, h.datum)*1000,2) gemgroei, date_format(hdo.datum,'%Y%m%d') uitvdm_sort,
 date_format(hdo.datum,'%d-%m-%Y') uitvdm, a.actie status
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 join tblRas r on (r.rasId = s.rasId)
 left join 
 (
    SELECT b.bezId, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE st.lidId = :lidId
 and a2.uit = 1
 and h1.skip = 0
 and h2.skip = 0
 and h1.actId != 2
    GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 left join tblHistorie ht on (ht.hisId = uit.hist)
 left join tblHistorie hdo on (hdo.hisId = uit.hist and hdo.actId = 14)
 left join tblActie a on (a.actId = ht.actId)
 left join (
     SELECT schaapId, datum
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
     WHERE actId = 4
 and skip = 0
 ) spn on (st.schaapId = spn.schaapId)
 left join (
     SELECT schaapId, datum
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
     WHERE actId = 3
 and skip = 0
 ) prnt on (st.schaapId = prnt.schaapId)
WHERE b.hokId = :hokId
 and h.skip = 0
 and $fase_tijdens_betreden_verblijf
 and (h.datum < :datum)
 and (isnull(ht.datum) or ht.datum > :startdatum)
ORDER BY st.schaapId, b.hisId
SQL
        , [
            [':lidId', $lidId, Type::INT],
            [':hokId', $hokId, Type::INT],
            [':datum', $dmafsl],
            [':startdatum', $dmStartPeriode],
        ]
        );
    }

    public function zoek_hok_ingebruik_geb($lidId) {
        return $this->run_query(
            <<<SQL
SELECT ho.hokId, ho.hoknr
FROM tblBezet b
 join tblHok ho on (b.hokId = ho.hokId)
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 left join tblRas r on (s.rasId = r.rasId)
 left join 
 (
    SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE st.lidId = :lidId and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
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
WHERE st.lidId = :lidId and h.skip = 0 and isnull(uit.bezId) and isnull(spn.schaapId) and isnull(prnt.schaapId)
GROUP BY ho.hokId, ho.hoknr
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function zoek_hok_ingebruik_spn($lidId) {
        return $this->run_query(
            <<<SQL
SELECT ho.hokId, ho.hoknr
FROM tblBezet b
 join tblHok ho on (b.hokId = ho.hokId)
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 left join tblRas r on (s.rasId = r.rasId)
 left join 
 (
    SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE st.lidId = :lidId and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
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
WHERE st.lidId = :lidId and h.skip = 0 and isnull(uit.bezId) and isnull(prnt.schaapId)
GROUP BY ho.hokId, ho.hoknr
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function hoklijst_zoek_nu_in_verblijf_geb($hokId) {
        return $this->run_query(
            <<<SQL
SELECT ho.hoknr, count(b.bezId) nu, r.ras, s.geslacht
FROM tblBezet b
 join tblHok ho on (b.hokId = ho.hokId)
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 left join tblRas r on (s.rasId = r.rasId)
 left join 
 (
    SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE b.hokId = :hokId and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
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
WHERE b.hokId = :hokId and h.skip = 0 and isnull(uit.bezId) and isnull(spn.schaapId) and isnull(prnt.schaapId)
GROUP BY ho.hoknr, r.ras, s.geslacht
SQL
        , [[':hokId', $hokId, Type::INT]]
        );
    }

    public function hoklijst_zoek_nu_in_verblijf_spn($hokId) {
        return $this->run_query(
            <<<SQL
SELECT ho.hoknr, count(b.bezId) nu, r.ras, s.geslacht
FROM tblBezet b
 join tblHok ho on (b.hokId = ho.hokId)
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 left join tblRas r on (s.rasId = r.rasId)
 left join 
 (
    SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE b.hokId = :hokId and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
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
WHERE b.hokId = :hokId and h.skip = 0 and isnull(uit.bezId) and isnull(prnt.schaapId)
GROUP BY ho.hoknr, r.ras, s.geslacht
SQL
        , [[':hokId', $hokId, Type::INT]]
        );
    }

    public function insert_tblBezet($hisId, $fldHok) {
        $sql = <<<SQL
    INSERT INTO tblBezet set hisId = :hisId, hokId = :fldHok
SQL;
        $args = [[':hisId', $hisId, Type::INT], [':fldHok', $fldHok]];
        return $this->run_query($sql, $args);
    }

    // TODO: deze query wordt samen met zoek_verblijf_gegevens gebruikt in Bezet_pdf. Alleen de eerste twee kolommen worden gebruikt
    public function zoek_verblijven_in_gebruik_bezet($lidId)    {
        $sql = <<<SQL
        SELECT h.hokId, h.hoknr, count(distinct schaap_geb) maxgeb, count(distinct schaap_spn) maxspn, min(dmin) eerste_in, max(dmuit) laatste_uit
        FROM (
            SELECT b.hokId, st.schaapId schaap_geb, NULL schaap_spn, h.datum dmin, NULL dmuit
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
                    WHERE h.actId = 4 and h.skip = 0
                 ) spn on (spn.schaapId = st.schaapId)
                 left join (
                    SELECT st.schaapId, h.datum dmprnt
                    FROM tblStal st
                     join tblHistorie h on (st.stalId = h.stalId)
                    WHERE h.actId = 3 and h.skip = 0
                 ) prnt on (prnt.schaapId = st.schaapId)
                WHERE u.lidId = :lidId and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
                 and h1.datum <= coalesce(dmspn, coalesce(dmprnt,'2200-01-01'))
                GROUP BY b.bezId, st.schaapId, h1.hisId
             ) uit on (uit.hisv = b.hisId)
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
            WHERE u.lidId = :lidId and h.skip = 0 and isnull(uit.bezId)
            and isnull(spn.schaapId)
            and isnull(prnt.schaapId)

            UNION

            SELECT b.hokId, NULL schaap_geb, st.schaapId schaap_spn, h.datum dmin, NULL dmuit
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
                WHERE u.lidId = :lidId and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h1.actId != 2
                GROUP BY b.bezId, st.schaapId, h1.hisId
             ) uit on (uit.hisv = b.hisId)
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
            WHERE u.lidId = :lidId and h.skip = 0 and isnull(uit.bezId)
            and isnull(prnt.schaapId)

            UNION

            SELECT b.hokId, st.schaapId schaap_geb, NULL schaap_spn, h.datum dmin, ht.datum dmuit
            FROM tblBezet b
             join tblHistorie h on (h.hisId = b.hisId)
             join 
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
                    FROM tblStal st join tblHistorie h on (st.stalId = h.stalId)
                    WHERE h.actId = 4 and h.skip = 0
                 ) spn on (spn.schaapId = st.schaapId)
                 left join (
                    SELECT st.schaapId, h.datum dmprnt
                    FROM tblStal st join tblHistorie h on (st.stalId = h.stalId)
                    WHERE h.actId = 3 and h.skip = 0
                 ) prnt on (prnt.schaapId = st.schaapId)
                WHERE u.lidId = :lidId and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h1.actId != 2
                 and h1.datum <= coalesce(dmspn, coalesce(dmprnt,'2200-01-01'))
                GROUP BY b.bezId, st.schaapId, h1.hisId
             ) uit on (uit.hisv = b.hisId)
             join tblHistorie ht on (ht.hisId = uit.hist)
             join tblStal st on (st.stalId = h.stalId)
         join tblUbn u on (st.ubnId = u.ubnId)
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
             left join (
                SELECT p.hokId, max(p.dmafsluit) dmstop
                FROM tblPeriode p
                 join tblHok h on (h.hokId = p.hokId)
                WHERE h.lidId = :lidId and p.doelId = 1 and dmafsluit is not null
                GROUP BY p.hokId
             ) endgeb on (endgeb.hokId = b.hokId)
            WHERE u.lidId = :lidId and h.skip = 0 and ht.datum > coalesce(dmstop,'1973-09-11') 
             and (isnull(spn.schaapId)  or spn.datum  > coalesce(dmstop,'1973-09-11') and h.datum < spn.datum) 
             and (isnull(prnt.schaapId) or prnt.datum > coalesce(dmstop,'1973-09-11'))

            UNION

            SELECT b.hokId, NULL schaap_geb, st.schaapId schaap_spn, h.datum dmin, ht.datum dmuit
            FROM tblBezet b
             join tblHistorie h on (h.hisId = b.hisId)
             join 
             (
                SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
                FROM tblBezet b
                 join tblHistorie h1 on (b.hisId = h1.hisId)
                 join tblActie a1 on (a1.actId = h1.actId)
                 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
                 join tblActie a2 on (a2.actId = h2.actId)
                 join tblStal st on (h1.stalId = st.stalId)
         join tblUbn u on (st.ubnId = u.ubnId)
                WHERE u.lidId = :lidId and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
                GROUP BY b.bezId, st.schaapId, h1.hisId
             ) uit on (uit.hisv = b.hisId)
             join tblHistorie ht on (ht.hisId = uit.hist)
             join tblStal st on (st.stalId = h.stalId)
         join tblUbn u on (st.ubnId = u.ubnId)
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
             left join (
                SELECT p.hokId, max(p.dmafsluit) dmstop
                FROM tblPeriode p
                 join tblHok h on (h.hokId = p.hokId)
                WHERE h.lidId = :lidId and p.doelId = 2 and dmafsluit is not null
                GROUP BY p.hokId
             ) endspn on (endspn.hokId = b.hokId)
            WHERE u.lidId = :lidId and h.skip = 0 and ht.datum > coalesce(dmstop,'1973-09-11') 
             and h.datum >= spn.datum and (h.datum < prnt.datum or isnull(prnt.schaapId))


            UNION

            SELECT b.hokId, NULL schaap_geb, NULL schaap_spn, NULL dmin, NULL dmuit
            FROM (
                SELECT b.hisId, b.hokId
                FROM tblBezet b
                 join tblHistorie h on (b.hisId = h.hisId)
                 join tblStal st on (st.stalId = h.stalId)
         join tblUbn u on (st.ubnId = u.ubnId)
                 join (
                    SELECT st.schaapId, h.hisId, h.datum
                    FROM tblStal st
                     join tblHistorie h on (st.stalId = h.stalId)
                    WHERE h.actId = 3 and h.skip = 0
                ) prnt on (prnt.schaapId = st.schaapId)
                WHERE u.lidId = :lidId and h.skip = 0 and h.datum >= prnt.datum
             ) b
             join tblHistorie h on (b.hisId = h.hisId)
             join tblStal st on (st.stalId = h.stalId)
         join tblUbn u on (st.ubnId = u.ubnId)
             left join 
             (
                SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
                FROM tblBezet b
                 join tblHistorie h1 on (b.hisId = h1.hisId)
                 join tblActie a1 on (a1.actId = h1.actId)
                 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
                 join tblActie a2 on (a2.actId = h2.actId)
                 join tblStal st on (h1.stalId = st.stalId)
         join tblUbn u on (st.ubnId = u.ubnId)
                WHERE u.lidId = :lidId and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
                GROUP BY b.bezId, h1.hisId
             ) uit on (uit.hisv = b.hisId)
             join (
                SELECT st.schaapId
                FROM tblStal st
                 join tblHistorie h on (st.stalId = h.stalId)
                WHERE h.actId = 3 and h.skip = 0
             ) prnt on (prnt.schaapId = st.schaapId)
            WHERE u.lidId = :lidId and isnull(uit.bezId)

         ) ingebr
         join tblHok h on (ingebr.hokId = h.hokId)
        GROUP BY h.hokId, h.hoknr
        ORDER BY hoknr
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->run_query($sql, $args);
    }

    public function zoek_nu_in_verblijf_geb($hokId)        {
        $sql = <<<SQL
SELECT count(b.bezId) aantin
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join 
 (
    SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE b.hokId = :hokId and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
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
WHERE b.hokId = :hokId
and isnull(uit.bezId)
and isnull(spn.schaapId)
and isnull(prnt.schaapId)
and h.skip = 0
SQL;
        $args = [[':hokId', $hokId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function hok_inhoud_geb($Karwerk, $hokId)        {
        $sql = <<<SQL
            SELECT s.schaapId, right(s.levensnummer,:Karwerk) werknr, r.ras, s.geslacht, date_format(hg.datum,'%d-%m-%Y') geb, date_format(h.datum,'%d-%m-%Y') van, date_format(hg.datum + interval 7 week,'%d-%m-%Y') ficspn, right(mdr.levensnummer,:Karwerk) mdr, lastkg.kg lstkg
            FROM tblBezet b
             join tblHistorie h on (b.hisId = h.hisId)
             join tblStal st on (st.stalId = h.stalId)
             join tblSchaap s on (s.schaapId = st.schaapId)
             left join tblRas r on (r.rasId = s.rasId)
             left join tblVolwas v on (v.volwId = s.volwId)
             left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
             left join 
             (
                SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
                FROM tblBezet b
                 join tblHistorie h1 on (b.hisId = h1.hisId)
                 join tblActie a1 on (a1.actId = h1.actId)
                 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
                 join tblActie a2 on (a2.actId = h2.actId)
                 join tblStal st on (h1.stalId = st.stalId)
                WHERE b.hokId = :hokId and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
                GROUP BY b.bezId, h1.hisId
             ) uit on (uit.hisv = b.hisId)
             left join (
                SELECT st.schaapId, h.datum
                FROM tblStal st
                 join tblHistorie h on (st.stalId = h.stalId)
                WHERE h.actId = 1 and h.skip = 0
             ) hg on (hg.schaapId = st.schaapId)
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
             left join (
                SELECT st.schaapId, max(h.hisId) hisId
                FROM tblStal st
                 join tblHistorie h on (st.stalId = h.stalId)
                WHERE h.kg is not null
                GROUP BY st.schaapId
             ) hkg on (hkg.schaapId = st.schaapId)
             left join tblHistorie lastkg on (lastkg.hisId = hkg.hisId)
            WHERE b.hokId = :hokId and h.skip = 0 and isnull(uit.bezId) and isnull(spn.schaapId) and isnull(prnt.schaapId)
            ORDER BY right(s.levensnummer,:Karwerk)
SQL;
        $args = [[':Karwerk', $Karwerk], [':hokId', $hokId, Type::INT]];
        return $this->run_query($sql, $args);
    }

    public function zoek_nu_in_verblijf_spn($hokId)    {
$sql = <<<SQL
SELECT count(b.bezId) aantin
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join 
 (
    SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE b.hokId = :hokId and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
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
WHERE b.hokId = :hokId
 and h.skip = 0
 and isnull(uit.bezId)
 and isnull(prnt.schaapId)
SQL;
        $args = [[':hokId', $hokId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function hok_inhoud_spn($Karwerk, $hokId)        {
        $sql = <<<SQL
            SELECT s.schaapId, right(s.levensnummer,:Karwerk) werknr, r.ras, s.geslacht, date_format(hg.datum,'%d-%m-%Y') geb, date_format(spn.datum,'%d-%m-%Y') spn, date_format(h.datum,'%d-%m-%Y') van, date_format(hg.datum + interval 7 week,'%d-%m-%Y') ficspn, date_format(hg.datum + interval 130 day,'%d-%m-%Y') ficafv, right(mdr.levensnummer,:Karwerk) mdr, lastkg.kg lstkg
            FROM tblBezet b
             join tblHistorie h on (b.hisId = h.hisId)
             join tblStal st on (st.stalId = h.stalId)
             join tblSchaap s on (s.schaapId = st.schaapId)
             left join tblRas r on (r.rasId = s.rasId)
             left join tblVolwas v on (v.volwId = s.volwId)
             left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
             left join 
             (
                SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
                FROM tblBezet b
                 join tblHistorie h1 on (b.hisId = h1.hisId)
                 join tblActie a1 on (a1.actId = h1.actId)
                 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
                 join tblActie a2 on (a2.actId = h2.actId)
                 join tblStal st on (h1.stalId = st.stalId)
                WHERE b.hokId = :hokId and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
                GROUP BY b.bezId, h1.hisId
             ) uit on (uit.hisv = b.hisId)
             left join (
                SELECT st.schaapId, h.datum
                FROM tblStal st
                 join tblHistorie h on (st.stalId = h.stalId)
                WHERE h.actId = 1 and h.skip = 0
             ) hg on (hg.schaapId = st.schaapId)
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
             left join (
                SELECT st.schaapId, max(h.hisId) hisId
                FROM tblStal st
                 join tblHistorie h on (st.stalId = h.stalId)
                WHERE h.kg is not null
                GROUP BY st.schaapId
             ) hkg on (hkg.schaapId = st.schaapId)
             left join tblHistorie lastkg on (lastkg.hisId = hkg.hisId)
            WHERE b.hokId = :hokId and h.skip = 0 and isnull(uit.bezId) and isnull(prnt.schaapId)
            ORDER BY right(s.levensnummer,:Karwerk)
SQL;
        $args = [[':Karwerk', $Karwerk], [':hokId', $hokId, Type::INT]];
        return $this->run_query($sql, $args);
    }

    public function zoek_nu_in_verblijf_prnt_pdf($hokId){
        $sql = <<<SQL
    SELECT count(b.hisId) aantin
    FROM (
        SELECT b.hisId, b.hokId
        FROM tblBezet b
         join tblHistorie h on (b.hisId = h.hisId)
         join tblStal st on (st.stalId = h.stalId)
         join (
            SELECT st.schaapId, h.hisId, h.datum
            FROM tblStal st
            join tblHistorie h on (st.stalId = h.stalId)
            WHERE h.actId = 3 and h.skip = 0
        ) prnt on (prnt.schaapId = st.schaapId)
        WHERE b.hokId = :hokId and h.skip = 0 and h.datum >= prnt.datum
     ) b
     join tblHistorie h on (b.hisId = h.hisId)
     join tblStal st on (st.stalId = h.stalId)
     left join 
     (
        SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
        FROM tblBezet b
         join tblHistorie h1 on (b.hisId = h1.hisId)
         join tblActie a1 on (a1.actId = h1.actId)
         join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
         join tblActie a2 on (a2.actId = h2.actId)
         join tblStal st on (h1.stalId = st.stalId)
        WHERE b.hokId = :hokId and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
        GROUP BY b.bezId, h1.hisId
     ) uit on (uit.hisv = b.hisId)
     join (
        SELECT st.schaapId
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
     ) prnt on (prnt.schaapId = st.schaapId)
    WHERE b.hokId = :hokId and isnull(uit.bezId)
SQL;
        $args = [[':hokId', $hokId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function hok_inhoud_vanaf_aanwas($Karwerk, $hokId)        {
        $sql = <<<SQL
            SELECT s.schaapId, right(s.levensnummer,:Karwerk) werknr, r.ras, s.geslacht, date_format(hg.datum,'%d-%m-%Y') geb, date_format(prnt.datum,'%d-%m-%Y') aanw, date_format(h.datum,'%d-%m-%Y') van, b.hisId,
                lastkg.kg lstkg
            FROM (
                SELECT b.hisId, b.hokId
                FROM tblBezet b
                 join tblHistorie h on (b.hisId = h.hisId)
                 join tblStal st on (st.stalId = h.stalId)
                 join (
                    SELECT st.schaapId, h.hisId, h.datum
                    FROM tblStal st
                    join tblHistorie h on (st.stalId = h.stalId)
                    WHERE h.actId = 3 and h.skip = 0
                ) prnt on (prnt.schaapId = st.schaapId)
                WHERE b.hokId = :hokId and h.skip = 0 and h.datum >= prnt.datum
             ) b
             join tblHistorie h on (b.hisId = h.hisId)
             join tblStal st on (st.stalId = h.stalId)
             join tblSchaap s on (s.schaapId = st.schaapId)
             left join tblRas r on (r.rasId = s.rasId)
             left join tblVolwas v on (v.volwId = s.volwId)
             left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
             left join 
             (
                SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
                FROM tblBezet b
                 join tblHistorie h1 on (b.hisId = h1.hisId)
                 join tblActie a1 on (a1.actId = h1.actId)
                 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
                 join tblActie a2 on (a2.actId = h2.actId)
                 join tblStal st on (h1.stalId = st.stalId)
                WHERE b.hokId = :hokId and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
                GROUP BY b.bezId, h1.hisId
             ) uit on (uit.hisv = b.hisId)
             left join (
                SELECT st.schaapId, h.datum
                FROM tblStal st
                 join tblHistorie h on (st.stalId = h.stalId)
                WHERE h.actId = 1 and h.skip = 0
             ) hg on (hg.schaapId = st.schaapId)
             join (
                SELECT st.schaapId, h.datum
                FROM tblStal st
                 join tblHistorie h on (st.stalId = h.stalId)
                WHERE h.actId = 3 and h.skip = 0
             ) prnt on (prnt.schaapId = st.schaapId)
             left join (
                SELECT st.schaapId, max(h.hisId) hisId
                FROM tblStal st
                 join tblHistorie h on (st.stalId = h.stalId)
                WHERE h.kg is not null
                GROUP BY st.schaapId
             ) hkg on (hkg.schaapId = st.schaapId)
             left join tblHistorie lastkg on (lastkg.hisId = hkg.hisId)
            WHERE b.hokId = :hokId and isnull(uit.bezId)
            ORDER BY right(s.levensnummer,:Karwerk)
SQL;
        $args = [[':Karwerk', $Karwerk], [':hokId', $hokId, Type::INT]];
        return $this->run_query($sql, $args);
    }

}
