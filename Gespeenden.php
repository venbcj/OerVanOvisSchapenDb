<?php

require_once("autoload.php");

include "connect_db.php";

$lidId = 3;
$date = $_GET['date'];
$stal_gateway = new StalGateway();
$geef = $stal_gateway->countHisHok1324($lidId, $date);

echo json_encode($geef);
