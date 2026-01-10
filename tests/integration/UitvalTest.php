<?php

class UitvalTest extends IntegrationCase {

    public function test_get() {
        $this->runfixture('moment');
        $this->get('/Uitval.php', [
            'ingelogd' => 1,
        ]);
        $this->assertNoNoise();
    }

    public function test_post_save_uitv_reden() {
        $this->post('/Uitval.php', [
            'ingelogd__' => 1,
            'knpSaveUitv__' => 1,
            'chbPil_reden_1' => 1, // opent eerste if (reden); vereist fldUitv, fldAfoer, fldSterfte
            'chbUitval_reden_1' => 1,
            'chbAfvoer_reden_1' => 1,
            'chbSterfte_reden_1' => 1,
        ]);
        $this->assertNoNoise();
    }

    public function test_post_save_uitv_moment() {
        $this->runSQL("INSERT INTO tblMomentuser(momuId, lidId, momId, scan) VALUES(1, 1, 1, 'bbr')");
        $this->post('/Uitval.php', [
            'ingelogd__' => 1,
            'knpSaveUitv__' => 1,
            'chbPil_moment_1' => 1, // opent tweede if (moment); vereist fldActief, dbActief
            'chbActief_moment_1' => 0, // nieuw record (fixture) is default actief, we zetten het uit
            'txtScan_moment_1' => 'znm',
        ]);
        $this->assertNoNoise();
    }

    public function test_post_save_reden() {
        $this->post('/Uitval.php', [
            'ingelogd__' => 1,
            'knpSaveReden__' => 1,
        ]);
        $this->assertNoNoise();
    }

    public function test_post_insert() {
        $this->post('/Uitval.php', [
            'ingelogd__' => 1,
            'knpInsertReden__' => 1,
            'kzlReden__' => 1,
        ]);
        $this->assertNoNoise();
    }

    public function test_post_new() {
        $this->post('/Uitval.php', [
            'ingelogd__' => 1,
            'knpNewReden__' => 1,
            'txtNaam__' => 1,
        ]);
        $this->assertNoNoise();
    }

}
