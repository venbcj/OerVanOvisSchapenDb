<?php

class VolwasGatewayTest extends GatewayCase {

    protected static $sutname = 'VolwasGateway';

    public function testGeenDekkingen() {
        $Karwerk = 5;
        $jaar = 2000;
        $actual = $this->sut->zoek_dekkingen(self::LIDID, $Karwerk, $jaar);
        $this->assertEquals(0, $actual->num_rows);
    }

    public function testZoekDekkingen() {
        $this->markTestIncomplete('schrijf de fixture "dekkingen"');
        $this->runfixture('schaap-4');
        $this->runfixture('dekkingen'); // Slechte naam; er is ook al eentje 'dekking'
        $Karwerk = 5;
        $jaar = 2000;
        $actual = $this->sut->zoek_dekkingen(self::LIDID, $Karwerk, $jaar);
        $this->assertEquals(1, $actual->num_rows);
    }

}
