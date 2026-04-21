<?php

class MomentGateway extends Gateway {

    public function kzlMoment($lidId) {
        return $this->run_query(
            <<<SQL
SELECT m.momId, moment
FROM tblMoment m
 join tblMomentuser mu on (m.momId = mu.momId)
WHERE mu.lidId = :lidId
union
SELECT 3, 'uitval voor merken'
FROM dual
ORDER BY momId
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function moment_invschaap($lidId) {
        return $this->run_query(<<<SQL
    SELECT m.momId, m.moment
    FROM tblMoment m
     join tblMomentuser mu on (m.momId = mu.momId)
    WHERE mu.lidId = :lidId
 and m.actief = 1
 and mu.actief = 1
    ORDER BY m.momId
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function kzlMoment_invschaap($lidId) {
        return $this->KV($this->moment_invschaap($lidId));
    }

    public function qry_lus($lidId) {
        $sql = <<<SQL
        SELECT momuId, mu.actief
        FROM tblMoment m
         join tblMomentuser mu on (m.momId = mu.momId)
        WHERE mu.lidId = :lidId and m.actief = 1
        ORDER BY momuId
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->run_query($sql, $args);
    }

    public function detail($Id) {
        $sql = <<<SQL
                SELECT moment, mu.actief
                FROM tblMoment m
                 join tblMomentuser mu on (m.momId = mu.momId)
                WHERE momuId = :Id and m.actief = 1
                ORDER BY moment
SQL;
        $args = [[':Id', $Id, Type::INT]];
        return $this->run_query($sql, $args);
    }


    public function zoek_actief($recId) {
        $sql = <<<SQL
    SELECT actief
    FROM tblMomentuser
    WHERE momuId = :recId
SQL;
        $args = [[':recId', $recId, Type::INT]];
        return $this->run_query($sql, $args);
    }

    public function update_actief($fldActief, $recId) {
        $sql = <<<SQL
            UPDATE tblMomentuser SET actief = :fldActief WHERE momuId = :recId
SQL;
        $args = [[':fldActief', $fldActief], [':recId', $recId, Type::INT]];
        return $this->run_query($sql, $args);
    }

}
