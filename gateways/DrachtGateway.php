<?php

class DrachtGateway extends Gateway {

    public function insert_dracht($volwId, $hisId) {
        $this->run_query(
            <<<SQL
INSERT INTO tblDracht SET volwId = :volwId, hisId = :hisId
SQL
        ,
            [
                [':volwId', $volwId, Type::INT],
                [':hisId', $hisId, Type::INT],
            ]
        );
    }

    public function list_for($lidId) {
        return $this->collect_list(<<<SQL
SELECT d.draId
FROM tblDracht d
 join tblVolwas v on (d.volwId = v.volwId)
 join tblSchaap s on (v.volwId = s.volwId)
 join tblStal st on (s.schaapId = st.schaapId)
WHERE st.lidId = :lidId
ORDER BY d.draId
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function delete_ids($ids) {
        $this->run_query(<<<SQL
DELETE FROM tblDracht WHERE %draId
SQL
        , ['draId' => $ids]
        );
    }

    public function zoek_drachtdatum($recId) {
        $sql = <<<SQL
        SELECT h.hisId, h.datum
        FROM tblDracht d
         join tblHistorie h on (d.hisId = h.hisId)
        WHERE h.skip = 0 and d.volwId = :recId
SQL;
        $args = [[':recId', $recId, Type::INT]];
        return $this->first_row($sql, $args);
    }

    public function insert_tblDracht($recId, $hisId) {
        $sql = <<<SQL
    INSERT INTO tblDracht SET volwId = :recId, hisId = :hisId
SQL;
        $args = [[':recId', $recId, Type::INT], [':hisId', $hisId, Type::INT]];
        $this->run_query($sql, $args);
    }

    public function zoek_hisId($recId) {
        $sql = <<<SQL
    SELECT hisId
    FROM tblDracht
    WHERE volwId = :recId
SQL;
        $args = [[':recId', $recId, Type::INT]];
        return $this->first_field($sql, $args);
    }

}
