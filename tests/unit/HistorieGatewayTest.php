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

    public function test_zoek_eerste_datum_stalop_leeg() {
        $this->runSQL("TRUNCATE tblMelding");
        $this->runSQL("TRUNCATE tblHistorie");
        $this->runSQL("TRUNCATE tblStal");
        $actual = $this->sut->zoek_eerste_datum_stalop(1);
        $this->assertEquals([null, null], $actual);
    }

    public function test_zoek_eerste_datum_stalop_data() {
        $this->runSQL("TRUNCATE tblMelding");
        $this->runSQL("TRUNCATE tblHistorie");
        $this->runSQL("TRUNCATE tblStal");
        $this->runSQL("INSERT INTO tblStal(stalId, schaapId) VALUES(1,1)");
        $this->runSQL("INSERT INTO tblMelding(meldId, hisId) VALUES(1,2)");
        $this->runSQL("INSERT INTO tblHistorie(hisId, actId, stalId, skip, datum) VALUES(1, 1, 1, 0, '2010-01-01')");
        $this->runSQL("INSERT INTO tblHistorie(hisId, actId, stalId, skip, datum) VALUES(2, 1, 1, 0, '2010-01-01')");
        $actual = $this->sut->zoek_eerste_datum_stalop(1);
        $this->assertEquals(['2010-01-01', '01-01-2010'], $actual);
    }

    public function test_setDatum() {
        $this->runSQL("TRUNCATE tblHistorie");
        $this->runSQL("TRUNCATE tblMelding");
        $this->runSQL("INSERT INTO tblHistorie(hisId, actId, stalId, skip, datum) VALUES(2, 1, 1, 0, '2010-01-01')");
        $this->runSQL("INSERT INTO tblMelding(meldId, hisId) VALUES(1,2)");
        $this->sut->setDatum('2020-02-02', 1);
        // THEN
        // moet je wel snappen dat record "2" in tblHistorie wordt gewijzigd.
        $this->assertTableWithPK('tblHistorie', 'hisId', 2, ['datum' => '2020-02-02']);
    }

    public function test_zoek_dekdatum_leeg() {
        $this->runSQL("TRUNCATE tblHistorie");
        $this->assertEquals([null, null], $this->sut->zoek_dekdatum(1));
    }

    public function test_zoek_dekdatum_data() {
        $this->runSQL("TRUNCATE tblHistorie");
        $this->runSQL("INSERT INTO tblHistorie(hisId, actId, stalId, skip, datum) VALUES(2, 1, 1, 0, '2010-01-01')");
        $this->assertEquals(['01-01-2010', 2010], $this->sut->zoek_dekdatum(2));
    }

}
