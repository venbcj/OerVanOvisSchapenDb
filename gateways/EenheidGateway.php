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

    public function controle($lidId, $naam) {
        $sql = <<<SQL
                SELECT count(*) aantal 
                FROM tblEenheid e
                 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
                 join tblArtikel a on (eu.enhuId = a.enhuId)
                WHERE eu.lidId = :lidId and a.naam = :naam and a.soort = 'pil'
                GROUP BY a.naam
SQL;
        $args = [[':lidId', $lidId, self::INT], [':naam', $naam]];
        return $this->first_field($sql, $args);
    }

    public function actieve_artikelen($lidId) {
        $sql = <<<SQL
        SELECT a.artId 
        FROM tblEenheid e
         join tblEenheiduser eu on (e.eenhId = eu.eenhId)
         join tblArtikel a on (a.enhuId = eu.enhuId) 
        WHERE eu.lidId = :lidId and a.soort = 'pil' and a.actief = 1
        ORDER BY a.actief desc, a.naam
SQL;
        $args = [[':lidId', $lidId, self::INT]];
        return $this->run_query($sql, $args);
    }

    public function qryArtikel($Id) {
        $sql = <<<SQL
                SELECT soort, naam, naamreader pres, a.stdat, a.enhuId, eenheid, perkg, btw, regnr, a.relId, a.wdgn_v, a.wdgn_m, a.rubuId, a.actief 
                FROM tblEenheid e
                 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
                 join tblArtikel a on (a.enhuId = eu.enhuId)
                WHERE a.artId = :Id
                ORDER BY a.naam
SQL;
        $args = [[':Id', $Id, self::INT]];
        return $this->run_query($sql, $args);
    }

    public function qryEenheid($lidId) {
        $sql = <<<SQL
            SELECT e.eenheid, eu.enhuId 
            FROM tblEenheid e
             join tblEenheiduser eu on (e.eenhId = eu.eenhId)
            WHERE eu.lidId = :lidId and eu.actief = 1
            ORDER BY e.eenheid
SQL;
        $args = [[':lidId', $lidId, self::INT]];
        return $this->run_query($sql, $args);
    }

    public function newvrb($lidId) {
        $sql = <<<SQL
        SELECT e.eenheid, eu.enhuId
        FROM tblEenheid e
         join tblEenheiduser eu on (e.eenhId = eu.eenhId)
        WHERE eu.lidId = :lidId and eu.actief = 1
        ORDER BY e.eenheid
SQL;
        $args = [[':lidId', $lidId, self::INT]];
        return $this->run_query($sql, $args);
    }

    public function Niet_in_gebruik($lidId) {
        $sql = <<<SQL
        SELECT count(artId) aant 
        FROM tblEenheid e
         join tblEenheiduser eu on (e.eenhId = eu.eenhId)
         join tblArtikel a on (a.enhuId = eu.enhuId)
        WHERE eu.lidId = :lidId and a.soort = 'pil' and a.actief = 0
SQL;
        $args = [[':lidId', $lidId, self::INT]];
        return $this->first_field($sql, $args);
    }

    public function inactieve_pillen($lidId) {
        $sql = <<<SQL
            SELECT artId, naam 
            FROM tblEenheid e
             join tblEenheiduser eu on (e.eenhId = eu.eenhId)
             join tblArtikel a on (a.enhuId = eu.enhuId)
            WHERE eu.lidId = :lidId and a.soort = 'pil' and a.actief = 0
            ORDER BY a.actief desc, a.naam
SQL;
        $args = [[':lidId', $lidId, self::INT]];
        return $this->run_query($sql, $args);
    }

    public function qryArtikel_med($Id) {
        $sql = <<<SQL
                    SELECT a.soort, a.naam, a.stdat, a.enhuId, e.eenheid, a.perkg, a.btw, a.regnr, p.naam relatie, a.wdgn_v, a.wdgn_m, r.rubriek, a.actief
                    FROM tblEenheid e
                     join tblEenheiduser eu on (e.eenhId = eu.eenhId)
                     join tblArtikel a on (a.enhuId = eu.enhuId)
                     left join tblRelatie rl on (rl.relId = a.relId)
                     left join tblPartij p on (p.partId = rl.partId)
                     left join tblRubriekuser ru on (a.rubuId = ru.rubuId)
                     left join tblRubriek r on (r.rubId = ru.rubId)
                    WHERE a.artId = :Id
                    ORDER BY a.naam
SQL;
        $args = [[':Id', $Id, self::INT]];
        return $this->run_query($sql, $args);
    }

}
