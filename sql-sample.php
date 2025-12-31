<?php

function insert_tblHistorie($STALID, $DATUM, $ACTID){
global $db;

$insert_tblHistorie = "INSERT INTO tblHistorie SET stalId = '".mysqli_real_escape_string($db, $STALID)."',
    datum = '".mysqli_real_escape_string($db, $DATUM)."',
actId = '".mysqli_real_escape_string($db, $ACTID)."' ";
/*echo $insert_tblHistorie.'<br>';*/        return mysqli_query($db, $insert_tblHistorie);
}
