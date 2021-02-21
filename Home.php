<?php /* 19-2-2015 : login toegevoegd */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '11-5-2020'; /* verwijzing naar demo site gewijzigd. 23-5 : jpg aangepast */
session_start(); ?>


<html>
<head>
<title>Home</title>
</head>
<body>

<center>

<?php
$titel = 'OER van OVIS';
$subtitel = 'Optimalisering En Rendementverbetering van het Schaap';
Include "header.php";?>

<TD width = 960 height = 400 align = "center">

<?php 
$file = "Home.php";
Include "login.php";
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {

Include "responscheck.php"; ?>
<table>
<?php $host = $_SERVER['HTTP_HOST'];
if($host == 'demo.oervanovis.nl' ) { ?>
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

</body>
</html>
