<?php

/* 6-3-2015 : sql beveiligd */
foreach ($_POST as $fldname => $fldvalue) {  //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    $multip_array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;  // Opbouwen van een Multidimensional array met 2 indexen. [Id] [naamveld] en een waarde nl. de veldwaarde.
}
foreach ($multip_array as $recId => $id) {
    if (!empty($recId)) {
        foreach ($id as $key => $value) {
            if ($key == 'txtScan' && !empty($value)) {
                $updScan = "'" . $value . "'";
            } elseif ($key == 'txtScan' && empty($value)) {
                $updScan = 'NULL';
            }
        }
        $stal_gateway = new StalGateway();
        $scan_db = $stal_gateway->zoek_scan($recId);
        if ($updScan <> $scan_db) {
        // Zoeken naar dubbel scancode
            if ($stal_gateway->is_dubbel($lidId, $updScan)) {
                $fout = " Het scannr bestaat al.";
            } else {
                $stal_gateway->verwijder_scan_afgevoerden($lidId, $updScan);
                $stal_gateway->update_scan($recId, $updScan);
            }
        }
    }
}
