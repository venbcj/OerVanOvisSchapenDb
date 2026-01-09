<?php

class MedicijnenTest extends IntegrationCase {

    public function test_get() {
        $this->get('/Medicijnen.php', [
            'ingelogd' => 1,
        ]);
        $this->assertNoNoise();
    }

    public function test_get_met_inactieve() {
        $this->runfixture('pil-inkoop');
        $this->runSQL("UPDATE tblArtikel SET actief=0, soort='pil'");
        $this->get('/Medicijnen.php', [
            'ingelogd' => 1,
        ]);
        $this->assertNoNoise();
        $this->assertPresent('Medicijnen niet in gebruik:');
    }

    public function test_post_nothing() {
        $this->post('/Medicijnen.php', [
            'ingelogd' => 1,
            'knpInsert_' => 1,
            'insNaam_' => 1,
        ]);
        $this->assertNoNoise();
    }

    public function test_post_duplicate() {
        $this->runfixture('pil-inkoop');
        // todo moet die soort='pil' niet in de fixture?
        $this->runSQL("UPDATE tblArtikel SET soort='pil'");
        $this->post('/Medicijnen.php', [
            'ingelogd' => 1,
            'knpInsert_' => 1,
            'insNaam_' => 'test',
        ]);
        $this->assertNoNoise();
        $this->assertFout('Dit medicijn bestaat al.');
    }

    // NOTE als je iets anders in insPres wil dan insNaam, wijzig dan tblArtikel.naamreader

    public function test_post_zonder_inkoop() {
        $this->runfixture('pil-inkoop');
        $this->runSQL("UPDATE tblArtikel SET soort='pil'");
        $this->runSQL("DELETE FROM tblInkoop");
        $this->runSQL("UPDATE tblArtikel SET actief=1");
        $this->post('/Medicijnen.php', [
            'ingelogd' => 1,
            'knpInsert_' => 1,
            'insNaam_' => 'test',
        ]);
        $this->assertNoNoise();
        $this->assertOptieCount('kzlNhd_93', 5); // 93 is het artikelid uit de fixture
    }

}
