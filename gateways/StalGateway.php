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
    $vw = $this->db->query("
SELECT u.lidId
FROM tblStal
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE st.stalId = ".$this->db->real_escape_string($stalId)." 
");
if ($vw->num_rows) {
    return $vw->fetch_row()[0];
}
return null;
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

public function updateKleurHalsnr($stalId, $kleur, $halsnr) {
    $this->db->query("UPDATE tblStal set kleur = ". db_null_input($kleur) .", halsnr = ". db_null_input($halsnr)." WHERE stalId = '".$this->db->real_escape_string($stalId)."' ");
}

public function zoek_relid($lidId, $schaapId) {
    $vw = $this->db->query("
SELECT st.stalId, st.rel_best
FROM (
    SELECT max(st.stalId) stalId
    FROM tblStal
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

}
