<?php

foreach ($_POST as $fldname => $fldvalue) {  //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    $multip_array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;  // Opbouwen van een Multidimensional array met 2 indexen. [Id] [naamveld] en een waarde nl. de veldwaarde.
}
$periode_gateway = new PeriodeGateway();
$inkoop_gateway = new InkoopGateway();
$voeding_gateway = new VoedingGateway();
$artikel_gateway = new ArtikelGateway();
foreach ($multip_array as $recId => $id) {
    //echo 'recId = '.$recId.'<br>';
    foreach ($id as $key => $value) {
        if ($key == 'txtDatum' && !empty($value)) {
            $dag = date_create($value);
            $fldDay =  "'" . date_format($dag, 'Y-m-d') . "'";
        }
        if ($key == 'txtKilo' && !empty($value)) {
            $fldKilo = $value;
        } elseif ($key == 'txtKilo' && empty($value)) {
            $fldKilo = 'NULL';
        }
        if ($key == 'chbDelVoer') {
            $fldDelVoer = $value;
        }
        if ($key == 'chbDelPeri') {
            $fldDelPeri = $value;
        }
    }
    if (isset($recId) and $recId > 0) {
        $zoek_in_database = $periode_gateway->zoek_in_database($recId);
        while ($co = $zoek_in_database->fetch_assoc()) {
            $dbDate = $co['dmafsluit'];
            $dbDate = "'" . $dbDate . "'";
            //}
            $dbArtId = $co['artId'];
            $dbNutat = $co['nutat'];
            if (empty($dbNutat)) {
                $dbNutat = 'NULL';
            }
            if (isset($fldDay) && $fldDay <> $dbDate) {
                $periode_gateway->update_datum($fldDay, $recId);
            }
            unset($fldDay);
            /*** WIJZIGEN VOER ***/
            if (isset($fldKilo) && $fldKilo <> $dbNutat) {
                // *** VOER toevoegen ***
                if ($fldKilo <> 'NULL' && $dbNutat <> 'NULL' && $fldKilo > $dbNutat) {
                    $verschil = $fldKilo - $dbNutat;
                    $instock = $inkoop_gateway->queryStock($dbArtId);
                    // EINDE Totale hoeveelheid voer op voorraad bepalen.
                    // Controle of voorraad toereikend is
                    if (isset($instock) && $instock < $verschil) {
                        $fout = "Er is onvoldoende voer op voorraad.";
                    }
                    // Einde Controle of voorraad toereikend is
                    if (!isset($fout)) {
                        $inkId_ingebruik = $inkoop_gateway->zoek_inkId($dbArtId);
                        $count = $inkoop_gateway->zoek_aantal_inkIds($dbArtId, $inkId_ingebruik);
                        for ($i = 1; $i <= $count; $i++) { // for loop
                            if ($verschil > 0) {
                                $inkId = $inkoop_gateway->zoek_inkId($dbArtId);
                                $inkvrd = $inkoop_gateway->stock_van_ink($inkId);
                                if ($inkvrd >= $verschil) {
                                    // Inkoopvoorraad volstaat WEL
                                    //STAP 3) Voer aan bestaand voedId/inkId toevoegen of nieuw voedId/inkId toevoegen
                                    if ($i == 1) {
                                        [ $voedId, $nutat ] = $voeding_gateway->zoek_ink_tblVoeding($recId, $inkId);
                                    } // Einde Als de eerst inkId wordt aangesproken kan deze reeds bestaan in tblVoeding
                                    if (isset($voedId)) {
                                        // Aan bestaand voedId toevoegen
                                        $newNutat = $nutat + $verschil;
                                        $voeding_gateway->update_kilo($newNutat, $voedId);
                                    } elseif (!isset($voedId)) {
                                        $stdat = $artikel_gateway->zoek_stdat_with_fraction($dbArtId);
                                        $voeding_gateway->insert_tblVoeding($recId, $inkId, $verschil, $stdat);
                                    } // Einde Nieuwe voedId toevoegen
                                    unset($voedId);
                                    $verschil = 0;
                                } // Einde Inkoopvoorraad volstaat WEL
                                // Inkoopvoorraad volstaat NIET
                                if ($inkvrd < $verschil) {
                                    if ($i == 1) {
                                        $zoek_ink_tblVoeding = $voeding_gateway->zoek_ink_tblVoeding($recId, $inkId);
                                    } // Einde Als de eerst inkId wordt aangesproken kan deze reeds bestaan in tblVoeding
                                    if (isset($voedId)) { // Aan bestaand voedId toevoegen
                                        $newNutat = $nutat + $inkvrd;
                                        $voeding_gateway->update_kilo($newNutat, $voedId);
                                    } elseif (!isset($voedId)) {
                                        $stdat = $artikel_gateway->zoek_stdat_with_fraction($dbArtId);
                                        $insert_tblVoeding = $voeding_gateway->insert_tblVoeding($recId, $inkId, $inkvrd, $stdat);
                                    } // Einde Nieuwe voedId toevoegen
                                    unset($voedId);
                                    $verschil = $verschil - $inkvrd;
                                } // Einde Inkoopvoorraad volstaat NIET
                            } // Einde $verschil > 0
                        } // Einde for loop
                    } // Einde Voldoende voorraad
                }
                // *** Einde VOER toevoegen ***
                // *** VOER verminderen ***
                if ($fldKilo <> 'NULL' && $fldKilo < $dbNutat) {
                    // Bij meerdere inkId's van 1 periId wordt per inkId gekeken hoeveel kg voer kan worden afgehaald of het inkId moet worden verwijderd.
                    $verschil = $dbNutat - $fldKilo;
                    $count = $voeding_gateway->hoeveel_inkIds($recId);
                    while ($aa = $hoeveel_inkIds->fetch_assoc()) {
                        $count = $aa['aant'];
                    }
                    if ($count == 1) {
                        $voeding_gateway->update_kilo_periode($fldKilo, $recId);
                    } elseif ($count > 1) {
                        for ($i = 1; $i <= $count; $i++) {
                            if ($verschil > 0) {
                                [$last_v, $nutat] = $voeding_gateway->zoek_kg_laatste_inkId($recId);
                                if ($nutat - $verschil > 0) {
                                    $newNutat = $nutat - $verschil;
                                    $verschil = 0;
                                    $voeding_gateway->update_kilo($newNutat, $last_v);
                                } else {
                                    $verschil = $verschil - $nutat;
                                    $voeding_gateway->delete_voedId($last_v);
                                }
                            } // Einde $verschil >0
                        } // Einde for loop
                    } // Einde als $count > 1
                }
                // *** Einde VOER verminderen ***
            }
            unset($fldKilo);
            /*** EINDE  WIJZIGEN VOER  EINDE ***/
            if (isset($fldDelVoer)) {
                $voeding_gateway->delete_voeding($recId);
                unset($fldDelVoer);
            }
            if (isset($fldDelPeri)) {
                $voeding_gateway->delete_voeding($recId);
                $periode_gateway->delete_periode($recId);
                unset($fldDelPeri);
            }
        }
    }
}
