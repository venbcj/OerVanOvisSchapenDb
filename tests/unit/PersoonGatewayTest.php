<?php

class PersoonGatewayTest extends GatewayCase {

    protected static $sutname = 'PersoonGateway';

    public function test_insert() {
        $partId = null;
        $data = null;
        $result = $this->sut->insert($partId, $data);
        $this->assertNotFalse($result);
    }

    public function test_zoek_bij_partij() {
        $partId = null;
        $result = $this->sut->zoek_bij_partij($partId);
        $this->assertNotFalse($result);
    }

    public function test_find() {
        $id = null;
        $result = $this->sut->find($id);
        $this->assertNotFalse($result);
    }

}
