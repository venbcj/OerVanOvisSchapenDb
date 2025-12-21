<?php

class PeriodeGatewayTest extends GatewayCase {

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

    // uit phpunit seed 1765720329 blijkt dat dit een vals-positief is.
    // Alleen de fixture jaarmaanden-n is NIET GENOEG.
    public function testMeerJaarmaanden() {
        $this->runfixture('voervoorraad');
        $this->runfixture('jaarmaanden-n');
        $artId = 1;
        $doelId = 1;
        $actual = $this->sut->aantal_jaarmaanden(self::LIDID, $artId, $doelId);
        $this->assertEquals(2, $actual);
    }

}
