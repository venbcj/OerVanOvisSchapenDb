<?php

class GebruikerTest extends IntegrationCase {

    const AANTAL_REDENEN_IN_NEWREADER_KEUZELIJSTEN = 14;

    public function tearDown(): void {
        # zodat geen 9 blijft hangen in sessie
        $this->get('/Home.php', ['ingelogd' => 1, 'uid' => 1]);
    }

    public static function teardownAfterClass(): void {
        self::runsetup('tblLeden');
    }

    // deze vier tests dekken de radioknop "melden: Ja"

    public function testGetNotMeld() {
        $this->runfixture('hok');
        $this->uses_db();
        $this->db->query("UPDATE tblLeden SET meld=0 WHERE lidId=42");
        $this->get('/Gebruiker.php', ['ingelogd' => 1, 'pstId' => 42]);
        $this->assertNoNoise();
        $this->assertAbsent('"user[meld]" value="1" checked');
        $this->assertPresent('"user[meld]" value="0" checked');
    }

    public function testGetMeld() {
        $this->uses_db();
        $this->runsetup('tblLeden');
        $this->assertTableWithPK('tblLeden', 'lidId', 42);
        $this->db->query("UPDATE tblLeden SET meld=1 WHERE lidId=42");
        $this->get('/Gebruiker.php', ['ingelogd' => 1, 'pstId' => 42]);
        $this->assertPresent('"user[meld]" value="1" checked');
        $this->db->query("UPDATE tblLeden SET meld=0 WHERE lidId=42");
    }

    public function testPostNotMeld() {
        $this->uses_db();
        $this->db->query("UPDATE tblLeden SET meld=1 WHERE lidId=42");
        $this->post('/Gebruiker.php', ['ingelogd' => 1, 'pstId' => 42, 'user' => ['meld' => 0]]);
        $this->assertAbsent('"user[meld]" value="1" checked');
        $this->db->query("UPDATE tblLeden SET meld=0 WHERE lidId=42");
    }

    public function testPostMeld() {
        $this->uses_db();
        $this->db->query("UPDATE tblLeden SET meld=0 WHERE lidId=42");
        $this->post('/Gebruiker.php', ['ingelogd' => 1, 'pstId' => 42, 'user' => ['meld' => 1]]);
        $this->assertPresent('"user[meld]" value="1" checked');
    }

    public function testSaveGebruiker() {
        $this->uses_db();
        $this->runsetup('user-1');
        $this->runsetup('tblLeden');
        $this->post('/Gebruiker.php', [
            'ingelogd' => 1,
            'uid' => 42,
            // hack. login.php vangt dit op;
            'knpSave' => 1,
            'user' => [
                'roep' => 'rr',
                'voegsel' => 'rr',
                'naam' => 'rr',
                'tel' => 'rr',
                'mail' => '',
                'relnr' => 993,
                'urvo' => 994,
                'prvo' => 995,
                'reader' => 'Agrident',
                'meld' => 1,
                'tech' => 1,
                'fin' => 0,
                'beheer' => 0,
                'ingescand' => '01-01-2021',
            ],
        # 'radBeheer' => 1, # and this, my friend, is why we do not put the form implementation into the name
            'kzlAdm' => 1,
        ]);
        // lid 1 moet niet gewijzigd zijn
        $this->assertTableWithPK('tblLeden', 'lidId', 1, ['prvo' => 22]);
        $this->assertTableWithPK('tblLeden', 'lidId', 42, ['prvo' => 995, 'meld' => 1, 'fin' => 0]);
    }

    public function testUpdateGebruiker() {
        $this->uses_db();
        $this->db->query("DELETE FROM tblRedenuser WHERE lidId=42");
        $this->expectNewRecordsInTables(['tblRedenuser' => self::AANTAL_REDENEN_IN_NEWREADER_KEUZELIJSTEN]);
        $this->post('/Gebruiker.php', [
            'ingelogd' => 1,
            'uid' => 42, // hack. login.php vangt dit op;
            'knpUpdate' => 1,
        ]);
        $this->assertNoNoise();
        $this->assertTablesGrew();
    }

}
