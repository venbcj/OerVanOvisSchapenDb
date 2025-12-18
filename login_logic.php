<?php

include "url.php";
include "connect_db.php";
require_once("basisfuncties.php");
require_once("demo_functions.php");

// BCB: kunstgreep om uitvoer te scheiden van berekening
$output = [];

// TODO: #0004171 verder scheiden. Dit doet nu:
// - globale variabelen zetten
// - redirecten
// - uitvoer genereren
// Wat gaan we doen?
// - Vervang alle losse session_start() door
//     Auth::start();
//     if (Auth::redirected()) {
//       return;
//     }
// - Auth::start() gaat alles doen wat nu in login staat, op de uitvoer na
//
// TODO: LET OP
// Deze verbouwing kan pas beginnen wanneer hier geen globals meer worden aangemaakt.
// Een spike voor het uitbouwen van globals vind je in just_connect_db.php
/* hier zijn wat globals die in dit bestand worden aangemaakt, en elders worden gebruikt:
$actuele_versie
$Karwerk
  # komt voornamelijk (alleen maar?) in queries voor.
$last_versieId
$lidId
$message
$mod # <== dit zou een goede eerste stap zijn, ipv de vier volgende
$modbeheer
$modfin
$modmeld
$modtech
$pag
$persoonlijke_map
  # importRespons, Newuser, Readerversies
$reader
$Readersetup_bestand // voor Readerversies, dus kan ook daar opgebouwd. Oh: /wordt/ ook daar opgebouwd. Dan kan het hier gewoon weg.
$Readertaken_bestand // idem
$appfile_exists // idem
$takenfile_exists // idem
$rendac_Id
$rendac_ubn
$RPP
$w_hok
$w_werknr
 */

//$host = "localhost"; $user = "bvdvschaapovis"; $pw = "MSenWL44"; $dtb = $db_p;
if (($url == 'https://test.oervanovis.nl/' || $url == 'https://demo.oervanovis.nl/') && $dtb == 'k36098_bvdvSchapenDb') {
    $output[] = 'pasop.tpl.php';
}
if (php_uname('n') == 'basq') {
    // twee sleutels, omdat de post_* includes veel werken met getIdFromKey, en die vraagt op alle sleutels een aanwezige underscore
    if (isset($_REQUEST['ingelogd']) || isset($_REQUEST['ingelogd_'])) {
        // met deze hack kan ik op mijn computer het ingelogd-zijn simuleren vanuit een unit test --BCB
        Session::set('U1', 1); // moet eigenlijk username zijn, maar dan vallen alle approval-tests om. Niet nodig.
        Session::set('W1', 1);
        Session::set('A1', 1);
        Session::set('I1', $_REQUEST['ingelogd'] ?? $_REQUEST['ingelogd_']);
        Session::set('PA', 1);
        Session::set('RPP', 30);
        Session::set('ID', $_REQUEST['uid'] ?? $_REQUEST['uid_'] ?? 1);
        // dit ID is mede van invloed op HokSpenen HokOverpl HokAanwas HokVerlaten Uitval HokAfleveren HokVerkopen HokUitscharen
        Session::set('CNT', $_REQUEST['cnt_'] ?? 0); // cnt wordt gelezen in Contact
    }
}

// *** ALS NIET IS INGELOGD ***
if (!Auth::is_logged_in()) {
    Auth::logout();

    if (isset($_POST['knpLogin']) || isset($_POST['knpBasis'])) {
        $lid_gateway = new LidGateway();
        $row = $lid_gateway->findByUserPassword($_POST['txtUser'], $passw);
        if ($row) {
            Auth::login($row);
            Response::redirect($file);
            return;
        }
        $output[] = "header_logout.tpl.php";
        $message = ' Gebruikersnaam of wachtwoord onjuist !';
        // NOTE: $file moet gezet zijn voor login_form
        // TODO: #0004172 $destination of $target zouden betere namen zijn voor deze variabele --BCB
        $output[] = "login_form.tpl.php";
    } else {
        $output[] = "header_logout.tpl.php"; // BV Dit bestand bestaat nog niet BCB: jawel, in templates/
        $output[] = "uitgelogd.tpl.php";      // BV Dit bestand bestaat nog niet BCB: idem
    }
    // *** EINDE ALS NIET IS INGELOGD ***
} elseif (Auth::is_logged_in()) {
    // ***     ALS WEL IS INGELOGD    ***
    // TODO: #0004173 variabele login wordt nergens gebruikt --BCB
    $login = Session::get('U1');
    $lidId = Session::get('I1');
    // TODO: (BV) #0004174 is dit geplande nieuwbouw? dat het uit staat, bedoel ik?
    //$alias = Session::get('A1');

    date_default_timezone_set('Europe/Paris');

    $lid_gateway = new LidGateway();
    $hok_gateway = new HokGateway();

    // Bepalen modules ja of nee
    $rechten = $lid_gateway->rechten($lidId);
    $modbeheer = $rechten->beheer;
    $modtech = $rechten->tech;
    $modfin = $rechten->fin;
    $modmeld = $rechten->meld;

    // Bepalen aantal karakters werknr
    $Karwerk = $lid_gateway->zoek_karwerk($lidId);
    CurrentUser::register('Karwerk', $Karwerk);

    # gebruikt in GroeiresultaatSchaap, en Zoeken
    $w_werknr = 25 + (8 * CurrentUser::Karwerk());

    // Bepalen aantal karakter verblijf
    $w_hok = max(60, 15 + 9 * $hok_gateway->findLongestHoknr($lidId));

    // Bepalen Id van crediteur ophalen dode dieren (Rendac)
    $res = $lid_gateway->findCrediteur($lidId);
    [$rendac_Id, $rendac_ubn] = $res;

    $reader = $lid_gateway->findReader($lidId);

    // Bepalen welke versie de laatste is.
    //  Wanneer deze nog niet is geinstalleerd wordt Beheer in menu1.php rood,
    //  en ook Readerversies in menuBeheer.php
    $dir = dirname(__FILE__); // Locatie bestanden op FTP server
    $persoonlijke_map = $dir.'/user_'.$lidId;
    foreach (setup_versies($db, $persoonlijke_map) as $name => $value) {
        global $$name;
        $$name = $value;
    }

    $output[] = "header.tpl.php";
// ***     EINDE ALS WEL IS INGELOGD     ***
}
