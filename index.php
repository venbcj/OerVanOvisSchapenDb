<?php

require_once("autoload.php");

/* 29-8-2018 titel.php verwijderd. Zit in header.php samen met Style.css
23-5-2020 logo aangepast
 */
$versie = '26-12-2024';
/* <TD width = 1390 height = 400 align = "center"> gewijzigd naar <TD align = "center">  */

$titel = '';
Auth::logout();

echo <<<HTML
<!DOCTYPE html>
<html>
<head>
<title>Home</title>
</head>
<body>

<!--
<table><tr align = center style = "font-size : 30px ";><td>OER van OVIS</td> </tr>
<tr align = center><td><sup style = "font-size : 18px "; >Optimalisering En Rendementverbetering van het Schaap</sup></td></tr></table>
-->
HTML;

include "header_logout.tpl.php";

echo <<<HTML
<TD align = "center">
    <br>
    <br>
    <br>
    <br>
    <!-- Mijn eerste commentaar 25-09-25 -->
    <br>
<img src="OER_van_OVIS.jpg" width=650 height=240 valign = "center"/>
    <br>
    <br>
    <br>
HTML;

// TODO: login-routing. Deze $file zorgt ervoor dat het inlogformulier naar Home wordt gepost.
// Het is schoner om het inloggen apart af te handelen, en dan hier te redirecten naar Home.
$file = "Home.php";
include "login.php";

echo <<<HTML
</TD>
</body>
</html>
HTML;
