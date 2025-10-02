<?php

require_once("autoload.php");

/* 20-2-2015 : login toegevoegd
14-11-2015 Eerste en tweede inenting verwijderd
18-11-2015 Hok gewijzigd naar verblijf
23-11-2015 Spenen afleveren mogelijk gemaakt en link 'periode afsluiten verplaatst naar achteren'
19-12-2015 : link 'hok overpl' gewijzigd naar overpl */
$versie = "18-1-2017"; /* Query's aangepast n.a.v. nieuwe tblDoel Aantal nu in hok gewijzigd van count(distinct st.schaapId)-count(distinct uit.schaapId) naar count(b.bezId)-count(uit.bezId) zodat terugplaatsen ook zichtbaar is. */
$versie = "22-1-2017"; /* tblBezetting gewijzigd naar tblBezet */
$versie = "5-2-2017"; /* Aanpassing n.a.v. verblijven met verschillende doelgroepen */
$versie = "12-2-2017"; /* Bij historie lammeren H1.ACTID != 2 toegevegd. Bij aankoop moederdieren bestaat act 2 en act 3 waardoor dit dier in het hok heeft gezeten van aankoop t/m aanwas als dier 'zonder' aanwas datum. Wordt ooit een lam aangekocht maak dan een nieuwe actie hiervoor aan in tblActie !!!!!!!!!!!!!!!! */
$versie = "29-12-2017"; /* Aantal aanwezige volwassen dieren toegevoegd */
$versie = "13-05-2018"; /* Session::set("DT1", NULL); Session::set("BST", NULL);  toegevoegd */
$versie = '28-09-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '18-05-2019'; /* Afleveren, spenen en Overplaatsen mogelijk gemaakt via Hoklijsten.php */
$versie = '20-12-2019'; /* tabelnaam gewijzigd van UIT naar uit tabelnaam */
$versie = '28-6-2020'; /* datum in verblijf van volwassen dieren toegevoegd zodat link 'periode sluiten' zichtbaar wordt bij verblijven met enkel volwassen dieren */
$versie = '8-2-2021'; /* zoek_nu_in_verblijf_prnt herschreven i.v.m. dubbele records. Sql beveiligd met quotes */
$versie = '4-6-2021'; /* Verblijf ook zichtbaar als enkel volwassen dieren in het verblijf hebben gezeten */
$versie = '9-7-2021'; /* Schapen uit verblijf herzien. Join gewijzigd van h.hisId = uit.hisv naar b.bezId = uit.bezId */
$versie = '4-8-2021'; /* Schapen die 0 dagen in verblijf zitten ook meegeteld. Zie bijv (h.datum = spn.datum && h.hisId >= spn.hisId) */
$versie = '23-12-2023'; /* In query zoek_nu_in_verblijf_prnt skip = 0 toegevoegd. Vandaag is bij Folkert een herstel actie uitgevoerd n.a.v. toevoegen speendatum op 17-12 jl. Alle 116 overplaatsingen zijn verwijderd (skip = 1) 27-12-2023 and skip = 0 toegevoegd bij tblHistorie */
$versie = '05-01-2024'; /* Schapen die in het verblijf spenen de status aanwas kregen werden niet getoond. Dit is aangepast
7-1-2024 : Aanwas werd onterecht aan een verblijf gekoppeld waardoor volwassendieren dubbel werden geteld in de kolom Volwassen aanwezig.
Dit is voor de toekomst aangepast in save_aanwas.php. Met distinct in zoek_nu_in_verblijf_prnt is dit ook met bestaande registraties hersteld
14-01-2024 Doelgroep verlaten telden ook volwassen dieren die niet in het verblijf hadden gezeten. Dit is aangepast door bij zoek_verlaten_spn_excl_overpl_en_uitval or (isnull(uit.bezId) and prnt.schaapId is not null)) uit te breiden naar or (isnull(uit.bezId) and prnt.schaapId is not null and h.datum < spn.datum)) */
$versie = '19-01-2024'; /* in nestquery 'uit' is 'and a1.aan = 1' uit WHERE gehaald. De hisId die voorkomt in tblBezet volstaat. Bovendien is bij Pieter hisId met actId 3 gekoppeld aan tblBezet en heeft het veld 'aan' in tblActie de waarde 0. De WHERE incl. 'and a1.aan = 1' geeft dus een fout resultaat. */
$versie = "10-03-2024"; /* De aantallen in kolom aanwezigen blauw gemaakt */
$versie = "11-03-2024"; /* Bij geneste query uit
join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId) gewijzgd naar
join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
I.v.m. historie van stalId 22623. Dit dier is eerst verkocht en met terugwerkende kracht geplaatst in verblijf Afmest 1 */
$versie = '31-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */

