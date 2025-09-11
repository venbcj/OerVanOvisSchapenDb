<?php
/*
<!-- 25-11-2006 : versie weergave toegevoegd
29-8-2021 : msg.php gewijzigd naar javascriptsAfhandeling.tpl.php -->
 */
include "url.php";

$tech_color = 'grey';
if ($modtech != 0) {
    $tech_color = 'blue';
}

include "javascriptsAfhandeling.tpl.php";
?>

<link rel="stylesheet" href="menu.css">
<td width = '150' height = '100' valign='top'>
Menu :
<br>
<hr class="blue">

<?php echo View::link_to('Home', 'Home.php', ['class' => 'blue']); ?>
<hr class="grey">

<?php echo View::link_to('Ooikaart detail', 'Ooikaart.php', ['color' => $tech_color]); ?>
<hr class="grey">

<?php echo View::link_to('Ooikaart moeders', 'OoikaartAll.php', ['color' => $tech_color]); ?>
<hr class="grey">

<?php echo View::link_to('Meerling in periode', 'Meerlingen5.php', ['color' => $tech_color]); ?>
<hr class="grey">

<?php echo View::link_to('Meerling per geslacht', 'Meerlingen.php', ['color' => $tech_color]); ?>
<hr class="grey">

<?php echo View::link_to('Meerlingen per jaar', 'Meerlingen2.php', ['color' => $tech_color]); ?>
<hr class="grey">

<?php echo View::link_to('Meerling oplopend', 'Meerlingen3.php', ['color' => $tech_color]); ?>
<hr class="grey">

<?php echo View::link_to('Meerlingen aanwezig', 'Meerlingen4.php', ['color' => $tech_color]); ?>

<hr class="grey">

<br/>
<hr class="grey">

<br/>
<hr class="grey">

<br/>
<hr class="grey">

<br/>
<hr class="grey">

<br/>
<hr class="grey">

<br/>
<hr class="grey">

<?php include "versie.tpl.php"; ?>
</td>
