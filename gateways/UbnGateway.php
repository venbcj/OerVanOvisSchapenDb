<?php

class UbnGateway extends Gateway {

    public function exists($ubn) {
        // zou ook met count() kunnen
        return 0 < $this->run_query(<<<SQL
SELECT ubn FROM tblUbn WHERE ubn = :ubn
SQL
        , [[':ubn', $ubn]])->num_rows;
    }

    public function exists_for_lid($ubn, $lidId) {
        return 0 < $this->first_field(<<<SQL
SELECT count(*) FROM tblUbn WHERE lidId = :lidId and ubn = :ubn
SQL
        , [[':lidId', $lidId, self::INT], [':ubn', $ubn, self::TXT]]);
    }

    public function insert($lidId, $ubn) {
        $this->run_query(<<<SQL
INSERT INTO tblUbn SET lidId = :lidId, ubn = :ubn
SQL
        , [[':lidId', $lidId, self::INT], [':ubn', $ubn]]);
    }

    public function insert_with_plaats($lidId, $new_ubn, $new_adres, $new_plaats) {
        $this->run_query(<<<SQL
INSERT INTO tblUbn SET lidId = :lidId, ubn = :ubn,
 adres = :adres, plaats = :plaats
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':ubn', $ubn], // default type is txt
            [':adres', $new_adres],
            [':plaats', $new_plaats],
        ]);
    }

    public function zoek_met_plaats($lidId) {
        return $this->run_query(<<<SQL
SELECT ubnId, ubn, adres, plaats, actief
FROM tblUbn
WHERE lidId = :lidId
ORDER BY actief desc, ubn
SQL
        , [[':lidId', $lidId, self::INT]]);
    }

    public function zoek_op_id_met_plaats($ubnId) {
        $vw = $this->run_query(<<<SQL
SELECT adres, plaats, actief
FROM tblUbn
WHERE ubnId = :ubnId
ORDER BY actief desc, ubn
SQL
        , [[':ubnId', $ubnId, self::INT]]);
        if ($vw->num_rows) {
            return $vw->fetch_assoc();
        }
        return [
            'adres' => '',
            'plaats' => '',
            'actief' => '',
        ];
    }

    public function zoek_relatie($ubnId) {
        return $this->first_field(<<<SQL
SELECT u.ubnId
FROM tblUbn u
 left join tblStal st on (st.ubnId = u.ubnId)
WHERE u.ubnId = :ubnId
 and isnull(st.stalId)
SQL
        , [[':ubnId', $ubnId, self::INT]]);
    }

    public function delete_by_id($ubnId) {
        $this->run_query("DELETE FROM tblUbn WHERE ubnId = :ubnId", [[':ubnId', $ubnId, self::INT]]);
    }

    # NOTE deze drie methoden kunnen gebundeld, als er een object is dat weet hoe de tabel in elkaar zit.
    # Ik wil niet klakkeloos alles als strings updaten namelijk.

    public function update_adres($ubnId, $adres) {
        $this->run_query("UPDATE tblUbn SET adres = :adres WHERE ubnId = :ubnId", [[':ubnId', $ubnId, self::INT], [':adres', $adres]]);
    }

    public function update_plaats($ubnId, $plaats) {
        $this->run_query("UPDATE tblUbn SET plaats = :plaats WHERE ubnId = :ubnId", [[':ubnId', $ubnId, self::INT], [':plaats', $plaats]]);
    }

    public function update_actief($ubnId, $actief) {
        // actief is tinyint in de tabel. Daar mag een bool in.
        $this->run_query("UPDATE tblUbn SET actief = :actief WHERE ubnId = :ubnId", [[':ubnId', $ubnId, self::INT], [':actief', $actief, self::BOOL]]);
    }

}
