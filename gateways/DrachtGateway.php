<?php

class DrachtGateway extends Gateway {

public function insert_dracht($volwId, $hidId) {
    $insert_tblDracht = "INSERT INTO tblDracht SET volwId = '".$this->db->real_escape_string($volwId)."',
    hisId = '".$this->db->real_escape_string($hisId)."' ";    
$this->db->query($insert_tblDracht);
}

}
