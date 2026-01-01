<?php

$db = null;
$STALID = 123;
$DATUM = '2025-01-01';
$ACTID = 6;

$insert_tblHistorie = "INSERT INTO tblHistorie SET stalId = '" . mysqli_real_escape_string($db, $STALID) . "',
        datum = '" . mysqli_real_escape_string($db, $DATUM) . "',
            actId = '" . mysqli_real_escape_string($db, $ACTID) . "' ";

$res = mysqli_query($db, $insert_tblHistorie);
