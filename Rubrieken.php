<?php

require_once("autoload.php");
$versie = '25-10-2015'; /*Gemaakt*/
$versie = '21-12-2015'; /*hoofdrubrieken gesorteerd*/
$versie = '19-10-2016';
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '12-7-2020'; /* ë uit database gewijzigd in echo htmlentities(string, ENT_COMPAT,'ISO-8859-1', true); bron https://www.php.net/htmlspecialchars via https://www.phphulp.nl/php/forum/topic/speciale-tekens-in-code-omzetten/50786/ */
$versie = '31-12-2023'; /* sql beveiligd met quotes */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD align="center" valign = 'top'> 31-12-24 include login voor include header gezet */
Session::start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Financieel</title>
</head>
<body>
<?php
$titel = 'Rubrieken';
$file = "Rubrieken.php";
include "login.php"; 
?>
        <TD align="center" valign = 'top'>
<?php
if (Auth::is_logged_in()) {
    if ($modfin == 1) {
        if (isset($_POST['knpSave_'])) {
            include "save_rubriek.php";
        }
        //*****************************
        //** RUBRIEKEN IN GEBRUIK
        //*****************************
?>
<form action="Rubrieken.php" method="post" >
<table border = 0 style="border-color: blue; border-style: solid;" > <!-- Overkoepelende tabel -->
<tr>
 <td width = 350>
    <table border = 0 style="border-color: green; border-style: solid;"  align =  "left" > <!-- Tabel met rubrieken in gebruik -->
    <tr>
     <td colspan =  3 > <b>Rubrieken in gebruik :</b> </td>
    </tr>
    <tr style =  "font-size:12px;" valign =  "bottom">
     <th width = 180>Rubriek</th>
     <th>Actief</th>
     <th>t.b.v.<br> Saldo-<br>&nbsp&nbspberekening</th>
    </tr>
<?php
        // START LOOP Hoofdrubrieken
        $rubriek_gateway = new RubriekGateway();
        // @TODO: #0004220 is er een echt verschil tussen Saldoberekening en Rubrieken, voor hoofdrubriek?
        $loopHRub = $rubriek_gateway->zoekHoofdrubriekSal($lidId);
        while ($rij = $loopHRub->fetch_assoc()) {
            $rubhId = $rij['rubhId'];
            $hrubr = $rij['rubriek'];
?>
    <tr>
     <th height = 50 valign = bottom align = 'left'> <?php echo htmlentities($hrubr, ENT_COMPAT, 'ISO-8859-1', true); ?>    <hr></th>
    </tr>
<?php
            // START LOOP Rubrieken
            $loopRub = $rubriek_gateway->zoek_rubriek_simpel($lidId, $rubhId);
            while ($row = $loopRub->fetch_assoc()) {
                $Id = "{$row['rubuId']}";
                $rubr = "{$row['rubriek']}";
                $actief = "{$row['actief']}";
?>
    <tr style = "font-size:12px;">
     <td width = 180 style = "font-size : 14px;"> <?php echo $rubr; // Rubrieknaam ?>
     </td>
     <td align = "center">
        <input type = "hidden" name = <?php echo "chkActief_$Id"; ?> size = 1 value =0 > <!-- hiddden -->
        <input type = "checkbox" name = <?php echo "chkActief_$Id"; ?> id="c1" value="1" <?php echo $row['actief'] == 1 ? 'checked' : ''; ?>         title = "Is Rubriek te gebruiken ja/nee ?">
     </td>
     <td align = "center">
        <input type = "checkbox" name = <?php echo "chkSalber_$Id"; ?> id="c1" value="1" <?php echo $row['sal'] == 1 ? 'checked' : ''; ?>         title = "te gebruiken bij saldoberekening ja/nee ?">
     </td>
    </tr>
<?php
            }
        }
?>
</tr>
</table> <!-- Einde Tabel met rubrieken in gebruik -->
<!--
*************************************
** EINDE RUBRIEKEN IN GEBRUIK
*************************************
-->
</td>
<td width = 200 align = "center" valign = 'top'> <!-- Ruimte tussen de twee tabellen-->
    <input type = "submit" name="knpSave_" value = "Opslaan" >
</td>
<?php
        //*****************************
        //** RUBRIEKEN NIET IN GEBRUIK
        //*****************************
        // Aantal rubrieken niet in gebruik
        $niet_actief = $rubriek_gateway->aantal_inactief($lidId);
        if ($niet_actief > 0) {
?>
 <td width = 350 align = 'right' valign = 'top'> <!--betreft rechter cel in de overkoepelende tabel -->
    <table border = 0 style="border-color: red; border-style: solid;"> <!-- Tabel met rubrieken niet in gebruik -->
    <tr>
     <td colspan =  4 valign = "bottom">
     <b>Rubrieken niet in gebruik:</b>
     </td>
    </tr>
    <tr style =  "font-size:12px;" valign =  "bottom">
     <th align = "left" >Rubriek</th>
     <th>Actief</th>
     <th>t.b.v.<br> Saldo-<br>&nbsp&nbspberekening</th>
    </tr>
<?php
            // START LOOP Hoofdrubrieken
            $loopHRub = $rubriek_gateway->inactieve_hoofdrubrieken($lidId);
            while ($rij = $loopHRub->fetch_assoc()) {
                $rubhId = $rij['rubhId'];
                $hrubr = $rij['rubriek'];
?>
    <tr><th height = 50 valign = bottom align = 'left'> <?php echo htmlentities($hrubr, ENT_COMPAT, 'ISO-8859-1', true); ?>    <hr></th>
    </tr>
<?php
                // START LOOP Rubrieken
                $loopRub = $rubriek_gateway->inactieve_rubrieken($lidId, $rubhId);
                while ($row = $loopRub->fetch_assoc()) {
                    $Id = "{$row['rubuId']}";
                    $rubr = "{$row['rubriek']}";
                    $actief = "{$row['actief']}";
?>
    <tr style = "font-size:12px;">
     <td style = "font-size : 14px;"> <?php echo $rubr; // Rubrieknaam ?>
     </td>
     <td align = "center">
        <input type = "hidden" name = <?php echo "chkActief_$Id"; ?> size = 1 value =0 > <!-- hiddden -->
        <input type = "checkbox" name = <?php echo "chkActief_$Id"; ?> id="c1" value="1" <?php echo $row['actief'] == 1 ? 'checked' : ''; ?> >
     </td>
     <td align = "center">
        <input type = "checkbox" name = <?php echo "chkSalber_$Id"; ?> id="c1" value="1" <?php echo $row['sal'] == 1 ? 'checked' : ''; ?>         title = "te gebruiken bij saldoberekening ja/nee ?">
     </td>
<?php
                }
            }
?>
    </tr>
</td> <!-- EInde betreft rechter cel in de overkoepelende tabel -->
</tr>
</table> <!-- Einde Tabel met rubrieken niet in gebruik -->
        <?php    } 
?>
</td></tr>
</table> <!-- Einde Overkoepelende tabel -->
<!--
*************************************
** EINDE RUBRIEKEN NIET IN GEBRUIK
************************************* -->
</form>
    </TD>
<?php } else {
?>
    <img src="rubrieken_php.jpg"  width='970' height='550'>
<?php
            }
            include "menuFinance.php";
} 
?>
</body>
</html>
