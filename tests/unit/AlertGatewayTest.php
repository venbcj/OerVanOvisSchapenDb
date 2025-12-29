<?php

class AlertGatewayTest extends GatewayCase {

    public static $sutname = 'AlertGateway';

    public function test_all() {
        $this->assertCount(6, $this->sut->all());
    }

    public function test_laatste_selectie() {
        $this->assertEquals(0, $this->sut->laatste_selectie(self::LIDID));
    }

    public function test_transponders() {
        $volgnr = null;
        $result = $this->sut->transponders($volgnr);
        $this->assertNotFalse($result);
    }

    public function test_insert() {
        $this->expectNewRecordsInTables(['tblAlertselectie' => 1]);
        $volgnr = 1;
        $transponder = 'test';
        $recId = 1;
        $this->sut->insert($volgnr, self::LIDID, $transponder, $recId);
        $this->assertTablesGrew();
    }

    public function test_zoek_aantal_selectie() {
        $volgnr = null;
        $result = $this->sut->zoek_aantal_selectie($volgnr);
        $this->assertEquals(0, $result);
    }

}
