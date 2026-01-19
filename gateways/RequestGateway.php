<?php

class RequestGateway extends Gateway {

    // TODO #0004133 mogelijk misleidende naam, want haalt meer op dan alleen het kale record
    public function find($recId) {
        $vw = $this->db->query("
SELECT r.reqId, r.code, r.def, m.skip, m.fout, h.datum, s.levensnummer, s.geslacht, st.rel_herk, st.rel_best
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
WHERE m.meldId = '$recId'
");
$rec = $vw->fetch_assoc();
return $rec;
    }

    public function setDef($reqId, $def) {
        $this->db->query("
UPDATE tblRequest SET def = '".$this->db->real_escape_string($def)."'
WHERE reqId = '".$this->db->real_escape_string($reqId)."' ");
    }

    // wordt een aantal keer aangeroepen met steeds een andere fldcode...
    // zou ook de bundel ineens kunnen ophalen --BCB
    public function countPerCode($lidid, $fldCode) {
        $vw = $this->db->query("
SELECT count(*) aant
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = '".$this->db->real_escape_string($lidid)."'
 and h.skip = 0
 and isnull(r.dmmeld)
 and code = '".$this->db->real_escape_string($fldCode)."'
"); // Foutafhandeling zit in return FALSE
    if ($vw) {
        $row = $vw->fetch_assoc();
            return $row['aant'];
    }
    return false; // Foutafhandeling
    }

    public function zoekLaatsteResponse($lidId) {
return $this->db->query("
SELECT r.reqId, r.code
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
 left join(
    SELECT max(respId) respId, reqId
    FROM impRespons 
    GROUP BY reqId
    ) lr on (r.reqId = lr.reqId)
 left join impRespons rp on (rp.respId = lr.respId)
WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and (rp.def != 'J' or isnull(rp.meldnr)) and h.skip = 0
GROUP BY r.reqId
ORDER BY r.reqId
");
    }

    public function zoek_definitieve_afvoermelding($stalId) {
        $vw = $this->db->query("
SELECT count(h.hisId) defat
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
WHERE stalId = '".$this->db->real_escape_string($stalId)."' and h.skip = 1 and rq.def = 1 and m.skip = 0
");
return $vw->fetch_row()[0];
}

public function hasOpenRequests($lidId) {
    $field = $this->first_field(<<<SQL
SELECT count(*) aant
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = :lidId
 and h.skip = 0
 and r.dmmeld is null
 and coalesce(m.skip, 0) <> 1
SQL
    , [[':lidId', $lidId, Type::INT]]
    );
    return $field > 0;
}

public function zoek_open_request($lidId, $code) {
    return $this->first_field(
        <<<SQL
SELECT r.reqId
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h. hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u on (u.ubnId = st.ubnId)
WHERE u.lidId = :lidId
 and isnull(r.dmmeld)
 and r.code = :code
 and h.skip = 0
GROUP BY r.reqId
HAVING (count(r.reqId) < 60)
SQL
    , [[':lidId', $lidId, Type::INT], [':code', $code]]
    );
}

public function insert($lidId, $fldCode) {
    $this->run_query(
        <<<SQL
INSERT INTO tblRequest SET lidId_new = :lidId, code = :code
SQL
    , [[':lidId', $lidId, Type::INT], [':code', $fldCode]]
    );
    return $this->db->insert_id;
}

public function update($reqId) {
    $this->run_query(
        <<<SQL
UPDATE tblRequest SET lidId_new = NULL
where reqId = :reqId
SQL
    , [[':reqId', $reqId, Type::INT]]
    );
}

public function update_response_date($reqId, $dmresponse) {
    $this->run_query(
        <<<SQL
UPDATE tblRequest SET dmresponse = :dmresponse
where reqId = :reqId
SQL
    , [[':reqId', $reqId, Type::INT], [':dmresponse', $dmresponse]]
    );
}

public function findById($reqId) {
    return $this->first_record(
        <<<SQL
    SELECT r.def, r.code
    FROM tblRequest r
    WHERE reqId = :reqId
SQL
    , [[':reqId', $reqId, Type::INT]]
        , ['def' => null, 'code' => null]
    );
}

public function delete_ids($ids) {
    $this->run_query(<<<SQL
DELETE FROM tblRequest WHERE :%reqId
SQL
    , ['reqId' => $ids]
    );
}

}
