<?php 

require_once("autoload.php");


$versie = '20-12-2020'; /* Pagina gemaakt */

Session::start();
 ?>
<html>
<head>
<title>Menu</title>
</head>
<body>

<center>
<?php
$titel = 'Meldingen RVO';
$subtitel = '';
$file = "Meldpagina.php";
include "login.php";
if (Auth::is_logged_in()) { 
?>
<td width = 960 height = 400 align = "center">
<img src= "OER_van_OVIS.jpg" width= 650 height= 240 valign = "center"/>
</td>
<?php
include "menuMelden.php"; } ?>
</tr>

</table>
</center>

</body>
</html>
