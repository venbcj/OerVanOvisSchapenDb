<?php

/*29-12-2023 : sql beveiligd
09-03-2025 : In Deklijst.php veld txtId_Id verwijderd en hier recId gedefinieerd*/

foreach ($_POST as $fldname => $fldvalue) {  //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    $multip_array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;  // Opbouwen van een Multidimensional array met 2 indexen. [Id] [naamveld] en een waarde nl. de veldwaarde.
}
$deklijst_gateway = new DeklijstGateway();
foreach ($multip_array as $recId => $id) {
    unset($flddekat);
    unset($fldwerpat);
    foreach ($id as $key => $value) {
        if ($key == 'txtDekat') {
            $flddekat = $value;
        }
    }
    if ($recId > 0) {
        $dekat_db = $deklijst_gateway->find_aantal($recId);
// Bijwerken dekaantal
        if ($flddekat <> $dekat_db) {
            $deklijst_gateway->update($recId, $flddekat);
        }
    }
}
