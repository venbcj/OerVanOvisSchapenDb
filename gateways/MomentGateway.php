<?php

class MomentGateway extends Gateway {

    public function kzlMoment($lidId) {
        return $this->run_query(
            <<<SQL
SELECT m.momId, moment, lower(if(isnull(scan),'6karakters',scan)) scan
FROM tblMoment m
 join tblMomentuser mu on (m.momId = mu.momId)
WHERE mu.lidId = :lidId
union
SELECT 3, 'uitval voor merken', 3 scan
FROM dual
ORDER BY momId
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

}
