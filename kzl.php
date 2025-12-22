<?php

// TODO: #0004180 een functie (methode in View) maken die de hele select afdrukt.
// Dit is alleen 1 optie, dat is geen goede abstractie
$opties = array($kzlkey=>$kzlvalue);
foreach ($opties as $key => $waarde) {
    $keuze = '';
    if ((isset($kzlId) && $kzlId == $key) || (isset($_POST[$name]) && $_POST[$name] == $key)) {
        $keuze = ' selected ';
    }
    echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
}
