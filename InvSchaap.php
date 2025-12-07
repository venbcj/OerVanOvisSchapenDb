<?php

require_once("autoload.php");

/* 8-8-2014 Aantal karakters werknr variabel gemaakt en quotes bij "met" en "zonder" weggehaald 
11-8-2014 : veld type gewijzigd in fase 
5-11-2014 : Bijwerken database aangevuld met inserten tblRequest en tblMeldingen 
20-2-2015 : login toegevoegd 
11-4-2015 : veld volgnr uit tblUitval gebruikt i.pv. veld uitvalId LET OP volgnr wordt ook gebruikt in 
            -    importReader.php 
            -    insGeboortes.php 
5-11-2015 : aanschafdatum gewijzigd in aankoopdatum 
15-11-2015 controle geboortedatum na einddatum moeder indien van toepassing 
17-11-2015 kzlMoeder en kzlVader aangepast fase in lijst verwijderd want fase is (bij moeder) altijd moeder en geen lam meer
18-11-2015 : hok gewijzigd naar verblijf 
25-9-2016 Bij uitval keuze reden en moment niet verplicht gemaakt
20-10-2016 : mdrId en vdrId gewijzigd in volwId 
28-10-2016 : Geboortedatum bij aanvoer vader- moederdieren niet verplicht gemaakt */
$versie = "18-11-2016"; /* Controle 'levnr bestaat al' gewijzigd. Geldt nl. alleen indien op stallijst. Controle op dood dier toegevoegd t.b.v. aanvoer */
$versie = "19-11-2016"; /* Variabele levnr bestaat alleen als er een levensnummer is ingevuld    21771 */
$versie = "22-1-2017"; /* 18-1-2017 Query's aangepast n.a.v. nieuwe tblDoel en hok_uitgez = 'Gespeend' gewijzigd in hok_uitgez = 2        22-1-2017 tblBezetting gewijzigd naar tblBezet */
$versie = "12-2-2017"; /* Halsnummer toegvoegd en komma bij geboorte gewicht omgezet naar een punt     19-2-2017 aantal handmatig ingevoerde schapen gebaseerd op tblStal zodat opnieuw aanvoer ook wordt geteld.        4-4-2017 : kleuren halsnummer uitgebreid */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '10-11-2018'; /* invoer vader- en moederdier aangepast. Worp kan 1 x per 183 dagen en gebeurt op 1 dag 
    Verder zijn er 3 scenario voor invoer vader- en/of moederdier bij invoer levensnummer schaap
  1. Levnr bestaat in db maar heeft geen ouders      => Geen drachtdatum en geen registratie 'drachtig'
  2. Levnr bestaat niet in db en het betreft aanvoer => Geen drachtdatum en geen registratie 'drachtig'
  3. Levnr bestaat niet in db, is geen aanvoer en dracht bestaat niet binnen 183 dagen => fictieve drachtdatum en geen registratie 'drachtig'. Geen registratie drachtig zodat pagina 'Dracht.php' alleen met veld drachtig kan filteren/tonen !! */
$versie = '9-1-2019'; /* javascript toegevoegd 13-1 : vaderdier obv dracht mbv javascript */
$versie = '6-2-2019'; /* Vaderdier is tot een jaar terug te kiezen */
$versie = '2-2-2020'; /* keuzelijst geslacht uitgebreid met kween */
$versie = '11-1-2022'; /* Script verbeterd/herschreven. SQL beveiligd d.m.v. quotes. Code aangepast n.a.v. registratie dekkingen en dracht */
$versie = '05-02-2022'; /* Drachtig (ja/nee) wordt niet meer vastgelegd in tblVolwas */
$versie = '09-09-2023'; /* if (isset(lst_dmworp)) { toegevoegd anders bestaat verschil_worp onterecht */
$versie = '31-12-2023'; /* and h.skip = 0 aangevuld bij tblHistorie */
$versie = '23-10-2024'; /* Paginanaam gewijzigd van Invoeren schaap naar Aanvoer schaap */
$versie = '26-12-2024'; /* <TD width = "960" height = "400" valign = "top" > gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */
$versie = '17-02-2025'; /* Terug van uitscharen mogelijk gemaakt 22-02-2025 velden m.b.t. index verwijderd */
$versie = '10-07-2025'; /* Keuzelijst ubn toegevoegd voor gebruikers met meerdere ubn's */

 Session::start();
  ?>
<!DOCTYPE html>
<html>
<head>
<title>Registratie</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<?php include "kalender.php"; ?>

</head>
<body>
<?php


$titel = 'Aanvoer schaap';
$file = "InvSchaap.php";
include "login.php"; ?>

            <TD valign = "top">
