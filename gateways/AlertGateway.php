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

}
