<?php
/*
<!-- 25-11-2006 : versie weergave toegevoegd
29-8-2021 : msg.php gewijzigd naar javascriptsAfhandeling.js.php -->
 */
include "url.php";

$tech_color = 'grey';
if ($modtech != 0) {
    $tech_color = 'blue';
}

include "javascriptsAfhandeling.js.php";
$menu_items = [
    (object)['caption' => 'Home', 'href' => 'Home.php', 'class' => 'blue'],
    (object)['caption' => 'Ooikaart detail', 'href' => 'Ooikaart.php', 'class' => $tech_color],
    (object)['caption' => 'Ooikaart moeders', 'href' => 'OoikaartAll.php', 'class' => $tech_color],
    (object)['caption' => 'Meerling in periode', 'href' => 'Meerlingen5.php', 'class' => $tech_color],
    (object)['caption' => 'Meerling per geslacht', 'href' => 'Meerlingen.php', 'class' => $tech_color],
    (object)['caption' => 'Meerlingen per jaar', 'href' => 'Meerlingen2.php', 'class' => $tech_color],
    (object)['caption' => 'Meerling oplopend', 'href' => 'Meerlingen3.php', 'class' => $tech_color],
    (object)['caption' => 'Meerlingen aanwezig', 'href' => 'Meerlingen4.php', 'class' => $tech_color],
    '',
    '',
    '',
    '',
    '',
    '',
];
?>

<link rel="stylesheet" href="menu.css">
<td width='150' height='100' valign='top'>
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
