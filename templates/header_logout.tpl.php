<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" type="text/css" href="menu.css">

<?php
include "back_to_top.js.php";
?>

<div id = "rechts_uitlijnen" class = 'header_breed'><section> </section><img src='OER_van_OVIS.jpg' /></div>

<ul class="header_smal" id = <?php echo Url::getTagId(); ?> >
    <li id = "rechts_uitlijnen"><?php echo View::link_to('Inloggen', 'index.php', ['class' => 'black']); ?></li>
</ul>

<?php #TODO: (BV) dit bestaat toch niet? # ?>
<script src="test2_script_header.js"></script>

<?php
# html-elementen openen in de ene template, en sluiten in een andere, dat voelt breekbaar
# TODO werken met yield-constructies --BCB

?>
<table id ="table1">
<tbody>
<tr height = 90> </tr>
<TR>
