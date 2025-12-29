<?php

class PartijGatewayTest extends GatewayCase {

    protected static $sutname = 'PartijGateway';

    public function test_findLeverancier() {
        $lidId = null;
        $result = $this->sut->findLeverancier($lidId);
        $this->assertNotFalse($result);
    }

    public function test_findKlant() {
        $lidId = null;
        $result = $this->sut->findKlant($lidId);
        $this->assertNotFalse($result);
    }

    public function test_relatienummers() {
        $lidId = null;
        $result = $this->sut->relatienummers($lidId);
        $this->assertNotFalse($result);
    }

    public function test_find_relatie() {
        $lidId = null;
        $result = $this->sut->find_relatie($lidId);
        $this->assertNotFalse($result);
    }

    public function test_findNaam() {
        $partId = null;
        $result = $this->sut->findNaam($partId);
        $this->assertNotFalse($result);
    }

}
