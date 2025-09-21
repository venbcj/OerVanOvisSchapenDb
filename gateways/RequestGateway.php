<?php

class RequestGateway extends Gateway {

    // TODO mogelijk misleidende naam, want haalt meer op dan alleen het kale record
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

    public function setDef($def, $reqId) {
        mysqli_query($this->db, "
UPDATE tblRequest SET def = '".mysqli_real_escape_string($this->db, $def)."'
WHERE reqId = '".mysqli_real_escape_string($this->db, $reqId)."' ");
    }

}
