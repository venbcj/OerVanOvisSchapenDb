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

Session::start();


// Nu nog een layout die op de juiste plek een yield() naar deze inhoud doet --BCB
?>
<!DOCTYPE html>
<html>
<head>
<title>Registratie</title>
</head>
<body>

<?php
$titel = 'Melden RVO';
// TODO: (BV) subtitel wordt nergens afgedrukt. Maken? Verwijderen?
$subtitel = 'Maximaal 60 per melding';
$file = "Melden.php";
include "login.php";
if (Auth::is_logged_in()) {
    $viewdata = ['authorized' => false];
    if ($modmeld == 1) {
        $viewdata['authorized'] = true;
        // TODO: dit nader uitzoeken. Doet geen uitvoer, maar zou wel $fout kunnen vullen. Dat heeft dan effect via menuMelden...
        include "responscheck.php";
        // Controleren of inloggevens bestaan
        $lid_gateway = new LidGateway($db);
        if ($lid_gateway->hasCompleteRvo($lidId)) {
            $viewdata['links'] = Menu::melden($db, $lidId);
        }
    }
    // de melden/page-template bevat een omliggende td, die waarschijnlijk naar de layout kan verhuizen
    View::render('melden/page', $viewdata);
    include "menuMelden.php";
}
?>
</tr>
</table>
</body>
</html>
