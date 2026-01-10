<?php

class RelatieTest extends IntegrationCase {

    public function test_get() {
        $this->runfixture('partij-1');
        $this->get('/Relatie.php', [
            'ingelogd' => 1,
            'pstid' => 11,
        ]);
        $this->assertNoNoise();
    }

    public function test_post_view() {
        $this->runfixture('partij-1');
        $this->post('/Relatie.php', [
            'ingelogd_' => 1,
            'txtpId_' => 11,
        ]);
        $this->assertNoNoise();
    }

    public function test_post_save() {
        $this->runfixture('partij-1');
        $this->post('/Relatie.php', [
            'ingelogd_' => 1,
            'txtpId_' => 11,
            'knpSave_' => 1,
            'txtrId_1' => 1,
            'txtStraat_1' => 'straat',
            'txtNr_1' => '42',
            'txtPc_1' => '7742 NL',
            'txtPlaats_1' => 'Loowoude',
            'chkActief_1' => 1,
        ]);
        $this->assertNoNoise();
    }

    public function test_post_nieuw_vervoer() {
        $this->runfixture('partij-1');
        $this->runSQL("DELETE FROM tblVervoer");
        $this->post('/Relatie.php', [
            'ingelogd_' => 1,
            'txtpId_' => 11,
            'knpSave_' => 1,
            'txtKent_' => 'HY-33-VL',
            'txtHang_' => 'vier',
        ]);
        $this->assertNoNoise();
    }

}
