<?php 

require_once("autoload.php");

$versie = '11-11-2014'; /*header("Location: http://localhost:8080/schapendb/Hok.php");   toegevoegd. Dit ververst de pagina zodat een wijziging op het eerste record direct zichtbaar is*/
$versie = '8-3-2015'; /*Login toegevoegd */
$versie = '18-11-2015'; /* hok verandert in verblijf*/
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '30-12-2023'; /* Veld scan (tblStal) weggehaald en daarmee ook de knop Opslaan en het bestand save_vader.php. Ook sql beveiligd met quotes */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = 'top'> 31-12-24 include login voor include header gezet */

Session::start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Beheer</title>
</head>
<body>

<?php
$titel = 'Dekrammen';
$file = "Vader.php";
include "login.php"; ?>
    <TD valign="top">
<?php
if (Auth::is_logged_in()) {
    if ($modtech == 1) {
        $schaap_gateway = new SchaapGateway($db);
        $pdf = $schaap_gateway->zoek_stalid($lidId);
        $vaders = $schaap_gateway->zoek_vaders($lidId, $Karwerk);
        View::render('vader/list', [
            'pdf' => $pdf,
            'records' => $vaders,
        ]);
?>
    </TD>
<?php
    }
include "menuBeheer.php";
} ?>
</body>
</html>
