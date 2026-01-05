<?php

class EenheidGateway extends Gateway {

    public function findByLid($lidId) {
        return $this->run_query(
            <<<SQL
SELECT e.eenheid, eu.enhuId
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
WHERE eu.lidId = :lidId
 and eu.actief = 1
ORDER BY e.eenheid
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function all($lidId) {
        return $this->run_query(
            <<<SQL
select eu.enhuId
from tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
where eu.lidId = :lidId
order by e.eenheid
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function get($lidId, $id) {
        return $this->run_query(
            <<<SQL
select eenheid, eu.actief
from tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
where eu.lidId = :lidId
 and eu.enhuId = :enhuId
order by eenheid
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':enhuId', $id, self::INT],
        ]
        );
    }

    public function newvoer($lidId) {
        $sql = <<<SQL
    SELECT artId, stdat, naam, concat(' ', eenheid) heid, soort, eenheid
    FROM tblEenheid e
     join tblEenheiduser eu on (e.eenhId = eu.eenhId)
     join tblArtikel a on (a.enhuId = eu.enhuId)
    WHERE eu.lidId = :lidId and a.actief = 1
    ORDER BY soort desc, naam
SQL;
        $args = [[':lidId', $lidId, self::INT]];
        return $this->run_query($sql, $args);
    } 

    public function keuze_eenhd($lidId, $txtArtikel) {
        $sql = <<<SQL
            SELECT eenheid 
            FROM tblEenheid e
             join tblEenheiduser eu on (e.eenhId = eu.eenhId)
             join tblArtikel a on (a.enhuId = eu.enhuId)
            WHERE eu.lidId = :lidId and a.artId = :artId
SQL;
        $args = [[':lidId', $lidId, self::INT], [':artId', $txtArtikel]];
        return $this->first_field($sql, $args);
    }

    public function group_jaar($lidId) {
        $sql = <<<SQL
    SELECT year(i.dmink) jaar
    FROM tblEenheid e
     join tblEenheiduser eu on (e.eenhId = eu.eenhId)
     join tblArtikel a on (a.enhuId = eu.enhuId)
     join tblInkoop i on (a.artId = i.artId)
    WHERE eu.lidId = :lidId
    GROUP BY year(i.dmink)
    ORDER BY year(i.dmink) desc
SQL;
        $args = [[':lidId', $lidId, self::INT]];
        return $this->run_query($sql, $args);
    }

}
