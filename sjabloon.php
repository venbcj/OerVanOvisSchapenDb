<!--Dit is commentaar-->

<?php session_start(); ?>  
<!DOCTYPE html>
<html>
<head>
<title>Sjabloon</title>
</head>
<body>

<?php
$titel = 'Sjabloon';
$file = "sjabloon.php";
include "login.php"; ?>

		<TD valign = 'top'>
<?php
if (Auth::is_logged_in()) { ?>
	
<form action = "Systeem.php" method = "post" >

</form>

</TD>
<?php
include "menuBeheer.php"; } ?>
</tr>

</table>

</body>
</html>
