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

    public function findByUserPassword($user, $password) {
        $vw = mysqli_query($this->db, "
            SELECT lidId, alias
            FROM tblLeden 
            WHERE login = '".mysqli_real_escape_string($this->db, $user)."' and passw = '".mysqli_real_escape_string($this->db, $password)."'");
        if (mysqli_num_rows($vw) == 0) {
            return [];
        }
        return mysqli_fetch_assoc($vw);
    }

}
