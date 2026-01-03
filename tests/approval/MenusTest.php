<?php

class MenusTest extends IntegrationCase {

    public function testMenuMeldenRood() {
        $this->runfixture('request-lid-codes');
        # $this->snapshot(['tblRequest','tblMelding', 'tblHistorie', 'tblStal', 'tblUbn']);
        $this->get('/Melden.php', ['ingelogd' => 1]);
        $this->approve();
    }

    public function testMenuMeldenBlauw() {
        # $this->snapshot(['tblRequest','tblMelding', 'tblHistorie', 'tblStal', 'tblUbn']);
        # Deze snapshots laten een verschil zien. Mooi, dan werkt het transactie-mechaniek.
        $this->get('/Melden.php', ['ingelogd' => 1]);
        $this->approve();
    }

    public function testMenuHome() {
        $this->get('/Home.php', ['ingelogd' => 1]);
        $this->approve();
    }

    public function testMenuAlerts() {
        $this->get('/Alerts.php', ['ingelogd' => 1]);
        $this->approve();
    }

    public function testMenuRapport() {
        $this->get('/Rapport.php', ['ingelogd' => 1]);
        $this->approve();
    }

    public function testMenuRapport1() {
        $this->get('/Rapport1.php', ['ingelogd' => 1]);
        $this->approve();
    }

    public function testMenuBeheer() {
        $this->get('/Beheer.php', ['ingelogd' => 1]);
        $this->approve();
    }

    public function testMenuInkoop() {
        $this->get('/Inkoop.php', ['ingelogd' => 1]);
        $this->approve();
    }

    public function testMenuFinance() {
        $this->get('/Finance.php', ['ingelogd' => 1]);
        $this->approve();
    }

}
