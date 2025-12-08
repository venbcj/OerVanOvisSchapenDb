<?php

/* 27-2-2020 bestand gekopieerd van impVerplaatsing.php
15-11-2020 Diverse imp... bestanden teruggebracht naar een bestand impAgrident.php
23-1-2021 : Transponder toegevoegd
20-6-2021 : Voerregistratie toegevoegd
18-12-2021 : Dekken en Dracht toegevoegd
26-11-2022 : Taak Aanvoer, Afvoer, Spenen en Dracht anders ingericht (andere loop in reader) */

include "connect_db.php";
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
switch ($_SERVER['REQUEST_METHOD']) { // Switch
case 'POST':
    $input = file_get_contents('php://input'); // php://input is de rauwe data. nl. het json bestand.
    $data = json_decode($input);
    $taken = array('Worpregistratie', 'Doodgeboren', 'Groepsgeboorte', 'Verplaatsing', 'Spenen', 'Afvoer', 'Aanvoer', 'Omnummeren', 'Medicaties', 'Halsnummers', 'Groepsafvoer', 'Voerregistratie', 'Dekken', 'Dracht');
    foreach ($data as $index => $item) {
        // Inlezen record
        for ($i = 0; $i < count($taken); $i++) { // Er zijn 7 elementen nl. zie array $velden
            if ($i == 0) {
                $inhoud = $item -> {$taken[$i]} ;
                include "impWorpregistratie.php";
            }
            if ($i == 1) {
                $inhoud = $item -> {$taken[$i]} ;
                include "impDoodgeboren.php";
            }
            if ($i == 2) {
                $inhoud = $item -> {$taken[$i]} ;
                $velden = array('ActId', 'Datum', 'Transponder', 'Levensnummer');
                include "impAgrident.php";
            }
            if ($i == 3) {
                $inhoud = $item -> {$taken[$i]} ;
                include "impVerplaatsing.php";
            }
            if ($i == 4) {
                $inhoud = $item -> {$taken[$i]} ;
                $velden = array('ActId', 'Datum', 'HokId', 'Levensnummer', 'Gewicht');
                include "impAgrident.php";
            }
            if ($i == 5) {
                $inhoud = $item -> {$taken[$i]} ;
                $velden = array('ActId', 'Datum', 'Ubn', 'Reden', 'Transponder', 'Levensnummer', 'Gewicht');
                include "impAgrident.php";
            }
            if ($i == 6) {
                $inhoud = $item -> {$taken[$i]} ;
                $velden = array('Datum', 'Ubn', 'RasId', 'HokId', 'Transponder', 'Levensnummer','Datumdier', 'Geslacht', 'ActId', 'Gewicht');
                include "impAgrident.php";
            }
            if ($i == 7) {
                $inhoud = $item -> {$taken[$i]} ;
                $velden = array('ActId', 'Datum', 'Transponder', 'Levensnummer', 'Nieuw_Transponder', 'Nieuw_Nummer');
                include "impAgrident.php";
            }
            if ($i == 8) {
                $inhoud = $item -> {$taken[$i]} ;
                $velden = array('ActId', 'Datum', 'ArtId','Reden','Toedat','Transponder','Levensnummer');
                include "impAgrident.php";
            }
            if ($i == 9) {
                $inhoud = $item -> {$taken[$i]} ;
                $velden = array('ActId', 'Datum', 'Transponder', 'Levensnummer', 'Kleur', 'Halsnr');
                include "impAgrident.php";
            }
            if ($i == 10) {
                $inhoud = $item -> {$taken[$i]} ;
                $velden = array('ActId', 'Datum', 'Ubn', 'Transponder', 'Levensnummer');
                include "impAgrident.php";
            }
            if ($i == 11) {
                $inhoud = $item -> {$taken[$i]} ;
                $velden = array('ActId', 'Datum', 'HokId', 'DoelId', 'ArtId', 'Toedat');
                include "impAgrident.php";
            }
            if ($i == 12) {
                $inhoud = $item -> {$taken[$i]} ;
                $velden = array('ActId', 'Datum', 'VdrId', 'MoederTransponder', 'Moeder');
                include "impAgrident.php";
            }
            if ($i == 13) {
                $inhoud = $item -> {$taken[$i]} ;
                $velden = array('ActId', 'Datum', 'MoederTransponder', 'Moeder', 'Drachtig', 'Grootte');
                include "impAgrident.php";
            }
            echo $i . '<br>';
        }
    }
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
