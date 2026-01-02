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

    public function zoek_afgeboekt_voer($recId) {
        $sql = <<<SQL
                SELECT round(sum(v.nutat*v.stdat),0) af
                FROM tblVoeding v
                WHERE v.inkId = :recId and isnull(periId)
SQL;
        $args = [[':recId', $recId, self::INT]];
        return $this->first_field($sql, $args);
    }

    // NOTE: tabel zou ook tblNuttig kunnen zijn. We kiezen nu voor een query alleen hier, met tabelparameter.
    // Twee queries zou ook kunnen.
    public function wijzig_voorraad($tabel, $recId, $updCorrat) {
        $sql = <<<SQL
                INSERT INTO :tabel set inkId = :recId, nutat = :updCorrat, stdat = 1, correctie = 1 
SQL;
        $args = [[':tabel', $tabel], [':recId', $recId, self::INT], [':updCorrat', $updCorrat, self::INT]];
        return $this->run_query($sql, $args);
    }

}
