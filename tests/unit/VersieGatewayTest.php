<?php

class VersieGatewayTest extends GatewayCase {

    protected static $sutname = 'VersieGateway';

    public function test_zoek_laatste_versie() {
        
        $result = $this->sut->zoek_laatste_versie();
        $this->assertNotFalse($result);
    }

    public function test_zoek_readersetup_in() {
        $last_versieId = null;
        $result = $this->sut->zoek_readersetup_in($last_versieId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_readertaken_in() {
        $last_versieId = null;
        $result = $this->sut->zoek_readertaken_in($last_versieId);
        $this->assertNotFalse($result);
    }

}
