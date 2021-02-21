<?php 
$versie = '11-3-2015'; /* Login toegevoegd*/
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
session_start(); ?>
<html>
<head>
<title>Rapport</title>
</head>
<body>

<center>
<?php
$titel = 'Worpindex';
$subtitel = '';
Include "header.php"; ?>
	<TD width = 960 height = 400 valign = "top" align = "center">
<?php
$file = "Worpindex.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { ?>

Deze pagina is nog in ontwikkeling

</TD>
<?php
Include "menuRapport.php"; } ?>

</tr>
</table>
</center>

</body>
</html>
