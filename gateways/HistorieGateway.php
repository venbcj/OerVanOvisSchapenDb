<?php

class HistorieGateway extends Gateway {

    public function zoek_eerste_datum_stalop($recId) {
        return $this->first_row(
            <<<SQL
SELECT min(datum) date, date_format(min(datum),'%d-%m-%Y') datum
FROM tblHistorie h
 join tblActie a on (a.actId = h.actId)
 join (
    SELECT st.stalId, h.hisId
    FROM tblStal st
     join tblHistorie h on (h.stalId = st.stalId)
     join tblMelding m on (m.hisId = h.hisId)
    WHERE m.meldId = :meldId
 ) st on (st.stalId = h.stalId and st.hisId <> h.hisId)
 WHERE a.op = 1
SQL
        ,
            [[':meldId', $recId, self::INT]],
            [null, null]
        );
    }

    public function setDatum($day, $recId) {
        $this->run_query(
            <<<SQL
 UPDATE tblHistorie h
  join tblMelding m on (h.hisId = m.hisId)
 set h.datum = :datum
 WHERE m.meldId = :meldId
SQL
        ,
            [
                [':datum', $day],
                [':meldId', $recId],
            ]
        );
    }

    // @TODO: naamgeving. Zoekt niet speciaal naar dekking.
    public function zoek_dekdatum($dekMoment) {
        return $this->first_row(
            <<<SQL
SELECT date_format(datum,'%d-%m-%Y') datum, year(datum) jaar
FROM tblHistorie
WHERE hisId = :hisId
 and skip = 0
SQL
        , [[':hisId', $dekMoment]],
            [0, 0]
        );
    }

    public function zoek_drachtdatum($drachtMoment) {
        return $this->first_field(<<<SQL
SELECT date_format(datum,'%d-%m-%Y') datum
FROM tblHistorie
WHERE hisId = :hisId and skip = 0
SQL
        , [[':hisId', $drachtMoment, self::INT]]
        );
    }

    public function zoek_jaartal_eerste_dekking_dracht($lidId, $een_startjaar_eerder_gebruiker) {
        return $this->first_field(<<<SQL
SELECT year(min(h.datum)) jaar
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE (actId = 18 or actId = 19)
 and skip = 0
 and u.lidId = :lidId
 and year(h.datum) >= :jaar
SQL
        , [[':lidId', $lidId, self::INT], [':jaar', $een_startjaar_eerder_gebruiker]]
        );
    }

