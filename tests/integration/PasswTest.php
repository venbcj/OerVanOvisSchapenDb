<?php

class PasswTest extends IntegrationCase {

    public function setup():void {
        require_once "autoload.php";
        $_SERVER['HTTP_HOST'] = 'basq';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_SCHEME'] = 'http';
        $_SERVER['REQUEST_URI'] = '';
    }

    public function testGet() {
        # GIVEN
        # WHEN
        ob_start();
        include "passw.php";
        $res = ob_get_clean();
        # THEN
        $this->assertEquals('', $passw);
    }

    public function testValidateMissingPassword() {
        # GIVEN
        include "connect_db.php";
        $this->simulatePostRequest('/Wachtwoord.php', [
            'knpChange' => 1,
            'txtUser' => 'kobus',
            'txtUserOld' => 'harm',
            'txtOld' => '',
            'txtNew' => '',
        ]);
        $lid = 1; // aha, wij zijn een onderdeel van Gebruiker.
        # WHEN
        ob_start();
        include "passw.php";
        $res = ob_get_clean();
        # THEN
        $this->assertEquals('Gebruikersnaam of wachtwoord is onbekend.', $fout);
    }

    public function testValidateWachtwoordVerschillend() {
        # GIVEN
        include "connect_db.php";
        $this->simulatePostRequest('/Wachtwoord.php', [
            'knpChange' => 1,
            'txtUser' => 'kobus',
            'txtUserOld' => 'harm',
            'txtOld' => 'fruit',
            'txtNew' => 'groente',
            'txtBevest' => 'groente-verschil',
        ]);
        $lid = 1; // aha, wij zijn een onderdeel van Gebruiker.
        # WHEN
        ob_start();
        include "passw.php";
        $res = ob_get_clean();
        # THEN
        $this->assertEquals('Het nieuwe wachtwoord komt niet overeen met de bevestiging.', $fout);
    }

    public function testValidateWachtwoordFout() {
        # GIVEN
        include "connect_db.php";
        $this->simulatePostRequest('/Wachtwoord.php', [
            'knpChange' => 1,
            'txtUser' => 'kobus',
            'txtUserOld' => 'harm',
            'txtOld' => 'fruit',
            'txtNew' => 'groente',
            'txtBevest' => 'groente',
        ]);
        $lid = 1; // aha, wij zijn een onderdeel van Gebruiker.
        # WHEN
        ob_start();
        include "passw.php";
        $res = ob_get_clean();
        # THEN
        $this->assertEquals('Het oude wachtwoord is onjuist.', $fout);
    }

    public function testValidateWachtwoordTekort() {
        # GIVEN
        include "connect_db.php";
        $passw = 'harpje';
        $this->simulatePostRequest('/Wachtwoord.php', [
            'knpChange' => 1,
            'txtUser' => 'kobus',
            'txtUserOld' => 'harm',
            'txtOld' => 'harpje',
            'txtNew' => 'groen',
            'txtBevest' => 'groen',
        ]);
        $lid = 1; // aha, wij zijn een onderdeel van Gebruiker.
        # WHEN
        ob_start();
        include "passw.php";
        $res = ob_get_clean();
        # THEN
        $this->assertEquals('Het wachtwoord moet uit minstens 6 karakters bestaan.', $fout);
    }

    public function testPostBestaatAl() {
        $this->runfixture('user-harm');
        $this->runfixture('user-kobus');
        # GIVEN
        include "connect_db.php";
        $passw = 'harpje';
        $this->simulatePostRequest('/Wachtwoord.php', [
            'knpChange' => 1,
            'txtUser' => 'kobus',
            'txtUserOld' => 'harm',
            'txtOld' => 'harpje',
            'txtNew' => 'groente',
            'txtBevest' => 'groente',
        ]);
        $lid = 1; // aha, wij zijn een onderdeel van Gebruiker.
        # WHEN
        ob_start();
        include "passw.php";
        $res = ob_get_clean();
        # THEN
        # $this->assertTrue(isset($fout), $res);
        $this->assertEquals('Deze combinatie tussen gebruikersnaam en wachtwoord bestaat al. Kies een andere combinatie.', $fout);
    }

    public function testPostWijzigUsername() {
        $this->runfixture('user-harm');
        $this->runfixture('user-kobus');
        # GIVEN
        include "connect_db.php";
        $this->db = $db;
        $passw = 'harpje';
        $this->simulatePostRequest('/Wachtwoord.php', [
            'knpChange' => 1,
            'txtUser' => 'krelis',
            'txtUserOld' => 'kobus',
            'txtOld' => 'harpje',
            'txtNew' => 'groente',
            'txtBevest' => 'groente',
        ]);
        $lid = 42; // aha, wij zijn een onderdeel van Gebruiker.
        # WHEN
        ob_start();
        include "passw.php";
        $res = ob_get_clean();
        # THEN
        $this->assertFalse(isset($fout), $res);
        $this->assertEquals('De inloggegevens zijn gewijzigd', $goed);
        $this->assertTableWithPK('tblLeden', 'lidId', 42, ['login' => 'krelis']);
    }

    public function testPostWijzigWachtwoord() {
        $this->runfixture('user-harm');
        $this->runfixture('user-kobus');
        # GIVEN
        include "connect_db.php";
        $this->db = $db;
        $passw = 'harpje';
        $this->simulatePostRequest('/Wachtwoord.php', [
            'knpChange' => 1,
            'txtUser' => 'kobus',
            'txtUserOld' => 'kobus',
            'txtOld' => 'harpje',
            'txtNew' => 'mmuismat',
            'txtBevest' => 'mmuismat',
        ]);
        $lid = 42; // aha, wij zijn een onderdeel van Gebruiker.
        # WHEN
        ob_start();
        include "passw.php";
        $res = ob_get_clean();
        # THEN
        $this->assertFalse(isset($fout), $res);
        $this->assertEquals('De inloggegevens zijn gewijzigd.', $goed);
        // TODO: deugdelijke assert maken voor het wachtwoord. Zoiets als "old password 'groente' (uit de fixture af te leiden) is niet meer geldig"
        $this->assertTableWithPK('tblLeden', 'lidId', 42, ['login' => 'kobus', 'passw' => 'e37fb031e454b9c9f4a4a46ebbc9ddb6']);
    }

}
