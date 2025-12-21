<?php

/* 07-01-2025 : gemaakt */

$opgaaf_gateway = new OpgaafGateway();
foreach ($_POST as $fldname => $fldvalue) {  //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    $multip_array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;  // Opbouwen van een Multidimensional array met 2 indexen. [Id] [naamveld] en een waarde nl. de veldwaarde.
}
foreach ($multip_array as $recId => $id) {
    unset($fldTerug);
    foreach ($id as $key => $value) {
        if ($key == 'chbTerug') {
            $fldTerug = $value;
        }
    }
    if (isset($fldTerug)) {
        $opgaaf_gateway->clear_history($recId);
    }
}
