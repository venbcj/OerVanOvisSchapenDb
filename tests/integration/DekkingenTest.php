<?php

class DekkingenTest extends IntegrationCase {

    public function testNieuweInvoerDierGeenDatum() {
        $this->post('/Dekkingen.php', [
            'ingelogd' => 1,
            'knpInsert1_' => 1,
        ]);
        $this->assertNoNoise();
        $this->assertFout('De datum is onbekend.');
    }

    public function testNieuweInvoerDierGeenRegistratie() {
        $this->post('/Dekkingen.php', [
            'ingelogd' => 1,
            'knpInsert1_' => 1,
            'txtDatum1_' => '13-01-2012',
        ]);
        $this->assertNoNoise();
        $this->assertFout('Soort registratie is onbekend.');
    }

    public function testNieuweInvoerDierGeenMoeder() {
        $this->post('/Dekkingen.php', [
            'ingelogd' => 1,
            'knpInsert1_' => 1,
            'txtDatum1_' => '13-01-2012',
            'kzlWat_' => '1',
        ]);
        $this->assertNoNoise();
        $this->assertFout('Moederdier is onbekend.');
    }

    public function testNieuweInvoerDierZelfdeRam() {
        // TODO: fixture versterken, dit is nog niet begrijpelijk
        $this->runfixture('dekking');
        $this->post('/Dekkingen.php', [
            'ingelogd' => 1,
            'knpInsert1_' => 1,
            'txtDatum1_' => '13-01-2012',
            'kzlWat_' => '1',
            'kzlOoi_' => 7,
            'kzlRamNew1_' => 8,
        ]);
        $this->assertNoNoise();
        // datum komt uit fixture
        $this->assertFout('Deze ram heeft deze ooi reeds als laatste gedekt en wel op 02-02-2013.');
    }

    public function testNieuweInvoerDierAlDrachtig() {
        // TODO: fixture versterken, dit is nog niet begrijpelijk
        $this->runfixture('dekking-dracht');
        $this->post('/Dekkingen.php', [
            'ingelogd' => 1,
            'knpInsert1_' => 1,
            'txtDatum1_' => '13-01-2012',
            'kzlWat_' => '1',
            'kzlOoi_' => 7,
            'kzlRamNew1_' => 8,
        ]);
        $this->assertNoNoise();
        // datum komt uit fixture
        $this->assertFout('Deze ooi is reeds drachtig per 02-02-2013.');
    }

    public function testNieuweInvoerHokGeenDatum() {
        $this->post('/Dekkingen.php', [
            'ingelogd' => 1,
            'knpInsert2_' => 1,
        ]);
        $this->assertNoNoise();
        $this->assertFout('De datum is onbekend.');
    }

    public function testNieuweInvoerHokGeenHok() {
        $this->post('/Dekkingen.php', [
            'ingelogd' => 1,
            'knpInsert2_' => 1,
            'txtDatum2_' => '13-01-2012',
        ]);
        $this->assertNoNoise();
        $this->assertFout('Verblijf is onbekend.');
    }

    public function testNieuweInvoerHokGeenRam() {
        $this->post('/Dekkingen.php', [
            'ingelogd' => 1,
            'knpInsert2_' => 1,
            'txtDatum2_' => '13-01-2012',
            'kzlHok_' => '1',
        ]);
        $this->assertNoNoise();
        $this->assertFout('Ram is onbekend.');
    }

    public function testNieuweInvoerHok() {
        $this->post('/Dekkingen.php', [
            'ingelogd' => 1,
            'knpInsert2_' => 1,
            'txtDatum2_' => '13-01-2012',
            'kzlWat_' => 1,
            'kzlHok_' => '1',
            'kzlRamNew2_' => 8,
        ]);
        $this->assertNoNoise();
        $this->assertFout('Dit verblijf heeft geen moederdieren.');
    }

    // todo query aantal_laatste_dekkingen_van_moeders_uit_gekozen_verblijf_met_laatste_dekkingen_met_gekozen_vader
    // todo deze naam wordt hergebruikt!

        // $this->assertFout('De dekdatum mag niet voor de laatste dekking met dit vaderdier liggen. Dit geldt voor tenminste 1 moederdier uit dit verblijf.');

}
