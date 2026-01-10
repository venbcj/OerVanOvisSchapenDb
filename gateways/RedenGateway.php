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
        , [[':lidId', $lidId, Type::INT]]
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
        , [[':lidId', $lidId, Type::INT]]
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
        , [[':lidId', $lidId, Type::INT]]
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
        , [[':lidId', $lidId, Type::INT]]
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
        , [[':lidId', $lidId, Type::INT]]
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
        , [[':lidId', $lidId, Type::INT]]
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
        , [[':lidId', $lidId, Type::INT], [':reduId', $reduId, Type::INT]]
        );
    }

    public function pil_actief($lidId, $reduId) {
        return $this->first_row(<<<SQL
SELECT r.reden, ru.pil
FROM tblReden r
 join tblRedenuser ru on (r.redId = ru.redId)
WHERE ru.lidId = :lidId and ru.reduId = :reduId
SQL
        , [[':lidId', $lidId, Type::INT], [':reduId', $reduId, Type::INT]]
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
        , [[':lidId', $lidId, Type::INT], [':reduId', $reduId, Type::INT]]
        );
    }

    public function reden_actief($lidId, $reduId) {
        return $this->first_row(<<<SQL
SELECT r.reden, ru.uitval
FROM tblReden r
 join tblRedenuser ru on (r.redId = ru.redId)
WHERE ru.lidId = :lidId and ru.reduId = :reduId
SQL
        , [[':lidId', $lidId, Type::INT], [':reduId', $reduId, Type::INT]]
            , [null, null]
        );
    }

    public function delete_user($lidId) {
        $this->run_query(<<<SQL
DELETE FROM tblRedenuser WHERE lidId = :lidId
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function controle($lidId, $redId) {
        $sql = <<<SQL
            SELECT count(redId) aantal
            FROM tblRedenuser
            WHERE lidId = :lidId and redId = :redId
            GROUP BY redId
SQL;
        $args = [[':lidId', $lidId, Type::INT], [':redId', $redId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function query_reden_toevoegen($lidId, $redId, $insUitv, $insPil, $insSterf) {
        $sql = <<<SQL
            INSERT INTO tblRedenuser SET
             lidId = :lidId,
             redId = :redId,
             uitval = :insUitv,
             pil = :insPil,
             sterfte = :insSterf
SQL;
        $args = [[':lidId', $lidId, Type::INT], [':redId', $redId, Type::INT], [':insUitv', $insUitv], [':insPil', $insPil], [':insSterf', $insSterf]];
        return $this->run_query($sql, $args);
    }

    public function qryReden($lidId) {
        $sql = <<<SQL
        SELECT r.redId, r.reden
        FROM tblReden r
         left join tblRedenuser ru on (ru.redId = r.redId and ru.lidId = :lidId)
        WHERE isnull(ru.redId) and r.actief = 1
        ORDER BY r.reden
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->run_query($sql, $args);
    }

    public function loop($lidId) {
        $sql = <<<SQL
        SELECT ru.reduId, r.redId, r.reden, ru.uitval, ru.pil, ru.afvoer, ru.sterfte
        FROM tblReden r
         join tblRedenuser ru on (r.redId = ru.redId)
        WHERE ru.lidId = :lidId
        ORDER BY if(uitval+pil = 2, 1 ,uitval+pil) desc, reden
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->run_query($sql, $args);
    }

    public function zoek_in_db($recId) {
        $sql = <<<SQL
     SELECT uitval, pil, afvoer, sterfte FROM tblRedenuser WHERE reduId = :recId
SQL;
        $args = [[':recId', $recId, Type::INT]];
        return $this->run_query($sql, $args);
    }

    public function update_uitv($fldUitv, $recId) {
        $sql = <<<SQL
            UPDATE tblRedenuser SET uitval = :fldUitv WHERE reduId = :recId
SQL;
        $args = [[':fldUitv', $fldUitv], [':recId', $recId, Type::INT]];
        $this->run_query($sql, $args);
    }

    public function update_pil($fldPil, $recId) {
        $sql = <<<SQL
            UPDATE tblRedenuser SET pil = :fldPil WHERE reduId = :recId
SQL;
        $args = [[':fldPil', $fldPil], [':recId', $recId, Type::INT]];
        $this->run_query($sql, $args);
    }

    public function update_afvoer($fldAfoer, $recId) {
        $sql = <<<SQL
            UPDATE tblRedenuser SET afvoer = :fldAfoer WHERE reduId = :recId
SQL;
        $args = [[':fldAfoer', $fldAfoer], [':recId', $recId, Type::INT]];
        $this->run_query($sql, $args);
    }

    public function update_sterfte($fldSterfte, $recId) {
        $sql = <<<SQL
            UPDATE tblRedenuser SET sterfte = :fldSterfte WHERE reduId = :recId
SQL;
        $args = [[':fldSterfte', $fldSterfte], [':recId', $recId, Type::INT]];
        $this->run_query($sql, $args);
    }

}
