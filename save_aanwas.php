<?php

/* 17-05-2019 gemaakt
01-01-2024 : sql beveiligd
07-01-2024 : insert_tblBezet uitgezet omdat Aanwas niet aan een verblijf wordt toegekend. Zie het veld aan in tblActie bij actId 3. Dit staat op 0
20-02-2025 Hidden velden in HokAanwas.php verwijderd en hier lege checkboxen gedefinieerd ondanks dat het niet nodig is! */

foreach ($_POST as $fldname => $fldvalue) {
    $multip_array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;
}
foreach ($multip_array as $recId => $id) {
    if (!$recId) {
        continue;
    }
    unset($fldKies);
    unset($updDag);
    unset($updKg);
    foreach ($id as $key => $value) {
        if ($key == 'chbkies' && $value == 1) {
            $fldKies = $value;
        }
        if ($key == 'txtDatum') {
            $dag = date_create($value);
            $updDag =  date_format($dag, 'Y-m-d');
        }
        if ($key == 'txtKg' && !empty($value)) {
            $updKg = str_replace(',', '.', $value);
        }
    }
// CONTROLE op alle verplichten velden bij aanwas lam
    if ($fldKies == 1 && !empty($updDag)) {
        $stal_gateway = new StalGateway();
        $stalId = $stal_gateway->findByLidWithoutBest($lidId, $recId);
        $historie_gateway = new HistorieGateway();
        $historie_gateway->insert_act_3($stalId, $updDag, $updKg);
    }
// EINDE CONTROLE op alle verplichten velden bij aanwas lam
}
