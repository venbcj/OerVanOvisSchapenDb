<?php

class VolwasGateway extends Gateway {

    public function zoek_laatste_koppel_na_laatste_worp_obv_moeder($kzlMdr) {
        $vw = $this->db->query("
SELECT max(v.volwId) volwId
FROM tblVolwas v
 left join tblHistorie dek on (dek.hisId = v.hisId)
 left join tblSchaap lam on (lam.volwId = v.volwId)
WHERE (isnull(dek.skip) or dek.skip = 0) and isnull(lam.volwId) and v.mdrId = '".$this->db->real_escape_string($kzlMdr)."'
");
return $vw->fetch_row()[0];
    }

    public function zoek_moeder_vader_uit_laatste_koppel($koppel) {
        $vw = $this->db->query("
SELECT mdrId, vdrId, v.hisId his_dek, d.hisId his_dracht
FROM tblVolwas v
 left join tblDracht d on (d.volwId = v.volwId) 
 left join tblHistorie hd on (hd.hisId = d.hisId)
WHERE (isnull(hd.skip) or hd.skip = 0) and v.volwId = '".$this->db->real_escape_string($koppel)."'
");
$lst_mdr = 0;
$lst_vdr = 0;
$dekMoment = 0;
$drachtMoment = 0;
    while ( $v_m = $vw->fetch_assoc()) { 
        $lst_mdr = $v_m['mdrId']; 
        $lst_vdr = $v_m['vdrId']; 
        $dekMoment = $v_m['his_dek']; 
        $drachtMoment = $v_m['his_dracht']; }
return [$lst_mdr, $lst_vdr, $dekMoment, $drachtMoment];
}

public function vroegst_volgende_dekdatum($kzlMdr) {
    $vw = $this->db->query("
SELECT date_add(max(h.datum),interval 60 day) datum
FROM tblVolwas v
 join tblSchaap lam on (lam.volwId = v.volwId)
 join tblStal st on (st.schaapId = lam.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
WHERE mdrId = '".$this->db->real_escape_string($kzlMdr)."' and h.actId = 1 and h.skip = 0
");

while ( $row = $vw->fetch_assoc()) { $vroegst_volgende_dekdatum = $row['datum']; } 
return $vroegst_volgende_dekdatum ?? null;
}

public function zoek_volwas($schaapId) {
    return $this->first_field(<<<SQL
SELECT max(volwId) volwId
FROM tblVolwas
WHERE mdrId = :schaapId
 OR vdrId = :schaapId
SQL
    , [[':schaapId', $schaapId, self::INT]]
    );
}

public function zoek_laatste_worp_moeder($mdrId) {
   $vw = $this->db->query("
SELECT max(v.volwId) max_worp
FROM tblVolwas v
 join tblSchaap s on (v.volwId = s.volwId)
WHERE v.mdrId = '" . $this->db->real_escape_string($mdrId) . "'
");
return $vw->fetch_row()[0];
}

public function zoek_dekkingen($lidId, $Karwerk, $jaar) {
    return $this->db->query("
SELECT v.volwId, v.hisId, dekdate, dekdatum, v.mdrId, right(mdr.levensnummer,$Karwerk) mdr, v.vdrId,
 count(lam.schaapId) lamrn, drachtdatum, v.grootte, werpdatum,
lst_volwId
FROM tblVolwas v
 join tblSchaap mdr on (v.mdrId = mdr.schaapId)
 join tblStal stm on (stm.schaapId = mdr.schaapId)
 join tblHistorie h on (stm.stalId = h.stalId and v.hisId = h.hisId)
 left join (
     SELECT hisId, h.datum dekdate, date_format(h.datum,'%d-%m-%Y') dekdatum, year(h.datum) dekjaar, skip
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
     WHERE actId = 18 and skip = 0 and st.lidId = '".$this->db->real_escape_string($lidId)."'
 ) dek on (v.hisId = dek.hisId)
 left join tblSchaap vdr on (v.vdrId = vdr.schaapId)
 left join (
    SELECT d.volwId, date_format(h.datum,'%d-%m-%Y') drachtdatum, year(h.datum) drachtjaar
     FROM tblDracht d 
     join tblHistorie h on (h.hisId = d.hisId)
     join tblStal st on (st.stalId = h.stalId)
    WHERE actId = 19 and h.skip = 0 and st.lidId = '".$this->db->real_escape_string($lidId)."'
 ) dra on (dra.volwId = v.volwId)
 left join tblSchaap lam on (lam.volwId = v.volwId)
 left join tblStal stl on (stl.schaapId = lam.schaapId)
 left join (
     SELECT stalId, date_format(datum,'%d-%m-%Y') werpdatum, year(date_add(datum,interval -145 day)) dekjaar_obv_worp
     FROM tblHistorie
     WHERE actId = 1 and skip = 0
 ) hl on (stl.stalId = hl.stalId)
 join (
    SELECT v.mdrId, max(v.volwId) lst_volwId
   FROM tblVolwas v
    left join (
       SELECT hisId
      FROM tblHistorie
      WHERE actId = 18 and skip = 0
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
       WHERE h.actId = 3 and h.skip = 0
    ) ha on (k.schaapId = ha.schaapId)
    WHERE (dek.hisId is not null or dra.volwId is not null) and isnull(ha.schaapId)
    GROUP BY mdrId
 ) lst_v on (lst_v.mdrId = v.mdrId)
WHERE stm.lidId = '".$this->db->real_escape_string($lidId)."'
 and (isnull(stl.lidId) or stl.lidId = '".$this->db->real_escape_string($lidId)."')
 and (dekdatum is not null or drachtdatum is not null)
 and coalesce(dekjaar, dekjaar_obv_worp, drachtjaar) = '".$this->db->real_escape_string($jaar)."'
 and isnull(stm.rel_best)
GROUP BY v.volwId, v.hisId, dekdatum, v.mdrId, mdr.levensnummer, v.vdrId, drachtdatum, werpdatum, v.grootte
ORDER BY right(mdr.levensnummer,$Karwerk), dekdate desc
");
}

public function insert_ouders($mdrId, $vdrId) {
    $this->db->query("INSERT INTO tblVolwas set mdrId = ".db_null_input($mdrId).", vdrId = ".db_null_input($vdrId));
    }

public function zoek_ouders($mdrId, $vdrId) {
    $vw = $this->db->query("
        SELECT max(volwId) volwId
        FROM tblVolwas
        WHERE ".db_null_filter('mdrId', $mdrId) . " and " . db_null_filter('vdrId', $vdrId));
    return $vw->fetch_row()[0];
}

}
