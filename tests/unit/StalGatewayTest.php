<?php

class StalGatewayTest extends GatewayCase {

    protected static $sutname = 'StalGateway';

    public function testUpdateHerkomstByMelding() {
        $this->runfixture('melding-4');
        $recId = 4; // uit de fixture.
        $fldHerk = 9;
        $this->sut->updateHerkomstByMelding($recId, $fldHerk);
        $this->assertTableWithPK('tblStal', 'stalId', 49, ['rel_herk' => 9]);
    }

    public function testZoekKleurHalsnr() {
        $this->runSQL("DELETE FROM tblStal");
        $this->assertNull($this->sut->zoekKleurHalsnr(self::LIDID, 1)['stalId']);
    }

    public function testZoekLaatsteStalId() {
        $this->runSQL("DELETE FROM tblStal");
        $this->assertNull($this->sut->zoek_laatste_stalId(self::LIDID, 4));
    }

    // **************************************************************

    public function test_updateHerkomstByMelding() {
        $recId = null;
        $fldHerk = null;
        $result = $this->sut->updateHerkomstByMelding($recId, $fldHerk);
        $this->assertNotFalse($result);
    }

    public function test_updateBestemmingByMelding() {
        $recId = null;
        $fldBest = null;
        $result = $this->sut->updateBestemmingByMelding($recId, $fldBest);
        $this->assertNotFalse($result);
    }

