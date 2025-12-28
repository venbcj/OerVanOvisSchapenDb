<?php

class RubriekGateway extends Gateway {

    public function zoekHoofdrubriek($lidId) {
        return $this->run_query(
            <<<SQL
SELECT hr.rubhId, hr.rubriek
FROM tblRubriekhfd hr
 join tblRubriek r on (hr.rubhId = r.rubhId)
 join tblRubriekuser ru on (r.rubId = ru.rubId)
WHERE ru.lidId = :lidId
 and hr.actief = 1 and r.actief = 1 and ru.sal = 1
GROUP BY hr.rubhId, hr.rubriek
ORDER BY hr.sort
SQL
        , 
            [[':lidId', $lidId, self::INT]]
        );
    }

    public function zoekHoofdrubriekSal($lidId) {
        return $this->run_query(
            <<<SQL
SELECT hr.rubhId, hr.rubriek
FROM tblRubriekhfd hr
 join tblRubriek r on (hr.rubhId = r.rubhId)
 join tblRubriekuser ru on (r.rubId = ru.rubId)
WHERE ru.lidId = :lidId
 and hr.actief = 1 and r.actief = 1 and (ru.actief or ru.sal = 1)
GROUP BY hr.rubhId, hr.rubriek
ORDER BY hr.sort
SQL
        , 
            [[':lidId', $lidId, self::INT]]
        );
    }

    public function zoekRubriek($lidId, $rubhId, $jaar) {
        return $this->run_query(
            <<<SQL
SELECT sb.salbId, r.rubId, r.credeb, ru.rubuId, r.rubriek, sb.aantal hoev, sum(coalesce(l.bedrag,0)) bedrag_liq, sb.waarde, sum(coalesce(o.bedrag,0)) bedrag_real
FROM tblRubriek r
 join tblRubriekuser ru on (r.rubId = ru.rubId)
 join tblSalber sb on (sb.tblId = ru.rubuId)
 left join tblLiquiditeit l on (l.rubuId = ru.rubuId and date_format(sb.datum,'%Y') = date_format(l.datum,'%Y'))
 left join tblOpgaaf o on (o.rubuId = ru.rubuId and date_format(o.datum,'%Y') = date_format(sb.datum,'%Y') and date_format(o.datum,'%Y%m') = date_format(l.datum,'%Y%m'))
WHERE ru.lidId = :lidId
 and r.rubhId = :rubhId
 and sb.tbl = 'ru'
 and year(sb.datum) = :jaar
 and r.actief = 1
 and ru.sal = 1
GROUP BY sb.salbId, ru.rubuId, r.rubriek, sb.waarde
ORDER BY r.rubriek
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':rubhId', $rubhId, self::INT],
            [':jaar', $jaar],
        ]
        );
    }

    public function zoek_rubriek_simpel($lidId, $rubhId) {
        return $this->run_query(<<<SQL
SELECT ru.rubuId, r.rubriek, ru.actief, ru.sal
FROM tblRubriek r
 join tblRubriekuser ru on (r.rubId = ru.rubId)
WHERE ru.lidId = :lidId and r.rubhId = :rubhId and r.actief = 1 and (ru.actief = 1 or ru.sal = 1)
ORDER BY r.rubriek
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':rubhId', $rubhId, self::INT],
        ]
        );
    }

    public function zoek_hoofdrubriek_6($lidId) {
        return $this->run_query(
            <<<SQL
SELECT ru.rubuId, r.rubriek
FROM tblRubriekuser ru 
 join tblRubriek r on (ru.rubId = r.rubId)
 join tblRubriekhfd hr on (r.rubhId = hr.rubhId)
WHERE ru.lidId = :lidId
 and r.rubhId = 6
 and r.actief = 1
 and hr.actief = 1
ORDER BY r.rubriek
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function update($recId, $fldActief, $fldSalber) {
        $this->run_query(
            <<<SQL
UPDATE tblRubriekuser
SET actief = :actief,
 sal = :sal
WHERE rubuId = :rubuId 
SQL
        ,
            [
                [':actief', $fldActief],
                [':sal', $fldSalber],
                [':rubuId', $recId],
            ]
        );
    }

    public function find($lidId, $maand) {
        return $this->run_query(
            <<<SQL
SELECT '$maand' dag, rubuId
FROM tblRubriekuser
WHERE lidId = :lidId
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function hoofdrubriek($lidId) {
        return $this->run_query(
            <<<SQL
SELECT hr.rubhId, hr.rubriek hrub
FROM tblRubriekhfd hr
 join tblRubriek r on (hr.rubhId = r.rubhId)
 join tblRubriekuser ru on (r.rubId = ru.rubId)
WHERE ru.lidId = :lidId
 and ru.actief = 1
GROUP BY hr.rubhId, hr.rubriek
ORDER BY hr.sort, hr.rubhId
SQL
        ,
            [[':lidId', $lidId, self::INT]]
        );
    }

    public function rubriek($lidId, $rubhId) {
        return $this->run_query(
            <<<SQL
SELECT ru.rubuId, r.rubId, r.rubriek
FROM tblRubriek r
 join tblRubriekuser ru on (r.rubId = ru.rubId)
WHERE ru.lidId = :lidId
 and ru.actief = 1
 and r.rubhId = :rubhId
ORDER BY r.rubriek
SQL
        ,
            [
                [':lidId', $lidId, self::INT],
                [':rubhId', $rubhId],
            ]
        );
    }

    public function kzlSubrubriek($lidId) {
        return $this->run_query(
            <<<SQL
SELECT ru.rubuId, r.rubriek
FROM tblRubriekuser ru
 join tblRubriek r on (ru.rubId = r.rubId)
WHERE lidId = :lidId
 and r.actief = 1
 and ru.actief = 1
ORDER BY r.rubriek
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function zoek_rubuId($lidId) {
        return $this->run_query(
            <<<SQL
SELECT rubuId
FROM tblRubriekuser
WHERE lidId = :lidId
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function zoek_rubriek_verkooplammeren($lidId) {
        return $this->first_field(
            <<<SQL
SELECT rubuId
FROM tblRubriekuser
WHERE rubId = 39 and lidId = :lidId
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function aantal_inactief($lidId) {
        return $this->first_field(<<<SQL
SELECT count(rubuId) aant
FROM tblRubriekuser
WHERE lidId = :lidId and actief = 0 and sal = 0
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function inactieve_hoofdrubrieken($lidId) {
        return $this->run_query(<<<SQL
SELECT hr.rubhId, hr.rubriek 
FROM tblRubriekhfd hr 
 join tblRubriek r on (hr.rubhId = r.rubhId)
 join tblRubriekuser ru on (r.rubId = ru.rubId)
WHERE ru.lidId = :lidId and (hr.actief = 0 or r.actief = 0 or (ru.actief = 0 and ru.sal = 0)) 
GROUP BY hr.rubhId, hr.rubriek 
ORDER BY hr.sort 
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function inactieve_rubrieken($lidId, $rubhId) {
        return $this->run_query(<<<SQL
SELECT ru.rubuId, r.rubriek, ru.actief, ru.sal
FROM tblRubriek r
 join tblRubriekuser ru on (r.rubId = ru.rubId)
WHERE ru.lidId = :lidId
 and r.rubhId = :rubhId
 and r.actief = 1
 and ru.actief = 0
 and ru.sal = 0
ORDER BY r.rubriek
SQL
        ,
            [
                [':lidId', $lidId, self::INT],
                [':rubhId', $rubhId, self::INT],
            ]
        );
    }

}
