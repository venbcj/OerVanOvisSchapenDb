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

    public function rassenKV($lidId) {
        return $this->KV($this->rassen($lidId));
    }

    public function zoek_ras_bij($rasId, $lidId) {
        return $this->first_record(<<<SQL
SELECT ru.scan, ru.sort, ru.actief
FROM tblRas r
 join tblRasuser ru on (r.rasId = ru.rasId)
WHERE r.rasId = :rasId and ru.lidId = :lidId
SQL
        , [[':lidId', $lidId, self::INT], [':rasId', $rasId, self::INT]]
            , ['scan' => null, 'sort' => null, 'actief' => null]
        );
    }

    public function countScan($lidId, $scan) {
        return $this->first_field(<<<SQL
SELECT count(scan)
FROM tblRasuser
WHERE lidId = :lidId and scan = :scan and scan is not NULL
SQL
        , [[':lidId', $lidId, self::INT], [':scan', $scan]]
        );
    }

    public function updateScan($lidId, $scan, $rasId) {
        $this->run_query(<<<SQL
UPDATE tblRasuser SET scan = :scan WHERE lidId = :lidId and rasId = :rasId
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':rasId', $rasId, self::INT],
            [':scan', $scan],
        ]
        );
    }

    public function set_actief($rasId, $actief) {
        $this->run_query(<<<SQL
UPDATE tblRasuser SET actief = :actief WHERE rasId = :rasId
SQL
        , [[':rasId', $rasId, self::INT], [':actief', $actief]]
        );
    }

    public function delete_user($lidId) {
        $this->run_query(<<<SQL
            DELETE FROM tblRasuser WHERE lidId = :lidId
SQL
        , [
            [':lidId', $lidId, self::INT],
        ]
        );
    }

}
