<?php

class VolwasGateway extends Gateway {

    public function zoek_laatste_koppel_na_laatste_worp_obv_moeder($kzlMdr) {
        return $this->first_field(
            <<<SQL
SELECT max(v.volwId) volwId
FROM tblVolwas v
 left join tblHistorie dek on (dek.hisId = v.hisId)
 left join tblSchaap lam on (lam.volwId = v.volwId)
WHERE (isnull(dek.skip) or dek.skip = 0)
 and isnull(lam.volwId)
 and v.mdrId = :mdrId
SQL
 ,
 [[':mdrId', $kzlMdr, self::INT]]
        );
    }

    public function zoek_moeder_vader_uit_laatste_koppel($koppel) {
        return $this->first_row(
            <<<SQL
SELECT mdrId, vdrId, v.hisId his_dek, d.hisId his_dracht
FROM tblVolwas v
 left join tblDracht d on (d.volwId = v.volwId) 
 left join tblHistorie hd on (hd.hisId = d.hisId)
WHERE (isnull(hd.skip) or hd.skip = 0)
 and v.volwId = :volwId
SQL
 ,
 [[':volwId', $koppel, self::INT]],
 [0, 0, 0, 0]
        );
    }

    public function vroegst_volgende_dekdatum($kzlMdr) {
        return $this->first_field(
            <<<SQL
SELECT date_add(max(h.datum),interval 60 day) datum
FROM tblVolwas v
 join tblSchaap lam on (lam.volwId = v.volwId)
 join tblStal st on (st.schaapId = lam.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
WHERE mdrId = :mdrId
 and h.actId = 1
 and h.skip = 0
SQL
 , [[':mdrId', $kzlMdr, self::INT]]
        );
    }

    public function zoek_volwas($schaapId) {
        return $this->first_field(
            <<<SQL
SELECT max(volwId) volwId
FROM tblVolwas
WHERE mdrId = :schaapId
 OR vdrId = :schaapId
SQL
        , [[':schaapId', $schaapId, self::INT]]
        );
    }

    public function zoek_laatste_worp_moeder($mdrId) {
        return $this->first_field(
            <<<SQL
SELECT max(v.volwId) max_worp
FROM tblVolwas v
 join tblSchaap s on (v.volwId = s.volwId)
WHERE v.mdrId = :mdrId
SQL
        ,
            [[':mdrId', $mdrId, self::INT]]
        );
    }

    public function zoek_dekkingen($lidId, $Karwerk, $jaar) {
        return $this->run_query(
            <<<SQL
SELECT v.volwId, v.hisId, dekdate, dekdatum, v.mdrId, right(mdr.levensnummer,$Karwerk) mdr, v.vdrId,
 count(lam.schaapId) lamrn, drachtdatum, v.grootte, werpdatum,
lst_volwId
FROM tblVolwas v
 join tblSchaap mdr on (v.mdrId = mdr.schaapId)
 join tblStal stm on (stm.schaapId = mdr.schaapId)
 join tblUbn um on (stm.ubnId = um.ubnId)
 join tblHistorie h on (stm.stalId = h.stalId
 and v.hisId = h.hisId)
 left join (
    SELECT hisId, h.datum dekdate, date_format(h.datum,'%d-%m-%Y') dekdatum, year(h.datum) dekjaar, skip
    FROM tblHistorie h
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE actId = 18
 and skip = 0
 and u.lidId = :lidId
 ) dek on (v.hisId = dek.hisId)
 left join tblSchaap vdr on (v.vdrId = vdr.schaapId)
 left join (
    SELECT d.volwId, date_format(h.datum,'%d-%m-%Y') drachtdatum, year(h.datum) drachtjaar
     FROM tblDracht d 
     join tblHistorie h on (h.hisId = d.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE actId = 19
 and h.skip = 0
 and u.lidId = :lidId
 ) dra on (dra.volwId = v.volwId)
 left join tblSchaap lam on (lam.volwId = v.volwId)
 left join tblStal stl on (stl.schaapId = lam.schaapId)
 join tblUbn ul on (stl.ubnId = ul.ubnId)
 left join (
     SELECT stalId, date_format(datum,'%d-%m-%Y') werpdatum, year(date_add(datum,interval -145 day)) dekjaar_obv_worp
     FROM tblHistorie
     WHERE actId = 1
 and skip = 0
 ) hl on (stl.stalId = hl.stalId)
 join (
    SELECT v.mdrId, max(v.volwId) lst_volwId
   FROM tblVolwas v
    left join (
       SELECT hisId
      FROM tblHistorie
      WHERE actId = 18
 and skip = 0
    ) dek on (v.hisId = dek.hisId)
    left join ( 
       SELECT volwId
      FROM tblDracht d
       join tblHistorie hd on (hd.hisId = d.hisId)
      WHERE skip = 0
    ) dra on (dra.volwId = v.volwId)
    left join tblSchaap k on (k.volwId = v.volwId)
    left join (
       SELECT s.schaapId
      FROM tblSchaap s
       join tblStal st on (s.schaapId = st.schaapId)
       join tblHistorie h on (st.stalId = h.stalId)
       WHERE h.actId = 3
 and h.skip = 0
    ) ha on (k.schaapId = ha.schaapId)
    WHERE (dek.hisId is not null or dra.volwId is not null)
 and isnull(ha.schaapId)
    GROUP BY mdrId
 ) lst_v on (lst_v.mdrId = v.mdrId)
WHERE um.lidId = :lidId
 and (isnull(ul.lidId) or ul.lidId = :lidId)
 and (dekdatum is not null or drachtdatum is not null)
 and coalesce(dekjaar, dekjaar_obv_worp, drachtjaar) = :jaar
 and isnull(stm.rel_best)
GROUP BY v.volwId, v.hisId, dekdatum, v.mdrId, mdr.levensnummer, v.vdrId, drachtdatum, werpdatum, v.grootte
ORDER BY right(mdr.levensnummer,$Karwerk), dekdate desc
SQL
        ,
            [[':lidId', $lidId, self::INT], [':jaar', $jaar]]
        );
    }

    public function zoek_ouders($mdrId, $vdrId) {
        return $this->first_field(
            <<<SQL
        SELECT max(volwId) volwId
        FROM tblVolwas
        WHERE mdrId = :mdrId
 and vdrId = :vdrId
SQL
        ,
            [[':mdrId', $mdrId, self::INT], [':vdrId', $vdrId, self::INT]]
        );
    }

    public function zoek_actuele_worp($mdrId, $datum) {
        return $this->first_field(
            <<<SQL
SELECT v.volwId
FROM tblVolwas v
 join tblSchaap l on (l.volwId = v.volwId)
 join tblStal stl on (stl.schaapId = l.schaapId)
 join tblHistorie h on (h.stalId = stl.stalId)
WHERE v.mdrId = :mdrId
 and h.actId = 1
 and h.skip = 0
 and h.datum = :datum
SQL
        ,
            [
                [':mdrId', $mdrId, self::INT],
                [':datum', $datum, self::DATE],
            ]
        );
    }

    public function zoek_vorige_worp($mdrId, $datum) {
        return $this->first_field(
            <<<SQL
SELECT max(l.volwId) volwId
FROM tblSchaap l
 join tblVolwas v on (l.volwId = v.volwId)
 join tblStal st on (l.schaapId = st.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 left join tblSchaap k on (k.volwId = v.volwId)
 left join (
    SELECT s.schaapId
    FROM tblSchaap s
     join tblStal st on (s.schaapId = st.schaapId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) ha on (k.schaapId = ha.schaapId)
WHERE v.mdrId = :mdrId
 and h.actId = 1
 and h.skip = 0
 and h.datum < :datum
 and isnull(ha.schaapId)
SQL
        ,
            [
                [':mdrId', $mdrId, self::INT],
                [':datum', $datum, self::DATE],
            ]
        );
    }

    public function zoek_actuele_dracht($mdrId, $volwId) {
        return $this->first_field(
            <<<SQL
SELECT v.volwId
FROM tblVolwas v
 join tblDracht d on (d.volwId = v.volwId)
 join tblHistorie h on (h.hisId = d.hisId)
WHERE h.skip = 0
 and v.mdrId = :mdrId
 and v.volwId > :volwId
SQL
        ,
            [
                [':mdrId', $mdrId, self::INT],
                [':volwId', $volwId, self::INT],
            ]
        );
    }

    public function zoek_actuele_dekking($mdrId, $volwId) {
        return $this->first_field(
            <<<SQL
SELECT max(v.volwId) volwId
FROM tblVolwas v
 join tblHistorie h on (h.hisId = v.hisId)
WHERE h.skip = 0
 and v.mdrId = :mdrId
 and v.volwId > :volwId
SQL
        ,
            [
                [':mdrId', $mdrId, self::INT],
                [':volwId', $volwId, self::INT],
            ]
        );
    }

    public function zoek_vader_uit_koppel($volwId) {
        return $this->first_field(
            <<<SQL
 SELECT vdrId
 FROM tblVolwas
 WHERE volwId = :volwId
SQL
        , [[':volwId', $volwId, self::INT]]
        );
    }

    public function update_koppel($vdrId, $volwId) {
        $this->run_query(
            <<<SQL
UPDATE tblVolwas set vdrId = :vdrId WHERE volwId = :volwId
SQL
    ,
    [
        [':vdrId', $vdrId, self::INT],
        [':volwId', $volwId, self::INT],
    ]
        );
    }

    public function maak_koppel($mdrId, $vdrId) {
        $this->run_query(
            <<<SQL
 INSERT INTO tblVolwas set mdrId = :mdrId, vdrId = :vdrId
SQL
        ,
            [[':mdrId', $mdrId, self::INT], [':vdrId', $vdrId, self::INT]]
        );
    }

    public function zoek_recentste_id($mdrId) {
        return $this->first_field(
            <<<SQL
 SELECT max(volwId) volwId
 FROM tblVolwas
 WHERE mdrId = :mdrId
SQL
        , [[':mdrId', $mdrId, self::INT]]
        );
    }

    public function zoek_bestaande_worp($mdrId, $datum) {
        return $this->first_field(
            <<<SQL
SELECT v.volwId
FROM tblVolwas v
 join tblSchaap l on (l.volwId = v.volwId)
 join tblStal stl on (stl.schaapId = l.schaapId)
 join tblHistorie h on (h.stalId = stl.stalId)
WHERE v.mdrId = :mdrId
 and h.actId = 1
 and h.skip = 0
 and h.datum = :datum
SQL
        ,
            [
                [':mdrId', $mdrId, self::INT],
                [':datum', $datum, self::DATE],
            ]
        );
    }

    public function zoek_laatste_worp_voor_geboortedatum($mdrId, $datum) {
        $vw = $this->run_query(
            <<<SQL
SELECT max(h.datum) datum, date_format(max(h.datum),'%d-%m-%Y') dag
FROM tblSchaap l
 join tblVolwas v on (l.volwId = v.volwId)
 join tblStal st on (l.schaapId = st.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
WHERE v.mdrId = :mdrId
 and h.actId = 1
 and h.skip = 0
 and h.datum < :datum
SQL
        ,
            [[':mdrId', $mdrId, self::INT], [':datum', $datum, self::DATE]]
        );
        if ($vw->num_rows) {
            return $vw->fetch_row();
        }
        return [null, null];
    }

    public function zoek_volgende_worp_na_geboortedatum($mdrId, $datum) {
        $vw = $this->run_query(
            <<<SQL
SELECT min(h.datum) datum, date_format(min(h.datum),'%d-%m-%Y') dag
FROM tblSchaap l
 join tblVolwas v on (l.volwId = v.volwId)
 join tblStal st on (l.schaapId = st.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
WHERE v.mdrId = :mdrId
 and h.actId = 1
 and h.skip = 0
 and h.datum > :datum
SQL
        ,
            [
                [':mdrId', $mdrId, self::INT],
                [':datum', $datum, self::DATE],
            ]
        );
        if ($vw->num_rows) {
            return $vw->fetch_row();
        }
        return [null, null];
    }

}
