<?php

class VolwasGatewayTest extends GatewayCase {

    protected static $sutname = 'VolwasGateway';

    public function test_zoek_laatste_koppel_na_laatste_worp_obv_moeder() {
        $kzlMdr = null;
        $result = $this->sut->zoek_laatste_koppel_na_laatste_worp_obv_moeder($kzlMdr);
        $this->assertNotFalse($result);
    }

    public function test_zoek_moeder_vader_uit_laatste_koppel() {
        $koppel = null;
        $result = $this->sut->zoek_moeder_vader_uit_laatste_koppel($koppel);
        $this->assertNotFalse($result);
    }

    public function test_vroegst_volgende_dekdatum() {
        $kzlMdr = null;
        $result = $this->sut->vroegst_volgende_dekdatum($kzlMdr);
        $this->assertNotFalse($result);
    }

    public function test_zoek_volwas() {
        $schaapId = 1;
        $result = $this->sut->zoek_volwas($schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_laatste_worp_moeder() {
        $mdrId = null;
        $result = $this->sut->zoek_laatste_worp_moeder($mdrId);
        $this->assertNotFalse($result);
    }

    public function testGeenDekkingen() {
        $Karwerk = 5;
        $jaar = 2000;
        $actual = $this->sut->zoek_dekkingen(self::LIDID, $Karwerk, $jaar);
        $this->assertEquals(0, $actual->num_rows);
    }

    public function test_zoek_dekkingen() {
        $Karwerk = 5;
        $jaar = null;
        $result = $this->sut->zoek_dekkingen(self::LIDID, $Karwerk, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_zoek_ouders() {
        $mdrId = null;
        $vdrId = null;
        $result = $this->sut->zoek_ouders($mdrId, $vdrId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_actuele_worp() {
        $mdrId = null;
        $datum = null;
        $result = $this->sut->zoek_actuele_worp($mdrId, $datum);
        $this->assertNotFalse($result);
    }

    public function test_zoek_vorige_worp() {
        $mdrId = null;
        $datum = null;
        $result = $this->sut->zoek_vorige_worp($mdrId, $datum);
        $this->assertNotFalse($result);
    }

    public function test_zoek_actuele_dracht() {
        $mdrId = null;
        $volwId = null;
        $result = $this->sut->zoek_actuele_dracht($mdrId, $volwId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_actuele_dekking() {
        $mdrId = null;
        $volwId = null;
        $result = $this->sut->zoek_actuele_dekking($mdrId, $volwId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_vader_uit_koppel() {
        $volwId = null;
        $result = $this->sut->zoek_vader_uit_koppel($volwId);
        $this->assertNotFalse($result);
    }

    public function test_update_koppel() {
        $vdrId = null;
        $volwId = null;
        $result = $this->sut->update_koppel($vdrId, $volwId);
        $this->assertNotFalse($result);
    }

    public function test_maak_koppel() {
        $mdrId = null;
        $vdrId = null;
        $result = $this->sut->maak_koppel($mdrId, $vdrId);
        $this->assertNotFalse($result);
    }

    public function test_insert() {
        $recId = null;
        $mdrId = null;
        $result = $this->sut->insert($recId, $mdrId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_recentste_id() {
        $mdrId = null;
        $result = $this->sut->zoek_recentste_id($mdrId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_bestaande_worp() {
        $mdrId = null;
        $datum = null;
        $result = $this->sut->zoek_bestaande_worp($mdrId, $datum);
        $this->assertNotFalse($result);
    }

    public function test_zoek_laatste_worp_voor_geboortedatum() {
        $mdrId = null;
        $datum = null;
        $result = $this->sut->zoek_laatste_worp_voor_geboortedatum($mdrId, $datum);
        $this->assertNotFalse($result);
    }

    public function test_zoek_volgende_worp_na_geboortedatum() {
        $mdrId = null;
        $datum = null;
        $result = $this->sut->zoek_volgende_worp_na_geboortedatum($mdrId, $datum);
        $this->assertNotFalse($result);
    }

    public function test_zoek_drachtdatum() {
        $volwId = null;
        $result = $this->sut->zoek_drachtdatum($volwId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_werpdatum() {
        $schaapId = 1;
        $result = $this->sut->zoek_werpdatum($schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_laatste_worp() {
        $schaapId = 1;
        $result = $this->sut->zoek_laatste_worp($schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_laatste_dekkingen_met_vader_zonder_werpdatum() {
        $Karwerk = 5;
        $result = $this->sut->zoek_laatste_dekkingen_met_vader_zonder_werpdatum(self::LIDID, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_zoek_laatste_werpdatum() {
        $Karwerk = 5;
        $result = $this->sut->zoek_laatste_werpdatum(self::LIDID, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_zoek_vader_uit_laatste_dekkingen() {
        $schaapId = 1;
        $Karwerk = 5;
        $result = $this->sut->zoek_vader_uit_laatste_dekkingen($schaapId, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_zoek_laatste_dekking_van_ooi() {
        $schaapId = 1;
        $result = $this->sut->zoek_laatste_dekking_van_ooi(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_laatste_werpdatum_dracht() {
        $schaapId = 1;
        $result = $this->sut->zoek_laatste_werpdatum_dracht($schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_dekkingen_voor_week1() {
        $jaar = null;
        $datum = null;
        $result = $this->sut->zoek_dekkingen_voor_week1(self::LIDID, $jaar, $datum);
        $this->assertNotFalse($result);
    }

    public function test_zoek_aantal_dekkingen() {
        $jaarweek = null;
        $result = $this->sut->zoek_aantal_dekkingen(self::LIDID, $jaarweek);
        $this->assertNotFalse($result);
    }

    public function test_zoek_aantal_dekkingen_per_maand() {
        $van = null;
        $tot = null;
        $result = $this->sut->zoek_aantal_dekkingen_per_maand(self::LIDID, $van, $tot);
        $this->assertNotFalse($result);
    }

}
