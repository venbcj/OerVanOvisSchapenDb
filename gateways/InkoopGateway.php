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

    // todo: klopt de eerste > in de where-clause? Moet dat niet >= zijn? Zo is de left join niks waard.
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

    public function laatst_aangesproken_voorraad_voer($artId) {
        return $this->first_row(
            <<<SQL
SELECT i.inkId, i.inkat - coalesce(n.nutat,0) vrdat, a.stdat
FROM tblArtikel a
 join tblInkoop i on (a.artId = i.artId)
 left join (
    SELECT inkId, sum(nutat*stdat) nutat
    FROM tblVoeding 
    GROUP BY inkId
 ) n on (i.inkId = n.inkId)
WHERE i.artId = :artId
 and i.inkat > (i.inkat - coalesce(n.nutat,0))
 and (i.inkat - coalesce(n.nutat,0)) > 0
SQL
        , [[':artId', $artId, self::INT]]
        );
    }

    public function set_prijs($prijs, $inkId) {
        $this->run_query(
            <<<SQL
UPDATE tblInkoop set prijs = :prijs WHERE inkId = :inkId
SQL
        , [[':prijs', $prijs], [':inkId', $inkId, self::INT]]
        );
    }

    public function remove($inkId) {
        $this->run_query(
            <<<SQL
DELETE FROM tblInkoop WHERE inkId = :inkId
SQL
        , [[':inkId', $inkId, self::INT]]
        );
    }

    public function zoek_voorraad($lidId, $artId) {
        return $this->first_row(
            <<<SQL
SELECT vrdat, actief v_actief 
FROM (
    SELECT i.artId, ifnull(vrd.inkId, max(i.inkId)) inkId, vrd.vrdat, a.actief
    FROM tblInkoop i
     join tblArtikel a on (i.artId = a.artId)
     join tblEenheiduser eu on (eu.enhuId = a.enhuId)
     left join (
        SELECT a.artId, i.inkId, sum(i.inkat-coalesce(n.vbrat,0)) vrdat
        FROM tblArtikel a
         join tblEenheiduser eu on (eu.enhuId = a.enhuId)
         join tblEenheid e on (e.eenhId = eu.eenhId)
         join tblInkoop i on (a.artId = i.artId)
         left join (
            SELECT n.inkId, sum(n.nutat*n.stdat) vbrat
            FROM tblEenheiduser eu
             join tblArtikel a on (a.enhuId = eu.enhuId)
             join tblInkoop i on (i.artId = a.artId)
             join tblNuttig n on (i.inkId = n.inkId)
            WHERE eu.lidId = :lidId
 and a.soort = 'pil'
            GROUP BY n.inkId
         ) n on (i.inkId = n.inkId)
         left join (
            SELECT a.artId, sum(i.inkat) - sum(coalesce(n.vbrat,0)) totvrd
            FROM tblEenheiduser eu
             join tblArtikel a on (a.enhuId = eu.enhuId)
             join tblInkoop i on (a.artId = i.artId)
             left join (
                SELECT n.inkId, sum(n.stdat*n.nutat) vbrat
                FROM tblStal st
                 join tblHistorie h on (h.stalId = st.stalId)
                 join tblNuttig n on (n.hisId = h.hisId)
                WHERE st.lidId = :lidId
 and h.skip = 0
                GROUP BY n.inkId
             ) n on (i.inkId = n.inkId)
            WHERE eu.lidId = :lidId
            GROUP BY a.artId 
         ) artvrd on (artvrd.artId = a.artId)
        WHERE eu.lidId = :lidId
 and a.soort = 'pil'
 and (i.inkat-coalesce(n.vbrat,0) > 0 or (a.actief = 1
 and totvrd = 0) )
        GROUP BY a.artId, a.naam, a.stdat, e.eenheid, i.inkId, i.charge, artvrd.totvrd
     ) vrd on (i.artId = vrd.artId)
    WHERE eu.lidId = :lidId
    GROUP BY i.artId, vrd.vrdat, actief
) A
WHERE artId = :artId
SQL
        , [[':lidId', $lidId, self::INT], [':artId', $artId, self::INT]]
        );
    }

    public function porties($lidId, $artId) {
        return $this->first_row(
            <<<SQL
SELECT a.stdat, e.eenheid
FROM tblInkoop i
 join tblArtikel a on (i.artId = a.artId)
 join tblEenheiduser eu on (i.enhuId = eu.enhuId)
 join tblEenheid e on (e.eenhId = eu.eenhId)
WHERE eu.lidId = :lidId
 and i.artId = :artId
SQL
        , [[':lidId', $lidId, self::INT], [':artId', $artId, self::INT]]
        );
    }

    public function zoek_voorraad_artikel($artId) {
        return $this->first_field(
            <<<SQL
SELECT sum(i.inkat-coalesce(n.vbrat,0)) vrdat
FROM tblInkoop i
 left join (
    SELECT v.inkId, sum(v.nutat*v.stdat) vbrat
    FROM tblVoeding v
     join tblInkoop i on (v.inkId = i.inkId)
    WHERE i.artId = :artId
    GROUP BY v.inkId
 ) n on (i.inkId = n.inkId)
WHERE i.artId = :artId and i.inkat-coalesce(n.vbrat,0) > 0
SQL
        , [[':artId', $artId, self::INT]]
        );
    }

    public function zoek_soort_artikel($recId) {
        $sql = <<<SQL
            SELECT a.soort
            FROM tblInkoop i
             join tblArtikel a on (a.artId = i.artId)
            WHERE i.inkId = :recId
SQL;
        $args = [[':recId', $recId, self::INT]];
        return $this->first_field($sql, $args);
    }

    public function zoek_voorraad_pil($recId) {
        $sql = <<<SQL
                SELECT round(i.inkat - sum(coalesce(n.nutat*n.stdat,0)),0) voorraad, e.eenheid
                FROM tblInkoop i
                 join tblEenheiduser eu on (eu.enhuId = i.enhuId)
                 join tblEenheid e on (e.eenhId = eu.eenhId)
                 left join tblNuttig n on (n.inkId = i.inkId)
                WHERE i.inkId = :recId
                GROUP BY e.eenheid
SQL;
        $args = [[':recId', $recId, self::INT]];
        return $this->first_field($sql, $args);
    }

    public function zoek_voorraad_voer($recId) {
        $sql = <<<SQL
                SELECT round(i.inkat - sum(coalesce(v.nutat*v.stdat,0)),0) voorraad, e.eenheid
                FROM tblInkoop i
                 join tblEenheiduser eu on (eu.enhuId = i.enhuId)
                 join tblEenheid e on (e.eenhId = eu.eenhId)
                 left join tblVoeding v on (v.inkId = i.inkId)
                WHERE i.inkId = :recId
                GROUP BY e.eenheid
SQL;
        $args = [[':recId', $recId, self::INT]];
        return $this->first_row($sql, $args);
    }

    public function insert_tblInkoop($insInkdm, $insVoer, $insCharge, $insInkat, $enhuId, $insPrijs, $insBtw, $insRc) {
        $sql = <<<SQL
            INSERT INTO tblInkoop SET dmink = :insInkdm, artId = :insVoer, charge = :insCharge, inkat = :insInkat, enhuId = :enhuId, prijs = :insPrijs, btw = :insBtw, relId = :insRc
SQL;
        $args = [[':insInkdm', $insInkdm, self::DATE], [':insVoer', $insVoer], [':insCharge', $insCharge], [':insInkat', $insInkat, self::INT], [':enhuId', $enhuId, self::INT], [':insPrijs', $insPrijs], [':insBtw', $insBtw], [':insRc', $insRc]];
        return $this->run_query($sql, $args);
    }

    public function inkopen_query($lidId, $jaar) {
        $sql = <<<SQL
                SELECT i.inkId, date_format(i.dmink,'%d-%m-%Y') inkdm, i.dmink, i.artId, a.naam, i.charge chargenr, inkat, i.enhuId, e.eenheid, round((i.prijs/inkat),2) stprijs, i.prijs, i.btw, p.naam crediteur, min(n.nutId) nutId, min(v.voedId) voedId
                FROM tblInkoop i 
                 join tblEenheiduser eu on (i.enhuId = eu.enhuId)
                 join tblEenheid e on (e.eenhId = eu.eenhId)
                 join tblArtikel a on (a.artId = i.artId)
                 left join tblNuttig n on (n.inkId = i.inkId)
                 left join tblVoeding v on (v.inkId = i.inkId)
                 left join tblRelatie r on (i.relId = r.relId)
                 join tblPartij p on (r.partId = p.partId)
                WHERE eu.lidId = :lidId and year(i.dmink) = :jaar
                GROUP BY i.inkId, i.dmink, i.dmink, i.artId, a.naam, i.charge, inkat, i.enhuId, e.eenheid, round((i.prijs/inkat),2), i.prijs, i.btw, p.naam
                ORDER BY i.dmink desc, inkId desc
SQL;
        $args = [[':lidId', $lidId, self::INT], [':jaar', $jaar]];
        return $this->run_query($sql, $args);
    }

}
