<?php

class RelatieGatewayTest extends GatewayCase {

    protected static $sutname = 'RelatieGateway';

    public function test_zoek_bestemming() {
        $last_stalId = null;
        $result = $this->sut->zoek_bestemming($last_stalId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_crediteur() {
        $partId = null;
        $result = $this->sut->zoek_crediteur($partId);
        $this->assertNotFalse($result);
    }

}
