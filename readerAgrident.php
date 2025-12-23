<?php

require_once("autoload.php");

/* 27-2-2020 bestand gekopieerd van impVerplaatsing.php
15-11-2020 Diverse imp... bestanden teruggebracht naar een bestand
23-1-2021 : Transponder toegevoegd
20-6-2021 : Voerregistratie toegevoegd
18-12-2021 : Dekken en Dracht toegevoegd
26-11-2022 : Taak Aanvoer, Afvoer, Spenen en Dracht anders ingericht (andere loop in reader) */






include "connect_db.php";
if (!function_exists('getallheaders')) {
    function getallheaders() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}
$string = '';
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    return;
}
$headers = getallheaders(); // geef in een array ook headers terug die ik naar de server heb gestuurd in eerste instantie
if (!isset($headers['Authorization'])) { // Als in de headers geen index 'Autorization voorkomt'
    http_response_code(401); // Unauthorized
    echo 'authorization header bestaat niet.';
    return;
}
$authorization = explode(" ", $headers['Authorization']);
if (count($authorization) != 2 || trim($authorization[0]) != "Bearer" || strlen(trim($authorization[1])) != 64) {
    http_response_code(401); // Unauthorized
    echo 'authorization header heeft niet de juiste opmaak.';
    return;
}
$lid_gateway = new LidGateway();
$lidid = $lid_gateway->findByReaderkey($authorization[1]);
if (!$lidid) {
    http_response_code(401); // Unauthorized
    echo 'via authorization header wordt de gebruiker niet gevonden.';
    return;
}
switch ($_SERVER['REQUEST_METHOD']) { // Switch
case 'POST':
    $input = file_get_contents('php://input'); // php://input is de rauwe data. nl. het json bestand.
    $data = json_decode($input);
    $parser = new JsonAgridentParser($data, $lidid);
    echo $parser->execute();
    // Maak een backup in de persoonlijke map op de server
    $dir = dirname(__FILE__);
    $dag = date('Y-m-d') . '_';
    $uur = date('H') . 'u';
    $minuut = date('i') . 'm';
    $sec = date('s') . 's';
    $tijdstip = $dag . $uur . $minuut . $sec;
    $bestandsnaam = 'reader_' . $tijdstip . '.txt';
    $locatie = $dir . "/" . "user_" . $lidid . "/";
    $root = $locatie . $bestandsnaam;
    $fh = fopen($root, 'w'); //bron : https://www.phphulp.nl/php/tutorial/php-functies/fopen/78/de-functie-fopen/145/
    fwrite($fh, $input);
    fclose($fh);
    // Einde Maak een backup in de persoonlijke map op de server
    break;
default:
    http_response_code(405); // Methode niet toegestaan
    return;
}
http_response_code(200); // Ok alles is goed
