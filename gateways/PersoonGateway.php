<?php

class PersoonGateway extends Gateway {

    public function insert($partId, $data) {
        $this->run_query(
            <<<SQL
INSERT INTO tblPersoon SET partId = :partId,
 roep = :roep,
 letter = :letter,
 voeg = :voeg,
 naam = :naam,
 geslacht = :geslacht,
 tel = :tel,
 gsm = :gsm,
 mail = :mail,
 functie = :functie
SQL
        , [
            [':partId', $partId, self::INT],
            [':roep', $data['insRoep_']],
            [':letter', $data['insLetter_']],
            [':voeg', $data['insVgsl_']],
            [':naam', $data['insNaam_']],
            [':geslacht', $data['kzlSekse_']],
            [':tel', $data['insTel_']],
            [':gsm', $data['insGsm_']],
            [':mail', $data['insMail_']],
            [':functie', $data['insFunct_']],
        ]
        );
    }

    public function zoek_bij_partij($partId) {
        return $this->run_query(
            <<<SQL
SELECT persId
FROM tblPersoon
WHERE partId = :partId
ORDER BY actief desc
SQL
        , [[':partId', $partId, self::INT]]
        );
    }

    public function find($id) {
        return $this->run_query(
            <<<SQL
SELECT persId, partId, letter, roep, voeg, naam, geslacht, tel, gsm, mail, functie, actief
FROM tblPersoon
WHERE persId = :persId
ORDER BY naam
SQL
        , [[':persId', $id, self::INT]]
        );
    }

}
