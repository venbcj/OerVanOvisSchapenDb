<?php

class AdresGateway extends Gateway {

    public function zoek_adres($updId) {
        $sql = <<<SQL
        SELECT a.adrId
            FROM tblAdres a
            WHERE a.relId = :updId
SQL;
        $args = [[':updId', $updId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function invoeradres($updId) {
        $sql = <<<SQL
            INSERT INTO tblAdres
                SET relId = :updId
SQL;
        $args = [[':updId', $updId, Type::INT]];
        $this->run_query($sql, $args);
        return $this->db->insert_id;
    }

    public function wijzigstraat($fldStraat, $updId) {
        $sql = <<<SQL
            UPDATE tblAdres
                SET straat = :fldStraat 
                WHERE relId = :updId
SQL;
        $args = [[':fldStraat', $fldStraat, Type::INT], [':updId', $updId, Type::INT]];
        $this->run_query($sql, $args);
    }

    public function wijzignummer($fldNr, $updId) {
        $sql = <<<SQL
            UPDATE tblAdres
                SET nr = :fldNr
                WHERE relId = :updId
SQL;
        $args = [[':fldNr', $fldNr], [':updId', $updId, Type::INT]];
        $this->run_query($sql, $args);
    }

    public function wijzigpostcode($fldPc, $updId) {
        $sql = <<<SQL
            UPDATE tblAdres
                SET pc = :fldPc
                WHERE relId = :updId
SQL;
        $args = [[':fldPc', $fldPc], [':updId', $updId, Type::INT]];
        $this->run_query($sql, $args);
    }

    public function wijzigplaats($fldPlaats, $updId) {
        $sql = <<<SQL
            UPDATE tblAdres
                SET plaats = :fldPlaats
                WHERE relId = :updId
SQL;
        $args = [[':fldPlaats', $fldPlaats], [':updId', $updId, Type::INT]];
        $this->run_query($sql, $args);
    }

}
