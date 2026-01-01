<?php

class DeklijstGatewayTest extends GatewayCase {

    public static $sutname = 'DeklijstGateway';

    public function test_insert() {
        $datum = null;
        $result = $this->sut->insert(self::LIDID, $datum);
        $this->assertNotFalse($result);
    }

    public function test_find_aantal() {
        $dekId = null;
        $result = $this->sut->find_aantal($dekId);
        $this->assertNotFalse($result);
    }

    public function test_update() {
        $dekId = null;
        $flddekat = null;
        $result = $this->sut->update($dekId, $flddekat);
        $this->assertNotFalse($result);
    }

    public function test_zoek_laatste_jaar() {
        $result = $this->sut->zoek_laatste_jaar(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_max_dekjaar() {
        $jaar = null;
        $result = $this->sut->zoek_max_dekjaar(self::LIDID, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_zoek_afvoermaanden() {
        $jaar = null;
        $result = $this->sut->zoek_afvoermaanden(self::LIDID, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_zoek_dekjaar() {
        $jaar = null;
        $result = $this->sut->zoek_dekjaar(self::LIDID, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_kzlJaar() {
        $result = $this->sut->kzlJaar(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_eerste_datum_week1() {
        $jaar = null;
        $result = $this->sut->zoek_eerste_datum_week1(self::LIDID, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_zoek_dekmaanden() {
        $jaar = null;
        $result = $this->sut->zoek_dekmaanden(self::LIDID, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_zoek_dekweken() {
        $jaar = null;
        $maand = null;
        $result = $this->sut->zoek_dekweken(self::LIDID, $jaar, $maand);
        $this->assertNotFalse($result);
    }

    public function test_zoek_prognose_weken() {
        $jaar = null;
        $maandag = null;
        $result = $this->sut->zoek_prognose_weken(self::LIDID, $jaar, $maandag);
        $this->assertNotFalse($result);
    }

    public function test_zoek_realisatie_weken() {
        $jaar = null;
        $maandag = null;
        $result = $this->sut->zoek_realisatie_weken(self::LIDID, $jaar, $maandag);
        $this->assertNotFalse($result);
    }

    public function test_zoek_aantal_dekkingen_per_week() {
        $jaarweek = null;
        $result = $this->sut->zoek_aantal_dekkingen_per_week(self::LIDID, $jaarweek);
        $this->assertNotFalse($result);
    }

    public function test_zoek_maandtotalen_prognose() {
        $jaar = null;
        $maand = null;
        $result = $this->sut->zoek_maandtotalen_prognose(self::LIDID, $jaar, $maand);
        $this->assertNotFalse($result);
    }

}
