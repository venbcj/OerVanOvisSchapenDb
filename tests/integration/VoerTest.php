<?php

class VoerTest extends IntegrationCase {

    public function test_get() {
        $this->get('/Voer.php', [
            'ingelogd' => 1,
        ]);
        $this->assertNoNoise();
        // todo assert "Voer niet in gebruik" absent
    }

    // todo fixture voor niet-actieve artikelen,
    // test met assert "Voer niet in gebruik" present

    public function test_post_insert() {
        $this->runfixture('crediteur'); // zodat qryLevcier iets bevat
        $this->post('/Voer.php', [
            'ingelogd_' => 1,
            'knpInsert_' => 1,
            'insNaam_' => 1,
            'insStdat_' => 1,
            'insNhd_' => 1,
            'insBtw_' => 1,
            'insRelatie_' => 1,
            'insRubriek_' => 1,
        ]);
        $this->assertNoNoise();
    }

    public function test_post_save() {
        $this->runfixture('voervoorraad');
        $this->post('/Voer.php', [
            'ingelogd_' => 1,
            'knpSave_' => 1,
            'txtNaam_1' => 'a',
            'txtStdat_1' => 1,
            'txtGewicht_1' => 1,
            'kzlBtw_1' => 1,
            'txtRegnr_1' => 1,
            'kzlRelatie_1' => 1,
            'txtWdgnV_1' => 1,
            'kzlRubriek_1' => 1,
        ]);
        $this->assertNoNoise();
    }

}
