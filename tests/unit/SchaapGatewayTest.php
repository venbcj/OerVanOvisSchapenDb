<?php

class SchaapGatewayTest extends GatewayCase {

    use Expectations;

    private const SCHAAP4_ID = 4;
    private const SCHAAP4_LEVENSNUMMER = '4';
    private const STALID = 1;
    private const NEW_GESLACHT = 'ooi';
    private const NEW_LEVENSNUMMER = '9990303';
    private const NEWSCHAAPID = 7;
    private const NEWSCHAAPID2 = 8;
    private const VOLWID = 1;

    protected static $sutname = 'SchaapGateway';

    // deze tests zijn inhoudelijk nog zwak. Er kan van alles varieren.

    public function testMedAantalFase() {
        $this->runfixture('schaap-2-lam');
        $lidid = 1;
        $M = 1; // "maandnummer" ?
        $J = 1970; // "jaar" ?
        $V = 1; // iets uit "kzlPil"
        # todo: deze twee inpakken. Veel queries hebben drie verschijningsvormen
        $Sekse = 's.geslacht is not null'; // ah dit gaat over lammeren?
        $Ouder = 'isnull(oudr.hisId)';
        $actual = $this->sut->med_aantal_fase($lidid, $M, $J, $V, $Sekse, $Ouder);
        $this->assertEquals(2, $actual);
    }

    public function testVoerFase() {
        // med-aantal-fase en voer-fase verlangen vergelijkbare datasets,
        // alleen in med* is historie.actId=8 gewenst,
        // in voer* waarden in nuttig.nutat en .stdat
        // -> rechtvaardigt dat twee fixtures? 't Is niet echt shared.
        $this->runfixture('schaap-2-lam');
        $lidid = 1;
        $M = 1; // "maandnummer" ?
        $J = 1970; // "jaar" ?
        $V = 1; // iets uit "kzlPil"
        # todo: deze twee inpakken. Veel queries hebben drie verschijningsvormen
        $Sekse = 's.geslacht is not null'; // ah dit gaat over lammeren?
        $Ouder = 'isnull(oudr.hisId)';
        $actual = $this->sut->voer_fase($lidid, $M, $J, $V, $Sekse, $Ouder);
        $this->assertEquals(2, $actual);
    }

    public function testGeenEenheidFase() {
        $lidid = 1;
        $M = 1; // "maandnummer" ?
        $J = 1970; // "jaar" ?
        $V = 1; // iets uit "kzlPil"
        # todo: deze twee inpakken. Veel queries hebben drie verschijningsvormen
        $Sekse = 's.geslacht is not null';
        $Ouder = 'isnull(oudr.hisId)';
        $this->assertEquals(false, $this->sut->eenheid_fase($lidid, $M, $J, $V, $Sekse, $Ouder));
    }

    public function testEenheidFase() {
        $this->runfixture('eenheid-fase');
        $lidid = 1;
        $M = 1; // "maandnummer" ?
        $J = 1970; // "jaar" ?
        $V = 1; // iets uit "kzlPil"
        # todo: deze twee inpakken. Veel queries hebben drie verschijningsvormen
        $Sekse = 's.geslacht is not null';
        $Ouder = 'isnull(oudr.hisId)';
        $this->assertEquals('kg', $this->sut->eenheid_fase($lidid, $M, $J, $V, $Sekse, $Ouder));
    }

    public function testZoekStapel() {
        $this->runfixture('schaap-4');
        $this->assertEquals(1, $this->sut->zoekStapel(self::LIDID));
    }

    public function testAfleverdatum() {
        $this->runfixture('schaap-afleverdatum');
        $res = $this->sut->afleverdatum(self::LIDID);
        $this->assertEquals(1, $res->num_rows);
    }

    public function testZoekSchaap_leeg() {
        $this->uses_db();
        $postdata = [
            'kzlLevnr_' => '1',
            'kzlWerknr_' => '',
            'kzlHalsnr_' => '',
            'kzlOoi_' => '',
            'kzlRam_' => '',
        ];
        $where = $this->sut->getZoekWhere($postdata);
        $this->assertEquals(null, $this->sut->zoekSchaap($where));
    }

    public function testZoekSchaap() {
        $this->runfixture('schaap-met-ouders');
        $postdata = [
            'kzlLevnr_' => '1',
            'kzlWerknr_' => '',
            'kzlHalsnr_' => '',
            'kzlOoi_' => '',
            'kzlRam_' => '',
        ];
        $where = $this->sut->getZoekWhere($postdata);
        $this->assertEquals($expected_schaapid = 1, $this->sut->zoekSchaap($where));
    }

    public function testZoekresultaat() {
        $this->runfixture('schaap-met-ouders');
        $postdata = [
            'kzlLevnr_' => '1',
            'kzlWerknr_' => '',
            'kzlHalsnr_' => '',
            'kzlOoi_' => '',
            'kzlRam_' => '',
        ];
        $where = $this->sut->getZoekWhere($postdata);
        $Karwerk = 5;
        $vw = $this->sut->zoekresultaat(self::LIDID, $where, $Karwerk);
        $this->assertEquals(1, $vw->num_rows);
        // "hoezo is dit SOMS 2?" als je alle unit-tests draait.
    }

    public function testZoekSchaapid() {
        $this->runfixture('schaap-4');
        $this->assertEquals(self::SCHAAP4_ID, $this->sut->zoek_schaapid(self::SCHAAP4_LEVENSNUMMER));
    }

    public function testZoekStalid() {
        $this->runfixture('schaap-4');
        // moet ook een actie 3 in de historie hebben; die is er niet, dus vinden we niks.
        $this->assertEquals('', $this->db->error);
        $this->assertEquals(null, $this->sut->zoek_stalid(self::LIDID));
    }

