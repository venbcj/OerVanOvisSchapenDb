<?php

class RelatieGateway extends Gateway {

    public function zoek_bestemming($last_stalId) {
        return $this->first_field("
SELECT r.partId
FROM tblStal st
 join tblRelatie r on (st.rel_best = r.relId)
WHERE st.stalId = '".$this->db->real_escape_string($last_stalId)."'
");
        }

    public function zoek_crediteur($partId) {
        return $this->first_field("
SELECT relId
FROM tblRelatie
WHERE partId = '".$this->db->real_escape_string($partId)."' and relatie = 'cred'
");
}

}
