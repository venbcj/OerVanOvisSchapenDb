<?php

class InsAanvoerTest extends IntegrationCase {

    public function test_invoer_moeder() {
        $this->runfixture('impagrident');
        $this->post('/InsAanvoer.php', [
            // noodzakelijk
            'ingelogd_' => 1,
            'kzlFase_1' => 'moeder',
            'chbkies_1' => 1,
            'chbDel_1' => 0,
            // om post_readerAanv te bereiken
            'knpInsert_' => 1,
// om "controle op verplichte velden" te bereiken
            'txtaanwdm_1' => '2020-01-01',
            'kzlUbn_1' => '1',
            'kzlras_1' => '1',
            'kzlKleur_1' => '1',
            'txtHnr_1' => '9',
            'kzlHerk_1' => 1,
            'txtkg_1' => 4,
        ]);
        $this->assertNoNoise();
    }

    public function test_invoer_lam() {
        $this->runfixture('impagrident');
        $this->runfixture('schaap-331');
        $this->post('/InsAanvoer.php', [
            // noodzakelijk
            'ingelogd_' => 1,
            'kzlFase_1' => 'lam',
            'chbkies_1' => 1,
            'chbDel_1' => 0,
            // om post_readerAanv te bereiken
            'knpInsert_' => 1,
// om "controle op verplichte velden" te bereiken
            'txtaanwdm_1' => '2020-01-01',
            'kzlHok_1' => 1,
            'kzlUbn_1' => '1',
            // tot hier toe levert het "undefined fldMoeder" op. En inderdaad: die wordt nergens gezet.
            // opgelost met een fixture ovor schaap 331
            'kzlHerk_1' => 1,
            'txtkg_1' => 4,
        ]);
        $this->assertNoNoise();
    }

}
