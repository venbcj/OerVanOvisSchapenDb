<?php

/* 22-11-2015 gemaakt
15-02-2017 : gewicht niet verplicht gemaakt en (extra) controle op 'maximale datum uit historie' verwijderd
06-06-2018 : kg met komma wordt omgezet naar kg met punt
01-01-2024 :  sql beveiligd met quotes en db_null_input()
20-02-2025 Hidden velden in HokAfleveren.php verwijderd en hier lege checkboxen gedefinieerd ondanks dat het niet nodig is! */

$array = array();
foreach ($_POST as $fldname => $fldvalue) {
    $multip_array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;
}
foreach ($multip_array as $recId => $id) {
    unset($fldKies);
    unset($updDag);
    unset($updLevnr);
    unset($updKg);
    unset($updRelId);
    foreach ($id as $key => $value) {
        if ($key == 'chbkies' && $value == 1) {
            $fldKies = $value;
        }
        if ($key == 'txtDatum') {
            $dag = date_create($value);
            $updDag =  date_format($dag, 'Y-m-d');
        }
        if ($key == 'txtLevnr' && !empty($value)) {
            $updLevnr = $value;
        }
        if ($key == 'txtKg' && !empty($value)) {
            $updKg = str_replace(',', '.', $value);
        }
        if ($key == 'kzlRel' && !empty($value)) {
            $updRelId = $value;
        }
    }
    if (!isset($fldKies)) {
        $fldKies = 0;
    }
// CONTROLE op alle verplichten velden bij spenen lam
    if ($fldKies == 1 && !empty($updDag) && !empty($updRelId)) {
        $stal_gateway = new StalGateway();
        $stalId = $stal_gateway->findByLidWithoutBest($lidId, $recId);
        $historie_gateway = new HistorieGateway();
        $historie_gateway->herstel_invoeren($stalId, $updDag, $updKg, $actId);
        if ($modmeld == 1) {
            $Melding = 'AFV';
            $afvoerd = $updDag;
            $hisId = $historie_gateway->findIdByAct($lidId, $actId);
            include "maak_request.php";
        }
        $stal_gateway->update_relbest($stalId, $updRelId);
    }
// EINDE CONTROLE op alle verplichten velden bij spenen lam
}
