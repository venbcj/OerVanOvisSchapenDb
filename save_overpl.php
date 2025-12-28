<?php

/* 22-11-2015 gemaakt
20-1-2017 : Query aangepast n.a.v. nieuwe tblDoel        22-1-2017 : tblBezetting gewijzigd naar tblBezet
11-2-2017 : insert tblPeriode verwijderd
29-12-2023 : and h.skip = 0 toegevoegd bij tblHistorie en sql beveiligd met quotes */
$array = array();
foreach ($_POST as $key => $value) {
    $array[Url::getIdFromKey($key)][Url::getNameFromKey($key)] = $value;
}
$bezet_gateway = new BezetGateway();
$schaap_gateway = new SchaapGateway();
$stal_gateway = new StalGateway();
$historie_gateway = new HistorieGateway();
foreach ($array as $recId => $id) { //recId is hier schaapId
    foreach ($id as $key => $value) {
        if ($key == 'chbkies' && $value == 1) {
            $box = $value ;
            foreach ($id as $key => $value) {
                if ($key == 'txtDatum') {
                    $dag = date_create($value);
                    $updDag =  date_format($dag, 'Y-m-d');
                }
                if ($key == 'kzlHok' && !empty($value)) {
                    $kzlHok = $value;
                }
            }
            $dmmin = $schaap_gateway->zoek_mindag($recId);
            // CONTROLE op alle verplichten velden bij overplaatsen schaap
            if (!empty($updDag) && $updDag >= $dmmin && !empty($kzlHok)) {
                $stalId = $stal_gateway->zoek_stal($lidId, $recId);
                $hisId = $historie_gateway->insert_afvoer_act($stalId, $updDag, 5);
                if (!isset($newHok) || $kzlHok <> $newHok) { // Als het gekozen verblijf is ongelijk aan verblijf van de vorige regel (record)
                    $newHok = $kzlHok;  // Periode van voorgaande overplaats-record mag niet meer bestaan.
                }
                $bezet_gateway->insert($hisId, $newHok);
            }
            // EINDE CONTROLE op alle verplichten velden bij spenen lam
        } // EINDE Alleen als checkbox chbkies de waarde 1 heeft
    }
}
