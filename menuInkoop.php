<?php
/*
 <!-- 14-11-2015 naamwijziging van Medicijnen naar Medicijnenbestand en Voersoorten naar Voerbestand
12-12-2015 :  versie toegveoged
29-8-2021 : msg.php gewijzigd naar javascriptsAfhandeling.js.php -->
 */

$tech_color = 'grey';
if ($modtech != 0) {
    $tech_color = 'blue';
}

include "javascriptsAfhandeling.js.php";
$menu_items = [
    (object)['caption' => 'Home', 'href' => 'Home.php', 'class' => 'blue'],
    '',
    (object)['caption' => 'Medicijnenbestand', 'href' => 'Medicijnen.php', 'class' => $tech_color],
    (object)['caption' => 'Voerbestand', 'href' => 'Voer.php', 'class' => $tech_color],
    (object)['caption' => 'Inkopen', 'href' => 'Inkopen.php', 'class' => $tech_color],
    (object)['caption' => 'Voorraad', 'href' => 'Voorraad.php', 'class' => $tech_color],
    '',
    '',
    '',
    '',
    '',
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
