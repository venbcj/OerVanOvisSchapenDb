<?php

class DeklijstGateway extends Gateway {

    public function insert($lidId, $datum) {
        $this->run_query(
            <<<SQL
INSERT INTO tblDeklijst
SET lidId = :lidId
dmdek = :dmdek
SQL
        ,
            [
                [':lidId', $lidId, self::INT],
                [':dmdek', $datum, self::DATE],
            ]
        );
    }

    public function find_aantal($dekId) {
        return $this->first_field(
            <<<SQL
SELECT dekat
FROM tblDeklijst 
WHERE dekId = :dekId
SQL
        , [[':dekId', $dekId, self::INT]]
        );
    }

    public function update($dekId, $flddekat) {
        $this->run_query(
            <<<SQL
UPDATE tblDeklijst SET dekat = ".db_null_input($flddekat)." WHERE dekId = '".mysqli_real_escape_string($db,$recId)."'
SQL
        , [[':dekat', $dekat], [':dekId', $dekId]]
        );
    }

}
