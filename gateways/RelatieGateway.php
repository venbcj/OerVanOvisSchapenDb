<?php

class RelatieGateway extends Gateway {

    public function zoek_bestemming($last_stalId) {
        return $this->first_field(
            <<<SQL
SELECT r.partId
FROM tblStal st
 join tblRelatie r on (st.rel_best = r.relId)
WHERE st.stalId = :stalId
SQL
        , [[':stalId', $last_stalId, Type::INT]]
        );
    }

    public function zoek_crediteur($partId) {
        return $this->first_field(
            <<<SQL
SELECT relId
FROM tblRelatie
WHERE partId = :partId
 and relatie = 'cred'
SQL
        , [[':partId', $partId, Type::INT]]
        );
    }

    public function zoek_postcode($updId) {
        $sql = <<<SQL
    SELECT a.pc
        FROM tblRelatie r
         join tblAdres a on (a.relId = r.relId)
        WHERE r.relId = :updId
SQL;
        $args = [[':updId', $updId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function zoek_plaats($updId) {
        $sql = <<<SQL
    SELECT a.plaats
        FROM tblRelatie r
         join tblAdres a on (a.relId = r.relId)
        WHERE r.relId = :updId
SQL;
        $args = [[':updId', $updId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function zoek_rendac($updId) {
        $sql = <<<SQL
    SELECT relId
        FROM tblRelatie r
         join tblPartij p on (r.partId = p.partId)
        WHERE r.relId = :updId and p.naam = 'Rendac'
SQL;
        $args = [[':updId', $updId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function zoek_actief($updId) {
        $sql = <<<SQL
        SELECT actief
            FROM tblRelatie
            WHERE relId = :updId
SQL;
        $args = [[':updId', $updId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function wijzigactief($fldActief, $updId) {
        $sql = <<<SQL
        UPDATE tblRelatie
            SET actief = :fldActief
            WHERE relId = :updId
SQL;
        $args = [[':fldActief', $fldActief], [':updId', $updId, Type::INT]];
        $this->run_query($sql, $args);
    }

}
