<?php

class StalGateway extends Gateway {

    public function updateHerkomstByMelding($recId, $fldHerk) {
        $this->db->query("
            UPDATE tblStal st
             join tblHistorie h on (h.stalId = st.stalId)
             join tblMelding m on (m.hisId = h.hisId)
            set st.rel_herk = '".$this->db->real_escape_string($fldHerk)."' 
            WHERE m.meldId = '$recId'
            ");
    }

    public function updateBestemmingByMelding($recId, $fldBest) {
            $this->db->query("
            UPDATE tblStal st
             join tblHistorie h on (h.stalId = st.stalId)
             join tblMelding m on (m.hisId = h.hisId)
            set st.rel_best = '".$this->db->real_escape_string($fldBest)."'
            WHERE m.meldId = '$recId'
            ");
    }

    public function zoek_lege_stallijst($lidId) {
        return $this->first_field(<<<SQL
SELECT count(stalId) aant
FROM tblStal
WHERE lidId = :lidId
SQL
        , [[':lidId', $lidId, self::INT]]);
    }

    public function kzlOoien($lidId, $Karwerk) {
return $this->db->query("
SELECT st.stalId, st.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk) werknr, count(lam.schaapId) lamrn, concat(st.kleur,' ',st.halsnr) halsnr
FROM (
    SELECT max(stalId) stalId, schaapId
    FROM tblStal
    WHERE lidId = '".$this->db->real_escape_string($lidId)."'
    GROUP BY schaapId
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
      join tblHistorie h on (st.stalId = h.stalId)
      join tblActie a on (h.actId = a.actId)
     WHERE a.af = 1 and h.actId <> 10 and lidId = '".$this->db->real_escape_string($lidId)."'
     ) afv on (afv.stalId = st.stalId)
WHERE s.geslacht = 'ooi' and (isnull(afv.stalId) or afv.datum > date_add(curdate(), interval -2 month) )

GROUP BY st.stalId, st.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk)
ORDER BY right(s.levensnummer,$Karwerk), count(lam.schaapId)
");
    }

    public function zoek_laatste_stalId($lidId, $schaapId) {
        $vw = $this->db->query("
SELECT max(stalId) stalId
FROM tblStal
WHERE lidId = '".$this->db->real_escape_string($lidId)."' and schaapId = '".$this->db->real_escape_string($schaapId)."' 
");
return $vw->fetch_row()[0];
}

public function findLidByStal($stalId) {
    $vw = $this->db->query("
SELECT lidId
FROM tblStal
WHERE stalId = ".$this->db->real_escape_string($stalId)." 
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
WHERE st.lidId = '".$this->db->real_escape_string($lidId)."' and st.schaapId = '".$this->db->real_escape_string($schaapId)."' and isnull(st.rel_best)
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
    SELECT max(stalId) stalId
    FROM tblStal
    WHERE lidId = '".$this->db->real_escape_string($lidId)."' and schaapId = '".$this->db->real_escape_string($schaapId)."'
 ) mst
 join tblStal st on (mst.stalId = st.stalId)
");
if ($vw->num_rows) {
    return $vw->fetch_row();
}
return [null, null];
}

public function update_relbest($stalId, $rel_best) {
    $this->db->query("UPDATE tblStal set rel_best = '".$this->db->real_escape_string($rel_best)."' WHERE stalId = '".$this->db->real_escape_string($stalId)."' ");
}

public function countHisHok1324($lidId, $date) {
    $vw = $this->db->query(" select count(h.hisId) aant
        from tblStal st
        join tblHistorie h on (h.stalId = st.stalId)
        join tblBezetting b on (h.hisId = b.hisId)
        join tblPeriode p on (p.periId =b.periId)
        join tblHok hk on (hk.hokId =p.hokId)
        left join
        (
            select b.bezId, h1.hisId hisv, min(h2.hisId) hist
            from tblBezetting b
            join tblHistorie h1 on (b.hisId = h1.hisId)
            join tblActie a1 on (a1.actId = h1.actId)
            join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
            join tblActie a2 on (a2.actId = h2.actId)
            join tblStal st on (h1.stalId = st.stalId)
            join tblPeriode p on (p.periId = b.periId)
            where st.lidId = ".$this->db->real_escape_string($lidId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
            group by b.bezId, h1.hisId
    ) uit
    on (uit.bezId = b.bezId)
    left join tblHistorie ht on (ht.hisId = uit.hist)
    left join (
        select st.schaapId, h.datum
        from tblStal st
        join tblHistorie h on (st.stalId = h.stalId)
        where h.actId = 4
    ) hs on (hs.schaapId = st.schaapId)
    where hk.hokId = 1324 and h.datum <= '$date' and (ht.datum > '$date' or isnull(ht.datum)) and hs.datum <= '$date'
    ");
    return $vw->fetch_row()[0];
}

}
