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
?>

<link rel="stylesheet" href="menu.css">
<td width = '150' height = '100' valign='top'>
Menu : </br>
<hr class="blue">

<?php echo View::link_to('Home', 'Home.php', ['class' => 'blue']); ?>
<hr class="grey">

<?php echo View::link_to('Inboeken', 'Kostenopgaaf.php', ['class' => $fin_color]); ?>
<hr class="grey">

<?php echo View::link_to('Deklijst', 'Deklijst.php', ['class' => $fin_color]); ?>
<hr class="grey">

<?php echo View::link_to('Liquiditeit', 'Liquiditeit.php', ['class' => $fin_color]); ?>
<hr class="grey">

<?php echo View::link_to('Saldoberekening', 'Saldoberekening.php', ['class' => $fin_color]); ?>
<hr class="grey">

<br/>
<hr class="grey">

<br/>
<hr class="grey">

<?php echo View::link_to('Rubrieken', 'Rubrieken.php', ['class' => $fin_color]); ?>
<hr class="grey">

<?php echo View::link_to('Componenten', 'Componenten.php', ['class' => $fin_color]); ?>
<hr class="grey">

<?php echo View::link_to('Betaalde posten', 'Kostenoverzicht.php', ['class' => $fin_color]); ?>
<hr class="grey">

 <br/>
<hr class="grey">

 <br/>
<hr class="grey">

 <br/>
<hr class="grey">

<?php include "versie.tpl.php"; ?>
</td>
