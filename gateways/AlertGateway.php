<?php

class AlertGateway extends Gateway {

    public function all() {
        return $this->run_query(
            <<<SQL
SELECT Id, alert `name`
FROM tblAlert
WHERE actief = 1
SQL
        );
    }

    public function laatste_selectie($lidId) {
        return $this->first_field(
            <<<SQL
SELECT max(volgnr) volgnr
FROM tblAlertselectie
WHERE lidId = :lidId
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function transponders($volgnr) {
        return $this->run_query(
            <<<SQL
SELECT transponder tran, alertId Id
FROM tblAlertselectie
WHERE volgnr = :volgnr
SQL
        , [[':volgnr', $volgnr]]
        );
    }

    public function insert($volgnr, $lidId, $transponder, $recId) {
        $this->run_query(
            <<<SQL
INSERT INTO tblAlertselectie
set volgnr = '".mysqli_real_escape_string($db,$volgnr)."',
 lidId = '".mysqli_real_escape_string($db,$lidId)."',
 transponder = '".mysqli_real_escape_string($db,$transponder)."',
 alertId = '".mysqli_real_escape_string($db,$recId)."' 
SQL
        , [
            [':volgnr', $volgnr],
            [':lidId', $lidId, self::INT],
            [':transponder', $transponder],
            [':alertId', $recId, self::INT],
        ]
        );
    }

    public function zoek_aantal_selectie($volgnr) {
        return $this->first_field(
            <<<SQL
SELECT count(Id) aant
FROM tblAlertselectie
WHERE volgnr = :volgnr
SQL
        , [[':volgnr', $volgnr]]
        );
    }

}
