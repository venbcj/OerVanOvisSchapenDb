<?php

class EenheidGateway extends Gateway {

    public function findByLid($lidId) {
        return $this->db->query("
SELECT e.eenheid, eu.enhuId
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
WHERE eu.lidId = '".$this->db->real_escape_string($lidId)."' and eu.actief = 1
ORDER BY e.eenheid
");
}

}
