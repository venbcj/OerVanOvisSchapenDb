<?php
/* 8-4-2015 : sql beveiligd
23-11-2015 : Berekening breddte kzlWerknr toegevoegd en query en berekening kzlHoknr toegevoegd 13-2-2017 : breedte kan niet kleiner zijn dan 60
3-12-2015 : ubn aan sessie toegevoegd
12-12-2015 : naast Id ook ubn rendac opgevragd
19-12-2015 : modfin toegevoegd
16-09-2016 : modules gesplitst
29-10-2016 : query modules bij inloggen toegevoegd zodat menu1.php goed wordt opgebouwd bij alleen melden
27-07-2017 : modbeheer toegevoegd
18-03-2018 : _SESSION["PA"]; en _SESSION["RPP"]; toegevoegd.
13-05-2018 : _SESSION["ID"]  _SESSION["DT1"]  _SESSION["BST"] toegevoegd
15-03-2020 : gebruik van welke reader toegevoegd
16-01-2021 : function db_quote toegevoegd
12-08-2023 : include basisfuncties toegevoegd en alle functions daar naar verplaatst
24-10-2023 : zoek_laatste_versie toegevoegd 26-10-2023 update_tblLeden toegevoegd
12-01-2024 : _SESSION["KZ"]; toegevoegd. 14-01-2024 controle toegevoegd op juiste connectie met de database
09-11-2024 : w_hok = 12+(8*lengte); gewijzigd naar w_hok = 15+(9*lengte);
04-01-2025 : include header.php en include header_logout.php hier in geplaatst
23-02-2025 : _SESSION["Fase"] en _SESSION["CNT"] toegevoegd
15-07-2015 : ubn uit sessie gehaald omdat er per 10-7-2025 meerdere ubn's bij 1 gebruiker kunnen bestaan.
 */

include "url.php";
include "connect_db.php";
require_once("basisfuncties.php");
require_once("demo_functions.php");

// BCB: kunstgreep om uitvoer te scheiden van berekening
$output = [];

// TODO: verder scheiden. Dit doet nu:
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
if (php_uname('n') == 'basq' && isset($_REQUEST['ingelogd'])) {
     // met deze hack kan ik op mijn computer het ingelogd-zijn simuleren vanuit een unit test --BCB
     $_SESSION['U1'] = 1;
     $_SESSION['W1'] = 1;
     $_SESSION['I1'] = 1;
     $_SESSION['PA'] = 1;
     $_SESSION['RPP'] = 30;
     $_SESSION['ID'] = 1;
}

