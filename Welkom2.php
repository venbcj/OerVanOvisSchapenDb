<?php 
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '9-1-2020'; /* schapencentrum.. vervangen door Oer van Ovis */
$versie = '26-12-2024'; /* <TD width = 1160 height = 400 valign = "top"> gewijzigd naar <TD valign = 'top'> */

 session_start(); ?>  
<!DOCTYPE html>
<html>
<head>
<title>Welkom</title>
</head>
<body>

<?php
$titel = 'Welkompagina';
$file = "";
include "header.php"; ?>

				<TD valign = 'top'>
<?php 
/*if ($_SERVER['REQUEST_METHOD'] == "POST") {
  header("location: http://www.jouwsite.nl/pagina.html"); }*/
if(isset($_POST['knpCreate']))
{
	$ubn = $_POST['txtUname'];
	$pword =  $_POST['txtPassw']; //ongecodeerd wachtwoord
	//$passw =  $_POST['txtPassw'];
	$passw = md5($_POST['txtPassw'].'zfO3puW?Wod/UT<-|=)1VT]+{hgABEK(Yh^!Wv;5{ja{P~wX4t'); // wordt gebruikt bij login
	$ctr_p =  $_POST['ctrPassw'];
	$tel = $_POST['txtTel'];
	$mail = $_POST['txtMail'];
	
	/*echo "Ubn : ".$ubn."<br>";
	echo "Waw : ".$passw."<br>";
	echo "Bev : ".$ctr_p."<br>";
	echo "Tel : ".$tel."<br>";
	echo "Mail : ".$mail."<br>";*/
	

include "demo_usercreate.php";
}
	?>
<form action= "Welkom2.php" method= "post" >
<table border = 0 valign = 'top' align = "center">
<tr><td valign = top>
	<table border = 0><tr><td height = 50></td></tr>
	<tr><td>
	<!--<img src='deklijst.jpg' width='285' height='220'> -->
	</td></tr>
	<tr><td height = 150>
	</td></tr>
	<tr><td>
	<!--<img src='MaanoverFok.jpg' width='245' height='39'> -->
	</td></tr>
	</table></td>
<td valign = 'top'>
	<table border = 0 align = "center">
<tr height = 50 valign ='top' ><td colspan = 4 align = "center">
<h2>Aanmaken demo account </td></tr>
<tr><td width = 150></td><td width = 150>Gebruikersnaam (Ubn) </td><td><input type = text name = 'txtUname' value = <?php if(isset($ubn)) { echo $ubn;} ?> >
 </td><td colspan = 2 width = 550 align = 'left'>Uw ubn is de gebruikersnaam en kan later worden gewijzigd</td><td></td></tr>

<tr><td></td><td>Wachtwoord </td><td> <input type = password name = 'txtPassw' value = <?php if(isset($passw) && $passw == $ctr_p) { echo $passw;} ?> ></td>
 <td width = 150> (minimaal 6 tekens)</td><td rowspan = 6 width = 500 align = right valign = "center"><img src='meldingen.jpg' width='480' height='200' ></td></tr>

<tr><td></td><td>Bevestig wachtwoord </td><td> <input type = password name = 'ctrPassw' value = <?php if(isset($ctr_p) && $ctr_p == $passw) { echo $ctr_p;} ?> >
 </td><td> </td></tr>
<tr><td></td><td>Telefoonnummer  </td><td> <input type = text name = 'txtTel' value = <?php if(isset($tel)) { echo $tel;} ?> ></td><td> </td></tr>
<tr><td></td><td>E-mail </td><td colspan = 2 > <input type = text name = 'txtMail' size = 45 value = <?php if(isset($mail)) { echo $mail;} ?> ></td><td></td></tr>
<tr><td colspan = 2></td><td align = "center"><input type = submit name = 'knpCreate' value = "Aanmaken" >
 </td><td><a href=' <?php echo $url; ?>Welkom.php' style = "color : blue"> Terug </a></td><td></td></tr>
<tr><td colspan = 5 height = 100></td></tr>
<tr>
<td colspan = 2 valign = 'bottom'>
Contact :<br/>
<div style = "font-size : 14px"; >06-48400813</div>
info@oervanovis.nl<br/><br/></td><td colspan = 4 height = 250 align = right valign = 'bottom'><img src='maanoverfok.jpg' width='725' height='250'></td></tr></table>


<td valign = top>
	<table border = 0>
	<tr><td height = 80 align = "center" valign = 'top' >
	</td></tr>
	<tr><td height = 400 valign = "center">
	</td></tr>
	<tr><td>
	</td></tr>

	</table>

</td></tr>
</table>

<?php include "msg.php"; ?>
	</TD>

</tr>

</table>
</form>

</body>
</html>
