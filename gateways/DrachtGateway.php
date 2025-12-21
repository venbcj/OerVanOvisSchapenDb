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

}
