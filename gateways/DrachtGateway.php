<?php

class DrachtGateway extends Gateway {

public function insert_dracht($volwId, $hidId) {
    $insert_tblDracht = "INSERT INTO tblDracht SET volwId = '".mysqli_real_escape_string($this->db,$volwId)."',
    hisId = '".mysqli_real_escape_string($this->db,$hisId)."' ";    
mysqli_query($this->db,$insert_tblDracht) or die (mysqli_error($this->db));
}

}
