<?php

class AlertGatewayTest extends GatewayCase {

    public static $sutname = 'AlertGateway';

    public function test_all() {
        $this->assertCount(6, $this->sut->all());
    }

    public function test_laatste_selectie() {
        $this->assertEquals(0, $this->sut->laatste_selectie(self::LIDID));
    }

}
