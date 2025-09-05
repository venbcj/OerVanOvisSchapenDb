<?php 
$versie = '8-3-2015'; /*Login toegevoegd*/
$versie = '14-11-2015'; /*naamwijziging van Menu Inkoop naar Menu Voorraadbeheer*/
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '23-5-2020'; /* jpg aangepast */
$versie = '26-12-2024'; /* <td width = 960 height = 400 align = "center"> gewijzigd naar <TD align = "center"> 31-12-24 include login voor include header gezet */

session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Menu</title>
</head>
<body>

<?php 
$titel = 'Menu Voorraadbeheer';
$file = "Inkoop.php";
include "login.php"; ?>

			<TD align = "center">
<?php
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { ?>

<img src="OER_van_OVIS.jpg" width= 650 height= 240 valign = "center"/>
</td>
<?php 
include "menuInkoop.php"; } ?>

</tr>

</table>

</body>
</html>
