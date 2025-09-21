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

}
