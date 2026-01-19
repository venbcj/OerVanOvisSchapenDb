<?php

/* 6-3-2015 : sql beveiligd
 19-1-2017 Query's aangepast n.a.v. nieuwe tblDoel en    21-1-2017 hidden velden txtId, txtLevspn en txtOvplId verwijderd in insSpenen.php en codering hier aangepast => recId nieuw    Ook speengewicht niet verplicht gemaakt     22-1-2017 : tblBezetting gewijzigd naar tblBezet
 12-2-2017 : insert tblPeriode verwijderd Priode wordt niet meer opgeslagen in tblBezet.
 10-6-2020 : onderscheid gemaakt tussen reader Agrident en Biocontrol
 13-7-2020 : impVerplaatsing gewijzigd in impAgrident
 8-5-2021 : isset(verwerkt) toegevoegd om dubbele invoer te voorkomen. Verschil tussen kiezen of verwijderen herschreven. SQL beveiligd met quotes
 */
$array = array();
foreach ($_POST as $key => $value) {
    $array[Url::getIdFromKey($key)][Url::getNameFromKey($key)] = $value;
}
foreach ($array as $recId => $id) {
// Id ophalen
//echo '$recId = '.$recId.'<br>';
// Einde Id ophalen
    foreach ($id as $key => $value) {
        if ($key == 'txtSpeendag' && !empty($value)) {
            $dag = date_create($value);
            $flddag =  date_format($dag, 'Y-m-d');
        }
        if ($key == 'txtKg' && !empty($value)) {
            $fldkg = str_replace(',', '.', $value);
        } elseif ($key == 'txtKg' && empty($value)) {
            $fldkg = '';
        }
        if ($key == 'kzlHok' && !empty($value)) {
            $fldhok = $value;
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
    // CONTROLE op alle verplichten velden bij spenen lam
        if (isset($flddag) && isset($fldhok)) {
            if ($reader == 'Agrident') {
                $zoek_levensnummer = mysqli_query($db, "
    SELECT levensnummer levnr_sp
    FROM impAgrident
    WHERE Id =  '" . mysqli_real_escape_string($db, $recId) . "' 
    ") or die(mysqli_error($db));
            } else {
                $zoek_levensnummer = mysqli_query($db, "
    SELECT levnr_sp
    FROM impReader
    WHERE readId =  '" . mysqli_real_escape_string($db, $recId) . "' 
    ") or die(mysqli_error($db));
            }
            while ($lvn = mysqli_fetch_assoc($zoek_levensnummer)) {
                $levnr = $lvn['levnr_sp'];
            }
            $zoek_schaapId = mysqli_query($db, "
    SELECT schaapId
    FROM tblSchaap
    WHERE levensnummer = '" . mysqli_real_escape_string($db, $levnr) . "'
    ") or die(mysqli_error($db));
            while ($sId = mysqli_fetch_assoc($zoek_schaapId)) {
                $schaapId = $sId['schaapId'];
            }
            $zoek_stalId = mysqli_query($db, "
    SELECT stalId
    FROM tblStal
    WHERE lidId = '" . mysqli_real_escape_string($db, $lidId) . "' and schaapId = " . $schaapId . " and isnull(rel_best)
    ") or die(mysqli_error($db));
            while ($stId = mysqli_fetch_assoc($zoek_stalId)) {
                $stalId = $stId['stalId'];
            }
            $insert_tblHistorie = "INSERT INTO tblHistorie set stalId = '" . mysqli_real_escape_string($db, $stalId) . "', datum = '" . mysqli_real_escape_string($db, $flddag) . "', kg = " . db_null_input($fldkg) . ", actId = 4 ";
        /*echo $insert_tblHistorie.'<br>';*/  mysqli_query($db, $insert_tblHistorie) or die(mysqli_error($db));
            unset($fldkg);
        // Insert tblBezet
            $zoek_hisId = mysqli_query($db, "
        SELECT hisId
        FROM tblHistorie
        WHERE stalId = '" . mysqli_real_escape_string($db, $stalId) . "' and actId = 4
        ") or die(mysqli_error($db));
            while ($hId = mysqli_fetch_assoc($zoek_hisId)) {
                $hisId = $hId['hisId'];
            }
            $insert_tblBezet = "INSERT INTO tblBezet set hisId = '" . mysqli_real_escape_string($db, $hisId) . "', hokId = '" . mysqli_real_escape_string($db, $fldhok) . "' ";
        /*echo $insert_tblBezet.'<br>';*/  mysqli_query($db, $insert_tblBezet) or die(mysqli_error($db));
        // Einde Insert tblBezet
            if ($reader == 'Agrident') {
                    $updateReader = "UPDATE impAgrident SET verwerkt = 1 WHERE Id =  '" . mysqli_real_escape_string($db, $recId) . "' ";
            } else {
                $updateReader = "UPDATE impReader SET verwerkt = 1 WHERE readId =  '" . mysqli_real_escape_string($db, $recId) . "' ";
            }
        /*echo '$updateReader = '.$updateReader.'<br>';*/  mysqli_query($db, $updateReader) or die(mysqli_error($db));
            if ($reader == 'Agrident') {
                $zoek_readId_tbv_overplaatsen = mysqli_query($db, "
    SELECT coalesce(rd.hokId,ro.Id) readId_ovpl
    FROM impAgrident rd 
     left join (
        SELECT lidId, levensnummer, Id 
        FROM impAgrident
        WHERE actId = 5 and isnull(verwerkt) 
     ) ro on (rd.lidId = ro.lidId and rd.levensnummer = ro.levensnummer)
    WHERE rd.Id =  '" . mysqli_real_escape_string($db, $recId) . "'
    ") or die(mysqli_error($db));
            } else {
                $zoek_readId_tbv_overplaatsen = mysqli_query($db, "
    SELECT coalesce(rd.hok_sp,ro.readId) readId_ovpl
    FROM impReader rd 
     left join (
        SELECT lidId, levnr_ovpl, readId 
        FROM impReader 
        WHERE teller_ovpl is not null and isnull(verwerkt) 
     ) ro on (rd.lidId = ro.lidId and rd.levnr_sp = ro.levnr_ovpl)
    WHERE rd.readId =  '" . mysqli_real_escape_string($db, $recId) . "'
    ") or die(mysqli_error($db));
            }
            while ($ovp = mysqli_fetch_assoc($zoek_readId_tbv_overplaatsen)) {
                $ovplId = $ovp['readId_ovpl'];
            }
            if (isset($ovplId)) {
                if ($reader == 'Agrident') {
                    $updateReaderOvpl = "UPDATE impAgrident SET verwerkt = 1 WHERE Id = '" . mysqli_real_escape_string($db, $ovplId) . "' ";
                } else {
                    $updateReaderOvpl = "UPDATE impReader SET verwerkt = 1 WHERE readId = '" . mysqli_real_escape_string($db, $ovplId) . "' ";
                }
            /*echo '$updateReaderOvpl = '.$updateReaderOvpl.'<br>';*/  mysqli_query($db, $updateReaderOvpl) or die(mysqli_error($db));
                unset($ovplId);
            }
        }
     // EINDE CONTROLE op alle verplichten velden bij spenen lam
    }
 // Einde if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt))
    if ($fldKies == 0 && $fldDel == 1) {
        if ($reader == 'Agrident') {
            $updateReader_del = "UPDATE impAgrident SET verwerkt = 1 WHERE Id =  '" . mysqli_real_escape_string($db, $recId) . "' " ;
        } else {
            $updateReader_del = "UPDATE impReader SET verwerkt = 1 WHERE readId =  '" . mysqli_real_escape_string($db, $recId) . "' " ;
        }
    /*echo '$updateReader_del = '.$updateReader_del.'<br>';*/  mysqli_query($db, $updateReader_del) or die(mysqli_error($db));
    }
}
