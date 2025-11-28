<?php

class InkoopGateway extends Gateway {

    public function findArtikel($ink_id) {
        return $this->first_field(<<<SQL
SELECT i.artId
FROM  tblInkoop i 
WHERE i.inkId = :inkId
SQL
        , [[':inkId', $ink_id, self::INT]]);
    }

    public function zoek_afgeboekt($Id) {
        return $this->first_field(<<<SQL
SELECT sum(af) af
FROM (
    SELECT round(sum(coalesce(n.nutat*n.stdat,0)),0) af
    FROM tblInkoop i
     left join tblNuttig n on (n.inkId = i.inkId)
    WHERE n.inkId = :inkId and correctie = 1

Union all

    SELECT round(sum(coalesce(v.nutat*v.stdat,0)),0) af
    FROM tblInkoop i
     left join tblVoeding v on (v.inkId = i.inkId)
    WHERE v.inkId = :inkId and correctie = 1
) tbl
SQL
        , [[':inkId', $Id, self::INT]]);
    }

    public function countArtikel($artId) {
        return $this->first_field(<<<SQL
SELECT count(artId) aant
FROM tblInkoop
WHERE artId = :artId
SQL
        , [[':artId', $artId, self::INT]]
        );
    }

}
