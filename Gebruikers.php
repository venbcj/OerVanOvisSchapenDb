<?php

require_once("autoload.php");


$versie = '9-6-2018'; /* Gemaakt 
ALTER TABLE `tblleden` ADD `roep` VARCHAR(25) NULL DEFAULT NULL AFTER `passw`, ADD `voegsel` VARCHAR(10) NULL DEFAULT NULL AFTER `roep`, ADD `naam` VARCHAR(25) NULL DEFAULT NULL AFTER `voegsel`, ADD INDEX (`roep`, `voegsel`, `naam`) ;
*/
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '27-10-2023'; /* Laatste inlog tijdstip toegevoegd */
$versie = '29-10-2023'; /* Wachtwoord resetten mogelijk gemaakt */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = "top"> 31-12-24 include "login.php"; voor include "header.tpl.php" gezet */

 Session::start();
 ?>
<!DOCTYPE html>
<html>
<head>
<title>Beheer</title>
</head>
<body>

<?php
if (isset ($_POST['knpNieuw'])) {
    include "url.php";
    Url::redirect('Newuser.php');
}
    
$titel = 'Gebruikers';
$file = "Gebruikers.php";
include "login.php";
?>
        <TD valign = "top">
<?php
if (Auth::is_logged_in()) {
    if($modtech ==1) { 
    if(isset($_POST['knpNieuw'])) {
        $form = "Newuser.php";
        header("Location: ".$url."Newuser.php");
    } else {
        $form = "Gebruikers.php";
    }
?>

<form action= <?php echo $form; ?> method="post">
<table border = 0 >
<tr>
 <td colspan = 10 style ="font-size:11px;"> <b style ="font-size:20px;">Gebruikers</b><br/> <!--tbv medicijnen--> <br></td>
 <td><input type="submit" name = "knpNieuw" value = "nieuwe gebruiker" > </td>
</tr>

<tr align = center style ="font-size:14px;">
  
 <td><b><br>Id</b><hr></td>
 <td><b><br>Alias</b><hr></td>
 <td><b><br>Inlognaam</b><hr></td>
 <td><b><br>Gebruiker</b><hr></td>
 <td><b><br>Ubn</b><hr></td>
<!-- <td>Relatienr RVO</td>
 <td>Gebruikersnaam RVO</td>
 <td>Wachtwoord RVO</td> -->
 <td><b><br>Telefoonnr</b><hr></td>
 <td><b><br>E-mail</b><hr></td>
 <td><b><br>Melden</b><hr></td>
 <td><b><br>Technisch</b><hr></td>
 <td><b><br>Financieel</b><hr></td>
 <td><b><br>Administrator</b><hr></td>
 <td><b><br>Laatst ingelogd</b><hr></td>
 <td><b>Reset<br>wachtwoord</b><hr></td>
 
</tr>

<?php
// START LOOP
$loop = mysqli_query($db,"
SELECT l.lidId, l.alias, l.login, l.roep, l.voegsel, l.naam, u.ubn, l.tel, l.mail, l.meld, l.tech, l.fin, l.beheer, date_format(laatste_inlog, '%d-%m-%Y %H:%i:%s') lst_i
FROM tblLeden l
 join tblUbn u on (l.lidId = u.lidId)
ORDER BY l.lidId
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
        $lstInlog = $row['lst_i'];
  
if (isset ($_POST['knpResetww_'.$lid])) {

$wwnew = md5($login.'zfO3puW?Wod/UT<-|=)1VT]+{hgABEK(Yh^!Wv;5{ja{P~wX4t');
    
$updateWW = "UPDATE tblLeden SET passw = '".mysqli_real_escape_string($db,$wwnew)."' 
                         WHERE lidId = '".mysqli_real_escape_string($db,$lid)."' ";
             mysqli_query($db, $updateWW) or die (mysqli_error($db));
$goed = 'Het wachtwoord is gelijk gemaakt aan de inlognaam.';
} ?>

<tr style ="font-size:14px;" height = 25>
 <td> <?php echo $lid; ?> </td>
     <?php Session::set("DT1", NULL); ?>
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
 <td align = center style ="font-size:12px;" > <?php echo $lstInlog; ?> </td>
 <td align="center"><input type="submit" name = <?php echo "knpResetww_".$lid; ?> value = "Reset" style = "font-size:10px;" > </td>
</tr>
        
<?php } ?>
</form>
</table>
    </TD>
<?php } else { ?> <img src='eenheden_php.jpg'  width='970' height='550'/> <?php }
include "menuBeheer.php"; } ?>

</tr>
</table>

</body>
</html>
