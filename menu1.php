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
29-8-2021 : msg.php gewijzigd naar javascriptsAfhandeling.tpl.php
25-12-2021 : Dracht.php hernoemd naar Dekkingen.php 11-1-2022 kleur link variabel gemaakt
22-10-2023 : Menu optie Beheer kleur rood als er nog een nieuwe readerversie moet worden gedownload
23-10-2024 : Invoer nieuwe schapen gewijzigd naar Aanvoer schaap
  */

include "url.php";

$tech_color = 'grey';
if ($modtech != 0) {
    $tech_color = 'blue';
}

$meld_color = 'grey';
if ($modmeld != 0) {
    $meld_color = 'blue';
    // Kijken of er nog meldingen openstaan
    $req_open = mysqli_query($db, "
SELECT count(*) aant
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = ".mysqli_real_escape_string($db, $lidId)." and h.skip = 0 and isnull(r.dmmeld) and m.skip <> 1 ");
    $row = mysqli_fetch_assoc($req_open);
    if ($row['aant'] > 0) {
        $meld_color = 'red';
    }
}

$beheer_color = 'red';
if (isset($actuele_versie) || $reader != 'Agrident') {
    $beheer_color = 'blue';
}

include "javascriptsAfhandeling.tpl.php";
?>

<link rel="stylesheet" href="menu.css">
<td width = '150' height = '100' valign='top'>
Menu : <br>
<hr class="blue">

<?php echo View::link_to('Home', 'Home.php', ['class' => 'blue']); ?>
<hr class="grey">

<?php echo View::link_to('Aanvoer schaap', 'InvSchaap.php', ['class' => 'blue']); ?>
<hr class="grey">

<?php echo View::link_to('Inlezen reader', 'InlezenReader.php', ['class' => 'blue']); ?> 
<hr class="grey">

<?php echo View::link_to('RVO', 'Melden.php', ['class' => $meld_color]) ?>
<hr class="grey">

<?php if ($modtech == 0 && $modmeld == 1) { ?>
<?php echo View::link_to('Afvoerlijst', 'Afvoerstal.php', ['class' => 'blue']); ?>
<?php } else { ?>
<?php echo View::link_to('Verblijven in gebruik', 'Bezet.php', ['class' => 'blue']); ?>
<?php } ?>
<hr class="grey">

<?php echo View::link_to('Schaap opzoeken', 'Zoeken.php', ['class' => 'blue']); ?>
<hr class="grey">

<?php echo View::link_to('Medicijn toediening', 'Med_registratie.php', ['class' => $tech_color]); ?>
<hr class="grey">

<?php echo View::link_to('Dekkingen / Dracht', 'Dekkingen.php', ['class' => 'blue']); ?>
<hr class="grey">

<?php echo View::link_to('Raederalerts', 'Alerts.php', ['class' => $tech_color]); ?>
<hr class="grey">

<?php echo View::link_to('Rapporten', 'Rapport.php', ['class' => 'blue']); ?>
<hr class="grey">

<?php echo View::link_to('Beheer', 'Beheer.php', ['class' => $beheer_color]); ?>
<hr class="grey">

<?php echo View::link_to('Voorraadbeheer', 'Inkoop.php', ['class' => $tech_color]); ?>
<hr class="grey">

<?php echo View::link_to('Financiëel', 'Finance.php', ['class' => $tech_color]); ?>
<hr class="grey">

<?php include "versie.tpl.php"; ?>
</td>
<!-- einde -->
