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

}
