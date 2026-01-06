<?php

class DekkingenTest extends IntegrationCase {

    public function testNieuweInvoerDierGeenDatum() {
        $this->post('/Dekkingen.php', [
            'ingelogd' => 1,
            'knpInsert1_' => 1,
        ]);
        $this->assertNoNoise();
        $this->assertFout('De datum is onbekend.');
    }

    public function testNieuweInvoerDierGeenRegistratie() {
        $this->post('/Dekkingen.php', [
            'ingelogd' => 1,
            'knpInsert1_' => 1,
            'txtDatum1_' => '13-01-2012',
        ]);
        $this->assertNoNoise();
        $this->assertFout('Soort registratie is onbekend.');
    }

    public function testNieuweInvoerDierGeenMoeder() {
        $this->post('/Dekkingen.php', [
            'ingelogd' => 1,
            'knpInsert1_' => 1,
            'txtDatum1_' => '13-01-2012',
            'kzlWat_' => '1',
        ]);
        $this->assertNoNoise();
        $this->assertFout('Moederdier is onbekend.');
    }

    public function testNieuweInvoerDierZelfdeRam() {
        // TODO: #0004114 fixture versterken, dit is nog niet begrijpelijk
        $this->runfixture('dekking');
        $this->post('/Dekkingen.php', [
            'ingelogd' => 1,
            'knpInsert1_' => 1,
            'txtDatum1_' => '13-01-2012',
            'kzlWat_' => '1',
            'kzlOoi_' => 7,
            'kzlRamNew1_' => 8,
        ]);
        $this->assertNoNoise();
        // datum komt uit fixture
        // NOTE: dit faalt af en toe. Er mist nog iets in de setup van deze test. Er komt dan geen fout.
        // Deductie. kennelijk is lst_mdr != kzlMdr?, dwz  != post[kzlOoi] ; lst_mdr komt uit zoek_moeder_vader_uit_laatste_koppel
        // of dekmoment niet gezet, lst_vdr != kzlVdr, kzlVdr niet gezet?
        // -- dekmoment komt uit zoek_moe...
        // Is er nog een omliggende if() die zou kunnen falen?
        // regel 123 txtDay [txtDatum1], registratie [post[kzlWat], kzlMdr: moeten allen gezet zijn.
        //  (104) post[knpInsert1] moet ook gezet zijn. Dat zit allemaal in de test-setup
        $this->assertFout('Deze ram heeft deze ooi reeds als laatste gedekt en wel op 02-02-2013.');
    }

    public function testNieuweInvoerDierAlDrachtig() {
        // TODO: fixture versterken, dit is nog niet begrijpelijk
        $this->runfixture('dekking-dracht');
        $this->post('/Dekkingen.php', [
            'ingelogd' => 1,
            'knpInsert1_' => 1,
            'txtDatum1_' => '13-01-2012',
            'kzlWat_' => '1',
            'kzlOoi_' => 7,
            'kzlRamNew1_' => 8,
        ]);
        $this->assertNoNoise();
        // datum komt uit fixture
        // NOTE: dit faalt af en toe. Er mist nog iets in de setup van deze test. Er komt dan geen fout.
        $this->assertFout('Deze ooi is reeds drachtig per 02-02-2013.');
    }

    public function testNieuweInvoerHokGeenDatum() {
        $this->post('/Dekkingen.php', [
            'ingelogd' => 1,
            'knpInsert2_' => 1,
        ]);
        $this->assertNoNoise();
        $this->assertFout('De datum is onbekend.');
    }

    public function testNieuweInvoerHokGeenHok() {
        $this->post('/Dekkingen.php', [
            'ingelogd' => 1,
            'knpInsert2_' => 1,
            'txtDatum2_' => '13-01-2012',
        ]);
        $this->assertNoNoise();
        $this->assertFout('Verblijf is onbekend.');
    }

    public function testNieuweInvoerHokGeenRam() {
        $this->post('/Dekkingen.php', [
            'ingelogd' => 1,
            'knpInsert2_' => 1,
            'txtDatum2_' => '13-01-2012',
            'kzlHok_' => '1',
        ]);
        $this->assertNoNoise();
        $this->assertFout('Ram is onbekend.');
    }

    public function testNieuweInvoerHok() {
        $this->uses_db();
        $this->post('/Dekkingen.php', [
            'ingelogd' => 1,
            'knpInsert2_' => 1,
            'txtDatum2_' => '13-01-2012',
            'kzlWat_' => 1,
            'kzlHok_' => '1',
            'kzlRamNew2_' => 8,
        ]);
        $this->assertNoNoise();
        $this->assertFout('Dit verblijf heeft geen moederdieren.');
    }

