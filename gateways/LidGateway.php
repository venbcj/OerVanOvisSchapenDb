<?php

class LidGateway extends Gateway {

    // BV : Telt het aantal schapen op de stallijst van een gebruiker om te bepalen of er een stallijst bestaat
    public function zoek_lege_stallijst($lidId) {
        return $this->first_field(<<<SQL
SELECT count(st.stalId) aant
FROM tblStal st
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = :lidId
SQL
        , [[':lidId', $lidId, Type::INT]]);
    }

    public function rechten($lidId) {
        $vw = $this->run_query(
            <<<SQL
SELECT beheer, tech, fin, meld FROM tblLeden
WHERE lidId = :lidId
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
        if ($vw->num_rows) {
            return $vw->fetch_object();
        }
        return (object) [
            'beheer' => false,
            'tech' => false,
            'fin' => false,
            'meld' => false,
        ];
    }

    public function hasCompleteRvo($lidId) {
        $vw = $this->run_query(
            <<<SQL
SELECT 1
FROM tblLeden
WHERE lidId = :lidId
AND relnr IS NOT NULL
AND urvo IS NOT NULL
AND prvo IS NOT NULL
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
        return $vw->num_rows > 0;
    }

    public function findByUserPassword($user, $password) {
        return $this->first_record(
            <<<SQL
SELECT lidId, alias
FROM tblLeden 
WHERE login = :login
and passw = :passw
SQL
        ,
        [
            [':login', $user],
            [':passw', $password],
        ],
        []
        );
    }

    public function findByRas($rasnr) {
        return $this->first_field(
            <<<SQL
SELECT lidId
FROM tblRasuser
WHERE rasuId = :rasuId
SQL
        , [[':rasuId', $rasnr, Type::INT]]
        );
    }

    public function findByReaderkey($key) {
        return $this->first_field(
            <<<SQL
SELECT lidId from tblLeden where readerkey = :key
SQL
        ,
            [[':key', $key]]
        );
    }

    public function findLididByUbn($ubn) {
        return $this->first_field(
            <<<SQL
SELECT lidId FROM tblLeden WHERE ubn = :ubn
SQL
        , [[':ubn', $ubn]]
        );
    }

    public function countWithReaderkey($apikey) {
        return $this->first_field(
            <<<SQL
    SELECT count(*) aant FROM tblLeden WHERE readerkey = :readerkey
SQL
        , [[':readerkey', $apikey]]
        );
    }

    public function countUserByLoginPassw($login, $passw) {
        $count = $this->run_query(
            <<<SQL
SELECT login, passw FROM tblLeden 
WHERE login = :login
and passw = :passw
SQL
        ,
        [
            [':login', $login],
            [':passw', $passw],
        ]
        );
        return $count->num_rows;
    }

    public function update_username($lidId, $login) {
        $this->run_query(
            <<<SQL
UPDATE tblLeden SET login = :login
WHERE lidId = :lidId
SQL
        ,
        [
            [':login', $login],
            [':lidId', $lidId, Type::INT],
        ]
        );
    }

    public function findLoginPasswById($lidId) {
        return $this->first_record(
            <<<SQL
SELECT login AS user, passw AS passw
FROM tblLeden
WHERE lidId = :lidId
SQL
        , [[':lidId', $lidId, Type::INT]]
            , ['user' => null, 'passw' => null]
        );
    }

    public function findUbn($lidId) {
        return $this->first_field(
            <<<SQL
SELECT ubnId
FROM tblUbn
WHERE lidId = :lidId
 and actief = 1
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function findUbns($lidId) {
        return $this->run_query(
            <<<SQL
SELECT ubn
FROM tblUbn
WHERE lidId = :lidId
 and actief = 1
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function findAlias($lidId) {
        return $this->first_field(
            <<<SQL
SELECT alias FROM tblLeden WHERE lidId = :lidId
SQL
        , [[':lidId', $lidId, Type::INT]]
            , ''
        );
    }

    public function findIdByAlias($alias) {
        $vw = $this->run_query(
            <<<SQL
SELECT lidId FROM tblLeden WHERE alias = :alias
SQL
        , [[':alias', $alias]],
            ''
        );
    }

    public function countWithAlias($alias) {
        return $this->first_field(
            <<<SQL
SELECT count(*) aant FROM tblLeden WHERE alias = :alias
SQL
        , [[':alias', $alias]]
        );
    }

    // onderdelen van create-nieuw-lid

    public function createLambar($lidId) {
        $this->run_query(
            <<<SQL
INSERT INTO tblHok SET lidId=$lidId, hoknr='Lambar', actief=1
SQL
        ,
            [[':lidId', $lidId, Type::INT]]
        );
    }

    public function createMoments($lidId) {
        $this->run_query(
            <<<SQL
INSERT INTO tblMomentuser (lidId, momId)
SELECT :lidId, momId
FROM tblMoment
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function getMoments($lidId, $schaapId, $where) {
        return $this->run_query(
            <<<SQL
SELECT m.momId, m.moment
FROM tblMoment m
 join tblMomentuser mu on (m.momId = mu.momId)
WHERE $where
 and mu.lidId = :lidId
 and m.actief = 1
 and mu.actief = 1
union
SELECT m.momId, m.moment
FROM tblMoment m
 join tblSchaap s on (m.momId = s.momId)
WHERE s.schaapId = :schaapId
ORDER BY momId
SQL
        , [[':lidId', $lidId, Type::INT], [':schaapId', $schaapId, Type::INT]]
        );
    }

    public function createEenheden($lidId) {
        $this->run_query(
            <<<SQL
INSERT INTO tblEenheiduser (lidId, eenhId) SELECT :lidId, eenhId FROM tblEenheid
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function createElementen($lidId) {
        // TODO: #0004134  'waarde' heeft geen default, en mag niet null zijn. Dit kan niet werken. Ik zet er 0. Wat moet het zijn?
        $this->run_query(
            <<<SQL
INSERT INTO tblElementuser (elemId, lidId, waarde) SELECT elemId, :lidId, 0 FROM tblElement
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
        //een aantal elementen m.b.t. de saldoberekening worden standaard uitgezet
        $this->run_query(
            <<<SQL
UPDATE tblElementuser set sal = 0
            WHERE lidId = :lidId and elemId IN (2,3,4,5,67,8,10,11,14,15,17)
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function createPartij($lidId) {
        $this->run_query(
            <<<SQL
INSERT INTO tblPartij (lidId, ubn, naam, actief, naamreader ) VALUES
(:lidId, 123123, 'Rendac', 1, 'Rendac'),
(:lidId, 123456, 'Vermist', 1, 'Vermist');
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function createRelatie($lidId) {
        $this->run_query(
            <<<SQL
INSERT INTO tblRelatie (partId, relatie, uitval, actief)
SELECT p.partId, 'cred', 1, 1
FROM tblPartij p
WHERE p.ubn = '123123' and p.lidId = :lidId
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
        $this->run_query(
            <<<SQL
INSERT INTO tblRelatie (partId, relatie, actief)
SELECT p.partId, 'cred', 0
FROM tblPartij p
WHERE p.ubn = '123456' and p.lidId = :lidId
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function createRubriek($lidId) {
        $this->run_query(
            <<<SQL
INSERT INTO tblRubriekuser (rubId, lidId)
SELECT rubId, :lidId
FROM tblRubriek
ORDER BY rubId;
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    // Dit is eigenlijk geen Gateway-methode meer, maar een Transaction-methode
    public function storeUitvalOm($lidId, $redId) {
        if ($this->heeftReden($lidId, $redId)) {
            $SQL = <<<SQL
UPDATE tblRedenuser set uitval = 1 WHERE redId = :redId and lidId = :lidId
SQL;
        } else {
            $SQL = <<<SQL
INSERT INTO tblRedenuser set uitval=1, redId = :redId, lidId = :lidId
SQL;
        }
        $this->run_query($SQL, [[':lidId', $lidId, Type::INT], [':redId', $redId, Type::INT]]);
        return $this->db->insert_id;
    }

    // Dit is eigenlijk geen Gateway-methode meer, maar een Transaction-methode
    public function storeAfvoerOm($lidId, $redId) {
        if ($this->heeftReden($lidId, $redId)) {
            $SQL = <<<SQL
UPDATE tblRedenuser set afvoer = 1 WHERE redId = :redId and lidId = :lidId
SQL;
        } else {
            $SQL = <<<SQL
INSERT INTO tblRedenuser set afvoer = 1, redId = :redId, lidId = :lidId
SQL;
        }
        $this->run_query($SQL, [[':lidId', $lidId, Type::INT], [':redId', $redId, Type::INT]]);
        return $this->db->insert_id;
    }

    private function heeftReden($lidId, $redId) {
        $vw = $this->run_query(
            <<<SQL
SELECT reduId
FROM tblRedenuser
WHERE redId = :redId and lidId = :lidId
SQL
        , [
            [':redId', $redId, Type::INT],
            [':lidId', $lidId, Type::INT],
        ]
        );
        return $vw->num_rows > 0;
    }

    public function findReader($lidId) {
        // Bepalen welke reader wordt gebruikt
        return $this->first_field(
            <<<SQL
SELECT reader FROM tblLeden WHERE lidId = :lidId
SQL
        ,
            [[':lidId', $lidId, Type::INT]]
        );
    }

    public function findCrediteur($lidId) {
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

    public function zoek_startdatum($lidId) {
        return $this->first_field(
            <<<SQL
SELECT date_format(dmcreate,'%Y-%m-01') dmstart
FROM tblLeden
WHERE lidId = :lidId
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function toon_historie($lidId) {
        return $this->first_field(
            <<<SQL
SELECT histo FROM tblLeden WHERE lidId = :lidId
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function kzlRas($lidId) {
        return $this->run_query(
            <<<SQL
SELECT r.rasId, r.ras
FROM tblRas r
 join tblRasuser ru on (r.rasId = ru.rasId)
WHERE ru.lidId = :lidId and r.actief = 1 and ru.actief = 1
ORDER BY r.ras
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function kzlBestemming($lidId) {
        return $this->run_query(
            <<<SQL
SELECT r.relId, p.naam
FROM tblPartij p
 join tblRelatie r on (p.partId = r.partId)
WHERE p.lidId = :lidId and r.relatie = 'deb' and p.actief = 1 and r.actief = 1
ORDER BY p.naam
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function kzlHokkeuze($lidId) {
        return $this->run_query(
            <<<SQL
SELECT hokId, hoknr
FROM tblHok h
WHERE lidId = :lidId and actief = 1
ORDER BY hoknr
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
WHERE ru.lidId = :lidId and ru.sterfte = 1
ORDER BY r.reden
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function user_eenheden($lidId) {
        return $this->run_query(
            <<<SQL
select e.eenheid
from tblElement e
 join tblElementuser eu on (e.elemId = eu.elemId)
where eu.lidId = :lidId and (actief = 1 or eu.sal = 1)
group by e.eenheid
order by e.eenheid
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function user_componenten($lidId, $eenh) {
        return $this->run_query(
            <<<SQL
select eu.elemuId, e.element, eu.waarde, e.eenheid, eu.actief, eu.sal
from tblElement e
 join tblElementuser eu on (e.elemId = eu.elemId)
where eu.lidId = :lidId and e.eenheid = :eenheid and (actief = 1 or eu.sal = 1)
order by e.eenheid, e.element
SQL
        , [[':lidId', $lidId, Type::INT], [':eenheid', $eenh]]
        );
    }

    public function countInactiveComponents($lidId) {
        $vw = $this->run_query(
            <<<SQL
select count(elemuId) aant
from tblElementuser
where lidId = :lidId and actief = 0 and sal = 0
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
        return $vw->fetch_row()[0];
    }

    public function zoek_inactieve_componenten($lidId) {
        return $this->run_query(
            <<<SQL
select e.eenheid
from tblElement e
 join tblElementuser eu on (e.elemId = eu.elemId)
where eu.lidId = :lidId and eu.actief = 0 and sal = 0
group by e.eenheid
order by e.eenheid
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function user_inactieve_componenten($lidId, $eenh) {
        return $this->run_query(
            <<<SQL
select eu.elemuId, e.element, eu.waarde, eu.actief, eu.sal
from tblElement e
 join tblElementuser eu on (e.elemId = eu.elemId)
where eu.lidId = :lidId and e.eenheid = :eenheid and actief = 0 and sal = 0
order by element
SQL
        , [[':lidId', $lidId, Type::INT], [':eenheid', $eenh]]
        );
    }

    public function zoek_ingescand($lidId) {
        return $this->first_field(
            <<<SQL
    SELECT ingescand
    FROM tblLeden 
    WHERE lidId = :lidId
SQL
        , [[':lidId', $lidId, Type::INT]]
        ); 
    }

    public function get_data($lidId) {
        $result = $this->run_query(
            <<<SQL
SELECT l.lidId, l.roep, l.voegsel, l.naam, l.relnr, u.ubn, l.urvo, l.prvo, l.mail, l.tel,
date_format(l.ingescand,'%d-%m-%Y') ingescand, l.meld, l.tech, l.fin, l.beheer, l.reader, l.readerkey 
FROM tblLeden l
 join tblUbn u on (l.lidId = u.lidId)
WHERE l.lidId = :lidId
SQL
        , [[':lidId', $lidId, Type::INT]]
        ); 
        $columns = explode(' ', 'lidId roep voegsel naam relnr ubn urvo prvo mail tel ingescand meld tech fin beheer reader readerkey');
        $row = array_fill_keys($columns, '');
        if ($result->num_rows) {
            $row = $result->fetch_assoc();
        }
        return $row;
    }

    public function update_details($data) {
        $this->run_query(
            <<<SQL
UPDATE tblLeden SET 
            roep = :roep,
            voegsel = :voegsel,
            naam = :naam,
            relnr = :relnr,
            urvo = :urvo,
            prvo = :prvo,
            mail = :mail,
            tel = :tel,
            meld = :meld,
            tech = :tech,
            fin = :fin,
            beheer = :beheer,
            ingescand = :ingescand,
            reader = :reader
            WHERE lidId = :lidId
SQL
        , $this->struct_to_args($data)
        );
    }

    public function zoek_redenen_uitval($lidId) {
        return $this->first_field(
            <<<SQL
SELECT count(redId) aant
FROM tblRedenuser
WHERE redId in (8, 13, 22, 42, 43, 44) and uitval = 1 and lidId = :lidId
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function zoek_redenen_afvoer($lidId) {
        return $this->first_field(
            <<<SQL
SELECT count(redId) aant
FROM tblRedenuser
WHERE redId in (15, 45, 46, 47, 48, 49, 50, 51) and afvoer = 1 and lidId = :lidId
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function zoek_groei($lidId) {
        return $this->first_field(
            <<<SQL
SELECT groei FROM tblLeden WHERE lidId = :lidId
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function zoek_crediteur_vermist($lidId) {
        return $this->first_field(
            <<<SQL
SELECT relId
FROM tblPartij p
 join tblRelatie r on (r.partId = p.partId)
WHERE p.lidId = :lidId and p.naam = 'Vermist'
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function all() {
        return $this->run_query(
            <<<SQL
SELECT l.lidId, l.alias, l.login, l.roep, l.voegsel, l.naam, l.tel, l.mail, l.meld, l.tech, l.fin, l.beheer,
 date_format(laatste_inlog, '%d-%m-%Y %H:%i:%s') lst_i
FROM tblLeden l
ORDER BY l.lidId
SQL
        );
    }

    public function get_ubns_user($lidId){
        return $this->run_query(
            <<<SQL
SELECT ubn
FROM tblUbn
WHERE lidId = :lidId and actief = 1
SQL
        ,[[':lidId', $lidId, Type::INT]]
        );
    }

    public function update_password($lidId, $wwnew) {
        $this->run_query(
            <<<SQL
UPDATE tblLeden SET passw = :passw WHERE lidId = :lidId
SQL
        , [
            [':lidId', $lidId, Type::INT],
            [':passw', $wwnew],
        ]
        );
    }

    public function ubn_exists($ubn) {
        return 0 < $this->first_field(
            <<<SQL
SELECT count(*) aant FROM tblUbn WHERE ubn = :ubn
SQL
        , [[':ubn', $ubn]]
        );
    }

    public function store($ubn, $passw, $tel, $mail) {
        $this->run_query(
            <<<SQL
INSERT INTO tblLeden SET login = :ubn,
 passw = :passw,
 ubn = :ubn,
 meld = 0,
 tech = 1,
 fin = 1,
 tel = :tel,
 mail = :mail
SQL
        , [
            [':ubn', $ubn],
            [':passw', $passw],
            [':tel', $tel],
            [':mail', $mail],
        ]
        );
    }

    public function save_new($form) {
        $this->run_query(
            <<<SQL
INSERT INTO tblLeden SET alias = :alias, login = :login, passw = :passw,
                roep = :roep, voegsel = :voegsel, naam = :naam,
                relnr = :relnr, urvo = :urvo, prvo = :prvo,
                mail = :mail, tel = :tel, kar_werknr = :kar_werknr,
                actief = :actief, ingescand = :ingescand, beheer = :beheer,
                histo = :histo, meld = :meld, tech = :tech, fin = :fin,
                reader = :reader, readerkey = :readerkey
SQL
        , $this->struct_to_args($form)
        );
        return $this->db->insert_id;
    }

    public function update_formdetails($data) {
        $this->run_query(
            <<<SQL
UPDATE tblLeden SET relnr = :relnr, urvo = :urvo, prvo = :prvo, reader = :reader, kar_werknr = :kar_werknr,
 histo = :histo, groei = :groei
WHERE lidId = :lidId
SQL
        , $this->struct_to_args($data)
        );
    }

    public function get_form($lidId) {
        return $this->first_row(
            <<<SQL
SELECT lidId, relnr, urvo, prvo, kar_werknr, histo, groei
FROM tblLeden
WHERE lidId = :lidId
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function zoek_startdatum_klant($lidId) {
        $sql = <<<SQL
        SELECT date_format(dmcreate, '%Y-%m-%d') date
        FROM tblLeden
        WHERE lidId = :lidId
SQL;
        $args = [[':lidId', $lidId]];
        return $this->first_field($sql, $args);
    }

    public function zoek_karwerk($lidId)    {
        $sql = <<<SQL
        SELECT kar_werknr 
        FROM tblLeden
        WHERE lidId = :lidId
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->first_field($sql, $args);
    }

}
