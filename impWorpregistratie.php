<?php

/*7-2-2020 bestand gekopieerd van reader
8-3-2020 Onderdeel gemaakt van impReaderAgrident.php
21-3-2020  Bestand hernoemd naar impWorpregistratie.php
30-5-2020 Uitval via Worpregistrtaie standaard als Volledig dood geboren opgeslagen
3-7-2020 : Gegevens reader opgeslagen in 1 tabel impAgrident
23-1-2021 : Transponder en MoederTransponder toegevoegd */

$velden_worp = array('ActId', 'MoederTransponder', 'Moeder', 'Datum', 'RasId', 'HokId', 'Verloop', 'Geboren', 'Levend', 'Reden', 'Lammeren');
$velden_lam = array('Transponder', 'Levensnummer', 'Geslacht', 'Gewicht');

$cnt_worp = count($velden_worp);
$last_element = $cnt_worp - 1;
$cnt_lam = count($velden_lam);

foreach ($inhoud as $key => $waarde) {
    // Er zijn maar $cnt_worp elementen in de array want element $cnt_worp is weer een array met meerdere elementen
    for ($g = 0; $g < $cnt_worp; $g++) {
        if ($g == 0) {
            $insert_qry_mdr = " INSERT INTO impAgrident SET ";
            $select_qry = "";
        }
        if ($g < $last_element && ($waarde -> {$velden_worp[$g]} == "" || $waarde -> {$velden_worp[$g]} == "0")) {
            $insert_qry_mdr .= "$velden_worp[$g] = NULL, ";
              $select_qry .= "ISNULL($velden_worp[$g]) and ";
        } elseif ($g < $last_element) {
            $insert_qry_mdr .= "$velden_worp[$g] = '" . $db->real_escape_string($waarde -> {$velden_worp[$g]}) . "', ";
                $select_qry .= "$velden_worp[$g] = '" . $db->real_escape_string($waarde -> {$velden_worp[$g]}) . "' and ";
        }
        if ($g == $last_element) { // element 8 is array met lammeren
            $array = $waarde -> {$velden_worp[$g]};
            foreach ($array as $key1 => $waarde1) {
                $insert_qry_lam = "";
                for ($gl = 0; $gl < $cnt_lam; $gl++) {
                     $insert_qry_lam .= "$velden_lam[$gl] = '" . $db->real_escape_string($waarde1 -> {$velden_lam[$gl]}) . "', ";
                }
                $insert_qry = $insert_qry_mdr;
                $insert_qry .= $insert_qry_lam;
                $insert_qry .= ' lidId = ' . $db->real_escape_string($lidid) . ';';
                echo $insert_qry;
                $db->run_query($insert_qry) or die($db->error($db));
                unset($insert_qry_lam);
                unset($insert_qry);
            }
        }
    }

/*Splits doden lammeren van levend per worp*/
    $zoek_laatste_record =  $db->run_query("
SELECT max(Id) Id FROM impAgrident WHERE lidId = " . $db->real_escape_string($lidid) . " and actId = 1
") or die($db->error($db));
    while ($mi = $zoek_laatste_record->fetch_assoc()) {
        $impId = $mi['Id'];
    }

    if (isset($impId)) { // De allereerste keer per klant kan er nog geen record zijn !!
        // De laatste record moet er wel een zijn van een levend lam
        $zoek_worp_aantallen = $db->run_query("
SELECT geboren, levend FROM impAgrident WHERE levensnummer is not null and Id = " . $db->real_escape_string($impId) . "
") or die($db->error($db));
        while ($wa = $zoek_worp_aantallen->fetch_assoc()) {
            $geboren = $wa['geboren'];
            $levend = $wa['levend'];
        }
        $doden = $geboren - $levend;
    }
    if (isset($doden) && $doden > 0) {
        for ($d = 1; $d <= $doden; $d++) {
            $insert_dood = $insert_qry_mdr . ' lidId = ' . $db->real_escape_string($lidid) . ';';
            echo $insert_dood;
            $db->run_query($insert_dood) or die($db->error($db));
            $zoek_laatste_record =  $db->run_query("
SELECT max(Id) Id FROM impAgrident WHERE lidId = " . $db->real_escape_string($lidid) . "
") or die($db->error($db));
            while ($lst = $zoek_laatste_record->fetch_assoc()) {
                $lstId = $lst['Id'];
            }
            $update_hokId = "UPDATE impAgrident set hokId = NULL, momId = 1 WHERE Id = " . $db->real_escape_string($lstId) . " ";
            $db->run_query($update_hokId) or die($db->error($db));
        }
        $update_tabel = "UPDATE impAgrident set reden = NULL WHERE levensnummer is not null and lidId = " . $db->real_escape_string($lidid) . " ";
        $db->run_query($update_tabel) or die($db->error($db));
    }
}
