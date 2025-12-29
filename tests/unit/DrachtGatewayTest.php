<?php

class DrachtGatewayTest extends GatewayCase {

    public static $sutname = 'DrachtGateway';

    public function test_insert_dracht() {
        $volwId = null;
        $hisId = null;
        $result = $this->sut->insert_dracht($volwId, $hisId);
        $this->assertNotFalse($result);
    }

    public function test_insert() {
        $this->expectNewRecordsInTables([
            'tblDracht' => 1,
        ]);
        $this->sut->insert_dracht(1, 1);
        $this->assertTablesGrew();
    }

}
