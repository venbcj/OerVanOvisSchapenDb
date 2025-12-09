<?php

class RubriekGateway extends Gateway {

    public function zoekHoofdrubriek($lidId) {
$vw = $this->db->query("
SELECT hr.rubhId, hr.rubriek
FROM tblRubriekhfd hr
 join tblRubriek r on (hr.rubhId = r.rubhId)
 join tblRubriekuser ru on (r.rubId = ru.rubId)
WHERE ru.lidId = '".$this->db->real_escape_string($lidId)."' and hr.actief = 1 and r.actief = 1 and ru.sal = 1
GROUP BY hr.rubhId, hr.rubriek
ORDER BY hr.sort
");
return $vw;
    }

public function zoekRubriek($lidId, $rubhId, $jaar) {
   $vw = $this->db->query("
SELECT sb.salbId, r.rubId, r.credeb, ru.rubuId, r.rubriek, sb.aantal hoev, sum(coalesce(l.bedrag,0)) bedrag_liq, sb.waarde, sum(coalesce(o.bedrag,0)) bedrag_real
FROM tblRubriek r
 join tblRubriekuser ru on (r.rubId = ru.rubId)
 join tblSalber sb on (sb.tblId = ru.rubuId)
 left join tblLiquiditeit l on (l.rubuId = ru.rubuId and date_format(sb.datum,'%Y') = date_format(l.datum,'%Y'))
 left join tblOpgaaf o on (o.rubuId = ru.rubuId and date_format(o.datum,'%Y') = date_format(sb.datum,'%Y') and date_format(o.datum,'%Y%m') = date_format(l.datum,'%Y%m'))
WHERE ru.lidId = '".$this->db->real_escape_string($lidId)."'
 and r.rubhId = '".$this->db->real_escape_string($rubhId)."'
 and sb.tbl = 'ru' and year(sb.datum) = '".$this->db->real_escape_string($jaar)."' and r.actief = 1 and ru.sal = 1
GROUP BY sb.salbId, ru.rubuId, r.rubriek, sb.waarde
ORDER BY r.rubriek
");
return $vw;
}

public function zoek_hoofdrubriek_6($lidId) {
    return $this->db->query("
SELECT ru.rubuId, r.rubriek
FROM tblRubriekuser ru 
 join tblRubriek r on (ru.rubId = r.rubId)
 join tblRubriekhfd hr on (r.rubhId = hr.rubhId)
WHERE ru.lidId = '".$this->db->real_escape_string($lidId)."' and r.rubhId = 6 and r.actief = 1 and hr.actief = 1
ORDER BY r.rubriek
");
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

}
