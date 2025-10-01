<?php

class WachtwoordTest extends IntegrationCase {

    private function login() {
        $this->uses_db();
        $_POST['txtUser'] = 'kobus';
        $_POST['txtPassw'] = 'harpje';
        Auth::login([
            'lidId' => 42,
            'alias' => 'kobus',
        ]);
    }

    public function testValidateMissingPassword() {
        $this->post('/Wachtwoord.php', [
            'ingelogd' => 42,
            'knpChange' => 1,
            'txtUser' => 'gewijzigd',
            'txtUserOld' => 'kobus',
            'txtOld' => '',
            'txtNew' => '',
        ]);
        $this->assertFout('Gebruikersnaam of wachtwoord is onbekend.');
    }

    public function testValidateWachtwoordVerschillend() {
        $this->post('/Wachtwoord.php', [
            'ingelogd' => 42,
            'knpChange' => 1,
            'txtUser' => 'kobus',
            'txtUserOld' => 'harm',
            'txtOld' => 'fruit',
            'txtNew' => 'groente',
            'txtBevest' => 'groente-verschil',
        ]);
        $this->assertFout('Het nieuwe wachtwoord komt niet overeen met de bevestiging.');
    }

    public function testValidateWachtwoordFout() {
        $this->post('/Wachtwoord.php', [
            'ingelogd' => 42,
            'knpChange' => 1,
            'txtUser' => 'kobus',
            'txtUserOld' => 'harm',
            'txtOld' => 'fruit',
            'txtNew' => 'groente',
            'txtBevest' => 'groente',
        ]);
        $this->assertFout('Het oude wachtwoord is onjuist.');
    }

    public function testValidateWachtwoordTekort() {
        $this->login();
        $this->post('/Wachtwoord.php', [
            'ingelogd' => 42,
            'knpChange' => 1,
            'txtUser' => 'kobus',
            'txtUserOld' => 'harm',
            'txtOld' => 'harpje',
            'txtNew' => 'groen',
            'txtBevest' => 'groen',
        ]);
        $this->assertFout('Het wachtwoord moet uit minstens 6 karakters bestaan.');
    }

    public function testPostBestaatAl() {
        $this->login();
        $this->runsetup('tblLeden');
        $this->runfixture('user-kobus');
        $this->post('/Wachtwoord.php', [
            'ingelogd' => 42,
            'knpChange' => 1,
            'txtUser' => 'kobus',
            'txtUserOld' => 'harm',
            'txtOld' => 'harpje',
            'txtNew' => 'groente',
            'txtBevest' => 'groente',
        ]);
        $this->assertFout('Deze combinatie tussen gebruikersnaam en wachtwoord bestaat al. Kies een andere combinatie.');
    }

    public function testPostWijzigUsername() {
        $this->login();
        $this->runsetup('tblLeden');
        $this->runfixture('user-kobus');
        $this->post('/Wachtwoord.php', [
            'ingelogd' => 42,
            'knpChange' => 1,
            'txtUser' => 'krelis',
            'txtUserOld' => 'kobus',
            'txtOld' => 'harpje',
            'txtNew' => 'groente',
            'txtBevest' => 'groente',
        ]);
        $this->assertFout('De inloggegevens zijn gewijzigd');
        $this->assertTableWithPK('tblLeden', 'lidId', 42, ['login' => 'krelis']);
        // cleanup
        $this->runsetup('tblLeden');
    }

    public function testPostWijzigWachtwoord() {
        $this->login();
        $this->runsetup('tblLeden');
        $this->runfixture('user-kobus');
        $this->post('/Wachtwoord.php', [
            'knpChange' => 1,
            'txtUser' => 'kobus',
            'txtUserOld' => 'kobus',
            'txtOld' => 'harpje',
            'txtNew' => 'mmuismat',
            'txtBevest' => 'mmuismat',
        ]);
        $this->assertFout('De inloggegevens zijn gewijzigd.');
        // TODO: #0004115 deugdelijke assert maken voor het wachtwoord. Zoiets als "old password 'groente' (uit de fixture af te leiden) is niet meer geldig"
        $this->assertTableWithPK('tblLeden', 'lidId', 42, ['login' => 'kobus', 'passw' => 'e37fb031e454b9c9f4a4a46ebbc9ddb6']);
        // cleanup
        $this->runsetup('tblLeden');
    }

    // TODO: Is een leeg wachtwoord echt toegestaan?
    public function testPostWisWachtwoord() {
        $this->login();
        $this->runsetup('tblLeden');
        $this->runfixture('user-kobus');
        $this->post('/Wachtwoord.php', [
            'knpChange' => 1,
            'txtUser' => 'kobus',
            'txtUserOld' => 'kobus',
            'txtOld' => 'harpje',
            'txtNew' => '',
            'txtBevest' => '',
        ]);
        $this->assertFout('De inloggegevens zijn gewijzigd.');
        // TODO: #0004115 deugdelijke assert maken voor het wachtwoord. Zoiets als "old password 'groente' (uit de fixture af te leiden) is niet meer geldig"
        $this->assertTableWithPK('tblLeden', 'lidId', 42, ['login' => 'kobus', 'passw' => '6edffa2b54fe663ac77c316115a0e44a']);
        // cleanup
        $this->runsetup('tblLeden');
    }

}
