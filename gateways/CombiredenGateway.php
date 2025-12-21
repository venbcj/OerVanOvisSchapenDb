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

    public function zoek_reden_medicijn($lidId) {
        return $this->run_query(
            <<<SQL
SELECT cr.scan, a.naam, round(cr.stdat) stdat, r.reden
from tblCombireden cr
 join tblArtikel a on (cr.artId = a.artId)
 join tblEenheiduser eu on (eu.enhuId = a.enhuId)
 left join tblRedenuser ru on (cr.reduId = ru.reduId)
 left join tblReden r on (r.redId = ru.redId)
where eu.lidId = :lidId
 and cr.tbl = 'p'
order by cr.scan
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

}
