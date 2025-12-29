<?php

class DoelGatewayTest extends GatewayCase {

    public static $sutname = 'DoelGateway';

    public function test_zoek_doel() {
        $doelId = null;
        $result = $this->sut->zoek_doel($doelId);
        $this->assertNotFalse($result);
    }

}