    public function test_tel_stallijsten() {
        $schaapId = null;
        $result = $this->sut->tel_stallijsten(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_kzlOoien() {
        $Karwerk = 5;
        $result = $this->sut->kzlOoien(self::LIDID, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_ooien_invschaap() {
        $Karwerk = 5;
        $row_former = null;
        $result = $this->sut->ooien_invschaap(self::LIDID, $Karwerk, $row_former);
        $this->assertNotFalse($result);
    }

    public function test_rammen_invschaap() {
        $Karwerk = 5;
        $row_former = null;
        $result = $this->sut->rammen_invschaap(self::LIDID, $Karwerk, $row_former);
        $this->assertNotFalse($result);
    }

    public function test_kzlRammen() {
        $Karwerk = 5;
        $result = $this->sut->kzlRammen(self::LIDID, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_zoek_laatste_stalId() {
        $schaapId = null;
        $result = $this->sut->zoek_laatste_stalId(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_findLidByStal() {
        $stalId = null;
        $result = $this->sut->findLidByStal($stalId);
        $this->assertNotFalse($result);
    }

    public function test_zoekKleurHalsnr() {
        $schaapId = null;
        $result = $this->sut->zoekKleurHalsnr(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_kleuren_halsnrs() {
        $result = $this->sut->zoek_kleuren_halsnrs(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_updateKleurHalsnr() {
        $stalId = null;
        $kleur = null;
        $halsnr = null;
        $result = $this->sut->updateKleurHalsnr($stalId, $kleur, $halsnr);
        $this->assertNotFalse($result);
    }

    public function test_zoek_relid() {
        $schaapId = null;
        $result = $this->sut->zoek_relid(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_update_relbest() {
        $stalId = null;
        $rel_best = null;
        $result = $this->sut->update_relbest($stalId, $rel_best);
        $this->assertNotFalse($result);
    }

    public function test_update_relbest_by_his() {
        $hisId = null;
        $rel_best = null;
        $result = $this->sut->update_relbest_by_his($hisId, $rel_best);
        $this->assertNotFalse($result);
    }

    public function test_insert() {
        $ubnId = 1;
        $schaapId = 1;
        $rel_herk = null;
        $result = $this->sut->insert(self::LIDID, $ubnId, $schaapId, $rel_herk);
        $this->assertNotFalse($result);
    }

    public function test_insert_uitgebreid() {
        $schaapId = 1;
        $rel_herk = null;
        $ubnId = 1;
        $kleur = null;
        $halsnr = null;
        $rel_best = null;
        $result = $this->sut->insert_uitgebreid(self::LIDID, $schaapId, $rel_herk, $ubnId, $kleur, $halsnr, $rel_best);
        $this->assertNotFalse($result);
    }

    public function test_zoek_laatste_stal() {
        $schaapId = null;
        $result = $this->sut->zoek_laatste_stal(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_in_stallijst() {
        $levnr = null;
        $result = $this->sut->zoek_in_stallijst(self::LIDID, $levnr);
        $this->assertNotFalse($result);
    }

    public function test_zoek_in_afgevoerd() {
        $levnr = null;
        $result = $this->sut->zoek_in_afgevoerd(self::LIDID, $levnr);
        $this->assertNotFalse($result);
    }

    public function test_zoek_dood() {
        $levnr = null;
        $result = $this->sut->zoek_dood($levnr);
        $this->assertNotFalse($result);
    }

    public function test_zoek_uitgeschaard() {
        $levnr = null;
        $result = $this->sut->zoek_uitgeschaard($levnr);
        $this->assertNotFalse($result);
    }

    public function test_zoek_herkomst() {
        $hisId = null;
        $result = $this->sut->zoek_herkomst($hisId);
        $this->assertNotFalse($result);
    }

    public function test_startdm_moeder() {
        $schaapId = null;
        $result = $this->sut->startdm_moeder(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_eindm_mdr_indien_afgevoerd() {
        $schaapId = null;
        $result = $this->sut->zoek_eindm_mdr_indien_afgevoerd(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_findByLidWithoutBest() {
        $recId = null;
        $result = $this->sut->findByLidWithoutBest(self::LIDID, $recId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_stal() {
        $schaapId = null;
        $result = $this->sut->zoek_stal(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_getHokSpenenFrom() {
        
        $result = $this->sut->getHokSpenenFrom();
        $this->assertNotFalse($result);
    }

    public function test_getHokSpenenWhere() {
        $hokId = null;
        $condition = null;
        $result = $this->sut->getHokSpenenWhere(self::LIDID, $hokId, $condition);
        $this->assertNotFalse($result);
    }

    public function test_zoek_afvoerstatus_mdr() {
        $schaapId = null;
        $result = $this->sut->zoek_afvoerstatus_mdr(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_terug_uitscharen() {
        $schaapId = null;
        $result = $this->sut->zoek_terug_uitscharen($schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_laatste_stal_medicijn() {
        $schaapId = null;
        $result = $this->sut->zoek_laatste_stal_medicijn($schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_scan() {
        $stalId = null;
        $result = $this->sut->zoek_scan($stalId);
        $this->assertNotFalse($result);
    }

    public function test_is_niet_dubbel_zonder_data() {
        $scan = null;
        $result = $this->sut->is_dubbel(self::LIDID, $scan);
        $this->assertFalse($result);
    }

    public function test_is_dubbel() {
        $this->runSQL("INSERT INTO tblStal(lidId, schaapId, scan, rel_best) VALUES(1, 1, 'test', null)");
        $scan = 'test';
        $result = $this->sut->is_dubbel(self::LIDID, $scan);
        $this->assertTrue($result);
    }

    public function test_verwijder_scan_afgevoerden() {
        $scan = null;
        $result = $this->sut->verwijder_scan_afgevoerden(self::LIDID, $scan);
        $this->assertNotFalse($result);
    }

    public function test_update_scan() {
        $stalId = null;
        $scan = null;
        $result = $this->sut->update_scan($stalId, $scan);
        $this->assertNotFalse($result);
    }

    public function test_jaargeboortes() {
        $jaar = null;
        $result = $this->sut->jaargeboortes(self::LIDID, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_jaarsterfte() {
        $jaar = null;
        $result = $this->sut->jaarsterfte(self::LIDID, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_zoek_startjaar_user() {
        $result = $this->sut->zoek_startjaar_user(self::LIDID);
        $this->assertNotFalse($result);
    }

}
