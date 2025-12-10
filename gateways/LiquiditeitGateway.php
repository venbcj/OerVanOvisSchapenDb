<?php

class LiquiditeitGateway extends Gateway {

    public function zoek_jaar($lidId, $year) {
        return $this->first_field(
            <<<SQL
SELECT year(datum) jaar 
FROM tblLiquiditeit li
join tblRubriekuser ru on (li.rubuId = ru.rubuId)
WHERE ru.lidId  = :lidId
and year(datum) = :year
GROUP BY year(datum)
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':year', $year],
        ]
        );
    }

    public function zoek_bedrag($rubuId, $maand, $jaar) {
        return $this->first_field(
            <<<SQL
SELECT bedrag
FROM tblLiquiditeit
WHERE rubuId = :rubuId
 and month(datum) = :maand
 and year(datum) = :jaar
SQL
        ,
            [
                [':rubuId', $rubuId, self::INT],
                [':maand', $maand],
                [':jaar', $jaar],
            ]
        );
    }

    public function update_bedrag($bedrag, $rubuId, $maand, $jaar) {
        $this->run_query(
            <<<SQL
UPDATE tblLiquiditeit SET bedrag = :bedrag
WHERE rubuId = :rubuId
 and month(datum) = :maand
 and year(datum) = :jaar
SQL
        ,
            [
                [':rubuId', $rubuId, self::INT],
                [':maand', $maand],
                [':bedrag', $bedrag],
                [':jaar', $jaar],
            ]
        );
    }

    public function insert($rub_user, $datum) {
        $this->run_query(
            <<<SQL
INSERT INTO tblLiquiditeit SET rubuId = :rubuId, datum = :datum
SQL
    , [
        [':rubuId', $rub_user, self::INT],
        [':datum', $datum],
    ]
        );
    }

}
