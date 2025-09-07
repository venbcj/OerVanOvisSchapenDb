<!-- 10-11-2014 gemaakt 
5-12-2016 : kzlPartij gesplitst in kzlHerk en kzlBest   9-2-2017 ctr-velden verwijderd 
5-5-2017 : Controle bij wijzigen datum aangepast van $fldDay < $last_day naar $fldDay > $last_day
26-1-2018 : Bij verwijderen melding wordt kzlBest niet meer leeggemaakt 
19-2-2022 : SQL beveiligd met quotes 
4-4-2022 : Controle $zoek_laatste_datum_stalaf uitgezet want reden van deze controle onbekend 
10-5-2023 : $eerste_dag keek niet naar een actie op stallijst (tblActie.op = 1). Dit is aangepast 
19-01-2024 : Controle melding verplicht gemaakt 
30-01-2024 : Controle of het veld kzlDef bestaat verplaatst. Zie isset($fldDef)
27-03-2025 : else if ($key == 'kzlHerk' && empty($value))  { $fldHerk = 'leegkeuzelijst'; } verwijderd -->
<?php
/*Save_Melding.php toegpast in :
    - MeldAanvoer.php
    - MeldAfvoer.php
    - MeldGeboortes.php
- MeldUitval.php    */

function getNameFromKey($key) {
    $array = explode('_', $key);
    return $array[0];
}

function getIdFromKey($key) {
    $array = explode('_', $key);
    return $array[1];
}

