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

    public function update_datum_bedrag($lidId, $day, $bedrag) {
        $this->run_query(
            <<<SQL
UPDATE tblLiquiditeit li
join tblRubriekuser ru on (li.rubuId = ru.rubuId)
join tblRubriek r on (ru.rubId = r.rubId)
SET bedrag = :bedrag
WHERE ru.lidId = :lidId
 and r.rubId = 39
 and datum = :day
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':day', $day, self::DATE],
            [':bedrag', $bedrag]
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

    public function laatste_jaar($lidId) {
        return $this->first_field(
            <<<SQL
SELECT max(year(datum)) jaar
FROM tblLiquiditeit l
 join tblRubriekuser ru on (l.rubuId = ru.rubuId)
WHERE ru.lidId = :lidId
SQL
        ,
            [[':lidId', $lidId, self::INT]]
        );
    }

    public function jaren($lidId) {
        return $this->run_query(
            <<<SQL
SELECT year(datum) jaar
FROM tblLiquiditeit l
 join tblRubriekuser ru on (l.rubuId = ru.rubuId)
WHERE ru.lidId = :lidId
GROUP BY year(datum)
ORDER BY year(datum)
SQL
        ,
            [[':lidId', $lidId, self::INT]]
        );
    }

    public function zoek_realiteit($rubuId, $jaar, $maand) {
        return $this->run_query(
            <<<SQL
SELECT li.rubuId, o.bedrag, 'realisatie' status
FROM tblLiquiditeit li
join (
    SELECT rubuId, date_format(datum,'%Y%m') jrmnd, sum(bedrag) bedrag 
    FROM tblOpgaaf
    WHERE month(datum) = :maand
 and year(datum) = :jaar
 and liq = 1
    GROUP BY rubuId, date_format(datum,'%Y%m') 
) o on (li.rubuId = o.rubuId and date_format(li.datum,'%Y%m') = o.jrmnd)
WHERE li.rubuId = :rubuId
 and month(li.datum) = :maand
 and year(li.datum) = :jaar
SQL
        ,
            [
                [':rubuId', $rubuId, self::INT],
                [':maand', $maand],
                [':jaar', $jaar],
            ]
        );
    }

    public function zoek_begroting($rubuId, $jaar, $maand) {
        return $this->first_row(
            <<<SQL
SELECT li.rubuId, li.bedrag, 'begroot' status
FROM tblLiquiditeit li
left join (
    SELECT rubuId, date_format(datum,'%Y%m') jrmnd, sum(bedrag) bedrag 
    FROM tblOpgaaf
    WHERE month(datum) = :maand
 and year(datum) = :jaar
 and liq = 1
 and rubuId = :rubuId
    GROUP BY rubuId, date_format(datum,'%Y%m') 
) o on (li.rubuId = o.rubuId and date_format(li.datum,'%Y%m') = o.jrmnd)
WHERE li.rubuId = :rubuId
 and month(li.datum) = :maand
 and year(li.datum) = :jaar
 and isnull(o.bedrag)
SQL
        ,
            [
                [':rubuId', $rubuId, self::INT],
                [':maand', $maand],
                [':jaar', $jaar],
            ]
            , [0, 0, '']
        );
    }

    public function totaal_maandbedragen($lidId, $jaar) {
        return $this->run_query(
            <<<SQL
SELECT jaarmnd, sum(bedrag) bedrag
FROM (
    SELECT date_format(l.datum,'%Y%m') jaarmnd, sum(l.bedrag) bedrag
    FROM tblLiquiditeit l
     join tblRubriekuser ru on (l.rubuId = ru.rubuId)
     join tblRubriek r on (r.rubId = ru.rubId)
     left join tblOpgaaf o on (l.rubuId = o.rubuId
 and date_format(l.datum,'%Y%m') = date_format(o.datum,'%Y%m') )
    WHERE ru.lidId = :lidId
 and ru.actief = 1
 and isnull(o.opgId)
 and year(l.datum) = :jaar
 and r.rubhId = 5
    GROUP BY date_format(l.datum,'%Y%m')
    union
    SELECT date_format(l.datum,'%Y%m') jaarmnd, -sum(l.bedrag) bedrag
    FROM tblLiquiditeit l
     join tblRubriekuser ru on (l.rubuId = ru.rubuId)
     join tblRubriek r on (r.rubId = ru.rubId)
     left join tblOpgaaf o on (l.rubuId = o.rubuId
 and date_format(l.datum,'%Y%m') = date_format(o.datum,'%Y%m') )
    WHERE ru.lidId = :lidId
 and ru.actief = 1
 and isnull(o.opgId)
 and year(l.datum) = :jaar
 and r.rubhId <> 5
    GROUP BY date_format(l.datum,'%Y%m')
    union
    SELECT date_format(o.datum,'%Y%m') jaarmnd, sum(bedrag) bedrag
    FROM tblOpgaaf o
     join tblRubriekuser ru on (o.rubuId = ru.rubuId)
     join tblRubriek r on (r.rubId = ru.rubId)
    WHERE ru.lidId = :lidId
 and ru.actief = 1
 and year(o.datum) = :jaar 
 and r.rubhId = 5
    GROUP BY date_format(o.datum,'%Y%m')
    union
    SELECT date_format(o.datum,'%Y%m') jaarmnd, -sum(bedrag) bedrag
    FROM tblOpgaaf o
     join tblRubriekuser ru on (o.rubuId = ru.rubuId)
     join tblRubriek r on (r.rubId = ru.rubId)
    WHERE ru.lidId = :lidId
 and ru.actief = 1
 and year(o.datum) = :jaar 
 and r.rubhId <> 5
    GROUP BY date_format(o.datum,'%Y%m')
) a
GROUP BY jaarmnd
SQL
        ,
            [
                [':lidId', $lidId, self::INT],
                [':jaar', $jaar],
            ]
        );
    }

    public function cumulatief_maandbedragen($lidId, $jaar, $maand) {
        return $this->first_field(
            <<<SQL
SELECT sum(bedrag) bedrag
FROM (
    SELECT date_format(l.datum,'%Y%m') jaarmnd, sum(l.bedrag) bedrag
    FROM tblLiquiditeit l
     join tblRubriekuser ru on (l.rubuId = ru.rubuId)
     join tblRubriek r on (r.rubId = ru.rubId)
     left join tblOpgaaf o on (l.rubuId = o.rubuId
 and date_format(l.datum,'%Y%m') = date_format(o.datum,'%Y%m') )
    WHERE ru.lidId = :lidId
 and ru.actief = 1
 and isnull(o.opgId)
 and year(l.datum) = :jaar
 and month(l.datum) <= :maand
 and r.rubhId = 5
    GROUP BY date_format(l.datum,'%Y%m')
    union
    SELECT date_format(l.datum,'%Y%m') jaarmnd, -sum(l.bedrag) bedrag
    FROM tblLiquiditeit l
     join tblRubriekuser ru on (l.rubuId = ru.rubuId)
     join tblRubriek r on (r.rubId = ru.rubId)
     left join tblOpgaaf o on (l.rubuId = o.rubuId
 and date_format(l.datum,'%Y%m') = date_format(o.datum,'%Y%m') )
    WHERE ru.lidId = :lidId
 and ru.actief = 1
 and isnull(o.opgId)
 and year(l.datum) = :jaar
 and month(l.datum) <= :maand
 and r.rubhId <> 5
    GROUP BY date_format(l.datum,'%Y%m')
    union
    SELECT date_format(o.datum,'%Y%m') jaarmnd, sum(bedrag) bedrag
    FROM tblOpgaaf o
     join tblRubriekuser ru on (o.rubuId = ru.rubuId)
     join tblRubriek r on (r.rubId = ru.rubId)
    WHERE ru.lidId = :lidId
 and ru.actief = 1
 and year(o.datum) = :jaar
 and month(o.datum) <= :maand
 and r.rubhId = 5
    GROUP BY date_format(o.datum,'%Y%m')
    union
    SELECT date_format(o.datum,'%Y%m') jaarmnd, -sum(bedrag) bedrag
    FROM tblOpgaaf o
     join tblRubriekuser ru on (o.rubuId = ru.rubuId)
     join tblRubriek r on (r.rubId = ru.rubId)
    WHERE ru.lidId = :lidId
 and ru.actief = 1
 and year(o.datum) = :jaar
 and month(o.datum) <= :maand
 and r.rubhId <> 5
    GROUP BY date_format(o.datum,'%Y%m')
)
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':jaar', $jaar],
            [':maand', $maand],
        ]
        );
    }

    public function deklijst_zoek_jaar($lidId, $jaar) {
        return $this->first_field(
            <<<SQL
SELECT count(*) aant
FROM tblLiquiditeit li
 join tblRubriekuser ru on (li.rubuId = ru.rubuId)
WHERE ru.lidId = :lidId
 and year(li.datum) = :jaar
SQL
        , [[':lidId', $lidId, self::INT], [':jaar', $jaar]]
        );
    }

}
