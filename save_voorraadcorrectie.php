<!-- 30-8-2020 gemaakt
30-12-2023 sql beveiligd met quotes -->
<?php

/*Save_Artikel.php toegpast in :
- Voorraadcorrectie.php    */
$array = array();
foreach ($_POST as $key => $value) {
    $array[Url::getIdFromKey($key)][Url::getNameFromKey($key)] = $value;
}
$inkoop_gateway = new InkoopGateway();
$nuttig_gateway = new NuttigGateway();
$voeding_gateway = new VoedingGateway();
foreach ($array as $recId => $id) {
    if (!$recId) {
        continue;
    }
    //echo '<br>'.'$recId = '.$recId.'<br>';
    foreach ($id as $key => $value) {
        if ($key == 'txtCorat' && !empty($value)) {
            $updAantal = str_replace(',', '.', $value);
        }
        if ($key == 'kzlCorr' && !empty($value)) {
            $updCorr = $value;
        }
    }
    if (isset($recId) && $recId > 0 && isset($updAantal)) {
        if ($updCorr == 'af') {
            $updCorrat =  $updAantal;
        }
        if ($updCorr == 'bij') {
            $updCorrat = -$updAantal;
        }
        $soort = $inkoop_gateway->zoek_soort_artikel($recId);
        /*Wijzig voorraad medicatie */
        if ($soort == 'pil') {
            $voorraad = $inkoop_gateway->zoek_voorraad_pil($recId);
            $afboek = $nuttig_gateway->zoek_afgeboekt_pil($recId);
            $tabel = 'tblNuttig';
        }
        /*Wijzig voorraad voer */
        if ($soort == 'voer') {
            [$voorraad, $eenh] = $inkoop_gateway->zoek_voorraad_voer($recId);
            $afboek = $voeding_gateway->zoek_afgeboekt_voer($recId);
            $tabel = 'tblVoeding';
        }
        /*Wijzig voorraad voer */
        if ($updCorr == 'af' && $voorraad == 0) {
            $fout = "De voorraad is reeds 0.";
        } elseif ($updCorr == 'af' && $voorraad < $updAantal) {
            $fout = "De correctie kan niet meer zijn dan " . $voorraad . " " . $eenh . ".";
        } elseif ($updCorr == 'bij' && (!isset($afboek) || $afboek <= 0)) {
            $fout = "Er is niets (meer) afgeboekt. Bijboeken is niet mogelijk.";
        } elseif ($updCorr == 'bij' && $afboek < $updAantal) {
            $fout = "Er is maximaal " . $afboek . " " . $eenh . " bij te boeken.";
        } else {
            $wijzig_voorraad = $voeding_gateway->wijzig_voorraad($tabel, $recId, $updCorrat);
            /*echo $wijzig_voorraad.'<br>';*/
        }
        unset($updAantal);
    }
}
