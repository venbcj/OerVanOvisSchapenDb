<?php

/*5-9-2021 : functie inlezen_voer gemaakt 22-9-2021 functie inlezen_pil gemaakt */

function volgende_inkoop_voer($datb, $artikel) {
    $inkoop_gateway = new InkoopGateway();
    $dmink = $inkoop_gateway->eerste_inkoopdatum_zonder_voeding($artikel);
    $new_inkId = $inkoop_gateway->eerste_inkoopid_voeding_op_datum($artikel, $dmink);
    $inkoop = $inkoop_gateway->zoek_inkoop($new_inkId);
    if (!$inkoop) {
        // TODO: betere tekst. Vermoedelijk gaat het juist mis als er nog voorraad is (inkoop-records zonder gekoppeld voeding-record)
        throw new Exception("volgende_inkoop_voer mag niet worden aangeroepen bij onvoldoende voorraad (new_id=$new_inkId)");
    }
    return $inkoop;
}

function zoek_voorraad_oudste_inkoop_voer($datb, $artikel) {
    $inkoop_gateway = new InkoopGateway();
    $inkoop = $inkoop_gateway->laatst_aangesproken_voorraad_voer($artikel);
    if (!isset($inkoop[0])) {
        $inkoop = volgende_inkoop_voer($datb, $artikel);
    }
    return $inkoop;
}

function inlezen_voer($datb, $artid, $rest_toedat, $toediendatum, $periode_id, $readerid) {
    $voeding_gateway = new VoedingGateway();
    $ink_voorraad = zoek_voorraad_oudste_inkoop_voer($datb, $artid);
    $inkId = $ink_voorraad[0];
    $rest_ink_vrd = $ink_voorraad[1];
    $stdat = $ink_voorraad[2];
    if ($rest_toedat > $rest_ink_vrd) {
        $voeding_gateway->inlezen($periode_id, $inkId, $rest_ink_vrd, $stdat, $toediendatum, $readerid);
        $rest_toedat = $rest_toedat - $rest_ink_vrd;
        inlezen_voer($datb, $artid, $rest_toedat, $toediendatum, $periode_id, $readerid);
    } else {
        $voeding_gateway->inlezen($periode_id, $inkId, $rest_toedat, $stdat, $toediendatum, $readerid);
    }
}

function volgende_inkoop_pil($artikel): array {
    $inkoop_gateway = new InkoopGateway();
    $dmink = $inkoop_gateway->eerste_inkoopdatum_zonder_nuttiging($artikel);
    $new_inkId = $inkoop_gateway->eerste_inkoopid_op_datum($artikel, $dmink);
    $inkoop = $inkoop_gateway->zoek_inkoop($new_inkId);
    if (!$inkoop) {
        throw new Exception("volgende_inkoop_pil mag niet worden aangeroepen bij onvoldoende voorraad");
    }
    return $inkoop;
}

function zoek_voorraad_oudste_inkoop_pil($artikel) {
    $inkoop_gateway = new InkoopGateway();
    $inkoop = $inkoop_gateway->laatst_aangesproken_voorraad($artikel);
    if (!isset($inkoop[0])) {
        $inkoop = volgende_inkoop_pil($artikel);
    }
    return $inkoop;
}

function inlezen_pil($datb, $hisid, $artid, $rest_toedat, $toediendatum, $reduid) {
    $nuttig_gateway = new NuttigGateway();
    $ink_voorraad = zoek_voorraad_oudste_inkoop_pil($artid);
    $inkId = $ink_voorraad[0];
    $rest_ink_vrd = $ink_voorraad[1];
    $stdat = $ink_voorraad[2];
# @TODO: #0004202 zorg dat je niet deelt door 0
    $rest_toedien_vrd = $rest_ink_vrd / $stdat;
    if ($rest_toedat > $rest_toedien_vrd) {
        $aantal = $rest_toedien_vrd;
        $nuttig_gateway->nuttig_pil($hisid, $inkId, $stdat, $reduid, $aantal);
        $rest_toedat = $rest_toedat - $rest_toedien_vrd;
        // @TODO: moet je opnieuw zoek_voorraad_oudste... doen?
        inlezen_pil($datb, $hisid, $artid, $rest_toedat, $toediendatum, $reduid);
    } else {
        $aantal = $rest_toedat;
        $nuttig_gateway->nuttig_pil($hisid, $inkId, $stdat, $reduid, $aantal);
    }
}
