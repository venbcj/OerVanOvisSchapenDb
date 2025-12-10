<?php

/* toegepast in :
- Componenten.php
<!-- 23-10-2015 : gemaakt
5-11-2016 : multip_array array uitgebreid met maandnr en typeveld. Dit heeft geleid naar 1 update statament i.p.v. 12
        Ook controlevelden verwijderd (dus ctrJan, ctrFeb enz.... )
14-2-2021 : Komma vervangen door punt. SQL in hoofdletters en beveiligd met quotes
11-03-2025 : multip_array met 4 indexen teruggebracht naar 3 indexen. wel of niet hidden veld is verwijderd als index. Een hidden veld wordt hier niet meer uitgesloten. In liquiditeit.php wordt het hidden veld txtM niet meer getoond dus bestaat txtM hier niet meer -->

 */

function getMndFromKey($string) {
    $split_mnd = explode('_', $string);
    return $split_mnd[2];
}

foreach ($_POST as $fldname => $fldvalue) {  //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    $multip_array[getMndFromKey($fldname)][Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;  // Opbouwen van een Multidimensional array met 3 indexen. [$i] [Id] [naamveld] en een waarde nl. de veldwaarde.
}
$liquiditeit_gateway = new LiquiditeitGateway();
foreach ($multip_array as $mnd => $id) {
    foreach ($id as $rubuId => $id) {
        foreach ($id as $key => $value) {
            unset($fldBedrag);
            if ($key == 'txtM' && !empty($value)) {
                $fldBedrag = str_replace(',', '.', $value);
            }
            $zoek_bedrag = $liquiditeit_gateway->zoek_bedrag($rubuId, $mnd, $toon_jaar);
            if ($fldBedrag <> $bedrag) {
                $liquiditeit_gateway->update_bedrag($fldBedrag, $rubuId, $mnd, $toon_jaar);
            }
        }
    }
}
