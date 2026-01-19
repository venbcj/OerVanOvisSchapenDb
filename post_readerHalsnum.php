<?php

/*
 * <!-- 30-9-2020 Gekopieerd van post_readerOmnum.php
7-5-2021 : isset(verwerkt) toegevoegd om dubbele invoer te voorkomen. Verschil tussen kiezen of verwijderen herschreven. SQL beveiligd met quotes. -->
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
//echo '$recId = '.$recId.'<br>';
// Einde Id ophalen
    foreach ($id as $key => $value) {
        if ($key == 'chbkies') {
            $fldKies = $value;
        }
        if ($key == 'chbDel') {
            $fldDel = $value;
        }
        if ($key == 'txtDag' && !empty($value)) {
            $dag = date_create($value);
            $valuedate =  date_format($dag, 'Y-m-d');
                                    /*echo $key.'='.$valuedate.' ';*/ $fldDay = $valuedate;
        }
        if ($key == 'kzlKleur' && !empty($value)) {
     /*echo $key.'='.$valuedate.' ';*/ $fldKleur = $value;
        }
        if ($key == 'txtHalsnr' && !empty($value)) {
     /*echo $key.'='.$valuedate.' ';*/ $fldHalsnr = $value;
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
    // CONTROLE op alle verplichten velden
        if (isset($fldDay) && isset($fldKleur) && isset($fldHalsnr)) {
            $zoek_schaapId = mysqli_query($db, "
SELECT s.schaapId
FROM impAgrident rd
 join tblSchaap s on (s.levensnummer = rd.levensnummer)
WHERE rd.Id = '" . mysqli_real_escape_string($db, $recId) . "'
") or die(mysqli_error($db));
            while ($zs = mysqli_fetch_assoc($zoek_schaapId)) {
                $schaapId = $zs['schaapId'];
            }
        //echo '$levnr = '.$levnr.'<br>';
            $zoek_stalId = mysqli_query($db, "
SELECT stalId
FROM tblStal
WHERE schaapId = '" . mysqli_real_escape_string($db, $schaapId) . "' and lidId = '" . mysqli_real_escape_string($db, $lidId) . "' and isnull(rel_best)
") or die(mysqli_error($db));
            while ($st = mysqli_fetch_assoc($zoek_stalId)) {
                $stalId = $st['stalId'];
            }
        //echo '$stalId = '.$stalId.'<br>';
            $update_tblStal = "UPDATE tblStal set kleur = '" . mysqli_real_escape_string($db, $fldKleur) . "', halsnr = '" . mysqli_real_escape_string($db, $fldHalsnr) . "'
        WHERE stalId = '" . mysqli_real_escape_string($db, $stalId) . "'
     ";
            mysqli_query($db, $update_tblStal) or die(mysqli_error($db));
            $updateReader = "UPDATE impAgrident SET verwerkt = 1 WHERE Id = '" . mysqli_real_escape_string($db, $recId) . "' ";
            mysqli_query($db, $updateReader) or die(mysqli_error($db));
        }
    // EINDE CONTROLE op alle verplichten velden
    }
 // Einde if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt))
    if ($fldKies == 0 && $fldDel == 1) {
        $updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = '" . mysqli_real_escape_string($db, $recId) . "' " ;
        mysqli_query($db, $updateReader) or die(mysqli_error($db));
    }
}
