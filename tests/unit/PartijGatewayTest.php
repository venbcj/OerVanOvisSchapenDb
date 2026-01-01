<?php

class PartijGatewayTest extends GatewayCase {

    protected static $sutname = 'PartijGateway';

    public function test_findLeverancier() {
        $result = $this->sut->findLeverancier(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_findKlant() {
        $result = $this->sut->findKlant(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_relatienummers() {
        $result = $this->sut->relatienummers(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_find_relatie() {
        $result = $this->sut->find_relatie(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_findNaam() {
        $partId = null;
        $result = $this->sut->findNaam($partId);
        $this->assertNotFalse($result);
    }

}
