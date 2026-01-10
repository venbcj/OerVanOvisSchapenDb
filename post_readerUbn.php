<?php

# <!-- 30-08-2025 Kopie gemaakt van post_readerAfv.php  -->
$array = array();
foreach ($_POST as $key => $value) {
    $array[Url::getIdFromKey($key)][Url::getNameFromKey($key)] = $value;
}
$historie_gateway = new HistorieGateway();
$impagrident_gateway = new ImpAgridentGateway();
$partij_gateway = new PartijGateway();
$stal_gateway = new StalGateway();
foreach ($array as $recId => $id) {
    unset($fldKies);
    unset($fldDel);
    unset($fldDag);
    unset($fldKg);
    foreach ($id as $key => $value) {
        if ($key == 'chbkies') {
            $fldKies = $value;
        }
        if ($key == 'chbDel') {
            $fldDel = $value;
        }
        if ($key == 'txtAfvoerdag' && !empty($value)) {
            $dag = date_create($value);
            $valuedatum =  date_format($dag, 'Y-m-d');
                                     $fldDag = $valuedatum;
        }
        if ($key == 'txtKg' && !empty($value)) {
            $fldKg = str_replace(',', '.', $value);
        }
    }
// Als checkboxen niet bestaan
    if (!isset($fldKies)) {
        $fldKies = 0;
    }
    if (!isset($fldDel)) {
        $fldDel = 0;
    }
// (extra) controle of readerregel reeds is verwerkt. Voor als de pagina 2x wordt verstuurd bij fouten op de pagina
    unset($verwerkt);
    $verwerkt = $impagrident_gateway->zoek_readerRegel_verwerkt($recId);
    unset($hisId_afv);
    unset($hisId_aanv);
/**** UBN WIJZIGING REGISTREREN ****/
    if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt)) {
    // CONTROLE op alle verplichten velden bij afvoer
        if (isset($fldDag)) {
            $zoek_data_reader = $impagrident_gateway->zoek_data_reader($recId);
            while ($zdr = $zoek_data_reader->fetch_assoc()) {
                $levnr = $zdr['levensnummer'];
                $ubnId_best = $zdr['ubnId'];
                $ubn_best = $zdr['ubn'];
                $schaapId = $zdr['schaapId'];
            }
            $rel_best = $partij_gateway->zoek_relatie_afvoer($ubn_best);
            [$stalId_afv, $ubn_herk] = $stal_gateway->zoek_stalId_afvoer($lidId, $schaapId);
            if (!isset($stalId_afv)) {
                // TODO: deze variabele wordt nergens gezet. Kopie-rest van post_readerAfv?
                echo $fldLevnr . ' staat niet meer op de stallijst !';
            } else {
                $aanwas = $historie_gateway->zoek_aanwas($schaapId);
                if (isset($aanwas)) {
                    $actId = 13;
                } else {
                    $actId = 12;
                }
                unset($aanwas);
                $insert_tblHistorie_afvoer = $historie_gateway->herstel_invoeren($stalId_afv, $fldDag, $fldKg, $actId);
                $update_tblStal_afvoer = $stal_gateway->update_tblStal_afvoer($rel_best, $stalId_afv);
                if ($modmeld == 1) {
                        $hisId_afv = $historie_gateway->zoek_hisId($stalId_afv, $actId);
                    $Melding = 'AFV';
                    $hisId = $hisId_afv;
                    include "maak_request.php";
                }
                $rel_herk = $partij_gateway->zoek_relatie_aanvoer($ubn_herk);
                $stalId_aanv = $stal_gateway->insert_tblStal_aanvoer($lidId, $ubnId_best, $schaapId, $rel_herk);
                $insert_tblHistorie_aanvoer = $historie_gateway->insert_tblHistorie_aanvoer($stalId_aanv, $fldDag, $fldKg);
                if ($modmeld == 1) {
                    $zoek_hisId_aanv = $historie_gateway->zoek_hisId_aanv($stalId_aanv);
                    while ($zha = $zoek_hisId_aanv->fetch_assoc()) {
                        $hisId_aanv = $zha['hisId'];
                    }
                    $Melding = 'AAN';
                    $hisId = $hisId_aanv;
                    include "maak_request.php";
                }
                $impagrident_gateway->set_verwerkt($recId);
            }
        }
    }
/**** Einde UBN WIJZIGING REGISTREREN ****/
/**** VERWIJDEREN ****/
    if ($fldKies == 0 && $fldDel == 1) {
        $impagrident_gateway->set_verwerkt($recId);
    }
/**** Einde VERWIJDEREN ****/
}
