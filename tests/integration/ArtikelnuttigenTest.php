<?php

class ArtikelnuttigenTest extends IntegrationCase {

    private const ARTID = 93;

    public function setup(): void {
        require_once "func_artikelnuttigen.php";
        parent::setup();
    }

    public function test_inlezen_pil() {
        // GIVEN
        $this->runfixture('pil-inkoop');
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
        $this->runfixture('pil-inkoop');
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
        $this->runfixture('pil-inkoop');
        $artid = self::ARTID;
        $this->expectException(Exception::class);
        $actual = volgende_inkoop_pil($artid);
    }

    public function test_volgende_inkoop_pil() {
        $this->runfixture('pil-inkoop');
        $this->runSQL("INSERT INTO tblInkoop(inkId, dmink, artId, inkat, enhuId, prijs)
            VALUES(2, '2012-01-01', " . self::ARTID . ", 1, 1, 1)");
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
        $this->runfixture('pil-inkoop');
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
        $this->runfixture('voer-inkoop');
        $this->expectNewRecordsInTables([
            'tblVoeding' => 1,
        ]);
        $artid = self::ARTID;
        $rest_toedat = 0;
        $toediendatum = '2020-01-01'; // moet geldig zijn. Relevantie wordt hier niet duidelijk
        $periode_id = 0;
        $readerid = 0;
        inlezen_voer($this->db, $artid, $rest_toedat, $toediendatum, $periode_id, $readerid);
        $this->assertTablesGrew();
    }

    public function test_inlezen_voer_meerDan() {
        $this->runfixture('voer-inkoop');
        $this->runSQL("INSERT INTO tblInkoop(inkId, dmink, artId, inkat, enhuId, prijs)
            VALUES(2, '2012-01-01', " . self::ARTID . ", 4, 1, 1)");
        $this->expectNewRecordsInTables([
            'tblVoeding' => 2,
        ]);
        $artid = self::ARTID;
        $rest_toedat = 2;
        $toediendatum = '2020-01-01';
        $periode_id = 0;
        $readerid = 0;
        inlezen_voer($this->db, $artid, $rest_toedat, $toediendatum, $periode_id, $readerid);
        $this->assertTablesGrew();
    }

    # TODO: scenarios voor:
    # volgende_inkoop_voer
    # zoek_voorraad_oudste_inkoop_voer

}
