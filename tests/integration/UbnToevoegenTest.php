<?php

class UbnToevoegenTest extends IntegrationCase {

    public function testSave_zet_inactief() {
        $this->runfixture('ubn-reset');
        $this->uses_db();
        $this->post('/Ubn_toevoegen.php', [
            'ingelogd_' => 1,
            'knpSave_' => 1,
            'chbActief_1' => 0,
        ]);
        $this->assertNoNoise();
        $this->assertTableWithPK('tblUbn', 'ubnId', 1, ['actief' => false]);
    }

    public function testSave_zet_adres_plaats() {
        $this->runfixture('ubn-reset');
        $this->uses_db();
        $this->post('/Ubn_toevoegen.php', [
            'ingelogd_' => 1,
            'knpSave_' => 1,
            'txtAdres_1' => 'Laanstraat 79A',
            'txtPlaats_1' => 'Zwoerden',
        ]);
        $this->assertNoNoise();
        $this->assertTableWithPK('tblUbn', 'ubnId', 1, ['adres' => 'Laanstraat 79A', 'plaats' => 'Zwoerden']);
    }

    public function testSave_verwijdert() {
        $this->runfixture('ubn-reset');
        $this->uses_db();
        $this->post('/Ubn_toevoegen.php', [
            'ingelogd_' => 1,
            'knpSave_' => 1,
            'chbDel_1' => 1,
        ]);
        $this->assertNoNoise();
        $this->assertTablesGrew(['tblUbn' => -1]);
    }

}
