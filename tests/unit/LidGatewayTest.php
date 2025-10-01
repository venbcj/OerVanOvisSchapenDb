<?php

class LidGatewayTest extends GatewayCase {

    protected static $sutname = 'LidGateway';

    public function testFindCrediteur() {
        $this->runfixture('crediteur');
        $res = $this->sut->findCrediteur(self::LIDID);
        $this->assertEquals([4, 13], $res);
    }

}
