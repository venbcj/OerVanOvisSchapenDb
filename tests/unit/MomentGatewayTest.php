<?php

class MomentGatewayTest extends GatewayCase {

    protected static $sutname = 'MomentGateway';

    public function test_kzlMoment() {
        $lidId = null;
        $result = $this->sut->kzlMoment($lidId);
        $this->assertNotFalse($result);
    }

    public function test_moment_invschaap() {
        $lidId = null;
        $result = $this->sut->moment_invschaap($lidId);
        $this->assertNotFalse($result);
    }

    public function test_kzlMoment_invschaap() {
        $lidId = null;
        $result = $this->sut->kzlMoment_invschaap($lidId);
        $this->assertNotFalse($result);
    }

}
