<?php

class HistorieGateway extends Gateway {

    public function zoek_eerste_datum_stalop($recId) {
        $first_day = null;
        $eerste_dag = null;
        $vw = $this->db->query("
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
            while ($mi = $vw->fetch_assoc()) {
                $first_day = $mi['date'];
                $eerste_dag = $mi['datum'];
            }
        // TODO: #0004135 record teruggeven ipv anonieme array?
        // TODO in een veel later stadium: opnemen in Transactie, samen met validatie
        return [$first_day, $eerste_dag];
    }

    public function setDatum($day, $recId) {
        $this->db->query("
 UPDATE tblHistorie h
  join tblMelding m on (h.hisId = m.hisId)
 set   h.datum  = '".$this->db->real_escape_string($day)."'
 WHERE m.meldId = '$recId' 
 ");
    }

    public function zoek_dekdatum($dekMoment) {
        $zoek_dekdatum = $this->db->query("
SELECT date_format(datum,'%d-%m-%Y') datum, year(datum) jaar
FROM tblHistorie
WHERE hisId = '".$this->db->real_escape_string($dekMoment)."' and skip = 0
    ");
        $dekdm = 0;
        $dekjaar = 0;
        while ( $zd = $zoek_dekdatum->fetch_assoc()) {
            $dekdm = $zd['datum']; $dekjaar = $zd['jaar']; 
        }
        return [$dekdm, $dekjaar];
        }

    public function zoek_drachtdatum($drachtMoment) {
        $zoek_drachtdatum = $this->db->query("
SELECT date_format(datum,'%d-%m-%Y') datum
FROM tblHistorie
WHERE hisId = '".$this->db->real_escape_string($drachtMoment)."' and skip = 0
");
while ( $zd = $zoek_drachtdatum->fetch_assoc()) {
    $drachtdm = $zd['datum']; 
}
return $drachtdm ?? 0;
}

public function zoek_jaartal_eerste_dekking_dracht($lidId, $een_startjaar_eerder_gebruiker) {
   $vw = $this->db->query("
SELECT year(min(h.datum)) jaar
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
WHERE (actId = 18 or actId = 19)
 and skip = 0
 and st.lidId = '".$this->db->real_escape_string($lidId)."'
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
WHERE st.lidId = '".$this->db->real_escape_string($lidId)."'
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
WHERE st.lidId = '".$this->db->real_escape_string($lidId)."'
 and st.schaapId = '".$this->db->real_escape_string($mdrId)."'
 and h.datum = '".$this->db->real_escape_string($date_verblijf)."'
"); 
while ($zhvtd = $zoek_hisId_verblijf_tijdens_dekking->fetch_array()) 
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
    WHERE st.lidId = '".$this->db->real_escape_string($lidId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
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

}
