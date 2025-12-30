<?php

class DrachtGateway extends Gateway {

    public function insert_dracht($volwId, $hisId) {
        $this->run_query(
            <<<SQL
INSERT INTO tblDracht SET volwId = :volwId, hisId = :hisId
SQL
        ,
            [
                [':volwId', $volwId, self::INT],
                [':hisId', $hisId, self::INT],
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
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function delete_ids($ids) {
        $this->run_query(<<<SQL
DELETE FROM tblDracht WHERE %draId
SQL
        , ['draId' => $ids]
        );
    }

}
