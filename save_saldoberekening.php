<?php

/* toegepast in :
- Ras.php
<!-- 15-11-2015 : gemaakt -->
 */
foreach ($_POST as $fldname => $fldvalue) {
    //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    $multip_array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;
    // Opbouwen van een Multidimensional array met 2 indexen. [Id] [naamveld] en een waarde nl. de veldwaarde.
}
$salber_gateway = new SalberGateway();
foreach ($multip_array as $recId => $id) {
    foreach ($id as $key => $value) {
        if ($key == 'txtElem' && !empty($value)) {
            $fldElem = str_replace(',', '.', $value);
        } elseif ($key == 'txtElem' && empty($value)) {
            $fldElem = 'NULL';
        }
        if ($key == 'txtRubriek' && !empty($value)) {
            $fldRub = str_replace(',', '.', $value);
        } elseif ($key == 'txtRubriek' && empty($value)) {
            $fldRub = 'NULL';
        }
        if ($key == 'txtRubat' && !empty($value) && $value > 0) {
            $fldRubat = str_replace(',', '.', $value);
        } elseif ($key == 'txtRubat' && (empty($value) || $value == 0)) {
            $fldRubat = 'NULL';
        }
    }
    // @TODO: #0004212 flow nakijken. Als er meerdere elementen in de POST binnenkomen, loopt alles hier over elkaar --BCB
    if (isset($fldElem)) {
        //$fldElem kan niet bestaan als alle componenten niet actief zijn of niet voor saldoberekening zijn aangevinkt
        $salber_gateway->update($recId, $fldElem);
    }
    if (isset($fldRub)) {
        //$fldRub kan niet bestaan als alle rubrieken niet actief zijn of niet voor saldoberekening zijn aangevinkt
        $salber_gateway->update($recId, $fldRub);
    }
    if (isset($fldRubat)) {
        //$fldRubat kan niet bestaan als alle rubrieken niet actief zijn of niet voor saldoberekening zijn aangevinkt
        $salber_gateway->update($recId, $fldRubat);
    }
}