function zoek_eerste_datum_stalop($db, $recId) {
    return mysqli_query($db, "
SELECT min(datum) date, date_format(min(datum),'%d-%m-%Y') datum
FROM tblHistorie h
 join tblActie a on (a.actId = h.actId)
 join (
    SELECT st.stalId, h.hisId
    FROM tblStal st
     join tblHistorie h on (h.stalId = st.stalId)
     join tblMelding m on (m.hisId = h.hisId)
    WHERE m.meldId = '$recId'
 ) st on (st.stalId = h.stalId and st.hisId <> h.hisId)
 WHERE a.op = 1
")
}

function zoek_schaapid($db, $fldLevnr) {
    return mysqli_query($db, "
SELECT schaapId
FROM tblSchaap 
WHERE levensnummer = '".mysqli_real_escape_string($db, $fldLevnr)."'");
}

function count_levnr($db, $fldLevnr, $schaapId) {
    return mysqli_query($db, "
SELECT count(*) aant
FROM tblSchaap 
WHERE levensnummer = '".mysqli_real_escape_string($db, $fldLevnr)."' and schaapId <> '".mysqli_real_escape_string($db, $schaapId)."'");
}

function zoek_in_database($db, $recId) {
    return mysqli_query($db, "
SELECT r.reqId, r.code, r.def, m.skip, m.fout, h.datum, s.levensnummer, s.geslacht, st.rel_herk, st.rel_best
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
WHERE m.meldId = '$recId'
");
}

function zoek_bestemming_in_db($db, $recId) {
    return mysqli_query($db, "
SELECT st.rel_best
FROM tblMelding m
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
WHERE m.meldId = '$recId'
");
}

$array = array();

foreach ($_POST as $fldname => $fldvalue) {
    $multip_array[getIdFromKey($fldname)][getNameFromKey($fldname)] = $fldvalue;
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

    if (isset($recId) and $recId > 0) {
        $recId = (int) $recId;
        if (!isset($fldDef)) {
            $fldDef = 'N';
        }

        /****** CONTROLE DATUM *******/

        $nummer_van_datum = intval(str_replace('-', '', $txtDag));

        /* Eerste datum zoeken ter controle bij aanvoer bedrijf */
        if ($code == 'AAN' || $code == 'GER') {
            $zoek_eerste_datum_stalop = zoek_eerste_datum_stalop($db, $recId);
            while ($mi = mysqli_fetch_assoc($zoek_eerste_datum_stalop)) {
                $first_day = $mi['date'];
                $eerste_dag = $mi['datum'];
            }
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

        /****** CONTROLE LEVENSNUMMER *******/
        // Bestaat alleen bij Geboortes en Aanvoer
        if (isset($fldLevnr)) {
            // Controle op duplicaten
            $zoek_schaapId = zoek_schaapid($db, $fldLevnr);
            $zs = mysqli_fetch_assoc($zoek_schaapId);
            $schaapId = $zs['schaapId'];

            $count_levnr = count_levnr($db, $fldLevnr, $schaapId);
            $row = mysqli_fetch_assoc($count_levnr);
            $levnr_exist = $row['aant'];
            // Einde Controle op duplicaten
            if (intval($fldLevnr) == 0) { // levensnummer is 000000000000
                if (isset($wrong_dag)) {
                    $wrong_levnr = $wrong_dag." en het levensnummer is onjuist";
                } else {
                    $wrong_levnr = "Het levensnummer is onjuist";
                }
            } elseif ($levnr_exist > 0) {
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
            } elseif (numeriek($fldLevnr) == 1) {
                if (isset($wrong_dag)) {
                    $wrong_levnr = $wrong_dag." en ".$fldLevnr." bevat een letter";
                } else {
                    $wrong_levnr = $fldLevnr." bevat een letter";
                }
            }
        }
        /****** EINDE CONTROLE LEVENSNUMMER *******/

        $zoek_in_database = zoek_in_database($db, $recId);
        while ($co = mysqli_fetch_assoc($zoek_in_database)) {
            $reqId = $co['reqId'];
            $code = $co['code'];
            $def_db = $co['def'];
            $skip_db = $co['skip'];
            $fout_db = $co['fout'];
            $datum_db = $co['datum'];
            $Levnr_db = $co['levensnummer'];
            $sekse_db = $co['geslacht'];
            $herk_db = $co['rel_herk'];
            $best_db = $co['rel_best'];
        }

        // Als verwijderd wordt hersteld bestaat kzlBest niet maar de bestemming in de database mogelijk wel en dus $fldBest dan ook !!
        // Dit t.b.v. $wrong_partij
        if ($fldSkip == 0 && $skip_db == 1) {
            $zoek_bestemming_in_db = zoek_bestemming_in_db($db, $recId);
            while ($zbid = mysqli_fetch_assoc($zoek_bestemming_in_db)) {
                $fldBest = $zbid['rel_best'];
            }
        }
        // Wijzigen keuze 'controle' versus 'vastleggen'
        if (isset($fldDef) && $fldDef <> $def_db) {
            $upd_tblRequest = "UPDATE tblRequest SET def = '".mysqli_real_escape_string($db, $fldDef)."' WHERE reqId = '".mysqli_real_escape_string($db, $reqId)."' ";
            mysqli_query($db, $upd_tblRequest) or die(mysqli_error($db));
        }
        //unset ($reqId);
        //Hiermee wordt het requestId maar 1x doorlopen en is t.b.v. wijzigen tblRequest i.p.v. elke regel uit tblMeldingen

        // CONTROLE op gewijzigde velden

        // Wijzigen datum

        if (!empty($fldDay) && $fldDay <> $datum_db && !isset($wrong_dag)) {
            $upd_tblHistorie = "
 UPDATE tblHistorie h
  join tblMelding m on (h.hisId = m.hisId)
 set   h.datum  = '".mysqli_real_escape_string($db, $fldDay)."'
 WHERE m.meldId = '$recId' 
 ";
            mysqli_query($db, $upd_tblHistorie) or die(mysqli_error($db));
        }

        // Wijzigen levensnummer
        if (isset($fldLevnr) && $fldLevnr <> $Levnr_db && !isset($wrong_levnr)) {
            $upd_tblSchaap = "UPDATE tblSchaap SET levensnummer = '".mysqli_real_escape_string($db, $fldLevnr)."' WHERE levensnummer = '".mysqli_real_escape_string($db, $Levnr_db)."' ";
            mysqli_query($db, $upd_tblSchaap) or die(mysqli_error($db));
        }

        // Wijzigen geslacht
        if (isset($fldSekse) && ($fldSekse <> $sekse_db || !isset($sekse_db))) {
            $upd_tblSchaap = "UPDATE tblSchaap SET geslacht = '".mysqli_real_escape_string($db, $fldSekse)."' WHERE levensnummer = '".mysqli_real_escape_string($db, $Levnr_db)."' ";
            mysqli_query($db, $upd_tblSchaap) or die(mysqli_error($db));
        }

        // Wijzigen herkomst
        if (isset($fldHerk) && (!isset($herk_db) || $fldHerk <> $herk_db)) {
            $upd_tblStal = "
            UPDATE tblStal st
             join tblHistorie h on (h.stalId = st.stalId)
             join tblMelding m on (m.hisId = h.hisId)
            set st.rel_herk = '".mysqli_real_escape_string($db, $fldHerk)."' 
            WHERE m.meldId = '$recId'
            ";
            mysqli_query($db, $upd_tblStal) or die(mysqli_error($db));
        } elseif (!isset($fldHerk) && $code == 'AAN') {
            if (isset($wrong_levnr)) {
                $wrong_partij = $wrong_levnr." en herkomst moet zijn gevuld.";
            } elseif (!isset($wrong_levnr) && isset($wrong_dag)) {
                $wrong_partij = $wrong_dag." en herkomst moet zijn gevuld.";
            } else {
                $wrong_partij = "Herkomst moet zijn gevuld.";
            }
        }

        // Wijzigen bestemming
        if ((isset($fldBest) && (!isset($best_db) || $fldBest <> $best_db) )) {
            $upd_tblStal = "
            UPDATE tblStal st
             join tblHistorie h on (h.stalId = st.stalId)
             join tblMelding m on (m.hisId = h.hisId)
            set st.rel_best = '".mysqli_real_escape_string($db, $fldBest)."'
            WHERE m.meldId = '$recId'
            ";
            mysqli_query($db, $upd_tblStal) or die(mysqli_error($db));
        } elseif (!isset($fldBest) && $code == 'AFV') {
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
        if ($fldSkip <> $skip_db) {
            $upd_tblMelding = "UPDATE tblMelding SET skip = '".mysqli_real_escape_string($db, $fldSkip)."', fout = NULL WHERE meldId = '$recId' ";
            mysqli_query($db, $upd_tblMelding) or die(mysqli_error($db));
        }
        // Foutmelding opslaan
        if (isset($wrong_partij)) {
            $wrong = $wrong_partij;
        } elseif (isset($wrong_levnr)) {
            $wrong = $wrong_levnr;
        } elseif (isset($wrong_dag)) {
            $wrong = $wrong_dag;
        }
        if ((isset($wrong) && (!isset($fout_db) || ($wrong <> $fout_db) )) || (!isset($wrong) && isset($fout_db))) {
            $upd_tblMelding = "UPDATE tblMelding SET fout = " . db_null_input($wrong) . " WHERE meldId = '$recId' and skip <> 1";
            mysqli_query($db, $upd_tblMelding) or die(mysqli_error($db));
        }
        unset($wrong);
        unset($wrong_dag);
        unset($wrong_levnr);
        unset($wrong_partij);
        // Einde Foutmelding opslaan
    }
}
