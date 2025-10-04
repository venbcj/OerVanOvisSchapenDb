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

}
