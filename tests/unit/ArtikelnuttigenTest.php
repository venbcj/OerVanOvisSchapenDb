<?php

class ArtikelnuttigenTest extends UnitCase {

    public function setup(): void {
        require_once "func_artikelnuttigen.php";
        $this->uses_db();
    }

    public function test_inlezen_pil() {
        # $this->markTestIncomplete('onderdelen moeten eerst goed werken');
        // GIVEN
        // zet de database goed
        $this->setArtikelFixture();
        // WHEN
        // voer de methode uit met de 6 parameters... er zijn heel wat scenario's denkbaar
        $hisid = 0;
        $artid = 93;
        $rest_toedat = '';
        $toediendatum = '';
        $reduid = 0;
        inlezen_pil($this->db, $hisid, $artid, $rest_toedat, $toediendatum, $reduid);
        // THEN
        // controleer dat de juiste dingen in de database zijn gewijzigd. Er is geen functie-output.
    }

    # inlezen_voer
    # volgende_inkoop_pil
    # volgende_inkoop_voer

    public function test_zoek_voorraad_oudste_inkoop_pil() {
        // GIVEN
        // zet de database goed
        // Voor het eerste scenario vind ik het belangrijk dat we niet verder afdalen
        //   in volgende_inkoop_pil.
        $this->setArtikelFixture();
        // WHEN
        // voer de methode uit
        $artid = 93;
        $actual = zoek_voorraad_oudste_inkoop_pil($this->db, $artid);
        // THEN
        // controleer dat de juiste waarde terugkomt
        $expected = [1, 1, 4];
        $this->assertEquals($expected, $actual);
    }

    # zoek_voorraad_oudste_inkoop_voer

    private function setArtikelFixture() {
        $this->runSQL("TRUNCATE tblArtikel");
        $this->runSQL("INSERT INTO tblArtikel(artId, stdat, soort, naam) VALUES(93, 4, 0, 'test')");
        $this->runSQL("TRUNCATE tblInkoop");
        $this->runSQL("INSERT INTO tblInkoop(artId, inkId, inkat, enhuId, prijs) VALUES(93, 1, 2, 1, 1)");
        $this->runSQL("TRUNCATE tblNuttig");
        $this->runSQL("INSERT INTO tblNuttig(inkId, nutat, stdat) VALUES(1, 1, 1)");
    }
}
