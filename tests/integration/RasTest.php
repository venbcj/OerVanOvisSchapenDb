<?php

class RasTest extends IntegrationCase {

    public function test_get() {
        // zouden fixtures bij kunnen voor keuzelijsten: rasuser, eh dat was het. wel met eigen=0; de raskiezer is wel gevuld.
        $this->get('/Ras.php', [
            'ingelogd' => 1,
        ]);
        $this->assertNoNoise();
    }

    public function test_post_insert_zonder_raskeuze() {
        $this->post('/Ras.php', [
            'ingelogd' => 1,
            'knpInsert_' => 1,
        ]);
        $this->assertNoNoise();
        $this->assertFout('U heeft geen ras geselecteerd.');
    }

    public function test_post_insert() {
        $this->post('/Ras.php', [
            'ingelogd' => 1,
            'knpInsert_' => 1,
            'kzlRas_' => 1,
            'insScan_' => 1,
            'insSort_' => 1,
        ]);
        $this->assertNoNoise();
    }

    public function test_post_insert2_zonder_ras() {
        $this->post('/Ras.php', [
            'ingelogd' => 1,
            'knpInsert2_' => 1,
        ]);
        $this->assertNoNoise();
        $this->assertFout('Er is geen ras ingevoerd.');
    }

    public function test_post_insert2() {
        $this->post('/Ras.php', [
            'ingelogd' => 1,
            'knpInsert2_' => 1,
            'txtRas_' => 'nieuw',
        ]);
        $this->assertNoNoise();
    }

    public function test_post_save_biocontrol() {
        $this->runSQL("UPDATE tblLeden SET reader='Biocontrol'");
        $this->post('/Ras.php', [
            'ingelogd_' => 1,
            'knpSave_' => 1,
            'txtScan_1' => 1,
            'chbActief_1' => 1,
        ]);
        $this->assertNoNoise();
    }

    public function test_post_save_wijzig_actief() {
        Response::setTest();
        $this->post('/Ras.php', [
            'ingelogd_' => 1,
            'knpSave_' => 1,
            'txtScan_1' => 1,
            'chbActief_1' => 0,
        ]);
        $this->assertNoNoise();
        $this->assertTrue(Response::isRedirected());
    }

    public function test_post_save_wijzig_sort() {
        $this->runSQL("UPDATE tblLeden SET reader='Agrident'");
        $this->post('/Ras.php', [
            'ingelogd_' => 1,
            'knpSave_' => 1,
            'txtScan_1' => 1,
            'chbActief_1' => 1,
            'txtSort_1' => 9,
        ]);
        $this->assertNoNoise();
        $this->assertTableWhereHas('tblRasuser', ['rasId' => 1], ['sort' => 9]);
    }

    // todo case waar scannummer al bestaat
}
