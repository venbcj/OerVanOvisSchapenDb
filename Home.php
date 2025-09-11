<?php

require_once("autoload.php");

/* 19-2-2015 : login toegevoegd */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '11-5-2020'; /* verwijzing naar demo site gewijzigd. 23-5 : jpg aangepast */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 align = "center"> gewijzigd naar <TD align = "center"> 31-12-24 include login voor include header gezet */

session_start();
ob_start('ob_gzhandler');
?>

<!DOCTYPE html>
<html>
<head>
<title>Home</title>
</head>
<body>

<?php
$titel = 'Home';
$file = "Home.php";
include "login.php";
?>
            <TD align = "center" width = "1600">
<?php
if (is_logged_in()) {
    include "responscheck.php";
?>
<table>
<?php $host = $_SERVER['HTTP_HOST'];
if ($host == 'demonstr.......nl') { ?>
<tr align = center>
 <td>
    <?php echo link_to('Instructieboekje', 'Instructieboekje.pdf', ['class' => 'blue', 'target' => "_blank"]); ?>
 </td>
</tr>
<?php } ?>
<tr>
 <td>
    <img src="OER_van_OVIS.jpg" width= 650 height= 240 valign = "center"/>
 </td>
</tr>
</table>

</TD>

<?php
include "menu1.php";
} ?>

</TR>
</tbody>
</table>

</body>
</html>
<?php ob_end_flush(); ?>
