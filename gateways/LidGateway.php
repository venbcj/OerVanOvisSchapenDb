<?php

class LidGateway extends Gateway {

    public function hasCompleteRvo($lidId) {
        $vw = mysqli_query($this->db, "
SELECT 1
FROM tblLeden
WHERE lidId = '".mysqli_real_escape_string($this->db, $lidId)."'
AND relnr IS NOT NULL
AND urvo IS NOT NULL
AND prvo IS NOT NULL
");
return mysqli_num_rows($vw) > 0;
    }

}
