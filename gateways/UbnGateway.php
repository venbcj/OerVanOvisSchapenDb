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

}