    public function testVindStalid() {
        $this->runfixture('schaap-4');
        // moet ook een actie 3 in de historie hebben
        $this->runSQL("INSERT INTO tblHistorie(stalId, actId) VALUES(1, 3)");
        $this->assertEquals(self::STALID, $this->sut->zoek_stalid(self::LIDID));
    }

    public function testZoekVaders() {
        $Karwerk = 5;
        # er is een preconditie waar de methode [0,0] geeft. Soms fout, dus
        $this->assertEquals([], $this->sut->zoek_vaders(self::LIDID, $Karwerk));
    }

    public function testVindVaders() {
        $this->runfixture('schaap-4');
        $this->runSQL("INSERT INTO tblHistorie(stalId, actId) VALUES(1, 3)");
        $Karwerk = 5;
        $expected = [['stalId' => 1, 'werknr' => '4', 'halsnr' => null]];
        $this->assertEquals($expected, $this->sut->zoek_vaders(self::LIDID, $Karwerk));
    }

    public function testLevnrUnique() {
        $this->runfixture('schaap-4');
        $this->assertEquals(false, $this->sut->levnr_exists_outside(self::SCHAAP4_LEVENSNUMMER, self::SCHAAP4_ID));
    }

    public function testLevnrNotUnique() {
        $this->runfixture('schaap-4');
        $this->runSQL("INSERT INTO tblSchaap(levensnummer) VALUES('" . self::SCHAAP4_LEVENSNUMMER . "')");
        $this->assertEquals(true, $this->sut->levnr_exists_outside(self::SCHAAP4_LEVENSNUMMER, self::SCHAAP4_ID));
    }

    public function testChangeLevensnummer() {
        $this->runfixture('schaap-4');
        $this->sut->changeLevensnummer(self::SCHAAP4_LEVENSNUMMER, self::NEW_LEVENSNUMMER);
        $this->assertTableWithPK('tblSchaap', 'schaapId', self::SCHAAP4_ID, ['levensnummer' => self::NEW_LEVENSNUMMER]);
    }

    public function testUpdateGeslacht() {
        $this->runfixture('schaap-4');
        $this->sut->updateGeslacht(self::SCHAAP4_LEVENSNUMMER, self::NEW_GESLACHT);
        $this->assertTableWithPK('tblSchaap', 'schaapId', self::SCHAAP4_ID, ['geslacht' => self::NEW_GESLACHT]);
    }

    public function testGeenLamOpStal() {
        $this->assertEquals(0, $this->sut->aantalLamOpStal(self::LIDID));
    }

    public function testAantalLamOpStal() {
        $this->runfixture('schaap-4');
        $this->assertEquals(1, $this->sut->aantalLamOpStal(self::LIDID));
    }

    public function testSchaapMetActie3IsGeenLam() {
        $this->runfixture('schaap-4');
        $this->runSQL("INSERT INTO tblHistorie(stalId, actId) VALUES(1, 3)");
        $this->assertEquals(0, $this->sut->aantalLamOpStal(self::LIDID));
    }

    public function testAantalRamOpStal() {
        $this->runfixture('schaap-4');
        // er moet een actie 3 in de historie zitten
        $this->runSQL("INSERT INTO tblHistorie(stalId, actId) VALUES(1, 3)");
        $this->assertEquals(1, $this->sut->aantalRamOpStal(self::LIDID));
    }

    public function testAantalOoiOpStal() {
        $this->runfixture('schaap-4');
        $this->runSQL("UPDATE tblSchaap SET geslacht='ooi'");
        // er moet een actie 3 in de historie zitten
        $this->runSQL("INSERT INTO tblHistorie(stalId, actId) VALUES(1, 3)");
        $this->assertEquals(1, $this->sut->aantalOoiOpStal(self::LIDID));
    }

    public function testGeenLamUitschaar() {
        $this->runfixture('schaap-4');
        $this->assertEquals(0, $this->sut->aantalLamUitschaar(self::LIDID));
    }

    public function testLamUitschaar() {
        $this->runfixture('schaap-4');
        $this->runSQL("INSERT INTO tblHistorie(stalId, actId) VALUES(1, 10)");
        $this->assertEquals(1, $this->sut->aantalLamUitschaar(self::LIDID));
        // TODO cases voor ooi, lam.
    }

    public function testZonderActie10GeenUitgeschaarden() {
        $this->runfixture('schaap-4');
        $this->assertEquals(0, $this->sut->countUitgeschaarden(self::LIDID));
    }

    public function testMetActie10WelUitgeschaarden() {
        $this->runfixture('schaap-4');
        $this->runSQL("INSERT INTO tblHistorie(stalId, actId) VALUES(1, 10)");
        $this->runSQL("INSERT INTO tblRelatie(relId, relatie, partId) values(1, 'bestemming', 1)");
        $this->runSQL("INSERT INTO tblPartij(partId, lidId, naam) values(1, 1, 'partij')");
        $this->runSQL("UPDATE tblStal SET rel_best=1 WHERE stalId=1");
        $this->assertEquals(1, $this->sut->countUitgeschaarden(self::LIDID));
    }

    public function testZoekUitgeschaarden() {
        $this->runfixture('schaap-4');
        $Karwerk = 5;
        $res = $this->sut->zoekUitgeschaarden(self::LIDID, $Karwerk);
        $this->assertEquals(0, $res->num_rows);
    }

    public function testGeenAanwezigen() {
        $Karwerk = 5;
        $this->assertEquals(0, $this->sut->aanwezigen(self::LIDID, $Karwerk)->num_rows);
    }

