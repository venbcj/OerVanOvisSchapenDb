<?php

class MedRegistratiePageTest extends IntegrationCase {

    use Expectations;

    public function tearDown(): void {
        unset ($GLOBALS['schaap_gateway']);
        parent::tearDown();
    }

    public function test_toont_plaatje_indien_module_uitgeschakeld() {
        $this->runSQL("UPDATE tblLeden SET tech=0");
        $this->get('/Med_registratie.php');
        $this->assertPresent('<img src="med_registratie');
    }

    public function testToonMedregistratieGeenSchaap() {
        $this->post('/Med_registratie.php', [
            'ingelogd' => 1,
            'knpToon' => 1,
            'radHok' => 0,
            'radAfv' => 0,
            'chbKeuze' => 1,
        ]);
        $this->assertNoNoise();
        $this->assertFout('Keuze uit schapen is niet gemaakt.');
    }

    public function testToonMedregistratieGeenMedicijn() {
        $this->post('/Med_registratie.php', [
            'ingelogd' => 1,
            'knpToon' => 1,
            'radHok' => 0,
            'radAfv' => 0,
            'chbKeuze' => 1,
            'kzlLevnr' => '1',
            'txtGeb_van' => '2010-01-03',
            'txtGeb_tot' => '2020-01-03',
        ]);
        $this->assertNoNoise();
        $this->assertFout('Medicijn is niet geselecteerd.');
    }

    public function testToonMedregistratie() {
        $this->post('/Med_registratie.php', [
            'ingelogd' => 1,
            'knpToon' => 1,
            'radHok' => 0,
            'radAfv' => 0,
            'chbKeuze' => 1,
            'kzlLevnr' => '1',
            'kzlArtikel' => '1',
        ]);
        $this->assertNoNoise();
        $this->assertNotFout();
        $this->assertPresent("name='knpInsert'");
    }

    public function testToonMedregistratieKeuzelijstArtikel() {
        $this->runfixture('artikelvoorraad');
        $this->post('/Med_registratie.php', [
            'ingelogd' => 1,
            'knpToon' => 1,
            'radHok' => 0,
            'radAfv' => 0,
            'chbKeuze' => 1,
            'kzlLevnr' => '1',
            'kzlArtikel' => '1',
        ]);
        $this->assertNoNoise();
        $this->assertNotFout();
        $this->assertOptieCount('kzlArtikel', 2); // 2, want er is altijd een lege optie
    }

    public function testToonMedregistratieKeuzelijstReden() {
        $this->runfixture('reden'); // zet 1 willekeurige reden op "pil"
        $this->post('/Med_registratie.php', [
            'ingelogd' => 1,
            'knpToon' => 1,
            'radHok' => 0,
            'radAfv' => 0,
            'chbKeuze' => 1,
            'kzlLevnr' => '1',
            'kzlArtikel' => '1',
        ]);
        $this->assertNoNoise();
        $this->assertNotFout();
        // TODO: dit faalt af en toe. precondities?
        $this->assertOptieCount('kzlReden', 2); // er is altijd een lege optie
    }

    public function testToonMedregistratieKeuzelijstHalsnr() {
        $this->runfixture('halsnr');
        $this->post('/Med_registratie.php', [
            'ingelogd' => 1,
            'knpToon' => 1,
            'radHok' => 0,
            'radAfv' => 0,
            'chbKeuze' => 1,
            'kzlLevnr' => '1',
            'kzlArtikel' => '1',
        ]);
        $this->assertNoNoise();
        $this->assertNotFout();
        $this->assertOptieCount('kzlHalsnr', 2); // er is altijd een lege optie
    }

    # TODO: #0004116 (BV) fixtures voor maken
    # public function testToonMedregistratieKeuzelijstHok() {
    #     $this->markTestIncomplete('Dit leg je me maar een keer uit');
    #     $this->runfixture('hok');
    #     $this->post('/Med_registratie.php', [
    #         'ingelogd' => 1,
    #         'knpToon' => 1,
    #         'radHok' => 0,
    #         'radAfv' => 0,
    #         'chbKeuze' => 1,
    #         'kzlLevnr' => '1',
    #         'kzlArtikel' => '1',
    #     ]);
    #     $this->assertNoNoise();
    #     $this->assertNotFout();
    #     $this->assertOptieCount('kzlHalsnr', 2); // 2, want er is altijd een lege optie
    # }

    public function testInsertMedregistratieGeenSchaap() {
        $this->post('/Med_registratie.php', [
            'ingelogd' => 1,
            'knpInsert' => 1,
        ]);
        $this->assertNoNoise();
        $this->assertFout('Er is geen schaap geselecteerd.');
    }

    public function testInsertMedregistratieGeenDatum() {
        $this->post('/Med_registratie.php', [
            'ingelogd' => 1,
            'knpInsert' => 1,
            'chbKeuze' => ['1'],
        ]);
        $this->assertNoNoise();
        $this->assertFout('Datum is niet bekend.');
    }

    public function testInsertMedregistratieGeenAantal() {
        $this->post('/Med_registratie.php', [
            'ingelogd' => 1,
            'knpInsert' => 1,
            'chbKeuze' => ['1'],
            'txtDatum' => '1990-05-02',
        ]);
        $this->assertNoNoise();
        $this->assertFout('Het aantal is niet bekend.');
    }

