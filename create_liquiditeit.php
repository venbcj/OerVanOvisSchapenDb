<?php

/* 30-10-2016 : year vervangen door new_jaar */

/* Toegepast in :
-    Deklijst.php
-    Liquiditeit.php
 */
// TODO dit kan korter, of als je er toch doorheen foreacht, direct met een range() in de for.
$maanden = [
    $new_jaar . '-01-01',
    $new_jaar . '-02-01',
    $new_jaar . '-03-01',
    $new_jaar . '-04-01',
    $new_jaar . '-05-01',
    $new_jaar . '-06-01',
    $new_jaar . '-07-01',
    $new_jaar . '-08-01',
    $new_jaar . '-09-01',
    $new_jaar . '-10-01',
    $new_jaar . '-11-01',
    $new_jaar . '-12-01',
];

$rubriek_gateway = new RubriekGateway();
$liquiditeit_gateway = new LiquiditeitGateway();
// TODO geen magic 12, maar door de maanden heen-foreachen
for ($i = 0; $i < 12; $i++) {
    $maand = $maanden[$i];
    $ophalen_rubriekuser = $rubriek_gateway->find($lidId);
    while ($oph = $ophalen_rubriekuser->fetch_assoc()) {
        $rub_user = $oph['rubuId'];
        $datum = $oph['dag'];
        $liquiditeit_gateway->insert($rub_user, $datum);
    }
}
