<?php 
$versie = '9-6-2018'; /* Gemaakt 
ALTER TABLE `tblleden` ADD `roep` VARCHAR(25) NULL DEFAULT NULL AFTER `passw`, ADD `voegsel` VARCHAR(10) NULL DEFAULT NULL AFTER `roep`, ADD `naam` VARCHAR(25) NULL DEFAULT NULL AFTER `voegsel`, ADD INDEX (`roep`, `voegsel`, `naam`) ;
*/
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
 session_start(); ?>
<html>
<head>
<title>Beheer</title>
</head>
<body>

<center>
<?php
if (isset ($_POST['knpUpdate'])) {
	Include "url.php";
	header("Location: ".$url."Gebruikers.php"); }
$titel = 'Gebruikers';
$subtitel = '';
Include "header.php"; ?>
	<TD width = 960 height = 400 valign = "top">
<?php
$file = "Eenheden.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { if($modtech ==1) { 

if(isset($_POST['knpNieuw'])) { $form = "Newuser.php"; header("Location: ".$url."Newuser.php");; } else { $form = "Gebruikers.php"; } ?>

<form action= <?php echo $form; ?> method="post">
<table border = 0 >
<tr>
 <td colspan = 10 style ="font-size:11px;"> <b style ="font-size:20px;">Gebruikers</b><br/> <!--tbv medicijnen--> <br></td>
 <td><input type="submit" name = "knpNieuw" value = "nieuwe gebruiker" > </td>
</tr>

<tr align = center style ="font-size:14px;">
  
 <td><b>Id</b><hr></td>
 <td><b>Alias</b><hr></td>
 <td><b>Inlognaam</b><hr></td>
 <td><b>Gebruiker</b><hr></td>
 <td><b>Ubn</b><hr></td>
<!-- <td>Relatienr RVO</td>
 <td>Gebruikersnaam RVO</td>
 <td>Wachtwoord RVO</td> -->
 <td><b>Telefoonnr</b><hr></td>
 <td><b>E-mail</b><hr></td>
 <td><b>Melden</b><hr></td>
 <td><b>Technisch</b><hr></td>
 <td><b>Financieel</b><hr></td>
 <td><b>Administrator</b><hr></td>
 
</tr>

<?php
// START LOOP
$loop = mysqli_query($db,"
SELECT lidId, alias, login, roep, voegsel, naam, ubn, tel, mail, meld, tech, fin, beheer
from tblLeden
order by lidId
") or die (mysqli_error($db));

	while($row = mysqli_fetch_assoc($loop))
	{
        $lid = $row['lidId'];
        $alias = $row['alias'];
        $login = $row['login'];
        $roep = $row['roep'];
        $voeg = $row['voegsel']; if(isset($voeg)) { $voeg = ' '.$voeg.' '; } else { $voeg = ' '; }
        $naam = $row['naam']; $naam = $roep.$voeg.$naam;
        $ubn = $row['ubn'];
        $tel = $row['tel'];
        $mail = $row['mail'];
        $meld = $row['meld']; if( $meld == 1) { $meld = 'Ja'; } else { $meld = 'Nee'; }
        $tech = $row['tech']; if( $tech == 1) { $tech = 'Ja'; } else { $tech = 'Nee'; }
        $fin = $row['fin']; if( $fin == 1) { $fin = 'Ja'; } else { $fin = 'Nee'; }
        $admin = $row['beheer']; if( $admin == 1) { $admin = 'Ja'; } else { $admin = 'Nee'; }
  
?>

<tr style ="font-size:14px;">
 <td> <?php echo $lid; ?> </td>
 	<?php $_SESSION["DT1"] = NULL; ?>
 <td> <a href='<?php echo $url; ?>Gebruiker.php?pstId=<?php echo $lid; ?>' style = 'color : blue'> <?php echo $alias; ?> </a> </td>
 <td> <?php echo $login; ?> </td>
 <td> <?php echo $naam; ?> </td>
 <td> <?php echo $ubn; ?> </td>
 <td> <?php echo $tel; ?> </td>
 <td> <?php echo $mail; ?> </td>
 <td align = center style ="font-size:12px;" > <?php echo $meld; ?> </td>
 <td align = center style ="font-size:12px;" > <?php echo $tech; ?> </td>
 <td align = center style ="font-size:12px;" > <?php echo $fin; ?> </td>
 <td align = center style ="font-size:12px;" > <?php echo $admin; ?> </td>
</tr>
		
<?php } ?>
</form>
		

</table>


	</TD>
<?php } else { ?> <img src='eenheden_php.jpg'  width='970' height='550'/> <?php }
Include "menuBeheer.php"; } ?>

	</tr>
	</table>
	</center>

	</body>
	</html>
