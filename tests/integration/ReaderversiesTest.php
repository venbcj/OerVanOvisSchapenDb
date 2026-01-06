<?php

class ReaderversiesTest extends IntegrationCase {

    public function test_get() {
        $this->runSQL("UPDATE tblLeden SET reader='Agrident'");
        $this->get('/Readerversies.php', [
            'ingelogd' => 1,
        ]);
        $this->assertNoNoise();
    }

    public function test_postInsert() {
        $this->runSQL("UPDATE tblLeden SET reader='Agrident'");
        $this->post('/Readerversies.php', [
            'ingelogd' => 1,
            'knpInsert' => 1,
            'insDatum' => '2010-01-01',
            'insVersie' => 1,
            'insNaamApp' => 1,
            'insNaamTaak' => 1,
            'insToel' => 1,
        ]);
        $this->assertNoNoise();
    }

    # Later oppakken, eerst bestandsbewerking doorgronden
    # public function test_postAfronden() {
    #     $this->runSQL("UPDATE tblVersiebeheer SET Id=1");
    #     $this->runSQL("UPDATE tblLeden SET reader='Agrident'");
    #     $this->post('/Readerversies.php', [
    #         'ingelogd' => 1,
    #         'knpAfronden_1' => 1,
    #     ]);
    #     $this->assertNoNoise();
    # }

    public function test_setup_versies() {
        $dir = getcwd();
        chdir('../..');
        $vars = setup_versies($this->db, __DIR__.'/user_1');
        chdir($dir);
        $this->assertEquals(1, $vars['appfile_exists'], 'appfile lukt niet');
        $this->assertEquals(1, $vars['takenfile_exists'], 'takenfile lukt niet');
        $this->assertEquals('Ja', $vars['actuele_versie']);
    }

}
