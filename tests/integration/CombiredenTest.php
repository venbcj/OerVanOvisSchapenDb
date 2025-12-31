<?php

class CombiredenTest extends IntegrationCase {

    public function testInsertD() {
        // dit kan niet werken... combireden zet insArtId op NULL, en tblCombireden.artId mag niet null zijn
        $this->post('/Combireden.php', [
            'ingelogd' => 1,
            'knpInsert_d' => 1,
            'insScan_d' => 1,
            'insReden_d' => 1,
            'insArt_d' => 1,
        ]);
        $this->assertNoNoise();
    }

    public function testInsertP() {
        $this->post('/Combireden.php', [
            'ingelogd' => 1,
            'knpInsert_p' => 1,
            'insScan_p' => 1,
            'insPil' => 1,
            'insReden_p' => 1,
            'insStdat' => 1,
        ]);
        $this->assertNoNoise();
    }

    public function testSaveD() {
        $this->runfixture('combireden-d');
        $this->post('/Combireden.php', [
            'ingelogd' => 1,
            'knpSave_d' => 1,
            'titel' => 1,
            'txtScan_d' => 1,
            'kzlReden_d' => 1,
            'txtId_d' => 1,
        ]);
        $this->assertNoNoise();
    }

    public function testSaveD_with_p_record() {
        $this->runfixture('combireden-d');
        $this->runSQL("insert into tblCombireden(scan, stdat, artId, tbl, reduId) values(1, 1, 5, 'p', 1)");
        $this->post('/Combireden.php', [
            'ingelogd' => 1,
            'knpSave_d' => 1,
            'titel' => 1,
            'txtScan_d' => 1,
            'kzlReden_d' => 1,
            'txtId_d' => 1,
        ]);
        $this->assertNoNoise();
    }

    public function testSaveP() {
        $this->runfixture('combireden-d');
        $this->runSQL("insert into tblCombireden(scan, stdat, artId, tbl, reduId) values(1, 1, 5, 'p', 1)");
        $this->post('/Combireden.php', [
            'ingelogd' => 1,
            'knpSave_p' => 1,
            'txtScan_p' => 1,
            'kzlPil' => 1,
            'txtStdat' => 1,
            'kzlReden_p' => 1,
            'txtId_p' => 1,
        ]);
        $this->assertNoNoise();
    }

}
