<?php

class ImpResponseGateway extends Gateway {

    public function updateLevensnummer($from, $to) {
        $this->run_query(
            <<<SQL
UPDATE impRespons
set levensnummer = :to WHERE levensnummer = :from 
SQL
        , [
            [':to', $to],
            [':from', $from],
        ]);
    }

    public function zoek_status_response($reqId) {
        return $this->first_field(
            <<<SQL
    SELECT r.def
    FROM impRespons r
     join (
        SELECT max(respId) respId
        FROM impRespons
        WHERE reqId = :reqId
     ) lr on (r.respId = lr.respId)
SQL
        , [[':reqId', $reqId, Type::INT]]
        );
    }

    public function delete_ids($ids) {
        $this->run_query(<<<SQL
      <<<SQL
DELETE FROM impRespons WHERE :%reqId
SQL
        , ['reqId' => $ids]
        );
    }

}
