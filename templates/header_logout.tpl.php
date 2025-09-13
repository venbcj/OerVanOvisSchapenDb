<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" type="text/css" href="menu.css">

<?php
include "back_to_top.js.php";
?>

<div id = "rechts_uitlijnen" class = 'header_breed'>
    <section style="text-align : center">
<?php # TODO: waarom de spaties? # ?>
        <?php echo $titel . str_repeat('&nbsp;', 28); ?>
    </section>
    <img src='OER_van_OVIS.jpg' />
</div>

<ul class="header_smal topnav" id = <?php echo Url::getTagId(); ?> >
<?php if (Auth::is_logged_in()) {
include "topnav.tpl.php";
} ?>
    <li id = "rechts_uitlijnen">
<?php if (Auth::is_logged_in()) { ?>
<?php } else { ?>
        <?php echo View::link_to('Inloggen', 'index.php', ['class' => 'black']); ?></li>
<?php } ?>
</ul>

<?php # TODO: (BV) dit bestaat toch niet? # ?>
<script src="test2_script_header.js"></script>

<?php
# html-elementen openen in de ene template, en sluiten in een andere, dat voelt breekbaar
# TODO werken met yield-constructies --BCB

?>
<table id ="table1" align="center">
<tbody>
<tr height = 90> </tr>
<TR>
