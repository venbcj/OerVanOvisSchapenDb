<?php 
$versie = '3-3-2015'; /* Login toegevoegd */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '23-5-2020'; /* jpg aangepast */
$versie = '31-12-2024'; /* <TD width = 960 height = 400 align = "center" > gewijzigd naar <TD align = "center"> 31-12-24 Include "login.php"; voor Include "header.php" gezet */

session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Menu</title>
</head>
<body>

<?php
$titel = 'Beheer basisgegevens';
$file = "Beheer.php";
Include "login.php"; ?>

		<TD align = "center">
<?php 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { 
?>
<img src= "OER_van_OVIS.jpg" width= 650 height= 240 valign = "center"/>
</td>
<?php
Include "menuBeheer.php"; } ?>
</tr>

</table>

</body>
</html>