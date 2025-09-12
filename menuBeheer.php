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

// TODO: moet deze logica ook zo werken in menu1?
$reader_color = 'grey';
if ($reader == 'Agrident') {
    $reader_color = 'red';
    if (isset($actuele_versie)) {
        $reader_color = 'blue';
    }
}
?>

<link rel="stylesheet" href="menu.css">
<td width = '150' height = '100' valign='top'>
Menu : </br>
<hr class="blue">

<?php echo View::link_to('Home', 'Home.php', ['class' => 'blue']); ?>
<hr class="grey">

<br/>
<hr class="grey">

<?php echo View::link_to('Verblijven', 'Hok.php', ['class' => $tech_color]); ?>
<hr class="grey">

<?php echo View::link_to('Rassen', 'Ras.php', ['class' => 'blue']); ?>
<hr class="grey">

<?php echo View::link_to('Redenen en momenten', 'Uitval.php', ['class' => 'blue']); ?>
<hr class="grey">

<?php echo View::link_to('Combi redenen', 'Combireden.php', ['class' => 'blue']); ?>
<hr class="grey">

<?php echo View::link_to('Dekrammen', 'Vader.php', ['class' => 'blue']); ?>
<hr class="grey">

<br/>
<hr class="grey">

<?php echo View::link_to('Eenheden', 'Eenheden.php', ['class' => $tech_color]); ?>
<hr class="grey">

<?php echo View::link_to('Relaties', 'Relaties.php', ['class' => 'blue']); ?>
<hr class="grey">

<?php echo View::link_to('Readerversies', 'Readerversies.php', ['class' => $reader_color]); ?>
<hr class="grey">

<?php if ($modbeheer == 1) { ?>
<?php echo View::link_to('Gebruikers', 'Gebruikers.php', ['class' => 'blue']); ?>
<?php } ?>
<hr class="grey">

<?php echo View::link_to('Instellingen', 'Systeem.php', ['class' => 'blue']); ?>
<hr class="grey">

<?php include "versie.tpl.php"; ?>
</td>