    public function testAanwezigen() {
        $Karwerk = 5;
        $this->runfixture('schaap-4');
        $this->runSQL("UPDATE tblStal SET ubnId=1");
        $this->runSQL("INSERT INTO tblHistorie(stalId, actId, datum) VALUES(1, 1, '2021-04-04')"); // hg
        $this->runSQL("INSERT INTO tblHistorie(stalId, actId, datum) VALUES(1, 3, '2019-04-04')"); // prnt
        $this->runSQL("INSERT INTO tblHistorie(stalId, actId, datum) VALUES(1, 22, '2019-04-04')"); // scan
        # waarom dit dan niet?
        #$this->runSQL("INSERT INTO tblHistorie(stalId, actId, datum) VALUES(1, 10, '2019-04-04')"); // haf (actid maakt niet uit, zolang af=1)
        $this->assertEquals(1, $this->sut->aanwezigen(self::LIDID, $Karwerk)->num_rows);
    }

    public function testGeenPeriode() {
        $volwid = 0;
        $this->assertEquals('', $this->sut->periode($volwid)[1]);
    }

    public function testPeriode() {
        // in productie volgt volwid uit aantal_meerlingen_perOoi
        $volwid = 3;
        $this->runfixture('schaap-4');
        $this->runSQL("UPDATE tblSchaap SET volwId=3 WHERE schaapId=4");
        $this->runSQL("INSERT INTO tblHistorie(stalId, actId, datum) VALUES(1, 1, '2021-11-07')");
        $this->assertEquals('2021', $this->sut->periode($volwid)[1]);
        $this->assertEquals('11', $this->sut->periode($volwid)[2]);
    }

    public function testGeenMeerlingenZonderLog() {
        $ooiId = 1;
        $aantal = 1;
        $this->assertEquals(0, $this->sut->aantal_meerlingen_perOoi(self::LIDID, $ooiId, $aantal)->num_rows);
    }

    public function testMeerlingen() {
        // richt de database zo in, dat een ooi met een tweeling bestaat. (kon ook drieling, vierling, enz zijn)
        $this->runfixture('schaap-4');
        $ooiId = 4;
        $aantal = 1;
        $this->runSQL("INSERT INTO tblVolwas(mdrId, volwId) VALUES(" . $ooiId . ", " . self::VOLWID . ")");
        $this->runSQL("INSERT INTO tblSchaap(schaapId, volwId) VALUES(" . self::NEWSCHAAPID . ", " . self::VOLWID . ")"); // lam
        $this->runSQL("INSERT INTO tblSchaap(schaapId, volwId) VALUES(" . self::NEWSCHAAPID2 . ", " . self::VOLWID . ")"); // lam
        $this->runSQL("INSERT INTO tblStal(stalId, ubnId, schaapId) VALUES(2, 1, " . self::NEWSCHAAPID . ")");
        $this->runSQL("INSERT INTO tblStal(stalId, ubnId, schaapId) VALUES(3, 1, " . self::NEWSCHAAPID2 . ")");
        $this->runSQL("INSERT INTO tblHistorie(stalId, actId, datum) VALUES(2, 1, '2021-11-07')");
        $res = $this->sut->aantal_meerlingen_perOoi(self::LIDID, $ooiId, $aantal);
        $this->assertEquals(1, $res->num_rows, 'het aantal rijen klopt niet');
        // deze '1' is het id in tblVolw. Wat zijn we nu aan het doen?
        $this->assertEquals(1, $res->fetch_assoc()['volwId'], 'het id klopt niet');
    }

    public function testDeLammerenLeeg() {
        $volwid = 1;
        $Karwerk = 5;
        $this->assertEquals([null, null], $this->sut->de_lammeren($volwid, $Karwerk));
    }

    public function testDeLammeren() {
        $this->runfixture('schaap-4');
        $this->runSQL("INSERT INTO tblHistorie(stalId, actId) VALUES(1, 1)");
        $this->runSQL("UPDATE tblSchaap SET volwId=1");
        $volwid = 1;
        $Karwerk = 5;
        $this->assertEquals(['ram', self::SCHAAP4_LEVENSNUMMER], $this->sut->de_lammeren($volwid, $Karwerk));
    }

    public function testGeenMeerlingenPerooiPerjaar() {
        $this->assertEquals([null, null], $this->sut->meerlingen_perOoi_perJaar(self::LIDID, self::SCHAAP4_ID, 2020, 9));
    }

    public function testWelMeerlingenPerooiPerjaar() {
        $this->runfixture('schaap-4');
        $this->runSQL("UPDATE tblSchaap SET geslacht='ooi'");
        $this->runSQL("INSERT INTO tblVolwas(mdrId, volwId) VALUES(4, 1)");
        $this->runSQL("INSERT INTO tblSchaap(schaapId, volwId) VALUES(" . self::NEWSCHAAPID . ", " . self::VOLWID . ")"); // lam
        $this->runSQL("INSERT INTO tblSchaap(schaapId, volwId) VALUES(" . self::NEWSCHAAPID2 . ", " . self::VOLWID . ")"); // lam
        $this->runSQL("INSERT INTO tblStal(stalId, ubnId, schaapId) VALUES(2, 1, " . self::NEWSCHAAPID . ")");
        $this->runSQL("INSERT INTO tblStal(stalId, ubnId, schaapId) VALUES(3, 1, " . self::NEWSCHAAPID2 . ")");
        $this->runSQL("INSERT INTO tblHistorie(stalId, actId, datum) VALUES(2, 1, '2020-09-07')");
        $this->runSQL("INSERT INTO tblHistorie(stalId, actId, datum) VALUES(3, 1, '2020-09-07')");
        $res = $this->sut->meerlingen_perOoi_perJaar(self::LIDID, self::SCHAAP4_ID, '2020', '09');
        $expected_lam_count = 2;
        $this->assertEquals([$expected_lam_count, self::VOLWID], $res);
    }

    public function testGeenAantalPerGeslacht() {
        $volwid = 1;
        $geslacht = 'ram';
        $jaar = '2020';
        $maand = '09';
        $this->assertEquals(0, $this->sut->aantal_perGeslacht($volwid, $geslacht, $jaar, $maand));
    }

