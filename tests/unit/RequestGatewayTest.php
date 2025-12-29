<?php

class RequestGatewayTest extends GatewayCase {

    protected static $sutname = 'RequestGateway';

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
        $this->sut->setDef(3, 1);
        $this->assertTableWithPK('tblRequest', 'reqId', 3, ['def' => 1]);
        $this->assertTableWithPK('tblRequest', 'reqId', 4, ['def' => 0]);
    }

    public function test_countpercode() {
        $this->runfixture('request-gw-lid');
        $actual = $this->sut->countPerCode(self::LIDID, 'v42');
        $this->assertEquals(1, $actual);
    }


    // *************************************

    public function test_find() {
        $recId = null;
        $result = $this->sut->find($recId);
        $this->assertNotFalse($result);
    }

    public function test_setDef() {
        $reqId = null;
        $def = null;
        $result = $this->sut->setDef($reqId, $def);
        $this->assertNotFalse($result);
    }

    public function test_zoekLaatsteResponse() {
        $result = $this->sut->zoekLaatsteResponse(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_definitieve_afvoermelding() {
        $stalId = null;
        $result = $this->sut->zoek_definitieve_afvoermelding($stalId);
        $this->assertNotFalse($result);
    }

    public function test_hasNoOpenRequestsWhenNoData() {
        $result = $this->sut->hasOpenRequests(self::LIDID);
        $this->assertFalse($result);
    }

    public function test_hasOpenRequests() {
        $this->runfixture('request-lid-codes');
        $result = $this->sut->hasOpenRequests(self::LIDID);
        $this->assertTrue($result);
    }

    public function test_zoek_open_request() {
        $code = null;
        $result = $this->sut->zoek_open_request(self::LIDID, $code);
        $this->assertNotFalse($result);
    }

    public function test_insert() {
        $fldCode = null;
        $result = $this->sut->insert(self::LIDID, $fldCode);
        $this->assertNotFalse($result);
    }

    public function test_update() {
        $reqId = null;
        $result = $this->sut->update($reqId);
        $this->assertNotFalse($result);
    }

    public function test_update_response_date() {
        $reqId = null;
        $dmresponse = null;
        $result = $this->sut->update_response_date($reqId, $dmresponse);
        $this->assertNotFalse($result);
    }

    public function test_findById() {
        $reqId = null;
        $result = $this->sut->findById($reqId);
        $this->assertNotFalse($result);
    }

}
