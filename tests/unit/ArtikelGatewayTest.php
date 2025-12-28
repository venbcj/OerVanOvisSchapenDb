<?php

class ArtikelGatewayTest extends GatewayCase {

    public static $sutname = 'ArtikelGateway';
    public function test_pilForLid() {
        $this->runfixture('voervoorraad');
        $result = $this->sut->pilForLid(self::LIDID);
        $this->assertNotFalse($result);
        $this->assertEquals(1, $result->num_rows);
    }

    public function test_new_medicijn() {
        $result = $this->sut->new_medicijn(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_soort() {
        $artId = null;
        $result = $this->sut->zoek_soort($artId);
        $this->assertNotFalse($result);
    }

    public function test_pilregels() {
        $artId = null;
        $result = $this->sut->pilregels($artId);
        $this->assertNotFalse($result);
    }

    public function test_voerregels() {
        $artId = null;
        $result = $this->sut->voerregels($artId);
        $this->assertNotFalse($result);
    }

    public function test_voer() {
        $this->runfixture('voervoorraad');
        $res = $this->sut->voer(self::LIDID);
        $this->assertEquals(1, $res->num_rows);
    }

    public function test_pil() {
        $result = $this->sut->pil(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_voer() {
        $result = $this->sut->zoek_voer(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_countVoerByName() {
        $naam = null;
        $result = $this->sut->countVoerByName(self::LIDID, $naam);
        $this->assertNotFalse($result);
    }

    public function test_store() {
        $insNaam = null;
        $insStdat = null;
        $insNhd = null;
        $insBtw = null;
        $insRelatie = null;
        $insRubriek = null;
        $result = $this->sut->store($insNaam, $insStdat, $insNhd, $insBtw, $insRelatie, $insRubriek);
        $this->assertNotFalse($result);
    }

    public function test_findVoerByUser() {
        $result = $this->sut->findVoerByUser(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_details() {
        $artId = null;
        $result = $this->sut->details($artId);
        $this->assertNotFalse($result);
    }

    public function test_details_met_partij() {
        $artId = null;
        $result = $this->sut->details_met_partij($artId);
        $this->assertNotFalse($result);
    }

    public function test_tel_niet_in_gebruik() {
        $result = $this->sut->tel_niet_in_gebruik(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_niet_in_gebruik() {
        $result = $this->sut->zoek_niet_in_gebruik(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_activeer() {
        $artId = null;
        $result = $this->sut->activeer($artId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_pil_op_voorraad() {
        $result = $this->sut->zoek_pil_op_voorraad(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek() {
        $artId = null;
        $result = $this->sut->zoek($artId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_eenheid() {
        $artId = null;
        $result = $this->sut->zoek_eenheid($artId);
        $this->assertNotFalse($result);
    }

    public function test_voorraad() {
        $artId = null;
        $result = $this->sut->voorraad($artId);
        $this->assertNotFalse($result);
    }

    public function test_periodes() {
        $minjaar = null;
        $maxjaar = null;
        $artId = null;
        $result = $this->sut->periodes(self::LIDID, $minjaar, $maxjaar, $artId);
        $this->assertNotFalse($result);
    }

    public function test_aantal_periodes() {
        $minjaar = null;
        $maxjaar = null;
        $artId = null;
        $result = $this->sut->aantal_periodes(self::LIDID, $minjaar, $maxjaar, $artId);
        $this->assertEquals(0, $result);
    }

    public function test_maandjaren() {
        $minjaar = null;
        $maxjaar = null;
        $artId = null;
        $filter = 'true';
        $result = $this->sut->maandjaren(self::LIDID, $minjaar, $maxjaar, $artId, $filter);
        $this->assertNotFalse($result);
    }

    public function test_zoek_artid_op_voorraad() {
        $result = $this->sut->zoek_artid_op_voorraad(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_stdat() {
        $artId = null;
        $result = $this->sut->zoek_stdat($artId);
        $this->assertNotFalse($result);
    }

    public function test_kzlMedicijn_combi() {
        $artId = null;
        $result = $this->sut->kzlMedicijn_combi(self::LIDID, $artId);
        $this->assertNotFalse($result);
    }

    public function test_medicijn_actief() {
        $artId = null;
        $result = $this->sut->medicijn_actief(self::LIDID, $artId);
        $this->assertNotFalse($result);
    }

}
