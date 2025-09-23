<?php

class GebruikerTest extends IntegrationCase {

    // deze vier tests dekken de radioknop "melden: Ja"

    public function testGetNotMeld() {
        $this->runfixture('user-kobus');
        $this->runfixture('hok');
        $this->get('/Gebruiker.php', ['ingelogd' => 1, 'pstId' => 42]);
        $this->assertNoNoise();
        $this->assertAbsent('"radMeld" value="1" checked');
    }

    public function testGetMeld() {
        $this->runfixture('user-kobus');
        include "just_connect_db.php";
        $db->query("UPDATE tblLeden SET meld=1 WHERE lidId=42");
        $this->get('/Gebruiker.php', ['ingelogd' => 1, 'pstId' => 42]);
        $this->assertPresent('"radMeld" value="1" checked');
    }

    public function testPostNotMeld() {
        $this->runfixture('user-kobus');
        include "just_connect_db.php";
        $db->query("UPDATE tblLeden SET meld=1 WHERE lidId=42");
        $this->post('/Gebruiker.php', ['ingelogd' => 1, 'pstId' => 42, 'radMeld' => 0]);
        $this->assertAbsent('"radMeld" value="1" checked');
    }

    public function testPostMeld() {
        $this->runfixture('user-kobus');
        $this->post('/Gebruiker.php', ['ingelogd' => 1, 'pstId' => 42, 'radMeld' => 1]);
        $this->assertPresent('"radMeld" value="1" checked');
    }

}
