<?php

class HistorieGateway extends Gateway {

    public function zoek_eerste_datum_stalop($recId) {
        $first_day = null;
        $eerste_dag = null;
        $vw = mysqli_query($this->db, "
SELECT min(datum) date, date_format(min(datum),'%d-%m-%Y') datum
FROM tblHistorie h
 join tblActie a on (a.actId = h.actId)
 join (
    SELECT st.stalId, h.hisId
    FROM tblStal st
     join tblHistorie h on (h.stalId = st.stalId)
     join tblMelding m on (m.hisId = h.hisId)
    WHERE m.meldId = '$recId'
 ) st on (st.stalId = h.stalId and st.hisId <> h.hisId)
 WHERE a.op = 1
");
            while ($mi = mysqli_fetch_assoc($vw)) {
                $first_day = $mi['date'];
                $eerste_dag = $mi['datum'];
            }
        // TODO: #0004135 record teruggeven ipv anonieme array?
        // TODO in een veel later stadium: Tell, Don't Ask -> verplaats het gedrag op basis van deze data hierheen
        return [$first_day, $eerste_dag];
    }

    public function setDatum($day, $recId) {
        mysqli_query($this->db, "
 UPDATE tblHistorie h
  join tblMelding m on (h.hisId = m.hisId)
 set   h.datum  = '".mysqli_real_escape_string($this->db, $day)."'
 WHERE m.meldId = '$recId' 
 ");
    }

    public function zoek_dekdatum($dekMoment) {
        $zoek_dekdatum = mysqli_query($this->db,"
SELECT date_format(datum,'%d-%m-%Y') datum, year(datum) jaar
FROM tblHistorie
WHERE hisId = '".mysqli_real_escape_string($this->db,$dekMoment)."' and skip = 0
    ");
        $dekdm = 0;
        $dekjaar = 0;
        while ( $zd = mysqli_fetch_assoc($zoek_dekdatum)) {
            $dekdm = $zd['datum']; $dekjaar = $zd['jaar']; 
        }
        return [$dekdm, $dekjaar];
        }

    public function zoek_drachtdatum($drachtMoment) {
        $zoek_drachtdatum = mysqli_query($this->db,"
SELECT date_format(datum,'%d-%m-%Y') datum
FROM tblHistorie
WHERE hisId = '".mysqli_real_escape_string($this->db,$drachtMoment)."' and skip = 0
") or die (mysqli_error($this->db));
while ( $zd = mysqli_fetch_assoc($zoek_drachtdatum)) {
    $drachtdm = $zd['datum']; 
}
return $drachtdm ?? 0;
}

public function zoek_jaartal_eerste_dekking_dracht($lidId, $een_startjaar_eerder_gebruiker) {
   $vw = mysqli_query($this->db,"
SELECT year(min(h.datum)) jaar
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
WHERE (actId = 18 or actId = 19)
 and skip = 0
 and st.lidId = '".mysqli_real_escape_string($this->db,$lidId)."'
 and year(h.datum) >= '".mysqli_real_escape_string($this->db,$een_startjaar_eerder_gebruiker)."'
");
    while($zj = mysqli_fetch_assoc($vw)) { $first_year_db = $zj['jaar']; }
return $first_year_db;
}

public function zoek_datum_verblijf_tijdens_dekking($lidId, $mdrId, $dmdek) {
   $vw = mysqli_query($this->db,"
SELECT max(h.datum) datum
FROM tblHistorie h
 join tblBezet b on (h.hisId = b.hisId)
 join tblStal st on (h.stalId = st.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($this->db,$lidId)."'
 and st.schaapId = '".mysqli_real_escape_string($this->db,$mdrId)."'
 and h.datum <= '".mysqli_real_escape_string($this->db,$dmdek)."'
"); 
while ($zdvtd = mysqli_fetch_array($vw)) 
{ $date_verblijf = $zdvtd['datum']; }
return $date_verblijf ?? null;
    }

public function zoek_hisId_verblijf_tijdens_dekking($lidId, $mdrId, $date_verblijf) {
   $vw = mysqli_query($this->db,"
SELECT max(h.hisId) hisId
FROM tblHistorie h
 join tblBezet b on (h.hisId = b.hisId)
 join tblStal st on (h.stalId = st.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($this->db,$lidId)."'
 and st.schaapId = '".mysqli_real_escape_string($this->db,$mdrId)."'
 and h.datum = '".mysqli_real_escape_string($this->db,$date_verblijf)."'
"); 
while ($zhvtd = mysqli_fetch_array($zoek_hisId_verblijf_tijdens_dekking)) 
{ $hisId_verblijf = $zhvtd['hisId']; }
return $hisId_verblijf ?? null;
}

public function zoek_verblijf_tijdens_dekking($lidId, $hisId_verblijf, $dmdek) {
    $vw = mysqli_query($this->db,"
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
    WHERE st.lidId = '".mysqli_real_escape_string($this->db,$lidId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (b.hisId = uit.hisv)
 left join tblHistorie ht on (ht.hisId = uit.hist)
WHERE b.hisId = '".mysqli_real_escape_string($this->db,$hisId_verblijf)."'
 and (isnull(uit.bezId) or ht.datum > '".mysqli_real_escape_string($this->db,$dmdek)."')
"); 
while ($zvtd = mysqli_fetch_array($vw)) 
{ $verblijf = $zvtd['hoknr']; }
return $verblijf ?? null;
}

}
