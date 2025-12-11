<?php

/* 28-05-2023 : gemaakt
31-12-2023 and h.skip = 0 toegevoegd aan tblHistorie
13-04-2025 Laatste dekdatum en dekram toegevoegd */
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
$Karwerk = $lid_gateway->zoek_karwerk($lidId);
$schaap_gateway = new SchaapGateway();
$zoek_info = $schaap_gateway->zoek_info($lidId, $Karwerk);
if ($zoek_info && $zoek_info->num_rows > 0) {
    while ($zi = $zoek_info->fetch_array()) {
        $geslacht = $zi['geslacht'];
        $lastdekdm = $zi['lastdekdm'];
        $lastdekram = $zi['dekram'];
        $lastras_dekram = $zi['ras_dekram'];
        $lastworp = $zi['lastworp'];
        $lastwerpdm = $zi['lastwerpdm'];
        $aantDek = $zi['aant_d'];
        $aantWorp = $zi['aant_w'];
        $gemWorp = $zi['gemWorp'];
        $aantLam = $zi['aant_lam'];
        $PercLevend = $zi['PercLevend'];
        $maxWorp = $zi['maxworp'];
        $aantMaxWorp = $zi['aantalmaxworp'];
        if ($geslacht == 'ram') {
            $aantMaxWorp = 'n.v.t.';
            $lastdekdm = 'n.v.t.';
            $lastdekram = 'n.v.t.';
            $lastras_dekram = 'n.v.t.';
            $lastworp = 'n.v.t.';
            $lastwerpdm = '00-00-0000';
            $aantDek = 'n.v.t.';
            $aantWorp = 'n.v.t.';
            $gemWorp = 'n.v.t.';
            $aantLam = 'n.v.t.';
            $PercLevend = 'n.v.t.';
            $maxWorp = 'n.v.t.';
        }
        $opties[] = [
            'Transponder' => $zi['tran'],
            'Geslacht' => $geslacht,
            'Ras' => $zi['ras'],
            'Laatstedekdm' => $lastdekdm,
            'Laatste_dekram_werknr' => $lastdekram,
            'Laatste_dekram_ras' => $lastras_dekram,
            'Laatsteworp' => $lastworp,
            'Laatstewerpdm' => $lastwerpdm,
            'Dekaantal' => $aantDek,
            'Worpaantal' => $aantWorp,
            'Gemiddeldeworp' => $gemWorp,
            'Lamaantal' => $aantLam,
            'PercLevend' => $PercLevend,
            'Maxworp' => $maxWorp,
            'Aantmaxworp' => $aantMaxWorp
        ];
    }
}
$vb = json_encode($opties);
echo $vb;
http_response_code(200); // Ok alles is goed
