<?php
/* 25-11-206 : versie weergave toegevoegd
29-8-2021 : msg.php gewijzigd naar javascriptsAfhandeling.tpl.php
07-10-2024 Groeiresultaten per weging toegevoegd */

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

<?php echo View::link_to('Stallijst', 'Stallijst.php', ['class' => 'blue']); ?>
<hr class="grey">

<?php echo View::link_to('Afleverlijst', 'ZoekAfldm.php', ['class' => 'blue']); ?>
<hr class="grey">

<?php echo View::link_to('Maandoverz. fokkerij', 'Mndoverz_fok.php', ['color' => $tech_color]); ?>
<hr class="grey">

<?php echo View::link_to('Maandoverz. vleeslam.', 'Mndoverz_vlees.php', ['color' => $tech_color]); ?>
<hr class="grey">

<?php echo View::link_to('Medicijn rapportage', 'Med_rapportage.php', ['color' => $tech_color]); ?>
<hr class="grey">

<?php echo View::link_to('Voer rapportage', 'Voer_rapportage.php', ['color' => $tech_color]); ?>
<hr class="grey">

<?php echo View::link_to('Ooi rapporten', 'Rapport1.php', ['color' => $tech_color]); ?>
<hr class="grey">

<?php echo View::link_to('Maandtotalen', 'MaandTotalen.php', ['color' => $tech_color]); ?>
<hr class="grey">

<?php echo View::link_to('Groeiresultaten per schaap', 'GroeiresultaatSchaap.php', ['color' => $tech_color]); ?>
<hr class="grey">

<?php echo View::link_to('Groeiresultaten per weging', 'GroeiresultaatWeging.php', ['color' => $tech_color]); ?>
<hr class="grey">

<?php echo View::link_to('Resultaten', 'ResultHok.php', ['color' => $tech_color]); ?>
<hr class="grey">

<br/>
<hr class="grey">

<br/>
<hr class="grey">

<?php include "versie.tpl.php"; ?>
</td>
