<?php

class NuttigGateway extends Gateway {

    public function nuttig_pil($hisId, $inkId, $stdat, $reduId, $aantal) {
        $this->run_query(<<<SQL
INSERT INTO tblNuttig SET hisId = :hisId, inkId = :inkId, nutat = :aantal, stdat = :stdat, reduId = :reduId
SQL
        , [
            [':hisId', $hisId, Type::INT],
            [':inkId', $inkId, Type::INT],
            [':reduId', $reduId, Type::INT],
            [':aantal', $aantal], // TODO: decimal
            [':stdat', $stdat],
        ]
        );
    }

    public function periode_medicijnen($lidId, $maand, $jaar, $artId, $Karwerk) {
        return $this->run_query(<<<SQL
SELECT date_format(h.datum,'%Y%m') jrmnd, date_format(h.datum,'%Y') jaar, month(h.datum) maand, 
 right(s.levensnummer,$Karwerk) werknr, s.geslacht, oudr.hisId ouder, 
 date_format(h.datum,'%d-%m-%Y') toedm, h.datum, DATEDIFF(CURRENT_DATE(),h.datum) rest, round(sum(n.nutat*n.stdat),2) totat, e.eenheid,
 i.charge, a.wdgn_v, a.wdgn_m
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblUbn u on (u.ubnId = st.ubnId)
 join tblHistorie h on (st.stalId = h.stalId)
 join tblNuttig n on (h.hisId = n.hisId)
 join tblInkoop i on (n.inkId = i.inkId)
 join tblArtikel a on (i.artId = a.artId)
 join tblEenheiduser eu on (eu.enhuId = a.enhuId)
 join tblEenheid e on (e.eenhId = eu.eenhId)
 left join (
    SELECT st.schaapId, h.hisId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) oudr on (s.schaapId = oudr.schaapId)
WHERE h.skip = 0
 and u.lidId = :lidId
 and month(h.datum) = :maand
 and year(h.datum) = :jaar
 and a.artId = :artId
GROUP BY date_format(h.datum,'%Y%m'), date_format(h.datum,'%Y'), month(h.datum), right(s.levensnummer,$Karwerk), s.geslacht, oudr.hisId,
 date_format(h.datum,'%d-%m-%Y'), h.datum, DATEDIFF(CURRENT_DATE(),h.datum), e.eenheid, i.charge, a.wdgn_v, a.wdgn_m
ORDER BY h.datum desc, right(s.levensnummer,$Karwerk)
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
                [':maand', $maand],
                [':jaar', $jaar],
                [':artId', $artId, Type::INT],
            ]
        );
    }

    public function zoek_afgeboekt_pil($recId) {
        $sql = <<<SQL
                SELECT round(sum(n.nutat*n.stdat),0) af
                FROM tblNuttig n
                WHERE n.inkId = :recId and isnull(hisId)
SQL;
        $args = [[':recId', $recId, Type::INT]];
        return $this->first_field($sql, $args);
    }

}
