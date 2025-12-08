<?php
/**
<!-- 15-8-2015 gemaakt 
    11-7-2020 € gewijzigd in &euro;

Toegepast in :
- Liquiditeit.php 
- Saldoberekening.php 
-->
**/
function euro_format($getal) {
    if ($getal == round($getal)) {
    // bron : https://www.phphulp.nl/php/forum/topic/getallen-punt-komma-streepje/95788/1/
        $euro = "&euro; ".number_format($getal, 0, ',', '.') . ',00';
    } else {
        $euro = "&euro; ".number_format($getal, 2, ',', '.');
    }
    return $euro;
}
