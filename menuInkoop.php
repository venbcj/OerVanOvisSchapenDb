<?php
/*
 <!-- 14-11-2015 naamwijziging van Medicijnen naar Medicijnenbestand en Voersoorten naar Voerbestand
12-12-2015 :  versie toegveoged
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

<?php echo link_to('Home', 'Home.php', ['class' => 'blue']); ?>
<hr class="grey">

<br/>
<hr class="grey">

<?php echo link_to('Medicijnenbestand', 'Medicijnen.php', ['color' => $tech_color]); ?>
<hr class="grey">

<?php echo link_to('Voerbestand', 'Voer.php', ['color' => $tech_color]); ?>
<hr class="grey">

<?php echo link_to('Inkopen', 'Inkopen.php', ['color' => $tech_color]); ?>
<hr class="grey">

<?php echo link_to('Voorraad', 'Voorraad.php', ['color' => $tech_color]); ?>
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

<br/>
<hr class="grey">

<?php include "versie.tpl.php"; ?>
</td>
