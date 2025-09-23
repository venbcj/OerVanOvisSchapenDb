<?php

class UbnGateway extends Gateway {

    public function exists($ubn) {
        // zou ook met count() kunnen
        $vw = mysqli_query($this->db, "SELECT ubn FROM tblUbn WHERE ubn = '".mysqli_real_escape_string($this->db, $ubn)."' ;") or Logger::error(mysqli_error($this->db));
        return (mysqli_num_rows($vw) != 0);
    }

    public function insert($lidId, $ubn) {
        mysqli_query($this->db, "INSERT INTO tblUbn SET lidId = '".mysqli_real_escape_string($this->db, $lidId)."', ubn = '".mysqli_real_escape_string($this->db, $ubn)."' ");
    }

}
