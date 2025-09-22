<?php

# use PHPUnit\Framework\Attributes\DataProvider;

class ControllersTest extends IntegrationCase {

    public static function gettable_controllers() {
        return self::txt2ar(<<<TXT
AfleverLijst.php
Afvoerstal.php
Alerts.php
Beheer.php
Bezet.php
Combireden.php
Componenten.php
Dekkingen.php
Deklijst.php
Eenheden.php
Finance.php
Gebruiker.php
Gebruikers.php
Groeiresultaat.php
GroeiresultaatSchaap.php
GroeiresultaatWeging.php
HokAanwas.php
HokAfleveren.php
HokAfsluiten.php
Hoklijsten.php
Hoklijst.php
HokOverpl.php
Hok.php
HokSpenen.php
HokUitscharen.php
HokVerkopen.php
HokVerlaten.php
Home.php
Inkoop.php
Inkopen.php
InlezenReader.php
InsAanvoer.php
InsAdoptie.php
InsAfvoer.php
InsDekken.php
InsDracht.php
InsGeboortes.php
InsGrWijzigingUbn.php
InsHalsnummers.php
InsLambar.php
InsMedicijn.php
InsOmnummeren.php
InsOverplaats.php
InsSpenen.php
InsStallijstscan_nieuwe_klant.php
InsTvUitscharen.php
InsUitscharen.php
InsUitval.php
InsVoerregistratie.php
InsWegen.php
InvSchaap.php
Klanten.php
Kostenopgaaf.php
Kostenoverzicht.php
Leveranciers.php
Liquiditeit.php
Loslopers.php
LoslopersVerkopen.php
MaandTotalen.php
Medicijnen.php
MedOverzSchaap.php
Med_rapportage.php
Med_registratie.php
Meerlingen2.php
Meerlingen3.php
Meerlingen4.php
Meerlingen5.php
Meerlingen.php
MeldAanvoer.php
MeldAfvoer.php
Melden.php
MeldGeboortes.php
Meldingen.php
MeldOmnummer.php
Meldpagina.php
MeldUitval.php
Mndoverz_fok.php
Mndoverz_vlees.php
Newuser.php
OmnSchaap.php
OoikaartAll.php
Ooikaart.php
OoilamSelectie.php
Rapport1.php
Rapport.php
Ras.php
Readerversies.php
Relaties.php
ResultHok.php
ResultSchaap.php
Rubrieken.php
Saldoberekening.php
Stallijst.php
Systeem.php
Ubn_toevoegen.php
Uitval.php
Vader.php
Voer.php
Voer_rapportage.php
Voorraadcorrectie.php
Voorraad.php
Wachtwoord.php
Wegen.php
Workload.php
Worpindex.php
ZoekAfldm.php
Zoeken.php
TXT
        );
    }

    public static function problematic_controllers() {
        // de welkoms doen raar met de sessie
        // de insstallijstscancontrole maakt een 'unknown column s.rasId in ON' ... ?
        // queries in loslopersplaatsen bevatten geen kolom 'aantin', maar daar wordt vervolgens wel naar gevraagd
        return self::txt2ar(<<<TXT
Welkom.php
Welkom2.php
InsStallijstscan_controle.php
LoslopersPlaatsen.php
TXT
        );
    }

    // deze hebben allemaal fpdf nodig
    public static function controllers_missing_libraries() {
        return self::txt2ar(<<<TXT
AfleverLijst_pdf.php
Bezet_pdf.php
Combireden_pdf.php
Hok_pdf.php
Hoklijst_pdf.php
Loslopers_pdf.php
Meerlingen5_pdf.php
Ooikaart_pdf.php
Ras_pdf.php
Stallijst_pdf.php
Vader_pdf.php
TXT
        );
    }

    public static function controllers_needing_database() {
        return self::txt2ar(<<<TXT
TXT
);
    }

    // in schema.sql zitten niet alle tabellen.
    public static function controllers_missing_tables() {
        return self::txt2ar(<<<TXT
Gespeenden.php
Klant.php
Leverancier.php
TXT
        );
    }

    private static function txt2ar($text) {
        $lemmata = preg_split('/\s+/', $text);
        return array_combine($lemmata, array_map(function ($str) {
            return [$str];
        }, $lemmata));
    }

    # php-8
    # #[DataProvider('gettable_controllers')]
    /**
     * @dataProvider gettable_controllers
     */
    public function testGetRouteGuest($controller) {
        $this->get("/$controller");
        $this->assertNoNoise();
    }

    # php-8
    # #[DataProvider('gettable_controllers')]
    /**
     * @dataProvider gettable_controllers
     */
    public function testGetRouteAuthenticated($controller) {
        $this->get("/$controller", ['ingelogd' => 1]);
        $this->assertNoNoise();
    }

    # geen centrale test voor post-routes met een knop;
    # ten eerste moet daar ook postdata bij,
    # ten tweede zitten er teveel verschillen tussen om dat leesbaar te bundelen

    public function testContact() {
        $_SESSION['CNT'] = 0;
        $this->get("/Contact.php");
        $this->assertNoNoise();
    }

    public function testRelatie() {
        $this->get("/Relatie.php", ['pstid' => 0]);
        $this->assertNoNoise();
    }

    public function testReaderbestanden() {
        $dir = 'user_1/Readerbestanden/';
        $file = 'x'.date('d-m-Y');
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        if (!file_exists("$dir$file")) {
            touch("$dir$file");
        }
        $this->get('/Readerbestanden.php', ['ingelogd' => 1]);
        $this->assertNoNoise();
        unlink("$dir$file");
    }

}
