<?php

$insert_tblHistorie = "INSERT INTO tblHistorie SET stalId = '" . mysqli_real_escape_string($db, $STALID) . "',
        datum = '" . mysqli_real_escape_string($db, $DATUM) . "',
            actId = '" . mysqli_real_escape_string($db, $ACTID) . "' ";

mysqli_query($db, $insert_tblHistorie);
$ledenlijst = mysqli_query($db, "SELECT * where lidId = ".mysqli_real_escape_string($db, $lidId)." AND true");

(new sample)->target(4);
class sample {

    public function target($arg1) {
        $this->run_query();
    }

}
