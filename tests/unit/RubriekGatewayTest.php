<?php

class RubriekGatewayTest extends GatewayCase {

    protected static $sutname = 'RubriekGateway';

    public function test_zoekHoofdrubriek() {
        $lidId = null;
        $result = $this->sut->zoekHoofdrubriek($lidId);
        $this->assertNotFalse($result);
    }

    public function test_zoekHoofdrubriekSal() {
        $lidId = null;
        $result = $this->sut->zoekHoofdrubriekSal($lidId);
        $this->assertNotFalse($result);
    }

    public function test_zoekRubriek() {
        $lidId = null;
        $rubhId = null;
        $jaar = null;
        $result = $this->sut->zoekRubriek($lidId, $rubhId, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_zoek_rubriek_simpel() {
        $lidId = null;
        $rubhId = null;
        $result = $this->sut->zoek_rubriek_simpel($lidId, $rubhId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_hoofdrubriek_6() {
        $lidId = null;
        $result = $this->sut->zoek_hoofdrubriek_6($lidId);
        $this->assertNotFalse($result);
    }

    public function test_update() {
        $recId = null;
        $fldActief = null;
        $fldSalber = null;
        $result = $this->sut->update($recId, $fldActief, $fldSalber);
        $this->assertNotFalse($result);
    }

    public function test_find() {
        $lidId = null;
        $maand = null;
        $result = $this->sut->find($lidId, $maand);
        $this->assertNotFalse($result);
    }

    public function test_hoofdrubriek() {
        $lidId = null;
        $result = $this->sut->hoofdrubriek($lidId);
        $this->assertNotFalse($result);
    }

    public function test_rubriek() {
        $lidId = null;
        $rubhId = null;
        $result = $this->sut->rubriek($lidId, $rubhId);
        $this->assertNotFalse($result);
    }

    public function test_kzlSubrubriek() {
        $lidId = null;
        $result = $this->sut->kzlSubrubriek($lidId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_rubuId() {
        $lidId = null;
        $result = $this->sut->zoek_rubuId($lidId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_rubriek_verkooplammeren() {
        $lidId = null;
        $result = $this->sut->zoek_rubriek_verkooplammeren($lidId);
        $this->assertNotFalse($result);
    }

    public function test_aantal_inactief() {
        $lidId = null;
        $result = $this->sut->aantal_inactief($lidId);
        $this->assertNotFalse($result);
    }

    public function test_inactieve_hoofdrubrieken() {
        $lidId = null;
        $result = $this->sut->inactieve_hoofdrubrieken($lidId);
        $this->assertNotFalse($result);
    }

    public function test_inactieve_rubrieken() {
        $lidId = null;
        $rubhId = null;
        $result = $this->sut->inactieve_rubrieken($lidId, $rubhId);
        $this->assertNotFalse($result);
    }

}
