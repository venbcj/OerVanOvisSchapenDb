<?php

class PartijGateway extends Gateway {

    public function findLeverancier($lidId) {
        return $this->db->query("
SELECT r.relId, p.naam
FROM tblPartij p
 join tblRelatie r on (p.partId = r.partId)
WHERE p.lidId = '".$this->db->real_escape_string($lidId)."' and relatie = 'cred' and p.actief = 1 and r.actief = 1
ORDER BY p.naam
");
    }

}
