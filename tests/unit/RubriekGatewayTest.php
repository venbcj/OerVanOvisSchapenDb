<?php

class RubriekGatewayTest extends GatewayCase {

    protected static $sutname = 'RubriekGateway';

    public function test_zoekHoofdrubriek() {
        $result = $this->sut->zoekHoofdrubriek(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoekHoofdrubriekSal() {
        $result = $this->sut->zoekHoofdrubriekSal(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoekRubriek() {
        $rubhId = null;
        $jaar = null;
        $result = $this->sut->zoekRubriek(self::LIDID, $rubhId, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_zoek_rubriek_simpel() {
        $rubhId = null;
        $result = $this->sut->zoek_rubriek_simpel(self::LIDID, $rubhId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_hoofdrubriek_6() {
        $result = $this->sut->zoek_hoofdrubriek_6(self::LIDID);
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
        $maand = null;
        $result = $this->sut->find(self::LIDID, $maand);
        $this->assertNotFalse($result);
    }

    public function test_hoofdrubriek() {
        $result = $this->sut->hoofdrubriek(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_rubriek() {
        $rubhId = null;
        $result = $this->sut->rubriek(self::LIDID, $rubhId);
        $this->assertNotFalse($result);
    }

    public function test_kzlSubrubriek() {
        $result = $this->sut->kzlSubrubriek(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_rubuId() {
        $result = $this->sut->zoek_rubuId(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_rubriek_verkooplammeren() {
        $result = $this->sut->zoek_rubriek_verkooplammeren(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_aantal_inactief() {
        $result = $this->sut->aantal_inactief(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_inactieve_hoofdrubrieken() {
        $result = $this->sut->inactieve_hoofdrubrieken(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_inactieve_rubrieken() {
        $rubhId = null;
        $result = $this->sut->inactieve_rubrieken(self::LIDID, $rubhId);
        $this->assertNotFalse($result);
    }

}
