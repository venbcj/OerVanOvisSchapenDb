<?php

// Toegepast in :  Deklijst.php

// komt uit Deklijst.php en maakt $year numeriek
$year = $year + 0;
// Week 1 Dit moet 01 zijn tussen dubbele quotes !!
$week1 = "01";
$week_eind = 52;
// De maandag van week 1. Let op is soms nog december van het vorige jaar. Bijv. 29-12-2014
$maandag1 = date("Y-m-d", strtotime($year . "W" . $week1 . "1"));
// Laatste maandag van de maand december
$maandag52 = date("j", strtotime($year . "W" . $week_eind . "1"));
if ($maandag52 > 24) {
    $weken_jaar = 52;
} else {
    $weken_jaar = 53;
}
// De eerste maandag voorafgaand aan de loop moet 7 dagen voor de eerste maandag van het jaar liggen
$day = strtotime($maandag1) - (86400 * 7);

$deklijst_gateway = new DeklijstGateway();
for ($i = 1; $i <= $weken_jaar; $i++) {
    $datum = date('Y-m-d', $day + ($i * 86400 * 7));
    // Soms valt de eerste maandag in het vorige jaar bijv. 29-12-2014 is week 1 van 2015.
    $juiste_jaar = date('Y', $day + ($i * 86400 * 7));
    if ($juiste_jaar == $year) {
        $deklijst_gateway->insert($lidId, $datum);
    }
}

// Kijken of het jaar ook binnen de liquiditeit moet worden aangemaakt.
// TODO: met deze opzet maak je twee gateways aan. Maak een object ipv een include, en verpak de test in de aan te roepen methode.
$liquiditeit_gateway = new LiquiditeitGateway();
if (is_null($liquiditeit_gateway->zoek_jaar($lidId, $year))) {
    $new_jaar = $year;
    include "create_liquiditeit.php";
}
