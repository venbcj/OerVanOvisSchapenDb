<?php

class NewUserTest extends IntegrationCase {

    private const NEW_USER_ID = 43; // in fixtures is 42 de laatste user
    private const NEW_USER_DIR = 'user_43'; // in fixtures is 42 de laatste user
    // oh boy. Soms bestaat user_43 al, en probeert de code dus user_44 aan te maken.

    protected $restore_keys_before = true;

    protected function restore_keys() {
        $this->db->query("ALTER TABLE tblLeden AUTOINCREMENT=42");
    }

    public function testGet() {
        $this->get('/Newuser.php', ['ingelogd' => 1]);
        $this->assertNoNoise();
    }

    public function testSave() {
        include "just_connect_db.php";
        $this->db = $db;
        if (is_dir(self::NEW_USER_DIR)) {
            rmdir(self::NEW_USER_DIR . '/Readerbestanden');
            rmdir(self::NEW_USER_DIR . '/Readerversies');
            rmdir(self::NEW_USER_DIR);
        }
        $count = [];
        $this->expectNewRecordsInTables([
            'tblLeden' => 1,
            'tblHok' => 1,
            'tblMomentuser' => 2,
            'tblEenheiduser' => $this->tableRowcount('tblEenheid'),
            'tblElementuser' => $this->tableRowcount('tblElement'),
            'tblRubriekuser' => 55,
            'tblPartij' => 2,
            'tblRelatie' => 2,
        ]);

        $this->post(
            '/Newuser.php',
            array_merge(
                [
                    'ingelogd' => 1,
                    'knpSave' => 1,
                ],
                $this->some_user_form()
            )
        );
        $this->assertNoNoise();
        # $this->assertNotFout();
        # deze "fout"-detectie is een misnomer, omdat ook $goed eroverheen wordt gevouwen.
        $this->assertFout('De gebruiker is ingevoerd.');
        // zou nog de vulling van de volgtabellen kunnen assereren. Maar die hangt af van de rest van het schema :S
        $this->assertTableWithPK('tblLeden', 'lidId', self::NEW_USER_ID);
        $this->assertTablesGrew();
    }

    private function some_user_form() {
        return [
            'user' => [
                'roep' => 'a',
                'voegsel' => 'v',
                'naam' => 'n',
                'tel' => '1',
                'mail' => 'a@b',
                'ubn' => 4,
                'relnr' => 2,
                'urvo' => '13',
                'prvo' => '26',
                'reader' => 3,
                'meld' => 1,
                'tech' => 0,
                'fin' => 0,
            ],
        ];
    }

}
