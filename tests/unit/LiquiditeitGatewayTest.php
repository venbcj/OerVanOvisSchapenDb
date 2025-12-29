<?php

class LiquiditeitGatewayTest extends GatewayCase {

    protected static $sutname = 'LiquiditeitGateway';

    public function test_zoek_jaar() {
        $year = null;
        $result = $this->sut->zoek_jaar(self::LIDID, $year);
        $this->assertNotFalse($result);
    }

    public function test_zoek_bedrag() {
        $rubuId = null;
        $maand = null;
        $jaar = null;
        $result = $this->sut->zoek_bedrag($rubuId, $maand, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_update_bedrag() {
        $bedrag = null;
        $rubuId = null;
        $maand = null;
        $jaar = null;
        $result = $this->sut->update_bedrag($bedrag, $rubuId, $maand, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_update_datum_bedrag() {
        $day = null;
        $bedrag = null;
        $result = $this->sut->update_datum_bedrag(self::LIDID, $day, $bedrag);
        $this->assertNotFalse($result);
    }

    public function test_insert() {
        $rub_user = null;
        $datum = null;
        $result = $this->sut->insert($rub_user, $datum);
        $this->assertNotFalse($result);
    }

    public function test_laatste_jaar() {
        $result = $this->sut->laatste_jaar(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_jaren() {
        $result = $this->sut->jaren(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_realiteit() {
        $rubuId = null;
        $jaar = null;
        $maand = null;
        $result = $this->sut->zoek_realiteit($rubuId, $jaar, $maand);
        $this->assertNotFalse($result);
    }

    public function test_zoek_begroting() {
        $rubuId = null;
        $jaar = null;
        $maand = null;
        $result = $this->sut->zoek_begroting($rubuId, $jaar, $maand);
        $this->assertNotFalse($result);
    }

    public function test_totaal_maandbedragen() {
        $jaar = null;
        $result = $this->sut->totaal_maandbedragen(self::LIDID, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_cumulatief_maandbedragen() {
        $jaar = null;
        $maand = null;
        $result = $this->sut->cumulatief_maandbedragen(self::LIDID, $jaar, $maand);
        $this->assertNotFalse($result);
    }

    public function test_deklijst_zoek_jaar() {
        $jaar = null;
        $result = $this->sut->deklijst_zoek_jaar(self::LIDID, $jaar);
        $this->assertNotFalse($result);
    }

}
