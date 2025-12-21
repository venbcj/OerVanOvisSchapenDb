<?php

/*
<!-- 23-10-2015 : gemaakt -->
 * toegepast in :
    - Componenten.php */

$rubriek_gateway = new RubriekGateway();
foreach ($_POST as $fldname => $fldvalue) {  //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    $multip_array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;  // Opbouwen van een Multidimensional array met 2 indexen. [Id] [naamveld] en een waarde nl. de veldwaarde.
}
foreach ($multip_array as $recId => $id) {
    unset($fldActief);
    unset($fldSalber);
    if (!empty($recId)) {
        foreach ($id as $key => $value) {
            if ($key == 'chkActief') {
                $fldActief = $value;
            }
            if ($key == 'chkSalber') {
                $fldSalber = $value;
            } else {
                $fldSalber = 0;
            }
        }
        $rubriek_gateway->update($recId, $fldActief, $fldSalber);
    }
}