<?php
if (Auth::is_logged_in()) {
    $schaap_gateway = new SchaapGateway();
    $stal_gateway = new StalGateway();
    $lid_gateway = new LidGateway();
    $volwas_gateway = new VolwasGateway();

// Array tbv javascript om fase automatisch te tonen bij bestaande dieren
    // @TODO (BV) deze variabele wordt nergens gebruikt. Weghalen?
    $array_fase_bij_dier = $schaap_gateway->fase_bij_dier();

// Array tbv javascript om vader automatisch te tonen
    // Zoek de laatste dekkingen. Deze laatste dekking moet een vader hebben geregistreerd
    // Als er een dracht bestaat in tblDracht moet deze niet zijn verwijderd (zie hd.skip = 0)
$array_vader_uit_koppel = $schaap_gateway->zoek_laatste_dekkingen($Karwerk);

// Array tbv javascript om werpdatum automatisch te tonen
    // Zoek de laatste dekkingen. Vervolgens daarvan actuele worpen (binnen de laatste 30 dagen) zoeken en werpdatum tonen
$array_worp = $schaap_gateway->zoek_werpdatum_laatste_dekking();

if (!empty($_POST['txtLevnr'])) { $levnr = $_POST['txtLevnr'];  }

$rel_herk = null;
$rel_best = null;
if (isset($levnr)) {
    $aanwezig = $stal_gateway->zoek_in_stallijst($lidId, $levnr);
//Als het die is afgevoerd en weer wordt aangevoerd. O.a. als het dier op een ander ubn van dezelfde gebruiker wordt gezet is dit relevant.
    $afgevoerd = $stal_gateway->zoek_in_afgevoerd($lidId, $levnr);
    $dood = $stal_gateway->zoek_dood($levnr);
    $uitgeschaard = $stal_gateway->zoek_uitgeschaard($levnr);
    if (isset($uitgeschaard)) {
        $rel_herk = $stal_gateway->zoek_herkomst($uitgeschaard);
    }
 }

 // TODO: (BV) #0004122 verwacht array_vader_uit_koppel ... maar die wordt verderop pas gezet. Klopt dit?
// ik heb het maar vast hier gezet, ipv op regel 54 of zo
 include "validate-invschaap.js.php";
/***********************
 ****    OPSLAAN        ****
 ***********************/
if (isset($_POST['knpSave'])) {
    #echo '$levnr = '.$levnr.'<br>';
    if (isset($levnr)) { // Zoek naar een bestaand levensnummer. Bijvoorbeeld die een andere gebruiker al eens heeft ingevoerd of opnieuw aanvoer.
        $zoek_bestaand_levensnummer = $schaap_gateway->zoek_eerder_levensnummer($levnr);
    while ($lvn = mysqli_fetch_assoc($zoek_bestaand_levensnummer)) {
        $levnr_db = $lvn['schaapId'];
        $mdrId_db = $lvn['mdrId'];
        $volwId_db = $lvn['volwId'];
        $dmgeb_db = $lvn['dmgeb'];
        $dmeerste_db = $lvn['dmeerste'];
        $eerstedm_db = $lvn['eerstedm'];
        $aanwas_db = $lvn['dmaanw'];
        $dmafvoer_db = $lvn['dmafv'];
        $laatste_afvoerdm = $lvn['afvdm']; 
        }
    } // Einde if (isset($levnr))

if (!isset($_POST['kzlUbn'])) { // Dit geldt als een gebruik slechts 1 ubn heeft. veld kzlUbn bestaat dan nl. niet
    $kzlUbn = $lid_gateway->findUbn($lidId);
} elseif (!empty($_POST['kzlUbn'])) { $kzlUbn = $_POST['kzlUbn']; }

$kzlKleur = null;
if (!empty($_POST['kzlKleur']))    { $kzlKleur = $_POST['kzlKleur']; }
$txtHalsnr = '';
if (!empty($_POST['txtHalsnr']))    { $txtHalsnr = $_POST['txtHalsnr']; }

if (!empty($_POST['kzlFase']))    { $kzlFase = $_POST['kzlFase']; } if($kzlFase != 'lam') { $invoer = 'aanvoer'; }
if (!empty($_POST['kzlSekse']))    { $kzlSekse = $_POST['kzlSekse']; }
if (!empty($_POST['kzlRas']))    { $kzlRas = $_POST['kzlRas']; }

if (!empty($_POST['kzlOoi']))    { $kzlOoi = $_POST['kzlOoi']; $moeder = $kzlOoi; } else if(isset($mdrId_db)) { $moeder = $mdrId_db; }
$kzlRam = null;
if (!empty($_POST['kzlRam']))    { $kzlRam = $_POST['kzlRam']; }

$txtGebkg = 0;
if (!empty($_POST['txtGebkg']))    { $txtGebkg = str_replace(',', '.', $_POST['txtGebkg']); }
if (!empty($_POST['txtGebdm']))     { $gebdm = date_create($_POST['txtGebdm']); $txtGebdm = $_POST['txtGebdm']; }
    else { $gebdm = date_create($array_worp[$kzlOoi]); $txtGebdm = $array_worp[$kzlOoi]; }
        
    if (isset($txtGebdm)) { $txtDmgeb =  date_format($gebdm, 'Y-m-d'); } //echo 'gebdm is leeg dus '.$txtDmgeb.'<br>'; } #/#
if (!empty($_POST['txtAanv']))    {
    $aanvdm = date_create($_POST['txtAanv']);
    $txtAanvdm = $_POST['txtAanv'];
    $txtDmaanv = date_format($aanvdm, 'Y-m-d');
}

$kzlMoment = null;
if (!empty($_POST['kzlMoment'])) { $kzlMoment = $_POST['kzlMoment']; }
if (!empty($_POST['txtUitvdm'])) {
    $uitvdm = date_create($_POST['txtUitvdm']);
    $txtUitvdm = $_POST['txtUitvdm'];
    $txtDmuitv =  date_format($uitvdm, 'Y-m-d');
}
if (!empty($_POST['kzlReden'])) { $kzlReden = $_POST['kzlReden']; }

if (!empty($_POST['kzlHok'])) { $kzlHok = $_POST['kzlHok']; }

if (isset($moeder)) {
    $startmdr = $stal_gateway->startdm_moeder($lidId, $moeder);
    $endmdr = $stal_gateway->zoek_eindm_mdr_indien_afgevoerd($lidId, $moeder);
    // Zoek naar laatste worp ter controle dat deze minstens 183 dagen is geleden.
    if (isset($txtDmgeb)) {
        $lst_volwId = $schaap_gateway->zoek_laatste_worp($moeder);
        [$lst_dmworp, $lst_worpdm] = $schaap_gateway->zoek_datum_laatste_worp($lst_volwId);
        if (isset($lst_dmworp)) {
            $datetime1 = date_create($lst_dmworp);
            $verschil_worp = date_diff($datetime1, $gebdm);
        }
    } // Einde Zoek naar laatste worp ter controle dat deze minstens 183 dagen is geleden.

} // Einde if (isset($moeder))

// Controle moederdier bij reeds geregistreerd levensnummer
    if (isset($levnr_db) && isset($mdrId_db) && isset($kzlOoi) && $mdrId_db <> $kzlOoi) {
         $fout = "Dit dier heeft al een moeder. Ooi (en ram) wordt niet opgeslagen. "; 
        }
// Einde Controle moederdier bij reeds geregistreerd levensnummer

// 1. CONTROLE OP JUISTE INVOER
 if (isset($levnr) && !isset($kzlSekse) && !isset($levnr_db) && !isset($kzlMoment) && !isset($txtDmuitv) && !isset($kzlReden) )
    {
        $fout = "Het geslacht moet zijn ingevuld.";
    }

else if (isset($levnr) && !isset($kzlRas) && !isset($levnr_db) && !isset($kzlMoment) && !isset($txtDmuitv) && !isset($kzlReden) )
    {
        $fout = "Het ras moet zijn ingevuld.";
    }

else if ($modtech == 1 && !isset($kzlOoi) && $kzlFase == 'lam' ) 
    {
        $fout = "Het moederdier moet zijn ingevuld.";
    }

/* per 13-1-2022 met javascript gecontroleerd
else if ($modtech == 1 && isset($levnr) && !isset($txtGebkg) && $kzlFase == 'lam' && !isset($kzlMoment) && !isset($txtDmuitv) && !isset($kzlReden) )
    {
        $fout = "Het gewicht moet zijn ingevuld.";
    }*/

else if ( (isset($kzlMoment) && !isset($txtDmuitv) )
      || (isset($kzlReden) && !isset($txtDmuitv) )
      || (!isset($levnr) && !isset($txtDmuitv)  ) )
    {
        $fout = "Bij overlijden moet datum t.b.v. uitval zijn ingevuld.";
    }

else if ( isset($txtDmuitv) && isset($txtGebdm) && $txtDmuitv < $txtDmgeb )
    {
        $fout = "Datum overlijden kan niet voor geboortedatum liggen !";
    }

else if ( isset($txtDmuitv) && isset($txtAanvdm) && $txtDmuitv < $txtDmaanv )
    {
        $fout = "Datum overlijden kan niet voor aanschafdatum liggen !";
    }

else if ( isset($txtGebdm) && isset($txtAanvdm) && $txtDmaanv < $txtDmgeb )
    {
        $fout = "Datum aanschaf kan niet voor geboortedatum liggen !";
    }
    
else if ($modtech == 1 && !isset($kzlHok) && $kzlFase == 'lam' && !isset($kzlMoment) && !isset($txtDmuitv) && !isset($kzlReden) )
    {
        $fout = "Plaats het lam ook nog in een verblijf.";
    }
    
else if ( !empty($aanwezig) && isset($levnr) )
    {
        $fout = "Dit dier staat al op de stallijst.";
    }
    
else if ( !isset($txtAanvdm) && ($kzlFase == 'moeder' || $kzlFase == 'vader' || (isset($levnr_db) && isset($aanwas_db))) )
    {
        $fout = "Bij invoer van een volwassen dier is de aanschafdatum verplicht.";
    }
    
//else if ($kzlFase == 'lam' && isset($txtDmgeb) && $txtDmgeb < $startmdr) // $txtDmgeb bestaat niet als werpdatum wordt gepresenteerd d.m.v. javascript i.p.v. het veld geboortedatum

else if ($kzlFase == 'lam' && $txtDmgeb < $startmdr)
    {
        $fout = "Geboortedatum kan niet voor aanvoerdatum van moederdier liggen.";

    }

else if ($kzlFase == 'lam' && isset($endmdr) && $endmdr < $txtDmgeb)
    {
        $fout = "Geboortedatum kan niet na afvoerdatum van moederdier liggen.";
    }

else if (!isset($dmgeb_db) && isset($txtGebdm) && isset($levnr_db) && $dmeerste_db < $txtDmgeb)
    {
        $fout = "Geboortedatum kan niet na ".$eerstedm_db." liggen.";
    }

else if (($kzlFase == 'moeder' || $kzlFase == 'vader' || (isset($levnr_db) && isset($aanwas_db)) ) && isset($txtAanvdm) && isset($dmafvoer_db) && $txtDmaanv < $dmafvoer_db)
    {
        $fout = "Aanvoerdatum kan niet voor ".$laatste_afvoerdm." liggen.";
    }

else if (isset($dood)) // Bestaand levensnummer dat reeds is overleden. T.b.v. aankoop volwassen dieren.
    {
        $fout = "Dit is een overleden schaap.";
    }

else if (isset($txtDmuitv) && isset($levnr_db)) // Dood dier met levensnummer dat al voorkomt in tblSchaap. Controle t.b.v. doodgeboren lam
    {
        $fout = "Dit levensnummer bestaat al.";
    }

else if (isset($verschil_worp) && $verschil_worp->days < 183 && $verschil_worp->days <> 0) // Het moederdier kan van deze dracht al een lam hebben. Die geboortedatum moet gelijk liggen aan de geboortedatum van dit schaap. Mits deze een geboortedatum heeft.
    {
        if ($verschil_worp->days < 10) {
        $fout = "Deze ooi heeft reeds geworpen op " . $lst_worpdm . ". Dat moet dus de geboortedatum zijn van dit schaap.";
            $txtGebdm = $lst_worpdm;
        }
        else{
            $fout = "Deze ooi heeft op " . $lst_worpdm . " nog geworpen. Een ooi kan 1 x per half jaar werpen.";
        }
    }

else if (!isset($levnr_db) && $kzlFase == 'lam' && isset($dmdracht) && $txtDmgeb < $dmdracht)
    {
        $fout = 'De geboortedatum mag niet voor drachtdatum ('.$drachtdm.') liggen.';
// EINDE  1. CONTROLE OP JUISTE INVOER
    } else {
// 2. DATABASE BIJWERKEN

// ********************
//       2.1 BEPAAL VOLWID // Bepaal volwId bij geboren lam
// ********************

if ($modtech == 1 && !isset($levnr_db) && $kzlFase == 'lam') { // Als levnr niet bestaat in database en het is geen aanvoer
    $volwId = $volwas_gateway->zoek_actuele_worp($kzlOoi, $txtDmgeb);
    if (!isset($volwId)) {
        $lst_volwId = $volwas_gateway->zoek_vorige_worp($kzlOoi, $txtDmgeb);
        $volwId = $volwas_gateway->zoek_actuele_dracht($kzlOoi, $lst_volwId);
    }
    if (!isset($volwId)) {
        $volwId = $volwas_gateway->zoek_actuele_dekking($kzlOoi, $lst_volwId);
    }
    if (isset($volwId) && isset($kzlRam)) {
// Als er een actuele volwId bestaat kan hier eventueel alsnog een vader worden toegevoegd aan het koppel
        $vdrId = $volwas_gateway->zoek_vader_uit_koppel($volwId);
        if (!isset($vdrId)) {
            $volwas_gateway->update_koppel($kzlRam, $volwId);
        }
    }
    if (!isset($volwId)) {
        $volwas_gateway->maak_koppel($kzlOoi, $kzlRam);
        // TODO laat de insert-query een aangemaakt id teruggeven
        $volwId = $volwas_gateway->zoek_recentste_id($kzlOoi);
    }
}

// Einde Bepaal volwId bij geboren lam

// Bepaal volwId bij aanvoer
if (
    ($modtech == 1 && (isset($kzlOoi) || isset($kzlRam)) )
    && (
        // levnr bestaat in db maar heeft geen ouders en nu wel
        (isset($levnr_db) && !isset($volwId_db) ) ||
        // Levnr bestaat niet in db en het betreft aanvoer met registratie ouders
        (!isset($levnr_db) && ($kzlFase == 'moeder' || $kzlFase == 'vader') )
    ) 
)
{
// Controle nieuwe worp. Deze moet 183 dagan van vorige worp of volgende worp liggen.
if (isset($txtDmgeb) && isset($kzlOoi)) {
    $volwId = $volwas_gateway->zoek_bestaande_worp($kzlOoi, $txtDmgeb);
    if (!isset($volwId)) {
    // Zoek de vorige worp t.o.v. de geboorte datum. Dus ongeacht wanneer de volwId is geregistreerd, max(volwId) geldt dus niet!
        [$vorige_dmworp, $vorige_worpdm] = $volwas_gateway->zoek_laatste_worp_voor_geboortedatum($kzlOoi, $txtDmgeb);
        $date_vorige = date_create($vorige_dmworp);
        $verschil_vorige_worp = date_diff($date_vorige, $gebdm);
        $dagen_vorige_worp = $verschil_vorige_worp->days;
    // Zoek de volgende worp t.o.v. de geboorte datum. Dus ongeacht wanneer de volwId is geregistreerd, max(volwId) geldt dus niet!
        [$volgend_dmworp, $volgend_worpdm] = $volwas_gateway->zoek_volgende_worp_na_geboortedatum($kzlOoi, $txtDmgeb);
        $date_volgende = date_create($volgend_dmworp);
        $verschil_volgende_worp = date_diff($gebdm, $date_volgende);
        $dagen_volgende_worp = $verschil_volgende_worp->days;
    }

}
// Einde Controle nieuwe worp. Deze moet 183 dagan van vorige worp of volgende worp liggen.

if (isset($verschil_vorige_worp) && $dagen_vorige_worp < 183) {
$fout = "De vorige worp van dit moederdier is ".$vorige_worpdm.". Een ooi kan 1x in het half jaar werpen.";

}
else if (isset($verschil_volgende_worp) && $dagen_volgende_worp < 183) {
$fout = "De volgende worp van dit moederdier is ".$volgend_worpdm.". Een ooi kan 1x in het half jaar werpen.";

} else {
    $volwas_gateway->maak_koppel($kzlOoi, $kzlRam);
        // TODO laat de insert-query een aangemaakt id teruggeven
        $volwId = $volwas_gateway->zoek_recentste_id($kzlOoi);
}

} 
// Einde Bepaal volwId bij aanvoer

// ********************
// EINDE 2.1 BEPAAL VOLWID
// ********************


// ***************************
//         2.2 GEGEVENS INLEZEN
// ***************************
if (isset($levnr) && !isset($levnr_db) && $kzlFase == 'lam' && !isset($txtDmuitv) )    { $scenario = 'Geboren_lam'; }

else if (isset($uitgeschaard)) { $scenario = 'Inscharen'; }

else if ( (isset($kzlFase) && $kzlFase != 'lam') || (isset($levnr_db) && isset($aanwas_db)) ) { $scenario = 'Aanvoer_ouder'; }

else if (isset($txtDmuitv) && isset($levnr) && !isset($levnr_db)) { $scenario = 'Dood_lam_met_levensnummer'; }

else if (isset($txtDmuitv) && !isset($levnr)) { $scenario = 'Dood_lam_zonder_levensnummer'; }
 
/***** 2.2.1 INLEZEN SCHAAP EN STAL  *****/
if (!isset($fout) && isset($scenario)) { //echo '$scenario = '.$scenario.'<br>'; #/#
$kzlReden = 3; // REMOVEME dit is alleen om een test draaiend te krijgen
if (isset($levnr_db)) {
    $schaapId = $schaap_gateway->zoek_schaapid($levnr);
}

else if ($scenario == 'Dood_lam_zonder_levensnummer') {
    $schaap_gateway->maak_schaap($ubn, $kzlRas, $kzlSekse, $volwId, $kzlMoment, $kzlReden);
    // TODO laat de insert-query een aangemaakt id teruggeven
    $volwId = $volwas_gateway->zoek_recentste_id($kzlOoi);
    $schaap_gateway->wis_levensnummer($ubn);
} else {

// $kzlRas is bij uitval zonder levensnummer niet verplicht
// $kzlSekse is bij uitval niet verplicht
// $kzlOoi en dus $volwId is alleen verplicht bij geboren lammeren i.c.m. module technisch
    $schaap_gateway->maak_schaap($levnr, $kzlRas, $kzlSekse, $volwId, $kzlMoment, $kzlReden);
    // TODO laat de insert-query een aangemaakt id teruggeven
    $volwId = $volwas_gateway->zoek_recentste_id($kzlOoi);
}

if ($scenario == 'Dood_lam_met_levensnummer' || $scenario == 'Dood_lam_zonder_levensnummer') {
    $rel_best = $rendac_Id;
}
$stal_gateway->insert_uitgebreid($lidId, $schaapId, $rel_herk, $kzlUbn, $kzlKleur, $txtHalsnr, $rel_best);
           $stalId = $stal_gateway->zoek_laatste_stalId($lidId, $schaapId);

/*****  EINDE 2.2.1 INLEZEN SCHAAP EN STAL  *****/    
/*****  2.2.2 INLEZEN HISTORIE  *****/

// inlezen geboortedatum
if (isset($txtDmgeb) && !isset($dmgeb_db)) { // Geboortedatum bij volwassendieren niet verplicht
$insert_tblHistorie_geb = "INSERT INTO tblHistorie SET stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$txtDmgeb)."', kg = " . db_null_input($txtGebkg) . ", actId = 1 ";
            
/*echo $insert_tblHistorie_geb.'<br>';    ##*/mysqli_query($db,$insert_tblHistorie_geb);# or die (mysqli_error($db));
}

if ($scenario == 'Inscharen' || $scenario == 'Aanvoer_ouder') { // Terug van uitscharen kan ook een lam zijn. Vandaar $scenario inscharen los van aanvoer_ouder gehouden

    if ($scenario == 'Inscharen') { $actId_op = 11; } else { $actId_op = 2; }
$insert_tblHistorie_aanv = "INSERT INTO tblHistorie SET stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$txtDmaanv)."', actId = '".mysqli_real_escape_string($db,$actId_op)."' ";
/*echo $insert_tblHistorie_aanv.'<br>';    ##*/mysqli_query($db,$insert_tblHistorie_aanv); # or die (mysqli_error($db));

if ($scenario == 'Aanvoer_ouder' && !isset($aanwas_db)) {
$insert_tblHistorie_aanw = "INSERT INTO tblHistorie SET stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$txtDmaanv)."', actId = 3 ";
/*echo $insert_tblHistorie_aanw.'<br>';    ##*/mysqli_query($db,$insert_tblHistorie_aanw); # or die (mysqli_error($db));
}

    
}


if ($scenario == 'Dood_lam_met_levensnummer' || $scenario == 'Dood_lam_zonder_levensnummer') {

$insert_tblHistorie_doo = "INSERT INTO tblHistorie SET stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$txtDmuitv)."', actId = 14  ";
/*echo $insert_tblHistorie_doo.'<br>';    ##*/mysqli_query($db,$insert_tblHistorie_doo); # or die (mysqli_error($db));

}
/*****  EINDE 2.2.2 INLEZEN HISTORIE  *****/

if (isset($kzlHok)) {

$zoek_hisId_tbv_tblBezet = mysqli_query($db,"
SELECT max(hisId) hisId
FROM tblHistorie h
 join tblActie a on (h.actId = a.actId)
WHERE h.skip = 0 and a.aan = 1 and stalId = '".mysqli_real_escape_string($db,$stalId)."'
"); # or die (mysqli_error($db));
    while ( $zhb = mysqli_fetch_assoc ($zoek_hisId_tbv_tblBezet)) { $hisId = $zhb['hisId']; }

    $insert_tblBezet = "INSERT INTO tblBezet SET hokId = '".mysqli_real_escape_string($db,$kzlHok)."', hisId = '".mysqli_real_escape_string($db,$hisId)."' ";
/*echo $insert_tblBezet.'<br>';    ##*/mysqli_query($db,$insert_tblBezet); # or die (mysqli_error($db));        
}


if ($modmeld == 1 && $scenario == 'Geboren_lam') {

$reqst_file = 'InvSchaap.php_geboren';
$Melding = 'GER';
}

if ($modmeld == 1 && ($scenario == 'Inscharen' || $scenario == 'Aanvoer_ouder')) {

$zoek_hisIdaanv = mysqli_query($db,"
SELECT hisId
FROM tblHistorie
WHERE skip = 0 and stalId = '".mysqli_real_escape_string($db,$stalId)."' and actId = '".mysqli_real_escape_string($db,$actId_op)."'
"); # or die (mysqli_error($db));
        while ( $hId = mysqli_fetch_assoc ($zoek_hisIdaanv)) { $hisId = $hId['hisId']; }
            
$reqst_file = 'InvSchaap.php_aanwas';
$Melding = 'AAN';
}

if ($modmeld == 1 && $scenario == 'Dood_lam_met_levensnummer') {
        
$zoek_hisId = mysqli_query($db,"
SELECT hisId
FROM tblHistorie
WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."' and actId = 14 and skip = 0
"); # or die (mysqli_error($db));
    while ( $hi = mysqli_fetch_assoc ($zoek_hisId)) { $hisId = $hi['hisId']; }

$reqst_file = 'InvSchaap.php_uitval';
$Melding = 'DOO';
}

}// Einde if (!isset($fout) && isset($scenario))            

// ***************************
//      EINDE 2.2 GEGEVENS INLEZEN
// ***************************

if (isset($levnr)) { $levnr = substr($levnr, 0, 6); }
// EINDE   2. DATABASE BIJWERKEN    
    }

} // Einde if (isset($_POST['knpSave']))
/***********************
 **** EINDE OPSLAAN    ****
 ***********************/

/************************************
 **** ZOEK SCHAAP IN DATABASE    ****
 ***********************************/
if (isset($_POST['knpZoek'])) {

$zoek_schaapId = mysqli_query($db,"
SELECT schaapId
FROM tblSchaap 
WHERE levensnummer = ".mysqli_real_escape_string($db,$levnr)."
"); # or die (mysqli_error($db));

while( $zs = mysqli_fetch_assoc($zoek_schaapId)) { $schaapId = $zs['schaapId']; }

if (isset($schaapId)) {

$zoek_gegevens_schaap = mysqli_query($db,"
SELECT prnt.hisId aanwId, s.geslacht, r.ras, right(mdr.levensnummer,$Karwerk) werknr_ooi, right(vdr.levensnummer,$Karwerk) werknr_ram, date_format(hgeb.datum,'%d-%m-%Y') gebdm
FROM tblSchaap s
 left join (
     SELECT h.hisId, st.schaapId
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
     WHERE actId = 3 and schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
 ) prnt on (s.schaapId = prnt.schaapId)
 left join tblRas r on (r.rasId = s.rasId)
 left join tblVolwas v on (s.volwId = v.volwId)
 left join tblSchaap mdr on (mdr.schaapId = v.mdrId)
 left join tblSchaap vdr on (vdr.schaapId = v.mdrId)
 left join (
     SELECT h.datum, st.schaapId
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
     WHERE actId = 1 and schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
 ) hgeb on (s.schaapId = hgeb.schaapId)
WHERE s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
"); # or die (mysqli_error($db));

 while( $zgs = mysqli_fetch_assoc($zoek_gegevens_schaap)) {
     $aanw_db = $zgs['aanwId'];
     $sekse_db = $zgs['geslacht']; if (isset($aanw_db) && $sekse_db == 'ooi') { $fase_db = 'moeder'; } else if(isset($aanw_db) && $sekse_db == 'ram') { $fase_db = 'vader'; } else { $fase_db = 'lam'; }
     $ras_db = $zgs['ras'];
     $werknr_ooi_db = $zgs['werknr_ooi'];
     $werknr_ram_db = $zgs['werknr_ram'];
     $gebdm_db = $zgs['gebdm'];
 }

}
else
{
$fout = 'Dit levensnummer wordt niet gevonden.';
}

} // Einde if (isset($_POST['knpZoek']))
/***************************************
 **** EINDE ZOEK SCHAAP IN DATABASE    ****
 ***************************************/

 // ( 'vandaag ingevoerd' is ter controle van schapen zonder levensnummer)
$zoek_vandaag_ingevoerd_met_levnr = mysqli_query($db,"
SELECT count(s.schaapId) aant
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
WHERE s.levensnummer is not null and date_format(s.dmcreatie,'%Y-%m-%d') = CURRENT_DATE() and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
"); # or die (mysqli_error($db));
    while ( $zvml = mysqli_fetch_assoc ($zoek_vandaag_ingevoerd_met_levnr)) { $met = $zvml['aant']; }

$zoek_vandaag_ingevoerd_zonder_levnr = mysqli_query($db,"
SELECT count(s.schaapId) aant
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
WHERE isnull(s.levensnummer) and date_format(s.dmcreatie,'%Y-%m-%d') = CURRENT_DATE() and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
"); # or die (mysqli_error($db));
    while ( $zvzl = mysqli_fetch_assoc ($zoek_vandaag_ingevoerd_zonder_levnr)) { $zonder = $zvzl['aant']; }

if ($met > 0 || $zonder > 0) { ?>

<table border = 0 style = "font-size : 10px" > 
<tr>
 <td><i> Vandaag ingevoerd :</td>
 <td><?php echo $met; ?> met levensnummer</td>
</tr>
<tr>
 <td></td>
 <td><?php echo $zonder; ?> zonder levensnummer. </td>
</tr>
</table>

<?php } 

// Declaratie Ubn
$zoek_aantal_ubn = mysqli_query($db,"
SELECT count(ubnId) aant
FROM tblUbn
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actief = 1
"); # or die (mysqli_error($db));

while ($zau = mysqli_fetch_assoc($zoek_aantal_ubn)) 
{ 
   $aantal_ubn = $zau['aant'];
} 

if ($aantal_ubn > 1) {
$qryUbn = mysqli_query($db,"
SELECT ubnId, ubn
FROM tblUbn
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actief = 1
ORDER BY ubn
"); # or die (mysqli_error($db));

$index = 0; 
while ($ubn = mysqli_fetch_assoc($qryUbn)) 
{ 
   $ubnId[$index] = $ubn['ubnId']; 
   $ubnnm[$index] = $ubn['ubn'];
   $index++; 
} 
unset($index);

}
else {
$zoek_ubn = mysqli_query($db,"
SELECT ubn
FROM tblUbn
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actief = 1
"); # or die (mysqli_error($db));

while ($zu = mysqli_fetch_assoc($zoek_ubn)) 
{ 
   $ubn = $zu['ubn'];
}
}
// EINDE Declaratie Ubn ?>

<form action="InvSchaap.php" method="post">
<table border = 0>
<tr valign="top">
 <td>    
<!-- ********************
         OPMAAK LINKS 
     ******************** -->
    <table border = 0>
    <tr>
     <td> Ubn : </td>
     <td>
<?php if ($aantal_ubn > 1) { ?>
    <!-- KZLUBN -->
     <select name= "kzlUbn" style= "width:62;" >
      <option></option>
    <?php   $count = count($ubnId); 
    for ($i = 0; $i < $count; $i++){

        $opties = array($ubnId[$i]=>$ubnnm[$i]);
                foreach($opties as $key => $waarde)
                {
      if (isset($_POST["kzlUbn"]) && $_POST["kzlUbn"] == $key){
        echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
      } else { 
        echo '<option value="' . $key . '" >' . $waarde . '</option>';  
      }     
                }
    }

 ?>
    </select>
    <!-- Einde KZLUBN -->
<?php } else { echo $ubn; } ?>
     </td>
    </tr>
    <tr>
     <td> Levensnummer : </td>
     <td><input type="text" name="txtLevnr" autofocus id="levnr" onfocus="toon_dracht()" onchange="toon_dracht()" value = <?php if (isset($levnr)) { echo $levnr ; } ?> ></td>
     <td> <input type="submit" name="knpZoek" onfocus = "verplicht_bij_zoeken()" value="Zoek levensnummer"> </td>
    </tr>
<!-- HALSNUMMER -->
    <tr>
     <td>Halsnr : </td>
     <td>
     <select name= "kzlKleur" style= "width:62;" > 
    <?php
    $opties = array('' => '', 'blauw' => 'blauw', 'geel' => 'geel', 'groen' => 'groen', 'oranje' => 'oranje', 'paars' => 'paars', 'rood'=>'rood', 'wit' => 'wit', 'zwart' => 'zwart');
    foreach ( $opties as $key => $waarde)
    {
       if ((!isset($_POST['knpSave']) && ($kzlKleur ?? '') == $key) || (isset($_POST["kzlKleur"]) && $_POST["kzlKleur"] == $key) ) {
        echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
      } else {
        echo '<option value="' . $key . '">' . $waarde . '</option>';
      }
    } ?>
    </select>  
     <input type = text name = "txtHalsnr" style = "text-align : right" size = 1 value = <?php if (isset($txtHalsnr)) { echo $txtHalsnr; } ?> > </td>
    </tr>
<!-- KZLGENERATIE -->
    <tr>
    <td>Generatie : </td>
    <td>
    <?php if (isset($fase_db)) { echo $fase_db; } else {
    $optie_fase = array('' => '', 'lam' => 'lam', 'moeder' => 'moeder', 'vader' => 'vader');
    ?>
        <select name= "kzlFase" id ="fase" onchange="toon_dracht()" style= "width:76;" > <?php
    foreach ( $optie_fase as $key =>$waarde)    
    {
        $keuze = '';
        if (isset($_POST['kzlFase']) && $_POST['kzlFase'] == $key)
        {
            $keuze = ' selected ';
        }
        echo '<option value="' . $key.'"' .$keuze .'>' . $waarde.'</option>';
    }
    ?>    
        </select>
    <sup> *</sup>
    <?php } ?>
     </td>
    </tr>

<!-- KZLGESLACHT -->
    <tr>
    <td> Geslacht :</td>
    <td>
    <?php if (isset($sekse_db)) { echo $sekse_db; } else { ?>

     <select name= "kzlSekse" id = "sekse" style= "width:59;" > 
    <?php 
    $opties = array('' => '', 'ooi' => 'ooi', 'ram' => 'ram', 'kween' => 'kween');
    foreach ( $opties as $key => $waarde)
    {
       $keuze = '';
       if (isset($_POST['kzlSekse']) && $_POST['kzlSekse'] == $key)
       {
            $keuze = ' selected ';
       }
       echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
    } ?>
     </select> 
    <sup> *</sup>
    <?php } ?>
     </td>
    </tr>

<!-- KZLRAS -->
    <tr>
    <td>Ras :</td>
    <td>
    <?php
    if (isset($ras_db)) { echo $ras_db; } else {

    $result = mysqli_query($db,"
    SELECT r.rasId, r.ras 
    FROM tblRas r
     join tblRasuser ru on (r.rasId = ru.rasId)
    WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and r.actief = 1 and ru.actief = 1
    ORDER BY r.ras
    "); # or die (mysqli_error($db)); ?>
     <select name= "kzlRas" id = "ras" style= "width:80;" >
     <option></option>
     <?php    while($row = mysqli_fetch_array($result))
            {
                $opties= array($row['rasId']=>$row['ras']);
                foreach ( $opties as $key => $waarde)
                {
                            $keuze = '';
            
            if (isset($_POST['kzlRas']) && $_POST['kzlRas'] == $key)
            {
                $keuze = ' selected ';
            }
                    
            echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
                }
            
            } ?>
     </select>

    <sup> *</sup>
    <?php } ?>
     </td>
    </tr>

<!-- KZLOOI -->
    <tr>
     <td> </td>    
     <td>
    <?php if (!isset($werknr_ooi_db)) { ?>
        <i><sub> Werknr - lammeren - halsnr </sub></i>
    <?php } ?>
     </td>
    </tr>

    <tr>
     <td> Werknr ooi (moeder) : </td>
     <td>
    <?php
    if (isset($werknr_ooi_db)) { echo $werknr_ooi_db; } else {
        $result = $stal_gateway->kzlOoien($lidId, $Karwerk);
?>

     <select name="kzlOoi" id ="moeder" style="width:100;" onfocus="kies_generatie()" onchange="toon_dracht()" >
     <option></option>    
    <?php    while($row = mysqli_fetch_array($result))
            {
                $opties= array($row['schaapId']=>$row['werknr'].'&nbsp &nbsp '.$row['lamrn'].'&nbsp &nbsp '.$row['halsnr']);
                foreach ( $opties as $key => $waarde)
                {
                            $keuze = '';
            
            if (isset($_POST['kzlOoi']) && $_POST['kzlOoi'] == $key)
            {
                $keuze = ' selected ';
            }
                    
            echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
                }
            
            } ?>
     </select> 
     <?php if ($modtech == 1) { ?> <sup> * / **</sup> <?php } ?>
     <input type = "hidden" name = "txtMaxmdr" size = 8 value = <?php if (isset($endmdr)) { echo $endmdr; } ?> >
     <!--<input type = "submit" name = "knpDracht" value = "Zoek vader" > (via dracht) -->

    <?php } ?>
     </td> <!-- hiddden -->
    </tr>

<!-- KZLRAM -->
    <tr> <td> Werknr ram (vader) : </td>

     <td> <?php
    if (isset($werknr_ram_db)) { echo $werknr_ram_db; } else {

    $resultvader = mysqli_query($db,"
    SELECT st.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk) werknr, s.indx
    FROM tblStal st 
     join tblSchaap s on (st.schaapId = s.schaapId)
     join tblHistorie h on (h.stalId = st.stalId)
    WHERE s.geslacht = 'ram' and h.actId = 3 and h.skip = 0 and lidId = '".mysqli_real_escape_string($db,$lidId)."'
    and not exists (
        SELECT st.schaapId
        FROM tblStal stal 
         join tblHistorie h on (h.stalId = stal.stalId)
         join tblActie  a on (a.actId = h.actId)
        WHERE stal.schaapId = s.schaapId and a.af = 1 and h.datum < DATE_ADD(CURDATE(), interval -1 year) and h.skip = 0 and lidId = '".mysqli_real_escape_string($db,$lidId)."')
    ORDER BY right(s.levensnummer,$Karwerk)
    "); # or die (mysqli_error($db)); ?>
     <select name= "kzlRam" style= "width:100; text-align:left;" id="vader" onfocus="toon_dracht()" >
     <option></option>    
    <?php    while($row = mysqli_fetch_array($resultvader))
            {
            
                $opties= array($row['schaapId']=>$row['werknr'].'&nbsp &nbsp '.$row['indx']);
                foreach ( $opties as $key => $waarde)
                {
                            $keuze = '';
            
            if ((isset($vaderId) && $vaderId == $key) || (isset($_POST['kzlRam']) && $_POST['kzlRam'] == $key))
            {
                $keuze = ' selected ';
            }
                    
            echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
                }
            
            } ?>
     </select><div id="result_vader"></div> 
    <?php } ?>
     </td>
    </tr>

<!--Geboortedatum -->
    <tr height = 40 > 
     <td valign = "bottom">Geboortedatum :</td>
     <td valign = "bottom">
    <?php if (isset($gebdm_db)) { echo $gebdm_db; } else { ?>

        <input id="datepicker1" name="txtGebdm" type="text" value = <?php if (isset($txtGebdm)) { echo $txtGebdm; } ?> > <div id="result_werpdatum"></div> 
    <?php } ?>
     </td>
    </tr>

<?php if ($modtech == 1) { ?>
    <tr>
     <td>Gewicht :</td>
     <td><input type= "text" id = "gewicht" name= "txtGebkg"  value = <?php if (isset($txtGebkg)) { echo $txtGebkg; } ?> > <sup> *</sup> </td>
    </tr>
<?php } ?>
    <tr>
     <td>Aanvoerdatum :</td>
     <td><input type= "text" id="datepicker2" name= "txtAanv" value = <?php if (isset($txtAanvdm)) { echo $txtAanvdm; } ?> ></td>
    </tr>
    </table>

 </td>
 <td width = 160> </td>
 <td valign = "top">

<!-- ********************* 
          OPMAAK MIDDEN 
      *********************-->
    <table border = 0>
    <th colspan = 2><i> UITVAL</i>
    </th>
<!-- moment uitval -->
    <tr>
     <td>Moment uitval :</td>
     <td>
    <?php
    $result = mysqli_query($db,"
    SELECT m.momId, m.moment
    FROM tblMoment m
     join tblMomentuser mu on (m.momId = mu.momId)
    WHERE mu.lidId = '".mysqli_real_escape_string($db,$lidId)."' and m.actief = 1 and mu.actief = 1
    ORDER BY m.momId
    "); # or die (mysqli_error($db)); ?>
     <select name="kzlMoment" id="moment" style="width:180;" >
     <option></option>    
    <?php    while($row = mysqli_fetch_array($result))
            {
                $opties= array($row['momId']=>$row['moment']);
                foreach ( $opties as $key => $waarde)
                {
                            $keuze = '';
            
            if (isset($_POST['kzlMoment']) && $_POST['kzlMoment'] == $key)
            {
                $keuze = ' selected ';
            }
                    
            echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
                }
            
            } ?>
     </select>
        <sup> **</sup>
     </td>
    </tr>

<!-- datum uitval -->
    <tr>
    <td>Datum uitval : </td>
    <td><input id="datepicker3" name="txtUitvdm" type="text" height= 50 value = <?php if (isset($txtUitvdm)) { echo $txtUitvdm; } ?> ><sup> **</sup></td>
    </tr>


<!-- KZLREDEN -->
    <tr>
     <td> Reden uitval :</td>
     <td> <?php
    $result = mysqli_query($db, "
    SELECT r.reden, ru.redId
    FROM tblReden r
     join tblRedenuser ru on (r.redId = ru.redId)
    WHERE r.actief = 1 and ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ru.uitval = 1
    ORDER BY r.reden
    "); # or die (mysqli_error($db)); ?>
     <select name= "kzlReden" id= "reden" style= "width:145;" >
     <option></option>
    <?php    while($row = mysqli_fetch_array($result))                    
            {
            
                $opties= array($row['redId']=>$row['reden']);
                foreach ( $opties as $key => $waarde)
                {
                            $keuze = '';
            
            if (isset($_POST['kzlReden']) && $_POST['kzlReden'] == $key)
            {
                $keuze = ' selected ';
            }
                    
            echo '<option value="' . $key . '" >' . $waarde . '</option>';
            //echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';  Gekozen waarde onthouden
                }
            
            } ?>
     </select>
     </td>
    </tr>
    <tr>
     <td height = 50> </td>
    </tr>

<?php if ($modtech == 1) { ?>
<!-- KZLHOK -->
    <tr>
     <td>Verblijf :</td>
     <td>
    <?php
    $result = mysqli_query($db," SELECT hokId, hoknr FROM tblHok WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actief = 1 ORDER BY hoknr "); # or die (mysqli_error($db)); ?>
     <select name="kzlHok" id="verblijf" style="width:100;" >
     <option></option>    
    <?PHP    while($row = mysqli_fetch_array($result))
            {
            
                $opties= array($row['hokId']=>$row['hoknr']/*.'&nbsp &nbsp &nbsp &nbsp '.$row['doel'].'&nbsp &nbsp &nbsp &nbsp &nbsp '.$row['nu']*/);
                foreach ( $opties as $key => $waarde)
                {
                            $keuze = '';
            
            if (isset($_POST['kzlHok']) && $_POST['kzlHok'] == $key)
            {
                $keuze = ' selected ';
            }
                    
            echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
                }
            
            } ?>
     </select> <sup> *</sup>
     </td>
    </tr>
<?php } ?>

    <tr height = 50>
     <td></td>
    </tr>

    <tr>
     <td colspan = 2>
     * Verplichte velden bij geboren lammeren.<br/>
     ** Verplicht bij overlijden.
     </td> 
    </tr>
    </table>

 </td>
 <?php if (isset($schaapId)) { ?>
 <td>
 <!-- ********************
         OPMAAK RECHTS
     ******************** -->
     <table border = 0 > <!-- tabel 9 : t.b.v. velden historie -->
    <tr>
     <td rowspan = 2 width = 100> </td>
     <td>Historie :</td>
    </tr>
    <tr>
     <td style = "font-size : 15px ;">
    <?php 
    $queryHistorie = mysqli_query($db,"
    SELECT date_format(datum,'%d-%m-%Y') dag, h.actId, actie, datum
    FROM tblSchaap s
     join tblStal st on (s.schaapId = st.schaapId)
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (a.actId = h.actId)
    WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and h.skip = 0
    and not exists (
        SELECT datum 
        FROM tblHistorie geenAanwas 
         join tblStal st on (geenAanwas.stalId = st.stalId)
        WHERE actId = 2 and h.datum = geenAanwas.datum and h.actId = geenAanwas.actId+1 and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)/* bij aankoop incl. aanwas wordt aanwas niet getoond */."')

    union

    SELECT date_format(datum,'%d-%m-%Y') dag, h.actId, actie, datum
    FROM tblSchaap s
     join tblStal st on (s.schaapId = st.schaapId)
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (a.actId = h.actId)
    WHERE h.actId = 1 and h.skip = 0 and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'

    union

    SELECT date_format(p.dmafsluit,'%d-%m-%Y') dag, h.actId, 'Gevoerd' actie, p.dmafsluit
    FROM tblVoeding v    
     join tblPeriode p on (p.periId = v.periId)
     join tblBezet b on (p.periId = b.periId)
     join tblHistorie h on (h.hisId = b.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join tblSchaap s on (s.schaapId =st.schaapId)
    WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'

    union

    SELECT date_format(min(h.datum),'%d-%m-%Y') dag, h.actId, 'Eerste worp' actie, min(h.datum) datum
    FROM tblSchaap s
     join tblVolwas v on (s.schaapId = v.mdrId)
     join tblSchaap lam on (v.volwId = lam.volwId)
     join tblStal st on (st.schaapId = lam.schaapId)
     join tblHistorie h on (st.stalId = h.stalId and h.actId = 1 and h.skip = 0)
    WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
    GROUP BY h.actId

    union

    SELECT date_format(max(h.datum),'%d-%m-%Y') dag, h.actId, 'Laatste worp' actie, max(h.datum) datum
    FROM tblSchaap s
     join tblVolwas v on (s.schaapId = v.mdrId)
     join tblSchaap lam on (v.volwId = lam.volwId)
     join tblStal st on (st.schaapId = lam.schaapId)
     join tblHistorie h on (st.stalId = h.stalId and h.actId = 1 and h.skip = 0)
    WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
    GROUP BY h.actId
    HAVING (max(h.datum) > min(h.datum))

    union

    SELECT date_format(rs.dmcreate,'%d-%m-%Y') dag, h.actId, 'Geboorte gemeld' actie, rs.dmcreate
    FROM impRespons rs
     join tblMelding m on (rs.reqId = m.reqId)
     join tblHistorie h on (m.hisId = h.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join tblSchaap s on (s.schaapId = st.schaapId and s.levensnummer = rs.levensnummer)
    WHERE rs.melding = 'GER' and rs.meldnr is not null and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'

    union

    SELECT date_format(rs.dmcreate,'%d-%m-%Y') dag, h.actId, 'Aanvoer gemeld' actie, rs.dmcreate
    FROM impRespons rs
     join tblMelding m on (rs.reqId = m.reqId)
     join tblHistorie h on (m.hisId = h.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join tblSchaap s on (s.schaapId = st.schaapId and s.levensnummer = rs.levensnummer)
    WHERE rs.melding = 'AAN' and rs.meldnr is not null and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'

    union

    SELECT date_format(rs.dmcreate,'%d-%m-%Y') dag, h.actId, 'Afvoer gemeld' actie, rs.dmcreate
    FROM impRespons rs
     join tblMelding m on (rs.reqId = m.reqId)
     join tblHistorie h on (m.hisId = h.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join tblSchaap s on (s.schaapId = st.schaapId and s.levensnummer = rs.levensnummer)
    WHERE rs.melding = 'AFV' and rs.meldnr is not null and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'

    union

    SELECT date_format(rs.dmcreate,'%d-%m-%Y') dag, h.actId, 'Uitval gemeld' actie, rs.dmcreate
    FROM impRespons rs
     join tblMelding m on (rs.reqId = m.reqId)
     join tblHistorie h on (m.hisId = h.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join tblSchaap s on (s.schaapId = st.schaapId and s.levensnummer = rs.levensnummer)
    WHERE rs.melding = 'DOO' and rs.meldnr is not null and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'

    ORDER BY datum desc, actId desc
    "); # or die (mysqli_error($db));

    while ($h = mysqli_fetch_assoc($queryHistorie)) { $da = $h['dag']; $ac = $h['actie'];  echo $da." - ".$ac."<br>"; } ?>
     </td>
    </tr>
    </table><!-- Einde tabel 9 : t.b.v. velden historie -->

 </td>
<?php  } // Einde if (isset($schaapId)) ?>
</tr>
<tr> 
 <td colspan = 4 align = "center"><input type = "submit" name = "knpSave" onfocus = "verplicht()" value = "Opslaan" ></td>
</tr>
</form>

</table>


        </TD>    
    
<?php    
        include "menu1.php";
}
?>
</tr>

</table>

</body>
</html>
