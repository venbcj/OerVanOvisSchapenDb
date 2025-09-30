<?php

class PeriodeGatewayTest extends GatewayCase {

    private const LIDID = 1;

    protected static $sutname = 'PeriodeGateway';

    public function testGeenJaarmaanden() {
        $this->runSQL("DELETE FROM tblPeriode");
        $artId = 1;
        $doelId = 1;
        $actual = $this->sut->aantal_jaarmaanden(self::LIDID, $artId, $doelId);
        $this->assertEquals(0, $actual);
    }

    public function testEenJaarmaanden() {
        $this->runfixture('jaarmaanden-1');
        $artId = 1;
        $doelId = 1;
        $actual = $this->sut->aantal_jaarmaanden(self::LIDID, $artId, $doelId);
        $this->assertEquals(1, $actual);
    }

    public function testMeerJaarmaanden() {
        $this->runfixture('jaarmaanden-n');
        $artId = 1;
        $doelId = 1;
        $actual = $this->sut->aantal_jaarmaanden(self::LIDID, $artId, $doelId);
        $this->assertEquals(2, $actual);
    }

}
