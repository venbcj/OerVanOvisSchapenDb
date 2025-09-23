<?php

/*1-6-2020 gemaakt 
wordt gebruikt in
 - Newuser.php
 - Gebruiker.php

Bij gebruik van reader Agrident moeten er bepaalde redenen t.b.v. uitval en afvoer in gebruik zijn bij een gebruiker 
20-6-2020 : controle op bestaan Lambar toegevoegd 
 31-1-2021 : Sql beveiligd met quotes. 
 12-02-2021 : Controle Lambar hier weggehaald en in impVerplaatsingen.php toegevoegd. */

$array_uitval = array( 8, 13, 22, 42, 43, 44 ); /*8 Klem gezeten 13 Onbekend 22 Zwak 42 In het vlies 43 Misvormd 44 Verkeerde ligging */
$array_afvoer = array( 15, 45, 46, 47, 48, 49, 50, 51); /*15 Prolaps 45 Slecht uier 46 Slacht ooi 47 Weinig melk 48 Verwerper 49 Gust 51 Weide lam */
$lid_gateway = new LidGateway($db);

// Aanvullen of bijwerken redenen uitval
foreach ($array_uitval as $redId) {
    $lid_gateway->storeUitvalOm($lidid, $redId);
}
// Einde Controle redenen uitval
// Aanvullen of bijwerken redenen afvoer
foreach ($array_afvoer as $redId) {
    $lid_gateway->storeAfvoerOm($lidid, $redId);
}
