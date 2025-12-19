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

    // had ook "lijst()" kunnen heten
    public function rassen($lidId) {
        return $this->run_query(
            <<<SQL
SELECT r.rasId, r.ras, lower(coalesce(ru.scan,'6karakters')) scan
FROM tblRas r
 join tblRasuser ru on (r.rasId = ru.rasId)
WHERE ru.lidId = :lidId and r.actief = 1 and ru.actief = 1
ORDER BY ras
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

}
