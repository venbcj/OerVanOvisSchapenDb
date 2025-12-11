<?php

class RasGateway extends Gateway {

    public function zoek_ras($lidId) {
        return $this->run_query(
            <<<SQL
SELECT ras, scan
FROM tblRas r
 join tblRasuser ru on (r.rasId = ru.rasId)
WHERE ru.actief = 1 and lidId = :lidId
ORDER BY sort, ras
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

}
