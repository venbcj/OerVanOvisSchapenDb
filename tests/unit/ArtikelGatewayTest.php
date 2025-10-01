<?php

class ArtikelGatewayTest extends GatewayCase {

    protected static $sutname = 'ArtikelGateway';

    public function test_voer() {
        $this->runfixture('voervoorraad');
        $res = $this->sut->voer(1);
        $this->assertEquals(1, $res->num_rows);
    }

}