    public function testInsertMedregistratieGeenReden() {
        $this->post('/Med_registratie.php', [
            'ingelogd' => 1,
            'knpInsert' => 1,
            'chbKeuze' => ['1'],
            'txtDatum' => '1990-05-02',
            'txtAantal' => 1,
        ]);
        $this->assertNoNoise();
        $this->assertFout('De reden is niet geselecteerd.');
    }

    private const ARTID = 93;
    public function testInsertMedregistratieGeenVoorraad() {
        # $this->markTestSkipped('geeft "opeens" division by zero.');
        $this->runfixture('medicijnvoorraad');
        $this->post('/Med_registratie.php', [
            'ingelogd' => 1,
            'knpInsert' => 1,
            'chbKeuze' => ['131072'], // levensnr schaap in fixture medicijnvoorraad
            'txtDatum' => '1990-05-02',
            'txtAantal' => 10,
            'kzlReden' => 4,
            'kzlArtikel' => 93,
        ]);
        $this->assertNoNoise();
        // TODO: met betere data komt hier een betekenisvolle melding
        // todo is het nou 4 of 4.00 kg?
        $this->assertFout("U kunt geen 10 kg toedienen er is nl. nog maar 4 kg beschikbaar.");
    }

    public function testInsertMedregistratieToedienen() {
        $this->runfixture('medicijnvoorraad');
        $this->uses_db();
        $this->post('/Med_registratie.php', [
            'ingelogd' => 1,
            'knpInsert' => 1,
            'chbKeuze' => ['131072'],
            'txtDatum' => '1990-05-02',
            'txtAantal' => 2,
            'kzlReden' => 4,
            'kzlArtikel' => 93,
        ]);
        $this->assertNoNoise();
        // TODO: met betere data komt hier een betekenisvolle melding
        $this->assertFout("Er is bij 1 dier 2kg wortel toegediend");
    }

    public function testInsertMedregistratieToedienenTeLaat() {
        $this->runfixture('medicijnvoorraad');
        $this->runSQL("INSERT INTO tblHistorie(actId, stalId, skip, datum) VALUES(12, 1, 0, '2001-12-13')");
        $this->post('/Med_registratie.php', [
            'ingelogd' => 1,
            'knpInsert' => 1,
            'chbKeuze' => ['131072'],
            'txtDatum' => '2990-05-02',
            'txtAantal' => 2,
            'kzlReden' => 4,
            'kzlArtikel' => 93,
        ]);
        $this->assertNoNoise();
        // hier moet je de \ escapen, en dat vind ik jammer. TODO ooit oplossen.
        $this->assertFout("De volgende dieren hebben geen medicatie gekregen !!\\n131072 de datum mag niet na de afvoerdatum 13-12-2001 liggen.\\n\\nEr is bij 0 dieren totaal 0kg wortel toegediend");
    }

    // NOTE: de prime-resultaten worden door SchaapGateway inhoudelijk getest.

    public function testKeuzelijstenLevnr() {
        $stub = new SchaapGatewayStub();
        // overschrijft zoek_medicatielijst, voegt 3 schapen toe
        $GLOBALS['schaap_gateway'] = $stub;
        $stub->prime('zoek_medicatie_lijst', $this->getExpected('zoek_medicatie_lijst'));
        $stub->prime('zoek_medicatielijst_werknummer', []); // wordt aangeroepen; testen we hier niet
        $this->post('/Med_registratie.php', [
            'ingelogd' => 1,
            'kzlLevnr' => '2', // dan wordt ook de selected-optie gedekt. Hoe assereren we dat?
        ]);
        // en nou nagaan dat er in select.kzlLevnr drie options zitten. Eh vier, er is altijd een lege
        $this->assertOptieCount('kzlLevnr', 4);
    }

    public function testKeuzelijstenWerknr() {
        $stub = new SchaapGatewayStub();
        $GLOBALS['schaap_gateway'] = $stub;
        $stub->prime('zoek_medicatie_lijst', [ ]);
        $stub->prime('zoek_medicatielijst_werknummer', $this->getExpected('zoek_medicatielijst_werknummer'));
        $this->post('/Med_registratie.php', [
            'ingelogd' => 1,
            'kzlWerknr' => '2', // dan wordt ook de selected-optie gedekt. Hoe assereren we dat?
        ]);
        // en nou nagaan dat er in de select drie options zitten. Eh vier, er is altijd een lege
        $this->assertOptieCount('kzlWerknr', 4);
    }

    public function testSchaapgegevens() {
        $stub = new SchaapGatewayStub();
        $GLOBALS['schaap_gateway'] = $stub;
        $stub->prime('zoek_medicatie_lijst', [ ]);
        $stub->prime('zoek_medicatielijst_werknummer', [ ]);
        $expected = $this->getExpected('zoek_schaapgegevens');
        $stub->prime('zoek_schaapgegevens', $expected);
        $this->post('/Med_registratie.php', [
            'ingelogd' => 1,
            'knpToon' => 1,
            'radHok' => 0,
            'radAfv' => 0,
            'chbKeuze' => 1,
            'kzlLevnr' => '1',
            'kzlArtikel' => '1',
        ]);
        // en nou nagaan dat er in de tabel een tr zit. Eh drie: er zitten twee "kunstregels" in.
        $this->assertTrCount('schapen', 2 + count($expected));
    }

}
