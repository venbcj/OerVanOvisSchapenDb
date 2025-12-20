<?php

require_once(__DIR__ . '/../autoload.php');

$gateway = new Gateway();

$stmt = "SELECT hisId, actId, datum FROM tblHistorie WHERE hisId >= :hisid and actId = :actid";

print_r($gateway-> explain_run_query($stmt, [[':hisid', 180],[':actid', 1]]));