    public function testAantalPerGeslacht() {
        $this->runfixture('schaap-4');
        $this->runSQL("UPDATE tblSchaap SET volwId=1");
        $this->runSQL("INSERT INTO tblHistorie(stalId, actId) VALUES(1, 1)");
        $volwid = 1;
        $geslacht = 'ram';
        $jaar = '2020';
        $maand = '09';
        $this->assertEquals(0, $this->sut->aantal_perGeslacht($volwid, $geslacht, $jaar, $maand));
    }

    public function testGeenOoienInJaar() {
        $this->assertEquals(0, $this->sut->zoek_ooien_in_jaar(self::LIDID, 2020));
    }

    // TODO: @WEAK
    public function testZoekOoienInJaar() {
        $this->runfixture('schaap-4');
        $this->runSQL("UPDATE tblSchaap SET geslacht='ooi'");
        $this->runSQL("INSERT INTO tblHistorie(stalId, actId, datum) VALUES(1, 3, '2020-01-01')");
        $this->assertEquals(1, $this->sut->zoek_ooien_in_jaar(self::LIDID, 2020));
    }

    public function testGeenLammerenInJaar() {
        $this->assertEquals(0, $this->sut->zoek_lammeren_in_jaar(self::LIDID, 2020, '2020-01-01'));
    }

    // TODO: @WEAK
    // mist een case die iets doet met parameter $jan1
    // maakt de functie van de joins niet duidelijk
    public function testZoekLammerenInJaarGeenOuder() {
        $this->runfixture('schaap-4');
        $this->runSQL("INSERT INTO tblHistorie(stalId, actId, datum) VALUES(1, 1, '2020-01-01')");
        # $this->runSQL("INSERT INTO tblHistorie(stalId, actId, datum) VALUES(1, 1, '2020-01-01')");
        $this->assertEquals(1, $this->sut->zoek_lammeren_in_jaar(self::LIDID, 2020, '2020-01-01'));
    }

    public function testGeenSterfteLammeren() {
        $this->assertEquals(0, $this->sut->zoek_aantal_sterfte_lammeren_in_jaar(self::LIDID, 2020));
    }

    public function testZoekSterfteLammeren() {
        $this->runfixture('schaap-4');
        $this->runSQL("INSERT INTO tblHistorie(stalId, actId, datum) VALUES(1, 14, '2020-01-01')");
        $this->assertEquals(1, $this->sut->zoek_aantal_sterfte_lammeren_in_jaar(self::LIDID, 2020));
    }

    public function testGeenSterfteMoeders() {
        $this->assertEquals(0, $this->sut->zoek_aantal_sterfte_moeder_in_jaar(self::LIDID, 2020));
    }

    public function testZoekSterfteMoeders() {
        $this->runfixture('schaap-4');
        $this->runSQL("UPDATE tblSchaap SET geslacht='ooi'");
        $this->runSQL("INSERT INTO tblHistorie(stalId, actId, datum) VALUES(1, 3, '2020-01-01')");
        $this->runSQL("INSERT INTO tblHistorie(stalId, actId, datum) VALUES(1, 14, '2020-01-01')");
        $this->assertEquals(1, $this->sut->zoek_aantal_sterfte_moeder_in_jaar(self::LIDID, 2020));
    }

    // Patroon: Confirm Stub Role
    // Deze test geeft de database-toestand om de Expectation "zoek_schaapgegevens" te laten uitkomen.
    //   de expectation doet dienst in de integratietest MedRegistratiePage.
    //
    // TODO meer scenarios, tenminste voor elke union eentje lijkt mij
    public function test_zoek_schaapgegevens() {
        // GIVEN
        $this->runfixture('schaap-4');
        $this->uses_db();
        $this->runSQL("INSERT INTO tblHistorie(stalId, actId) VALUES(1, 1)");
        // WHEN
        $expectation = 'zoek_schaapgegevens';
        $lidId = self::LIDID;
        $Karwerk = 5;
        $afvoer = 0;
        $filter = 'true';
        $result = $this->sut->zoek_schaapgegevens($lidId, $Karwerk, $afvoer, $filter);
        // THEN
        $this->assertStubBehaves($expectation, $result);
    }

    // zoek_medicatie_lijst en zoek_medicatielijst_werknummer gebruiken dezelfde bron
    public function test_zoek_medicatie_lijst() {
        // GIVEN
        $this->runfixture('stubproof-medicatielijst');
        // WHEN
        $result = $this->sut->zoek_medicatie_lijst(self::LIDID, $afvoer = 1); // dit "dekt" de if in db_filter_afvoerdatum, maar dat deugt natuurlijk voor geen meter
        // THEN
        $this->assertStubBehaves('zoek_medicatie_lijst', $result);
    }

    // zoek_medicatie_lijst en zoek_medicatielijst_werknummer gebruiken dezelfde bron
    public function test_zoek_medicatielijst_werknummer() {
        // GIVEN
        $this->runfixture('stubproof-medicatielijst');
        // WHEN
        $result = $this->sut->zoek_medicatielijst_werknummer(self::LIDID, $Karwerk = 5, $afvoer = 0);
        // THEN
        $this->assertStubBehaves('zoek_medicatielijst_werknummer', $result);
    }

    public function test_zoekGeschiedenis_leeg() {
        $this->uses_db();
        $result = $this->sut->zoekGeschiedenis(self::LIDID, self::SCHAAP4_ID, $Karwerk = 5);
        $this->assertEquals(0, $result->num_rows);
    }

    # public function test_zoekGeschiedenis() {
    #     $this->markTestIncomplete("Er zijn nogal wat scenarios nodig voor zoekGeschiedenis");
    # }

