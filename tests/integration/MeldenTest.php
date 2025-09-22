<?php

class MeldenTest extends IntegrationCase {

    // voor branch-coverage zou er ook een niet-ingelogd-test bij moeten.
    // 't Is wel goed.

    public function testNotAuthorized() {
        // kobus heeft geen meld-module.
        $this->runfixture('user-kobus');
        $this->get('/Melden.php', ['ingelogd' => 42]);
        // TODO consequent dubbelquote gebruiken in html
        $this->assertPresent('img src="Melden_php.jpg');
    }

    public function testIncompleteRvo() {
        $this->runfixture('user-geen-rvo');
        $this->get('/Melden.php', ['ingelogd' => 1]);
        $this->assertNoNoise();
        $this->assertPresent('Melden is niet mogelijk');
        $this->assertAbsent('href="Melden.php"');
    }

    public function testCompleteRvo() {
        $this->runfixture('user-harm');
        $this->get('/Melden.php', ['ingelogd' => 1]);
        $this->assertAbsent('Melden is niet mogelijk');
    }

    // de volgende drie tests leren van MeldenFunctionsTest. Dat is een unit-test die de implementatie test.
    // Nuttig om fixtures mee op te stellen, het is beter om scherm-verschijningen te meten.

    public function testGeenGeboortes() {
        $this->runfixture('user-harm');
        $this->runfixture('request-none');
        $this->get('/Melden.php', ['ingelogd' => 1]);
        $this->assertAbsent('href="MeldGeboortes.php');
    }

    public function testGeboorte() {
        $this->runfixture('user-harm');
        $this->runfixture('request-lid-codes');
        $this->get('/Melden.php', ['ingelogd' => 1]);
        $this->assertPresent('MeldGeboortes.php'); // beetje klooien. Dit zit in een href. Met een Presenter tussen Controller en View kun je die Presenter eleganter bevragen.
        $this->assertPresent('&nbsp 1 geboorte(s) te melden.');
    }

    public function testMeerdan60Afvoer() {
        $this->runfixture('user-harm');
        $this->runfixture('request-61-afvoer');
        $this->get('/Melden.php', ['ingelogd' => 1]);
        $this->assertPresent('MeldAfvoer.php'); // beetje klooien. Dit zit in een href. Met een Presenter tussen Controller en View kun je die Presenter eleganter bevragen.
        $this->assertPresent('&nbsp; 61 afvoer te melden.&nbsp&nbsp&nbsp U ziet per melding max. 60 schapen. ');
    }

    public function testImport() {
        $this->runfixture('user-harm');
        $this->runfixture('request-lid-codes');
        $this->runfixture('response');
        $dir = 'BRIGHT/';
        $reqfile = '_1_request.txt';
        $respfile = '_1_response.txt';
        $userdir = 'user_1/';
        if (!is_dir($dir)) mkdir($dir);
        if (!is_dir($userdir)) mkdir($userdir);
        touch("$dir$reqfile");
        touch("$dir$respfile");
        $this->get('/Melden.php', ['ingelogd' => 1]);
        $this->assertFalse(file_exists("$dir$reqfile"), "request niet verplaatst?");
        $this->assertFalse(file_exists("$dir$respfile"), "response niet verplaatst?");
        rmdir($dir);
    }

}
