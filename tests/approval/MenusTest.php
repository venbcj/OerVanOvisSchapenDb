<?php

class MenusTest extends IntegrationCase {

    public function testMenuMeldenRood() {
        $this->runfixture('request-lid-codes');
        $this->get('/Melden.php', ['ingelogd' => 1]);
        $this->approve();
    }

    public function testMenuMeldenBlauw() {
        $this->runfixture('request-none');
        $this->get('/Melden.php', ['ingelogd' => 1]);
        $this->approve();
    }

    public function testMenuHome() {
        $this->runfixture('request-none');
        $this->get('/Home.php', ['ingelogd' => 1]);
        $this->approve();
    }

    public function testMenuAlerts() {
        $this->runfixture('request-none');
        $this->get('/Alerts.php', ['ingelogd' => 1]);
        $this->approve();
    }

    public function testMenuRapport() {
        $this->runfixture('request-none');
        $this->get('/Rapport.php', ['ingelogd' => 1]);
        $this->approve();
    }

    public function testMenuRapport1() {
        $this->runfixture('request-none');
        $this->get('/Rapport1.php', ['ingelogd' => 1]);
        $this->approve();
    }

    public function testMenuBeheer() {
        $this->runfixture('request-none');
        $this->get('/Beheer.php', ['ingelogd' => 1]);
        $this->approve();
    }

    public function testMenuInkoop() {
        $this->runfixture('request-none');
        $this->get('/Inkoop.php', ['ingelogd' => 1]);
        $this->approve();
    }

    public function testMenuFinance() {
        $this->runfixture('request-none');
        $this->get('/Finance.php', ['ingelogd' => 1]);
        $this->approve();
    }

}
