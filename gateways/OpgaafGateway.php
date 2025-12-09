<?php

class OpgaafGateway extends Gateway {

    public function clear_history($recId) {
        $this->run_query(
            <<<SQL
UPDATE tblOpgaaf SET his = NULL WHERE opgId = :opgId
SQL
        , [[':opgId', $recId, self::INT]]
        );
    }

}
