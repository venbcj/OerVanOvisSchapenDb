<?php

class BezetGatewayTest extends GatewayCase {

    protected static $sutname = 'BezetGateway';

    public function testGeenMoedersInVerblijf() {
        $this->runSQL("DELETE FROM tblBezet");
        $this->assertEquals(0, $this->sut->zoek_moeders_in_verblijf(1)->num_rows);
    }

    public function testMoedersInVerblijf() {
        $this->runfixture('schaap-4');
        $this->runfixture('moeders-in-verblijf');
        $this->assertEquals(1, $this->sut->zoek_moeders_in_verblijf(1)->num_rows);
    }

}
