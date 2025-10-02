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

}
