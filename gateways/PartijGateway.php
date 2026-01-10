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

    public function zoek_vervoer($pId) {
        $sql = <<<SQL
                select v.vervId
                    from tblVervoer v
                     join tblPartij p on (v.partId = p.partId)
                    where p.partId = :pId
SQL;
        $args = [[':pId', $pId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function update($form) {
        $this->run_query(<<<SQL
    update tblPartij p set ubn = :ubn,
 naam = :naam,
 tel = :tel,
 fax = :fax,
 email = :mail,
 site = :site,
 banknr = :bank,
 relnr = :relnr,
 wachtw = :wawo
    where partId = :partId
SQL
        , $this->struct_to_args($form)
        );
    }

    public function insert_vervoer($partId, $kenteken, $aanhanger) {
        $this->run_query(<<<SQL
    insert into tblVervoer
    set partId = :partId, kenteken = :kenteken, aanhanger = :aanhanger
SQL
        , [[':partId', $partId, Type::INT], [':kenteken', $kenteken], [':aanhanger', $aanhanger]]
        );
    }

    public function wijzig_vervoer($partId, $kenteken, $aanhanger) {
        $this->run_query(<<<SQL
    update tblVervoer v
    set kenteken = :kenteken, aanhanger = :aanhanger
    where partId = :partId
SQL
        , [[':partId', $partId, Type::INT], [':kenteken', $kenteken], [':aanhanger', $aanhanger]]
        );
    }

    public function find($pId) {
        $sql = <<<SQL
        select p.partId, r.relId, relatie, ubn, naam, tel, fax, email, site, banknr, p.relnr, p.wachtw, kenteken, aanhanger 
        from tblPartij p
         join tblRelatie r on (p.partId = r.partId)
         left join tblVervoer v on (p.partId = v.partId) 
        where p.partId = :pId
SQL;
        $args = [[':pId', $pId, Type::INT]];
        return $this->run_query($sql, $args);
    }

    public function Relatie($pId) {
        $sql = <<<SQL
        select r.relId, relatie, ubn, naam, straat, nr, pc, plaats, tel, fax, email, site, banknr, p.actief actief_p, r.actief 
        from tblPartij p
         join tblRelatie r on (p.partId = r.partId)
         left join tblAdres a on (r.relId = a.relId) 
        where p.partId = :pId 
        order by actief desc, relatie desc
SQL;
        $args = [[':pId', $pId, Type::INT]];
        return $this->run_query($sql, $args);
    }

    public function zoek_straat($updId) {
        $sql = <<<SQL
    SELECT a.straat
        FROM tblPartij p
         join tblRelatie r on (p.partId = r.partId)
         join tblAdres a on (a.relId = r.relId)
        WHERE r.relId = :updId
SQL;
        $args = [[':updId', $updId, Type::INT]];
        return $this->first_field($sql, $args, '');
    }

    public function zoek_nr($updId) {
        $sql = <<<SQL
    SELECT a.nr
        FROM tblPartij p
         join tblRelatie r on (p.partId = r.partId)
         join tblAdres a on (a.relId = r.relId)
        WHERE r.relId = :updId
SQL;
        $args = [[':updId', $updId, Type::INT]];
        return $this->first_field($sql, $args, '');
    }

    public function zoek_relatie_afvoer($ubn_best) {
        $sql = <<<SQL
        SELECT r.relId
        FROM tblPartij p
         join tblRelatie r on (p.partId = r.partId)
        WHERE p.ubn = :ubn_best and r.relatie = 'deb'
SQL;
        $args = [[':ubn_best', $ubn_best]];
        return $this->first_field($sql, $args);
    }

    public function zoek_relatie_aanvoer($ubn_herk) {
        $sql = <<<SQL
    SELECT r.relId
    FROM tblPartij p
     join tblRelatie r on (p.partId = r.partId)
    WHERE p.ubn = :ubn_herk and r.relatie = 'cred'
SQL;
        $args = [[':ubn_herk', $ubn_herk]];
        return $this->first_field($sql, $args);
    }

}
