<?php

class MedRegistratiePageTest extends IntegrationCase {

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
        $this->runfixture('reden');
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

    # TODO (BV) fixtures voor maken
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

    public function testInsertMedregistratieGeenVoorraad() {
        $this->runfixture('medicijnvoorraad');
        $this->post('/Med_registratie.php', [
            'ingelogd' => 1,
            'knpInsert' => 1,
            'chbKeuze' => ['131072'],
            'txtDatum' => '1990-05-02',
            'txtAantal' => 10,
            'kzlReden' => 4,
            'kzlArtikel' => 1,
        ]);
        $this->assertNoNoise();
        // TODO: met betere data komt hier een betekenisvolle melding
        $this->assertFout("U kunt geen 10 kg toedienen er is nl. nog maar 4.00 kg beschikbaar.");
    }

    public function testInsertMedregistratieToedienen() {
        $this->runfixture('medicijnvoorraad');
        $this->post('/Med_registratie.php', [
            'ingelogd' => 1,
            'knpInsert' => 1,
            'chbKeuze' => ['131072'],
            'txtDatum' => '1990-05-02',
            'txtAantal' => 2,
            'kzlReden' => 4,
            'kzlArtikel' => 1,
        ]);
        $this->assertNoNoise();
        // TODO: met betere data komt hier een betekenisvolle melding
        $this->assertFout("Er is bij 1 dier 2kg wortel toegediend");
    }

}
