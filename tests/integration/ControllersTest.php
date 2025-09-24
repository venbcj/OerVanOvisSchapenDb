<?php

# use PHPUnit\Framework\Attributes\DataProvider;

class ControllersTest extends IntegrationCase {

    public static function setupBeforeClass() : void {
        self::runfixture('hok');
        self::runfixture('user-harm');
        Response::setTest();
    }

    public static function controllers_with_post_include() {
        return [
            ['InsAanvoer.php', [], ['kzlFase_1' => 1, 'chbkies_1' => 1, 'chbDel_1' => 0, ]],
            ['InsAdoptie.php', [], ['chbkies_1' => 1, 'chbDel_1' => 0, ]],
            ['InsAfvoer.php', [], []],
            ['InsDekken.php', [], ['chbKies_1' => 1, 'chbDel_1' => 0, ]], // hoofdletter K !?!
            ['InsDracht.php', [], ['chbKies_1' => 1, ]],
            ['InsGeboortes.php', [], ['chbkies_1' => 1, 'chbDel_1' => 0, ]],
            ['InsGrWijzigingUbn.php', [], []],
            ['InsHalsnummers.php', [], ['chbkies_1' => 1, 'chbDel_1' => 0, ]],
            ['InsLambar.php', [], ['chbkies_1' => 1, 'chbDel_1' => 0, ]],
            ['InsMedicijn.php', [], ['chbkies_1' => 1, 'chbDel_1' => 0, ]],
            ['InsOmnummeren.php', [], ['chbkies_1' => 1, 'chbDel_1' => 0, ]],
            ['InsOverplaats.php', [], ['chbkies_1' => 1, 'chbDel_1' => 0, ]],
            # deze checkt fldKies die nooit gezet wordt
            # ['InsSpenen.php', [], ['chbkies_1' => 1, ]],
            # deze veroorzaakt Unknown column s.rasId in ON, Page_numbers:43
            # ['InsStallijstscan_controle.php', ['impagrident'], ['chbkies_1' => 1, 'kzlFase_1' => 1, 'chbDel_1' => 0, ]],
            ['InsStallijstscan_nieuwe_klant.php', [], ['chbkies_1' => 1, 'kzlFase_1' => 1, 'chbDel_1' => 0, ]],
            ['InsTvUitscharen.php', [], ['chbKies_1' => 1, 'chbDel_1' => 0, ]], // hoofdletter K ?!
            ['InsUitscharen.php', [], []],
            ['InsUitval.php', [], ['chbkies_1' => 1, 'chbDel_1' => 0, ]],
            ['InsVoerregistratie.php', [], []],
            ['InsWegen.php', [], ['chbkies_1' => 1,'chbDel_1' => 0, ]],
        ];
    }

    public static function controllers_with_save_include() {
        return [
            ['Afvoerstal.php', [], []],
            ['Componenten.php', [], []],
            ['Contact.php', ['partij-1'], ['cnt_' => 1, ]],
            ['Dekkingen.php', [], []],
            ['Deklijst.php', [], ['kzlJaar_' => 2020, ]],
            ['Hok.php', [], []],
            ['HokAanwas.php', [], ['chbkies_1' => 1, ]],
            ['HokAfleveren.php', ['schaap-4'], ['chbkies_4' => 1, 'txtDatum_4' => '01-01-2020', 'txtKg_4' => 7, ]],
            ['HokAfsluiten.php', [], []],
            # dit gaat af en toe mis, als $data gevuld wordt. Maar die query is nogal ... dik.
            ['HokOverpl.php', ['schaap-4'], ['chbkies_4' => 1, 'txtDatum_4' => '02-02-2021', ]],
            ['HokSpenen.php', ['schaap-4'], ['chbkies_4' => 1, 'txtDatum_4' => '01-02-2020', 'txtKg_4' => 2, ]],
            ['HokVerlaten.php', ['schaap-4'], ['chbkies_4' => 1, 'txtDatum_4' => '01-01-2021', ]],
            ['Inkopen.php', [], []],
            ['InsGeboortes.php', [], []],
            ['InsVoerregistratie.php', [], []],
            # save_klanten bestaat niet. Kan niet werken.
            # ['Klanten.php', [], []],
            ['Kostenopgaaf.php', ['opgaaf-1'], ['chbLiq_1' => 0, 'kzlRubr_1' => 1, 'txtDatum_1' => '13-12-2021', 'txtBedrag_1' => 11, 'txtToel_1' => 'kennelijk', ]],
            ['Kostenoverzicht.php', [], []],
            ['Liquiditeit.php', [], []],
            ['LoslopersPlaatsen.php', [], []],
            ['LoslopersVerkopen.php', [], []],
            ['Medicijnen.php', [], []],
            ['MeldAanvoer.php', [], []],
            ['MeldAfvoer.php', [], []],
            ['MeldGeboortes.php', [], []],
            ['MeldOmnummer.php', [], []],
            ['MeldUitval.php', [], []],
            ['OoilamSelectie.php', [], []],
            ['Ras.php', [], []],
            ['Relatie.php', ['partij-1'], ['txtpId_' => 1, 'txtNaam_' => 'nodig']],
            ['Relaties.php', [], []],
            ['Rubrieken.php', [], []],
            ['Saldoberekening.php', [], []],
            ['Ubn_toevoegen.php', [], []],
            ['Uitval.php', [], []],
            ['Voer.php', [], []],
            ['Voer_rapportage.php', [], ['kzlVoer_' => 1, 'kzlDoel_' => 1, ]],
            ['Voorraadcorrectie.php', ['artikelvoorraad'], ['inkid_1' => 1, ]],
            ['Zoeken.php', [], ['radHis_' => 1, 'radOud_' => 1, 'txtComm_1' => 1]],
        ];
    }

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

    # php-8
    # #[DataProvider('controllers_with_post_include')]
    /**
     * @dataProvider controllers_with_post_include
     * Dit is een beginnetje tbv coverage / storingsdetectie
     */
    public function testPostInsert($controller, $fixtures, $postdata) {
        foreach ($fixtures as $fixture) {
            $this->runfixture($fixture);
        }
        $full_postdata = array_merge(['ingelogd_' => 1, 'knpInsert_' => 1], $postdata);
        $this->post("/$controller", $full_postdata);
        $this->assertNoNoise();
    }

    # php-8
    # #[DataProvider('controllers_with_save_include')]
    /**
     * @dataProvider controllers_with_save_include
     * Dit is een beginnetje tbv coverage / storingsdetectie
     */
    public function testPostSave($controller, $fixtures, $postdata) {
        foreach ($fixtures as $fixture) {
            $this->runfixture($fixture);
        }
        $full_postdata = array_merge(['ingelogd_' => 1, 'knpSave_' => 1], $postdata);
        $this->post("/$controller", $full_postdata);
        $this->assertNoNoise();
    }

    public function testContact() {
        Session::set('CNT', 0);
        $this->get("/Contact.php");
        $this->assertNoNoise();
    }

    public function testRelatie() {
        $this->runfixture('partij-1');
        $this->get("/Relatie.php", ['pstid' => 1]);
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
