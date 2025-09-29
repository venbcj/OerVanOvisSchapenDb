<?php

class HokGateway extends Gateway {

    public function kzlHok($lidId) {
        return $this->db->query("
SELECT hokId, hoknr
FROM tblHok
WHERE actief = 1 and lidId = '" . mysqli_real_escape_string($this->db,$lidId) . "' 
ORDER BY hoknr 
");
    }

}
