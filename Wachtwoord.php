<?php 
$versie = '19-3-2015'; /* bestand gemaakt*/
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */

session_start(); ?>
<html>
<head>
<title>Beheer</title>
</head>
<body>

<center>
<?php
$titel = 'Wijzigen inloggegevens';
$subtitel = '';
Include "header.php"; ?>

<TD width = 960 height = 400 valign = "center" align = "center" >
<?php 
$file = "Wachtwoord.php";
Include "login.php";
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {

$name = $_SESSION["U1"];  ?>

<form method="POST" action=" <?php echo $file; ?> ">
<p>
<table>
 <tr height = 50 ><td> Gebruikersnaam : </td>
	<td> <input type="text" name="txtUser" size="20" value = <?php echo $name; ?> ></td>
	<td> <input type="hidden" name="txtUserOld" size="20" value = <?php echo $name; ?> ></td> <!-- hiddden -->
</tr>
 <tr><td> Oud wachtwoord : </td>
	<td> <input type="password" name="txtOld" 	  size="20" value = <?php if (isset($ww)) { echo $ww;} // $ww gedeclareerd in wachtwoord ?> ></td> 
	<td> <input type="hidden" name="txtOldcntr" size="20" value = <?php echo $passw; ?> ></td> <!-- hiddden -->
 </tr>
 <tr><td> Nieuw wachtwoord : </td>
	<td> <input type="password" name="txtNew" size="20" value = <?php ; ?> ></td>
 </tr>
 <tr><td> Bevestig wachtwoord : </td>
	<td> <input type="password" name="txtBevest" size="20" value = <?php ; ?> ></td>
 </tr>
 <tr height = 100 ><td colspan = 2 align = 'center'> <input type=<?php echo $veld; ?>  value="Opslaan" name="knpChange"></td></tr>
 </table></p>
 </form>
 
 
	</TD>
<?php
Include "menuBeheer.php"; } ?>
</tr>

</table>
</center>

</body>
</html>