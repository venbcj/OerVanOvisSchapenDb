<?php

class StalGateway extends Gateway {

    public function updateHerkomstByMelding($recId, $fldHerk) {
        $this->run_query("
            UPDATE tblStal st
             join tblHistorie h on (h.stalId = st.stalId)
             join tblMelding m on (m.hisId = h.hisId)
            set st.rel_herk = '" . $this->db->real_escape_string($fldHerk) . "' 
            WHERE m.meldId = '$recId'
            ");
    }

    public function updateBestemmingByMelding($recId, $fldBest) {
            $this->db->query("
            UPDATE tblStal st
             join tblHistorie h on (h.stalId = st.stalId)
             join tblMelding m on (m.hisId = h.hisId)
            set st.rel_best = '" . $this->db->real_escape_string($fldBest) . "'
            WHERE m.meldId = '$recId'
            ");
    }

    public function tel_stallijsten($lidId, $schaapId) {
        return $this->first_field("
SELECT count(st.stalId) stalId
FROM tblStal st
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE st.schaapId = '" . $this->db->real_escape_string($schaapId) . "' and u.lidId <> '" . $this->db->real_escape_string($lidId) . "'");
    }

    public function kzlOoien($lidId, $Karwerk) {
        return $this->run_query(
            $this->kzl_ooien_statement(),
            [[':lidId', $lidId, self::INT], [':Karwerk', $Karwerk, self::INT]]
        );
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

    public function zoek_laatste_stalId($lidId, $schaapId) {
        $vw = $this->db->query("
SELECT max(st.stalId) stalId
FROM tblStal st
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and st.schaapId = '".$this->db->real_escape_string($schaapId)."' 
");
return $vw->fetch_row()[0];
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
    $vw = $this->db->query("
SELECT stalId, kleur, halsnr
FROM tblStal st
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = '".$this->db->real_escape_string($lidId)."'
 and st.schaapId = '".$this->db->real_escape_string($schaapId)."'
 and isnull(st.rel_best)
");
if ($vw->num_rows) {
    return $vw->fetch_assoc();
}
return ['stalId' => null, 'kleur' => null, 'halsnr' => null];
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
    $this->db->query("UPDATE tblStal set kleur = ". db_null_input($kleur) .", halsnr = ". db_null_input($halsnr)." WHERE stalId = '".$this->db->real_escape_string($stalId)."' ");
}

public function zoek_relid($lidId, $schaapId) {
    $vw = $this->db->query("
SELECT st.stalId, st.rel_best
FROM (
    SELECT max(st.stalId) stalId
    FROM tblStal st
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and st.schaapId = '".$this->db->real_escape_string($schaapId)."'
 ) mst
 join tblStal st on (mst.stalId = st.stalId)
");
if ($vw->num_rows) {
    return $vw->fetch_row();
}
return [null, null];
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

    public function insert($lidId, $schaapId, $rel_herk) {
        $this->db->query("INSERT INTO tblStal set lidId = '" . $this->db->real_escape_string($lidId) . "',
        schaapId = '" . $this->db->real_escape_string($schaapId) . "',
        rel_herk = '" . $this->db->real_escape_string($rel_herk) . "' ");
    }

    public function insert_uitgebreid() {
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

}