    public function zoek_datum_verblijf_tijdens_dekking($lidId, $mdrId, $dmdek) {
        return $this->first_field(<<<SQL
SELECT max(h.datum) datum
FROM tblHistorie h
 join tblBezet b on (h.hisId = b.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = :lidId
 and st.schaapId = :mdrId
 and h.datum <= :datum
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':schaapId', $mdrId, self::INT],
            [':datum', $dmdek, self::DATE],
        ]);
    }

    public function zoek_hisId_verblijf_tijdens_dekking($lidId, $mdrId, $date_verblijf) {
        return $this->first_field(<<<SQL
SELECT max(h.hisId) hisId
FROM tblHistorie h
 join tblBezet b on (h.hisId = b.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = :lidId
 and st.schaapId = :mdrId
 and h.datum = :date_verblijf
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':mdrId', $mdrId, self::INT],
            [':date_verblijf', $date_verblijf, self::DATE],
        ]
        );
    }

    public function zoek_verblijf_tijdens_dekking($lidId, $hisId_verblijf, $dmdek) {
        $vw = $this->run_query(<<<SQL
SELECT ho.hoknr
FROM tblBezet b
 join tblHok ho on (b.hokId = ho.hokId)
 left join
 (
    SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE u.lidId = :lidId and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (b.hisId = uit.hisv)
 left join tblHistorie ht on (ht.hisId = uit.hist)
WHERE b.hisId = :hisId_verblijf
 and (isnull(uit.bezId) or ht.datum > :dmdek)
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':hisId_verblijf', $hisId_verblijf, self::INT],
            [':dmdek', $dmdek, self::DATE],
        ]
        );
    }

    public function dagwegingen($lidId, $schaapId, $datum) {
        return $this->first_field(<<<SQL
SELECT count(hisId) aant
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = :lidId
 and schaapId = :schaapId
 and datum = :datum
 and h.actId = 9
 and h.skip = 0
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':schaapId', $schaapId, self::INT],
        ]
        );
    }

    public function eerste_datum_schaap($stalId) {
        return $this->first_field(<<<SQL
SELECT max(datum) datumfirst, date_format(max(datum),'%d-%m-%Y') datum1
FROM tblHistorie
WHERE stalId = :stalId and (actId = 1 or actId = 2 or actId = 11) and skip = 0
SQL
        , [[':stalId', $stalId, self::INT]]
        );
    }

    public function laatste_datum_schaap($stalId) {
        return $this->first_field(<<<SQL
SELECT max(datum) datumend, date_format(max(datum),'%d-%m-%Y') enddatum
FROM tblHistorie
WHERE stalId = :stalId and (actId = 10 or actId = 12 or actId = 13 or actId = 14) and skip = 0
SQL
        , [[':stalId', $stalId, self::INT]]
        );
    }

    public function wegen_invoeren($stalId, $datum, $newkg) {
        $this->run_query(<<<SQL
INSERT INTO tblHistorie
SET stalId = :stalId,
datum = :datum,
kg = :newkg,
actId = 9
SQL
        , [
            [':stalId', $stalId, self::INT],
            [':datum', $datum, self::DATE],
            [':newkg', $newkg, self::FLOAT], // TODO: decimal? zoals in de tabel
        ]
        );
    }

    public function herstel_invoeren($stalId, $datum, $kg, $actId): int {
        $this->run_query(<<<SQL
INSERT INTO tblHistorie
SET stalId = :stalId,
datum = :datum,
kg = :kg,
actId = :actId
SQL
        , [
            [':stalId', $stalId, self::INT],
            [':actId', $actId, self::INT],
            [':datum', $datum, self::DATE],
            [':kg', $kg, self::FLOAT],
        ]
        );
        return $this->db->insert_id;
    }

    public function medicijn_invoeren($stalId, $datum) {
        $this->run_query(<<<SQL
 INSERT INTO tblHistorie SET stalId = :stalId,
        datum = :datum,
        actId = 8
SQL
        , [
            [':stalId', $stalId, self::INT],
            [':datum', $datum, self::DATE]
        ]
        );
    }

    public function weegaantal($lidId, $schaapId) {
        return $this->first_field(<<<SQL
SELECT count(hisId) aant
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
 join tblSchaap s on (s.schaapId = st.schaapId)
WHERE u.lidId = :lidId
 and st.schaapId = :schaapId
 and h.actId = 9
 and h.skip = 0
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':schaapId', $schaapId, self::INT],
        ]
        );
    }

    # LET OP er is een weeg() in Historie en Schaap
    public function weeg($lidId, $schaapId) {
        return $this->run_query(<<<SQL
SELECT datum, kg
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
 join tblSchaap s on (s.schaapId = st.schaapId)
WHERE u.lidId = :lidId
 and st.schaapId = :schaapId
 and h.actId = 9
 and h.skip = 0
ORDER BY datum desc
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':schaapId', $schaapId, self::INT],
        ]
        );
    }

    public function zoek_geboorte($schaapId) {
        return $this->first_field(<<<SQL
SELECT datum
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE h.actId = 1 and h.skip = 0 and st.schaapId = :schaapId
SQL
        , [
            [':schaapId', $schaapId, self::INT],
        ]
        );
    }

    public function zoek_eerste_datum($schaapId) {
        return $this->first_field(<<<SQL
SELECT min(datum) date1
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE h.skip = 0 and st.schaapId = :schaapId
SQL
        , [
            [':schaapId', $schaapId, self::INT],
        ]
        );
    }

    public function insert_geboorte($stalId, $datum) {
        $this->run_query(<<<SQL
INSERT INTO tblHistorie set stalId = :stalId,
        datum = :datum,
        actId = 1
SQL
        , [
            [':stalId', $stalId, self::INT],
            [':datum', $datum, self::DATE],
        ]
        );
    }

    public function zoek_aanwasdatum($schaapId): array {
        return $this->first_row(<<<SQL
SELECT hisId, datum
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE st.schaapId = :schaapId and h.actId = 3 and h.skip = 0
SQL
        , [
            [':schaapId', $schaapId, self::INT],
        ]
        , [null, null]
        );
    }

    public function zoek_nietvoor_datum($lidId, $schaapId) {
        return $this->first_field(<<<SQL
SELECT max(datum) date
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = :lidId and st.schaapId = :schaapId and
 actId = 4 and h.skip = 0
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':schaapId', $schaapId, self::INT],
        ]
        );
    }

    public function zoek_nietvoor_datum_456($lidId, $schaapId) {
        return $this->first_field(<<<SQL
SELECT max(datum) date
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = :lidId and st.schaapId = :schaapId and
 actId IN (4,5,6) and h.skip = 0
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':schaapId', $schaapId, self::INT],
        ]
        );
    }

    public function zoek_afvoer_nietvoor_datum($lidId, $schaapId) {
        return $this->first_field(<<<SQL
SELECT max(datum) date
From (
    SELECT h.datum, a.actie
    FROM tblActie a
     join tblHistorie h on (a.actId = h.actId)
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE u.lidId = :lidId and st.schaapId = :schaapId and
     a.af != 1 and h.skip = 0
    union
    SELECT max(h.datum) datum, 'Laatste worp' actie
    FROM tblSchaap mdr
     join tblVolwas v on (mdr.schaapId = v.mdrId)
     join tblSchaap lam on (v.volwId = lam.volwId)
     join tblStal st on (st.schaapId = lam.schaapId)
     join tblUbn u on (st.ubnId = u.ubnId)
     join tblHistorie h on (st.stalId = h.stalId and h.actId = 1 and h.skip = 0)
    WHERE u.lidId = :lidId and mdr.schaapId = :schaapId
    GROUP BY mdr.schaapId, h.actId
    HAVING (max(h.datum) > min(h.datum))
) datum
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':schaapId', $schaapId, self::INT],
        ]
        );
    }

    public function zoek_nietna_datum($lidId, $schaapId) {
        return $this->first_field(<<<SQL
SELECT min(datum) date
From (
    SELECT datum, actie
    FROM tblActie a
     join tblHistorie h on (a.actId = h.actId)
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE u.lidId = :lidId
 and st.schaapId = :schaapId
 and a.af = 1
 and h.skip = 0
    union
    SELECT  min(h.datum) datum, 'Eerste worp' actie
    FROM tblSchaap mdr
     join tblVolwas v on (mdr.schaapId = v.mdrId)
     join tblSchaap lam on (v.volwId = lam.volwId)
     join tblStal st on (st.schaapId = lam.schaapId)
     join tblUbn u on (st.ubnId = u.ubnId)
     join tblHistorie h on (st.stalId = h.stalId and h.actId = 1 and h.skip = 0)
    WHERE u.lidId = :lidId and mdr.schaapId = :schaapId
) datum
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':schaapId', $schaapId, self::INT],
        ]
        );
    }

    public function update_aanwas($hisId, $datum) {
        $this->run_query(<<<SQL
UPDATE tblHistorie SET datum = :datum WHERE hisId = :hisId
SQL
        , [
            [':hisId', $hisId, self::INT],
            [':datum', $datum, self::DATE],
        ]
        );
    }

    public function zoek_speendm($schaapId) {
        return $this->first_row(<<<SQL
SELECT hisId, datum
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE st.schaapId = :schaapId and h.actId = 4 and h.skip = 0
SQL
        , [
            [':schaapId', $schaapId, self::INT],
        ]
        , [null, null]
        );
    }

    public function zoek_speen_nietvoor_datum($lidId, $schaapId) {
        return $this->first_field(<<<SQL
SELECT max(datum) date
From (
    SELECT datum
    FROM tblHistorie h
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE u.lidId = :lidId and st.schaapId = :schaapId and
     (actId = 1 or actId = 2) and h.skip = 0
    union
    SELECT datum
    FROM tblHistorie h
     join tblBezet b on (h.hisId = b.hisId)
     join tblPeriode p on (p.periId = b.periId)
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE u.lidId = :lidId and st.schaapId = :schaapId and p.doelId = 1 and (h.actId = 5 or h.actId = 6) and h.skip = 0
) datum
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':schaapId', $schaapId, self::INT],
        ]
        );
    }

    public function controle_nietna_datum($lidId, $schaapId) {
        return $this->first_field(<<<SQL
SELECT min(datum) date
From (
    SELECT datum
    FROM tblHistorie h
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE u.lidId = :lidId and st.schaapId = :schaapId and (h.actId = 3 or h.actId = 10 or h.actId = 12 or h.actId = 14) and h.skip = 0
    union
    SELECT datum
    FROM tblHistorie h
     join tblBezet b on (h.hisId = b.hisId)
     join tblPeriode p on (p.periId = b.periId)
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE u.lidId = :lidId and st.schaapId = :schaapId and p.doelId = 2 and (h.actId = 5 or h.actId = 6) and h.skip = 0
) datum
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':schaapId', $schaapId, self::INT],
        ]
        );
    }

    // update_speendatum en update_afvoerdm doen exact hetzelfde.
    public function update_speendatum($hisId, $datum) {
        $this->run_query(<<<SQL
UPDATE tblHistorie SET datum = :datum
WHERE hisId = :hisId
SQL
        , [
            [':hisId', $hisId, self::INT],
            [':datum', $datum, self::DATE],
        ]
        );
    }

    public function zoek_speenkg($schaapId) {
        return $this->first_row(<<<SQL
SELECT hisId, kg speenkg
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE st.schaapId = :schaapId and h.actId = 4 and h.skip = 0
SQL
        , [
            [':schaapId', $schaapId, self::INT],
        ]
        , [null, null]
        );
    }

    public function update_speenkg($hisId, $kg) {
        $this->run_query(<<<SQL
UPDATE tblHistorie SET kg = :kg WHERE hisId = :hisId
SQL
        , [[':kg', $kg, self::FLOAT], [':hisId', $hisId, self::INT]]
        );
    }

    public function zoek_afvoerdm($schaapId) {
        $vw = $this->run_query(<<<SQL
SELECT hisId, datum
FROM tblActie a
 join tblHistorie h on (a.actId = h.actId)
 join tblStal st on (h.stalId = st.stalId)
WHERE st.schaapId = :schaapId and a.af = 1 and h.skip = 0
SQL
        , [[':schaapId', $schaapId, self::INT]]
            , [null, null]
        );
    }

    // update_speendatum en update_afvoerdm doen exact hetzelfde.
    public function update_afvoerdm($hisId, $datum) {
        $this->run_query(<<<SQL
UPDATE tblHistorie SET datum = :datum WHERE hisId = :hidId
SQL
        , [
            [':hisId', $hisId, self::INT],
            [':datum', $datum, self::DATE],
        ]
        );
    }

    public function zoek_afvoerkg($schaapId) {
        return $this->first_field(<<<SQL
SELECT kg
FROM tblActie a
 join tblHistorie h on (a.actId = h.actId)
 join tblStal st on (h.stalId = st.stalId)
WHERE st.schaapId = :schaapId and a.af = 1 and h.skip = 0
SQL
        , [[':schaapId', $schaapId, self::INT]]
        );
    }

    public function update_afvoerkg($schaapId, $kg) {
        $this->run_query(<<<SQL
    UPDATE tblActie a
     join tblHistorie h on (a.actId = h.actId)
     join tblStal st on (h.stalId = st.stalId)
    set h.kg = :kg
    WHERE st.schaapId = :schaapId and a.af = 1 and h.skip = 0
SQL
        , [
            [':schaapId', $schaapId, self::INT],
            [':kg', $kg, self::FLOAT],
        ]
        );
    }

    public function insert_afvoer($stalId, $dmafv) {
        $this->run_query(<<<SQL
INSERT INTO tblHistorie SET stalId = :stalId,
        datum = :dmafv,
        actId = 3
SQL
        , [
            [':dmafv', $dmafv, self::DATE],
            [':stalId', $stalId, self::INT],
        ]
        );
    }

    public function insert_act_3($stalId, $datum, $kg) {
        $this->run_query(
            <<<SQL
INSERT INTO tblHistorie set stalId = :stalId,
        datum = :datum,
        kg = :kg,
        actId = 3
SQL
        , [
            [':stalId', $stalId, self::INT],
            [':datum', $datum, self::DATE],
            [':kg', $kg],
        ]
        );
    }

    // waarschijnlijk verkeerde naam, afgekeken hierboven bij actid=3
    public function insert_afvoer_act($stalId, $datum, $actId) {
        $this->run_query(<<<SQL
INSERT INTO tblHistorie SET stalId = :stalId,
        datum = :datum,
        actId = :actId
SQL
        , [
            [':stalId', $stalId, self::INT],
            [':datum', $datum, self::DATE],
            [':actId', $actId],
        ]
        );
        return $this->db->insert_id;
    }

    public function zoek_vorige_weging($schaapId, $date) {
        return $this->first_field(<<<SQL
SELECT max(hisId) vorige_weging
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
WHERE st.schaapId = :schaapId
 and h.datum < :date
 and h.kg is not null
SQL
        , [
            [':schaapId', $schaapId, self::INT],
            [':date', $date, self::DATE],
        ]
        );
    }

    // TODO in het ideale geval is first_record slim genoeg om zelf een null-record terug te geven als dat moet.
    public function zoek_actie_vorige_weging($hisId) {
        return $this->first_record(<<<SQL
SELECT h.actId, actie, h.datum, kg
FROM tblHistorie h
 join tblActie a on (h.actId = a.actId)
WHERE h.hisId = :hisId
SQL
        , [
            [':hisId', $hisId, self::INT],
        ]
        , ['actId' => null, 'actie' => null, 'datum' => null, 'kg' => null]
        );
    }

    public function zoek_acties($lidId) {
        return $this->run_query(<<<SQL
SELECT h.actId, a.actie
FROM tblHistorie h
 join tblActie a on (h.actId = a.actId)
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = :lidId and h.kg is not null
GROUP BY h.actId, a.actie
ORDER BY h.actId
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function zoek_datum_na($lidId, $schaapId) {
        $vw = $this->run_query(<<<SQL
SELECT max(datum) date
FROM (
     SELECT h.datum
     FROM tblHistorie h
      join tblStal st on (h.stalId = st.stalId)
     WHERE st.schaapId = :schaapId and actId = 1 and skip = 0
  union
     SELECT max(h.datum) dmaank
     FROM tblHistorie h
      join tblStal st on (h.stalId = st.stalId)
      join tblUbn u on (st.ubnId = u.ubnId)
     WHERE st.schaapId = :schaapId and u.lidId = :lidId and actId = 2 and skip = 0
  union
      SELECT h.datum
     FROM tblHistorie h
      join tblStal st on (h.stalId = st.stalId)
     WHERE st.schaapId = :schaapId and actId = 4 and skip = 0
) dm_na
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':schaapId', $schaapId, self::INT],
        ]
        );
        return $vw->fetch_row()[0];
    }

    public function zoek_datum_vanaf($lidId, $schaapId) {
        return $this->first_field(<<<SQL
SELECT max(h.datum) date, date_format(max(h.datum),'%d-%m-%Y') datum
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE st.schaapId = :schaapId
 and u.lidId = :lidId
 and (actId = 3 or actId = 7)
 and skip = 0
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':schaapId', $schaapId, self::INT],
        ]
        );
    }

    public function zoek_uitschaardatum($last_stalId) {
        return $this->first_field(<<<SQL
SELECT datum date
FROM tblHistorie
WHERE stalId = :stalId and actId = 10
SQL
        , [[':stalId', $last_stalId, self::INT]]
        );
    }

    public function zoek_laatste_hisid($lidId, $schaapId) {
        return $this->first_field(<<<SQL
SELECT max(hisId) hisId
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = :lidId and st.schaapId = :schaapId
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':schaapId', $schaapId, self::INT],
        ]
        );
    }

    public function zoek_afgevoerd($maxhis) {
        return $this->run_query(<<<SQL
SELECT h.hisId afvhisId, date_format(h.datum,'%d-%m-%Y') afvoerdm, h.kg afvoerkg, h.actId, a.actie, lower(a.actie) status
FROM tblHistorie h
 join tblActie a on (h.actId = a.actId)
WHERE hisId = :hisId and a.af = 1
SQL
        , [
            [':hisId', $maxhis, self::INT],
        ]
        );
    }

    public function zoek_laatste_verblijf($lidId, $schaapId) {
        return $this->first_field(<<<SQL
SELECT max(h.hisId) hisId
FROM tblHistorie h
 join tblBezet b on (b.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = :lidId and st.schaapId = :schaapId
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':schaapId', $schaapId, self::INT],
        ]
        );
    }

    public function zoek_dier_uit_verblijf($lst_bezet, $schaapId) {
        return $this->first_field(<<<SQL
SELECT h.actId
FROM tblHistorie h
 join tblActie a on (h.actId = a.actId)
 join tblStal st on (h.stalId = st.stalId)
WHERE hisId > :lst_bezet and a.uit = 1 and st.schaapId = :schaapId
SQL
        , [
            [':lst_bezet', $lst_bezet, self::INT],
            [':schaapId', $schaapId, self::INT],
        ]
        );
    }

    public function skip($hisId) {
        $this->run_query("UPDATE tblHistorie SET skip=1 WHERE hisId=:hisId", [[':hisId', $hisId, self::INT]]);
    }

    public function zoek_laatste($stalId, $datum) {
        return $this->first_field(<<<SQL
SELECT max(hisId) hisId
FROM tblHistorie
WHERE stalId = :stalId
and datum = :datum
and actId = 8
SQL
        , [
            [':stalId', $stalId, self::INT],
            [':datum', $datum, self::DATE],
        ]
        );
    }

    public function zoek_einddatum($stalId) {
        return $this->first_row(<<<SQL
SELECT datum day, date_format(datum,'%d-%m-%Y') datum
FROM tblHistorie h
 join tblActie a on (h.actId = a.actId)
WHERE a.af = 1
and h.actId != 10
and h.stalId = :stalId
and h.skip = 0
SQL
        , [[':stalId', $stalId, self::INT]]
        );
    }

    public function zoek_verblijf_moeder($stalId) {
        return $this->first_field(
            <<<SQL
SELECT b.hokId
FROM (
    SELECT max(hisId) hisId
    FROM tblHistorie h
     join tblActie a on (a.actId = h.actId)
    WHERE stalId = :stalId and a.aan = 1
 ) hin
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
    SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE st.stalId = :stalId
 and a1.aan = 1
 and a2.uit = 1
 and h1.skip = 0
 and h2.skip = 0
    GROUP BY b.bezId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
WHERE isnull(uit.hist)
SQL
        , [[':stalId', $stalId, self::INT]]
        );
    }

    public function zoek_nu_in_verblijf_geb_spn($lidId) {
        return $this->first_field(
            <<<SQL
SELECT count(hin.schaapId) aantin
FROM (
    SELECT st.schaapId, max(hisId) hisId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (a.actId = h.actId)
    WHERE st.lidId = :lidId
 and isnull(st.rel_best)
 and a.aan = 1
 and h.skip = 0
    GROUP BY st.schaapId
 ) hin
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
    SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId
 and ((h1.datum < h2.datum) or (h1.datum = h2.datum
 and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE st.lidId = :lidId
 and a1.aan = 1
 and a2.uit = 1
 and h1.skip = 0
 and h2.skip = 0
    GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 and h.skip = 0
 ) prnt on (prnt.schaapId = hin.schaapId)
WHERE (isnull(b.hokId) or uit.hist is not null)
 and isnull(prnt.schaapId)
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function zoek_nu_in_verblijf_parent($lidId) {
        return $this->first_field(
            <<<SQL
SELECT count(hin.schaapId) aantin
FROM (
    SELECT st.schaapId, max(hisId) hisId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (a.actId = h.actId)
    WHERE st.lidId = :lidId
 and isnull(st.rel_best)
 and a.aan = 1
 and h.skip = 0
    GROUP BY st.schaapId
 ) hin
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
    SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId
 and ((h1.datum < h2.datum) or (h1.datum = h2.datum
 and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE st.lidId = :lidId
 and a1.aan = 1
 and a2.uit = 1
 and h1.skip = 0
 and h2.skip = 0
    GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
 join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 and h.skip = 0
 ) prnt on (prnt.schaapId = hin.schaapId)
WHERE (isnull(b.hokId) or uit.hist is not null)
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function zoek_commentaar($hisId) {
        return $this->first_field(
            <<<SQL
SELECT comment
FROM tblHistorie
WHERE hisId = :hisId
SQL
        , [[':hisId', $hisId, self::INT]]
        );
    }

    public function update_commentaar($hisId, $comment) {
        $this->run_query(
            <<<SQL
UPDATE tblHistorie SET comment = :comment WHERE hisId = :hisId
SQL
        , [[':hisId', $hisId, self::INT], [':comment', $comment]]
        );
    }

    public function wis_commentaar($hisId) {
        $this->run_query(
            <<<SQL
UPDATE tblHistorie SET comment = NULL WHERE hisId = :hisId
SQL
        , [[':hisId', $hisId, self::INT]]
        );
    }

    public function zoek_afvoerdatum($stalId) {
        return $this->first_row(
            <<<SQL
SELECT h.datum date, date_format(h.datum,'%d-%m-%Y') datum
FROM tblHistorie h
 join tblActie a on (a.actId = h.actId)
WHERE h.stalId = :stalId
 and a.af = 1
 and h.skip = 0
SQL
        , [[':stalId', $stalId, self::INT]]
        );
    }

    public function zoek_afleverlijst($his) {
        return $this->run_query(
            <<<SQL
SELECT u.lidId, date_format(datum,'%d-%m-%Y') datum, datum date, rel_best, p.naam
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
 join tblRelatie r on (st.rel_best = r.relId)
 join tblPartij p on (p.partId = r.partId)
WHERE h.hisId = :hisId
SQL
        , [[':hisId', $his, self::INT]]
        );
    }

    public function count_afleverlijst($lidId, $datum, $relId) {
        return $this->first_field(
            <<<SQL
SELECT count(distinct st.stalId) aant
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
 join tblActie a on (h.actId = a.actId)
WHERE u.lidId = :lidId
 and h.datum = :datum
 and st.rel_best = :relId
 and a.af = 1
 and h.skip = 0
SQL
        ,
            [
                [':lidId', $lidId, self::INT],
                [':datum', $datum],
                [':relId', $relId, self::INT],
            ]
        );
    }

    public function zoek_schaap($lidId, $datum, $relId, $Karwerk) {
        return $this->run_query(
            <<<SQL
SELECT u.lidId, s.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk) werknr, h.kg, pil.datum, pil.naam, pil.wdgn_v
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 join tblActie a on (h.actId = a.actId)
 left join (
    SELECT s.schaapId, date_format(h.datum,'%d-%m-%Y') datum, art.naam, art.wdgn_v
    FROM tblSchaap s
     join tblStal st on (st.schaapId = s.schaapId)
     join tblUbn u on (st.ubnId = u.ubnId)
     join tblHistorie h on (h.stalId = st.stalId)
     join tblNuttig n on (h.hisId = n.hisId)
     join tblInkoop i on (i.inkId = n.inkId)
     join tblArtikel art on (i.artId = art.artId)
    WHERE u.lidId = :lidId
 and h.actId = 8
 and h.skip = 0
 and (h.datum + interval art.wdgn_v day) >= sysdate()
) pil on (st.schaapId = pil.schaapId)
WHERE h.datum = :datum
 and st.rel_best = :relId
 and a.af = 1
 and h.skip = 0
ORDER BY right(s.levensnummer,$Karwerk)
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':datum', $datum],
            [':relId', $relId, self::INT],
        ]);
    }

    public function findIdByAct($lidId, $actId) {
        return $this->first_field(
            <<<SQL
SELECT max(hisId) hisId
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = :lidId
 and h.actId = :actId
SQL
        , [[':lidId', $lidId, self::INT], [':actId', $actId, self::INT]]
        );
    }

    public function zoek_maxdatum($stalId) {
        return $this->first_row(
            <<<SQL
SELECT datum date, date_format(datum,'%d-%m-%Y') datum
FROM tblHistorie h
 join (
    SELECT max(hisId) hisId
    FROM tblHistorie
    WHERE stalId = :stalId
 and skip = 0
 ) mh on (h.hisId = mh.hisId)
SQL
        , [[':stalId', $stalId, self::INT]]
            , [null, null]
        );
    }

    public function insert($stalId, $datum, $actId) {
        $this->run_query(
            <<<SQL
INSERT INTO tblHistorie
    set stalId = :stalId,
 datum = :datum,
 actId = :actId
SQL
        , [
            [':stalId', $stalId, self::INT],
            [':datum', $datum],
            [':actId', $actId, self::INT],
        ]
        );
    }

    public function zoek_aantal_afleveren_per_jaar($lidId, $jaar) {
        return $this->first_field(
            <<<SQL
SELECT count(h.hisId) aant
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE h.actId = 12
 and h.skip = 0
 and date_format(h.datum,'%Y') = :jaar
 and st.lidId = :lidId
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':jaar', $jaar],
        ]
        );
    }

    public function zoek_aantal_lammeren($lidId, $jaarweek) {
        return $this->first_field(
            <<<SQL
SELECT count(h.hisId) aant
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE h.actId = 1 and h.skip = 0 and date_format(h.datum,'%Y%u') = :jaarweek and st.lidId = :lidId
SQL
        , [[':lidId', $lidId, self::INT], [':jaarweek', $jaarweek]]
        );
    }

    public function zoek_aantal_afvoer($lidId, $jaarweek) {
        return $this->first_field(
            <<<SQL
SELECT count(h.hisId) aant
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE h.actId = 12 and h.skip = 0 and date_format(h.datum,'%Y%u') = :jaarweek and st.lidId = :lidId
SQL
        , [[':lidId', $lidId, self::INT], [':jaarweek', $jaarweek]]
        );
    }

    public function zoek_aantal_geboortes_per_week($lidId, $jaarweek) {
        return $this->first_field(
            <<<SQL
SELECT count(h.hisId) aant
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE h.actId = 1 and h.skip = 0 and date_format(h.datum,'%Y%u') = :jaarweek and st.lidId = :lidId
SQL
        , [[':lidId', $lidId, self::INT], [':jaarweek', $jaarweek]]
        );
    }

    public function zoek_aantal_afvoer_per_week($lidId, $jaarweek) {
        return $this->first_field(
            <<<SQL
SELECT count(h.hisId) aant
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE h.actId = 12 and h.skip = 0 and date_format(h.datum,'%Y%u') = :jaarweek and st.lidId = :lidId
SQL
        , [[':lidId', $lidId, self::INT], [':jaarweek', $jaarweek]]
        );
    }

    public function zoek_aantal_lammeren_per_maand($lidId, $van, $tot) {
        return $this->first_field(
            <<<SQL
SELECT count(h.hisId) aant
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE h.actId = 1 and h.skip = 0 and date_format(h.datum,'%Y%u') >= :van and date_format(h.datum,'%Y%u') <= :tot and st.lidId = :lidId
SQL
        , [[':lidId', $lidId, self::INT], [':van', $van], [':tot', $tot]]
        );
    }

    public function zoek_aantal_afleveren_per_maand($lidId, $van, $tot) {
        return $this->first_field(
            <<<SQL
SELECT count(h.hisId) aant
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE h.actId = 12 and h.skip = 0 and date_format(h.datum,'%Y%u') >= :van and date_format(h.datum,'%Y%u') <= :tot and st.lidId = :lidId
SQL
        , [[':lidId', $lidId, self::INT], [':van', $van], [':tot', $tot]]
        );
    }

    public function zoek_actId($stalId, $actId) {
        // TODO: is deze WHERE volledig? Er zijn toch wel meer regels met actid=2?
        return $this->first_field(
            <<<SQL
SELECT hisId
FROM tblHistorie
WHERE stalId = :stalId and actId = :actId
SQL
        , [[':stalId', $stalId, self::INT], [':actId', $actId, self::INT]]
        );
    }

    public function huidig_aantal_ooien_persaldo($lidId) {
        return $this->first_field(<<<SQL
SELECT sum(coalesce(aanv_m.mdrs,0) - coalesce(afv_m.mdrs,0) - coalesce(doo_m.mdrs,0)) saldo_ooi_end
FROM (
    SELECT date_format(datum,'%Y%m') jrmnd
    FROM tblHistorie
    WHERE skip = 0
    GROUP BY date_format(datum,'%Y%m')
    ) nr
left join (
    SELECT date_format(h.datum,'%Y%m') jrmnd, count(s.schaapId) mdrs
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
     join tblSchaap s on (s.schaapId = st.schaapId)
    WHERE st.lidId = :lidId and h.actId = 3 and s.geslacht = 'ooi' and skip = 0
    GROUP BY date_format(h.datum,'%Y%m')
) aanv_m on (nr.jrmnd = aanv_m.jrmnd)
left join (
    SELECT date_format(h.datum,'%Y%m') jrmnd, count(s.schaapId) mdrs
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
     join tblSchaap s on (s.schaapId = st.schaapId)
    WHERE st.lidId = :lidId and h.actId = 13 and s.geslacht = 'ooi' and skip = 0
    GROUP BY date_format(h.datum,'%Y%m')
) afv_m on (nr.jrmnd = afv_m.jrmnd)
left join (
    SELECT date_format(h.datum,'%Y%m') jrmnd, count(st.schaapId) mdrs
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
     join tblSchaap s on (s.schaapId = st.schaapId)
     join (
        SELECT schaapId
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and skip = 0
     ) ouder on (ouder.schaapId = st.schaapId)
    WHERE st.lidId = :lidId and h.actId = 14
     and s.geslacht = 'ooi' and skip = 0
    GROUP BY date_format(h.datum,'%Y%m')
) doo_m on (nr.jrmnd = doo_m.jrmnd)
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function eerste_jaar_tbv_testen($lidId) {
        return $this->first_field(<<<SQL
SELECT min(year(h.datum)) minjaar
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
WHERE st.lidId = :lidId and h.datum > 0 and h.actId = 3 and s.geslacht = 'ooi' and skip = 0
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function huidig_aantal_rammen_persaldo($lidId) {
        return $this->first_field(<<<SQL
SELECT sum(coalesce(aanv_v.vdrs,0) - coalesce(afv_v.vdrs,0) - coalesce(doo_v.vdrs,0)) saldo_ram_end
FROM (
    SELECT date_format(datum,'%Y%m') jrmnd
    FROM tblHistorie
    WHERE skip = 0
    GROUP BY date_format(datum,'%Y%m')
    ) nr
left join (
    SELECT date_format(h.datum,'%Y%m') jrmnd, count(s.schaapId) vdrs
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
     join tblSchaap s on (s.schaapId = st.schaapId)
    WHERE st.lidId = :lidId and h.actId = 3 and s.geslacht = 'ram' and skip = 0
    GROUP BY date_format(h.datum,'%Y%m')
) aanv_v on (nr.jrmnd = aanv_v.jrmnd)
left join (
    SELECT date_format(h.datum,'%Y%m') jrmnd, count(s.schaapId) vdrs
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
     join tblSchaap s on (s.schaapId = st.schaapId)
    WHERE st.lidId = :lidId and h.actId = 13 and s.geslacht = 'ram' and skip = 0
    GROUP BY date_format(h.datum,'%Y%m')
) afv_v on (nr.jrmnd = afv_v.jrmnd)
left join (
    SELECT date_format(h.datum,'%Y%m') jrmnd, count(st.schaapId) vdrs
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
     join tblSchaap s on (s.schaapId = st.schaapId)
     join (
        SELECT schaapId
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and skip = 0
     ) ouder on (ouder.schaapId = st.schaapId)
    WHERE st.lidId = :lidId and h.actId = 14
     and s.geslacht = 'ram' and skip = 0
    GROUP BY date_format(h.datum,'%Y%m')
) doo_v on (nr.jrmnd = doo_v.jrmnd)
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function result_per_maand($j, $jm, $endjrmnd, $lidId) {
        return $this->run_query(<<<SQL
    SELECT nr.jrmnd jm, nr.jaar, aanv_m.jrmnd, aanv_m.mdrs mdrs_aanv, afv_m.mdrs mdrs_afv, doo_m.mdrs mdrs_doo,
     coalesce(aanw_m.oudrs_m,0) + coalesce(aanv_m.mdrs,0) - coalesce(afv_m.mdrs,0) - coalesce(doo_m.mdrs,0) saldo_ooi,
     gebrn.aant gebrn, aanw_m.oudrs_m, afv_lam.afv afv_lam, doo_lam.lam doo_lam,
     aanv_v.vdrs vdrs_aanv, afv_v.vdrs vdrs_afv, doo_v.vdrs vdrs_doo,
     coalesce(aanw_v.oudrs_v,0) + coalesce(aanv_v.vdrs,0) - coalesce(afv_v.vdrs,0) - coalesce(doo_v.vdrs,0) saldo_ram,
     aanw_v.oudrs_v
    FROM (
        SELECT :jm jrmnd, :j jaar
        FROM dual
        WHERE :jm <= :endjrmnd
    ) nr
    left join (
        SELECT date_format(h.datum,'%Y%m') jrmnd, count(distinct s.schaapId) mdrs
        FROM tblHistorie h
         join tblStal st on (h.stalId = st.stalId)
         join tblSchaap s on (s.schaapId = st.schaapId)
         join (
            SELECT st.schaapId, h.datum
            FROM tblStal st
             join tblHistorie h on (st.stalId = h.stalId)
            WHERE h.actId = 3
 and skip = 0
         ) ouder on (ouder.schaapId = s.schaapId)
        WHERE st.lidId = :lidId
 and date_format(h.datum,'%Y%m') = :jm
 and (h.actId = 2 or h.actId = 11)
 and skip = 0
 and s.geslacht = 'ooi'
 and ouder.datum <= h.datum
        GROUP BY date_format(h.datum,'%Y%m')
    ) aanv_m on (nr.jrmnd = aanv_m.jrmnd)
    left join (
        SELECT date_format(h.datum,'%Y%m') jrmnd, count(distinct s.schaapId) mdrs
        FROM tblHistorie h
         join tblStal st on (h.stalId = st.stalId)
         join tblSchaap s on (s.schaapId = st.schaapId)
         join (
            SELECT st.schaapId, h.datum
            FROM tblStal st
             join tblHistorie h on (st.stalId = h.stalId)
            WHERE h.actId = 3
 and skip = 0
         ) ouder on (ouder.schaapId = s.schaapId)
        WHERE st.lidId = :lidId
 and date_format(h.datum,'%Y%m') = :jm
 and (h.actId = 10 or h.actId = 13)
 and skip = 0
 and s.geslacht = 'ooi'
 and ouder.datum <= h.datum
        GROUP BY date_format(h.datum,'%Y%m')
    ) afv_m on (nr.jrmnd = afv_m.jrmnd)
    left join (
        SELECT date_format(h.datum,'%Y%m') jrmnd, count(st.schaapId) mdrs
        FROM tblHistorie h
         join tblStal st on (h.stalId = st.stalId)
         join tblSchaap s on (s.schaapId = st.schaapId)
         join (
            SELECT schaapId
            FROM tblStal st
             join tblHistorie h on (st.stalId = h.stalId)
            WHERE h.actId = 3
 and skip = 0
         ) ouder on (ouder.schaapId = st.schaapId)
        WHERE st.lidId = :lidId
 and date_format(h.datum,'%Y%m') = :jm
 and h.actId = 14
 and skip = 0
 and s.geslacht = 'ooi'
        GROUP BY date_format(h.datum,'%Y%m')
    ) doo_m on (nr.jrmnd = doo_m.jrmnd)
    left join (
        SELECT date_format(h.datum,'%Y%m') jrmnd, count(st.schaapId) oudrs_m
        FROM tblHistorie h
         join tblStal st on (h.stalId = st.stalId)
         join tblSchaap s on (s.schaapId = st.schaapId)
         left join (
            SELECT h.stalId, datum
            FROM tblHistorie h
             join tblStal st on (st.stalId = h.stalId)
            WHERE st.lidId = :lidId
 and h.actId = 2
 and skip = 0
         ) aanv on (aanv.stalId = h.stalId)
        WHERE st.lidId = :lidId
 and s.geslacht = 'ooi'
 and date_format(h.datum,'%Y%m') = :jm
 and h.actId = 3
 and skip = 0
 and coalesce(aanv.datum, date_add(h.datum, INTERVAL 10 DAY)) <> h.datum
        GROUP BY date_format(h.datum,'%Y%m')
    ) aanw_m on (nr.jrmnd = aanw_m.jrmnd)
    left join (
        SELECT date_format(h.datum,'%Y%m') jrmnd, count(st.schaapId) aant
        FROM tblHistorie h
         join tblStal st on (h.stalId = st.stalId)
        WHERE st.lidId = :lidId
 and date_format(h.datum,'%Y%m') = :jm
 and h.actId = 1
 and skip = 0
        GROUP BY date_format(h.datum,'%Y%m')
    ) gebrn on (nr.jrmnd = gebrn.jrmnd)
    left join (
        SELECT date_format(h.datum,'%Y%m') jrmnd, count(st.schaapId) afv
        FROM tblHistorie h
         join tblStal st on (h.stalId = st.stalId)
        WHERE st.lidId = :lidId
 and date_format(h.datum,'%Y%m') = :jm
 and h.actId = 12
 and skip = 0
        GROUP BY date_format(h.datum,'%Y%m')
    ) afv_lam on (nr.jrmnd = afv_lam.jrmnd)
    left join (
        SELECT date_format(h.datum,'%Y%m') jrmnd, count(st.schaapId) lam
        FROM tblHistorie h
         join tblStal st on (h.stalId = st.stalId)
         left join (
            SELECT schaapId
            FROM tblStal st
             join tblHistorie h on (st.stalId = h.stalId)
            WHERE h.actId = 3
 and skip = 0
         ) ouder on (ouder.schaapId = st.schaapId)
        WHERE st.lidId = :lidId
 and date_format(h.datum,'%Y%m') = :jm
 and h.actId = 14
 and skip = 0
 and isnull(ouder.schaapId)
        GROUP BY date_format(h.datum,'%Y%m')
    ) doo_lam on (nr.jrmnd = doo_lam.jrmnd)
    left join (
    SELECT date_format(h.datum,'%Y%m') jrmnd, count(distinct s.schaapId) vdrs
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
     join tblSchaap s on (s.schaapId = st.schaapId)
     join (
        SELECT st.schaapId, h.datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3
 and skip = 0
     ) ouder on (ouder.schaapId = s.schaapId)
    WHERE st.lidId = :lidId
 and date_format(h.datum,'%Y%m') = :jm
 and (h.actId = 2 or h.actId = 11)
 and skip = 0
 and s.geslacht = 'ram'
 and ouder.datum <= h.datum
    GROUP BY date_format(h.datum,'%Y%m')
    ) aanv_v on (nr.jrmnd = aanv_v.jrmnd)
    left join (
        SELECT date_format(h.datum,'%Y%m') jrmnd, count(distinct s.schaapId) vdrs
        FROM tblHistorie h
         join tblStal st on (h.stalId = st.stalId)
         join tblSchaap s on (s.schaapId = st.schaapId)
         join (
            SELECT st.schaapId, h.datum
            FROM tblStal st
             join tblHistorie h on (st.stalId = h.stalId)
            WHERE h.actId = 3
 and skip = 0
         ) ouder on (ouder.schaapId = s.schaapId)
        WHERE st.lidId = :lidId
 and date_format(h.datum,'%Y%m') = :jm
 and (h.actId = 10 or h.actId = 13)
 and skip = 0
 and s.geslacht = 'ram'
 and ouder.datum <= h.datum
        GROUP BY date_format(h.datum,'%Y%m')
    ) afv_v on (nr.jrmnd = afv_v.jrmnd)
    left join (
        SELECT date_format(h.datum,'%Y%m') jrmnd, count(st.schaapId) vdrs
        FROM tblHistorie h
         join tblStal st on (h.stalId = st.stalId)
         join tblSchaap s on (s.schaapId = st.schaapId)
         join (
            SELECT schaapId
            FROM tblStal st
             join tblHistorie h on (st.stalId = h.stalId)
            WHERE h.actId = 3
 and skip = 0
         ) ouder on (ouder.schaapId = st.schaapId)
        WHERE st.lidId = :lidId
 and date_format(h.datum,'%Y%m') = :jm
 and h.actId = 14
 and skip = 0
 and s.geslacht = 'ram'
        GROUP BY date_format(h.datum,'%Y%m')
    ) doo_v on (nr.jrmnd = doo_v.jrmnd)
    left join (
        SELECT date_format(h.datum,'%Y%m') jrmnd, count(st.schaapId) oudrs_v
        FROM tblHistorie h
         join tblStal st on (h.stalId = st.stalId)
         join tblSchaap s on (s.schaapId = st.schaapId)
         left join (
            SELECT h.stalId, datum
            FROM tblHistorie h
             join tblStal st on (st.stalId = h.stalId)
            WHERE st.lidId = :lidId
 and h.actId = 2
 and skip = 0
         ) aanv on (aanv.stalId = h.stalId)
        WHERE st.lidId = :lidId
 and s.geslacht = 'ram'
 and date_format(h.datum,'%Y%m') = :jm
 and h.actId = 3
 and skip = 0
 and coalesce(aanv.datum, date_add(h.datum, INTERVAL 10 DAY)) <> h.datum
        GROUP BY date_format(h.datum,'%Y%m')
    ) aanw_v on (nr.jrmnd = aanw_v.jrmnd)
    WHERE jaar = :j
    ORDER BY jrmnd desc
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':jm', $jm, self::INT],
            [':j', $j, self::INT],
            [':endjrmnd', $endjrmnd, self::INT],
        ]
        );
    }

    public function zoek_hisId_tbv_tblBezet($stalId) {
        return $this->first_field(<<<SQL
SELECT max(hisId) hisId
FROM tblHistorie h
 join tblActie a on (h.actId = a.actId)
WHERE h.skip = 0 and a.aan = 1 and stalId = :stalId
SQL
        , [[':stalId', $stalId, self::INT]]
        );
    }

    public function zoek_hisIdaanv($stalId, $actId) {
        return $this->first_field(<<<SQL
SELECT hisId
FROM tblHistorie
WHERE skip = 0
 and stalId = :stalId
 and actId = :actId
SQL
        , [[':stalId', $stalId, self::INT], [':actId', $actId, self::INT]]
        );
    }

    // @TODO: #0004219 koppelen via ubn
    public function historie_invschaap($lidId, $schaapId) {
        return $this->run_query(<<<SQL
    SELECT date_format(datum,'%d-%m-%Y') dag, h.actId, actie, datum
    FROM tblSchaap s
     join tblStal st on (s.schaapId = st.schaapId)
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (a.actId = h.actId)
    WHERE st.lidId = :lidId
 and s.schaapId = :schaapId
 and h.skip = 0

 and not exists (
        SELECT datum
        FROM tblHistorie geenAanwas
         join tblStal st on (geenAanwas.stalId = st.stalId)
        WHERE actId = 2
 and h.datum = geenAanwas.datum
 and h.actId = geenAanwas.actId+1
 and st.schaapId = :schaapId)

    union

    SELECT date_format(datum,'%d-%m-%Y') dag, h.actId, actie, datum
    FROM tblSchaap s
     join tblStal st on (s.schaapId = st.schaapId)
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (a.actId = h.actId)
    WHERE h.actId = 1
 and h.skip = 0
 and s.schaapId = :schaapId

    union

    SELECT date_format(p.dmafsluit,'%d-%m-%Y') dag, h.actId, 'Gevoerd' actie, p.dmafsluit
    FROM tblVoeding v
     join tblPeriode p on (p.periId = v.periId)
     join tblBezet b on (p.periId = b.periId)
     join tblHistorie h on (h.hisId = b.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join tblSchaap s on (s.schaapId =st.schaapId)
    WHERE h.skip = 0
 and st.lidId = :lidId
 and s.schaapId = :schaapId

    union

    SELECT date_format(min(h.datum),'%d-%m-%Y') dag, h.actId, 'Eerste worp' actie, min(h.datum) datum
    FROM tblSchaap s
     join tblVolwas v on (s.schaapId = v.mdrId)
     join tblSchaap lam on (v.volwId = lam.volwId)
     join tblStal st on (st.schaapId = lam.schaapId)
     join tblHistorie h on (st.stalId = h.stalId
 and h.actId = 1
 and h.skip = 0)
    WHERE st.lidId = :lidId
 and s.schaapId = :schaapId
    GROUP BY h.actId

    union

    SELECT date_format(max(h.datum),'%d-%m-%Y') dag, h.actId, 'Laatste worp' actie, max(h.datum) datum
    FROM tblSchaap s
     join tblVolwas v on (s.schaapId = v.mdrId)
     join tblSchaap lam on (v.volwId = lam.volwId)
     join tblStal st on (st.schaapId = lam.schaapId)
     join tblHistorie h on (st.stalId = h.stalId
 and h.actId = 1
 and h.skip = 0)
    WHERE st.lidId = :lidId
 and s.schaapId = :schaapId
    GROUP BY h.actId
    HAVING (max(h.datum) > min(h.datum))

    union

    SELECT date_format(rs.dmcreate,'%d-%m-%Y') dag, h.actId, 'Geboorte gemeld' actie, rs.dmcreate
    FROM impRespons rs
     join tblMelding m on (rs.reqId = m.reqId)
     join tblHistorie h on (m.hisId = h.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join tblSchaap s on (s.schaapId = st.schaapId
 and s.levensnummer = rs.levensnummer)
    WHERE rs.melding = 'GER'
 and rs.meldnr is not null
 and h.skip = 0
 and st.lidId = :lidId
 and s.schaapId = :schaapId

    union

    SELECT date_format(rs.dmcreate,'%d-%m-%Y') dag, h.actId, 'Aanvoer gemeld' actie, rs.dmcreate
    FROM impRespons rs
     join tblMelding m on (rs.reqId = m.reqId)
     join tblHistorie h on (m.hisId = h.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join tblSchaap s on (s.schaapId = st.schaapId
 and s.levensnummer = rs.levensnummer)
    WHERE rs.melding = 'AAN'
 and rs.meldnr is not null
 and h.skip = 0
 and st.lidId = :lidId
 and s.schaapId = :schaapId

    union

    SELECT date_format(rs.dmcreate,'%d-%m-%Y') dag, h.actId, 'Afvoer gemeld' actie, rs.dmcreate
    FROM impRespons rs
     join tblMelding m on (rs.reqId = m.reqId)
     join tblHistorie h on (m.hisId = h.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join tblSchaap s on (s.schaapId = st.schaapId
 and s.levensnummer = rs.levensnummer)
    WHERE rs.melding = 'AFV'
 and rs.meldnr is not null
 and h.skip = 0
 and st.lidId = :lidId
 and s.schaapId = :schaapId

    union

    SELECT date_format(rs.dmcreate,'%d-%m-%Y') dag, h.actId, 'Uitval gemeld' actie, rs.dmcreate
    FROM impRespons rs
     join tblMelding m on (rs.reqId = m.reqId)
     join tblHistorie h on (m.hisId = h.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join tblSchaap s on (s.schaapId = st.schaapId
 and s.levensnummer = rs.levensnummer)
    WHERE rs.melding = 'DOO'
 and rs.meldnr is not null
 and h.skip = 0
 and st.lidId = :lidId
 and s.schaapId = :schaapId

    ORDER BY datum desc, actId desc
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':schaapId', $schaapId, self::INT],
        ]
        );
    }

    public function zoek_aantal_doelgroep1($lidId) {
        return $this->first_field(<<<SQL
SELECT count(hin.schaapId) aantin
FROM (
    SELECT st.schaapId, max(hisId) hisId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (a.actId = h.actId)
    WHERE st.lidId = :lidId and isnull(st.rel_best) and a.aan = 1 and h.skip = 0
    GROUP BY st.schaapId
 ) hin
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
    SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE st.lidId = :lidId and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = hin.schaapId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = hin.schaapId)
WHERE (isnull(b.hokId) or uit.hist is not null) and isnull(spn.schaapId) and isnull(prnt.schaapId)
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function zoek_aantal_doelgroep2($lidId) {
        return $this->first_field(<<<SQL
SELECT count(hin.schaapId) aantin
FROM (
    SELECT st.schaapId, max(hisId) hisId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (a.actId = h.actId)
    WHERE st.lidId = :lidId and isnull(st.rel_best) and a.aan = 1 and h.skip = 0
    GROUP BY st.schaapId
 ) hin
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
    SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE st.lidId = :lidId and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
 join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = hin.schaapId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = hin.schaapId)
WHERE (isnull(b.hokId) or uit.hist is not null) and isnull(prnt.schaapId)
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function zoek_aantal_doelgroep3($lidId) {
        return $this->first_field(<<<SQL
SELECT count(hin.schaapId) aantin
FROM (
    SELECT st.schaapId, max(hisId) hisId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (a.actId = h.actId)
    WHERE st.lidId = :lidId and isnull(st.rel_best) and a.aan = 1 and h.skip = 0
    GROUP BY st.schaapId
 ) hin
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
    SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE st.lidId = :lidId and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
 join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = hin.schaapId)
WHERE (isnull(b.hokId) or uit.hist is not null)
SQL
        , [[':lidId', $lidId, self::INT]]
        );
    }

    public function bezet_list_for($lidId) {
        return $this->collect_list(<<<SQL
SELECT h.hisId
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
 join tblBezet b on (b.hisId = h.hisId)
WHERE st.lidId = :%lidId
GROUP BY h.hisId
ORDER BY h.hisId
SQL
        , ['lidId' => $lidId]
        );
    }

    public function list_for($lidId) {
        return $this->collect_list(<<<SQL
SELECT h.hisId
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = :%lidId and h.actId = 8
SQL
        , ['lidId' => $lidId]
        );
    }

    public function delete_ids($ids) {
        $this->run_query(<<<SQL
DELETE FROM tblHistorie WHERE :%hisId
SQL
        , ['hisId', $ids]
        );
    }

    public function kzlJaar($lidId, $jaarstart) {
        $sql = <<<SQL
        SELECT date_format(h.datum,'%Y') jaar 
        FROM tblHistorie h
         join tblStal st on (st.stalId = h.stalId)
        WHERE st.lidId = :lidId and date_format(datum,'%Y') >= :jaarstart and h.actId = 4 and h.skip = 0
        GROUP BY date_format(datum,'%Y')
        ORDER BY date_format(datum,'%Y') desc 
SQL;
        $args = [[':lidId', $lidId], [':jaarstart', $jaarstart]];
        return $this->run_query($sql, $args);
    }

    public function waardes_per_maand($lidId, $kzlJaar) {
        $sql = <<<SQL
            SELECT jrmnd jaarmnd, jaar, maand, speenat, afvat, doodat, Perc_naopleg, round(daggroei,2) gemgroei, round(voer,2) voer
            FROM (
                SELECT aant.jrmnd, aant.maand, aant.jaar, aant.speenat, aant.afvat, 
                 naopleg.doodat, round((naopleg.doodat/aant.speenat*100),2) perc_naopleg, 
                 groei.gemgroeidag daggroei,
                 kgvoer.nutat_mnd voer
                FROM (
                    SELECT date_format(h.datum,'%Y%m') jrmnd, Month(h.datum) maand, year(h.datum) jaar, count(h.hisId) speenat, count(haf.hisId) afvat
                    FROM tblHistorie h
                     join tblStal st on (st.stalId = h.stalId)
                     join tblSchaap s on (s.schaapId = st.schaapId)
                     left join (
                        SELECT h.stalId, h.hisId
                        FROM tblHistorie h
                        WHERE h.actId = 12 and h.skip = 0
                     ) haf on (st.stalId = haf.stalId)
                    WHERE st.lidId = :lidId and h.actId = 4 and h.skip = 0 and year(h.datum) = :kzlJaar
                    GROUP BY Month(h.datum), year(h.datum)
                 ) aant
                left join (
                    SELECT date_format(h.datum,'%Y%m') jrmnd, Month(h.datum) maand, Year(h.datum) jaar, count(distinct s.schaapId) doodat
                    FROM tblSchaap s
                     join tblStal st on (s.schaapId = st.schaapId)
                     join tblHistorie h on (st.stalId = h.stalId)
                     join tblHistorie ho on (st.stalId = ho.stalId and ho.actId = 14)
                     join tblHistorie hs on (st.stalId = hs.stalId and hs.actId = 4)
                     left join tblHistorie ha on (st.stalId = ha.stalId and ha.actId = 3)
                    WHERE st.lidId = :lidId and h.actId = 4 and h.skip = 0 and isnull(ha.actId) and year(h.datum) = :kzlJaar
                    GROUP BY month(h.datum), Year(h.datum)    
                 ) naopleg on (aant.jrmnd = naopleg.jrmnd)
                left join (
                    SELECT date_format(h.datum,'%Y%m') jrmnd, sum((haf.kg -  h.kg)*1000/ DATEDIFF(haf.datum, h.datum)) groeidag, count(distinct st.schaapId), 
                    sum((haf.kg -  h.kg)*1000/ DATEDIFF(haf.datum, h.datum)) / count(st.schaapId) gemgroeidag
                    FROM tblSchaap s 
                     join tblStal st on (st.schaapId = s.schaapId)
                     join tblHistorie h on (st.stalId = h.stalId and h.actId = 4)
                     join (
                        SELECT h.stalId, h.kg, h.datum
                        FROM tblHistorie h
                        WHERE h.actId = 12 and h.skip = 0
                     ) haf on (st.stalId = haf.stalId)
                    WHERE st.lidId = :lidId and year(h.datum) = :kzlJaar
                    GROUP BY Month(h.datum), Year(h.datum)
                 ) groei on (aant.jrmnd = groei.jrmnd)
                 left join (
                    SELECT gesp_jrmnd, sum(nutat_peri_mnd) nutat_mnd
                    FROM (
                        SELECT date_format(spn.datum,'%Y%m') gesp_jrmnd, vantot.periId, dgperi.dgn_periId,
                         sum(datediff(tot.datum,van.datum)) dgn,
                         sum(datediff(tot.datum,van.datum))/dgperi.dgn_periId*100 perc_dgn,
                         v.nutat,
                         sum(datediff(tot.datum,van.datum))/dgperi.dgn_periId*v.nutat nutat_peri_mnd
                        FROM (
                            SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist, b.periId, h1.actId
                            FROM tblBezet b
                             join tblHistorie h1 on (b.hisId = h1.hisId)
                             join tblActie a1 on (a1.actId = h1.actId)
                             join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
                             join tblActie a2 on (a2.actId = h2.actId)
                             join tblStal st on (h1.stalId = st.stalId)
                             join tblPeriode p on (b.periId = p.periId)
                            WHERE st.lidId = :lidId and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
                             and p.doelId = 2 and year(h1.datum) = :kzlJaar
                            GROUP BY b.bezId, st.schaapId, h1.hisId, h1.actId
                        ) vantot
                         join (
                            SELECT vantot.periId, sum(datediff(tot.datum,van.datum)) dgn_periId
                            FROM (
                                SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist, b.periId, h1.actId
                                FROM tblBezet b
                                 join tblHistorie h1 on (b.hisId = h1.hisId)
                                 join tblActie a1 on (a1.actId = h1.actId)
                                 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
                                 join tblActie a2 on (a2.actId = h2.actId)
                                 join tblStal st on (h1.stalId = st.stalId)
                                 join tblPeriode p on (b.periId = p.periId)
                                WHERE st.lidId = :lidId and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
                                 and p.doelId = 2
                                GROUP BY b.bezId, st.schaapId, h1.hisId, h1.actId
                            ) vantot
                             join tblHistorie van on (van.hisId = vantot.hisv)
                             join tblHistorie tot on (tot.hisId = vantot.hist)
                            GROUP BY vantot.periId
                         ) dgperi on (vantot.periId = dgperi.periId)
                         join tblHistorie van on (van.hisId = vantot.hisv)
                         join tblHistorie tot on (tot.hisId = vantot.hist)
                         join tblVoeding v on (v.periId = vantot.periId)
                         join tblStal st on (st.schaapId = vantot.schaapId)
                         join (
                            SELECT h.stalId, h.datum
                            FROM tblHistorie h
                            WHERE h.actId = 4 and h.skip = 0
                         ) spn on (spn.stalId = st.stalId)
                        GROUP BY date_format(spn.datum,'%Y%m'), vantot.periId, dgperi.dgn_periId, v.nutat
                    ) vr_mnd
                    GROUP BY gesp_jrmnd
                 ) kgvoer on (aant.jrmnd = kgvoer.gesp_jrmnd)
            ) mv
            ORDER BY jaarmnd desc
SQL;
        $args = [[':lidId', $lidId], [':kzlJaar', $kzlJaar]];
        return $this->run_query($sql, $args);
    }

    public function zoek_aantal_maanden($lidId, $kzlJaar) {
        $sql = <<<SQL
            SELECT count(distinct(month(h.datum))) mndat
            FROM tblHistorie h
             join tblStal st on (st.stalId = h.stalId)
             join tblSchaap s on (s.schaapId = st.schaapId)
            WHERE st.lidId = :lidId and h.actId = 4 and h.skip = 0 and year(h.datum) = :kzlJaar
SQL;
        $args = [[':lidId', $lidId], [':kzlJaar', $kzlJaar]];
        return $this->first_field($sql, $args);
    }

    public function zoek_overleden_schapen($Karwerk, $lidId, $kzlJaar, $keuze_mnd) {
        $sql = <<<SQL
            SELECT right(s.levensnummer, :Karwerk) werknr, date_format(h.datum,'%d-%m-%Y') speendm, date_format(dood.datum,'%d-%m-%Y') uitvdm, r.reden, meld.meldnr
            FROM tblSchaap s
             join tblStal st on (st.schaapId = s.schaapId)
             join tblHistorie h on (st.stalId = h.stalId)
             left join tblReden r on (r.redId = s.redId)
             join(
                 SELECT st.schaapId, datum
                 FROM tblStal st
                  join tblHistorie h on (st.stalId = h.stalId)
                 WHERE h.actId = 14 and h.skip = 0 and st.lidId = :lidId
             ) dood on (dood.schaapId = s.schaapId)
             left join(
                 SELECT rs.levensnummer, rs.meldnr
                 FROM impRespons rs
                 WHERE rs.meldnr is not null and rs.melding = 'DOO'
             ) meld on (meld.levensnummer = s.levensnummer)
            WHERE s.levensnummer is not null and h.actId = 4 and h.skip = 0 and year(h.datum) = :kzlJaar and month(h.datum) = :keuze_mnd and st.lidId = :lidId
            GROUP BY s.schaapId, st.stalId
SQL;
        $args = [[':Karwerk', $Karwerk], [':lidId', $lidId], [':kzlJaar', $kzlJaar], [':keuze_mnd', $keuze_mnd]];
        return $this->run_query($sql, $args);
    }

    public function delete_dracht($hisId) {
        $sql = <<<SQL
    UPDATE tblHistorie SET skip = 1 WHERE hisId = :hisId
SQL;
        $args = [[':hisId', $hisId, self::INT]];
        $this->run_query($sql, $args);
    }

    public function updateDracht($fldDmDracht, $hisId_dr_db) {
        $sql = <<<SQL
                UPDATE tblHistorie SET datum = :fldDmDracht WHERE hisId = :hisId_dr_db
SQL;
        $args = [[':fldDmDracht', $fldDmDracht], [':hisId_dr_db', $hisId_dr_db]];
        $this->run_query($sql, $args);
    }

    public function insert_tblHistorie($stalId, $fldDmDracht) {
        $sql = <<<SQL
        INSERT INTO tblHistorie SET stalId = :stalId, datum = :fldDmDracht, actId = 19
SQL;
        $args = [[':stalId', $stalId, self::INT], [':fldDmDracht', $fldDmDracht]];
        $this->run_query($sql, $args);
        return $this->db->insert_id;
    }

    public function update_tblHistorie($hisId) {
        $sql = <<<SQL
    UPDATE tblHistorie SET skip = 1 WHERE hisId = :hisId
SQL;
        $args = [[':hisId', $hisId, self::INT]];
        $this->run_query($sql, $args);
    }

    public function omnummer($stalId, $fldDay, $levnr_old) {
        $sql = <<<SQL
        INSERT INTO tblHistorie set stalId = :stalId, datum = :fldDay, actId = 17, oud_nummer = :levnr_old
SQL;
        $args = [[':stalId', $stalId, self::INT], [':fldDay', $fldDay], [':levnr_old', $levnr_old]];
        return $this->run_query($sql, $args);
    }

    public function zoek_omnummering($stalId) {
        $sql = <<<SQL
        SELECT max(hisId) hisId
        FROM tblHistorie
        WHERE stalId = :stalId and actId = 17
SQL;
        $args = [[':stalId', $stalId, self::INT]];
        return $this->first_field($sql, $args);
    }

    public function insert_tblHistorie_18($fldStalIdMdr, $fldDrachtDay) {
        $sql = <<<SQL
    INSERT INTO tblHistorie set stalId = :fldStalIdMdr, datum = :fldDrachtDay, actId = 18
SQL;
        $args = [[':fldStalIdMdr', $fldStalIdMdr], [':fldDrachtDay', $fldDrachtDay]];
        $this->run_query($sql, $args);
        return $this->db->insert_id;
    }

    public function insert_tblHistorie_geb($stalId, $fldDag, $fldKg) {
        $sql = <<<SQL
        INSERT INTO tblHistorie set stalId = :stalId, datum = :fldDag, kg = :fldKg, actId = 1
SQL;
        $args = [[':stalId', $stalId, self::INT], [':fldDag', $fldDag], [':fldKg', $fldKg]];
        $this->run_query($sql, $args);
        return $this->db->insert_id;
    }

    public function insert_tblHistorie_14($stalId, $doodday) {
        $sql = <<<SQL
      INSERT INTO tblHistorie set stalId = :stalId, datum = :doodday, actId = 14
SQL;
        $args = [[':stalId', $stalId, self::INT], [':doodday', $doodday]];
        return $this->run_query($sql, $args);
    }

}
