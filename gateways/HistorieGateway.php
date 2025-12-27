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
        $vw = $this->db->query("
SELECT date_format(datum,'%d-%m-%Y') datum
FROM tblHistorie
WHERE hisId = '".$this->db->real_escape_string($drachtMoment)."' and skip = 0
");
while ( $zd = $vw->fetch_assoc()) {
    $drachtdm = $zd['datum']; 
}
return $drachtdm ?? 0;
}

public function zoek_jaartal_eerste_dekking_dracht($lidId, $een_startjaar_eerder_gebruiker) {
   $vw = $this->db->query("
SELECT year(min(h.datum)) jaar
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE (actId = 18 or actId = 19)
 and skip = 0
 and u.lidId = '".$this->db->real_escape_string($lidId)."'
 and year(h.datum) >= '".$this->db->real_escape_string($een_startjaar_eerder_gebruiker)."'
");
    while($zj = $vw->fetch_assoc()) { $first_year_db = $zj['jaar']; }
return $first_year_db;
}

public function zoek_datum_verblijf_tijdens_dekking($lidId, $mdrId, $dmdek) {
   $vw = $this->db->query("
SELECT max(h.datum) datum
FROM tblHistorie h
 join tblBezet b on (h.hisId = b.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = '".$this->db->real_escape_string($lidId)."'
 and st.schaapId = '".$this->db->real_escape_string($mdrId)."'
 and h.datum <= '".$this->db->real_escape_string($dmdek)."'
"); 
while ($zdvtd = $vw->fetch_array()) 
{ $date_verblijf = $zdvtd['datum']; }
return $date_verblijf ?? null;
    }

public function zoek_hisId_verblijf_tijdens_dekking($lidId, $mdrId, $date_verblijf) {
   $vw = $this->db->query("
SELECT max(h.hisId) hisId
FROM tblHistorie h
 join tblBezet b on (h.hisId = b.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = '".$this->db->real_escape_string($lidId)."'
 and st.schaapId = '".$this->db->real_escape_string($mdrId)."'
 and h.datum = '".$this->db->real_escape_string($date_verblijf)."'
"); 
while ($zhvtd = $vw->fetch_array()) 
{ $hisId_verblijf = $zhvtd['hisId']; }
return $hisId_verblijf ?? null;
}

public function zoek_verblijf_tijdens_dekking($lidId, $hisId_verblijf, $dmdek) {
    $vw = $this->db->query("
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
    WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (b.hisId = uit.hisv)
 left join tblHistorie ht on (ht.hisId = uit.hist)
WHERE b.hisId = '".$this->db->real_escape_string($hisId_verblijf)."'
 and (isnull(uit.bezId) or ht.datum > '".$this->db->real_escape_string($dmdek)."')
"); 
while ($zvtd = $vw->fetch_array()) 
{ $verblijf = $zvtd['hoknr']; }
return $verblijf ?? null;
}

public function dagwegingen($lidId, $schaapId, $datum) {
    $vw = $this->db->query("
SELECT count(hisId) aant
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = '".$this->db->real_escape_string($lidId)."'
 and schaapId = '".$this->db->real_escape_string($schaapId)."'
 and datum = '".$this->db->real_escape_string($datum)."'
 and h.actId = 9
 and h.skip = 0
");
return $vw->fetch_row()[0];
}

public function eerste_datum_schaap($stalId) {
    $vw = $this->db->query(" 
        SELECT max(datum) datumfirst, date_format(max(datum),'%d-%m-%Y') datum1
        FROM tblHistorie
        WHERE stalId = '".$this->db->real_escape_string($stalId)."' and (actId = 1 or actId = 2 or actId = 11) and skip = 0
        ");
    return $vw->fetch_row()[0];
}

public function laatste_datum_schaap($stalId) {
   $vw = $this->db->query("
SELECT max(datum) datumend, date_format(max(datum),'%d-%m-%Y') enddatum
FROM tblHistorie
WHERE stalId = '".$this->db->real_escape_string($stalId)."' and (actId = 10 or actId = 12 or actId = 13 or actId = 14) and skip = 0
");
return $vw->fetch_row()[0];
}

public function wegen_invoeren($stalId, $datum, $newkg) {
    $this->db->query("
INSERT INTO tblHistorie
SET stalId = '".$this->db->real_escape_string($stalId)."',
datum = '".$this->db->real_escape_string($datum)."',
kg = '".$this->db->real_escape_string($newkg)."',
actId = 9 "
);
}

public function herstel_invoeren($stalId, $datum, $kg, $actId) {
    $this->db->query("
INSERT INTO tblHistorie
SET stalId = '".$this->db->real_escape_string($stalId)."', 
datum = '".$this->db->real_escape_string($datum)."',
kg = ".db_null_input($kg).",
actId = '".$this->db->real_escape_string($actId)."' "
);
    return $this->db->insert_id;
}

public function medicijn_invoeren($stalId, $datum) {
    $this->run_query(<<<SQL
 INSERT INTO tblHistorie SET stalId = :stalId,
        datum = :datum,
        actId= 8
SQL
    , [
        [':stalId', $stalId, self::INT],
        [':datum', $datum, self::DATE]
    ]
    );
}

public function weegaantal($lidId, $schaapId) {
    $vw = $this->db->query("
SELECT count(hisId) aant
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
 join tblSchaap s on (s.schaapId = st.schaapId)
WHERE u.lidId = '".$this->db->real_escape_string($lidId)."'
 and st.schaapId = '".$this->db->real_escape_string($schaapId)."'
 and h.actId = 9
 and h.skip = 0
");
return $vw->fetch_row()[0];
        }

# LET OP er is een weeg() in Historie en Schaap
public function weeg($lidId, $schaapId) {
    return $this->db->query("
SELECT datum, kg
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
 join tblSchaap s on (s.schaapId = st.schaapId)
WHERE u.lidId = '".$this->db->real_escape_string($lidId)."'
 and st.schaapId = '".$this->db->real_escape_string($schaapId)."'
 and h.actId = 9
 and h.skip = 0
ORDER BY datum desc
");
        }

public function zoek_geboorte($schaapId) {
    $vw = $this->db->query("
SELECT datum
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE h.actId = 1 and h.skip = 0 and st.schaapId = '".$this->db->real_escape_string($schaapId)."'
");
if ($vw->num_rows) {
    return $vw->fetch_row()[0];
}
return null;
    }

public function zoek_eerste_datum($schaapId) {
    $vw = $this->db->query("
SELECT min(datum) date1
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE h.skip = 0 and st.schaapId = '".$this->db->real_escape_string($schaapId)."'
");
if ($vw->num_rows) {
    return $vw->fetch_row()[0];
}
return null;
        }

public function insert_geboorte($stalId, $datum) {
    $this->db->query("INSERT INTO tblHistorie set stalId = '".$this->db->real_escape_string($stalId)."',
        datum = '".$this->db->real_escape_string($datum)."',
        actId = 1 ");
}

public function zoek_aanwasdatum($schaapId) {
    $vw = $this->db->query("
SELECT hisId, datum
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE st.schaapId = '".$this->db->real_escape_string($schaapId)."' and h.actId = 3 and h.skip = 0
");
if ($vw->num_rows) {
    return $vw->fetch_row();
}
return [null, null];
}

public function zoek_nietvoor_datum($lidId, $schaapId) {
    $vw = $this->db->query("
SELECT max(datum) date
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and st.schaapId = '".$this->db->real_escape_string($schaapId)."' and 
 actId = 4 and h.skip = 0
");
return $vw->fetch__row()[0];
}

public function zoek_nietvoor_datum_456($lidId, $schaapId) {
    $vw = $this->db->query("
SELECT max(datum) date
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and st.schaapId = '".$this->db->real_escape_string($schaapId)."' and 
 actId IN (4,5,6) and h.skip = 0
");
return $vw->fetch__row()[0];
}

public function zoek_afvoer_nietvoor_datum($lidId, $schaapId) {
    $vw = $this->db->query("
SELECT max(datum) date
From (
    SELECT h.datum, a.actie
    FROM tblActie a 
     join tblHistorie h on (a.actId = h.actId)
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and st.schaapId = '".$this->db->real_escape_string($schaapId)."' and 
     a.af != 1 and h.skip = 0

    union

    SELECT max(h.datum) datum, 'Laatste worp' actie
    FROM tblSchaap mdr
     join tblVolwas v on (mdr.schaapId = v.mdrId)
     join tblSchaap lam on (v.volwId = lam.volwId)
     join tblStal st on (st.schaapId = lam.schaapId)
     join tblUbn u on (st.ubnId = u.ubnId)
     join tblHistorie h on (st.stalId = h.stalId and h.actId = 1 and h.skip = 0)
    WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and mdr.schaapId = '".$this->db->real_escape_string($schaapId)."'
    GROUP BY mdr.schaapId, h.actId
    HAVING (max(h.datum) > min(h.datum))
) datum
");
return $vw->fetch_row()[0];
  }

public function zoek_nietna_datum($lidId, $schaapId) {
    $vw = $this->db->query("
SELECT min(datum) date
From (
    SELECT datum, actie
    FROM tblActie a
     join tblHistorie h on (a.actId = h.actId)
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE u.lidId = '".$this->db->real_escape_string($lidId)."'
 and st.schaapId = '".$this->db->real_escape_string($schaapId)."'
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
    WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and mdr.schaapId = '".$this->db->real_escape_string($schaapId)."'
) datum
");
return $vw->fetch_row()[0];
}

public function update_aanwas($hisId, $datum) {
    $this->db->query("UPDATE tblHistorie set datum = '".$this->db->real_escape_string($datum)."' WHERE hisId = '".$this->db->real_escape_string($hisId)."' ");
}

public function zoek_speendm($schaapId) {
    $vw = $this->db->query("
SELECT hisId, datum
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE st.schaapId = '".$this->db->real_escape_string($schaapId)."' and h.actId = 4 and h.skip = 0
");
if ($vw->num_rows) {
    return $vw->fetch_row();
}
return [null, null];
}

public function zoek_speen_nietvoor_datum($lidId, $schaapId) {
    $vw = $this->db->query("
SELECT max(datum) date
From (
    SELECT datum
    FROM tblHistorie h
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and st.schaapId = '".$this->db->real_escape_string($schaapId)."' and 
     (actId = 1 or actId = 2) and h.skip = 0

    union

    SELECT datum
    FROM tblHistorie h
     join tblBezet b on (h.hisId = b.hisId)
     join tblPeriode p on (p.periId = b.periId)
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and st.schaapId = '".$this->db->real_escape_string($schaapId)."' and p.doelId = 1 and (h.actId = 5 or h.actId = 6) and h.skip = 0
) datum
");
if ($vw->num_rows) {
    return $vw->fetch_row()[0];
}
return null;
    }

public function controle_nietna_datum($lidId, $schaapId) {
   $vw = $this->db->query("
SELECT min(datum) date
From (
    SELECT datum
    FROM tblHistorie h
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and st.schaapId = '".$this->db->real_escape_string($schaapId)."' and (h.actId = 3 or h.actId = 10 or h.actId = 12 or h.actId = 14) and h.skip = 0

    union

    SELECT datum
    FROM tblHistorie h
     join tblBezet b on (h.hisId = b.hisId)
     join tblPeriode p on (p.periId = b.periId)
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and st.schaapId = '".$this->db->real_escape_string($schaapId)."' and p.doelId = 2 and (h.actId = 5 or h.actId = 6) and h.skip = 0
) datum
");
if ($vw->num_rows) {
    return $vw->fetch_row()[0];
}
return null;
}

public function update_speendatum($hisId, $datum) {
        $this->db->query("UPDATE tblHistorie h set h.datum = '".$this->db->real_escape_string($datum)."'    WHERE hisId = '".$this->db->real_escape_string($hisId)."' ");
        }

public function zoek_speenkg($schaapId) {
    $vw = $this->db->query("
SELECT hisId, kg speenkg
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE st.schaapId = '".$this->db->real_escape_string($schaapId)."' and h.actId = 4 and h.skip = 0
");
if ($vw->num_rows) {
    return $vw->fetch_row();
}
return [null, null];
}

public function update_speenkg($hisId, $kg) {
    $this->db->query("UPDATE tblHistorie h set h.kg = '".$this->db->real_escape_string($kg)."' WHERE hisId = '".$this->db->real_escape_string($hisId)."' ");
}

public function zoek_afvoerdm($schaapId) {
$vw = $this->db->query("
SELECT hisId, datum
FROM tblActie a
 join tblHistorie h on (a.actId = h.actId)
 join tblStal st on (h.stalId = st.stalId)
WHERE st.schaapId = '".$this->db->real_escape_string($schaapId)."' and a.af = 1 and h.skip = 0
");
if ($vw->num_rows) {
    return $vw->fetch_row();
}
return [null, null];
}

public function update_afvoerdm($hidId, $datum) {
    $this->db->query("UPDATE tblHistorie h set h.datum = '".$this->db->real_escape_string($datum)."' WHERE hisId = '".$this->db->real_escape_string($hidId)."' ");
}

public function zoek_afvoerkg($schaapId) {
    $vw = $this->db->query("
SELECT kg
FROM tblActie a
 join tblHistorie h on (a.actId = h.actId)
 join tblStal st on (h.stalId = st.stalId)
WHERE st.schaapId = '".$this->db->real_escape_string($schaapId)."' and a.af = 1 and h.skip = 0 ");
if ($vw->num_rows) {
    return $vw->fetch_row()[0];
}
return null;
}

public function update_afvoerkg($schaapId, $kg) {
    $this->db->query("
    UPDATE tblActie a
     join tblHistorie h on (a.actId = h.actId)
     join tblStal st on (h.stalId = st.stalId)
    set h.kg = '".$this->db->real_escape_string($kg)."'
    WHERE st.schaapId = '".$this->db->real_escape_string($schaapId)."' and a.af = 1 and h.skip = 0 ");
}

public function insert_afvoer($stalId, $dmafv) {
    $this->db->query("INSERT INTO tblHistorie set stalId = '".$this->db->real_escape_string($stalId)."',
        datum = '".$this->db->real_escape_string($dmafv)."',
        actId = 3 ");
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
    $this->db->query("INSERT INTO tblHistorie set stalId = '".$this->db->real_escape_string($stalId)."',
        datum = '".$this->db->real_escape_string($datum)."',
        actId = '".$this->db->real_escape_string($actId)."' ");
}

public function zoek_vorige_weging($schaapId, $date) {
$vw = $this->db->query("
SELECT max(hisId) vorige_weging
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
WHERE st.schaapId = '".$this->db->real_escape_string($schaapId)."'
 and h.datum < '".$this->db->real_escape_string($date)."'
 and h.kg is not null
");
return $vw->fetch_row()[0];
}

public function zoek_actie_vorige_weging($hisId) {
    $vw = $this->db->query("
SELECT h.actId, actie, h.datum, kg
FROM tblHistorie h
 join tblActie a on (h.actId = a.actId)
WHERE h.hisId = '".$this->db->real_escape_string($hisId)."'
");
if ($vw->num_rows) {
    return $vw->fetch_assoc();
}
return null;
}

public function zoek_acties($lidId) {
return $this->db->query("
SELECT h.actId, a.actie
FROM tblHistorie h
 join tblActie a on (h.actId = a.actId)
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and h.kg is not null
GROUP BY h.actId, a.actie
ORDER BY h.actId
");
}

public function zoek_datum_na($lidId, $schaapId) {
    $vw = $this->db->query("
SELECT max(datum) date
FROM (
     SELECT h.datum
     FROM tblHistorie h
      join tblStal st on (h.stalId = st.stalId)
     WHERE st.schaapId = '".$this->db->real_escape_string($schaapId)."' and actId = 1 and skip = 0
  union
     SELECT max(h.datum) dmaank
     FROM tblHistorie h
      join tblStal st on (h.stalId = st.stalId)
      join tblUbn u on (st.ubnId = u.ubnId)
     WHERE st.schaapId = '".$this->db->real_escape_string($schaapId)."' and u.lidId = '".$this->db->real_escape_string($lidId)."' and actId = 2 and skip = 0
  union
      SELECT h.datum
     FROM tblHistorie h
      join tblStal st on (h.stalId = st.stalId)
     WHERE st.schaapId = '".$this->db->real_escape_string($schaapId)."' and actId = 4 and skip = 0
) dm_na    
");
return $vw->fetch_row()[0];
}

public function zoek_datum_vanaf($lidId, $schaapId) {
    $vw = $this->db->query("
SELECT max(h.datum) date, date_format(max(h.datum),'%d-%m-%Y') datum
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE st.schaapId = '".$this->db->real_escape_string($schaapId)."'
 and u.lidId = '".$this->db->real_escape_string($lidId)."'
 and (actId = 3 or actId = 7)
 and skip = 0
");
return $vw->fetch_row()[0];
}

public function zoek_uitschaardatum($last_stalId) {
    $vw = $this->db->query("
SELECT datum date
FROM tblHistorie
WHERE stalId = '".$this->db->real_escape_string($last_stalId)."' and actId = 10
");
if ($vw->num_rows) {
    return $vw->fetch_row()[0];
}
return null;
}

public function zoek_laatste_hisid($lidId, $schaapId) {
    return $this->first_field("
SELECT max(hisId) hisId
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and st.schaapId = '".$this->db->real_escape_string($schaapId)."'
");
}

public function zoek_afgevoerd($maxhis) {
    return $this->db->query("
SELECT h.hisId afvhisId, date_format(h.datum,'%d-%m-%Y') afvoerdm, h.kg afvoerkg, h.actId, a.actie, lower(a.actie) status
FROM tblHistorie h
 join tblActie a on (h.actId = a.actId)
WHERE hisId = '".$this->db->real_escape_string($maxhis)."' and a.af = 1
");    
    }

public function zoek_laatste_verblijf($lidId, $schaapId) {
    return $this->first_field("
SELECT max(h.hisId) hisId
FROM tblHistorie h
 join tblBezet b on (b.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = '".$this->db->real_escape_string($lidId)."' and st.schaapId = '".$this->db->real_escape_string($schaapId)."'
");
    }

public function zoek_dier_uit_verblijf($lst_bezet, $schaapId) {
    return $this->first_field("
SELECT h.actId
FROM tblHistorie h
 join tblActie a on (h.actId = a.actId)
 join tblStal st on (h.stalId = st.stalId)
WHERE hisId > '".$this->db->real_escape_string($lst_bezet)."' and a.uit = 1 and st.schaapId = '".$this->db->real_escape_string($schaapId)."'
");
    }

public function skip($hisId) {
    $this->db->query("UPDATE tblHistorie SET skip=1 WHERE hisId=".$this->db->real_escape_string($hisId));
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

}
