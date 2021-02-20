<?php /* 29-8-2018 titel.php verwijderd. Zit in header.php samen met Style.css 
23-5-2020 logo aangepast 
11-7-2020  $file = "index.php"; gewijzigd naar $file = "Home.php"; */
session_start();
// destroy the session 
session_destroy();   ?>
<html>
<head>
<title>Home</title>
</head>
<body>

<center>
<?php 
//Include "titel.php"; ?> 
<!-- <table><tr align = center style = "font-size : 30px ";><td>OER van OVIS</td> </tr>
<tr align = center><td><sup style = "font-size : 18px "; >Optimalisering En Rendementverbetering van het Schaap</sup></td></tr></table>-->
<?php 
$titel = 'OER van OVIS';
$subtitel = 'Optimalisering En Rendementverbetering van het Schaap';
Include "header.php"; 
 ?>

<TD width = 1390 height = 400 align = "center">
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>

<img src="OER_van_OVIS.jpg" width=650 height=240 valign = "center"/>
	<br>
	<br>
	<br>


 <?php
 session_start();
 $file = "Home.php";
 $menu = "menu1.php";
 include "login.php";
 ?>

</TD>

</body>
</html>