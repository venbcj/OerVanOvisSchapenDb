<?php

class RelatieGateway extends Gateway {

    public function zoekPartijId($relId) {
        return $this->first_field(
            <<<SQL 
SELECT partId
FROM tblRelatie
WHERE relId = :relId
SQL
        , [[':relId', $relId, Type::INT]] 
        );
    }

    public function zoek_bestemming($last_stalId) {
        return $this->first_row(
            <<<SQL
SELECT r.partId, ubnId
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

    public function keuzelijst_herkomst($lidId) {
        $sql = <<<SQL 
SELECT r.relId, concat(' - ', p.naam) naam
FROM tblRelatie r
 join tblPartij p USING(partId)
WHERE p.lidId = :lidId and p.actief = 1 and r.actief = 1 and r.relatie = 'cred' and p.ubn is null

UNION

SELECT r.relId, concat(p.ubn, ' - ', p.naam) naam
FROM tblRelatie r
 join tblPartij p USING(partId)
WHERE p.lidId = :lidId and p.actief = 1 and r.actief = 1 and r.relatie = 'cred' and p.ubn is not null and r.uitval is null
SQL;

$args = [[':lidId', $lidId, Type::INT]];

    return $this->run_query($sql, $args);

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

    public function findRendac($lidId) {
        return $this->first_row(
            <<<SQL
    SELECT r.relId, p.ubn 
    FROM tblPartij p
     join tblRelatie r on (p.partId = r.partId)
    WHERE p.lidId = :lidId and r.uitval = 1;
SQL
        , [[':lidId', $lidId, Type::INT]]
            , [null, null]
        );
    }

    public function zoekRelIdRendac($lidId) {
        return $this->first_field(
<<<SQL 
SELECT relId
FROM tblRelatie r
 join tblPartij p USING(partId)
WHERE p.naam = 'Rendac' and lidId = :lidId 
SQL
    , [['lidId', $lidId, Type::INT]]);

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
