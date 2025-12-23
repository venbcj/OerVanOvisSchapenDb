<?php

class OpgaafGateway extends Gateway {

    public function clear_history($recId) {
        $this->run_query(
            <<<SQL
UPDATE tblOpgaaf SET his = NULL WHERE opgId = :opgId
SQL
        , [[':opgId', $recId, self::INT]]
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
            [':rubuId', $rubr, self::INT],
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
        , [[':lidId', $lidId, self::INT]]
        );
    }

}
