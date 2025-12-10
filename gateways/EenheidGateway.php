<?php

class EenheidGateway extends Gateway {

    public function findByLid($lidId) {
        return $this->run_query(
            <<<SQL
SELECT e.eenheid, eu.enhuId
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
WHERE eu.lidId = :lidId
 and eu.actief = 1
ORDER BY e.eenheid
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function all($lidId) {
        return $this->run_query(
            <<<SQL
select eu.enhuId
from tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
where eu.lidId = :lidId
order by e.eenheid
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function get($lidId, $id) {
        return $this->run_query(
            <<<SQL
select eenheid, eu.actief
from tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
where eu.lidId = :lidId
 and eu.enhuId = :enhuId
order by eenheid
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':enhuId', $id, self::INT],
        ]
        );
    }

}