    // TODO kandidaat voor Pull Up Method
    protected function assertStubBehaves($expectation, $result) {
        $expected = $this->getExpected($expectation);
        $this->assertInstanceOf(mysqli_result::class, $result, "Query mislukt, kan niet kloppen");
        $this->assertEquals(count($expected), $result->num_rows, "Verwacht een specifiek aantal rijen");
        // NOTE misschien doet iets ergens ook wel fetch_row ipv fetch_assoc. Nou, dan faalt het vanzelf huh.
        $actual = $result->fetch_all($mode = MYSQLI_ASSOC);
        $this->assertEquals($expected, $actual);
    }
    
    public function test_zoek_schaap_aflever() {
        $this->uses_db();
        $this->runSQL("INSERT INTO tblPartij(naam, partId, lidId) VALUES('Stempelmans', 1, 1)");
        $this->runSQL("INSERT INTO tblRelatie(relId, partId, relatie) VALUES(1, 1, 'test')");
        $this->runSQL("INSERT INTO tblStal(stalId, rel_best, schaapId) VALUES(1, 1, 4)");
        $this->runSQL("INSERT INTO tblHistorie(hisId, stalId, actId, datum, kg) VALUES(1, 1, 12, '2010-01-01', 1)");
        $this->runSQL("INSERT INTO tblSchaap(schaapId, levensnummer) VALUES(4, '131072')");
        // WHEN
        $result = $this->sut->zoek_schaap_aflever($bestm = 1, $date = '2010-01-01', $Karwerk = 5);
        $this->assertStubBehaves('zoek_schaap_aflever', $result);
    }

    //***********************************

    public function test_zoek_schaapid() {
        $fldLevnr = null;
        $result = $this->sut->zoek_schaapid($fldLevnr);
        $this->assertNotFalse($result);
    }

    public function test_zoek_schaapid_transponder() {
        $levnr = null;
        $result = $this->sut->zoek_schaapid_transponder($levnr);
        $this->assertNotFalse($result);
    }

