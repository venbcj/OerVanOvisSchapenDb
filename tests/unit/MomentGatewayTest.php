<?php

class MomentGatewayTest extends GatewayCase {

    protected static $sutname = 'MomentGateway';

    public function test_kzlMoment() {
        $result = $this->sut->kzlMoment(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_moment_invschaap() {
        $result = $this->sut->moment_invschaap(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_kzlMoment_invschaap() {
        $result = $this->sut->kzlMoment_invschaap(self::LIDID);
        $this->assertNotFalse($result);
    }

}
