<?php

require_once("autoload.php");

/* 6-11-2014 gemaakt
20-2-2015 : login toegevoegd
23-11-2015 : </form> toegvoegd */
$versie = "22-1-2017"; /* Foto toegevoegd voor gebruikers die module melden niet hebben */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '4-7-2020'; /* Omnummering toegevoegd */
$versie = '20-12-2020'; /* Menu gewijzigd */
$versie = '31-12-2023'; /* sql beveiligd met quotes */
$versie = '19-01-2024'; /* Functie aantal_melden() verplaatst naar basisfuncties.php en hernoemd */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = 'top'> 31-12-24 include login voor include header gezet */
$versie = '10-08-2025'; /* veld ubn uit tblLeden verwijderd */

session_start();

// voor verder splitsen van berekening en uitvoer
// moet eerst login.php onderverdeeld worden. Zie opmerking in login --BCB

require_once("melden_functions.php");

?>
<!DOCTYPE html>
<html>
<head>
<title>Registratie</title>
</head>
<body>

<?php
$titel = 'Melden RVO';
$subtitel = 'Maximaal 60 per melding';
$file = "Melden.php";
include "login.php";

if (Auth::is_logged_in()) {
    if ($modmeld == 1) {
        include "responscheck.php";
        // Controleren of inloggevens bestaan
        $lid_gateway = new LidGateway($db);
        if ($lid_gateway->hasCompleteRvo($lidId)) {
            [$target, $caption, $remark] = melden_menu($db, $lidId);
        } else {
            $onvolledig = 'variabele bestaat';
        }
?>
<TD valign = 'top'>    
<!-- <form action="Melden.php" method = "post"> -->

<br><br>
<h2 align="center" style="color:blue">Hier kun je meldingen bij RVO indienen.</h2>
<table border = 0 align="center">
<tr  height = 40><td></td></tr>
<?php
        if (isset($onvolledig)) {
?>
    <tr><td>Melden is niet mogelijk. Inloggevens RVO zijn onvolledig. Zie systeemgegevens.</td></tr>
<?php
        } else {
?>
<?php foreach ($target as $index => $href) { ?>
<tr>
<td>
<?php echo View::link_to($caption[$index], $href, ['class' => 'blue']);
?>
</td>
<td style = "font-size : 12px;">
<?php echo $remark[$index]; ?>
</td>
</tr>
<?php } ?>

<?php
        } ?>
</table>
<br><br><br>
    </TD>
<?php
    } else {
?>
    <img src='Melden_php.jpg'  width='970' height='550'/>
<?php
    }
    include "menuMelden.php";
}
?>
</tr>
</table>
<!-- </form> -->

</body>
</html>
