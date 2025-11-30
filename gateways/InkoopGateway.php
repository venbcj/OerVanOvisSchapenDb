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

    public function eerste_inkoopdatum_zonder_nuttiging($artikel) {
        // deze query leverde nooit geen-rijen op! MIN() zonder GROUP BY is onvoorspelbaar --BCB
        return $this->first_field(<<<SQL
  SELECT min(dmink) dmink
  FROM tblInkoop i
   left join tblNuttig n on (i.inkId = n.inkId) 
  WHERE artId = :artId
 and isnull(n.inkId)
GROUP BY i.inkId
SQL
        , [[':artId', $artikel, self::INT]]
        );
    }

    public function eerste_inkoopid_op_datum($artikel, $dmink) {
        return $this->first_field(<<<SQL
  SELECT min(i.inkId) inkId
  FROM tblInkoop i
   left join tblNuttig n on (i.inkId = n.inkId)
  WHERE artId = :artId
 and dmink = :dmink
 and isnull(n.inkId)
SQL
        , [[':artId', $artikel, self::INT], [':dmink', $dmink, self::DATE]]
        );
    }

    public function eerste_inkoopdatum_zonder_voeding($artikel) {
        return $this->first_field(<<<SQL
  SELECT min(dmink) dmink
  FROM tblInkoop i
   left join tblVoeding v on (i.inkId = v.inkId) 
  WHERE artId = :artId and isnull(v.inkId)
SQL
        , [[':artId', $artikel, self::INT]]
        );
    }

    public function eerste_inkoopid_voeding_op_datum($artikel, $dmink) {
        return $this->first_field(<<<SQL
  SELECT min(i.inkId) inkId
  FROM tblInkoop i
   left join tblVoeding v on (i.inkId = v.inkId)
  WHERE artId = :artId
 and dmink = :dmink
 and isnull(v.inkId)
SQL
        , [
            [':artId', $artikel, self::INT],
            [':dmink', $dmink, self::DATE],
        ]
        );
    }

    public function zoek_inkoop($new_inkId) {
        return $this->first_row(<<<SQL
  SELECT i.inkId, i.inkat, a.stdat
  FROM tblInkoop i
   join tblArtikel a on (i.artId = a.artId)
  WHERE inkId = :inkId
SQL
        , [[':inkId', $new_inkId, self::INT]]
        );
    }

    public function laatst_aangesproken_voorraad($artikel) {
        return $this->first_row(<<<SQL
SELECT i.inkId, i.inkat - coalesce(n.nutat,0) vrdat, a.stdat
FROM tblArtikel a
 join tblInkoop i on (a.artId = i.artId)
 left join (
    SELECT inkId, sum(nutat*stdat) nutat
    FROM tblNuttig 
    GROUP BY inkId
 ) n on (i.inkId = n.inkId)
WHERE i.artId = :artId
 and i.inkat > (i.inkat - coalesce(n.nutat,0))
 and (i.inkat - coalesce(n.nutat,0)) > 0
SQL
        , [[':artId', $artikel, self::INT]]
        );

    }
}
