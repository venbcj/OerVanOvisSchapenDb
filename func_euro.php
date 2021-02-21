<!-- 15-8-2015 gemaakt 
	11-7-2020 € gewijzigd in &euro;

Toegepast in :
- Liquiditeit.php 
- Saldoberekening.php 
-->
<?php

function euro_format($getal) {
if($getal == round($getal)) { $euro= "&euro; ".number_format($getal, 0, ',', '.') . ',00'; } // bron : https://www.phphulp.nl/php/forum/topic/getallen-punt-komma-streepje/95788/1/
	else { $euro = "&euro; ".number_format($getal, 2, ',', '.'); }

	return $euro; }

?>