<?php

require_once("autoload.php");

/*
 <!-- 8-3-2015 : Login toegevoegd 
12-12-2015 : kolom 'Aantal nog toe te dienen' aangevuld met eenheid 
29-8-2020 : Voorraadcorrectie toegevoegd -->
 */
$versie = '12-12-2015';
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = 'top'> 31-12-24 include login voor include header gezet */

Session::start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Inkoop</title>
</head>
<body>

<?php
$titel = 'Voorraad';
$file = "Voorraad.php";
include "login.php";
?>
        <TD valign = 'top'>
<?php
if (Auth::is_logged_in()) {
    if($modtech == 1) {
        $artikel_gateway = new ArtikelGateway();
        // 1-8-2016 : Er is geen rekening gehouden met de inkoopeenheden bij sommatie alle inkoophoeveelheden.
        // Reden : te complex t.o.v. de kans dat eenheden veranderen. Mogelijk in de toekomst noodzakelijk
        $voer = $artikel_gateway->voer($lidId);
        $pil = $artikel_gateway->pil($lidId);
        View::render('voorraad/list', ['voer' => $voer, 'pil' => $pil]);
    } else {
        View::render('voorraad/preview');
    }
    include "menuInkoop.php";
}
?>
</TD>
</tr>
</table>
</body>
</html>
