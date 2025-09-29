<?php

class BezetGatewayTest extends UnitCase {

    protected $sut;

    public function setup(): void {
        $this->uses_db();
        $this->sut = new BezetGateway($this->db);
    }

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
