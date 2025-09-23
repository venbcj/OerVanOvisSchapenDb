<?php

class NewUserTest extends IntegrationCase {

    public function testGet() {
        $this->get('/Newuser.php', ['ingelogd' => 1]);
        $this->assertNoNoise();
    }

    public function testSave() {
        $this->runfixture('user-harm');
        if (is_dir('user_2')) {
            rmdir('user_2/Readerbestanden');
            rmdir('user_2/Readerversies');
            rmdir('user_2/');
        }
        $this->post('/Newuser.php', [
            'ingelogd' => 1,
            'knpSave' => 1,
            'txtRoep' => 'a',
            'txtVoeg' => 'v',
            'txtNaam' => 'n',
            'txtTel' => '1',
            'txtMail' => 'a@b',
            'txtUbn' => 4,
            'txtRelnr' => 2,
            'txtUrvo' => '13',
            'txtPrvo' => '26',
            'kzlReader' => 3,
            'radMeld' => 1,
            'radTech' => 0,
            'radFin' => 0,
        ]);
        $this->assertNoNoise();
        # $this->assertNotFout();
        # deze "fout"-detectie is een misnomer, omdat ook $goed eroverheen wordt gevouwen.
        $this->assertFout('De gebruiker is ingevoerd.');
    }

}
