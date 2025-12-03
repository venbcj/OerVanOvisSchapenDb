<?php

class HistorieGatewayTest extends GatewayCase {

    protected static $sutname = 'HistorieGateway';

    public function test_zoek_einddatum() {
        $this->runSQL("TRUNCATE tblHistorie");
        // actie 12 is afgeleverd
        $this->runSQL("INSERT INTO tblHistorie(actId, stalId, skip, datum) VALUES(12, 1, 0, '2010-01-01')");
        $res = $this->sut->zoek_einddatum(1);
        $this->assertEquals(['2010-01-01', '01-01-2010'], $res);
    }

}
