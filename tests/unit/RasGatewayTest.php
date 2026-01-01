<?php

class RasGatewayTest extends GatewayCase {

    protected static $sutname = 'RasGateway';

    public function test_zoek_ras() {
        $result = $this->sut->zoek_ras(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_rassen() {
        $result = $this->sut->rassen(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_rassenKV() {
        $result = $this->sut->rassenKV(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_ras_bij() {
        $rasId = null;
        $result = $this->sut->zoek_ras_bij($rasId, self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_countScan() {
        $scan = null;
        $result = $this->sut->countScan(self::LIDID, $scan);
        $this->assertNotFalse($result);
    }

    public function test_updateScan() {
        $scan = null;
        $rasId = null;
        $result = $this->sut->updateScan(self::LIDID, $scan, $rasId);
        $this->assertNotFalse($result);
    }

    public function test_set_actief() {
        $rasId = null;
        $actief = null;
        $result = $this->sut->set_actief($rasId, $actief);
        $this->assertNotFalse($result);
    }

}
