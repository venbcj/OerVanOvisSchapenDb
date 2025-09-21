<?php
/*
 <!-- 6-12-2015 :  versie toegveoged 
28-12-2016 : linken grijs bij module niet in gebruik 
29-12-2016 : Archief gewijzigd in Betaalde 
29-08-2021: msg.php gewijzigd naar javascriptsAfhandeling.js.php 
07-01-2025: De omschrijving Invulformulier gewijzigd naar Inboeken -->
 */

$fin_color = 'grey';
if ($modfin == 1) {
    $fin_color = 'blue';
}

include "javascriptsAfhandeling.js.php";
$menu_items = [
    (object)['caption' => 'Home', 'href' => 'Home.php', 'class' => 'blue'],
    (object)['caption' => 'Inboeken', 'href' => 'Kostenopgaaf.php', 'class' => $tech_color],
    (object)['caption' => 'Deklijst', 'href' => 'Deklijst.php', 'class' => $tech_color],
    (object)['caption' => 'Liquiditeit', 'href' => 'Liquiditeit.php', 'class' => $tech_color],
    (object)['caption' => 'Saldoberekening', 'href' => 'Saldoberekening.php', 'class' => $tech_color],
    '',
    '',
    (object)['caption' => 'Rubrieken', 'href' => 'Rubrieken.php', 'class' => $tech_color],
    (object)['caption' => 'Componenten', 'href' => 'Componenten.php', 'class' => $tech_color],
    (object)['caption' => 'Betaalde posten', 'href' => 'Kostenoverzicht.php', 'class' => $tech_color],
    '',
    '',
    '',
];
?>

<link rel="stylesheet" href="menu.css">
<td width = '150' height = '100' valign='top'>
Menu : </br>
<hr class="blue">

<?php
foreach ($menu_items as $item) :
    if ($item) {
        echo View::link_to($item->caption, $item->href, ['class' => $item->class]);
    } else {
        echo '<br/>';
    }
echo PHP_EOL.'<hr class="grey">'.PHP_EOL;
endforeach; ?>

<?php include "versie.tpl.php"; ?>
</td>
