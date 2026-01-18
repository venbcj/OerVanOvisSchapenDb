<?php

/* .php toegepast in :
- InsUitval.php
<!--  10-5-2014 : Bijwerken tblBezetting toegevoegd
16-11-2014 include Maak_Request toegevoegd
2-3-2017 : het kunnen verwijderen toegevoegd en recId zodat hidden velden in insUitval.php weg kunnen
13-7-2020 : Onderscheid gemaakt tussen reader Biocontrol en Agrident gemaakt
23-1-2021 : In UPDATE impAgrident readId gewijzigd in Id. Sql beveiligd met quotes verschil tussen kiezen of verwijderen herschreven
8-5-2021 : isset(verwerkt) toegevoegd om dubbele invoer te voorkomen -->
 */
$array = array();
foreach ($_POST as $key => $value) {
    $array[Url::getIdFromKey($key)][Url::getNameFromKey($key)] = $value;
}
foreach ($array as $recId => $id) {
    if (!$recId) {
        continue;
    }
// Id ophalen
//echo $recId.'<br>';
// Einde Id ophalen
    foreach ($id as $key => $value) {
        if ($key == 'chbkies') {
            $fldKies = $value;
        }
        if ($key == 'chbDel') {
            $fldDel = $value;
        }
        if ($key == 'txtuitvdm') {
            $dag = date_create($value);
            $flddag = date_format($dag, 'Y-m-d');
        }
        if ($key == 'txtlevuitv' && !empty($value)) {
            $fldlevnr = $value;
        }
        if ($key == 'kzlreden' && !empty($value)) {
            $updreden = $value;
        } elseif ($key == 'kzlreden' && empty($value)) {
            $updreden = '';
        }
    }
// (extra) controle of readerregel reeds is verwerkt. Voor als de pagina 2x wordt verstuurd bij fouten op de pagina
    unset($verwerkt);
    if ($reader == 'Agrident') {
        $zoek_readerRegel_verwerkt = mysqli_query($db, "
SELECT verwerkt
FROM impAgrident
WHERE Id = '" . mysqli_real_escape_string($db, $recId) . "'
") or die(mysqli_error($db));
    } else {
        $zoek_readerRegel_verwerkt = mysqli_query($db, "
SELECT verwerkt
FROM impReader
WHERE readId = '" . mysqli_real_escape_string($db, $recId) . "'
") or die(mysqli_error($db));
    }
    while ($verw = mysqli_fetch_array($zoek_readerRegel_verwerkt)) {
        $verwerkt = $verw['verwerkt'];
    }
// Einde (extra) controle of readerregel reeds is verwerkt.
    if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt)) {
     // isset($verwerkt) is een extra controle om dubbele invoer te voorkomen
    // CONTROLE op alle verplichten velden bij uitval
        if (isset($flddag) && isset($fldlevnr)) {
            $update_tblSchaap = "UPDATE tblSchaap SET redId = " . db_null_input($updreden) . " WHERE levensnummer = '" . mysqli_real_escape_string($db, $fldlevnr) . "' ";
            mysqli_query($db, $update_tblSchaap) or die(mysqli_error($db));
        // Update tblStal
            $zoek_stalId = mysqli_query($db, "
SELECT stalId 
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
WHERE lidId = '" . mysqli_real_escape_string($db, $lidId) . "' and levensnummer = '" . mysqli_real_escape_string($db, $fldlevnr) . "' and isnull(rel_best)
") or die(mysqli_error($db));
            while ($stId = mysqli_fetch_assoc($zoek_stalId)) {
                $stalId = $stId['stalId'];
            }
            $insert_tblHistorie = "INSERT INTO tblHistorie SET stalId = '" . mysqli_real_escape_string($db, $stalId) . "', datum = '" . mysqli_real_escape_string($db, $flddag) . "', actId = 14 ";
                mysqli_query($db, $insert_tblHistorie) or die(mysqli_error($db));
        // Update tblStal
            $update_tblStal = "UPDATE tblStal SET rel_best = '" . mysqli_real_escape_string($db, $rendac_Id) . "' WHERE stalId = '" . mysqli_real_escape_string($db, $stalId) . "' ";
                mysqli_query($db, $update_tblStal) or die(mysqli_error($db));
        // Einde Update tblStaplek
            if ($reader == 'Agrident') {
                    $updateReader = "UPDATE impAgrident SET verwerkt = 1 WHERE Id = '" . mysqli_real_escape_string($db, $recId) . "' ";
            } else {
                $updateReader = "UPDATE impReader SET verwerkt = 1 WHERE readId = '" . mysqli_real_escape_string($db, $recId) . "' ";
            }
            mysqli_query($db, $updateReader) or die(mysqli_error($db));
            if ($modmeld == 1) {
                $zoek_hisId = mysqli_query($db, "SELECT hisId FROM tblHistorie WHERE stalId = '" . mysqli_real_escape_string($db, $stalId) . "' and actId = 14 and skip = 0 ") or die(mysqli_error($db));
                while ($hId = mysqli_fetch_assoc($zoek_hisId)) {
                    $hisId = $hId['hisId'];
                }
                $Melding = 'DOO';
                include "maak_request.php";
            }
        }
    // EINDE CONTROLE op alle verplichten velden bij uitval
    }
 // Einde if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt))
    if ($fldKies == 0 && $fldDel == 1) {
        if ($reader == 'Agrident') {
            $updateReader = "UPDATE impAgrident SET verwerkt = 1 WHERE Id = '" . mysqli_real_escape_string($db, $recId) . "' ";
        } else {
            $updateReader = "UPDATE impReader SET verwerkt = 1 WHERE readId = '" . mysqli_real_escape_string($db, $recId) . "' " ;
        }
    /*echo $updateReader.'<br>';*/        mysqli_query($db, $updateReader) or die(mysqli_error($db));
    }
}
