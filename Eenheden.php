<?php

require_once("autoload.php");

/* 11-11-2014 : header("Location: http://localhost:8080/schapendb/.....php");   toegevoegd. Dit ververst de pagina zodat een wijziging op het eerste record direct zichtbaar is
8-3-2015 : Login toegevoegd */
$versie = '3-3-2017'; /* Alles m.b.t. invoer verwijderd */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD align = "center" valign = "top"> 31-12-24 include login voor include header gezet */
Session::start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Beheer</title>
</head>
<body>

<?php
if (isset($_POST['knpUpdate'])) {
    header("Location: " . Url::getWebroot() . "Eenheden.php");
}

$titel = 'Verbruikseenheden';
$file = "Eenheden.php";
include "login.php";
?>
            <TD align = "center" valign = "top">
<?php
if (Auth::is_logged_in()) {
    if ($modtech == 1) {
?>
<table border = 0 ><tr><td>
<form action= "Eenheden.php" method="post">
<table border = 0 >
<tr>
<td colspan = 5 align = center style ="font-size:11px;"> <b style ="font-size:20px;">Verbuikseenheden</b><br/> <!--tbv medicijnen--> <br></td>
</tr>
<tr align = center style ="font-size:12px;">
<td></td> 
<td><b><i>Omschrijving</i></b></td>
<td><b><i>actief</i></b></td>
</tr>
<?php
        $eenheid_gateway = new EenheidGateway();
        // START LOOP
        $loop = $eenheid_gateway->all($lidId);
        while ($record = $loop->fetch_assoc()) {
            $id = $record['enhuId'];
            if (empty($_POST['txtId'])) {
                $rowid = null;
            } else {
                $rowid = $_POST['txtId'];
            }
            if (empty($_POST['chkAct'])) {
                $updact = "NULL";
            } else {
                $updact = " '$_POST[chkAct]' ";
            }
            $query = $eenheid_gateway->get($lidId, $id);
            while ($record = $query->fetch_assoc()) {
?>
<tr>
<td width = 80></td>
<td> <?php echo $record['eenheid'];?> </td>
<form action= "Eenheden.php" method = "post">  
    <input type = "hidden" name = "txtId" value = <?php echo $id ?> >
    <td align = center>
        <input type="checkbox" name="chkAct" id="c1" value="1" <?php echo $record['actief'] == 1 ? 'checked' : ''; ?> title = "Is deze eenheid te gebruiken ja/nee ?">
    </td>
    <td><input type = "submit" name="knpUpdate" value = "Opslaan" style = "font-size:9px;"></td>
</tr>
        </form>
<?php
            }
        }
?>    
</table>
</table>
    </TD>
<?php
    } else {
?>
        <img src='eenheden_php.jpg'  width='970' height='550'/>
<?php
    }
    include "menuBeheer.php";
}
?>
</tr>
</table>
</body>
</html>
