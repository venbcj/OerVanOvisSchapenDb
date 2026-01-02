<?php

class VoorraadcorrectieTest extends IntegrationCase {

    public function test_get() {
        // raakt pilregels
        $this->runfixture('artikelvoorraad');
        $this->get('/Voorraadcorrectie.php', [
            'ingelogd_' => 1,
            'pst' => 1,
        ]);
        $this->assertNoNoise();
    }

    public function test_get_voer() {
        // raakt voerregels
        $this->runfixture('voervoorraad');
        $this->get('/Voorraadcorrectie.php', [
            'ingelogd_' => 1,
            'pst' => 1,
        ]);
        $this->assertNoNoise();
    }

    public function test_postSave_voer() {
        $this->runfixture('voervoorraad');
        $this->post('/Voorraadcorrectie.php', [
            'ingelogd_' => 1,
            'knpSave_' => 1,
            // in fixture voervoorraad is artikel 1 voer, artikel 2 pil
            'txtCorat_1' => 1,
            'kzlCorr_1' => 'bij',
        ]);
        $this->assertNoNoise();
    }

    public function test_postSave_pil() {
        $this->runfixture('voervoorraad');
        $this->post('/Voorraadcorrectie.php', [
            'ingelogd_' => 1,
            'knpSave_' => 1,
            // in fixture voervoorraad is artikel 1 voer, artikel 2 pil
            'txtCorat_2' => 1,
            'kzlCorr_2' => 'bij',
        ]);
        $this->assertNoNoise();
    }

    public function test_validatie_nulvoorraad() {
        $this->runfixture('voervoorraad-nul');
        $this->post('/Voorraadcorrectie.php', [
            'ingelogd_' => 1,
            'knpSave_' => 1,
            'txtCorat_2' => 1,
            'kzlCorr_2' => 'af',
        ]);
        $this->assertNoNoise();
        $this->assertFout('De voorraad is reeds 0.');
    }

    public function test_validatie_teveel_af() {
        $this->runfixture('voervoorraad');
        $this->post('/Voorraadcorrectie.php', [
            'ingelogd_' => 1,
            'knpSave_' => 1,
            // kennelijk alleen bij voer-artikelen?
            'txtCorat_1' => 10,
            'kzlCorr_1' => 'af',
        ]);
        $this->assertNoNoise();
        $this->assertFout('De correctie kan niet meer zijn dan 9 cc.');
    }

    public function test_validatie_kanniet_bij() {
        $this->runfixture('voervoorraad-geen-afboek');
        $this->post('/Voorraadcorrectie.php', [
            'ingelogd_' => 1,
            'knpSave_' => 1,
            // kennelijk alleen bij voer-artikelen?
            'txtCorat_1' => 10,
            'kzlCorr_1' => 'bij',
        ]);
        $this->assertNoNoise();
        $this->assertFout('Er is niets (meer) afgeboekt. Bijboeken is niet mogelijk.');
    }

    public function test_validatie_teveel_bij() {
        $this->runfixture('voervoorraad');
        $this->post('/Voorraadcorrectie.php', [
            'ingelogd_' => 1,
            'knpSave_' => 1,
            // kennelijk alleen bij voer-artikelen?
            'txtCorat_1' => 10,
            'kzlCorr_1' => 'bij',
        ]);
        $this->assertNoNoise();
        $this->assertFout('Er is maximaal 1 cc bij te boeken.');
    }

}
