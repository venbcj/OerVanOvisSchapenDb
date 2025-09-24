<?php 

require_once("autoload.php");

$versie = '20-12-2020'; /* Pagina gemaakt */
$versie = '31-12-2024'; /* <TD width = 960 height = 400 align = "center" > gewijzigd naar <TD align = "center"> 31-12-24 include login voor include header gezet */

Session::start();
 ?>
<!DOCTYPE html>
<html>
<head>
<title>Menu</title>
</head>
<body>

<?php
$titel = 'Readeralerts';
$file = "Alerts.php";
include "login.php"; ?>

        <TD align = "center">
<?php 
if (Auth::is_logged_in()) { 
?>
<img src= "OER_van_OVIS.jpg" width= 650 height= 240 valign = "center"/>
</td>
<?php
include "menuAlerts.php"; } ?>
</tr>

</table>

</body>
</html>
