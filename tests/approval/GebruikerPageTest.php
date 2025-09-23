<?php

class GebruikerPageTest extends IntegrationCase {

    public function testGebruikerPageForm() {
        $this->runfixture('user-kobus');
        $this->runfixture('hok');
        $this->get('/Gebruiker.php', ['ingelogd' => 1, 'pstId' => 42]);
        $this->approve();
    }

    public function testGebruikerPageFormAgrident() {
        $this->runfixture('user-kobus');
        $this->runfixture('user-kobus-agrident');
        $this->runfixture('hok');
        $this->get('/Gebruiker.php', ['ingelogd' => 1, 'pstId' => 42]);
        $this->approve();
    }

}
