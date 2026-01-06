<?php

class LoginTest extends IntegrationCase {

    public function setup(): void {
        parent::setup();
        $this->runfixture('versie-1');
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
    // Faalt omdat de readerversies-link rood is ipv zwart.
    public function testAlreadyLoggedin() {
        // [v] met alle requests verwijderd verwachten we een meld_color=zwarte link naar RVO/Melden
        // [X] met appfile en takenfile wordt actuele_versie ja, en verwachten we een readercolor=zwarte link naar readerversies
        $this->get('/Home.php', ['ingelogd' => 1]);
        $this->approve();
    }

}
