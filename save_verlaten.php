<?php

/* 22-11-2015 gemaakt
15-2-2017 : gewicht niet verplicht gemaakt en (extra) controle op 'maximale datum uit historie' verwijderd
6-6-2018 : kg met komma wordt omgezet naar kg met punt
30-12-2023 : sql beveiligd met quotes */

$array = array();
foreach ($_POST as $key => $value) {
    $array[Url::getIdFromKey($key)][Url::getNameFromKey($key)] = $value;
}
$stal_gateway = new StalGateway();
$historie_gateway = new HistorieGateway();
foreach ($array as $recId => $id) {
    foreach ($id as $key => $value) {
        if ($key == 'chbkies' && $value == 1) {
            $box = $value ;
            foreach ($id as $key => $value) {
                if ($key == 'txtDatum') {
                    $dag = date_create($value);
                    $updDag =  date_format($dag, 'Y-m-d');
                }
            }
            // CONTROLE op alle verplichten velden bij spenen lam
            if (!empty($updDag)) {
                $rec = $stal_gateway->zoekKleurHalsnr($lidId, $recId);
                $stalId = $rec['stalId'];
                $historie_gateway->insert_afvoer_act($stalId, $updDag, 7);
            }
        }
    }
}
