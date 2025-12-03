<?php

class VoedingGateway extends Gateway {

    public function inlezen($periode_id, $inkId, $rest_ink_vrd, $stdat, $toediendatum, $readerid) {
        $this->run_query(<<<SQL
INSERT INTO tblVoeding SET periId = :periId
, inkId = :inkId
, nutat = :nutat
, stdat = :stdat
, datum = :datum
, readerId = :readerId
SQL
        , [
            [':periId', $periode_id, self::INT],
            [':inkId', $inkId, self::INT],
            [':nutat', $rest_ink_vrd],
            [':stdat', $stdat],
            [':datum', $toediendatum, self::DATE],
            [':readerId', $readerid, self::INT],
        ]
        );
    }

}
