<?php

class PartijGateway extends Gateway {

    public function findLeverancier($lidId) {
        return $this->run_query(
            <<<SQL
SELECT r.relId, p.naam
FROM tblPartij p
 join tblRelatie r on (p.partId = r.partId)
WHERE p.lidId = :lidId
 and relatie = 'cred'
 and p.actief = 1
 and r.actief = 1
ORDER BY p.naam
SQL
        , [[':lidId', $lidId, self::INT]]
        );
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
        , [[':lidId', $lidId, self::INT]]
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
        , [[':lidId', $lidId, self::INT]]
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
    , [[':lidId', $lidId, self::INT]]
    );
}

}
