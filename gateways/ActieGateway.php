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

}
