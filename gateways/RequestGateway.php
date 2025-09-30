<?php

class RequestGateway extends Gateway {

    // TODO #0004133 mogelijk misleidende naam, want haalt meer op dan alleen het kale record
    public function find($recId) {
        $vw = mysqli_query($this->db, "
SELECT r.reqId, r.code, r.def, m.skip, m.fout, h.datum, s.levensnummer, s.geslacht, st.rel_herk, st.rel_best
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
WHERE m.meldId = '$recId'
");
$rec = mysqli_fetch_assoc($vw);
return $rec;
    }

    public function setDef($reqId, $def) {
        mysqli_query($this->db, "
UPDATE tblRequest SET def = '".mysqli_real_escape_string($this->db, $def)."'
WHERE reqId = '".mysqli_real_escape_string($this->db, $reqId)."' ");
    }

    // wordt een aantal keer aangeroepen met steeds een andere fldcode...
    // zou ook de bundel ineens kunnen ophalen --BCB
    public function countPerCode($lidid, $fldCode) {
        $vw = mysqli_query($this->db, "
SELECT count(*) aant
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($this->db, $lidid)."'
 and h.skip = 0
 and isnull(r.dmmeld)
 and code = '".mysqli_real_escape_string($this->db, $fldCode)."'
"); // Foutafhandeling zit in return FALSE
    if ($vw) {
        $row = mysqli_fetch_assoc($vw);
            return $row['aant'];
    }
    return false; // Foutafhandeling
    }

    public function zoekLaatsteResponse($lidId) {
return mysqli_query($this->db, "
SELECT r.reqId, r.code
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join(
    SELECT max(respId) respId, reqId
    FROM impRespons 
    GROUP BY reqId
    ) lr on (r.reqId = lr.reqId)
 left join impRespons rp on (rp.respId = lr.respId)
WHERE st.lidId = '".mysqli_real_escape_string($this->db, $lidId)."' and (rp.def != 'J' or isnull(rp.def)) and h.skip = 0
GROUP BY r.reqId
ORDER BY r.reqId
");
    }

}
