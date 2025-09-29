<?php
/*
<!-- 10-11-2014 gemaakt 
5-12-2016 : kzlPartij gesplitst in kzlHerk en kzlBest   9-2-2017 ctr-velden verwijderd 
5-5-2017 : Controle bij wijzigen datum aangepast van fldDay < last_day naar fldDay > last_day
26-1-2018 : Bij verwijderen melding wordt kzlBest niet meer leeggemaakt 
19-2-2022 : SQL beveiligd met quotes 
4-4-2022 : Controle zoek_laatste_datum_stalaf uitgezet want reden van deze controle onbekend 
10-5-2023 : eerste_dag keek niet naar een actie op stallijst (tblActie.op = 1). Dit is aangepast 
19-01-2024 : Controle melding verplicht gemaakt 
30-01-2024 : Controle of het veld kzlDef bestaat verplaatst. Zie isset(fldDef)
27-03-2025 : else if (key == 'kzlHerk' && empty(value))  { fldHerk = 'leegkeuzelijst'; } verwijderd -->
*Save_Melding.php toegpast in :
    - MeldAanvoer.php
    - MeldAfvoer.php
    - MeldGeboortes.php
- MeldUitval.php
 */

$array = array();

foreach ($_POST as $fldname => $fldvalue) {
    $multip_array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;
}

