<?php 

require_once("autoload.php");

$versie = '21-2-2015'; /* login toegevoegd */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '26-12-2024'; /* <td width = 960 height = 400 align = "center"> gewijzigd naar <TD align = 'center'> 31-12-24 include login voor include header gezet */

 Session::start();
 ?>
<!DOCTYPE html>
<html>
<head>
<title>Menu</title>
</head>
<body>

<?php
$titel = 'Rapportages';
$file = "Rapport1.php";
include "login.php"; ?>

        <TD align = 'center'>
<?php
if (Auth::is_logged_in()) { ?>

<img src="OER_van_OVIS.jpg" width = 650 height = 240 valign = "center"/>
</td>
<?php
include "menuRapport1.php"; } ?>

</tr>

</table>

</body>
</html>
