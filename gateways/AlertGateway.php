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

    public function laatste_selectie($lidId): ?int {
        return $this->first_field(
            <<<SQL
SELECT max(volgnr) volgnr
FROM tblAlertselectie
WHERE lidId = :lidId
SQL
        , [[':lidId', $lidId, Type::INT]]
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

    public function insert($volgnr, $lidId, $transponder, $recId): void {
        $this->run_query(
            <<<SQL
INSERT INTO tblAlertselectie
set volgnr = :volgnr,
 lidId = :lidId,
 transponder = :transponder,
 alertId = :alertId 
SQL
        , [
            [':volgnr', $volgnr],
            [':lidId', $lidId, Type::INT],
            [':transponder', $transponder],
            [':alertId', $recId, Type::INT],
        ]
        );
    }

    public function zoek_aantal_selectie($volgnr): ?int {
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
