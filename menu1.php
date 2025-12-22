<!-- menu1 -->
<?php
 /*
  6-11-2014 Melden RVO toegevoegd
26-2-2015 url aangepast
14-11-2015 naamwijziging van Inkoop naar Voorraadbeheer en Medicijn registratie naar Medicijn toediening
18-11-2015 Hok gewijzigd naar verblijf
6-12-2015 :  versie toegveoged
19-12-2015 : query moduleFinancieel verplaatst naar login.php
20-12-2020 : Alerts toegevoegd
29-8-2021 : msg.php gewijzigd naar javascriptsAfhandeling.js.php
25-12-2021 : Dracht.php hernoemd naar Dekkingen.php 11-1-2022 kleur link variabel gemaakt
22-10-2023 : Menu optie Beheer kleur rood als er nog een nieuwe readerversie moet worden gedownload
23-10-2024 : Invoer nieuwe schapen gewijzigd naar Aanvoer schaap
  */

$tech_color = 'grey';
if ($modtech != 0) {
    $tech_color = 'blue';
}

$meld_color = 'grey';
if ($modmeld != 0) {
    $meld_color = 'blue';
    // Kijken of er nog meldingen openstaan
    $request_gateway = new RequestGateway();
    if ($request_gateway->hasOpenRequests($lidId)) {
        $meld_color = 'red';
    }
}

$beheer_color = 'red';
if (isset($actuele_versie) || $reader != 'Agrident') {
    $beheer_color = 'blue';
}

include "javascriptsAfhandeling.js.php";
$menu_items = [
    (object)['caption' => 'Home', 'href' => 'Home.php', 'class' => 'blue'],
    (object)['caption' => 'Aanvoer schaap', 'href' => 'InvSchaap.php', 'class' => 'blue'],
    (object)['caption' => 'Inlezen reader', 'href' => 'InlezenReader.php', 'class' => 'blue'],
    (object)['caption' => 'RVO', 'href' => 'Melden.php', 'class' => $meld_color],
    (object)['caption' => 'Afvoerlijst', 'href' => 'Afvoerstal.php', 'class' => 'blue', 
    'if' => (!$modtech && $modmeld)],
    (object)['caption' => 'Verblijven in gebruik', 'href' => 'Bezet.php', 'class' => 'blue',
    'if' => !(!$modtech && $modmeld)],
    (object)['caption' => 'Schaap opzoeken', 'href' => 'Zoeken.php', 'class' => 'blue'],
    (object)['caption' => 'Medicijn toediening', 'href' => 'Med_registratie.php', 'class' => $tech_color],
    (object)['caption' => 'Dekkingen / Dracht', 'href' => 'Dekkingen.php', 'class' => 'blue'],
    (object)['caption' => 'Raederalerts', 'href' => 'Alerts.php', 'class' => $tech_color],
    (object)['caption' => 'Rapporten', 'href' => 'Rapport.php', 'class' => 'blue'],
    (object)['caption' => 'Beheer', 'href' => 'Beheer.php', 'class' => 'blue'],
    (object)['caption' => 'Voorraadbeheer', 'href' => 'Inkoop.php', 'class' => 'blue'],
    (object)['caption' => 'Financiëel', 'href' => 'Finance.php', 'class' => 'blue'],
];
$menu_items = array_filter($menu_items, function ($item) {
    return !isset($item->if) || $item->if;
});
?>

<link rel="stylesheet" href="menu.css">
<td width = '150' height = '100' valign='top'>
Menu : <br>
<hr class="blue">

<?php
foreach ($menu_items as $item) :
    if ($item) {
        echo View::link_to($item->caption, $item->href, ['class' => $item->class]);
    } else {
        echo '<br/>';
    }
echo PHP_EOL.'<hr class="grey">'.PHP_EOL;
endforeach; ?>

<?php include "versie.tpl.php"; ?>
</td>
