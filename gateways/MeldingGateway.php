<?php

class MeldingGateway extends Gateway {

    public function zoek_bestemming($recId) {
        return $this->first_field(
            <<<SQL
SELECT st.rel_best
FROM tblMelding m
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
WHERE m.meldId = :recId
SQL
        , [[':recId', $recId, Type::INT]]
        );
    }

    public function updateSkip($recId, $fldSkip) {
        $this->run_query(
            <<<SQL
UPDATE tblMelding SET skip = :skip, fout = NULL
WHERE meldId = :recId
SQL
        , [[':recId', $recId, Type::INT], [':skip', $fldSkip]]
        );
    }

    public function updateFout($recId, $wrong) {
        $this->run_query(
            <<<SQL
UPDATE tblMelding SET fout = :fout
WHERE meldId = :recId and skip <> 1
SQL
        , [[':recId', $recId, Type::INT], [':fout', $wrong]]
        );
    }

    // Aantal dieren goed geregistreerd om automatisch te kunnen melden. De datum mag hier niet liggen na de afvoerdatum.
    public function aantal_oke_Omnum($fldReqId) {
        return $this->first_field(
            <<<SQL
SELECT count(*) aant 
FROM tblMelding m
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (st.schaapId = s.schaapId)
 left join (
    SELECT schaapId, max(datum) datum 
    FROM tblHistorie h 
     join tblStal st on (h.stalId = st.stalId)
     join tblActie a on (h.actId = a.actId)
    WHERE a.af = 1 and h.skip = 0
    GROUP BY schaapId
 ) afv on (st.schaapId = afv.schaapId)
 left join (
    SELECT levensnummer, levensnummer_new, meldnr
    FROM impRespons
    WHERE reqId = :reqId and meldnr is not null
 ) rvomeldnr on (coalesce(rvomeldnr.levensnummer_new, rvomeldnr.levensnummer) = s.levensnummer)
WHERE m.reqId = :reqId
 and h.skip = 0
 and h.datum is not null
 and (h.datum <= afv.datum or isnull(afv.datum))
 and LENGTH(RTRIM(CAST(s.levensnummer AS UNSIGNED))) = 12 
 and m.skip <> 1
SQL
 , [[':reqId', $fldReqId, Type::INT]],
 false
        );
    }

    // Aantal dieren goed geregistreerd om automatisch te kunnen melden.
    public function aantal_oke_uitv($fldReqId, $nestHistorieDm) {
        return $this->first_field(
            <<<SQL
SELECT count(*) aant
FROM tblMelding m
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (st.schaapId = s.schaapId) 
 join (
    SELECT schaapId, max(datum) lastdatum 
    FROM ($nestHistorieDm) hd 
    WHERE hd.actId != 14
 and actie != 'Gevoerd'
 and actie not like '% gemeld'
    GROUP BY schaapId
 ) mhd on (st.schaapId = mhd.schaapId)
 join tblRelatie r on (r.relId = st.rel_best)
 join tblPartij p on (r.partId = p.partId)
 left join (
    SELECT levensnummer, levensnummer_new, meldnr
    FROM impRespons
    WHERE reqId = :reqId and meldnr is not null
 ) rvomeldnr on (coalesce(rvomeldnr.levensnummer_new, rvomeldnr.levensnummer) = s.levensnummer)
WHERE m.reqId = :reqId
 and h.datum is not null
 and h.datum >= mhd.lastdatum
 and h.datum <= curdate()
 and LENGTH(RTRIM(CAST(s.levensnummer AS UNSIGNED))) = 12 
 and p.ubn is not null    
 and m.skip <> 1
 and h.skip = 0                            
SQL
        , [[':reqId', $fldReqId, Type::INT]]
        );
    }

    public function aantal_oke_afv($reqId, $nestHistorieDm) {
        return $this->first_field(
            <<<SQL
SELECT count(*) aant
FROM tblMelding m
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (st.schaapId = s.schaapId)
 join ( 
    SELECT hd.stalId, schaapId, max(datum) lastdatum 
    FROM ($nestHistorieDm) hd
     left join tblActie a on (hd.actId = a.actId)
    WHERE (a.af = 0 or isnull(a.af))
 and hd.actie != 'Gevoerd'
 and hd.actie not like '% gemeld'
    GROUP BY hd.stalId, schaapId
 ) mhd on (st.stalId = mhd.stalId and s.schaapId = mhd.schaapId)
 left join (
    SELECT levensnummer, levensnummer_new, meldnr
    FROM impRespons
    WHERE reqId = :reqId and meldnr is not null
 ) rvomeldnr on (coalesce(rvomeldnr.levensnummer_new, rvomeldnr.levensnummer) = s.levensnummer)
WHERE m.reqId = :reqId
 and h.skip = 0
 and h.datum is not null
 and h.datum >= mhd.lastdatum
 and h.datum <= (curdate() + interval 3 day)
 and LENGTH(RTRIM(CAST(s.levensnummer AS UNSIGNED))) = 12 
 and st.rel_best is not null
 and m.skip <> 1
 and isnull(rvomeldnr.meldnr)
SQL
        , [[':reqId', $reqId, Type::INT]]
        );
    }

    public function insert($reqId, $hisId) {
        $this->run_query(
            <<<SQL
INSERT INTO tblMelding SET reqId = :reqId, hisId = :hisId
SQL
        , [[':reqId', $reqId, Type::INT], [':hisId', $hisId, Type::INT]]
        );
    }

    public function requests_list_for($lidId) {
        return $this->collect_list(<<<SQL
SELECT m.reqId
FROM tblMelding m
 join tblBezet b on (b.hisId = m.hisId)
 join tblHok h on (h.hokId = b.hokId)
WHERE h.lidId = :%lidId
GROUP BY m.reqId
ORDER BY m.reqId
SQL
        , ['lidId' => $lidId]
        );
    }

    public function list_for($lidId) {
        return $this->collect_list(<<<SQL
SELECT m.meldId
FROM tblMelding m
 join tblBezet b on (b.hisId = m.hisId)
 join tblHok h on (h.hokId = b.hokId)
WHERE h.lidId = :%lidId
ORDER BY m.meldId
SQL
        , ['lidId' => $lidId]
        );
    }

    public function delete_ids($ids) {
        $this->run_query(<<<SQL
DELETE FROM tblMelding WHERE :%meldId
SQL
        , ['meldId' => $ids]
        );
    }

}
