<?php

class RedenGateway extends Gateway {

    public function alle_lijst_voor($lidId) {
        return $this->run_query(<<<SQL
SELECT reduId, reden
FROM tblReden r
 join tblRedenuser ru on (r.redId = ru.redId)
WHERE ru.lidId = :lidId
and ru.pil = 1
ORDER BY reden
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

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

    public function KV_uitval_lijst_voor($lidId) {
        return $this->KV($this->uitval_lijst_voor($lidId));
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

    public function pil_lijst_voor($lidId) {
        return $this->run_query(
            <<<SQL
SELECT ru.reduId, r.reden
FROM tblReden r
join tblRedenuser ru on (r.redId = ru.redId) 
WHERE ru.lidId = :lidId
 and ru.pil = 1
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

    public function pil_actief($lidId, $reduId) {
        return $this->first_row(<<<SQL
SELECT r.reden, ru.pil
FROM tblReden r
 join tblRedenuser ru on (r.redId = ru.redId)
WHERE ru.lidId = :lidId and ru.reduId = :reduId
SQL
        , [[':lidId', $lidId, self::INT], [':reduId', $reduId, self::INT]]
        , [null, null]
        );
    }

    public function kzlReden_combi($lidId, $reduId) {
        // Declaratie REDEN  Met union all kan ik een niet actieve (aangeduid als pil) reden toch tonen en kan dit (en andere) inactieve artikelen niet worden gekozen !!
        return $this->run_query(<<<SQL
SELECT u.reduId, u.reden
FROM (
    SELECT ru.reduId, r.reden
    FROM tblReden r
     join tblRedenuser ru on (r.redId = ru.redId)
    WHERE ru.lidId = :lidId and ru.uitval = 1 
   Union all
    SELECT ru.reduId, r.reden
    FROM tblReden r
     join tblRedenuser ru on (r.redId = ru.redId)
    WHERE ru.lidId = :lidId and ru.reduId = :reduId
) u
GROUP BY u.reduId, u.reden
ORDER BY u.reden
SQL
        , [[':lidId', $lidId, self::INT], [':reduId', $reduId, self::INT]]
        );
    }

    public function reden_actief($lidId, $reduId) {
        return $this->first_row(<<<SQL
SELECT r.reden, ru.uitval
FROM tblReden r
 join tblRedenuser ru on (r.redId = ru.redId)
WHERE ru.lidId = :lidId and ru.reduId = :reduId
SQL
        , [[':lidId', $lidId, self::INT], [':reduId', $reduId, self::INT]]
            , [null, null]
        );
    }

    public function delete_user($lidId) {
        $this->run_query(<<<SQL
DELETE FROM tblRedenuser WHERE lidId = :lidId
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

}
