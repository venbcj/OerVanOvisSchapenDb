<?php
/*
18-11-2015 : hok verandert in verblijf
12-12-2015 :  versie toegveoged
1-6-2020 : Uitval en redenen gewijzigd naar Redenen en momenten
12-02-2021 : Systeemgegevens gewijzigd naar Instellingen
29-8-2021 : msg.php gewijzigd naar javascriptsAfhandeling.js.php
22-10-2023 : Readerversie toegevoegd
 */

include "javascriptsAfhandeling.js.php";

$tech_color = 'grey';
if ($modtech == 1) {
    $tech_color = 'blue';
}

// TODO: #0004144 moet deze logica ook zo werken in menu1?
$reader_color = 'grey';
if ($reader == 'Agrident') {
    $reader_color = 'red';
    if (isset($actuele_versie)) {
        $reader_color = 'blue';
    }
}

$menu_items = [
    (object)['caption' => 'Home', 'href' => 'Home.php', 'class' => 'blue'],
    '',
    (object)['caption' => 'Verblijven', 'href' => 'Hok.php', 'class' => $tech_color],
    (object)['caption' => 'Rassen', 'href' => 'Ras.php', 'class' => 'blue'],
    (object)['caption' => 'Redenen en momenten', 'href' => 'Uitval.php', 'class' => 'blue'],
    (object)['caption' => 'Combi redenen', 'href' => 'Combireden.php', 'class' => 'blue'],
    (object)['caption' => 'Dekrammen', 'href' => 'Vader.php', 'class' => 'blue'],
    '',
    (object)['caption' => 'Eenheden', 'href' => 'Eenheden.php', 'class' => $tech_color],
    (object)['caption' => 'Relaties', 'href' => 'Relaties.php', 'class' => 'blue'],
    (object)['caption' => 'Readerversies', 'href' => 'Readerversies.php', 'class' => $reader_color],
    (object)['caption' => 'Gebruikers', 'href' => 'Gebruikers.php', 'class' => 'blue',
    'if' => $modbeheer],
    (object)['caption' => 'Instellingen', 'href' => 'Systeem.php', 'class' => 'blue'],

];
$menu_items = array_filter($menu_items, function ($item) {
    return !isset($item->if) || $item->if;
});
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
