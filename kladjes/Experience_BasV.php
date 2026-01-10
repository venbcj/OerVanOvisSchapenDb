<?php

require_once(__DIR__ . '/../autoload.php');

$gateway = new Gateway();

$stmt = "SELECT hisId, actId, datum FROM tblHistorie WHERE hisId >= :hisid and actId = :actid";

// BCB: okee... hiervoor gaan we niet een methode aan een object toevoegen
// print_r($gateway-> explain_run_query($stmt, [[':hisid', 180],[':actid', 1]]));
print_r($gateway->run_query($stmt, [[':hisid', 180],[':actid', 1]])->fetch_all(MYSQLI_ASSOC));
