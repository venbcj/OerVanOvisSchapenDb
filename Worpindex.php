<?php 

require_once("autoload.php");

require_once('url_functions.php');

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
include "header.tpl.php"; ?>
	<TD width = 960 height = 400 valign = "top" align = "center">
<?php
$file = "Worpindex.php";
include "login.php"; 
if (is_logged_in()) { ?>

Deze pagina is nog in ontwikkeling

</TD>
<?php
include "menuRapport.php"; } ?>

</tr>
</table>
</center>

</body>
</html>
