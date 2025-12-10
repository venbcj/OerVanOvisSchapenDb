<?php

/* 9-8-2020 : gemaakt */

include "connect_db.php";

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405); // Methode niet toegestaan
    return;
}
$headers = getallheaders(); // geef in een array ook headers terug die ik naar de server heb gestuurd in eerste instantie
if (!isset($headers['Authorization'])) { // Als in de headers geen index 'Autorization voorkomt'
    http_response_code(401); // Unauthorized
    echo 'authorization header bestaat niet.';
    return;
}
$authorization = explode(" ", $headers['Authorization']);
if (count($authorization) == 2 && trim($authorization[0]) == "Bearer" && strlen(trim($authorization[1])) == 64) {
    $lid_gateway = new LidGateway();
    $lidid = $lid_gateway->findByReaderkey($authorization[1]);
    if (!$lidid) {
        http_response_code(401); // Unauthorized
        echo 'via authorization header wordt de gebruiker niet gevonden.';
        return;
    }
} else {
    http_response_code(401); // Unauthorized
    echo 'authorization header heeft niet de juiste opmaak.';
    return;
}
$alert_gateway = new AlertGateway();
$result = $alert_gateway->all();
// @TODO #0004210 dit met fetch_all oplossen zodra er een unit-test om dit bestand zit.
$rows = $result->num_rows;
unset($opties);
if (isset($result) && $rows > 0) {
    while ($row = $result->fetch_array()) {
        $opties[] = array('recordid' => $row['Id'], 'name' => $row['name']);
    }
}
$vb = json_encode($opties);
echo $vb;
http_response_code(200); // Ok alles is goed
