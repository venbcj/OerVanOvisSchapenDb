<?php

/* 18-9-2016 gemaakt
28-12-2023 : and h.skip = 0 toegevoegd bij tblHistorie
21-02-2025 Lege checkboxen gedefinieerd ondanks dat het niet nodig is! */
$array = array();
foreach ($_POST as $fldname => $fldvalue) {
    $multip_array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;
}
foreach ($multip_array as $recId => $id) {
    unset($fldKies);
    unset($fldBest);
    unset($fldDood);
    foreach ($id as $key => $value) {
        if ($key == 'chbKies' && $value == 1) {
            $fldKies = $value ;
        }
        if ($key == 'txtDatum' && !empty($value)) {
            $dag = date_create($value);
            $fldDag =  date_format($dag, 'Y-m-d');
        }
        if ($key == 'kzlBest' && !empty($value)) {
            $fldBest = $value;
        }
        if ($key == 'chbDood') {
            $fldDood = $value;
        }
    }
    if ($recId > 0) {
        $historie_gateway = new HistorieGateway();
        [$maxdm, $dmmax] = $historie_gateway->zoek_maxdatum($recId);
    }
// CONTROLE op alle verplichten velden bij afvoer stal
    if ($fldKies == 1 && isset($fldDag) && $dmmax <= $fldDag && ( (isset($fldBest) && !isset($fldDood)) || (!isset($fldBest) && isset($fldDood)) )) {
        if (isset($fldBest)) {
            $actId = 12;
            $meldafvoer = 'AFV';
        }
        if (isset($fldDood)) {
            $actId = 14;
            $meldafvoer = 'DOO';
            $fldBest = $rendac_Id;
        }
        $historie_gateway->insert($recId, $fldDag, $actId);
        if ($modmeld == 11) {
            $Melding = $meldafvoer;
            $afvoerd = $fldDag;
            $hisId = $historie_gateway->findIdByAct($lidId, $actId);
            include "maak_request.php";
        }
        $stal_gateway = new StalGateway();
        $stal_gateway->update_relbest($recId, $fldBest);
    }
// EINDE CONTROLE op alle verplichten velden bij afvoer stal
// unset($fldDag); Deze unset staat hier te vroeg. Nl. nog nodig bij foutmelding in Afvoerstal.php
}
