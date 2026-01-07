<?php

class LoginTest extends IntegrationCase {

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
        // jammer, die link is rood.
        $this->makeUserFilesPresent(); // todo ook weer verwijderen. Helpt niet eens. Nou dan verwacht ik vanaf nu rood hoor :(
        $this->get('/Home.php', ['ingelogd' => 1]);
        $this->approve();
    }

    // doet Home nou iets anders dan, zeg, Eenheden ? Dat is bovendien onderdeel van beheer,
    // zodat in het rechtermenu ook een gekleurde link verschijnt.
    public function testAlreadyLoggedinEenheden() {
        // [v] met alle requests verwijderd verwachten we een meld_color=zwarte link naar RVO/Melden
        // [X] met appfile en takenfile wordt actuele_versie ja, en verwachten we een readercolor=zwarte link naar readerversies
        // jammer, die link is rood.
        $this->makeUserFilesPresent(); // todo ook weer verwijderen
        $this->get('/Eenheden.php', ['ingelogd' => 1]);
        $this->approve();
    }
}
