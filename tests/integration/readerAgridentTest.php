<?php

class ReaderAgridentTest extends IntegrationCase {

    private $path = '/readerAgrident.php';

    private const INVALID_READER = '8888888888888888888888888888888888888888888888888888888888888888';
    private const VALID_READER = '3333333333333333333333333333333333333333333333333333333333333333';

    public function setup(): void {
        parent::setup();
        unset($_SERVER['HTTP_Authorization']);
    }

    public function testRefusesGet() {
        $this->get($this->path);
        $this->assertEquals('', $this->output);
    }

    public function testRefusesMissingAuthorization() {
        $this->post($this->path);
        $this->assertResponsecode(401);
        $this->assertEquals('authorization header bestaat niet.', $this->output);
    }

    public function testRefusesIncorrectAuthorization() {
        $_SERVER['HTTP_Authorization'] = 'kaas';
        $this->post($this->path);
        $this->assertResponsecode(401);
        $this->assertEquals('authorization header heeft niet de juiste opmaak.', $this->output);
    }

    public function testRefusesUnauthorized() {
        // er is geen gebruiker met een reader-code die uit 64 achten bestaat
        $_SERVER['HTTP_Authorization'] = 'Bearer ' . self::INVALID_READER;
        $this->post($this->path);
        $this->assertResponsecode(401);
        $this->assertEquals('via authorization header wordt de gebruiker niet gevonden.', $this->output);
    }

    public function testRefusesOtherThanPost() {
        $this->uses_db();
        $this->runSQL("UPDATE tblLeden SET readerkey='" . self::VALID_READER . "' WHERE lidId=1");
        $_SERVER['HTTP_Authorization'] = 'Bearer ' . self::VALID_READER;
        $this->patch($this->path);
        $this->assertResponsecode(405);
        $this->assertEquals('', $this->output);
    }

    // dit kan niet inhoudelijk: controller leest in php://input, en die stream kunnen we niet beinvloeden
    // Zie daarvoor de test op JsonAgridentParser
    public function testSucces() {
        $this->uses_db();
        $this->runSQL("UPDATE tblLeden SET readerkey='" . self::VALID_READER . "' WHERE lidId=1");
        $_SERVER['HTTP_Authorization'] = 'Bearer ' . self::VALID_READER;
        $this->post($this->path);
        $this->assertResponsecode(200);
        // er moet nu een "backup" van de invoerstroom geschreven zijn, met de timestamp van nu.
        // Dat is ook lastig testen.
    }

    // wellicht Pull Up Method
    protected function assertResponsecode($code) {
        $this->assertEquals($code, http_response_code());
    }

}
