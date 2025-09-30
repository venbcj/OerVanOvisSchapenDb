<?php

class SchaapGatewayTest extends GatewayCase {

    private const SCHAAP4_ID = 4;
    private const SCHAAP4_LEVENSNUMMER = '4';
    private const LIDID = 1;
    private const STALID = 1;
    private const NEW_GESLACHT = 'ooi';
    private const NEW_LEVENSNUMMER = '9990303';
    private const NEWSCHAAPID = 7;
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
        $this->runSQL("DELETE FROM tblSchaap");
        $lidid = 1;
        $M = 1; // "maandnummer" ?
        $J = 1970; // "jaar" ?
        $V = 1; // iets uit "kzlPil"
        # todo: deze twee inpakken. Veel queries hebben drie verschijningsvormen
        $Sekse = 's.geslacht is not null';
        $Ouder = 'isnull(oudr.hisId)';
        $this->assertSame(false, $this->sut->eenheid_fase($lidid, $M, $J, $V, $Sekse, $Ouder));
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
        $this->assertEquals(1, $this->sut->zoekStapel(1));
    }

    public function testAfleverdatum() {
        $this->runfixture('schaap-afleverdatum');
        $res = $this->sut->afleverdatum(1);
        $this->assertEquals(1, $res->num_rows);
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
        $this->assertEquals(1, $this->sut->zoekSchaap($where));
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
    }

    public function testZoekSchaapid() {
        $this->runfixture('schaap-4');
        $this->assertEquals(self::SCHAAP4_ID, $this->sut->zoek_schaapid(self::SCHAAP4_LEVENSNUMMER));
    }

    public function testZoekStalid() {
        $this->runfixture('schaap-4');
        $this->runSQL("DELETE FROM tblHistorie WHERE stalId=1");
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
        $this->runSQL("DELETE FROM tblHistorie WHERE stalId=1");
        $Karwerk = 5;
        # er is een preconditie waar de methode [0,0] geeft. Soms fout, dus
        $this->assertEquals([], $this->sut->zoek_vaders(self::LIDID, $Karwerk));
    }

    public function testVindVaders() {
        $this->runfixture('schaap-4');
        $this->runSQL("INSERT INTO tblHistorie(stalId, actId) VALUES(1, 3)");
        $Karwerk = 5;
        $this->assertEquals([['stalId' => 1, 'werknr' => '4', 'halsnr' => null]], $this->sut->zoek_vaders(self::LIDID, $Karwerk));
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
        $this->runSQL("DELETE FROM tblSchaap");
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
        $this->runSQL("DELETE FROM tblRelatie");
        $this->runSQL("DELETE FROM tblPartij");
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
        $this->runSQL("DELETE FROM tblSchaap");
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
        $this->runSQL("DELETE FROM tblSchaap");
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
        $this->runfixture('schaap-4');
        $ooiId = 4;
        $aantal = 1;
        $this->runSQL("DELETE FROM tblVolwas");
        $this->runSQL("INSERT INTO tblVolwas(mdrId, volwId) VALUES(4, 1)");
        $this->runSQL("INSERT INTO tblSchaap(schaapId, volwId) VALUES(" . self::NEWSCHAAPID . ", " . self::VOLWID . ")"); // lam
        $this->runSQL("INSERT INTO tblStal(stalId, lidId, schaapId) VALUES(2, 1, " . self::NEWSCHAAPID . ")");
        $this->runSQL("INSERT INTO tblHistorie(stalId, actId, datum) VALUES(2, 1, '2021-11-07')");
        $res = $this->sut->aantal_meerlingen_perOoi(self::LIDID, $ooiId, $aantal);
        $this->assertEquals(1, $res->num_rows);
        // deze '1' is het id in tblVolw. Wat zijn we nu aan het doen?
        $this->assertEquals(1, $res->fetch_assoc()['volwId']);
    }

    public function testDeLammerenLeeg() {
        $this->runSQL("DELETE FROM tblSchaap");
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
        $this->runSQL("DELETE FROM tblVolwas");
        $this->runSQL("INSERT INTO tblVolwas(mdrId, volwId) VALUES(4, 1)");
        $this->runSQL("INSERT INTO tblSchaap(schaapId, volwId) VALUES(" . self::NEWSCHAAPID . ", " . self::VOLWID . ")"); // lam
        $this->runSQL("INSERT INTO tblStal(stalId, lidId, schaapId) VALUES(2, 1, " . self::NEWSCHAAPID . ")");
        $this->runSQL("INSERT INTO tblHistorie(stalId, actId, datum) VALUES(2, 1, '2020-09-07')");
        $res = $this->sut->meerlingen_perOoi_perJaar(self::LIDID, self::SCHAAP4_ID, 2020, '09');
        $this->assertEquals([1, self::VOLWID], $res);
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
        $this->runSQL("DELETE FROM tblSchaap");
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
        $this->runSQL("DELETE FROM tblSchaap");
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
        $this->runSQL("DELETE FROM tblSchaap");
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

}
