<?php

class MeldingGateway extends Gateway {

    public function zoek_bestemming($recId) {
    $vw = mysqli_query($this->db, "
SELECT st.rel_best
FROM tblMelding m
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
WHERE m.meldId = '$recId'
");
$fldBest = null;
            while ($zbid = mysqli_fetch_assoc($vw)) {
                $fldBest = $zbid['rel_best'];
            }
return $fldBest;
    }

    public function updateSkip($recId, $fldSkip) {
        mysqli_query($this->db, "
UPDATE tblMelding SET skip = '".mysqli_real_escape_string($this->db, $fldSkip)."', fout = NULL
WHERE meldId = '$recId' ");
    }

    public function updateFout($recId, $wrong) {
        mysqli_query($this->db, "
UPDATE tblMelding SET fout = " . db_null_input($wrong ?? null) . "
WHERE meldId = '$recId' and skip <> 1");
    }

    // Aantal dieren goed geregistreerd om automatisch te kunnen melden. De datum mag hier niet liggen na de afvoerdatum.
    public function aantal_oke_Omnum($fldReqId) {
        $juistaantal = mysqli_query ($this->db,"
SELECT count(*) aant 
FROM tblMelding m
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (st.schaapId = s.schaapId)
 left join (
    SELECT schaapId, max(datum) datum 
    FROM tblHistorie h 
     join tblStal st on (h.stalId = st.stalId)
     join tblActie a on (h.actId = a.actId)
    WHERE a.af = 1 and h.skip = 0
    GROUP BY schaapId
 ) afv on (st.schaapId = afv.schaapId)
WHERE m.reqId = '".mysqli_real_escape_string($this->db,$fldReqId)."'
 and h.skip = 0
 and h.datum is not null
 and (h.datum <= afv.datum or isnull(afv.datum))
 and LENGTH(RTRIM(CAST(s.levensnummer AS UNSIGNED))) = 12 
 and m.skip <> 1
");
    if($juistaantal)
    {    $row = mysqli_fetch_assoc($juistaantal);
            return $row['aant'];
    }
    return FALSE;
}

// Aantal dieren goed geregistreerd om automatisch te kunnen melden.
public function aantal_oke_uitv($lidid,$fldReqId,$nestHistorieDm) {
$juistaantal = mysqli_query ($this->db,"
SELECT count(*) aant
FROM tblMelding m
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (st.schaapId = s.schaapId) 
 join (
    SELECT schaapId, max(datum) lastdatum 
    FROM (".$nestHistorieDm.") hd 
    WHERE hd.actId != 14 and actie != 'Gevoerd' and actie not like '% gemeld'
    GROUP BY schaapId
 ) mhd on (st.schaapId = mhd.schaapId)
 join tblRelatie r on (r.relId = st.rel_best)
 join tblPartij p on (r.partId = p.partId)
WHERE m.reqId = '".mysqli_real_escape_string($this->db,$fldReqId)."'
 and h.datum is not null
 and h.datum >= mhd.lastdatum
 and h.datum <= curdate()
 and LENGTH(RTRIM(CAST(s.levensnummer AS UNSIGNED))) = 12 
 and p.ubn is not null    
 and m.skip <> 1
 and h.skip = 0                            
");
    if($juistaantal)
    {    $row = mysqli_fetch_assoc($juistaantal);
            return $row['aant'];
    }
    return FALSE;
}

public function aantal_oke_afv($lidid,$fldReqId,$nestHistorieDm) {
$juistaantal = mysqli_query ($this->db,"
SELECT count(*) aant
FROM tblMelding m
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (st.schaapId = s.schaapId)
 join ( 
    SELECT schaapId, max(datum) lastdatum 
    FROM (".$nestHistorieDm.") hd
     left join tblActie a on (hd.actId = a.actId)
    WHERE (a.af = 0 or isnull(a.af)) and hd.actie != 'Gevoerd' and hd.actie not like '% gemeld'
    GROUP BY schaapId
 ) mhd on (s.schaapId = mhd.schaapId)
WHERE m.reqId = '".mysqli_real_escape_string($this->db,$fldReqId)."' 
 and h.datum is not null
 and h.datum >= mhd.lastdatum
 and h.datum <= (curdate() + interval 3 day)
 and LENGTH(RTRIM(CAST(s.levensnummer AS UNSIGNED))) = 12 
 and st.rel_best is not null
 and m.skip <> 1
 and h.skip = 0
");
    if($juistaantal)
    {    $row = mysqli_fetch_assoc($juistaantal);
            return $row['aant'];
    }
    return FALSE;
}

}
