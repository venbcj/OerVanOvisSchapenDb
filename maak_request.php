<?php

unset($reqId); // Nodig als er diverse soorten meldingen tegelijk worden aangemaakt. Zie bijv. Afvoerstal.php (alleen module melden)
$request_gateway = new RequestGateway();
// *** HET REQUEST ***
//Zoeken naar een openstaand request
$reqId = $request_gateway->zoek_open_request($lidId, $Melding);
        
if (!isset($reqId)) {
// Nieuw request aanmaken indien nodig
    $reqId = Request::maak_request($db, $lidId, $Melding);
    $newlidId = $lidId;
    // T.t.v. het aanmaken van het request zijn er nog geen meldingen gekoppeld
    // en is dus niet bekend voor welke gebruiker dit request is bestemd.
    //  Zolang er geen meldingen voorkomen in tblMelding
    //    bepaalt $newlidId voor welke gebruiker het request is bestemd.
}
// *** EINDE HET REQUEST ***

// *** DE MELDINGEN ***
$melding_gateway = new MeldingGateway();
$melding_gateway->insert($reqId, $hisId);
if (isset($newlidId)) {
    $request_gateway = new RequestGateway();
    $request_gateway->update($reqId);
    unset($newlidId);
}
// *** EINDE DE MELDINGEN ***
