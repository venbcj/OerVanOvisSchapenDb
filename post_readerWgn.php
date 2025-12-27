<?php
/* 3-9-2017 aangemaakt
8-5-2021 : isset(verwerkt) toegevoegd om dubbele invoer te voorkomen. Verschil tussen kiezen of verwijderen herschreven. SQL beveiligd met quotes */
$array = array();
foreach($_POST as $key => $value) {
    $array[Url::getIdFromKey($key)][Url::getNameFromKey($key)] = $value;
}

$schaap_gateway = new SchaapGateway();
$impagrident_gateway = new ImpAgridentGateway();
$impreader_gateway = new ImpReaderGateway();
$historie_gateway = new HistorieGateway();
foreach($array as $recId => $id) {
    if (!$recId) continue;
    // Id ophalen
    //echo '$recId = '.$recId.'<br>';
    // Einde Id ophalen
    foreach($id as $key => $value) {
        if ($key == 'chbkies') { $fldKies = $value; }
        if ($key == 'chbDel') { $fldDel = $value; }
        if ($key == 'txtWeegdag' && !empty($value)) { $dag = date_create($value); $fldday =  date_format($dag, 'Y-m-d');  }
        if ($key == 'txtKg' && !empty($value)) { $fldkg = str_replace(',', '.', $value); }
    }
    // (extra) controle of readerregel reeds is verwerkt. Voor als de pagina 2x wordt verstuurd bij fouten op de pagina
    if($reader == 'Agrident') {
        $verwerkt = $impagrident_gateway->zoek_readerregel_verwerkt($recId);
    }
    else {
        $verwerkt = $impreader_gateway->zoek_readerregel_verwerkt($recId);
    }
    // Einde (extra) controle of readerregel reeds is verwerkt.
    if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt)) { // isset($verwerkt) is een extra controle om dubbele invoer te voorkomen
        // CONTROLE op alle verplichten velden
        if ( isset($fldday) && isset($fldkg) ) {
            $levnr = $impagrident_gateway->zoek_levnr_reader($recId)[0];
            $schaapId = $schaap_gateway->zoek_schaapid($levnr);
            $stalId = $stal_gateway->zoek_stal($lidId, $schaapId);
            $historie_gateway->wegen_invoeren($stalId, $fldday, $fldkg);
            unset ($fldkg);
            $impagrident_gateway->set_verwerkt($recId);
        }
        // EINDE CONTROLE op alle verplichten velden
    } // Einde if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt))
    if ($fldKies == 0 && $fldDel == 1) {
        $impagrident_gateway->set_verwerkt($recId);
    }
}
