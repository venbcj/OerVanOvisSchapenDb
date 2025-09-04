<?php

class LoginTest extends EndToEndCase {

    public function test_HomepageIsLoggedOut() {
        $this->visit('/index.php');
        $this->approve();
    }

    public function test_LoginFails() {
        $this->visit('/index.php');
        $this->assertPresent('<input type="submit" value="Inloggen"');
    }

}
