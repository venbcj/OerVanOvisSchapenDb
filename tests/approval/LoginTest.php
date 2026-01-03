<?php

class LoginTest extends IntegrationCase {

    public static function setupBeforeClass(): void {
        static::runfixture('versie-1');
    }

    public function testHomepageIsLoggedOut() {
        $this->get('/index.php');
        $this->approve();
    }

    public function testLoginWithoutUserOrPasswordFails() {
        $this->post('/index.php', ['txtUser' => '', 'txtPassw' => '', 'knpLogin' => 1]);
        $this->assertAbsent('Je bent niet ingelogd');
    }

    public function testLoginWithWrongUserFails() {
        $this->post('/index.php', ['txtUser' => 'ONGELDIG', 'txtPassw' => 'harpje', 'knpLogin' => 1]);
        $this->assertAbsent('Je bent niet ingelogd');
    }

    public function testLoginCorrect() {
        Response::setTest();
        $this->post('/index.php', ['txtUser' => 'harm', 'txtPassw' => 'harpje', 'knpLogin' => 1]);
        $this->assertRedirected();
    }

    // faalt bij seed 1758476906 ... ? waarom?
    // 1758477385
    public function testAlreadyLoggedin() {
        // met alle requests verwijderd verwachten we een zwarte link naar Readerversies
        $this->runfixture('request-none');
        $this->get('/Home.php', ['ingelogd' => 1]);
        $this->approve();
    }

}
