<?php

class GebruikerPageTest extends IntegrationCase {

    public function testGebruikerPageForm() {
        $this->runfixture('user-harm');
        $this->runfixture('user-kobus');
        $this->runfixture('hok');
        include "just_connect_db.php";
        $db->query("delete from tblRedenuser");
        $this->get('/Gebruiker.php', ['ingelogd' => 1, 'pstId' => 42]);
        $this->approve();
    }

    public function testGebruikerPageFormAgrident() {
        $this->runfixture('user-harm');
        $this->runfixture('user-kobus');
        $this->runfixture('user-kobus-agrident');
        $this->runfixture('hok');
        include "just_connect_db.php";
        $db->query("delete from tblRedenuser");
        $this->get('/Gebruiker.php', ['ingelogd' => 1, 'pstId' => 42]);
        $this->approve();
    }

}
