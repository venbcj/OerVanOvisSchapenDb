<?php

class ActieGateway extends Gateway {

    public function getList() {
        return $this->run_query(
            <<<SQL
SELECT actId, actie
FROM tblActie
SQL
        );
    }

    public function getListOp1() {
        return $this->run_query(
            <<<SQL
SELECT actId, actie
FROM tblActie
WHERE op = 1
ORDER BY actId
SQL
        );
    }

}