// *** ALS NIET IS INGELOGD ***
if (!Auth::is_logged_in()) {
    Auth::logout();

    if (isset($_POST['knpLogin']) || isset($_POST['knpBasis'])) {
        $qrylidId = mysqli_query($db, "
            SELECT lidId, alias, tech, fin, meld
            FROM tblLeden 
            WHERE login = '".mysqli_real_escape_string($db, $_POST['txtUser'])."' and passw = '".mysqli_real_escape_string($db, $passw)."' ;
        ") or die(mysqli_error($db));
        if (mysqli_num_rows($qrylidId) == 0) {
            $output[] = "header_logout.tpl.php";
            $message = ' Gebruikersnaam of wachtwoord onjuist !';
            // NOTE: $file moet gezet zijn voor login_form
            // TODO: $destination of $target zouden betere namen zijn voor deze variabele --BCB
            $output[] = "login_form.tpl.php";
        } else {
            $row = mysqli_fetch_assoc($qrylidId);
            Auth::login($row);
            header("Location: $file");
            return;
        }
    } else {
        $output[] = "header_logout.tpl.php";
        $output[] = "uitgelogd.tpl.php";
    }
// *** EINDE ALS NIET IS INGELOGD ***
} elseif (Auth::is_logged_in()) {
    // ***     ALS WEL IS INGELOGD    ***
    // TODO: variabele login wordt nergens gebruikt --BCB
    $login = $_SESSION["U1"];
    $lidId = $_SESSION["I1"];
    // TODO: (BV) is dit geplande nieuwbouw? dat het uit staat, bedoel ik?
    //$alias = $_SESSION["A1"];
    $pag = $_SESSION["PA"]; // paginanummer dat moet worden ontouden als de pagina wordt ververst
    $RPP = $_SESSION["RPP"]; // standaard aantal regels per pagina

    date_default_timezone_set('Europe/Paris');

    // Bepalen modules ja of nee
    $module = mysqli_query($db, "SELECT beheer, tech, fin, meld FROM tblLeden WHERE lidId = '".mysqli_real_escape_string($db, $lidId)."'; ") or die(mysqli_error($db));
    while ($mod = mysqli_fetch_assoc($module)) {
        $modbeheer = $mod['beheer'];
        $modtech = $mod['tech'];
        $modfin = $mod['fin'];
        $modmeld = $mod['meld'];
    }

    // Bepalen aantal karakters werknr
    $result = mysqli_query($db, "SELECT kar_werknr FROM tblLeden WHERE lidId = '".mysqli_real_escape_string($db, $lidId)."';") or die(mysqli_error($db));
    while ($row = mysqli_fetch_assoc($result)) {
        $Karwerk = $row['kar_werknr'];
    }

    # gebruikt in GroeiresultaatSchaap, en Zoeken
    $w_werknr = 25+(8*$Karwerk);

    // Bepalen aantal karakter verblijf
    $max_lengte = mysqli_query($db, "SELECT max(length(hoknr)) lengte FROM`tblHok`WHERE lidId ='".mysqli_real_escape_string($db, $lidId)."' ") or die(mysqli_error($db));
    while ($max = mysqli_fetch_assoc($max_lengte)) {
        $lengte = $max['lengte'];
    }
    $w_hok = 15+(9*$lengte);
    if ($w_hok < 60) {
        $w_hok = 60;
    }

    // Bepalen Id van crediteur ophalen dode dieren (Rendac)
    $qryRendac = mysqli_query($db, "
    SELECT r.relId, p.ubn 
    FROM tblPartij p
     join tblRelatie r on (p.partId = r.partId)
    WHERE p.lidId = '".mysqli_real_escape_string($db, $lidId)."' and r.uitval = 1;") or die(mysqli_error($db));
    while ($ren = mysqli_fetch_assoc($qryRendac)) {
         $rendac_Id = $ren['relId'];
         $rendac_ubn = $ren['ubn'];
    }

    // Bepalen welke reader wordt gebruikt
    $result = mysqli_query($db, "SELECT reader FROM tblLeden WHERE lidId = '".mysqli_real_escape_string($db, $lidId)."' ;") or die(mysqli_error($db));
    while ($row = mysqli_fetch_assoc($result)) {
         $reader = $row['reader'];
    }

    // Bepalen welke versie de laatste is. Wanneer deze nog niet is geinstalleerd wordt Beheer in menu1.php rood en ook Readerversies in menuBeheer.php

    $dir = dirname(__FILE__); // Locatie bestanden op FTP server
    $persoonlijke_map = $dir.'/user_'.$lidId;

    /* Eerste query zoek alleen readerApp versies
    Tweede query zoek naar readerApp versie i.c.m. taakversies
    Derde query zoek naar alleen taakversies */
    $zoek_laatste_versie = mysqli_query($db, "
SELECT max(Id) lstId
FROM (
    SELECT a.Id
    FROM tblVersiebeheer a
     left join tblVersiebeheer t on (a.Id = t.versieId)
    WHERE a.app = 'App' and isnull(t.Id)

    UNION
    SELECT a.Id
    FROM tblVersiebeheer a
     join tblVersiebeheer t on (a.Id = t.versieId)
    WHERE a.app = 'App'

    UNION

    SELECT Id
    FROM tblVersiebeheer 
    WHERE app = 'Reader' and isnull(versieId)
) a
    ") or die(mysqli_error($db));

    while ($zlv = mysqli_fetch_assoc($zoek_laatste_versie)) {
         $last_versieId = $zlv['lstId'];
    }

    $zoek_readersetup_in_laatste_versie =  mysqli_query($db, "
SELECT bestand
FROM tblVersiebeheer 
WHERE app = 'App' and Id = '".mysqli_real_escape_string($db, $last_versieId)."'
") or die(mysqli_error($db));

    while ($zrv = mysqli_fetch_assoc($zoek_readersetup_in_laatste_versie)) {
         $Readersetup_bestand = $zrv['bestand'];
    }

    // hee, dit fragment /staat/ al in Readerversies.php
    if (isset($Readersetup_bestand)) {
        $appfile_exists = file_exists($persoonlijke_map.'/Readerversies/'.$Readersetup_bestand);
    } else {
        $appfile_exists = 1;
    }

    $zoek_readertaken_in_laatste_versie =  mysqli_query($db, "
SELECT bestand
FROM tblVersiebeheer 
WHERE app = 'Reader' and (Id = '".mysqli_real_escape_string($db, $last_versieId)."' or versieId = '".mysqli_real_escape_string($db, $last_versieId)."')
") or die(mysqli_error($db));

    while ($zrv = mysqli_fetch_assoc($zoek_readertaken_in_laatste_versie)) {
         $Readertaken_bestand = $zrv['bestand'];
    }

    // hee, dit fragment /staat/ al in Readerversies.php
    if (isset($Readertaken_bestand)) {
        $takenfile_exists = file_exists($persoonlijke_map.'/Readerversies/'.$Readertaken_bestand);
    } else {
        $takenfile_exists = 1;
    }

    if ($appfile_exists == 1 && $takenfile_exists == 1) {
        $actuele_versie = 'Ja';
    }
    $output[] = "header.tpl.php";
// ***     EINDE ALS WEL IS INGELOGD     ***
}
foreach ($output as $view) {
    include $view;
}
