    <li class="dropdown">
<?php echo View::link_to('Home', 'Home.php', ['class' => 'black']); ?></li>
    <li class="dropdown"><span>Registratie</span>
        <div class="dropdown-content">
            <?php echo View::link_to('Aanvoer schaap', 'InvSchaap.php', ['class' => 'black']); ?>
            <?php echo View::link_to('Medicijn toediening', 'Med_registratie.php', ['class' => $tech_color]); ?>
            <?php echo View::link_to('Dekkingen / Dracht', 'Dekkingen.php', ['class' => $tech_color]); ?>
        </div>
    </li>

    <li class="dropdown"><span>Reader</span>
        <div class="dropdown-content-smal">
            <?php echo View::link_to('Inlezen reader', 'InlezenReader.php', ['class' => 'black']); ?> 
            <?php echo View::link_to('Raederalerts', 'Alerts.php', ['class' => 'black']); ?>
        </div>
    </li>

    <li class="dropdown"><span style = "color : <?php echo $meld_color; ?> ;">RVO</span>
        <div class="dropdown-content-smal">
            <?php echo View::link_to('Melden RVO', 'Melden.php', ['class' => $meld_color]) ?>
            <?php echo View::link_to('Meldingen', 'Meldingen.php', ['class' => 'black']); ?>
        </div>
    </li>

    <li class="dropdown"><span>RAADPLEGEN</span>
        <div class="dropdown-content">
            <?php if ($modtech == 0 && $modmeld == 1) { ?>
            <?php echo View::link_to('Afvoerlijst', 'Afvoerstal.php', ['class' => 'black']); ?>
            <?php } else { ?>
            <?php echo View::link_to('Verblijven in gebruik', 'Bezet.php', ['class' => 'black']); ?>
            <?php } ?>
            <?php echo View::link_to('Schaap opzoeken', 'Zoeken.php', ['class' => 'black']); ?>
            <?php echo View::link_to('Stallijst', 'Stallijst.php', ['class' => 'black']); ?>
            <?php echo View::link_to('Afleverlijst', 'ZoekAfldm.php', ['class' => 'black']); ?>
        </div>
    </li>

    <li class="dropdown"><span>Rapporten</span>
        <div class="dropdown-content-breed">
            <?php echo View::link_to('Maandoverz. fokkerij', 'Mndoverz_fok.php', ['class' => $tech_color]); ?>
            <?php echo View::link_to('Maandoverz. vleeslam.', 'Mndoverz_vlees.php', ['class' => $tech_color]); ?>
            <?php echo View::link_to('Medicijn rapportage', 'Med_rapportage.php', ['class' => $tech_color]); ?>
            <?php echo View::link_to('Voer rapportage', 'Voer_rapportage.php', ['class' => $tech_color]); ?>
            <?php echo View::link_to('Maandtotalen', 'MaandTotalen.php', ['class' => $tech_color]); ?>
            <?php echo View::link_to('Groeiresultaten per schaap', 'GroeiresultaatSchaap.php', ['class' => $tech_color]); ?>
            <?php echo View::link_to('Groeiresultaten per weging', 'GroeiresultaatWeging.php', ['class' => $tech_color]); ?>
            <?php # TODO: #0004121 omschrijving is anders dan in menu1! ?>
            <?php echo View::link_to('Periode resultaten', 'ResultHok.php', ['class' => $tech_color]); ?>

        <ul class="nested-dropdown">
        <li class="dropdown2"><span>Ooi rapporten</span>
            <div class="dropdown-content2">
                <?php echo View::link_to('Ooikaart detail', 'Ooikaart.php', ['class' => $tech_color]); ?>
                <?php echo View::link_to('Ooikaart moeders', 'OoikaartAll.php', ['class' => $tech_color]); ?>
                <?php echo View::link_to('Meerling in periode', 'Meerlingen5.php', ['class' => $tech_color]); ?>
                <?php echo View::link_to('Meerling per geslacht', 'Meerlingen.php', ['class' => $tech_color]); ?>
                <?php echo View::link_to('Meerlingen per jaar', 'Meerlingen2.php', ['class' => $tech_color]); ?>
                <?php echo View::link_to('Meerling oplopend', 'Meerlingen3.php', ['class' => $tech_color]); ?>
                <?php echo View::link_to('Meerlingen aanwezig', 'Meerlingen4.php', ['class' => $tech_color]); ?>
            </div>
        </li>
        </ul>

        </div>
    </li>

    <li class="dropdown"><span>Voorraadbeheer</span>
        <div class="dropdown-content">
            <?php echo View::link_to('Medicijnenbestand', 'Medicijnen.php', ['class' => $tech_color]); ?>
            <?php echo View::link_to('Voerbestand', 'Voer.php', ['class' => $tech_color]); ?>
            <?php echo View::link_to('Inkopen', 'Inkopen.php', ['class' => $tech_color]); ?>
            <?php echo View::link_to('Voorraad', 'Voorraad.php', ['class' => $tech_color]); ?>
        </div>
    </li>

    <li class="dropdown"><span>Financieel</span>
        <div class="dropdown-content">
            <?php echo View::link_to('Inboeken', 'Kostenopgaaf.php', ['class' => $fin_color]); ?>
            <?php echo View::link_to('Deklijst', 'Deklijst.php', ['class' => $fin_color]); ?>
            <?php echo View::link_to('Liquiditeit', 'Liquiditeit.php', ['class' => $fin_color]); ?>
            <?php echo View::link_to('Saldoberekening', 'Saldoberekening.php', ['class' => $fin_color]); ?>
            <?php echo View::link_to('Rubrieken', 'Rubrieken.php', ['class' => $fin_color]); ?>
            <?php echo View::link_to('Componenten', 'Componenten.php', ['class' => $fin_color]); ?>
            <?php echo View::link_to('Betaalde posten', 'Kostenoverzicht.php', ['class' => $fin_color]); ?>
        </div>
    </li>

    <li class="dropdown"><span style = 'color : black'>Beheer</span>
        <div class="dropdown-content">
            <?php echo View::link_to('Verblijven', 'Hok.php', ['class' => $tech_color]); ?>
            <?php echo View::link_to('Rassen', 'Ras.php', ['class' => 'black']); ?>
            <?php echo View::link_to('Redenen en momenten', 'Uitval.php', ['class' => 'black']); ?>
            <?php echo View::link_to('Combi redenen', 'Combireden.php', ['class' => 'black']); ?>
            <?php echo View::link_to('Dekrammen', 'Vader.php', ['class' => 'black']); ?>
            <?php echo View::link_to('Eenheden', 'Eenheden.php', ['class' => $tech_color]); ?>
            <?php echo View::link_to('Relaties', 'Relaties.php', ['class' => 'black']); ?>
            <?php echo View::link_to('Readerversies', 'Readerversies.php', ['class' => $reader_color]); ?>
            <?php echo View::link_to('Readerbestanden', 'bestanden.php', ['class' => 'black']); ?>
            <?php if ($modbeheer == 1) { ?>
            <?php echo View::link_to('Gebruikers', 'Gebruikers.php', ['class' => 'black']); ?>
            <?php } ?>
            <?php echo View::link_to('Instellingen', 'Systeem.php', ['class' => 'blue']); ?>
        </div>
    </li>