Session::start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Actueel</title>
</head>
<body>
<?php
$titel = 'Verblijven in gebruik';
$file = "Bezet.php";
include "login.php";
?>
        <TD valign = "top">
<?php
if (Auth::is_logged_in()) {
    $bezet_gateway = new BezetGateway();
    $aantal_zonder_speendatum = $bezet_gateway->zoek_verblijven_ingebruik_zonder_speendm($lidId);
    $aantal_met_speendatum = $bezet_gateway->zoek_verblijven_ingebruik_met_speendm($lidId);
    $aantal_zonder_verblijf = $bezet_gateway->zoek_schapen_zonder_verblijf($lidId);
    $zoek_verblijven_in_gebruik = $bezet_gateway->zoek_verblijven_in_gebruik($lidId);
    $periode_gateway = new PeriodeGateway();

    $closure = function ($row) use ($periode_gateway, $bezet_gateway) {
        // BCB: da's 12 queries per rij. Dat kon wel eens langzaam zijn ja.
        // TODO: (BV) #0004143 grote datasets opstellen tbv test, en dan kijken of indexen het sneller maken
        # rijdata gaat naar de view als $row, de extra berekeningen in $extra
        $extra = [];
        $dmstopgeb = $periode_gateway->zoek_laatste_afsluitdm_geb($row['hokId']);
        if (!isset($dmstopgeb)) {
            $dmstopgeb = '1973-09-11';
        }
        $dmstopspn = $periode_gateway->zoek_laatste_afsluitdm_spn($row['hokId']);
        if (!isset($dmstopspn)) {
            $dmstopspn = '1973-09-11';
        }
        $aanwezig1 = $bezet_gateway->zoek_nu_in_verblijf_geb($row['hokId']);
        $aanwezig2 = $bezet_gateway->zoek_nu_in_verblijf_spn($row['hokId']);
        $extra['aanwezig'] = $aanwezig1 + $aanwezig2;
        $extra['aanwezig3'] = $bezet_gateway->zoek_nu_in_verblijf_prnt($row['hokId']);
        $aanwezig_incl = $extra['aanwezig'] + $extra['aanwezig3']; // wordt niet gebruikt
        $uit_geb = $bezet_gateway->zoek_verlaten_geb_excl_overpl_en_uitval($row['hokId'], $dmstopgeb);
        $uit_spn = $bezet_gateway->zoek_verlaten_spn_excl_overpl_en_uitval($row['hokId'], $dmstopspn);
        $extra['uit'] = $uit_geb + $uit_spn;
        $overpl_geb = $bezet_gateway->zoek_overplaatsing_geb($row['hokId'], $dmstopgeb);
        $overpl_spn = $bezet_gateway->zoek_overplaatsing_spn($row['hokId'], $dmstopspn);
        $extra['overpl'] = $overpl_geb + $overpl_spn;
        $uitval1 = $bezet_gateway->zoek_overleden_geb($row['hokId'], $dmstopgeb);
        $uitval2 = $bezet_gateway->zoek_overleden_spn($row['hokId'], $dmstopspn);
        $extra['uitval'] = $uitval1 + $uitval2;
        $extra['mdrs'] = $bezet_gateway->zoek_moeders_van_lam($row['hokId']);
        $extra['dmvan'] = '';
        $extra['van'] = '';
        $extra['tot'] = '';
        if (isset($row['eerste_in'])) {
            $datum = date_create($row['eerste_in']);
            $extra['van'] = date_format($datum, 'd-m-Y');
            $extra['dmvan'] = date_format($datum, 'Y-m-d');
            $extra['today'] = date('Y-m-d');
        }
        if (isset($row['laatste_uit'])) {
            $datum = date_create($row['laatste_uit']);
            $extra['tot'] = date_format($datum, 'd-m-Y');
        }
        return $extra;
    };

    View::render('bezet/list', compact(explode(' ', 'aantal_zonder_verblijf aantal_met_speendatum aantal_zonder_speendatum zoek_verblijven_in_gebruik closure')));
?>
</TD>
<?php
    include "menu1.php";
}
?>
</tr>
</table>
</body>
</html>
