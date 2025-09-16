<?php

class LoginTest extends IntegrationCase {

    public static function setupBeforeClass(): void {
        static::runfixture('user-harm');
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
        $this->post('/index.php', ['txtUser' => 'harm', 'txtPassw' => 'harpje', 'knpLogin' => 1]);
        $this->assertRedirected();
    }

    public function testAlreadyLoggedin() {
        $this->get('/Home.php', ['ingelogd' => 1]);
        $this->approve();
    }

}
