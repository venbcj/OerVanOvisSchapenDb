<?php

$tech_color = 'grey';
if ($modtech != 0) {
    $tech_color = 'blue';
}

$fin_color = 'grey';
if ($modfin == 1) {
    $fin_color = 'black';
}

$meld_color = 'grey';
if ($modmeld != 0) {
    // NOTE: kleur is hier zwart, in menu1 (en zo) blauw. Misschien stijlen hernoemen naar 'inactief', 'actief', 'attentie' --BCB
    $meld_color = 'black';
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

$reader_color = 'red';
if (isset($actuele_versie)) {
    $reader_color = 'black';
}

?>

<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" href="menu.css">

<?php include "back_to_top.js.php" ?>

<div id = "rechts_uitlijnen" class = 'header_breed'>
    <section style="text-align : center">
<?php # TODO: waarom de spaties? # ?>
        <?php echo $titel . str_repeat('&nbsp;', 28); ?>
        </section>
    <img src='OER_van_OVIS.jpg' />
</div>

<ul class="header_smal" id = <?php echo Url::getTagId(); ?> >
    <li class="dropdown"><?php echo View::link_to('Home', 'Home.php', ['class' => 'black']); ?></li>
    <li class="dropdown"><span>Registratie</span>
        <div class="dropdown-content">
            <?php echo View::link_to('Aanvoer schaap', 'InvSchaap.php', ['class' => 'black']); ?>
            <br><br>
            <?php echo View::link_to('Medicijn toediening', 'Med_registratie.php', ['class' => $tech_color]); ?>
            <br><br>
            <?php echo View::link_to('Dekkingen / Dracht', 'Dekkingen.php', ['class' => $tech_color]); ?>
            <br><br>
        </div>
    </li>

    <li class="dropdown"><span>Reader</span>
        <div class="dropdown-content-smal">
            <?php echo View::link_to('Inlezen reader', 'InlezenReader.php', ['class' => 'black']); ?> 
            <br><br>
            <?php echo View::link_to('Raederalerts', 'Alerts.php', ['class' => 'black']); ?>
            <br><br>
        </div>
    </li>

    <li class="dropdown"><span style = "color : <?php echo $meld_color; ?> ;">RVO</span>
        <div class="dropdown-content-smal">
            <?php echo View::link_to('Melden RVO', 'Melden.php', ['class' => $meld_color]) ?>
            <br><br>
            <?php echo View::link_to('Meldingen', 'Meldingen.php', ['class' => 'black']); ?>
            <br><br>
        </div>
    </li>

    <li class="dropdown"><span>RAADPLEGEN</span>
        <div class="dropdown-content">
            <?php if ($modtech == 0 && $modmeld == 1) { ?>
            <?php echo View::link_to('Afvoerlijst', 'Afvoerstal.php', ['class' => 'black']); ?>
            <?php } else { ?>
            <?php echo View::link_to('Verblijven in gebruik', 'Bezet.php', ['class' => 'black']); ?>
            <?php } ?>
            <br><br>
            <?php echo View::link_to('Schaap opzoeken', 'Zoeken.php', ['class' => 'black']); ?>
            <br><br>
            <?php echo View::link_to('Stallijst', 'Stallijst.php', ['class' => 'black']); ?>
            <br><br>
            <?php echo View::link_to('Afleverlijst', 'ZoekAfldm.php', ['class' => 'black']); ?>
            <br><br>
        </div>
    </li>

    <li class="dropdown"><span>Rapporten</span>
        <div class="dropdown-content-breed">
            <?php echo View::link_to('Maandoverz. fokkerij', 'Mndoverz_fok.php', ['class' => $tech_color]); ?>
            <br><br>
            <?php echo View::link_to('Maandoverz. vleeslam.', 'Mndoverz_vlees.php', ['class' => $tech_color]); ?>
            <br><br>
            <?php echo View::link_to('Medicijn rapportage', 'Med_rapportage.php', ['class' => $tech_color]); ?>
            <br><br>
            <?php echo View::link_to('Voer rapportage', 'Voer_rapportage.php', ['class' => $tech_color]); ?>
            <br><br>
            <?php echo View::link_to('Maandtotalen', 'MaandTotalen.php', ['class' => $tech_color]); ?>
            <br><br>
            <?php echo View::link_to('Groeiresultaten per schaap', 'GroeiresultaatSchaap.php', ['class' => $tech_color]); ?>
            <br><br>
            <?php echo View::link_to('Groeiresultaten per weging', 'GroeiresultaatWeging.php', ['class' => $tech_color]); ?>
            <br><br>
            <?php # TODO: omschrijving is anders dan in menu1! ?>
            <?php echo View::link_to('Periode resultaten', 'ResultHok.php', ['class' => $tech_color]); ?>
            <br><br>

        <ul class="nested-dropdown">
        <li class="dropdown2"><span>Ooi rapporten</span><br><br>
            <div class="dropdown-content2">
                <?php echo View::link_to('Ooikaart detail', 'Ooikaart.php', ['class' => $tech_color]); ?>
                <br><br>
                <?php echo View::link_to('Ooikaart moeders', 'OoikaartAll.php', ['class' => $tech_color]); ?>
                <br><br>
                <?php echo View::link_to('Meerling in periode', 'Meerlingen5.php', ['class' => $tech_color]); ?>
                <br><br>
                <?php echo View::link_to('Meerling per geslacht', 'Meerlingen.php', ['class' => $tech_color]); ?>
                <br><br>
                <?php echo View::link_to('Meerlingen per jaar', 'Meerlingen2.php', ['class' => $tech_color]); ?>
                <br><br>
                <?php echo View::link_to('Meerling oplopend', 'Meerlingen3.php', ['class' => $tech_color]); ?>
                <br><br>
                <?php echo View::link_to('Meerlingen aanwezig', 'Meerlingen4.php', ['class' => $tech_color]); ?>
                <br><br>
            </div>
        </li>
        </ul>

        </div>
    </li>

    <li class="dropdown"><span>Voorraadbeheer</span>
        <div class="dropdown-content">
            <?php echo View::link_to('Medicijnenbestand', 'Medicijnen.php', ['class' => $tech_color]); ?>
            <br><br>
            <?php echo View::link_to('Voerbestand', 'Voer.php', ['class' => $tech_color]); ?>
            <br><br>
            <?php echo View::link_to('Inkopen', 'Inkopen.php', ['class' => $tech_color]); ?>
            <br><br>
            <?php echo View::link_to('Voorraad', 'Voorraad.php', ['class' => $tech_color]); ?>
            <br><br>
        </div>
    </li>

    <li class="dropdown"><span>Financieel</span>
        <div class="dropdown-content">
            <?php echo View::link_to('Inboeken', 'Kostenopgaaf.php', ['class' => $fin_color]); ?>
            <br><br>
            <?php echo View::link_to('Deklijst', 'Deklijst.php', ['class' => $fin_color]); ?>
            <br><br>
            <?php echo View::link_to('Liquiditeit', 'Liquiditeit.php', ['class' => $fin_color]); ?>
            <br><br>
            <?php echo View::link_to('Saldoberekening', 'Saldoberekening.php', ['class' => $fin_color]); ?>
            <br><br>
            <?php echo View::link_to('Rubrieken', 'Rubrieken.php', ['class' => $fin_color]); ?>
            <br><br>
            <?php echo View::link_to('Componenten', 'Componenten.php', ['class' => $fin_color]); ?>
            <br><br>
            <?php echo View::link_to('Betaalde posten', 'Kostenoverzicht.php', ['class' => $fin_color]); ?>
            <br><br>
        </div>
    </li>

    <li class="dropdown"><span style = 'color : black'>Beheer</span>
        <div class="dropdown-content">
            <?php echo View::link_to('Verblijven', 'Hok.php', ['class' => $tech_color]); ?>
            <br><br>
            <?php echo View::link_to('Rassen', 'Ras.php', ['class' => 'black']); ?>
            <br><br>
            <?php echo View::link_to('Redenen en momenten', 'Uitval.php', ['class' => 'black']); ?>
            <br><br>
            <?php echo View::link_to('Combi redenen', 'Combireden.php', ['class' => 'black']); ?>
            <br><br>
            <?php echo View::link_to('Dekrammen', 'Vader.php', ['class' => 'black']); ?>
            <br><br>
            <?php echo View::link_to('Eenheden', 'Eenheden.php', ['class' => $tech_color]); ?>
            <br><br>
            <?php echo View::link_to('Relaties', 'Relaties.php', ['class' => 'black']); ?>
            <br><br>
            <?php echo View::link_to('Readerversies', 'Readerversies.php', ['class' => $reader_color]); ?>
            <br><br>
            <?php echo View::link_to('Readerbestanden', 'bestanden.php', ['class' => 'black']); ?>
            <br><br>
            <?php if ($modbeheer == 1) { ?>
            <?php echo View::link_to('Gebruikers', 'Gebruikers.php', ['class' => 'black']); ?>
            <br><br>
            <?php } ?>
            <?php echo View::link_to('Instellingen', 'Systeem.php', ['class' => 'blue']); ?>
        </div>
    </li>

    <li id = "rechts_uitlijnen">
        <?php echo View::link_to('Uitloggen', 'index.php', ['class' => 'black']); ?>
    </li>
</ul>

<?php # TODO: halve html-elementen heel maken --BCB # ?>
<table id ="table1" align="center">
<tbody>
<tr height = 90> </tr>
<TR>
