<?php

/* 20-12-2020 : gemaakt */

include "connect_db.php";

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405); // Methode niet toegestaan
    return;
}
$headers = getallheaders(); // geef in een array ook headers terug die ik naar de server heb gestuurd in eerste instantie
if (!isset($headers['Authorization'])) { // Als in de headers geen index 'Autorization voorkomt'
    // Hier zou je een 400 kunnen geven (Bad Request) --BCB
    http_response_code(401); // Unauthorized
    echo 'authorization header bestaat niet.';
    return;
}
$authorization = explode(" ", $headers['Authorization']);
if (count($authorization) == 2 && trim($authorization[0]) == "Bearer" && strlen(trim($authorization[1])) == 64) {
    $lid_gateway = new LidGateway();
    $lidId = $lid_gateway->findByReaderkey($authorization[1]);
    if (!$lidId) {
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
$volgnr = $alert_gateway->laatste_selectie($lidId);
$result = $alert_gateway->transponders($volgnr);
if ($result && $result->num_rows > 0) {
    while ($row = mysqli_fetch_array($result)) {
        $opties[] = array('Transponder' => $row['tran'], 'AlertId' => $row['Id']);
    }
}
$vb = json_encode($opties);
echo $vb;
http_response_code(200); // Ok alles is goed