foreach ($multip_array as $recId => $id) {
    //unset($fldDef); Deze variabele wordt maar 1x gevuld. Per melding mag deze variabele dus niet worden leeggemaakt.
    unset($fldLevnr);
    unset($fldSekse);
    unset($fldHerk);
    unset($fldBest);
    unset($fldSkip);

    foreach ($id as $key => $value) {
        if ($key == 'kzlDef') {
            $fldDef = $value;
        }

        if ($key == 'txtSchaapdm' && !empty($value)) {
            $txtDag = $value;
            $dag = date_create($value);
            $fldDay =  date_format($dag, 'Y-m-d');
            $updDag =  date_format($dag, 'd-m-Y');
        }

        if ($key == 'txtLevnr' && !empty($value)) {
            $fldLevnr = $value;
        } // in MeldGeboortes.php en mogelijk ook in MeldAanvoer.php

        if ($key == 'kzlSekse' && !empty($value)) {
            $fldSekse = $value;
        }

        if ($key == 'kzlHerk' && !empty($value)) {
            $fldHerk = $value;
        } // in MeldAanvoer.php
        if ($key == 'kzlBest' && !empty($value)) {
            $fldBest = $value;
        }  // in MeldAfvoer.php dus niet voor MeldUitval.php !!!

        if ($key == 'chbSkip') {
            $fldSkip = $value;
        }
    }
    if (!isset($fldSkip)) {
        $fldSkip = 0;
    }

    // TODO: #0004164 deze conditie omkeren en gebruiken als guard clause -> early return.
    if (isset($recId) and $recId > 0) {
        $recId = (int) $recId;
        if (!isset($fldDef)) {
            $fldDef = 'N';
        }

        /****** CONTROLE DATUM *******/

        $nummer_van_datum = intval(str_replace('-', '', $txtDag));

        /* Eerste datum zoeken ter controle bij aanvoer bedrijf */
        $historie_gateway = new HistorieGateway($db);
        if ($code == 'AAN' || $code == 'GER') {
            // TODO: #0004165 deze variabelen zijn alleen nodig om de foutmelding wrong_dag te vormen. Verplaats naar HistorieGateway? Nee, naar een Transactie
            [$first_day, $eerste_dag] = $historie_gateway->zoek_eerste_datum_stalop($recId);
        }
        /* Einde Eerste datum zoeken ter controle bij aanvoer bedrijf */

        /* Maximale datum RVO bepalen ter controle */
        if ($code == 'AAN' || $code == 'GER' || $code == 'DOO') {
            $maxday_rvo = date("Y-m-d");
        }
        $overovermorgen = mktime(0, 0, 0, date("m"), date("d")+3, date("Y"));
        if ($code == 'AFV') {
            $maxday_rvo = date('Y-m-d', $overovermorgen);
        }
        /* Einde Maximale datum RVO bepalen ter controle */

        if ($nummer_van_datum == 0) {
            $wrong_dag = "De datum is onjuist";
        } elseif (isset($first_day) && $fldDay < $first_day) {
            $wrong_dag = "De datum (".$updDag.") kan niet voor ".$eerste_dag." liggen";
        } elseif (isset($maxday_rvo) && $fldDay > $maxday_rvo) {
            $wrong_dag = $txtDag." ligt voor RVO te ver in de toekomst";
        }

        /****** EINDE CONTROLE DATUM *******/

        $schaap_gateway = new SchaapGateway($db);
        /****** CONTROLE LEVENSNUMMER *******/
        // Bestaat alleen bij Geboortes en Aanvoer
        // BCB: en bij omnummeren. Commentaar loopt zo snel achter...
        if (isset($fldLevnr)) {
            // Controle op duplicaten
            $schaapId = $schaap_gateway->zoek_schaapid($fldLevnr);
            $levnr_exist = $schaap_gateway->levnr_exists_outside($fldLevnr, $schaapId);
            // Einde Controle op duplicaten
            if (intval($fldLevnr) == 0) { // levensnummer is 000000000000
                if (isset($wrong_dag)) {
                    $wrong_levnr = $wrong_dag." en het levensnummer is onjuist";
                } else {
                    $wrong_levnr = "Het levensnummer is onjuist";
                }
            } elseif ($levnr_exist) {
                if (isset($wrong_dag)) {
                    $wrong_levnr = $wrong_dag." en levensummer ".$fldLevnr." bestaat al";
                } else {
                    $wrong_levnr = "Levensummer ".$fldLevnr." bestaat al";
                }
            } elseif (strlen($fldLevnr) <> 12) {
                if (isset($wrong_dag)) {
                    $wrong_levnr = $wrong_dag." en ".$fldLevnr." is geen 12 karakters lang";
                } else {
                    $wrong_levnr = $fldLevnr." is geen 12 karakters lang";
                }
            } elseif (Validate::numeriek($fldLevnr) == 1) {
                if (isset($wrong_dag)) {
                    $wrong_levnr = $wrong_dag." en ".$fldLevnr." bevat een letter";
                } else {
                    $wrong_levnr = $fldLevnr." bevat een letter";
                }
            }
        }
        /****** EINDE CONTROLE LEVENSNUMMER *******/

        $request_gateway = new RequestGateway($db);
        $co = $request_gateway->find($recId);

        // Als verwijderd wordt hersteld bestaat kzlBest niet maar de bestemming in de database mogelijk wel en dus $fldBest dan ook !!
        // Dit t.b.v. $wrong_partij
        $melding_gateway = new MeldingGateway($db);
        if ($fldSkip == 0 && $co['skip'] == 1) {
            $fldBest = $melding_gateway->zoek_bestemming($recId);
        }
        // Wijzigen keuze 'controle' versus 'vastleggen'
        // fldDef is hierboven op 'N' gezet, dus die isset() kan weg --BCB
        if (isset($fldDef) && $fldDef <> $co['def']) {
            $request_gateway->setDef($fldDef, $co['reqId']);
        }
        //Hiermee wordt het requestId maar 1x doorlopen en is t.b.v. wijzigen tblRequest i.p.v. elke regel uit tblMeldingen

        // CONTROLE op gewijzigde velden

        // Wijzigen datum

        if (!empty($fldDay) && $fldDay <> $co['datum'] && !isset($wrong_dag)) {
            $historie_gateway->setDatum($fldDay, $recId);
        }

        // Wijzigen levensnummer
        // TODO: (BV) #0004166 wanneer kan dit waar zijn? Je zoekt een schaap op basis van fldLevnr, en db_levnr is het levensnummer van het schaap --BCB
        if (isset($fldLevnr) && $fldLevnr <> $co['levensnummer'] && !isset($wrong_levnr)) {
            $schaap_gateway->changeLevensnummer($co['levensnummer'], $fldLevnr);
        }

        // Wijzigen geslacht
        if (isset($fldSekse) && ($fldSekse <> $co['geslacht'] || !isset($co['geslacht']))) {
            $schaap_gateway->updateGeslacht($co['levensnummer'], $fldSekse);
        }

        $stal_gateway = new StalGateway($db);
        // Wijzigen herkomst
        if (isset($fldHerk) && (!isset($co['rel_herk']) || $fldHerk <> $co['rel_herk'])) {
            $stal_gateway->updateHerkomstByMelding($recId, $fldHerk);
        } elseif (!isset($fldHerk) && $co['code'] == 'AAN') {
            if (isset($wrong_levnr)) {
                $wrong_partij = $wrong_levnr." en herkomst moet zijn gevuld.";
            } elseif (!isset($wrong_levnr) && isset($wrong_dag)) {
                $wrong_partij = $wrong_dag." en herkomst moet zijn gevuld.";
            } else {
                $wrong_partij = "Herkomst moet zijn gevuld.";
            }
        }

        // Wijzigen bestemming
        if ((isset($fldBest) && (!isset($co['rel_best']) || $fldBest <> $co['rel_best']) )) {
            $stal_gateway->updateBestemmingByMelding($recId, $fldBest);
        } elseif (!isset($fldBest) && $co['code'] == 'AFV') {
            if (isset($wrong_levnr)) {
                $wrong_partij = $wrong_levnr." en bestemming moet zijn gevuld.";
            } elseif (isset($wrong_dag)) {
                $wrong_partij = $wrong_dag." en bestemming moet zijn gevuld.";
            } elseif (!isset($fldBest)) {
                $wrong_partij = "Bestemming moet zijn gevuld.";
            }
        }
        // EINDE CONTROLE op gewijzigde velden

        // Veld skip vullen en veld fout ledigen.
        //Als skip wordt gewijzigd naar 0 dan wordt het veld fout eventueel ingevuld bij Foutmelding opslaan
        if ($fldSkip <> $co['skip']) {
            $melding_gateway->updateSkip($recId, $fldSkip);
        }
        // Foutmelding opslaan
        if (isset($wrong_partij)) {
            $wrong = $wrong_partij;
        } elseif (isset($wrong_levnr)) {
            $wrong = $wrong_levnr;
        } elseif (isset($wrong_dag)) {
            $wrong = $wrong_dag;
        }
        if ((isset($wrong) && (!isset($co['fout']) || ($wrong <> $co['fout']) )) || (!isset($wrong) && isset($co['fout']))) {
            // TODO: #0004167 $wrong niet zomaar gebruiken, is soms niet gezet --BCB
            $melding_gateway->updateFout($recId, $wrong ?? null);
        }
        unset($wrong);
        unset($wrong_dag);
        unset($wrong_levnr);
        unset($wrong_partij);
        // Einde Foutmelding opslaan
    }
}
