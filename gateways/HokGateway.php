<?php

class HokGateway extends Gateway {

    public function findLongestHoknr($lidId) {
        return $this->first_field(<<<SQL
SELECT max(length(hoknr)) lengte FROM tblHok WHERE lidId = :lidId
SQL
    , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function findHoknrById($hokId) {
        return $this->first_field("SELECT hoknr FROM tblHok WHERE hokId = :hokId", [[':hokId', $hokId, Type::INT]]);
    }

    public function findSortActief($hokId) {
        return $this->first_row(<<<SQL
SELECT sort, actief
FROM tblHok
WHERE hokId = :hokId
SQL
        , [[':hokId', $hokId, Type::INT]]
        );
    }

    public function updateSort($hokId, $sort) {
        $this->run_query(<<<SQL
UPDATE tblHok SET sort = :sort WHERE hokId = :hokId 
SQL
        , [[':hokId', $hokId, Type::INT], [':sort', $sort]]
        );
    }

    public function is_aanwezig($lidId, $hok) {
        return 0 < $this->first_field(
            <<<SQL
SELECT count(*)
FROM tblHok
WHERE lidId = :lidId and hoknr = :hoknr
SQL
        , [[':lidId', $lidId, Type::INT], [':hoknr', $hok]]
        );
    }

    public function kzlHok($lidId) {
        return $this->run_query(
            <<<SQL
SELECT hokId, hoknr
FROM tblHok
WHERE actief = 1 and lidId = :lidId
ORDER BY hoknr 
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function kzlHokKV($lidId) {
        return $this->KV($this->kzlHok($lidId));
    }

    // TODO REFACTOR gebruik KV
    public function items_without_one($lidId, $hokId) {
        $vw = $this->run_query(
            <<<SQL
SELECT hokId, hoknr
FROM tblHok h
WHERE lidId = :lidId
 and hokId != :hokId
 and actief = 1
ORDER BY hoknr
SQL
        , [
            [':lidId', $lidId, Type::INT],
            [':hokId', $hokId, Type::INT],
        ]);
        if ($vw->num_rows == 0) {
            return [[], []];
        }
        $index = 0;
        $hoknum = [];
        while ($hnr = $vw->fetch_assoc()) { 
            $hoknId[$index] = $hnr['hokId']; 
            $hoknum[$index] = $hnr['hoknr'];
            $index++; 
        } 
        return [$hoknId, $hoknum];
    }

    public function lidIdByHokId($hok) {
        return $this->first_field(<<<SQL
SELECT lidId
FROM tblHok
WHERE hokId = :hokId
SQL
        , [[':hokId', $hok, Type::INT]]
        );
    }

    public function zoek_verblijf($lidId) {
        return $this->run_query(<<<SQL
SELECT hoknr, scan
FROM tblHok
WHERE actief = 1 and lidId = :lidId
ORDER BY hoknr
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    // lijkt erg op kzlHok
    public function hoknummer($lidId) {
        return $this->run_query(
            <<<SQL
SELECT hokId, hoknr, lower(if(isnull(scan),'6karakters',scan)) scan
FROM tblHok
WHERE lidId = :lidId and actief = 1
ORDER BY hoknr
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function countVerblijven($lidId, $artId, $doelId) {
        return $this->first_field(<<<SQL
SELECT count(p.periId) aant
FROM tblHok h
 join tblPeriode p on (p.hokId = h.hokId)
 left join tblVoeding v on (p.periId = v.periId)
 left join tblInkoop i on (i.inkId = v.inkId)
WHERE h.lidId = :lidId
 and i.artId = :artId
 and p.doelId = :doelId
SQL
        , [
            [':lidId', $lidId, Type::INT],
            [':artId', $artId, Type::INT],
            [':doelId', $doelId, Type::INT],
        ]);
    }

    public function kzlHokVoer($lidId, $artId) {
        return $this->run_query(<<<SQL
SELECT h.hokId, h.hoknr
FROM tblHok h
 join tblPeriode p on (p.hokId = h.hokId)
 left join tblVoeding v on (p.periId = v.periId)
 left join tblInkoop i on (i.inkId = v.inkId)
WHERE h.lidId = :lidId and i.artId = :artId
GROUP BY h.hoknr
SQL
        , [
            [':lidId', $lidId, Type::INT],
            [':artId', $artId, Type::INT],
        ]);
    }

    public function zoek_hok($schaapId) {
        return $this->first_field(<<<SQL
     SELECT hk.hoknr
     FROM tblHok hk
      join tblPeriode p on (hk.hokId = p.hokId)
      join tblBezet b on (p.periId = b.periId)
      join (
        SELECT max(bezId) bezId
        FROM tblBezet b
         join tblHistorie h on (b.hisId = h.hisId)
         join tblStal st on (st.stalId = h.stalId)
        WHERE h.skip = 0 and st.schaapId = :schaapId
      ) mb on (mb.bezId = b.bezId)
SQL
        , [
            [':schaapId', $schaapId, Type::INT],
        ]);
    }

    public function zoek_lid_hok($lidId) {
        return $this->run_query(
            <<<SQL
SELECT hokId
FROM tblHok
WHERE lidId = :lidId
ORDER BY sort, hokId
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function nummers_bij_lid($lidId) {
        return $this->run_query(
            <<<SQL
SELECT h.hokId, hoknr, lower(coalesce(scan,'6karakters')) scan
FROM tblHok h
WHERE h.lidId = :lidId
ORDER BY hoknr
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function actieve_nummers_bij_lid($lidId) {
        return $this->run_query(
            <<<SQL
SELECT h.hokId, hoknr, lower(coalesce(scan,'6karakters')) scan
FROM tblHok h
WHERE h.lidId = :lidId
 and actief = 1
ORDER BY hoknr
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    /* Binnen subquery hokIn zit een union t.b.v. doelId 3.
     *  In die Where cluse is h.datum >= prnt.datum toegepast
     *   i.p.v. (h.datum >= prnt.datum or ht.datum > prnt.datum)
     *    Schapen die in een verblijf een aanwasdatum krijgen worden niet meegeteld
     *    als doelgroep 3 zijnde 'Stallijst'. Deze vallen dus enkel in de doelgroep gespeend */
    public function resultaten($lidId) {
        return $this->run_query(
            <<<SQL
SELECT result.periId, h.hokId, h.hoknr, result.doelId, d.doel,
    min(result.dm_in) dmeerste_in, date_format(min(result.dm_in),'%Y%m%d') eertse_in_sort, date_format(min(result.dm_in),'%d-%m-%Y') eertse_in, 
        count(distinct result.schaapId) aant, 
        result.van dm_start_periode, date_format(result.van,'%Y%m%d') start_periode_sort, date_format(result.van,'%d-%m-%Y') start_periode,
        date_format(result.tot,'%Y%m%d') eind_periode_sort, date_format(result.tot,'%d-%m-%Y') eind_periode
FROM tblHok h
 join (
    SELECT hokIn.schaapId, hokIn.hokId, hokIn.doelId, hokIn.dm_in, periodes.periId, periodes.van, periodes.tot
    FROM (
        SELECT st.schaapId, b.hokId, 1 doelId, h.datum dm_in, ht.datum dm_uit
        FROM tblBezet b
         join tblHistorie h on (b.hisId = h.hisId)
         join tblStal st on (h.stalId = st.stalId)
         join tblUbn u on (u.ubnId = st.ubnId)
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
             join tblUbn u on (u.ubnId = st.ubnId)
            WHERE u.lidId = :lidId
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
        WHERE u.lidId = :lidId
 and h.skip = 0
 and ( (isnull(spn.datum)
 and isnull(prnt.datum)) or h.datum < spn.datum)
        UNION
        SELECT st.schaapId, b.hokId, 2 doelId, h.datum dm_in, ht.datum dm_uit
        FROM tblBezet b
         join tblHistorie h on (b.hisId = h.hisId)
         join tblStal st on (h.stalId = st.stalId)
         join tblUbn u on (u.ubnId = st.ubnId)
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
             join tblUbn u on (u.ubnId = st.ubnId)
            WHERE u.lidId = :lidId
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
        WHERE u.lidId = :lidId
 and h.skip = 0
 and ((h.datum >= spn.datum
 and (isnull(prnt.datum) or h.datum < prnt.datum)) or (isnull(spn.datum)
 and h.datum < prnt.datum))
        UNION
        SELECT st.schaapId, b.hokId, 3 doelId, h.datum dm_in, ht.datum dm_uit
        FROM tblBezet b
         join tblHistorie h on (b.hisId = h.hisId)
         join tblStal st on (h.stalId = st.stalId)
         join tblUbn u on (u.ubnId = st.ubnId)
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
             join tblUbn u on (u.ubnId = st.ubnId)
            WHERE u.lidId = :lidId
 and a2.uit = 1
 and h1.skip = 0
 and h2.skip = 0
 and h1.actId != 2
            GROUP BY b.bezId
         ) uit on (uit.bezId = b.bezId)
         left join tblHistorie ht on (ht.hisId = uit.hist)
         join (
            SELECT schaapId, datum
            FROM tblHistorie h
             join tblStal st on (st.stalId = h.stalId)
            WHERE actId = 3
 and skip = 0
         ) prnt on (st.schaapId = prnt.schaapId)
        WHERE u.lidId = :lidId
 and h.skip = 0
 and h.datum >= prnt.datum
    ) hokIn
     join (
        SELECT p2.hokId, p2.doelId, p.periId, '2000-01-01' van, p2.dmafsluit tot
        FROM  tblPeriode p
         join (
            SELECT p.hokId, p.doelId, min(p.dmafsluit) dmafsluit
            FROM tblPeriode p
             join tblHok h on (h.hokId = p.hokId)
            WHERE h.lidId = :lidId
            GROUP BY p.hokId, p.doelId
         ) p2 on (p.hokId = p2.hokId
 and p.doelId = p2.doelId
 and p.dmafsluit = p2.dmafsluit)
        UNION
        SELECT p1.hokId, p1.doelId, p2.periId, p1.dmafsluit dmafsluit1, p2.dmafsluit dmafsluit2
        FROM (
            SELECT p1.periId periId1, min(p2.periId) periId2
            FROM tblPeriode p1
             join tblHok h on (h.hokId = p1.hokId)
             join tblPeriode p2 on (p1.hokId = p2.hokId
 and p1.doelId = p2.doelId
 and p1.dmafsluit < p2.dmafsluit)
            WHERE h.lidId = :lidId
            GROUP BY p1.periId
         ) a
         join tblPeriode p1 on (a.periId1 = p1.periId)
         join tblPeriode p2 on (a.periId2 = p2.periId)
     ) periodes on (hokIn.hokId = periodes.hokId
 and hokIn.doelId = periodes.doelId)
    WHERE (hokIn.dm_in  < periodes.tot
 and (isnull(hokIn.dm_uit) or hokIn.dm_uit > periodes.van))
) result on (result.hokId = h.hokId)
 join tblDoel d on (d.doelId = result.doelId)
GROUP BY result.periId, result.hokId, h.hoknr, result.doelId, d.doel, result.van, result.tot
ORDER BY result.hokId, result.doelId, result.van
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function insert($lidId, $hoknr, $sort) {
        $this->run_query(
            <<<SQL
INSERT INTO tblHok 
SET lidId = :lidId,
  hoknr = :hoknr,
  sort = :sort
SQL
        , [
            [':lidId', $lidId, Type::INT],
            [':hoknr', $hoknr],
            [':sort', $sort],
        ]
        );
    }

    public function countHokRelatiesAlle($lidId, $actief) {
        return $this->first_field(
            <<<SQL
SELECT count(h.hokId) aant
FROM tblHok h
 left join tblBezet b on (h.hokId = b.hokId)
 left join tblPeriode p on (h.hokId = p.hokId)
WHERE h.lidId = :lidId
 and actief > :actief
 and isnull(b.hokId)
 and isnull(p.hokId)
SQL
        , [[':lidId', $lidId, Type::INT], [':actief', $actief]]
        );
    }

    public function zoekRelatiesAlle($lidId, $actief) {
        return $this->run_query(
            <<<SQL
SELECT hokId, hoknr, scan, sort, actief
FROM tblHok
WHERE lidId = :lidId
 and actief > :actief
ORDER BY coalesce(sort, hoknr)
SQL
        , [[':lidId', $lidId, Type::INT], [':actief', $actief]]
        );
    }

    public function zoek_relatie($hokId) {
        return $this->first_field(
            <<<SQL
SELECT h.hokId
FROM tblHok h
 left join tblBezet b on (h.hokId = b.hokId)
 left join tblPeriode p on (h.hokId = p.hokId)
WHERE h.hokId = :hokId
 and isnull(b.hokId)
 and isnull(p.hokId)
SQL
        , [[':hokId', $hokId, Type::INT]]
        );
    }

    public function hokn_beschikbaar($lidId, $hokId) {
        return $this->first_row(<<<SQL
SELECT hoknr, nu aantal FROM (
SELECT h.hokId, h.hoknr, coalesce(inhok.doelId,'niet in gebruik') doel, inhok.nu, h.scan
FROM tblHok h
left join (
    Select p.periId, p.hokId, p.doelId, count(b.bezId)-coalesce(uit.weg,0) nu
    From tblHok h
    join tblPeriode p on (h.hokId = p.hokId)
    join tblDoel d on (d.doelId = p.doelId)
    join tblBezet b on (p.periId = b.periId)
    left join (
        Select b.periId, count(uit.bezId) weg
        From tblBezet b
        join
        (
            select b.bezId, h1.hisId hisv, min(h2.hisId) hist
            from tblBezet b
             join tblHistorie h1 on (b.hisId = h1.hisId)
             join tblActie a1 on (a1.actId = h1.actId)
             join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
             join tblActie a2 on (a2.actId = h2.actId)
             join tblStal st on (h1.stalId = st.stalId)
             join tblUbn u on (u.ubnId = st.ubnId)
             join tblPeriode p on (p.periId = b.periId)
            where u.lidId = :lidId and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and isnull(p.dmafsluit)
            group by b.bezId, h1.hisId
        ) uit
        on (uit.bezId = b.bezId)
        Group by b.periId
    ) uit
    on (p.periId = uit.periId)
    Where h.lidId = :lidId and isnull(p.dmafsluit)
    Group by p.periId, p.hokId, p.doelId, uit.weg
) inhok
on (h.hokId = inhok.hokId)
WHERE h.lidId = :lidId and h.actief = 1
) hb WHERE hokId = :hokId
SQL
        , [[':lidId', $lidId, Type::INT], [':hokId', $hokId, Type::INT]]
        );
    }

    public function set_actief($hokId, $actief) {
        $this->run_query(<<<SQL
UPDATE tblHok SET actief = :actief WHERE hokId = :hokId
SQL
        , [[':hokId', $hokId, Type::INT], [':actief', $actief]]
        );
    }

    public function delete($hokId) {
        $this->run_query(<<<SQL
DELETE FROM tblHok WHERE hokId = :hokId
SQL
        , [[':hokId', $hokId, Type::INT]]
        );
    }

    public function zoek_lambar($lidId) {
        return $this->first_field(<<<SQL
SELECT hokId
FROM tblHok
WHERE hoknr = 'Lambar' and lidId = :lidId
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function insert_lambar($lidId) {
        $this->run_query(<<<SQL
INSERT INTO tblHok set hoknr = 'Lambar', lidId = :lidId
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
        return $this->db->insert_id;
    }

    public function zoek_verblijf_gegevens($hokId)    {
        $sql = <<<SQL
        SELECT hokId, hoknr
        FROM tblHok
        WHERE hokId = :hokId
SQL;
        $args = [[':hokId', $hokId, Type::INT]];
        return $this->run_query($sql, $args);
    }

}
