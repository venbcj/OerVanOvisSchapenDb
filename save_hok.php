<?php

/* 6-3-2015 : sql beveiligd
30-5-2020 : hidden velden ctrScan en ctrActief verwijderd en aangepast op Agrident reader
02-08-2020 : veld sort toegevoegd
20-04-2024 : Verblijven kunnen worden verwijderd zolang er geen relatie ligt met andere tabellen
10-03-2025 : Hidden veld chbActief_Id in Hok.php verwijderd en hier lege checkbox gedefinieerd */
foreach ($_POST as $fldname => $fldvalue) {  //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    $multip_array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;  // Opbouwen van een Multidimensional array met 2 indexen. [Id] [naamveld] en een waarde nl. de veldwaarde.
}
foreach ($multip_array as $recId => $id) {
    unset($fldSort);
    unset($fldActief);
    unset($fldDelete);
    foreach ($id as $key => $value) {
        if ($key == 'txtSort' && !empty($value)) {
            $fldSort = $value;
        }
        if ($key == 'chbActief') {
            $fldActief = $value;
        }
        if ($key == 'chbDel') {
            $fldDelete = $value;
        }
    }
    if (!isset($fldActief)) {
        $fldActief = 0;
    }
    if ($recId > 0) {
        $hok_gateway = new HokGateway();
        [$Sort_db, $Actief_db] = $hok_gateway->findSortActief($recId);
        if ($fldSort <> $Sort_db) {
            $hok_gateway->updateSort($recId, $fldSort);
            $goed = 'De wijziging is opgeslagen. Vergeet niet de reader bij te werken!';
        }
        if ($fldActief <> $Actief_db) {
        // Zoeken naar hoeveelheid schapen per hok
            [$hoknr, $inhok] = $hok_gateway->hokn_beschikbaar($lidId, $recId);
            if (isset($inhok) && $inhok > 0 && $fldActief == 0) {
                if ($inhok == 1) {
                    $fout = "$hoknr kan niet buiten gebruik worden gesteld omdat er nog 1 schaap in zit.";
                } else {
                    $fout = "$hoknr kan niet buiten gebruik worden gesteld omdat er nog $inhok schapen in zitten.";
                }
            } else {
                $hok_gateway->set_actief($recId, $fldActief);
                $goed = 'De wijziging is opgeslagen. Vergeet niet de reader bij te werken!';
            }
        }
        if (isset($fldDelete)) {
            $hok_gateway->delete($recId);
        }
    }
}
