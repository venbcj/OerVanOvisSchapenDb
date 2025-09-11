<?php
/*
 <!-- 20-12-2020 : Pagina gemaakt 
29-8-2021 : msg.php gewijzigd naar javascriptsAfhandeling.tpl.php -->
 */
include "javascriptsAfhandeling.tpl.php";
 
$tech_color = 'grey';
if ($modtech != 0) {
    $tech_color = 'blue';
}

?>
<td width = '150' height = '100' valign='top'>
Menu : </br>
<hr class="blue">

<?php echo View::link_to('Home', 'Home.php', ['class' => 'blue']); ?>
<hr class="grey">

<br/>
<hr class="grey">

<?php echo View::link_to('Ooitjes uit meerlingen', 'OoilamSelectie.php', ['class' => $tech_color]); ?>
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
<br/>
<hr class="grey">
<br/>
<hr class="grey">
<br/>
<hr class="grey">

<?php include "versie.tpl.php"; ?>
</td>