    public function testNieuweInvoerHokMetMoeders() {
        $this->runfixture('schaap-4');
        $this->runfixture('moeders-in-verblijf');
        $this->post('/Dekkingen.php', [
            'ingelogd' => 1,
            'knpInsert2_' => 1,
            'txtDatum2_' => '13-01-2012',
            'kzlWat_' => 1,
            'kzlHok_' => '1',
            'kzlRamNew2_' => 8,
        ]);
        $this->assertNoNoise();
        $this->assertNotFout();
    }

    public function testNieuweInvoerHokMetMoedersEnDekkingen() {
        $this->runfixture('schaap-4');
        $this->runfixture('moeders-in-verblijf');
        $this->runfixture('dekkingen'); // Slechte naam; er is ook al eentje 'dekking'
        $this->post('/Dekkingen.php', [
            'ingelogd' => 1,
            'knpInsert2_' => 1,
            'txtDatum2_' => '13-01-2012',
            'kzlWat_' => 1,
            'kzlHok_' => '1',
            'kzlRamNew2_' => 8,
        ]);
        $this->assertNoNoise();
        $this->assertNotFout();
    }

        // $this->assertFout('De dekdatum mag niet voor de laatste dekking met dit vaderdier liggen. Dit geldt voor tenminste 1 moederdier uit dit verblijf.');

    public function testPostSave_delete() {
        $this->runfixture('schaap-4');
        $this->runfixture('moeders-in-verblijf');
        $this->runfixture('dekkingen');
        $this->post('/Dekkingen.php', [
            'ingelogd_' => 1,
            'knpSave_' => 1,
            'chkDel_1' => 1,
            'kzlDrachtUpd_1' => 1,
        ]);
        $this->assertNoNoise();
        $this->assertNotFout();
    }

    public function testPostSave_euh() {
        $this->runfixture('schaap-4');
        $this->runfixture('moeders-in-verblijf');
        $this->runfixture('dekkingen');
        $this->post('/Dekkingen.php', [
            'ingelogd_' => 1,
            'knpSave_' => 1,
            'kzlRam_1' => 1, // dekt een stukje "ram wijzigen"
            'kzlDrachtUpd_1' => 1,
        ]);
        $this->assertNoNoise();
        $this->assertNotFout();
    }

    /* bereik het stuk "dracht wijzigen"
     * *-hoe maak je fldDracht=ja?
     * | +- zet post[kzlDrachtUpd] op ja
     * *-hoe maak je drachtig=ja?
     *   +-zet drachtdm_db of schaapId
     *     +-hoe zet je drachtdm_db?
     *     | +-heb een entry in tblDracht join tblHistorie using(hisId) met d.volwId=recid
     *     +-hoe zet je schaapId? <=== TODO case hiervoor?
     *       +-heb een entry in tblVolwas join tblSchaap using (volwId) met volwId=recid
     * hoe vul je dmDek?
     * +-heb een entry in tblVolwas join tblHistorie using(hisId) met volwId=recId
     */
    public function testPostSave_bestaande_dracht() {
        $this->runfixture('save_dracht');
        $this->post('/Dekkingen.php', [
            'ingelogd_' => 1,
            'knpSave_' => 1,
            'kzlDrachtUpd_1' => 'ja',
            'txtDrachtdm_1' => '2020-01-01',
            'txtGrootte_1' => 1,
        ]);
        $this->assertNoNoise();
        $this->assertNotFout();
    }

    public function testPostSave_nieuwe_dracht() {
        $this->runSQL("INSERT INTO tblVolwas(hisId, volwId, mdrId) VALUES(2, 1, 4)");
        $this->runSQL("INSERT INTO tblStal(stalId, schaapId, ubnId) values(9, 4, 1)");
        $this->post('/Dekkingen.php', [
            'ingelogd_' => 1,
            'knpSave_' => 1,
            'kzlDrachtUpd_1' => 'ja',
            'txtDrachtdm_1' => '2020-01-01',
            'txtGrootte_1' => 1,
        ]);
        $this->assertNoNoise();
        $this->assertNotFout();
    }

    public function testPostSave_verwijder_dracht() {
        $this->runfixture('save_dracht');
        # $this->runSQL("INSERT INTO tblVolwas(hisId, volwId, mdrId) VALUES(2, 1, 4)");
        # $this->runSQL("INSERT INTO tblStal(stalId, schaapId, ubnId) values(9, 4, 1)");
        $this->post('/Dekkingen.php', [
            'ingelogd_' => 1,
            'knpSave_' => 1,
            'kzlDrachtUpd_1' => 'nee',
            'txtDrachtdm_1' => '2020-01-01',
            'txtGrootte_1' => 1,
        ]);
        $this->assertNoNoise();
        $this->assertNotFout();
    }

}
