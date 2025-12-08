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
    $this->db->query("INSERT INTO tblHistorie
        SET stalId = '".$this->db->real_escape_string($stalId)."',
 datum = '".$this->db->real_escape_string($datum)."',
 kg = '".$this->db->real_escape_string($newkg)."',
 actId = 9 ");
}

public function herstel_invoeren($stalId, $datum, $kg, $actId) {
    $this->db->query("INSERT INTO tblHistorie SET stalId = '".$this->db->real_escape_string($stalId)."', 
    datum = '".$this->db->real_escape_string($datum)."',
 kg = ".db_null_input($kg).",
 actId = '".$this->db->real_escape_string($actId)."' ");
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
WHERE st.schaapId = '".$this->db->real_escape_string($schaapId)."' and h.datum < '".$this->db->real_escape_string($date)."' and h.kg is not null
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

}
