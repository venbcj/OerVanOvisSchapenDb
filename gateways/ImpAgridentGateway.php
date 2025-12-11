<?php

class ImpAgridentGateway extends Gateway {

    public function zoek_aantal_uit_reader($Id) {
        return $this->first_row(
            <<<SQL
SELECT toedat, toedat_upd
FROM impAgrident
WHERE Id = :id
SQL
        , [[':id', $Id]]
        );
    }

    public function update($id, $aantal) {
        $this->run_query(
            <<<SQL
UPDATE impAgrident set toedat_upd = :aantal WHERE Id = :id
SQL
        , [[':id', $recId, self::INT], [':aantal', $aantal]]
        );
    }

}
