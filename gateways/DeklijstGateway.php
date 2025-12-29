<?php

class DeklijstGateway extends Gateway {

    public function insert($lidId, $datum): void {
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

    public function find_aantal($dekId): ?int {
        return $this->first_field(
            <<<SQL
SELECT dekat
FROM tblDeklijst 
WHERE dekId = :dekId
SQL
        , [[':dekId', $dekId, self::INT]]
        );
    }

    public function update($dekId, $dekat): void {
        $this->run_query(
            <<<SQL
UPDATE tblDeklijst SET dekat = :dekat WHERE dekId = :dekId
SQL
        , [[':dekat', $dekat], [':dekId', $dekId]]
        );
    }

    public function zoek_laatste_jaar($lidId): ?int {
        return $this->first_field(
            <<<SQL
SELECT max(year(dmdek)) maxjaar
FROM tblDeklijst 
WHERE lidId = :lidId
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function zoek_max_dekjaar($lidId, $jaar): ?int {
        return $this->first_field(
            <<<SQL
SELECT max(year(dmdek + interval 9 month)) maxjaar
FROM tblDeklijst 
WHERE lidId = :lidId
 and year(dmdek) = :jaar
SQL
        , [[':lidId', $lidId, self::INT], [':jaar', $jaar]]
        );
    }

    public function zoek_afvoermaanden($lidId, $jaar) {
        return $this->run_query(
            <<<SQL
SELECT date_format((dmdek + interval 9 month),'%Y-%m') afvmnd, sum(dekat) dektot
FROM tblDeklijst 
WHERE lidId = :lidId
 and year(dmdek) = :jaar
GROUP BY date_format((dmdek + interval 9 month),'%Y-%m')
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':jaar', $jaar]
        ]
        );
    }

    // return type is eigenlijk Records, maar dat type moet nog geschreven worden
    public function zoek_dekjaar($lidId, $jaar): array {
        return $this->first_record(
            <<<SQL
SELECT sum(dekat) dektot, liq.bedrag 
FROM tblDeklijst dek
 join (
     SELECT date_format(li.datum,'%Y%m') jrmnd, li.bedrag 
    FROM tblLiquiditeit li 
     join tblRubriekuser ru on (li.rubuId = ru.rubuId) 
    WHERE ru.lidId = :lidId and ru.rubId = 39 and year(li.datum) >= :jaar
 ) liq on (liq.jrmnd = date_format((dek.dmdek + interval 9 month),'%Y%m') )
WHERE lidId = :lidId and year(dmdek) = :jaar
GROUP BY liq.jrmnd, liq.bedrag
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':jaar', $jaar],
        ]
            , ['dektot' => null, 'bedrag' => null]
        );
    }

    public function kzlJaar($lidId) {
        return $this->run_query(
            <<<SQL
SELECT year(dmdek) jaar
FROM tblDeklijst
WHERE lidId = :lidId
GROUP BY year(dmdek)
ORDER BY  year(dmdek)
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

    // TODO hier een eenduidige datum teruggeven? ipv string
    public function zoek_eerste_datum_week1($lidId, $jaar): ?string {
        return $this->first_field(
            <<<SQL
SELECT min(dmdek) dmdek1
FROM tblDeklijst 
WHERE lidId = :lidId and year(dmdek) = :jaar
SQL
        , [[':lidId', $lidId, self::INT], [':jaar', $jaar]]
        );
    }

    public function zoek_dekmaanden($lidId, $jaar) {
        return $this->run_query(
            <<<SQL
SELECT month(dmdek) mndnr
FROM tblDeklijst dek
WHERE lidId = :lidId and year(dmdek) = :jaar
GROUP BY month(dmdek)
SQL
        , [[':lidId', $lidId, self::INT], [':jaar', $jaar]]
        );
    }

    public function zoek_dekweken($lidId, $jaar, $maand) {
        return $this->run_query(
            <<<SQL
SELECT dmdek 
FROM tblDeklijst 
WHERE lidId = :lidId and year(dmdek) = :jaar and month(dmdek) = :maand
SQL
        , [[':lidId', $lidId, self::INT], [':jaar', $jaar], [':maand', $maand]]
        );
    }

    public function zoek_prognose_weken($lidId, $jaar, $maandag) {
        return $this->run_query(
            <<<SQL
SELECT dekId, dmdek, dekat, dmdek + interval 145 day dmwerp, (dmdek + interval 194 day) dmspeen,
 (dmdek + interval 275 day) dmafvoer, month(((dmdek + interval 145 day) + interval 4 month)) afvmnd 
FROM tblDeklijst
WHERE lidId = :lidId and year(dmdek) = :jaar and dmdek = :maandag
SQL
        , [[':lidId', $lidId, self::INT], [':jaar', $jaar], [':maandag', $maandag]]
        );
    }

    public function zoek_realisatie_weken($lidId, $jaar, $maandag) {
        return $this->run_query(
            <<<SQL
SELECT dekId, dmdek,
 date_format(dmdek,'%d-%m-%Y') dekdm, 
 (dmdek + interval 145 day) dmwerp,
 date_format(dmdek + interval 145 day,'%d-%m-%Y') werpdatum,
 date_format(dmdek + interval 145 day,'%Y%u') werpjaarweek,
 (dmdek + interval 275 day) dmafvoer,
 date_format(dmdek + interval 275 day,'%d-%m-%Y') afvoerdm,
 date_format(dmdek + interval 275 day,'%Y%u') afvjaarweek
FROM tblDeklijst 
WHERE lidId = :lidId and year(dmdek) = :jaar and dmdek = :maandag
SQL
        , [[':lidId', $lidId, self::INT], [':jaar', $jaar], [':maandag', $maandag]]
        );
    }

    public function zoek_aantal_dekkingen_per_week($lidId, $jaarweek): ?int {
        return $this->first_field(
            <<<SQL
SELECT count(v.volwId) aant
FROM tblVolwas v 
 join tblHistorie h on (v.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
WHERE h.skip = 0 and date_format(h.datum,'%Y%u') = :jaarweek and st.lidId = :lidId
SQL
        , [[':lidId', $lidId, self::INT], [':jaarweek', $jaarweek]]
        );
    }

    public function zoek_maandtotalen_prognose($lidId, $jaar, $maand) {
        return $this->run_query(
            <<<SQL
SELECT sum(d.dekat) dektot, liq.bedrag 
FROM tblDeklijst d
 left join (
         SELECT date_format(l.datum,'%Y%m') jrmnd, l.bedrag 
      FROM tblLiquiditeit l
       join tblRubriekuser ru on (l.rubuId = ru.rubuId) 
      WHERE ru.lidId = :lidId and ru.rubId = 39 and year(l.datum) >= :jaar
       ) liq on (liq.jrmnd = date_format((d.dmdek + interval 9 month),'%Y%m') )
WHERE d.lidId = :lidId and year(d.dmdek) = :jaar and month(d.dmdek) = :maand
GROUP BY liq.bedrag
SQL
        , [[':lidId', $lidId, self::INT], [':jaar', $jaar], [':maand', $maand]]
        );
    }

}
