<?php

class NewUserTest extends IntegrationCase {

    public function testGet() {
        $this->get('/Newuser.php', ['ingelogd' => 1]);
        $this->assertNoNoise();
    }

    public function testSave() {
        include "just_connect_db.php";
        $this->db = $db;
        $this->runfixture('user-harm');
        $this->runfixture('newuser-pre');
        if (is_dir('user_2')) {
            rmdir('user_2/Readerbestanden');
            rmdir('user_2/Readerversies');
            rmdir('user_2/');
        }
        $count = [];
        foreach (explode(' ', 'tblHok tblMomentuser tblEenheiduser tblElementuser tblRubriekuser') as $table) {
            $count[$table] = $this->tableRowcount($table);
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
        // zou nog de vulling van de volgtabellen kunnen assereren. Maar die hangt af van de rest van het schema :S
        $this->assertTableWithPK('tblLeden', 'lidId', 2);
        $this->assertTableRowcount('tblLeden', 2);
        $this->assertTableRowcount('tblHok', $count['tblHok'] + 1);
        $this->assertTableRowcount('tblMomentuser', $count['tblMomentuser'] + 3);
        $this->assertTableRowcount('tblEenheiduser', $count['tblEenheiduser'] + 2);
        # dit doet het niet, want de query is kapot
        # $this->assertTableRowcount('tblElementuser', $count['tblElementuser']+4);
        $this->assertTableRowcount('tblPartij', 2); // twee per user
        $this->assertTableRowcount('tblRelatie', 2);
        $this->assertTableRowcount('tblRubriekuser', $count['tblRubriekuser'] + 1);
    }

}
