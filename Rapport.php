<?php 
$versie = '21-2-2015'; /* login toegevoegd */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '23-5-2020'; /* jpg aangepast */
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
$file = "Rapport.php";
Include "login.php";
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { ?>

<img src="OER_van_OVIS.jpg" width= 650 height= 240 valign = "center"/>
</td>
<?php
Include "menuRapport.php"; } ?>

</tr>

</table>
</center>

</body>
</html>
