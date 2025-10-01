<?php

class PasswTest extends IntegrationCase {

    public static function setupBeforeClass(): void {
        self::runsetup('user-1');
    }

    public function setup(): void {
        require_once "autoload.php";
        $GLOBALS['passw'] = '';
        $_SERVER['HTTP_HOST'] = 'basq';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_SCHEME'] = 'http';
        $_SERVER['REQUEST_URI'] = '';
        $this->uses_db();
    }

    public function testGet() {
        # GIVEN
        Auth::logout();
        # WHEN
        ob_start();
        $db = $this->db;
        include "passw.php";
        $res = ob_get_clean();
        # THEN
        $this->assertEquals('', $GLOBALS['passw']);
    }

}
