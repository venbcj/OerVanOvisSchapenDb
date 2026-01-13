<?php

# WERKBOEK
# Deze pagina krijgt eenzelfde behandeling als Melden.
#
require_once("autoload.php");
$versie = '10-4-2014'; /*vw_Reader_sp wordt gebruikt in InsSpenen*/
$versie = '13-4-2014'; /*vw_Reader_ovpl wordt gebruikt in InsOverplaatsen */
$versie = '20-2-2015'; /*login toegevoegd*/
$versie = '18-11-2015'; /*gewijzigd inlezen aanwas naar inlezen aanvoer en inlezen locatie naar inlezen verblijf*/
$versie = '16-9-2016'; /*overschrijven van reader.txt gewijzigd in aanvullen*/
$versie = '22-6-2018'; /*Velden in impReader aangepast*/
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '2-2-2020'; /* De root naar alle bestanden op de FTP server variabel gemaakt */
$versie = '15-3-2020'; /* Onderscheid gemaakt tussen reader Agrident en Biocontrol */
$versie = '4-6-2020'; /* Overleggen gewijzigd in adoptie */
$versie = '30-9-2020'; /* Halsnummers toegevoegd */
$versie = '14-11-2020'; /* Medicatie aangepast i.v.m. mogelijk vanuit reader Agrident */
$versie = '20-06-2021'; /* Voerregistratie toegevoegd */
$versie = '18-12-2021'; /* Dekken en Dracht toegevoegd */
$versie = '05-08-2023'; /* Stallijstscan toegevoegd */
$versie = '02-12-2023'; /* Tussenweging toegevoegd */
$versie = '03-11-2024'; /* Uitscharen en terug van uitscharen toegevoegd */
$versie = '21-12-2024'; /* Bestanden uploaden van raeder Biocontrol verwijderd */
$versie = '31-12-2024'; /* include login voor include header gezet */
$versie = '28-02-2025'; /* Als de stallijst leeg is wordt de link inlezen stallijst nieuwe klant ook getoond */
Session::start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Registratie</title>
</head>
<body>
<?php
$titel = 'Inlezen reader';
$file = "InlezenReader.php";
include "login.php";
?>
         <TD>
