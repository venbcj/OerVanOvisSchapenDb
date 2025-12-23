<?php

/*
 * <!-- 19-12-2020 : gekopieerd van save_ras.php
    31-1-2021 : Transponder uit database gehaald
    29-12-2023 : and h.skip = 0 toegevoegd bij tblHistorie
 -->
*/
$alert_gateway = new AlertGateway();
$old_volgnr = $alert_gateway->laatste_selectie($lidId);
foreach ($_POST as $fldname => $fldvalue) {  //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    $multip_array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;  // Opbouwen van een Multidimensional array met 2 indexen. [Id] [naamveld] en een waarde nl. de veldwaarde.
}
foreach ($multip_array as $recId => $id) {
    foreach ($id as $key => $value) {
        if ($key == 'check' && !empty($value)) {
            $uitvoeren = $value;
        }
        if ($key == 'txtWorpVan' && !empty($value)) {
            $dag = date_create($value);
            $flddagvan = date_format($dag, 'Y-m-d');
        }
        if ($key == 'txtWorpTot' && !empty($value)) {
            $dag = date_create($value);
            $flddagtot = date_format($dag, 'Y-m-d');
        }
        if (!empty($recId)) {
            if (!isset($old_volgnr)) {
                $volgnr = 1;
            } else {
                $volgnr = $old_volgnr + 1;
            }
            $schaap_gateway = new SchaapGateway();
            $zoek_dieren = $schaap_gateway->zoek_dieren($lidId, $flddagvan, $flddagtot, $recId);
            while ($zd = $zoek_dieren->fetch_assoc()) {
                $transponder = $zd['transponder'] . $zd['levensnummer'];
                $alert_gateway->insert($volgnr, $lidId, $transponder, $recId);
            }
        }
        if (isset($volgnr)) {
            $aantal = $alert_gateway->zoek_aantal_selectie($volgnr);
            $goed = 'Er staan ' . $aantal . ' schapen klaar om naar de reader te sturen.';
        }
    }
}
