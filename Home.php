<?php /* 19-2-2015 : login toegevoegd */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '11-5-2020'; /* verwijzing naar demo site gewijzigd. 23-5 : jpg aangepast */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 align = "center"> gewijzigd naar <TD align = "center"> 31-12-24 Include "login.php"; voor Include "header.php" gezet */

session_start();
ob_start('ob_gzhandler'); ?>

<!DOCTYPE html>
<html>
<head>
<title>Home</title>
</head>
<body>

<?php
$titel = 'Home';
$file = "Home.php";
Include "login.php"; ?>

			<TD align = "center" width = "1600">
<?php
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {

Include "responscheck.php"; ?>
<table>
<?php $host = $_SERVER['HTTP_HOST'];
if($host == 'demonstr.......nl' ) { ?>
<tr align = center>
 <td>
	<a href=' <?php echo $url; ?>Instructieboekje.pdf' target="_blank" style = "color : blue"> Instructieboekje </a>
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
Include "menu1.php"; 
} ?>

</TR>
</tbody>
</table>

</body>
</html>
<?php ob_end_flush(); ?>