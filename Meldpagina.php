<?php 
$versie = '20-12-2020'; /* Pagina gemaakt */

session_start(); ?>
<html>
<head>
<title>Menu</title>
</head>
<body>

<center>
<?php
$titel = 'Meldingen RVO';
$subtitel = '';
Include "header.php"; ?>

<td width = 960 height = 400 align = "center">
<?php 
$file = "Meldpagina.php";
Include "login.php";
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { 
?>
<img src= "OER_van_OVIS.jpg" width= 650 height= 240 valign = "center"/>
</td>
<?php
Include "menuMelden.php"; } ?>
</tr>

</table>
</center>

</body>
</html>