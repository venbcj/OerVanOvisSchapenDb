<?php

class GebruikerTest extends IntegrationCase {

    const AANTAL_REDENEN_IN_NEWREADER_KEUZELIJSTEN = 14;

    public function tearDown(): void {
        # zodat geen 9 blijft hangen in sessie
        $this->get('/Home.php', ['ingelogd' => 1, 'uid' => 1]);
    }

    // deze vier tests dekken de radioknop "melden: Ja"

    public function testGetNotMeld() {
        $this->runfixture('hok');
        include "just_connect_db.php";
        $db->query("UPDATE tblLeden SET meld=0 WHERE lidId=42");
        $this->get('/Gebruiker.php', ['ingelogd' => 1, 'pstId' => 42]);
        $this->assertNoNoise();
        $this->assertAbsent('"radMeld" value="1" checked');
    }

    public function testGetMeld() {
        include "just_connect_db.php";
        $this->db = $db;
        $this->assertTableWithPK('tblLeden', 'lidId', 42);
        $db->query("UPDATE tblLeden SET meld=1 WHERE lidId=42");
        $this->get('/Gebruiker.php', ['ingelogd' => 1, 'pstId' => 42]);
        $this->assertPresent('"radMeld" value="1" checked');
        $db->query("UPDATE tblLeden SET meld=0 WHERE lidId=42");
    }

    public function testPostNotMeld() {
        include "just_connect_db.php";
        $db->query("UPDATE tblLeden SET meld=1 WHERE lidId=42");
        $this->post('/Gebruiker.php', ['ingelogd' => 1, 'pstId' => 42, 'radMeld' => 0]);
        $this->assertAbsent('"radMeld" value="1" checked');
        $db->query("UPDATE tblLeden SET meld=0 WHERE lidId=42");
    }

    public function testPostMeld() {
        include "just_connect_db.php";
        $db->query("UPDATE tblLeden SET meld=0 WHERE lidId=42");
        $this->post('/Gebruiker.php', ['ingelogd' => 1, 'pstId' => 42, 'radMeld' => 1]);
        $this->assertPresent('"radMeld" value="1" checked');
    }

    public function testSaveGebruiker() {
        include "just_connect_db.php";
        $this->db = $db;
        $this->post('/Gebruiker.php', [
            'ingelogd' => 1,
            'uid' => 42, // hack. login.php vangt dit op;
            'knpSave' => 1,
            'txtRoep' => 'rr',
            'txtVoeg' => 'rr',
            'txtNaam' => 'rr',
            'txtTel' => 'rr',
            'txtMail' => '',
            'txtRelnr' => 993,
            'txtUrvo' => 994,
            'txtPrvo' => 995,
            'kzlReader' => 'Agrident',
            'radMeld' => 1,
            'radTech' => 1,
            'radFin' => 0,
            # 'radBeheer' => 1, # and this, my friend, is why we do not put the form implementation into the name
            'kzlAdm' => 1,
            'txtIngescand' => '01-01-2021',
        ]);
            // lid 1 moet niet gewijzigd zijn
        $this->assertTableWithPK('tblLeden', 'lidId', 1, ['prvo' => 22]);
        $this->assertTableWithPK('tblLeden', 'lidId', 42, ['prvo' => 995, 'meld' => 1, 'fin' => 0]);
        // teardown
        $this->db->query("DELETE FROM tblLeden WHERE lidId=42");
        $this->runsetup('tblLeden');
    }

    public function testUpdateGebruiker() {
        include "just_connect_db.php";
        $this->db = $db;
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
