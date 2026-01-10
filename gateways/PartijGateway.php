<?php

class PartijGateway extends Gateway {

    public function findLeverancier($lidId) {
        $sql = <<<SQL
SELECT r.relId, p.naam
FROM tblPartij p
 join tblRelatie r on (p.partId = r.partId)
WHERE p.lidId = :lidId
 and relatie = 'cred'
 and p.actief = 1
 and r.actief = 1
ORDER BY p.naam
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->run_query($sql, $args);
    }

    public function findKlant($lidId) {
        return $this->run_query(
            <<<SQL
SELECT r.relId, concat(p.ubn, ' - ', p.naam) naam
FROM tblPartij p
join tblRelatie r on (r.partId = p.partId)
WHERE p.lidId = :lidId
and r.relatie = 'deb'
and p.actief = 1
and r.actief = 1
ORDER BY p.naam
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function relatienummers($lidId) {
        return $this->run_query(
            <<<SQL
SELECT relId, lower(coalesce(ubn,999999)) ubn, naam
FROM tblRelatie r
join tblPartij p on (r.partId = p.partId)
WHERE p.lidId = :lidId
and r.relatie = 'cred'
and ubn is not null
and isnull(r.uitval)
and r.actief = 1
and p.actief = 1
ORDER BY relatie
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function find_relatie($lidId) {
        return $this->run_query(
            <<<SQL
SELECT r.relId, '6karakters' ubn, concat(p.ubn, ' - ', p.naam) naam
FROM tblPartij p join tblRelatie r on (p.partId = r.partId)    
WHERE p.lidId = :lidId
 and relatie = 'deb'
 and p.actief = 1
 and r.actief = 1
 and isnull(p.ubn)
union
SELECT r.relId, p.ubn, concat(p.ubn, ' - ', p.naam) naam
FROM tblPartij p
 join tblRelatie r on (p.partId = r.partId)    
WHERE p.lidId = :lidId
 and relatie = 'deb'
 and p.actief = 1
 and r.actief = 1 
 and ubn is not null
ORDER BY naam
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function findNaam($partId) {
        return $this->first_field(
            <<<SQL
SELECT naam FROM tblPartij WHERE partId = :partId
SQL
        , [[':partId', $partId, Type::INT]]
        );
    }

    public function has_partij($lidId, $newPartij) {
        return (bool) $this->first_field(
            <<<SQL
SELECT EXISTS (
    SELECT naam
    FROM tblPartij
    WHERE lidId = :lidId
     and naam = :partij)
SQL
        , [[':lidId', $lidId, Type::INT],[':partij',$newPartij, Type::TXT]]
        );
    }

}
