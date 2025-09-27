<?php

class GebruikerPageTest extends IntegrationCase {

    public function testGebruikerPageForm() {
        include "just_connect_db.php";
        $this->db = $db;
        $this->db->query("DELETE FROM tblLeden WHERE lidId=42");
        $this->runsetup("tblLeden");
        $this->runfixture('user-harm');
        $this->runfixture('user-kobus');
        $this->runfixture('hok');
        $this->assertTableWithPK('tblLeden', 'lidId', 42, ['login' => 'kobus', 'alias' => 'koob']);
        $db->query("delete from tblRedenuser");
        $db->query("delete from tblRequest");
        $this->get('/Gebruiker.php', ['ingelogd' => 1, 'pstId' => 42]);
        $this->approve();
    }

    public function testGebruikerPageFormAgrident() {
        include "just_connect_db.php";
        $this->db = $db;
        $this->db->query("DELETE FROM tblLeden WHERE lidId=42");
        $this->runsetup("tblLeden");
        $this->runfixture('user-harm');
        $this->runfixture('user-kobus');
        $this->runfixture('user-kobus-agrident');
        $this->runfixture('hok');
        $this->assertTableWithPK('tblLeden', 'lidId', 42, ['login' => 'kobus', 'alias' => 'koob']);
        $db->query("delete from tblRedenuser");
        $db->query("delete from tblRequest");
        $this->get('/Gebruiker.php', ['ingelogd' => 1, 'pstId' => 42]);
        $this->approve();
    }

}
