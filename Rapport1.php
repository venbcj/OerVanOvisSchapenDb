<?php 
$versie = '21-2-2015'; /* login toegevoegd */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
session_start(); ?>
<html>
<head>
<title>Menu</title>
</head>
<body>

<center>
<?php
$titel = 'Rapportages';
$subtitel = '';
Include "header.php"; ?>

<td width = 960 height = 400 align = "center">
<?php 
$file = "Rapport1.php";
Include "login.php";
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { ?>

<img src="OER_van_OVIS.jpg" width = 650 height = 240 valign = "center"/>
</td>
<?php
Include "menuRapport1.php"; } ?>

</tr>

</table>
</center>

</body>
</html>
