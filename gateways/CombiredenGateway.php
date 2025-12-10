<?php

class CombiredenGateway extends Gateway {

    public function zoek_reden_uitval($lidId) {
        return $this->run_query(
            <<<SQL
SELECT cr.scan, r.reden
from tblCombireden cr
 join tblRedenuser ru on (cr.reduId = ru.reduId)
 join tblReden r on (r.redId = ru.redId)
where ru.lidId = :lidId
 and cr.tbl = 'd'
order by cr.scan
SQL
        , [[':lidId', $lidId, self::INT]]
        )->fetch_all();
    }

}
