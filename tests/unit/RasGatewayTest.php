<?php

class RasGatewayTest extends GatewayCase {

    protected static $sutname = 'RasGateway';

    public function test_zoek_ras() {
        $lidId = null;
        $result = $this->sut->zoek_ras($lidId);
        $this->assertNotFalse($result);
    }

    public function test_rassen() {
        $lidId = null;
        $result = $this->sut->rassen($lidId);
        $this->assertNotFalse($result);
    }

    public function test_rassenKV() {
        $lidId = null;
        $result = $this->sut->rassenKV($lidId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_ras_bij() {
        $rasId = null;
        $lidId = null;
        $result = $this->sut->zoek_ras_bij($rasId, $lidId);
        $this->assertNotFalse($result);
    }

    public function test_countScan() {
        $lidId = null;
        $scan = null;
        $result = $this->sut->countScan($lidId, $scan);
        $this->assertNotFalse($result);
    }

    public function test_updateScan() {
        $lidId = null;
        $scan = null;
        $rasId = null;
        $result = $this->sut->updateScan($lidId, $scan, $rasId);
        $this->assertNotFalse($result);
    }

    public function test_set_actief() {
        $rasId = null;
        $actief = null;
        $result = $this->sut->set_actief($rasId, $actief);
        $this->assertNotFalse($result);
    }

}
