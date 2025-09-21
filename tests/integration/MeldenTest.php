<?php

class MeldenTest extends IntegrationCase {

    public function testIncompleteRvo() {
        $this->runfixture('user-geen-rvo');
        $this->get('/Melden.php', ['ingelogd' => 1]);
        $this->assertNoNoise();
        $this->assertPresent('Melden is niet mogelijk');
        $this->assertAbsent('href="Melden.php"');
    }

    public function testGeenGeboortes() {
        $this->runfixture('user-harm');
        $this->get('/Melden.php', ['ingelogd' => 1]);
        $this->assertAbsent('Melden is niet mogelijk');
        $this->assertAbsent('href="MeldGeboortes.php');
    }

    public function testNotAuthorized() {
        // kobus heeft geen meld-module.
        $this->runfixture('user-kobus');
        $this->get('/Melden.php', ['ingelogd' => 42]);
        // TODO consequent dubbelquote gebruiken in html
        $this->assertPresent("img src='Melden_php.jpg");
    }

}
