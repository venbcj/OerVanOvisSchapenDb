<?php

/* 29-3-2017 : gemaakt
29-12-2023 sql voorzien van enkele quotes */
foreach ($_POST as $fldname => $fldvalue) {  //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    $multip_array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;  // Opbouwen van een Multidimensional array met 2 indexen. [Id] [naamveld] en een waarde nl. de veldwaarde.
}
$historie_gateway = new HistorieGateway();
foreach ($multip_array as $recId => $id) {
    foreach ($id as $key => $value) {
        if (isset($recId) && $recId > 0) {
            foreach ($id as $key => $value) {
                if ($key == 'txtComm' && !empty($value)) {
                    $updComm = "'" . $value . "'";
                } elseif ($key == 'txtComm' && empty($value)) {
                    $updComm = 'NULL';
                }
            }
            $comm = $historie_gateway->zoek_commentaar($recId);
            if (!isset($comm)) {
                $dbComm = 'NULL';
            } else {
                $dbComm = "'" . $comm . "'";
            }
            if ($updComm <> $dbComm && $updComm == 'NULL') {
                $historie_gateway->wis_commentaar($recId);
            }
            if ($updComm <> $dbComm && $updComm <> 'NULL') {
                $historie_gateway->update_commentaar($recId, $updComm);
            }
        }
    }
}
