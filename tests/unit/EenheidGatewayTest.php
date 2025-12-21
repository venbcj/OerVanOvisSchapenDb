<?php

class EenheidGatewayTest extends GatewayCase {

    public static $sutname = 'EenheidGateway';

    public function test_findbylid() {
        $actual = $this->sut->findByLid(self::LIDID);
        $this->assertEquals(4, $actual->num_rows); // kennelijk zitten er 4 eenheden bij de gebruiker, in de standaard-fixture
    }

}
