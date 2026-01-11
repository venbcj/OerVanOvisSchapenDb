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
            [':periId', $periode_id, Type::INT],
            [':inkId', $inkId, Type::INT],
            [':nutat', $rest_ink_vrd],
            [':stdat', $stdat],
            [':datum', $toediendatum, Type::DATE],
            [':readerId', $readerid, Type::INT],
        ]
        );
    }

    public function zoek_afgeboekt_voer($recId) {
        $sql = <<<SQL
                SELECT round(sum(v.nutat*v.stdat),0) af
                FROM tblVoeding v
                WHERE v.inkId = :recId and isnull(periId)
SQL;
        $args = [[':recId', $recId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    // NOTE: tabel zou ook tblNuttig kunnen zijn. We kiezen nu voor een query alleen hier, met tabelparameter.
    // Twee queries zou ook kunnen.
    public function wijzig_voorraad($tabel, $recId, $updCorrat) {
        $sql = <<<SQL
                INSERT INTO :tabel set inkId = :recId, nutat = :updCorrat, stdat = 1, correctie = 1 
SQL;
        $args = [[':tabel', $tabel], [':recId', $recId, Type::INT], [':updCorrat', $updCorrat, Type::INT]];
        return $this->run_query($sql, $args);
    }

    public function zoek_ink_tblVoeding($recId, $inkId) {
        $sql = <<<SQL
        SELECT voedId, nutat
        FROM tblVoeding
        WHERE periId = :recId and inkId = :inkId
SQL;
        $args = [[':recId', $recId, Type::INT], [':inkId', $inkId, Type::INT]];
        return $this->first_row($sql, $args, [0, 0]);
    }

    public function update_kilo($newNutat, $voedId) {
        $sql = <<<SQL
     UPDATE tblVoeding SET nutat = :newNutat WHERE voedId = :voedId
SQL;
        $args = [[':newNutat', $newNutat, Type::INT], [':voedId', $voedId, Type::INT]];
        $this->run_query($sql, $args);
    }

    public function insert_tblVoeding($recId, $inkId, $verschil, $stdat) {
        $sql = <<<SQL
        INSERT INTO tblVoeding SET periId = :recId, inkId = :inkId, nutat = :verschil, stdat = :stdat
SQL;
        $args = [[':recId', $recId, Type::INT], [':inkId', $inkId, Type::INT], [':verschil', $verschil], [':stdat', $stdat, Type::INT]];
        $this->run_query($sql, $args);
    }

    public function update_kilo_periode($fldKilo, $recId) {
        $sql = <<<SQL
            UPDATE tblVoeding SET nutat = :fldKilo WHERE periId = :recId
SQL;
        $args = [[':fldKilo', $fldKilo], [':recId', $recId, Type::INT]];
        return $this->run_query($sql, $args);
    }

    public function hoeveel_inkIds($recId) {
        $sql = <<<SQL
            SELECT count(voedId) aant
            FROM tblVoeding
            WHERE periId = :recId
SQL;
        $args = [[':recId', $recId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function zoek_kg_laatste_inkId($recId) {
        $sql = <<<SQL
        SELECT v.voedId, v.nutat
        FROM tblVoeding v
         join (
            SELECT max(voedId) voedId
            FROM tblVoeding
            WHERE periId = :recId
         ) lv on (v.voedId = lv.voedId)
SQL;
        $args = [[':recId', $recId, Type::INT]];
        return $this->first_row($sql, $args, [0, 0]);
    }

    public function delete_voedId($last_v) {
        $sql = <<<SQL
    DELETE FROM tblVoeding WHERE voedId = :last_v
SQL;
        $args = [[':last_v', $last_v]];
        $this->run_query($sql, $args);
    }

    public function delete_voeding($recId) {
        $sql = <<<SQL
            DELETE FROM tblVoeding WHERE periId = :recId
SQL;
        $args = [[':recId', $recId, Type::INT]];
        $this->run_query($sql, $args);
    }

}