    public function test_zoek_stalid() {
        $result = $this->sut->zoek_stalid(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_staldetails() {
        $Karwerk = 5;
        $result = $this->sut->zoek_staldetails(self::LIDID, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_zoek_vaders() {
        $Karwerk = 5;
        $result = $this->sut->zoek_vaders(self::LIDID, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_zoek_werknummer() {
        $mdrId = null;
        $Karwerk = 5;
        $result = $this->sut->zoek_werknummer($mdrId, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_no_levnr_exists_outside_when_no_data() {
        $fldLevnr = null;
        $schaapId = null;
        $result = $this->sut->levnr_exists_outside($fldLevnr, $schaapId);
        $this->assertFalse($result);
    }

    public function test_changeLevensnummer() {
        $old = null;
        $new = null;
        $result = $this->sut->changeLevensnummer($old, $new);
        $this->assertNotFalse($result);
    }

    public function test_updateGeslacht() {
        $levensnummer = null;
        $geslacht = null;
        $result = $this->sut->updateGeslacht($levensnummer, $geslacht);
        $this->assertNotFalse($result);
    }

    public function test_updateTransponder() {
        $schaapId = null;
        $transponder = null;
        $result = $this->sut->updateTransponder($schaapId, $transponder);
        $this->assertNotFalse($result);
    }

    public function test_aantalLamOpStal() {
        $result = $this->sut->aantalLamOpStal(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_aantalOoiOpStal() {
        $result = $this->sut->aantalOoiOpStal(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_aantalRamOpStal() {
        $result = $this->sut->aantalRamOpStal(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_countByStal() {
        $result = $this->sut->countByStal(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_aantalLamUitschaar() {
        $result = $this->sut->aantalLamUitschaar(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_aantalOoiUitschaar() {
        $result = $this->sut->aantalOoiUitschaar(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_aantalRamUitschaar() {
        $result = $this->sut->aantalRamUitschaar(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoekStapel() {
        $result = $this->sut->zoekStapel(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_countUitgeschaarden() {
        $result = $this->sut->countUitgeschaarden(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoekUitgeschaarden() {
        $Karwerk = 5;
        $result = $this->sut->zoekUitgeschaarden(self::LIDID, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_aanwezigen() {
        $Karwerk = 5;
        $result = $this->sut->aanwezigen(self::LIDID, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_ooien_met_vijfling() {
        $ooiId = null;
        $result = $this->sut->ooien_met_vijfling(self::LIDID, $ooiId);
        $this->assertNotFalse($result);
    }

    public function test_aantal_meerlingen_perOoi() {
        $Ooiid = null;
        $Nr = null;
        $result = $this->sut->aantal_meerlingen_perOoi(self::LIDID, $Ooiid, $Nr);
        $this->assertNotFalse($result);
    }

    public function test_meerlingen_perOoi_perJaar() {
        $Ooiid = null;
        $Jaar = null;
        $Maand = null;
        $result = $this->sut->meerlingen_perOoi_perJaar(self::LIDID, $Ooiid, $Jaar, $Maand);
        $this->assertNotFalse($result);
    }

    public function test_periode() {
        $volwId = null;
        $result = $this->sut->periode($volwId);
        $this->assertNotFalse($result);
    }

    public function test_aantal_perGeslacht() {
        $Volwid = null;
        $Geslacht = null;
        $Jaar = null;
        $Maand = null;
        $result = $this->sut->aantal_perGeslacht($Volwid, $Geslacht, $Jaar, $Maand);
        $this->assertNotFalse($result);
    }

    public function test_afleverdatum() {
        $result = $this->sut->afleverdatum(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_ooien_in_jaar() {
        $jaar = null;
        $result = $this->sut->zoek_ooien_in_jaar(self::LIDID, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_zoek_lammeren_in_jaar() {
        $jaar = null;
        $jan1 = null;
        $result = $this->sut->zoek_lammeren_in_jaar(self::LIDID, $jaar, $jan1);
        $this->assertNotFalse($result);
    }

    public function test_zoek_aantal_sterfte_lammeren_in_jaar() {
        $jaar = null;
        $result = $this->sut->zoek_aantal_sterfte_lammeren_in_jaar(self::LIDID, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_zoek_aantal_sterfte_moeder_in_jaar() {
        $jaar = null;
        $result = $this->sut->zoek_aantal_sterfte_moeder_in_jaar(self::LIDID, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_zoek_worpen_in_jaar() {
        $jaar = null;
        $result = $this->sut->zoek_worpen_in_jaar(self::LIDID, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_eigen_schapen() {
        $result = $this->sut->eigen_schapen(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_werknummers() {
        $Karwerk = 5;
        $result = $this->sut->werknummers(self::LIDID, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_halsnummers() {
        $result = $this->sut->halsnummers(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_ooien() {
        $Karwerk = 5;
        $result = $this->sut->ooien(self::LIDID, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_rammen() {
        $Karwerk = 5;
        $result = $this->sut->rammen(self::LIDID, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_getZoekWhere() {
        $postdata = null;
        $result = $this->sut->getZoekWhere($postdata);
        $this->assertNotFalse($result);
    }

    public function test_zoekAankoop() {
        $where = 'true';
        $result = $this->sut->zoekAankoop(self::LIDID, $where);
        $this->assertNotFalse($result);
    }

    public function test_zoekSchaap() {
        $where = 'true';
        $result = $this->sut->zoekSchaap($where);
        $this->assertNotFalse($result);
    }

    public function test_zoekresultaat() {
        $where = 'true';
        $Karwerk = 5;
        $result = $this->sut->zoekresultaat(self::LIDID, $where, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_zoekGeschiedenis() {
        $schaapId = null;
        $Karwerk = 5;
        $result = $this->sut->zoekGeschiedenis(self::LIDID, $schaapId, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_zoek_laatste_werpdatum() {
        $max_worp = null;
        $result = $this->sut->zoek_laatste_werpdatum($max_worp);
        $this->assertNotFalse($result);
    }

    public function test_resultvader() {
        $Karwerk = 5;
        $result = $this->sut->resultvader(self::LIDID, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_ouders() {
        $schaapId = null;
        $result = $this->sut->ouders($schaapId);
        $this->assertNotFalse($result);
    }

    public function test_weeg() {
        $schaapId = null;
        $result = $this->sut->weeg(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_bestaand_levensnummer() {
        $schaapId = null;
        $result = $this->sut->zoek_bestaand_levensnummer($schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_op_levensnummer() {
        $levensnummer = null;
        $result = $this->sut->zoek_op_levensnummer($levensnummer);
        $this->assertNotFalse($result);
    }

    public function test_updateLevensnummer() {
        $schaapId = null;
        $levensnummer = null;
        $result = $this->sut->updateLevensnummer($schaapId, $levensnummer);
        $this->assertNotFalse($result);
    }

    public function test_zoek_fokkernr() {
        $schaapId = null;
        $result = $this->sut->zoek_fokkernr($schaapId);
        $this->assertNotFalse($result);
    }

    public function test_updateFokkernr() {
        $schaapId = null;
        $newfokrnr = null;
        $result = $this->sut->updateFokkernr($schaapId, $newfokrnr);
        $this->assertNotFalse($result);
    }

    public function test_update_geslacht() {
        $schaapId = null;
        $newsekse = null;
        $result = $this->sut->update_geslacht($schaapId, $newsekse);
        $this->assertNotFalse($result);
    }

    public function test_zoek_ras() {
        $schaapId = null;
        $result = $this->sut->zoek_ras($schaapId);
        $this->assertNotFalse($result);
    }

    public function test_update_ras() {
        $schaapId = null;
        $rasId = null;
        $result = $this->sut->update_ras($schaapId, $rasId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_moeder() {
        $schaapId = null;
        $result = $this->sut->zoek_moeder($schaapId);
        $this->assertNotFalse($result);
    }

    public function test_update_moeder() {
        $schaapId = null;
        $mdrId = null;
        $result = $this->sut->update_moeder($schaapId, $mdrId);
        $this->assertNotFalse($result);
    }

    public function test_update_vader() {
        $schaapId = null;
        $newvdrId = null;
        $result = $this->sut->update_vader($schaapId, $newvdrId);
        $this->assertNotFalse($result);
    }

    public function test_update_volw() {
        $schaapId = null;
        $volwId = null;
        $result = $this->sut->update_volw($schaapId, $volwId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_reden() {
        $schaapId = null;
        $result = $this->sut->zoek_reden($schaapId);
        $this->assertNotFalse($result);
    }

    public function test_update_reden() {
        $schaapId = null;
        $redId = null;
        $result = $this->sut->update_reden($schaapId, $redId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_geslacht() {
        $schaapId = null;
        $result = $this->sut->zoek_geslacht($schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_halsnr_db() {
        $kleur = null;
        $halsnr = null;
        $result = $this->sut->zoek_halsnr_db(self::LIDID, $kleur, $halsnr);
        $this->assertNotFalse($result);
    }

    public function test_zoek_schapen() {
        $result = $this->sut->zoek_schapen(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_moeders() {
        $Karwerk = 5;
        $result = $this->sut->zoek_moeders(self::LIDID, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_zoek_groeiresultaat_schaap() {
        $Karwerk = 5;
        $where = null;
        $result = $this->sut->zoek_groeiresultaat_schaap(self::LIDID, $Karwerk, $where);
        $this->assertNotFalse($result);
    }

    public function test_zoek_groeiresultaat_weging() {
        $Karwerk = 5;
        $where = null;
        $result = $this->sut->zoek_groeiresultaat_weging(self::LIDID, $Karwerk, $where);
        $this->assertNotFalse($result);
    }

    public function test_query_historie() {
        $schaapId = 1; // query-mechanisme kan niet tegen NULL!
        $result = $this->sut->query_historie(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_show_update() {
        $schaapId = null;
        $Karwerk = 5;
        $result = $this->sut->show_update(self::LIDID, $schaapId, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_noteer_overleden() {
        $schaapId = null;
        $result = $this->sut->noteer_overleden($schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_eerste_worp() {
        $schaapId = null;
        $result = $this->sut->zoek_eerste_worp(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_tel_bij_lid_en_levensnummer() {
        $levensnummers = null;
        $result = $this->sut->tel_bij_lid_en_levensnummer(self::LIDID, $levensnummers);
        $this->assertNotFalse($result);
    }

    public function test_zoek_per_levensnummers() {
        $levensnummers = null;
        $result = $this->sut->zoek_per_levensnummers($levensnummers);
        $this->assertNotFalse($result);
    }

    public function test_getMedicatieWhere() {
        $post = null;
        $result = $this->sut->getMedicatieWhere($post);
        $this->assertNotFalse($result);
    }

    public function test_zoek_aanwezig_moeder() {
        $where_mdr = 'true';
        $result = $this->sut->zoek_aanwezig_moeder(self::LIDID, $where_mdr);
        $this->assertNotFalse($result);
    }

    public function test_tel_medicijn_historie() {
        $schaapId = null;
        $result = $this->sut->tel_medicijn_historie(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_datum_bestemming() {
        $hisId = null;
        $result = $this->sut->zoek_datum_bestemming($hisId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_aflevergegevens() {
        $bestm = null;
        $date = null;
        $result = $this->sut->zoek_aflevergegevens($bestm, $date);
        $this->assertNotFalse($result);
    }

    public function test_zoek_schaap_aanvoer() {
        $schaapId = null;
        $result = $this->sut->zoek_schaap_aanvoer($schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_pil_aflever() {
        $schaapId = null;
        $result = $this->sut->zoek_pil_aflever(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_pil() {
        $date = '2020-01-01';
        $schaapId = null;
        $result = $this->sut->zoek_pil(self::LIDID, $date, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_pil_afvoer() {
        $schaapId = null;
        $date = null;
        $result = $this->sut->zoek_pil_afvoer(self::LIDID, $schaapId, $date);
        $this->assertNotFalse($result);
    }

    public function test_fase_bij_dier() {
        
        $result = $this->sut->fase_bij_dier();
        $this->assertNotFalse($result);
    }

    public function test_zoek_laatste_dekkingen() {
        $Karwerk = 5;
        $result = $this->sut->zoek_laatste_dekkingen($Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_zoek_laatste_dekking_van_ooi() {
        $schaapId = null;
        $result = $this->sut->zoek_laatste_dekking_van_ooi(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_werpdatum_laatste_dekking() {
        
        $result = $this->sut->zoek_werpdatum_laatste_dekking();
        $this->assertNotFalse($result);
    }

    public function test_zoek_eerder_levensnummer() {
        $levnr = null;
        $result = $this->sut->zoek_eerder_levensnummer($levnr);
        $this->assertNotFalse($result);
    }

    public function test_zoek_laatste_worp() {
        $mdrId = null;
        $result = $this->sut->zoek_laatste_worp($mdrId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_datum_laatste_worp() {
        $volwId = null;
        $result = $this->sut->zoek_datum_laatste_worp($volwId);
        $this->assertNotFalse($result);
    }

    public function test_maak_schaap() {
        $levnr = null;
        $rasId = null;
        $geslacht = null;
        $volwId = null;
        $momId = null;
        $redId = null;
        $result = $this->sut->maak_schaap($levnr, $rasId, $geslacht, $volwId, $momId, $redId);
        $this->assertNotFalse($result);
    }

    public function test_maak_minimaal_schaap() {
        $levnr = null;
        $ras = null;
        $sekse = null;
        $result = $this->sut->maak_minimaal_schaap($levnr, $ras, $sekse);
        $this->assertNotFalse($result);
    }

    public function test_wis_levensnummer() {
        $ubn = null;
        $result = $this->sut->wis_levensnummer($ubn);
        $this->assertNotFalse($result);
    }

    public function test_groeiresultaat() {
        $Karwerk = 5;
        $result = $this->sut->groeiresultaat(self::LIDID, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_zoek_wegingen() {
        $result = $this->sut->zoek_wegingen(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_pil_overzicht() {
        $schaapId = null;
        $result = $this->sut->pil_overzicht(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_ooien_met_meerlingworpen() {
        $Karwerk = 5;
        $order = '1';
        $result = $this->sut->ooien_met_meerlingworpen(self::LIDID, $Karwerk, $order);
        $this->assertNotFalse($result);
    }

    public function test_alle_ooien_met_meerlingworpen() {
        $Karwerk = 5;
        $order = '1';
        $jaar1 = null;
        $jaar2 = null;
        $jaar3 = null;
        $jaar4 = null;
        $result = $this->sut->alle_ooien_met_meerlingworpen(self::LIDID, $Karwerk, $order, $jaar1, $jaar2, $jaar3, $jaar4);
        $this->assertNotFalse($result);
    }

    public function test_zoek_maanden_per_ooi() {
        $ooiId = null;
        $jaar1 = null;
        $jaar4 = null;
        $result = $this->sut->zoek_maanden_per_ooi(self::LIDID, $ooiId, $jaar1, $jaar4);
        $this->assertNotFalse($result);
    }

    public function test_zoek_meerlingen() {
        $Karwerk = 5;
        $van = null;
        $tot = null;
        $result = $this->sut->zoek_meerlingen(self::LIDID, $Karwerk, $van, $tot);
        $this->assertNotFalse($result);
    }

    public function test_zoek_meerling() {
        $Karwerk = 5;
        $van = null;
        $tot = null;
        $result = $this->sut->zoek_meerling(self::LIDID, $Karwerk, $van, $tot);
        $this->assertNotFalse($result);
    }

    public function test_ooikaart_all() {
        $Karwerk = 5;
        $result = $this->sut->ooikaart_all(self::LIDID, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_toon_meerlingen() {
        $van = null;
        $tot = null;
        $result = $this->sut->toon_meerlingen(self::LIDID, $van, $tot);
        $this->assertNotFalse($result);
    }

    public function test_tel_niet_afgevoerd() {
        $result = $this->sut->tel_niet_afgevoerd(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_afvoerlijst() {
        $Karwerk = 5;
        $result = $this->sut->afvoerlijst(self::LIDID, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_zoek_info() {
        $Karwerk = 5; 
        $result = $this->sut->zoek_info(self::LIDID, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_zoek_moederdier() {
        $Karwerk = 5;
        $ooi = null;
        $result = $this->sut->zoek_moederdier(self::LIDID, $Karwerk, $ooi);
        $this->assertNotFalse($result);
    }

    public function test_zoek_lammeren() {
        $ooi = null;
        $Karwerk = 5;
        $result = $this->sut->zoek_lammeren(self::LIDID, $ooi, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_stallijstgegevens() {
        $Karwerk = 5;
        $result = $this->sut->stallijstgegevens(self::LIDID, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_getHokAanwasFrom() {
        
        $result = $this->sut->getHokAanwasFrom();
        $this->assertNotFalse($result);
    }

    public function test_getHokAanwasWhere() {
        $ID = null;
        $result = $this->sut->getHokAanwasWhere($ID, self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_getHokOverplFrom() {
        
        $result = $this->sut->getHokOverplFrom();
        $this->assertNotFalse($result);
    }

    public function test_getHokOverplWhere() {
        $keuze = null;
        $hokId = null;
        $result = $this->sut->getHokOverplWhere($keuze, $hokId);
        $this->assertNotFalse($result);
    }

    public function test_aantal_volwassen_dieren() {
        $hokId = null;
        $result = $this->sut->aantal_volwassen_dieren($hokId);
        $this->assertNotFalse($result);
    }

    public function test_getHokVerlatenFrom() {
        
        $result = $this->sut->getHokVerlatenFrom();
        $this->assertNotFalse($result);
    }

    public function test_getHokVerlatenWhere() {
        $hokId = null;
        $result = $this->sut->getHokVerlatenWhere(self::LIDID, $hokId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_moederdieren() {
        $Karwerk = 5;
        $result = $this->sut->zoek_moederdieren(self::LIDID, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_zoek_vaderdieren() {
        $Karwerk = 5;
        $result = $this->sut->zoek_vaderdieren(self::LIDID, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_zoek_vader_laatste_dekkingen() {
        $volwId = null;
        $Karwerk = 5;
        $result = $this->sut->zoek_vader_laatste_dekkingen($volwId, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_zoek_moederdieren_183() {
        $Karwerk = 5;
        $result = $this->sut->zoek_moederdieren_183(self::LIDID, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_start_moeder() {
        $schaapId = null;
        $stalId = null;
        $result = $this->sut->start_moeder(self::LIDID, $schaapId, $stalId);
        $this->assertNotFalse($result);
    }

    public function test_einde_moeder() {
        $schaapId = null;
        $result = $this->sut->einde_moeder(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_vorige_worp() {
        $schaapId = null;
        $day = null;
        $result = $this->sut->zoek_vorige_worp($schaapId, $day);
        $this->assertNotFalse($result);
    }

    public function test_zoek_huidige_worp() {
        $mdrId = null;
        $volwId = null;
        $result = $this->sut->zoek_huidige_worp($mdrId, $volwId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_fase() {
        $schaapId = null;
        $result = $this->sut->zoek_fase(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_levnr() {
        $levensnummer = null;
        $result = $this->sut->zoek_levnr($levensnummer);
        $this->assertNotFalse($result);
    }

    public function test_zoek_levnr_db() {
        $schaapId = null;
        $result = $this->sut->zoek_levnr_db($schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_aantal_niet_gescand() {
        $result = $this->sut->zoek_aantal_niet_gescand(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_niet_gescande_schapen() {
        $result = $this->sut->zoek_niet_gescande_schapen(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_dieren() {
        $datumvan = null;
        $datumtot = null;
        $aant = null;
        $result = $this->sut->zoek_dieren(self::LIDID, $datumvan, $datumtot, $aant);
        $this->assertNotFalse($result);
    }

    public function test_jaarworp() {
        $jaar = null;
        $result = $this->sut->jaarworp(self::LIDID, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_zoek_gegevens_schaap() {
        $schaapId = null;
        $Karwerk = 5;
        $result = $this->sut->zoek_gegevens_schaap($schaapId, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_zoek_vandaag_ingevoerd_met_levnr() {
        $result = $this->sut->zoek_vandaag_ingevoerd_met_levnr(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_vandaag_ingevoerd_zonder_levnr() {
        $result = $this->sut->zoek_vandaag_ingevoerd_zonder_levnr(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_mindag() {
        $schaapId = null;
        $result = $this->sut->zoek_mindag($schaapId);
        $this->assertNotFalse($result);
    }

    public function test_schapen_geboren() {
        $Karwerk = 5;
        $result = $this->sut->schapen_geboren(self::LIDID, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_schapen_speen() {
        $Karwerk = 5;
        $result = $this->sut->schapen_speen(self::LIDID, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_schapen_vanaf_aanwas() {
        $Karwerk = 5;
        $result = $this->sut->schapen_vanaf_aanwas(self::LIDID, $Karwerk);
        $this->assertNotFalse($result);
    }

}
