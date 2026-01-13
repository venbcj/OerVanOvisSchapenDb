<?php

class HokAfsluitenTest extends IntegrationCase {

    public function test_get() {
        $this->get('/HokAfsluiten.php', [
            'ingelogd' => 1,
        ]);
        $this->assertNoNoise();
    }

    // NOTE: voor alle save1*-tests zouden ook een save2 en save3 moeten komen.
    // Ik geloof het voor nu wel even.
    public function test_validate_save1_nodate() {
        $this->post('/HokAfsluiten.php', [
            'ingelogd' => 1,
            'knpSave1' => 1,
        ]);
        $this->assertNoNoise();
        $this->assertFout('Afsluitdatum is niet bekend');
    }

    public function test_validate_save1_double_date() {
        $this->runSQL("INSERT INTO tblPeriode(hokId, doelId, dmafsluit) VALUES(1, 1, '2010-02-02')");
        $this->post('/HokAfsluiten.php', [
            'ingelogd' => 1,
            'knpSave1' => 1,
            'txtId' => 1,
            'txtSluitdm1' => '2010-02-02',
        ]);
        $this->assertNoNoise();
        $this->assertFout('Deze afsluitdatum bestaat al.');
    }

    # :( gaat stuk op ongesloten buffer
    # public function test_validate_save1_exception_onvoldoende_voer() {
    #     $this->expectException(Exception::class);
    #     $this->post('/HokAfsluiten.php', [
    #         'ingelogd' => 1,
    #         'knpSave1' => 1,
    #         'txtId' => 1,
    #         'txtSluitdm1' => '2010-02-02',
    #         'txtKg1' => 90,
    #         'kzlArtikel1' => 1,
    #     ]);
    #     $this->assertNoNoise();
    #     $this->assertFout('Er is onvoldoende voer op voorraad.');
    # }

    public function test_validate_save1_onvoldoende_voer() {
        $this->runfixture('voervoorraad');
        $this->post('/HokAfsluiten.php', [
            'ingelogd' => 1,
            'knpSave1' => 1,
            'txtId' => 1,
            'txtSluitdm1' => '2010-02-02',
            'txtKg1' => 90,
            'kzlArtikel1' => 1,
        ]);
        $this->assertNoNoise();
        $this->assertFout('Er is onvoldoende voer op voorraad.');
    }

    public function test_validate_save1_gewicht_zonder_artikel() {
        $this->post('/HokAfsluiten.php', [
            'ingelogd' => 1,
            'knpSave1' => 1,
            'txtSluitdm1' => '2010-02-02',
            'txtKg1' => 2,
        ]);
        $this->assertNoNoise();
        $this->assertFout('Het voer is onvolledig ingevuld.');
    }

    public function test_validate_save1_artikel_zonder_gewicht() {
        $this->post('/HokAfsluiten.php', [
            'ingelogd' => 1,
            'knpSave1' => 1,
            'txtSluitdm1' => '2010-02-02',
            'kzlArtikel1' => 2,
        ]);
        $this->assertNoNoise();
        $this->assertFout('Het voer is onvolledig ingevuld.');
    }

    public function test_post_save1_geen_hok() {
        $this->runfixture('voervoorraad');
        $this->post('/HokAfsluiten.php', [
            'ingelogd' => 1,
            'knpSave1' => 1,
            'txtId' => 1,
            'txtSluitdm1' => '2010-02-02',
            'txtKg1' => 1,
            'kzlArtikel1' => 1,
        ]);
        $this->assertNoNoise();
        $this->assertNotFout();
        // postconditie van inlezen_voer assereren?
    }

    public function test_post_save1_hok_ex_voer() {
        $this->runfixture('voervoorraad');
        $this->runfixture('hok');
        $this->post('/HokAfsluiten.php', [
            'ingelogd' => 1,
            'knpSave1' => 1,
            'txtId' => 1,
            'txtSluitdm1' => '2010-02-02',
        ]);
        $this->assertNoNoise();
        // zal ik deze "assertGoed" noemen? Dit is geen foutmelding
        $this->assertFout('1 is per 2010-02-02 afgesloten excl. voer.');
        // todo postconditie assereren. Wat is er nu gewijzigd?
    }

    public function test_post_save1_hok_inc_voer() {
        $this->runfixture('voervoorraad');
        $this->runfixture('hok');
        $this->post('/HokAfsluiten.php', [
            'ingelogd' => 1,
            'knpSave1' => 1,
            'txtId' => 1,
            'txtSluitdm1' => '2010-02-02',
            'txtKg1' => 1,
            'kzlArtikel1' => 1,
        ]);
        $this->assertNoNoise();
        $this->assertFout('1 is per 2010-02-02 afgesloten incl. voer.');
        // zelfde opmerkingen als voor ex_voer
    }

}
