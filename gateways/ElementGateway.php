<?php

class ElementGateway extends Gateway {

    public function update($recId, $fldWaarde, $fldActief, $fldSalber) {
        $this->run_query(
            <<<SQL
UPDATE tblElementuser
SET waarde = :waarde,
 actief = :actief,
 sal = :sal
WHERE elemuId = :elemuId
SQL
        ,
            [
                [':waarde', $fldWaarde],
                [':actief', $fldActief], // moet dit self::BOOL zijn?
                [':sal', $fldSalber],
                [':elemuId', $recId, self::INT],
            ]
        );
    }

}
