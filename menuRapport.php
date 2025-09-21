<?php
/* 25-11-206 : versie weergave toegevoegd
29-8-2021 : msg.php gewijzigd naar javascriptsAfhandeling.js.php
07-10-2024 Groeiresultaten per weging toegevoegd */

include "url.php";

$tech_color = 'grey';
if ($modtech != 0) {
    $tech_color = 'blue';
}

include "javascriptsAfhandeling.js.php";
$menu_items = [
    (object)['caption' => 'Home', 'href' => 'Home.php', 'class' => 'blue'],
    (object)['caption' => 'Stallijst', 'href' => 'Stallijst.php', 'class' => 'blue'],
    (object)['caption' => 'Afleverlijst', 'href' => 'ZoekAfldm.php', 'class' => 'blue'],
    (object)['caption' => 'Maandoverz. fokkerij', 'href' => 'Mndoverz_fok.php', 'class' => $tech_color],
    (object)['caption' => 'Maandoverz. vleeslam', 'href' => 'Mndoverz_vlees.php', 'class' => $tech_color],
    (object)['caption' => 'Medicijn rapportage', 'href' => 'Med_rapportage.php', 'class' => $tech_color],
    (object)['caption' => 'Voer rapportage', 'href' => 'Voer_rapportage.php', 'class' => $tech_color],
    (object)['caption' => 'Ooi rapporten', 'href' => 'Rapport1.php', 'class' => $tech_color],
    (object)['caption' => 'Maandtotalen', 'href' => 'MaandTotalen.php', 'class' => $tech_color],
    (object)['caption' => 'Groeiresultaten per schaap', 'href' => 'GroeiresultaatSchaap.php', 'class' => $tech_color],
    (object)['caption' => 'Groeiresultaten per weging', 'href' => 'GroeiresultaatWeging.php', 'class' => $tech_color],
    (object)['caption' => 'Resultaten', 'href' => 'ResultHok.php', 'class' => $tech_color],
    '',
    '',
];
?>

<link rel="stylesheet" href="menu.css">
<td width = '150' height = '100' valign='top'>
Menu :
<br>
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
