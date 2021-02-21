<!--Dit is commentaar-->

<?php session_start(); ?>  
<html>
<head>
<title>Sjabloon</title>
</head>
<body>

<center>
<?php
$titel = 'Sjabloon';
$subtitel = '';
Include "header.php";?>
<TD width = 960 height = 400 valign = "top" >
<?php
$file = "sjabloon.php";
Include "login.php";
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {
?>	
<form action = "Systeem.php" method = "post" >

</form>

</TD>
<?php
Include "menuBeheer.php"; } ?>
</tr>

</table>
</center>



</body>
</html>
