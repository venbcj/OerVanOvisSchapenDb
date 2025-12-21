<?php

/*
<!--  25-6-2021 : Gekopieerd van post_readerMed.php
5-9-2021 : Functie leesvoer_in toegevoegd -->
 */
$array = array();
foreach ($_POST as $key => $value) {
    $array[Url::getIdFromKey($key)][Url::getNameFromKey($key)] = $value;
}
foreach ($array as $recId => $id) {
    foreach ($id as $key => $value) {
        if ($key == 'txtAantal' && !empty($value)) {
            $fldAantal = str_replace(',', '.', $value);
        }
    }
    if (isset($fldAantal)) {
        $impagrident_gateway = new ImpAgridentGateway();
        [$toedat, $toedat_upd] = $impagrident_gateway->zoek_aantal_uit_reader($recId);
        if ($fldAantal != $toedat && (!isset($toedat_upd) || $fldAantal <> $toedat_upd)) {
            $impagrident_gateway->update($recId, $fldAantal);
        }
        if ($fldAantal == $toedat && isset($toedat_upd)) {
            $impagrident_gateway->update($recId, null);
        }
    }
}
