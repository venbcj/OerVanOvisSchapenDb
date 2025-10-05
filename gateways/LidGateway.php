<?php

class LidGateway extends Gateway {

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

    public function update_password($lidId, $passw) {
        $this->db->query("UPDATE tblLeden SET passw = '".$this->db->real_escape_string($passw)."' 
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

}
