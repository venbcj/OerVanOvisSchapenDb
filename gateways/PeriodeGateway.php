<?php

class PeriodeGateway extends Gateway {

    public function zoek_laatste_afsluitdm_geb($hokId) {
$vw = mysqli_query($this->db,"
SELECT max(dmafsluit) dmstop
FROM tblPeriode
WHERE hokId = '".mysqli_real_escape_string($this->db,$hokId)."' and doelId = 1 and dmafsluit is not null
") or die (mysqli_error($this->db));
if ($vw->num_rows == 0) {
    return null;
}
return $vw->fetch_row()[0];
    }

    public function zoek_laatste_afsluitdm_spn($hokId) {
$vw = mysqli_query($this->db,"
SELECT max(dmafsluit) dmstop
FROM tblPeriode
WHERE hokId = '".mysqli_real_escape_string($this->db,$hokId)."' and doelId = 2 and dmafsluit is not null
") or die (mysqli_error($this->db));
if ($vw->num_rows == 0) {
    return null;
}
return $vw->fetch_row()[0];
    }

}
