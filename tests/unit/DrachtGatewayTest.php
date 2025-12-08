<?php

class DrachtGatewayTest extends GatewayCase {

    protected static $sutname = 'DrachtGateway';

    public function test_insert() {
        $this->expectNewRecordsInTables([
            'tblDracht' => 1,
        ]);
        $this->sut->insert_dracht(1, 1);
        $this->assertTablesGrew();
    }

}
