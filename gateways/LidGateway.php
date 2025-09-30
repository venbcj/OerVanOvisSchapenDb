<?php

class LidGateway extends Gateway {

    public function hasCompleteRvo($lidId) {
        $vw = mysqli_query($this->db, "
SELECT 1
FROM tblLeden
WHERE lidId = '".mysqli_real_escape_string($this->db, $lidId)."'
AND relnr IS NOT NULL
AND urvo IS NOT NULL
AND prvo IS NOT NULL
");
return mysqli_num_rows($vw) > 0;
    }

    public function findByUserPassword($user, $password) {
        $vw = mysqli_query($this->db, "
            SELECT lidId, alias
            FROM tblLeden 
            WHERE login = '".mysqli_real_escape_string($this->db, $user)."' and passw = '".mysqli_real_escape_string($this->db, $password)."'");
        if (mysqli_num_rows($vw) == 0) {
            return [];
        }
        return mysqli_fetch_assoc($vw);
    }

    public function findAlias($lidId) {
        $result = mysqli_query($this->db, "SELECT alias FROM tblLeden WHERE lidId = '".mysqli_real_escape_string($this->db, $lidId)."' ");
        while ($row = mysqli_fetch_assoc($result)) {
            return $row['alias'];
        }
        return '';
    }

    public function findIdByAlias($alias) {
        $result = mysqli_query($this->db, "SELECT lidId FROM tblLeden WHERE alias = '".mysqli_real_escape_string($this->db, $alias)."' ");
        while ($row = mysqli_fetch_assoc($result)) {
            return $row['lidId'];
        }
        return '';
    }

    // onderdelen van create-nieuw-lid

    public function createLambar($lidId) {
        mysqli_query($this->db, "INSERT INTO tblHok SET lidId=$lidId, hoknr='Lambar', actief=1");
    }

    public function createMoments($lidId) {
$insert_tblMomentuser = "INSERT INTO tblMomentuser (lidId, momId)
    SELECT '".mysqli_real_escape_string($this->db,$lidId)."', momId
    FROM tblMoment
    ";
        mysqli_query($this->db,$insert_tblMomentuser) or Logger::error(mysqli_error($this->db));
    }

    public function createEenheden($lidId) {
$insert_tblEenheiduser = "INSERT INTO tblEenheiduser (lidId, eenhId)
    SELECT '".mysqli_real_escape_string($this->db,$lidId)."', eenhId
    FROM tblEenheid
    ";
        mysqli_query($this->db,$insert_tblEenheiduser) or Logger::error(mysqli_error($this->db));
    }

    public function createElementen($lidId) {
        // TODO: #0004134  'waarde' heeft geen default, en mag niet null zijn. Dit kan niet werken. Ik zet er 0. Wat moet het zijn?
        $insert_tblElementuser = "INSERT INTO tblElementuser (elemId, lidId, waarde)
            SELECT elemId, '".mysqli_real_escape_string($this->db,$lidId)."', 0
            FROM tblElement
            ORDER BY elemId
";
        mysqli_query($this->db,$insert_tblElementuser) or Logger::error(mysqli_error($this->db));
        //een aantal elementen m.b.t. de saldoberekening worden standaard uitgezet
        $update_tblElementuser = "UPDATE tblElementuser set sal = 0
            WHERE lidId = '".mysqli_real_escape_string($this->db,$lidId)."' and elemId IN (2,3,4,5,67,8,10,11,14,15,17) ";
        mysqli_query($this->db,$update_tblElementuser) or Logger::error(mysqli_error($this->db));
    }

    public function createPartij($lidId) {
$insert_tblPartij = "INSERT INTO tblPartij (lidId, ubn, naam, actief, naamreader ) VALUES
(    '".mysqli_real_escape_string($this->db,$lidId)."', 123123, 'Rendac', 1, 'Rendac'),
(    '".mysqli_real_escape_string($this->db,$lidId)."', 123456, 'Vermist', 1, 'Vermist');
";
        mysqli_query($this->db,$insert_tblPartij) or Logger::error(mysqli_error($this->db));
    }

    public function createRelatie($lidId) {
        $insert_tblRelatie_Rendac = "INSERT INTO tblRelatie (partId, relatie, uitval, actief)
            SELECT p.partId, 'cred', 1, 1
            FROM tblPartij p
            WHERE p.ubn = '123123' and p.lidId = '".mysqli_real_escape_string($this->db,$lidId)."' ;
";
        mysqli_query($this->db,$insert_tblRelatie_Rendac) or Logger::error(mysqli_error($this->db));
        $insert_tblRelatie_Vermist = "INSERT INTO tblRelatie (partId, relatie, actief)
            SELECT p.partId, 'cred', 0
            FROM tblPartij p
            WHERE p.ubn = '123456' and p.lidId = '".mysqli_real_escape_string($this->db,$lidId)."' ;
";
        mysqli_query($this->db,$insert_tblRelatie_Vermist) or Logger::error(mysqli_error($this->db));
    }

    public function createRubriek($lidId) {
        $insert_tblRubriekuser = "INSERT INTO tblRubriekuser (rubId, lidId)
            SELECT rubId, '".mysqli_real_escape_string($this->db,$lidId)."'
            FROM tblRubriek
            ORDER BY rubId;
";
        mysqli_query($this->db,$insert_tblRubriekuser) or Logger::error(mysqli_error($this->db));
    }

    public function storeUitvalOm($lidId, $redId) {
        if ($this->heeftReden($lidId, $redId)) {
            $SQL = "UPDATE tblRedenuser set uitval = 1 WHERE redId = '".mysqli_real_escape_string($this->db,$redId)."' and lidId = '".mysqli_real_escape_string($this->db,$lidId)."' ";
        } else {
            $SQL = "INSERT INTO tblRedenuser set uitval=1, redId = '".mysqli_real_escape_string($this->db,$redId)."', lidId = '".mysqli_real_escape_string($this->db,$lidId)."' ";
        }
        mysqli_query($this->db,$SQL) or Logger::error(mysqli_error($this->db));
    }

    public function storeAfvoerOm($lidId, $redId) {
        if($this->heeftReden($lidId, $redId)) {
            $SQL = "UPDATE tblRedenuser set afvoer = 1 WHERE redId = '".mysqli_real_escape_string($this->db,$redId)."' and lidId = '".mysqli_real_escape_string($this->db,$lidId)."'";
        } else {
            $SQL = "INSERT INTO tblRedenuser set afvoer = 1, redId = '".mysqli_real_escape_string($this->db,$redId)."', lidId = '".mysqli_real_escape_string($this->db,$lidId)."'";
        }
        mysqli_query($this->db,$SQL) or Logger::error(mysqli_error($this->db));
    }

    public function heeftReden($lidId, $redId) {
        $vw = mysqli_query($this->db,"
SELECT redId
FROM tblRedenuser
WHERE redId = '".mysqli_real_escape_string($this->db,$redId)."' and lidId = '".mysqli_real_escape_string($this->db,$lidId)."'
") or die (mysqli_error($this->db));
return $vw->num_rows > 0;
    }

    public function findReader($lidId) {
        // Bepalen welke reader wordt gebruikt
        $result = mysqli_query($this->db, "SELECT reader FROM tblLeden WHERE lidId = '".mysqli_real_escape_string($this->db, $lidId)."' ;") or die(mysqli_error($this->db));
        while ($row = mysqli_fetch_assoc($result)) {
            return $row['reader'];
        }
    }

    public function findCrediteur($lidId) {
        $qryRendac = mysqli_query($this->db, "
    SELECT r.relId, p.ubn 
    FROM tblPartij p
     join tblRelatie r on (p.partId = r.partId)
    WHERE p.lidId = '".mysqli_real_escape_string($this->db, $lidId)."' and r.uitval = 1;");
    if ($qryRendac->num_rows > 0) {
        return $qryRendac->fetch_row();
    }
    return [null, null];
    }

    public function zoek_startdatum($lidId) {
        $vw = $this->db->query(" 
            SELECT date_format(dmcreate,'%Y-%m-01') dmstart
            FROM tblLeden
            WHERE lidId = '".mysqli_real_escape_string($this->db,$lidId)."'
            ");
        return $vw->fetch_row()[0];
    }

}
