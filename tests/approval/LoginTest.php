<?php

class LoginTest extends EndToEndCase {

    public static function setupBeforeClass(): void {
        static::runfixture('user-harm');
        static::runfixture('versie-1');
    }

    public function testHomepageIsLoggedOut() {
        $this->get('/index.php');
        $this->approve();
    }

    public function testLoginWithoutUserOrPasswordFails() {
        $this->get('/index.php');
        $this->post('/index.php', ['txtUser' => '', 'txtPassw' => '', 'knpLogin' => 1]);
        $this->assertAbsent('Je bent niet ingelogd');
    }

    public function testLoginWithIncorrectUserFails() {
        $this->get('/index.php');
        $this->post('/index.php', ['txtUser' => 'ONGELDIG', 'txtPassw' => 'harpje', 'knpLogin' => 1]);
        $this->assertAbsent('Je bent niet ingelogd');
    }

    public function testLoginCorrect() {
        $this->get('/index.php');
        $this->post('/index.php', ['txtUser' => 'harm', 'txtPassw' => 'harpje', 'knpLogin' => 1]);
        $this->approve();
    }

    public function testAlreadyLoggedin() {
        $this->get('/Home.php', ['ingelogd' => 1]);
        $this->approve();
    }

}
