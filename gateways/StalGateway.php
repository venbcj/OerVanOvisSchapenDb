<?php

class StalGateway extends Gateway {

    public function updateHerkomstByMelding($recId, $fldHerk) {
        $this->run_query(<<<SQL
            UPDATE tblStal st
             join tblHistorie h on (h.stalId = st.stalId)
             join tblMelding m on (m.hisId = h.hisId)
            set st.rel_herk = :fldHerk 
            WHERE m.meldId = :recId
SQL
        , [
            [':recId', $recId, self::INT],
            [':fldHerk', $fldHerk],
        ]);
    }

    public function updateBestemmingByMelding($recId, $fldBest) {
        $this->run_query(<<<SQL
            UPDATE tblStal st
             join tblHistorie h on (h.stalId = st.stalId)
             join tblMelding m on (m.hisId = h.hisId)
            set st.rel_best = :fldBest
            WHERE m.meldId = :recId
SQL
        , [
            [':recId', $recId, self::INT],
            [':fldBest', $fldBest],
        ]);
    }

    public function tel_stallijsten($lidId, $schaapId) {
        return $this->first_field(<<<SQL
SELECT count(st.stalId) stalId
FROM tblStal st
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE st.schaapId = :schaapId and u.lidId <> :lidId
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':schaapId', $schaapId, self::INT],
        ]);
    }

    public function kzlOoien($lidId, $Karwerk) {
        return $this->run_query(
            $this->kzl_ooien_statement(),
            [[':lidId', $lidId, self::INT], [':Karwerk', $Karwerk, self::INT]]
        );
    }

    public function ooien_invschaap($lidId, $Karwerk, $row_former) {
        $vw = $this->kzlOoien($lidId, $Karwerk);
        return $this->KV($vw, $row_former);
    }

    private function kzl_ooien_statement() {
        return <<<SQL
SELECT st.stalId, st.schaapId, s.levensnummer, right(s.levensnummer,:Karwerk) werknr, count(lam.schaapId) lamrn,
 concat(st.kleur,' ',st.halsnr) halsnr
FROM (
    SELECT max(st.stalId) stalId, st.schaapId
    FROM tblStal st
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE u.lidId = :lidId
    GROUP BY st.schaapId
 ) stm
 join tblStal st on (stm.stalId = st.stalId)
 join tblSchaap s on (st.schaapId = s.schaapId)
 join (
    SELECT schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) ouder on (ouder.schaapId = st.schaapId)
 left join tblVolwas v on (s.schaapId = v.mdrId)
 left join tblSchaap lam on (lam.volwId = v.volwId)
 left join (
    SELECT st.stalId, datum
    FROM tblStal st
     join tblUbn u on (st.ubnId = u.ubnId)
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (h.actId = a.actId)
    WHERE a.af = 1 and h.actId <> 10 and u.lidId = :lidId
     ) afv on (afv.stalId = st.stalId)
WHERE s.geslacht = 'ooi' and (isnull(afv.stalId) or afv.datum > date_add(curdate(), interval -2 month) )
GROUP BY st.stalId, st.schaapId, s.levensnummer, right(s.levensnummer,:Karwerk)
ORDER BY right(s.levensnummer,:Karwerk), count(lam.schaapId)
SQL
        ;
    }

    public function rammen_invschaap($lidId, $Karwerk, $row_former) {
        $vw = $this->kzlRammen($lidId, $Karwerk);
        return $this->KV($vw, $row_former);
    }

    public function kzlRammen($lidId, $Karwerk) {
        return $this->run_query(<<<SQL
SELECT st.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk) werknr, s.indx
FROM tblStal st 
join tblSchaap s on (st.schaapId = s.schaapId)
join tblHistorie h on (h.stalId = st.stalId)
WHERE s.geslacht = 'ram'
and h.actId = 3
and h.skip = 0
and lidId = :lidId
and not exists (
SELECT st.schaapId
FROM tblStal stal 
 join tblHistorie h on (h.stalId = stal.stalId)
 join tblActie  a on (a.actId = h.actId)
WHERE stal.schaapId = s.schaapId
and a.af = 1
and h.datum < DATE_ADD(CURDATE(), interval -1 year)
and h.skip = 0
and lidId = :lidId
)
ORDER BY right(s.levensnummer,$Karwerk)
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function zoek_laatste_stalId($lidId, $schaapId) {
        return $this->first_field(<<<SQL
SELECT max(st.stalId) stalId
FROM tblStal st
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = :lidId and st.schaapId = :schaapId 
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':schaapId', $schaapId, self::INT],
        ]);
    }

    public function findLidByStal($stalId) {
        return $this->first_field(
            <<<SQL
SELECT u.lidId
FROM tblStal st
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE st.stalId = :stalId
SQL
        , [[':stalId', $stalId, self::INT]]
        );
    }

    public function zoekKleurHalsnr($lidId, $schaapId) {
        return $this->first_record(<<<SQL
SELECT stalId, kleur, halsnr
FROM tblStal st
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = :lidId
 and st.schaapId = :schaapId
 and isnull(st.rel_best)
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':schaapId', $schaapId, self::INT],
        ], ['stalId' => null, 'kleur' => null, 'halsnr' => null]
        );
    }

    public function zoek_kleuren_halsnrs($lidId) {
        return $this->run_query(<<<SQL
SELECT schaapId, concat(kleur,' ',halsnr) halsnr
FROM tblStal st
INNER JOIN tblUbn u USING (ubnId)
WHERE u.lidId = :lidId and isnull(rel_best) and (kleur is not null or halsnr is not null)
ORDER BY concat(kleur,' ',halsnr)
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function updateKleurHalsnr($stalId, $kleur, $halsnr) {
        $this->run_query(<<<SQL
UPDATE tblStal set kleur = :kleur, halsnr = :halsnr WHERE stalId = :stalId
SQL
        , [
            [':stalId', $stalId, self::INT],
            [':kleur', $kleur],
            [':halsnr', $halsnr, self::INT],
        ]);
    }

    public function zoek_relid($lidId, $schaapId) {
        return $this->first_row(<<<SQL
SELECT st.stalId, st.rel_best
FROM (
    SELECT max(st.stalId) stalId
    FROM tblStal st
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE u.lidId = :lidId and st.schaapId = :schaapId
 ) mst
 join tblStal st on (mst.stalId = st.stalId)
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':schaapId', $schaapId, self::INT],
        ], [null, null]
        );
    }

    public function update_relbest($stalId, $rel_best) {
        $this->run_query($this->update_relbest_statement(),
            [[':stalId', $stalId, self::INT], [':rel_best', $rel_best]]
        );
    }

    private function update_relbest_statement() {
        return <<<SQL
UPDATE tblStal
SET rel_best = :rel_best
WHERE stalId = :stalId
SQL;
    }

    public function update_relbest_by_his($hisId, $rel_best) {
        $this->run_query(
            $this->update_relbest_by_his_statement(),
            [[':hisId', $hisId, self::INT], [':rel_best', $rel_best]]
        );
    }

    private function update_relbest_by_his_statement() {
        return <<<SQL
UPDATE tblStal st join tblHistorie h on (st.stalId = h.stalId)
SET st.rel_best = :rel_best
WHERE hisId = :hisId
SQL;
    }

    public function insert($lidId, $ubnId, $schaapId, $rel_herk) {
        $this->run_query(
            <<<SQL
INSERT INTO tblStal set lidId = :lidId, ubnId = :ubnId, schaapId = :schaapId, rel_herk = :rel_herk
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':ubnId', $ubnId, self::INT],
            [':schaapId', $schaapId, self::INT],
            [':rel_herk', $rel_herk],
        ]
        );
        return $this->db->insert_id;
    }

    public function insert_uitgebreid($lidId, $schaapId, $rel_herk, $ubnId, $kleur, $halsnr, $rel_best) {
        $this->run_query(<<<SQL
INSERT INTO tblStal SET
    lidId = :lidId,
 ubnId = :ubnId,
 schaapId = :schaapId,
 kleur = :kleur,
 halsnr = :halsnr,
 rel_herk = :rel_herk,
 rel_best = :rel_best
SQL
        ,
            [
                [':lidId', $lidId, self::INT],
                [':ubnId', $ubnId, self::INT],
                [':schaapId', $schaapId, self::INT],
                [':kleur', $kleur],
                [':halsnr', $halsnr],
                [':rel_herk', $rel_herk],
                [':rel_best', $rel_best],
            ]
        );
        return $this->db->insert_id;
    }

    public function zoek_laatste_stal($lidId, $schaapId) {
        return $this->run_query(<<<SQL
SELECT max(stalId) stalId
FROM tblStal st
INNER JOIN tblUbn u ON (st.ubnId = u.ubnId)
WHERE u.lidId = :lidId
 and st.schaapId = :schaapId
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':schaapId', $schaapId, self::INT],
        ]
        );
    }

    public function zoek_in_stallijst($lidId, $levnr) {
        return $this->first_field(
            <<<SQL
SELECT s.schaapId 
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 INNER JOIN tblUbn u USING(ubnId)
WHERE u.lidId = :lidId
 and levensnummer = :levnr
 and isnull(st.rel_best)
SQL
        ,
            [
                [':lidId', $lidId, self::INT],
                [':levnr', $levnr],
            ]
        );
    }

    public function zoek_in_afgevoerd($lidId, $levnr) {
        return $this->first_field(
            <<<SQL
SELECT s.schaapId 
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE st.lidId = :lidId
 and levensnummer = :levnr
 and st.rel_best is not null
 and (h.actId = 12 or h.actId = 13)
 and h.skip = 0
SQL
        ,
            [
                [':lidId', $lidId, self::INT],
                [':levnr', $levnr],
            ]
        );
    }

    public function zoek_dood($levnr) {
        return $this->first_field(
            <<<SQL
SELECT s.schaapId 
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE levensnummer = :levnr
 and h.actId = 14
 and h.skip = 0
SQL
        ,
            [
                [':levnr', $levnr],
            ]
        );
    }

    public function zoek_uitgeschaard($levnr) {
        return $this->first_field(
            <<<SQL
SELECT hisId
FROM tblHistorie h
 join (
     SELECT max(stalId) stalId
     FROM tblStal st
      join tblSchaap s on (s.schaapId = st.schaapId)
     WHERE levensnummer = :levnr
 ) st on (st.stalId = h.stalId) 
WHERE h.actId = 10 and h.skip = 0
SQL
        ,
            [
                [':levnr', $levnr],
            ]
        );
    }

    public function zoek_herkomst($hisId) {
        return $this->first_field(
            <<<SQL
SELECT rel_best
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId) 
WHERE h.hisId = :hisId
SQL
        ,
            [[':hisId', $hisId, self::INT]]
        );
    }

    public function startdm_moeder($lidId, $schaapId) {
        return $this->first_field(
            <<<SQL
SELECT h.datum
FROM (
    SELECT st.stalId
    FROM tblStal st
    INNER JOIN tblUbn u USING(ubnId)
    WHERE u.lidId = :lidId
        and isnull(rel_best)
        and schaapId = :schaapId
 ) minst
 join tblHistorie h on (minst.stalId = h.stalId)
 join tblActie a on (h.actId = a.actId)
WHERE a.op = 1 and h.skip = 0
SQL
        ,
            [
                [':lidId', $lidId, self::INT],
                [':schaapId', $schaapId, self::INT],
            ]
        );
    }

    public function zoek_eindm_mdr_indien_afgevoerd($lidId, $schaapId) {
        return $this->first_field(
            <<<SQL
SELECT h.datum
FROM (
    SELECT max(st.stalId) stalId, schaapId
    FROM tblStal st
    INNER JOIN tblUbn u USING (ubnId)
    WHERE u.lidId = :lidId
 and schaapId = :schaapId
    GROUP BY schaapId
 ) maxst
 join tblStal st on (st.stalId = maxst.stalId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblActie a on (h.actId = a.actId)
WHERE a.af = 1
SQL
        ,
            [
                [':lidId', $lidId, self::INT],
                [':schaapId', $schaapId, self::INT],
            ]
        );
    }
    
    public function findByLidWithoutBest($lidId, $recId) {
        return $this->first_field(
            <<<SQL
SELECT stalId
FROM tblStal st
WHERE isnull(st.rel_best)
 and st.schaapId = :schaapId
 and st.lidId = :lidId
SQL
        ,
            [[':lidId', $lidId, self::INT], [':schaapId', $recId, self::INT]],
            0
        );
    }

    public function zoek_stal($lidId, $schaapId) {
        return $this->first_field(
            <<<SQL
SELECT stalId
FROM tblStal st
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE schaapId = :schaapId
 and u.lidId = :lidId
 and isnull(rel_best)
SQL
        , [[':lidId', $lidId, self::INT], [':schaapId', $schaapId, self::INT]]
        );
    }

    public function getHokSpenenFrom() {
        return <<<SQL
tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblBezet b on (b.hisId = h.hisId)
 left join
 (
        SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
        FROM tblBezet b
         join tblHistorie h1 on (b.hisId = h1.hisId)
         join tblActie a1 on (a1.actId = h1.actId)
         join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
         join tblActie a2 on (a2.actId = h2.actId)
         join tblStal st on (h1.stalId = st.stalId)
        WHERE b.hokId = :hokId and st.lidId = :lidId and a1.aan = 1
         and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
        GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.bezId = b.bezId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId, h.datum, h.actId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
SQL;
    }

    public function getHokSpenenWhere($lidId, $hokId, $condition) {
        if ($condition) {
            $fiter = "WHERE b.hokId = :hokId and isnull(uit.bezId) and h.skip = 0 and (isnull(spn.schaapId) or prnt.schaapId is not null)";
        } else {
            $fiter = "WHERE b.hokId = :hokId and isnull(uit.bezId) and h.skip = 0 and isnull(spn.schaapId) and isnull(prnt.schaapId)";
        }
        return [
            $fiter,
            [
                [':hokId', $hokId, self::INT],
                [':lidId', $lidId, self::INT],
            ]
        ];
    }

    public function zoek_afvoerstatus_mdr($lidId, $schaapId) {
        return $this->first_field(
            <<<SQL
SELECT lower(a.actie) actie
FROM tblStal st
 join (
     SELECT max(stalId) stalId
     FROM tblStal
     WHERE lidId = :lidId
 and schaapId = :schaapId
 ) maxst on (maxst.stalId = st.stalId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblActie a on (a.actId = h.actId)
WHERE a.af = 1
 and h.actId != 10
 and h.skip = 0
SQL
        , [[':lidId', $lidId, self::INT], [':schaapId', $schaapId, self::INT]]
        );
    }

    public function zoek_terug_uitscharen($schaapId) {
        return $this->first_field(
            <<<SQL
SELECT st.stalId
FROM tblStal st
 join tblHistorie h on (h.stalId = st.stalId)
WHERE h.actId = 11 and st.schaapId = :schaapId
SQL
        , [[':schaapId', $schaapId, self::INT]]
        );
    }

    public function zoek_laatste_stal_medicijn($schaapId) {
        return $this->first_field(
            <<<SQL
SELECT max(stalId) stalId
FROM tblStal
WHERE schaapId = :schaapId
SQL
        , [[':schaapId', $schaapId, self::INT]]
        );
    }

    public function zoek_scan($stalId) {
        return $this->first_field(
            <<<SQL
SELECT scan
FROM tblStal
WHERE stalId = :stalId
SQL
        , [[':stalId', $stalId, self::INT]]
        );
    }

    public function is_dubbel($lidId, $scan) : bool {
        return $this->first_field(
            <<<SQL
SELECT count(*) FROM tblStal WHERE lidId=:lidId AND scan=:scan and scan IS NOT NULL and rel_best IS NULL
SQL
        , [[':lidId', $lidId, self::INT], [':scan', $scan]]
        ) > 0;
    }

    public function verwijder_scan_afgevoerden($lidId, $scan) {
        $this->run_query(
            <<<SQL
UPDATE tblStal SET scan = NULL WHERE lidId = :lidId and scan = :scan and rel_best is not null
SQL
        , [[':lidId', $lidId, self::INT], [':scan', $scan]]
        );
    }

    public function update_scan($stalId, $scan) {
        $this->run_query(
            <<<SQL
UPDATE tblStal SET scan = :scan WHERE stalId = :stalId
SQL
        , [[':stalId', $stalId, self::INT], [':scan', $scan]]
        );
    }

    public function jaargeboortes($lidId, $jaar) {
        return $this->first_field(
            <<<SQL
SELECT count(st.schaapId) aant
FROM tblStal st
 join tblHistorie h on (h.stalId = st.stalId)
WHERE h.actId = 1 and h.skip = 0 and date_format(h.datum,'%Y') = :jaar and st.lidId = :lidId
SQL
        , [[':lidId', $lidId, self::INT], [':jaar', $jaar]]
        );
    }

    public function jaarsterfte($lidId, $jaar) {
        return $this->first_field(
            <<<SQL
SELECT count(st.schaapId) aant
FROM tblStal st
 join tblHistorie h on (h.stalId = st.stalId)
 join (
     SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (h.stalId = st.stalId)
    WHERE h.actId = 1 and h.skip = 0 and date_format(h.datum,'%Y') = :jaar and st.lidId = :lidId
 ) geb on (geb.schaapId = st.schaapId)
WHERE h.actId = 14 and h.skip = 0 and date_format(h.datum,'%Y') = :jaar and st.lidId = :lidId
SQL
        , [[':lidId', $lidId, self::INT], [':jaar', $jaar]]
        );
    }

    public function zoek_startjaar_user($lidId) {
        $sql = <<<SQL
        SELECT date_format(min(dmcreatie),'%Y') jaar 
        FROM tblStal
        WHERE lidId = :lidId
SQL;
        $args = [[':lidId', $lidId]];
        return $this->first_field($sql, $args);
    }

}
