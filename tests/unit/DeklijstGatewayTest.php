<?php

class DeklijstGatewayTest extends GatewayCase {

    public static $sutname = 'DeklijstGateway';

    public function test_insert() {
        $lidId = null;
        $datum = null;
        $result = $this->sut->insert($lidId, $datum);
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
        $lidId = null;
        $result = $this->sut->zoek_laatste_jaar($lidId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_max_dekjaar() {
        $lidId = null;
        $jaar = null;
        $result = $this->sut->zoek_max_dekjaar($lidId, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_zoek_afvoermaanden() {
        $lidId = null;
        $jaar = null;
        $result = $this->sut->zoek_afvoermaanden($lidId, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_zoek_dekjaar() {
        $lidId = null;
        $jaar = null;
        $result = $this->sut->zoek_dekjaar($lidId, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_kzlJaar() {
        $lidId = null;
        $result = $this->sut->kzlJaar($lidId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_eerste_datum_week1() {
        $lidId = null;
        $jaar = null;
        $result = $this->sut->zoek_eerste_datum_week1($lidId, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_zoek_dekmaanden() {
        $lidId = null;
        $jaar = null;
        $result = $this->sut->zoek_dekmaanden($lidId, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_zoek_dekweken() {
        $lidId = null;
        $jaar = null;
        $maand = null;
        $result = $this->sut->zoek_dekweken($lidId, $jaar, $maand);
        $this->assertNotFalse($result);
    }

    public function test_zoek_prognose_weken() {
        $lidId = null;
        $jaar = null;
        $maandag = null;
        $result = $this->sut->zoek_prognose_weken($lidId, $jaar, $maandag);
        $this->assertNotFalse($result);
    }

    public function test_zoek_realisatie_weken() {
        $lidId = null;
        $jaar = null;
        $maandag = null;
        $result = $this->sut->zoek_realisatie_weken($lidId, $jaar, $maandag);
        $this->assertNotFalse($result);
    }

    public function test_zoek_aantal_dekkingen_per_week() {
        $lidId = null;
        $jaarweek = null;
        $result = $this->sut->zoek_aantal_dekkingen_per_week($lidId, $jaarweek);
        $this->assertNotFalse($result);
    }

    public function test_zoek_maandtotalen_prognose() {
        $lidId = null;
        $jaar = null;
        $maand = null;
        $result = $this->sut->zoek_maandtotalen_prognose($lidId, $jaar, $maand);
        $this->assertNotFalse($result);
    }

}
