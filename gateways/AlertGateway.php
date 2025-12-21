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

}
