<?php

class ActieGatewayTest extends GatewayCase {

    protected static $sutname = 'ActieGateway';

    // @FRAGILE
    // rust op database-vulling
    public function test_getList() {
        $this->assertCount(22, $this->sut->getList());
    }

    public function test_getListOp1() {
        $this->assertCount(3, $this->sut->getListOp1());
    }

}
