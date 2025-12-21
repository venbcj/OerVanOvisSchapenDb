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

    public function uitval_lijst_voor($lidId) {
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

    // @TODO: afvoer-lijst gebruikt reduId, uitval-lijst gebruikt redId. Waarom dat verschil?
    public function afvoer_lijst_voor($lidId) {
        return $this->run_query(
            <<<SQL
SELECT ru.reduId, r.reden
FROM tblReden r
join tblRedenuser ru on (r.redId = ru.redId) 
WHERE ru.lidId = :lidId
 and ru.afvoer = 1
ORDER BY r.reden
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function kzlReden($lidId) {
        return $this->run_query(
            <<<SQL
SELECT r.redId, r.reden
FROM tblReden r
 join tblRedenuser ru on (r.redId = ru.redId) 
WHERE ru.lidId = :lidId
ORDER BY r.reden
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function zoek_reden_actief($lidId, $reduId) {
        return $this->first_field(
            <<<SQL
SELECT ru.pil
FROM tblRedenuser ru
WHERE ru.lidId = :lidId
 and ru.reduId = :reduId
SQL
        , [[':lidId', $lidId, self::INT], [':reduId', $reduId, self::INT]]
        );
    }

}
