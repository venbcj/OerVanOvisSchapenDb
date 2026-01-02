<?php

class VoerRapportageTest extends IntegrationCase {

    public function testUitgelogd_geeft_wit_scherm() {
        Auth::logout();
        $this->get('/Voer_rapportage.php');
        $this->assertNoNoise();
        $this->assertPresent('Je bent niet ingelogd');
    }

    public function testIngelogd_zonder_techrecht_geeft_afbeelding() {
        $this->uses_db();
        $this->runSQL("UPDATE tblLeden SET tech=0 WHERE lidId=1");
        $this->get('/Voer_rapportage.php', ['ingelogd' => 1]);
        $this->assertPresent('<img src="Voer_rapportage_php.jpg');
        $this->runSQL("UPDATE tblLeden SET tech=1 WHERE lidId=1");
    }

    public function testGet() {
        $this->runfixture('voervoorraad');
        $this->get('/Voer_rapportage.php', ['ingelogd' => 1]);
        $this->assertNoNoise();
        $this->assertAbsent('Je bent niet ingelogd');
        $this->assertOptieCount('kzlVoer_', 2);
    }

    public function testToon_geen_maanden() {
        $this->post('/Voer_rapportage.php', [
            'ingelogd_' => 1,
            'knpToon' => 1,
            'kzlVoer_' => 1,
            'kzlDoel_' => 0,
        ]);
        $this->assertNoNoise();
        $this->assertAbsent('<select name="kzlMdjr_"');
    }

    // ooh lekker weer. Deze test faalt "af en toe".
    public function testToon_met_maanden() {
        $this->runfixture('voervoorraad');
        $this->runfixture('jaarmaanden-n');
        $this->post('/Voer_rapportage.php', [
            'ingelogd_' => 1,
            'knpToon_' => 1,
            'kzlVoer_' => 1,
            'kzlDoel_' => 1,
        ]);
        $this->assertNoNoise();
        $this->assertPresent('<select name="kzlMdjr_"');
        $fixed_options = 1; // een lege
        $this->assertOptieCount('kzlMdjr_', $fixed_options + 2);
    }

    public function testSave() {
        $this->runfixture('voervoorraad');
        $this->runfixture('jaarmaanden-1');
        $this->post('/Voer_rapportage.php', [
            'ingelogd_' => 1,
            'knpSave_' => 1,
            'kzlVoer_' => 1,
            'kzlDoel_' => 1,
            'txtDatum_1' => '2020-01-01',
            'txtKilo_1' => 2, // anders dan de 1 in fixture
            'chbDelVoer_1' => 0,
            'chbDelPeri_1' => 0,
        ]);
        $this->assertNoNoise();
    }

}