<?php
if (Auth::is_logged_in()) {
    include "responscheck.php";
?>
 <form action="#" method="post" enctype="multipart/form-data">
<?php
$impagrident_gateway = new ImpAgridentGateway();

$aantNewLid = $impagrident_gateway->count_stallijstscan_new_lid($lidId);
$aantdek = $impagrident_gateway->count_zoek_dekken($lidId);
$aantdra = $impagrident_gateway->count_zoek_dracht($lidId);
$aantgeb = $impagrident_gateway->count_lammeren($lidId);
$aantLbar = $impagrident_gateway->count_lambar($lidId);
$aantspn = $impagrident_gateway->count_gespeenden($lidId);
$aantafl = $impagrident_gateway->count_afgeleverden($lidId);
$aantUitsch = $impagrident_gateway->count_uitgeschaarden($lidId);
$aantuitv = $impagrident_gateway->count_uitgevallen($lidId);
$aantaanw = $impagrident_gateway->count_aanvoer($lidId);
$aantTvUitsch = $impagrident_gateway->count_TvUitscharen($lidId);
$aantovpl = $impagrident_gateway->count_overplaatsen($lidId);
$speen_ovpl = $impagrident_gateway->count_SpenenEnOverpl($lidId);
$aantadop = $impagrident_gateway->count_adoptie($lidId);
$aantpil = $impagrident_gateway->count_medicijn($lidId);
$aantwg = $impagrident_gateway->count_wegingen($lidId);
$aantomn = $impagrident_gateway->count_omnummer($lidId);
$aanthals = $impagrident_gateway->count_halsnummer($lidId);
$aantvoer = $impagrident_gateway->count_voerregistratie($lidId);
$aantubn = $impagrident_gateway->count_wijzigingen_ubn($lidId);
$aantscan = $impagrident_gateway->count_stallijstscan_controle($lidId);
    # NOTE: dit brengt een armvol variabelen in scope.
    # Ik wil er een functie van maken, en dan een array teruggeven:
    # [A ] nu komt bijvoorbeeld $speen_ovpl terug,
    # [ Z] dan krijg je $aantallen['speen_ovpl']
    # In tweede instantie wil ik dat nog verder verkleinen, want 22 count-queries met vrijwel gelijke voorwaarden is
    #  - niet efficient
    #  - niet communicatief (ubn is anders; hoe, waarom?; twee soorten aanvoer; spenen en overplaatsen gaan kennelijk samen)
    #  Alle delen die wel op dezelfde manier werken, bundelen we in een GROUP BY actId. Dan krijg je vanzelf al een array.
    #  De actId-waarden vertalen naar betekenisvolle namen kan eenvoudig daarna.
    $lid_gateway = new LidGateway();
    $stallijstaantal = $lid_gateway->zoek_lege_stallijst($lidId);
?>
<table border = 0 align="center" style = "font-size: 17px"; >
   <h2 align="center" style="color:blue";>Hier kun je de gegevens uit de reader verwerken<br> in het managementprogramma.</h2>
<tr height = 50 ><td></td> </tr>
<?php
    // TODO #0004215 geen slimmigheden met halve html-tags,
    // maar uitvoeren als View::link_to met een conditionele $path-parameter
    $leeg = "<a href=' " . Url::getWebroot() . "InlezenReader.php' style = 'color : blue'>";
    if ($aantNewLid > 0 || $stallijstaantal == 0) {
?>
<tr height = 50 valign="top">
 <td>
<?php
        if (!empty($aantNewLid)) {
?>
 <a href='<?= Url::getWebroot();?>InsStallijstscan_nieuwe_klant.php' style = 'color : blue'>
<?php
        } else {
            echo "$leeg";
        }
?>
inlezen stallijst nieuwe klant </a>
 </td>
 <td style = "font-size : 14px;">
<?php
        if (!empty($aantNewLid)) {
            echo "&nbsp $aantNewLid dieren in te lezen.";
        }
?>
 </td>
</tr>
<?php
    }
?>
<tr>
 <td>
<?php
    if (!empty($aantdek)) {
?>
 <a href='<?= Url::getWebroot();?>InsDekken.php' style = 'color : blue'>
<?php
    } else {
        echo "$leeg";
    }
?>
inlezen dekken </a>
 </td>
 <td style = "font-size : 14px;">
<?php
    if (!empty($aantdek)) {
        echo "&nbsp $aantdek dekkingen in te lezen.";
    }
?>
 </td>
</tr>
<tr>
 <td>
<?php
    if (!empty($aantdra)) {
?>
 <a href='<?= Url::getWebroot();?>InsDracht.php' style = 'color : blue'>
<?php
    } else {
        echo "$leeg";
    }
?>
inlezen dracht </a>
 </td>
 <td style = "font-size : 14px;">
<?php
    if (!empty($aantdra)) {
        echo "&nbsp $aantdra dracht in te lezen.";
    }
?>
 </td>
</tr>
<tr>
 <td>
<?php
    if (!empty($aantgeb)) {
?>
 <a href='<?= Url::getWebroot();?>InsGeboortes.php' style = 'color : blue'>
<?php
    } else {
        echo "$leeg";
    }
?>
inlezen geboortes </a>
 </td>
 <td style = "font-size : 14px;">
<?php
    if (!empty($aantgeb)) {
        echo "&nbsp $aantgeb geboorte(s) in te lezen.";
    }
?>
 </td>
</tr>
<?php
    if ($reader == 'Agrident') { 
?>
<tr>
 <td>
<?php
        if (!empty($aantLbar)) {
?>
 <a href='<?= Url::getWebroot();?>InsLambar.php' style = 'color : blue'>
<?php
        } else {
            echo "$leeg";
        }
?>
inlezen lambar </a>
 </td>
 <td style = "font-size : 14px;">
<?php
        if (!empty($aantLbar)) {
            echo "&nbsp $aantLbar lambar in te lezen.";
        }
?>
 </td>
</tr>
<?php
    }
?>
<tr>
 <td>
<?php
    if (!empty($aantspn)) {
?>
 <a href='<?= Url::getWebroot();?>InsSpenen.php' style = 'color : blue'>
<?php
    } else {
        echo "$leeg";
    }
?>
inlezen gespeenden </a>
 </td>
 <td style = "font-size : 14px;">
<?php
    if (!empty($aantspn)) {
        echo "&nbsp $aantspn gespeenden in te lezen.";
    }
?>
 </td>
</tr>
<tr>
 <td>
<?php
    if (!empty($aantwg)) {
?>
 <a href='<?= Url::getWebroot();?>InsWegen.php' style = 'color : blue' >
<?php
    } else {
        echo "$leeg";
    }
?>
inlezen wegingen </a>
 </td>
 <td style = "font-size : 14px;">
<?php
    if (!empty($aantwg)) {
        echo "&nbsp $aantwg wegingen in te lezen.";
    }
?>
 </td>
</tr>
<tr>
 <td>
<?php
    if (!empty($aantafl)) {
?>
<a href='<?= Url::getWebroot();?>InsAfvoer.php' style = 'color : blue'>
<?php
    } else {
        echo "$leeg";
    }
?>
inlezen afvoer </a>
 </td>
 <td style = "font-size : 14px;">
<?php
    if (!empty($aantafl)) {
        echo "&nbsp $aantafl afgeleverden in te lezen.";
    }
?>
 </td>
</tr>
<tr>
 <td>
<?php
    if (!empty($aantUitsch)) {
?>
<a href='<?= Url::getWebroot();?>InsUitscharen.php' style = 'color : blue'>
<?php
    } else {
        echo "$leeg";
    }
?>
inlezen uitscharen </a>
 </td>
 <td style = "font-size : 14px;">
<?php
    if (!empty($aantUitsch)) {
        echo "&nbsp $aantUitsch afgeleverden in te lezen.";
    }
?>
 </td>
</tr>
<tr>
 <td>
<?php
    if (!empty($aantuitv)) {
?>
<a href='<?= Url::getWebroot();?>InsUitval.php' style = 'color : blue'>
<?php
    } else {
        echo "$leeg";
    }
?>
inlezen uitval </a>
 </td>
 <td style = "font-size : 14px;">
<?php
    if (!empty($aantuitv)) {
        echo "&nbsp $aantuitv uitval in te lezen.";
    }
?>
 </td>
</tr>
<tr>
 <td>
<?php
    if (!empty($aantaanw)) {
?>
 <a href='<?= Url::getWebroot();?>InsAanvoer.php' style = 'color : blue'>
<?php
    } else {
        echo "$leeg";
    }
?>
inlezen aanvoer </a>
 </td>
 <td style = "font-size : 14px;">
<?php
    if (!empty($aantaanw)) {
        echo "&nbsp $aantaanw aanwas in te lezen.";
    }
?>
 </td>
</tr>
<tr>
 <td>
<?php
    if (!empty($aantTvUitsch)) {
?>
 <a href='<?= Url::getWebroot();?>InsTvUitscharen.php' style = 'color : blue'>
<?php
    } else {
        echo "$leeg";
    }
?>
inlezen terug van uitscharen </a>
 </td>
 <td style = "font-size : 14px;">
<?php
    if (!empty($aantTvUitsch)) {
        echo "&nbsp $aantTvUitsch terug van uitscharen in te lezen.";
    }
?>
 </td>
</tr>
<tr>
 <td>
<?php
    if (!empty($aantovpl)) {
?>
 <a href='<?= Url::getWebroot();?>InsOverplaats.php' style = 'color : blue'>
<?php
    } else {
        echo "$leeg";
    }
?>
inlezen overplaatsen </a>
 </td>
 <td style = "font-size : 14px;">
<?php
    if (!empty($aantovpl) && empty($speen_ovpl)) {
        echo "&nbsp $aantovpl overplaatsingen in te lezen.";
    } elseif (!empty($aantovpl) && $speen_ovpl == 1) {
        echo "&nbsp $aantovpl overplaatsingen in te lezen waarvan er $speen_ovpl eerst moet worden gespeend. *";
    } elseif (!empty($aantovpl) && $speen_ovpl > 1) {
        echo "&nbsp $aantovpl overplaatsingen in te lezen waarvan er $speen_ovpl eerst moeten worden gespeend. *";
    }
?>
 </td>
</tr>
<tr>
 <td>
<?php
    if (!empty($aantadop)) {
?>
 <a href='<?= Url::getWebroot();?>InsAdoptie.php' style = 'color : blue'>
<?php
    } else {
        echo "$leeg";
    }
?>
inlezen adoptie </a>
 </td>
 <td style = "font-size : 14px;">
<?php
    if (!empty($aantadop)) {
        echo "&nbsp $aantadop adoptie in te lezen.";
    }
?>
 </td>
</tr>
<tr>
 <td>
<?php
    if (!empty($aantpil)) {
?>
 <a href='<?= Url::getWebroot();?>InsMedicijn.php' style = 'color : blue' >
<?php
    } else {
        echo "$leeg";
    }
?>
inlezen medicatie </a>
 </td>
 <td style = "font-size : 14px;">
<?php
    if (!empty($aantpil)) {
        echo "&nbsp $aantpil medicatie in te lezen.";
    }
?>
 </td>
</tr>
<tr>
 <td>
<?php
    if (!empty($aantomn)) {
?>
 <a href='<?= Url::getWebroot();?>InsOmnummeren.php' style = 'color : blue'>
<?php
    } else {
        echo "$leeg";
    }
?>
inlezen omnummeren </a>
 </td>
 <td style = "font-size : 14px;">
<?php
    if (!empty($aantomn)) {
        echo "&nbsp $aantomn omnummeren in te lezen.";
    }
?>
 </td>
</tr>
<tr>
 <td>
<?php
    if (!empty($aanthals)) {
?>
 <a href='<?= Url::getWebroot();?>InsHalsnummers.php' style = 'color : blue'>
<?php
    } else {
        echo "$leeg";
    }
?>
inlezen halsnummers </a>
 </td>
 <td style = "font-size : 14px;">
<?php
    if (!empty($aanthals)) {
        echo "&nbsp $aanthals halsnummers in te lezen.";
    }
?>
 </td>
</tr>
<tr>
 <td>
<?php
    if (!empty($aantvoer)) {
?>
 <a href='<?= Url::getWebroot();?>InsVoerregistratie.php' style = 'color : blue'>
<?php
    } else {
        echo "$leeg";
    }
?>
inlezen voerregistratie </a>
 </td>
 <td style = "font-size : 14px;">
<?php
    if (!empty($aantvoer)) {
        echo "&nbsp $aantvoer voerregistraties in te lezen.";
    }
?>
 </td>
</tr>
<tr>
 <td>
<?php
    if (!empty($aantubn)) {
?>
 <a href='<?= Url::getWebroot();?>InsGrWijzigingUbn.php' style = 'color : blue'>
<?php
    } else {
        echo "$leeg";
    }
?>
inlezen ubn wijziging </a>
 </td>
 <td style = "font-size : 14px;">
<?php
    if (!empty($aantubn)) {
        echo "&nbsp $aantubn ubn wijzigingen in te lezen.";
    }
?>
 </td>
</tr>
<tr>
 <td>
<?php
    if (!empty($aantscan)) {
?>
 <a href='<?= Url::getWebroot();?>InsStallijstscan_controle.php' style = 'color : blue'>
<?php
    } else {
        echo "$leeg";
    }
?>
inlezen stallijstscan </a>
 </td>
 <td style = "font-size : 14px;">
<?php
    if (!empty($aantscan)) {
        echo "&nbsp $aantscan stallijstscans in te lezen.";
    }
?>
 </td>
</tr>
</table>
<br><br><br>
<table>
<tr><td style = "font-size : 13px ;">
<?php
    if (!empty($aantovpl) && $speen_ovpl > 0) {
?>
 * Mogelijk moeten schapen worden herverdeeld na het spenen.<br>
 Deze herverdeling (= functie locatie in reader) gebeurt gelijktijdig met het inlezen van gespeende lammeren.
<?php
    }
?>    </td>
</tr>
</table>
</form>
    </TD>
<?php
    include "menu1.php";
}
?>
</tr>
</table>
</body>
</html>
