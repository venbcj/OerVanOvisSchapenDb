<?php

class GebruikerPageTest extends IntegrationCase {

    public function testGebruikerPageForm() {
        $this->runsetup('tblLeden');
        $this->runfixture('user-kobus');
        $this->runfixture('hok');
        $this->assertTableWithPK('tblLeden', 'lidId', 42, ['login' => 'kobus', 'alias' => 'koob']);
        $this->db->query("delete from tblRedenuser");
        $this->db->query("delete from tblRequest");
        $this->get('/Gebruiker.php', ['ingelogd' => 1, 'pstId' => 42]);
        $this->approve();
    }

    public function testGebruikerPageFormAgrident() {
        $this->runsetup('tblLeden');
        $this->runfixture('user-kobus-agrident');
        $this->runfixture('hok');
        // even checken dat we echt tegen de goede data aankijken:
        $this->assertTableWithPK('tblLeden', 'lidId', 42, ['login' => 'kobus', 'alias' => 'koob', 'reader' => 'Agrident']);
        $this->db->query("delete from tblRedenuser");
        $this->db->query("delete from tblRequest");
        $this->get('/Gebruiker.php', ['ingelogd' => 1, 'pstId' => 42]);
        $this->approve();
    }

}
