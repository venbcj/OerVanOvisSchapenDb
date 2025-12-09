<?php

class RedenGateway extends Gateway {

    public function lijst_voor($lidId) {
        return $this->run_query(<<<SQL
SELECT reduId, reden
FROM tblReden r
 join tblRedenuser ru on (r.redId = ru.redId)
WHERE ru.lidId = :lidId
and r.actief = 1
and ru.pil = 1
ORDER BY reden
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function alle_lijst_voor($lidId) {
        return $this->run_query(
            <<<SQL
SELECT r.redId, r.reden
FROM tblReden r
join tblRedenuser ru on (r.redId = ru.redId) 
WHERE ru.lidId = :lidId
 and ru.uitval = 1
ORDER BY r.reden
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

}
