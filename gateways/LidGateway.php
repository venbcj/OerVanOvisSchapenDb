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
        , [[':lidId', $lidId, self::INT]]);
    }

    public function zoek_karwerk($lidId) {
        return $this->first_field(<<<SQL
SELECT kar_werknr 
FROM tblLeden
WHERE lidId = :lidId
SQL
        , [[':lidId', $lidId, self::INT]]);
    }

    public function rechten($lidId) {
        $vw = $this->db->query("SELECT beheer, tech, fin, meld FROM tblLeden WHERE lidId = '".$this->db->real_escape_string($lidId)."'; ");
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
        $vw = $this->db->query("
SELECT 1
FROM tblLeden
WHERE lidId = '".$this->db->real_escape_string($lidId)."'
AND relnr IS NOT NULL
AND urvo IS NOT NULL
AND prvo IS NOT NULL
");
return $vw->num_rows > 0;
    }

    public function findByUserPassword($user, $password) {
        $vw = $this->db->query("
            SELECT lidId, alias
            FROM tblLeden 
            WHERE login = '".$this->db->real_escape_string($user)."' and passw = '".$this->db->real_escape_string($password)."'");
        if ($vw->num_rows == 0) {
            return [];
        }
        return $vw->fetch_assoc();
    }

    public function findByRas($rasnr) {
        return $this->first_field(
            <<<SQL
SELECT lidId
FROM tblRasuser
WHERE rasuId = :rasuId
SQL
        , [[':rasuId', $rasnr, self::INT]]
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
        $count = $this->db->query("SELECT login, passw FROM tblLeden 
            WHERE login = '".$this->db->real_escape_string($login)."' 
            and passw = '".$this->db->real_escape_string($passw)."' ");
        return $count->num_rows;
    }

    public function update_username($lidId, $login) {
        $this->db->query("UPDATE tblLeden SET login = '".$this->db->real_escape_string($login)."' 
            WHERE lidId = '".$this->db->real_escape_string($lidId)."' ");
    }

    public function findLoginPasswById($lidId) {
        $vw = $this->db->query("
SELECT login AS user, passw AS passw
FROM tblLeden
WHERE lidId = '".$this->db->real_escape_string($lidId)."'
");
if ($vw->num_rows) {
        return $vw->fetch_assoc();
}
return ['user' => null, 'passw' => null];
    }

    public function findUbn($lidId) {
        return $this->first_field(
            <<<SQL
SELECT ubnId
FROM tblUbn
WHERE lidId = :lidId
 and actief = 1
SQL
        , [[':lidId', $lidId, self::INT]]
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
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function findAlias($lidId) {
        $vw = $this->db->query("SELECT alias FROM tblLeden WHERE lidId = '".$this->db->real_escape_string($lidId)."' ");
        while ($row = $vw->fetch_assoc()) {
            return $row['alias'];
        }
        return '';
    }

    public function findIdByAlias($alias) {
        $vw = $this->db->query("SELECT lidId FROM tblLeden WHERE alias = '".$this->db->real_escape_string($alias)."' ");
        while ($row = $vw->fetch_assoc()) {
            return $row['lidId'];
        }
        return '';
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
        $this->db->query("INSERT INTO tblHok SET lidId=$lidId, hoknr='Lambar', actief=1");
    }

    public function createMoments($lidId) {
$insert_tblMomentuser = "INSERT INTO tblMomentuser (lidId, momId)
    SELECT '".$this->db->real_escape_string($lidId)."', momId
    FROM tblMoment
    ";
        $this->db->query($insert_tblMomentuser);
    }

    public function getMoments($lidId, $schaapId, $where) {
        return $this->db->query("
SELECT m.momId, m.moment
FROM tblMoment m
 join tblMomentuser mu on (m.momId = mu.momId)
WHERE '" .$where. "'
 and mu.lidId = '".$this->db->real_escape_string($lidId)."'
 and m.actief = 1
 and mu.actief = 1

union

SELECT m.momId, m.moment
FROM tblMoment m
 join tblSchaap s on (m.momId = s.momId)
WHERE s.schaapId = '".$this->db->real_escape_string($schaapId)."'

ORDER BY momId");
    }

    public function createEenheden($lidId) {
$insert_tblEenheiduser = "INSERT INTO tblEenheiduser (lidId, eenhId)
    SELECT '".$this->db->real_escape_string($lidId)."', eenhId
    FROM tblEenheid
    ";
        $this->db->query($insert_tblEenheiduser);
    }

    public function createElementen($lidId) {
        // TODO: #0004134  'waarde' heeft geen default, en mag niet null zijn. Dit kan niet werken. Ik zet er 0. Wat moet het zijn?
        $insert_tblElementuser = "INSERT INTO tblElementuser (elemId, lidId, waarde)
            SELECT elemId, '".$this->db->real_escape_string($lidId)."', 0
            FROM tblElement
            ORDER BY elemId
";
        $this->db->query($insert_tblElementuser);
        //een aantal elementen m.b.t. de saldoberekening worden standaard uitgezet
        $update_tblElementuser = "UPDATE tblElementuser set sal = 0
            WHERE lidId = '".$this->db->real_escape_string($lidId)."' and elemId IN (2,3,4,5,67,8,10,11,14,15,17) ";
        $this->db->query($update_tblElementuser);
    }

    public function createPartij($lidId) {
$insert_tblPartij = "INSERT INTO tblPartij (lidId, ubn, naam, actief, naamreader ) VALUES
(    '".$this->db->real_escape_string($lidId)."', 123123, 'Rendac', 1, 'Rendac'),
(    '".$this->db->real_escape_string($lidId)."', 123456, 'Vermist', 1, 'Vermist');
";
        $this->db->query($insert_tblPartij);
    }

    public function createRelatie($lidId) {
        $insert_tblRelatie_Rendac = "INSERT INTO tblRelatie (partId, relatie, uitval, actief)
            SELECT p.partId, 'cred', 1, 1
            FROM tblPartij p
            WHERE p.ubn = '123123' and p.lidId = '".$this->db->real_escape_string($lidId)."' ;
";
        $this->db->query($insert_tblRelatie_Rendac);
        $insert_tblRelatie_Vermist = "INSERT INTO tblRelatie (partId, relatie, actief)
            SELECT p.partId, 'cred', 0
            FROM tblPartij p
            WHERE p.ubn = '123456' and p.lidId = '".$this->db->real_escape_string($lidId)."' ;
";
        $this->db->query($insert_tblRelatie_Vermist);
    }

    public function createRubriek($lidId) {
        $insert_tblRubriekuser = "INSERT INTO tblRubriekuser (rubId, lidId)
            SELECT rubId, '".$this->db->real_escape_string($lidId)."'
            FROM tblRubriek
            ORDER BY rubId;
";
        $this->db->query($insert_tblRubriekuser);
    }

    public function storeUitvalOm($lidId, $redId) {
        if ($this->heeftReden($lidId, $redId)) {
            $SQL = "UPDATE tblRedenuser set uitval = 1 WHERE redId = '".$this->db->real_escape_string($redId)."' and lidId = '".$this->db->real_escape_string($lidId)."' ";
        } else {
            $SQL = "INSERT INTO tblRedenuser set uitval=1, redId = '".$this->db->real_escape_string($redId)."', lidId = '".$this->db->real_escape_string($lidId)."' ";
        }
        $this->db->query($SQL);
    }

    public function storeAfvoerOm($lidId, $redId) {
        if($this->heeftReden($lidId, $redId)) {
            $SQL = "UPDATE tblRedenuser set afvoer = 1 WHERE redId = '".$this->db->real_escape_string($redId)."' and lidId = '".$this->db->real_escape_string($lidId)."'";
        } else {
            $SQL = "INSERT INTO tblRedenuser set afvoer = 1, redId = '".$this->db->real_escape_string($redId)."', lidId = '".$this->db->real_escape_string($lidId)."'";
        }
        $this->db->query($SQL);
    }

    public function heeftReden($lidId, $redId) {
        $vw = $this->db->query("
SELECT redId
FROM tblRedenuser
WHERE redId = '".$this->db->real_escape_string($redId)."' and lidId = '".$this->db->real_escape_string($lidId)."'
");
return $vw->num_rows > 0;
    }

    public function findReader($lidId) {
        // Bepalen welke reader wordt gebruikt
        $vw = $this->db->query("SELECT reader FROM tblLeden WHERE lidId = '".$this->db->real_escape_string($lidId)."' ;");
        while ($row = $vw->fetch_assoc()) {
            return $row['reader'];
        }
    }

    public function findCrediteur($lidId) {
        $vw = $this->db->query("
    SELECT r.relId, p.ubn 
    FROM tblPartij p
     join tblRelatie r on (p.partId = r.partId)
    WHERE p.lidId = '".$this->db->real_escape_string($lidId)."' and r.uitval = 1;");
    if ($vw->num_rows > 0) {
        return $vw->fetch_row();
    }
    return [null, null];
    }

    public function zoek_startdatum($lidId) {
        $vw = $this->db->query(" 
            SELECT date_format(dmcreate,'%Y-%m-01') dmstart
            FROM tblLeden
            WHERE lidId = '".$this->db->real_escape_string($lidId)."'
            ");
        return $vw->fetch_row()[0];
    }

    public function toon_historie($lidId) {
        $vw = $this->db->query("SELECT histo FROM tblLeden WHERE lidId = '".$this->db->real_escape_string($lidId)."' ");
        if ($vw->num_rows) {
            return $vw->fetch_row()[0];
        }
        return null;
    }

    public function kzlRas($lidId) {
        return $this->db->query("
SELECT r.rasId, r.ras
FROM tblRas r
 join tblRasuser ru on (r.rasId = ru.rasId)
WHERE ru.lidId = '".$this->db->real_escape_string($lidId)."' and r.actief = 1 and ru.actief = 1
ORDER BY r.ras
");
    }

public function kzlBestemming($lidId) {
    return $this->db->query("
SELECT r.relId, p.naam
FROM tblPartij p
 join tblRelatie r on (p.partId = r.partId)
WHERE p.lidId = '".$this->db->real_escape_string($lidId)."' and r.relatie = 'deb' and p.actief = 1 and r.actief = 1
ORDER BY p.naam
");
}

public function kzlHokkeuze($lidId) {
    return $this->db->query("
SELECT hokId, hoknr
FROM tblHok h
WHERE lidId = '".$this->db->real_escape_string($lidId)."' and actief = 1
ORDER BY hoknr
");
}

public function kzlReden($lidId) {
    return $this->db->query("
SELECT r.redId, r.reden
FROM tblReden r
 join tblRedenuser ru on (r.redId = ru.redId)
WHERE ru.lidId = '".$this->db->real_escape_string($lidId)."' and ru.sterfte = 1
ORDER BY r.reden
");
}

public function user_eenheden($lidId) {
    return $this->db->query("
select e.eenheid
from tblElement e
 join tblElementuser eu on (e.elemId = eu.elemId)
where eu.lidId = ".$this->db->real_escape_string($lidId)." and (actief = 1 or eu.sal = 1)
group by e.eenheid
order by e.eenheid
");
}

public function user_componenten($lidId, $eenh) {
    return $this->db->query("
select eu.elemuId, e.element, eu.waarde, e.eenheid, eu.actief, eu.sal
from tblElement e
 join tblElementuser eu on (e.elemId = eu.elemId)
where eu.lidId = ".$this->db->real_escape_string($lidId)." and e.eenheid = '".$this->db->real_escape_string($eenh)."' and (actief = 1 or eu.sal = 1)
order by e.eenheid, e.element
");
        }

public function countInactiveComponents($lidId) {
    $vw = $this->db->query("
select count(elemuId) aant
from tblElementuser
where lidId = ".$this->db->real_escape_string($lidId)." and actief = 0 and sal = 0
");
return $vw->fetch_row()[0];
}

public function zoek_inactieve_componenten($lidId) {
    return $this->db->query("
select e.eenheid
from tblElement e
 join tblElementuser eu on (e.elemId = eu.elemId)
where eu.lidId = ".$this->db->real_escape_string($lidId)." and eu.actief = 0 and sal = 0
group by e.eenheid
order by e.eenheid
");
    }

public function user_inactieve_componenten($lidId, $eenh) {
    return $this->db->query("
select eu.elemuId, e.element, eu.waarde, eu.actief, eu.sal
from tblElement e
 join tblElementuser eu on (e.elemId = eu.elemId)
where eu.lidId = ".$this->db->real_escape_string($lidId)." and e.eenheid = '".$this->db->real_escape_string($eenh)."' and actief = 0 and sal = 0
order by element
");
        }

public function zoek_ingescand($lidId) {
    $vw = $this->db->query("
    SELECT ingescand
    FROM tblLeden 
    WHERE lidId = '".$this->db->real_escape_string($lidId)."' ;
    "); 
    if ($vw->num_rows) {
        return $vw->fetch_row()[0];
    }
    return null;
    }

public function get_data($ID) {
    $result = $this->db->query("
SELECT l.lidId, l.roep, l.voegsel, l.naam, l.relnr, u.ubn, l.urvo, l.prvo, l.mail, l.tel,
date_format(l.ingescand,'%d-%m-%Y') ingescand, l.meld, l.tech, l.fin, l.beheer, l.reader, l.readerkey 
FROM tblLeden l
 join tblUbn u on (l.lidId = u.lidId)
WHERE l.lidId = '".$this->db->real_escape_string($ID)."' ;
"); 
$columns = explode(' ', 'lidId roep voegsel naam relnr ubn urvo prvo mail tel ingescand meld tech fin beheer reader readerkey');
$row = array_fill_keys($columns, '');
if ($result->num_rows) {
    $row = $result->fetch_assoc();
}
return $row;
}

public function update_details($ID, $data) {
    $this->db->query("UPDATE tblLeden SET 
        roep = '".$this->db->real_escape_string($data['txtRoep'])."',
        voegsel = ". db_null_input($data['txtVoeg']) . ",
        naam = '".$this->db->real_escape_string($data['txtNaam'])."',
        relnr = ". db_null_input($data['txtRelnr']) . ",
        urvo = ". db_null_input($data['txtUrvo']) . ",
        prvo = ". db_null_input($data['txtPrvo']) . ",
        mail = ". db_null_input($data['txtMail']) . ",
        tel = ". db_null_input($data['txtTel']) . ",
        meld = '".$this->db->real_escape_string($data['radMeld'])."',
        tech = '".$this->db->real_escape_string($data['radTech'])."',
        fin = '".$this->db->real_escape_string($data['radFin'])."',
        beheer = '".$this->db->real_escape_string($data['kzlAdm'])."',
        ingescand = '".$this->db->real_escape_string($data['lstScanDay'])."',
        reader = ". db_null_input($data['kzlReader']) . "
        WHERE lidId = '".$this->db->real_escape_string($ID)."'
        ;");
}

public function zoek_redenen_uitval($ID) {
    $vw =  $this->db->query("
SELECT count(redId) aant
FROM tblRedenuser
WHERE redId in (8, 13, 22, 42, 43, 44) and uitval = 1 and lidId = '".$this->db->real_escape_string($ID)."'
");
return $vw->fetch_row()[0];
    }

public function zoek_redenen_afvoer($ID) {
    $vw =  $this->db->query("
SELECT count(redId) aant
FROM tblRedenuser
WHERE redId in (15, 45, 46, 47, 48, 49, 50, 51) and afvoer = 1 and lidId = '".$this->db->real_escape_string($ID)."'
");
return $vw->fetch_row()[0];
    }

public function zoek_groei($lidId) {
   $vw = $this->db->query("SELECT groei FROM tblLeden WHERE lidId = '".$this->db->real_escape_string($lidId)."' ");
    while ( $gr = $vw->fetch_assoc()) { $groei = $gr['groei']; }
   return $groei;
}

public function zoek_crediteur_vermist($lidId) {
    $vw = $this->db->query("
SELECT relId
FROM tblPartij p
 join tblRelatie r on (r.partId = p.partId)
WHERE p.lidId = ".$this->db->real_escape_string($lidId)." and p.naam = 'Vermist'
");
while($zcv = $vw->fetch_assoc()) {
    $cred_vermist = $zcv['relId']; 
}
    return $cred_vermist ?? null;
}

public function all() {
    return $this->run_query(
        <<<SQL
SELECT l.lidId, l.alias, l.login, l.roep, l.voegsel, l.naam, u.ubn, l.tel, l.mail, l.meld, l.tech, l.fin, l.beheer,
 date_format(laatste_inlog, '%d-%m-%Y %H:%i:%s') lst_i
FROM tblLeden l
 join tblUbn u on (l.lidId = u.lidId)
ORDER BY l.lidId
SQL
    );
}

public function update_password($lidId, $wwnew) {
    $this->run_query(
        <<<SQL
UPDATE tblLeden SET passw = :passw WHERE lidId = :lidId
SQL
    , [
        [':lidId', $lidId, self::INT],
        [':passw', $wwnew],
    ]
    );
}

public function ubn_exists($ubn) {
    return 0 < $this->first_field(
        <<<SQL
SELECT count(*) aant FROM tblLeden WHERE ubn = :ubn
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
}

public function update_formdetails($lidId, $data) {
    $this->run_query(
        <<<SQL
UPDATE tblLeden SET relnr = :relnr, urvo = :urvo, prvo = :prvo, reader = :reader, kar_werknr = :kar_werknr,
 histo = :histo, groei = :groei
WHERE lidId = :lidId
SQL
    , [
        [':lidId', $lidId, self::INT],
        [':relnr', $data['txtRelnr']],
        [':urvo', $data['txtUrvo']],
        [':prvo', $data['txtPrvo']],
        [':reader', $data['kzlReader']],
        [':kar_werknr', $data['txtKarWerknr'], self::INT],
        [':histo', $data['kzlHis']],
        [':groei', $data['kzlGroei']],
    ]
    );
}

public function get_form($lidId) {
    return $this->first_row(
        <<<SQL
SELECT lidId, relnr, urvo, prvo, kar_werknr, histo, groei
FROM tblLeden
WHERE lidId = :lidId
SQL
    , [[':lidId', $lidId, self::INT]]
    );
}

}
