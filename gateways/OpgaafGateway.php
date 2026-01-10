<?php

class OpgaafGateway extends Gateway {

    public function clear_history($recId) {
        $this->run_query(
            <<<SQL
UPDATE tblOpgaaf SET his = NULL WHERE opgId = :opgId
SQL
        , [[':opgId', $recId, Type::INT]]
        );
    }

    public function insert($rubr, $day, $bedrag, $toel, $insLiq) {
        $this->run_query(
            <<<SQL
INSERT INTO tblOpgaaf SET rubuId = :rubuId,
datum = :datum,
bedrag = :bedrag,
toel = :toel,
liq = :liq
SQL
        , [
            [':rubuId', $rubr, Type::INT],
            [':datum', $day],
            [':bedrag', $bedrag],
            [':toel', $toel],
            [':liq', $insLiq],
        ]
        );
    }

    public function inboekingen($lidId) {
        return $this->run_query(
            <<<SQL
SELECT op.opgId, op.rubuId, date_format(op.datum,'%d-%m-%Y') datum, op.bedrag, op.toel, op.liq, op.his
FROM tblOpgaaf op
 join tblRubriekuser ru on (op.rubuId = ru.rubuId)
WHERE ru.lidId = :lidId
 and (isnull(op.his) or op.his = 0)
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function jaaropbrengst($rubuId, $jaar) {
        return $this->first_field(
            <<<SQL
SELECT sum(bedrag) bedrag 
FROM tblOpgaaf o
WHERE rubuId = :rubuId and date_format(datum,'%Y') = :jaar
SQL
        , [
            [':rubuId', $rubuId, Type::INT],
            [':jaar', $jaar]
        ]
        );
    }

    public function zoek_afleverbedrag_per_maand($rubuId, $van, $tot) {
        return $this->first_field(
            <<<SQL
SELECT sum(bedrag) bedrag 
FROM tblOpgaaf o
WHERE rubuId = :rubuId and date_format(datum,'%Y%u') >= :van and date_format(datum,'%Y%u') <= :tot
SQL
        , [[':rubuId', $rubuId, Type::INT], [':van', $van], [':tot', $tot]]
        );
    }

    public function insert_tblOpgaaf($rubuId, $insInkdm, $PrijsInclBtw, $relatie) {
        $sql = <<<SQL
            INSERT INTO tblOpgaaf SET rubuId = :rubuId, datum = :insInkdm, bedrag = :PrijsInclBtw, toel = :relatie, liq = 1
SQL;
        $args = [[':rubuId', $rubuId, Type::INT], [':insInkdm', $insInkdm, Type::DATE], [':PrijsInclBtw', $PrijsInclBtw], [':relatie', $relatie]];
        return $this->run_query($sql, $args);
    }

}
