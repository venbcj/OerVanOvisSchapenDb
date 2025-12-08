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

}
