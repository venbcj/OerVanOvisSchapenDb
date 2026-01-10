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
                [':actief', $fldActief], // moet dit Type::BOOL zijn?
                [':sal', $fldSalber],
                [':elemuId', $recId, Type::INT],
            ]
        );
    }

    public function zoek_prijs_lam($lidId) {
        return $this->first_row(
            <<<SQL
SELECT e.element, eu.waarde
FROM tblElement e
 join tblElementuser eu on (e.elemId = eu.elemId)
WHERE eu.lidId = :lidId and e.elemId = 10
SQL
        , [[':lidId', $lidId, Type::INT]]
            , [null, null]
        );
    }

    public function zoek_worpgrootte($lidId) {
        return $this->first_row(
            <<<SQL
SELECT e.element, eu.waarde
FROM tblElement e
 join tblElementuser eu on (e.elemId = eu.elemId)
WHERE eu.lidId = :lidId and e.elemId = 19
SQL
        , [[':lidId', $lidId, Type::INT]]
            , [null, null]
        );
    }

    public function zoek_sterfte($lidId) {
        return $this->first_row(
            <<<SQL
SELECT e.element, eu.waarde
FROM tblElement e
 join tblElementuser eu on (e.elemId = eu.elemId)
WHERE eu.lidId = :lidId and e.elemId = 12
SQL
        , [[':lidId', $lidId, Type::INT]]
            , [null, null]
        );
    }

}
