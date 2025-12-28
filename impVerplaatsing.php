<?php

/*27-2-2020 bestand gekopieerd van impGeboortes.php
8-3-2020 Onderdeel gemaakt van impReaderAgrident.php
3-7-2020 : Gegevens reader opgeslagen in 1 tabel impAgrident
26-1-2021 : Transponder toegevoegd
12-02-2021 : Controle Lambar in Newreader_keuzelijsten.php weggehaald en hier toegevoegd. SQL beveiligd met quotes */

$velden = array('ActId', 'Datum', 'Transponder', 'Levensnummer', 'Reden', 'MoederTransponder', 'Moeder', 'Gewicht', 'HokId' );
$cnt_velden = count($velden);
foreach ($inhoud as $index => $waarde) {
    for ($h = 0; $h < $cnt_velden; $h++) { // Er zijn 8 elementen
        if ($h == 0) {
            $insert_qry = " INSERT INTO impAgrident SET ";
        }
        if ($waarde -> {$velden[$h]} == "" || $waarde -> {$velden[$h]} == "0") {
            $insert_qry .= "$velden[$h] = NULL, ";
        } else {
            $insert_qry .= "$velden[$h] = '" . $db->real_escape_string($waarde -> {$velden[$h]}) . "', ";
        }
    }
    $insert_qry .= ' lidId = ' . $db->real_escape_string($lidid) . ';';
    echo $insert_qry; // de tests in JsonAgridentParserTest leunen nu op deze uitvoer, maar dat moet veranderen.
    $db->query($insert_qry);
    unset($insert_qry);
// update record t.b.v. verblijf lambar
    $impagrident_gateway = new ImpAgridentGateway();
    $lbarId = $impagrident_gateway->zoek_lambar_record($lidid);
    if (isset($lbarId)) {
        $hok_gateway = new HokGateway();
        $hokId = $hok_gateway->zoek_lambar($lidid);
        if (!isset($hokId)) {
            $hokId = $hok_gateway->insert_lambar($lidid);
        }
        $impagrident_gateway->update_hok($lbarId, $hokId);
    }
}
