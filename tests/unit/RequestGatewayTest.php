<?php

class RequestGatewayTest extends UnitCase {

    public function setup() : void {
        $this->uses_db();
        $this->sut = new RequestGateway($this->db);
    }

    public function test_find_nothing() {
        // ja maar dit moet niet zo blijven. Liskov draait in haar graf man. Geef een leeg record terug.
        $this->assertNull($this->sut->find(0));
    }

    public function test_find_record() {
        $this->runfixture('request-gw-1');
        $actual = $this->sut->find(1);
        $this->assertEquals('v42', $actual['code']);
    }

    public function test_set_def() {
        $this->runfixture('request-gw-not-def');
        $this->sut->setDef(1, 3); // TODO pk nog voorop zetten
        $this->assertTableWithPK('tblRequest', 'reqId', 3, ['def' => 1]);
        $this->assertTableWithPK('tblRequest', 'reqId', 4, ['def' => 0]);
    }

    public function test_countpercode() {
        $this->runfixture('request-gw-lid');
        $actual = $this->sut->countPerCode(1, 'v42');
        $this->assertEquals(1, $actual);
    }

}
