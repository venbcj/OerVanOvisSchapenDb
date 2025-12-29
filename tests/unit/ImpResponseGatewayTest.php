<?php

class ImpResponseGatewayTest extends GatewayCase {

    protected static $sutname = 'ImpResponseGateway';

    public function test_updateLevensnummer() {
        $from = null;
        $to = null;
        $result = $this->sut->updateLevensnummer($from, $to);
        $this->assertNotFalse($result);
    }

    public function test_zoek_status_response() {
        $reqId = null;
        $result = $this->sut->zoek_status_response($reqId);
        $this->assertNotFalse($result);
    }

}
