<?php

class SchaapGateway extends Gateway {

    public function zoek_schaapid($fldLevnr) {
        $vw = mysqli_query($this->db, "
SELECT schaapId
FROM tblSchaap 
WHERE levensnummer = '".mysqli_real_escape_string($this->db, $fldLevnr)."'");
$rec = mysqli_fetch_assoc($vw);
return $rec['schaapId'] ?? 0;
            # TODO: nullcheck. Als fldLevnr niet voorkomt, is zs geen array, en dat geeft een warning.
        # Dit wijst erop dat de code dingen doet die niet bij elkaar horen.
    }

    public function count_levnr($fldLevnr, $schaapId) {
        $vw = mysqli_query($this->db, "
SELECT count(*) aant
FROM tblSchaap 
WHERE levensnummer = '".mysqli_real_escape_string($this->db, $fldLevnr)."' and schaapId <> '".mysqli_real_escape_string($this->db, $schaapId)."'");
$rec = mysqli_fetch_assoc($vw);
return $rec['aant'];
    }

    // deze handeling heet "change" omdat het sleutelveld verandert
    public function changeLevensnummer($old, $new) {
        mysqli_query($this->db, "
UPDATE tblSchaap SET levensnummer = '".mysqli_real_escape_string($this->db, $new)."'
        WHERE levensnummer = '".mysqli_real_escape_string($this->db, $old)."' ");
    }

    public function updateGeslacht($levensnummer, $geslacht) {
        mysqli_query($this->db, "
UPDATE tblSchaap SET geslacht='".mysqli_real_escape_string($this->db, $geslacht)."'
        WHERE levensnummer = '".mysqli_real_escape_string($this->db, $levensnummer)."' ");
    }

    public function aantalLamOpStal($lidId) {
        $sekse = "(isnull(s.geslacht) or s.geslacht is not null)";
        $ouder = 'isnull(prnt.schaapId)';
        return $this->countByStalFase($lidId, $sekse, $ouder);
    }

    public function aantalOoiOpStal($lidId) {
        $sekse = "s.geslacht = 'ooi'";
        $ouder = 'prnt.schaapId is not null';
        return $this->countByStalFase($lidId, $sekse, $ouder);
    }

    public function aantalRamOpStal($lidId) {
        $sekse = "s.geslacht = 'ram'";
        $ouder = 'prnt.schaapId is not null';
        return $this->countByStalFase($lidId, $sekse, $ouder);
    }

    private function countByStalFase($lidid, $Sekse, $Ouder) {
$vw = mysqli_query($this->db, "
SELECT count(distinct(s.schaapId)) aant 
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId) 
WHERE st.lidId = '".mysqli_real_escape_string($this->db, $lidid)."' and isnull(st.rel_best) and ".$Sekse." and ".$Ouder." 
");
if ($vw) {
            $row = mysqli_fetch_assoc($vw);
            return $row['aant'];
}
return false; // Foutafhandeling
    }

    public function aantalLamUitschaar($lidId) {
        $sekse = "(isnull(s.geslacht) or s.geslacht is not null)";
        $ouder = 'isnull(prnt.schaapId)';
        return $this->countByFaseUitgeschaard($lidId, $sekse, $ouder);
    }

    public function aantalOoiUitschaar($lidId) {
        $sekse = "s.geslacht = 'ooi'";
        $ouder = 'prnt.schaapId is not null';
        return $this->countByFaseUitgeschaard($lidId, $sekse, $ouder);
    }

    public function aantalRamUitschaar($lidId) {
        $sekse = "s.geslacht = 'ram'";
        $ouder = 'prnt.schaapId is not null';
        return $this->countByFaseUitgeschaard($lidId, $sekse, $ouder);
    }

    private function countByFaseUitgeschaard($lidid, $Sekse, $Ouder) {
        $vw = mysqli_query($this->db, "
SELECT count(distinct(s.schaapId)) aant 
FROM tblSchaap s
 join (
     SELECT lidId, schaapId, max(stalId) stalId
     FROM tblStal
     WHERE lidId = '".mysqli_real_escape_string($this->db, $lidid)."'
     GROUP BY lidId, schaapId
  ) mst on (mst.schaapId = s.schaapId)
 join (
     SELECT h.stalId, h.actId
     FROM tblHistorie h
      join tblStal st on (h.stalId = st.stalId)
     WHERE h.actId = 10
 ) haf on (haf.stalId = mst.stalId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId) 
WHERE mst.lidId = '".mysqli_real_escape_string($this->db, $lidid)."' and ".$Sekse." and ".$Ouder." 
");
if ($vw) {
    $row = mysqli_fetch_assoc($vw);
    return $row['aant'];
}
return false; // Foutafhandeling
    }

    // Functie die het aantal lammeren, moederdieren of vaders telt
    public function med_aantal_fase($lidid,$M,$J,$V,$Sekse,$Ouder) {
        $vw_totaalFase = mysqli_query($this->db,"
SELECT count(distinct s.levensnummer) werknrs
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
 join tblNuttig n on (h.hisId = n.hisId)
 join tblInkoop i on (n.inkId = i.inkId)
 left join (
    SELECT st.schaapId, h.hisId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) oudr on (s.schaapId = oudr.schaapId)
WHERE h.skip = 0 and month(h.datum) = $M and date_format(h.datum,'%Y') = $J and i.artId = $V and ".$Sekse." and ".$Ouder."
    and st.lidId = '".mysqli_real_escape_string($this->db,$lidid)."' and h.actId = 8
GROUP BY date_format(h.datum,'%Y%m')
");
if($vw_totaalFase) {
    $row = mysqli_fetch_assoc($vw_totaalFase);
                return $row['werknrs'];
        }
        return FALSE; // Foutafhandeling
}


}
