<?php

class RedenGatewayTest extends GatewayCase {

    protected static $sutname = 'RedenGateway';

    public function test_alle_lijst_voor() {
        $result = $this->sut->alle_lijst_voor(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_lijst_voor() {
        $result = $this->sut->lijst_voor(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_uitval_lijst_voor() {
        $result = $this->sut->uitval_lijst_voor(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_KV_uitval_lijst_voor() {
        $result = $this->sut->KV_uitval_lijst_voor(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_afvoer_lijst_voor() {
        $result = $this->sut->afvoer_lijst_voor(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_pil_lijst_voor() {
        $result = $this->sut->pil_lijst_voor(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_kzlReden() {
        $result = $this->sut->kzlReden(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_reden_actief() {
        $reduId = null;
        $result = $this->sut->zoek_reden_actief(self::LIDID, $reduId);
        $this->assertNotFalse($result);
    }

    public function test_pil_actief() {
        $reduId = null;
        $result = $this->sut->pil_actief(self::LIDID, $reduId);
        $this->assertNotFalse($result);
    }

    public function test_kzlReden_combi() {
        $reduId = null;
        $result = $this->sut->kzlReden_combi(self::LIDID, $reduId);
        $this->assertNotFalse($result);
    }

    public function test_reden_actief() {
        $reduId = null;
        $result = $this->sut->reden_actief(self::LIDID, $reduId);
        $this->assertNotFalse($result);
    }

}
