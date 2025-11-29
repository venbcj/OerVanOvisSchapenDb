<?php

class ArtikelnuttigenTest extends IntegrationCase {

    private const ARTID = 93;

    public function setup(): void {
        require_once "func_artikelnuttigen.php";
        $this->uses_db();
    }

    public function test_inlezen_pil() {
        // GIVEN
        $this->setArtikelPilFixture();
        $this->expectNewRecordsInTables([
            'tblNuttig' => 1,
        ]);
        // WHEN
        $hisid = 0;
        $artid = self::ARTID;
        $rest_toedat = 0;
        $toediendatum = '';
        $reduid = 0;
        inlezen_pil($this->db, $hisid, $artid, $rest_toedat, $toediendatum, $reduid);
        // THEN
        $this->assertTablesGrew();
    }

    public function test_inlezen_pil_meerDan() {
        // GIVEN
        $this->setArtikelPilFixture();
        // als je onvoldoende inkoopt, klapt de test eruit. 3 is genoeg. Nee 4. Waarom?
        $this->runSQL("INSERT INTO tblInkoop(inkId, dmink, artId, inkat, enhuId, prijs)
            VALUES(2, '2012-01-01', " . self::ARTID . ", 4, 1, 1)");
        $this->expectNewRecordsInTables([
            'tblNuttig' => 2,
        ]);
        // WHEN
        $hisid = 0;
        $artid = self::ARTID;
        $rest_toedat = 1; // dat is "meer dan" 0 in de basis-test
        $toediendatum = '';
        $reduid = 0;
        inlezen_pil($this->db, $hisid, $artid, $rest_toedat, $toediendatum, $reduid);
        // THEN
        $this->assertTablesGrew();
    }

    public function test_volgende_inkoop_pil_throws_exception_when_insufficient_voorraad() {
        $this->setArtikelPilFixture();
        $artid = self::ARTID;
        $this->expectException(Exception::class);
        $actual = volgende_inkoop_pil($artid);
    }

    public function test_volgende_inkoop_pil() {
        $this->setArtikelPilFixture();
        $this->runSQL("INSERT INTO tblInkoop(inkId, dmink, artId, inkat, enhuId, prijs) VALUES(2, '2012-01-01', " . self::ARTID . ", 1, 1, 1)");
        $artid = self::ARTID;
        $actual = volgende_inkoop_pil($artid);
        $expected = [2, 1, 4];
        $this->assertEquals($expected, $actual);
    }

    public function test_zoek_voorraad_oudste_inkoop_pil() {
        // GIVEN
        // zet de database goed
        // Voor het eerste scenario vind ik het belangrijk dat we niet verder afdalen
        //   in volgende_inkoop_pil.
        $this->setArtikelPilFixture();
        // WHEN
        // voer de methode uit
        $artid = self::ARTID;
        $actual = zoek_voorraad_oudste_inkoop_pil($artid);
        // THEN
        // controleer dat de juiste waarde terugkomt
        $expected = [1, 1, 4];
        $this->assertEquals($expected, $actual);
    }

    public function test_inlezen_voer() {
        $this->setArtikelVoerFixture();
        $this->expectNewRecordsInTables([
            'tblVoeding' => 1,
        ]);
        $artid = self::ARTID;
        $rest_toedat = 0;
        $toediendatum = '';
        $periode_id = 0;
        $readerid = 0;
        inlezen_voer($this->db, $artid, $rest_toedat, $toediendatum, $periode_id, $readerid);
        $this->assertTablesGrew();
    }

    public function test_inlezen_voer_meerDan() {
        $this->setArtikelVoerFixture();
        $this->runSQL("INSERT INTO tblInkoop(inkId, dmink, artId, inkat, enhuId, prijs)
            VALUES(2, '2012-01-01', " . self::ARTID . ", 4, 1, 1)");
        $this->expectNewRecordsInTables([
            'tblVoeding' => 2,
        ]);
        $artid = self::ARTID;
        $rest_toedat = 2;
        $toediendatum = '';
        $periode_id = 0;
        $readerid = 0;
        inlezen_voer($this->db, $artid, $rest_toedat, $toediendatum, $periode_id, $readerid);
        $this->assertTablesGrew();
    }

    # TODO: scenarios voor:
    # volgende_inkoop_voer
    # zoek_voorraad_oudste_inkoop_voer

    private function setArtikelPilFixture() {
        $this->runSQL("TRUNCATE tblInkoop");
        $this->runSQL("TRUNCATE tblNuttig");
        $this->runSQL("TRUNCATE tblArtikel");
        $this->runSQL("INSERT INTO tblArtikel(artId, naam, stdat, soort) VALUES(" . self::ARTID . ", 'test', 4, 0)");
        $this->runSQL("INSERT INTO tblInkoop(inkId, artId, inkat, enhuId, prijs) VALUES(1, " . self::ARTID . ", 2, 1, 1)");
        $this->runSQL("INSERT INTO tblNuttig(inkId, nutat, stdat) VALUES(1, 1, 1)");
    }

    private function setArtikelVoerFixture() {
        $this->runSQL("TRUNCATE tblArtikel");
        $this->runSQL("TRUNCATE tblInkoop");
        $this->runSQL("TRUNCATE tblVoeding");
        $this->runSQL("INSERT INTO tblArtikel(artId, naam, stdat, soort) VALUES(" . self::ARTID . ", 'test', 4, 0)");
        $this->runSQL("INSERT INTO tblInkoop(inkId, artId, inkat, enhuId, prijs) VALUES(1, " . self::ARTID . ", 2, 1, 1)");
        $this->runSQL("INSERT INTO tblVoeding(inkId, nutat, stdat) VALUES(1, 1, 1)");
    }

}
