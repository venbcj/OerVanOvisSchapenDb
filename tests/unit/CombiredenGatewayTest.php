<?php

class CombiredenGatewayTest extends GatewayCase {

    public static $sutname = 'CombiredenGateway';

    public function test_zoek_reden_uitval() {
        $result = $this->sut->zoek_reden_uitval(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_reden_medicijn() {
        $result = $this->sut->zoek_reden_medicijn(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_bestaat_reden() {
        $whereArtId = null;
        $whereStdat = null;
        $whereRed = null;
        $fldTbl = null;
        $result = $this->sut->bestaat_reden($whereArtId, $whereStdat, $whereRed, $fldTbl);
        $this->assertNotFalse($result);
    }

    public function test_bestaat_scannr() {
        $whereScan = null;
        $fldTbl = null;
        $result = $this->sut->bestaat_scannr(self::LIDID, $whereScan, $fldTbl);
        $this->assertNotFalse($result);
    }

    public function test_insert() {
        $fldTbl = null;
        $insArtId = null;
        $insStdat = null;
        $insRed = null;
        $insScan = null;
        $result = $this->sut->insert($fldTbl, $insArtId, $insStdat, $insRed, $insScan);
        $this->assertNotFalse($result);
    }

    public function test_zoek_reden_uitval_combi() {
        $result = $this->sut->zoek_reden_uitval_combi(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_find() {
        $comrId = null;
        $result = $this->sut->find($comrId);
        $this->assertNotFalse($result);
    }

    public function test_bestaat_combireden2() {
        $whereRed = null;
        $rowid_d = null;
        $result = $this->sut->bestaat_combireden2(self::LIDID, $whereRed, $rowid_d);
        $this->assertNotFalse($result);
    }

    public function test_bestaat_scannr2() {
        $whereScan = null;
        $rowid_d = null;
        $result = $this->sut->bestaat_scannr2(self::LIDID, $whereScan, $rowid_d);
        $this->assertNotFalse($result);
    }

    public function test_update() {
        $rowid_d = null;
        $fldScan = null;
        $fldReden = null;
        $result = $this->sut->update($rowid_d, $fldScan, $fldReden);
        $this->assertNotFalse($result);
    }

    public function test_update2() {
        $rowid_p = null;
        $fldScan = null;
        $fldArtId = null;
        $fldStdat = null;
        $fldReden = null;
        $result = $this->sut->update2($rowid_p, $fldScan, $fldArtId, $fldStdat, $fldReden);
        $this->assertNotFalse($result);
    }

    public function test_delete() {
        $comrId = null;
        $result = $this->sut->delete($comrId);
        $this->assertNotFalse($result);
    }

    public function test_p_list_for() {
        $result = $this->sut->p_list_for(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_c_list_for() {
        $comrId = null;
        $result = $this->sut->c_list_for(self::LIDID, $comrId);
        $this->assertNotFalse($result);
    }

    public function test_bestaat_combireden3() {
        $whereStdat = null;
        $whereRed = null;
        $rowid_p = null;
        $result = $this->sut->bestaat_combireden3(self::LIDID, $whereStdat, $whereRed, $rowid_p);
        $this->assertNotFalse($result);
    }

    public function test_bestaat_scan3() {
        $whereScan = null;
        $comrId = null;
        $result = $this->sut->bestaat_scan3(self::LIDID, $whereScan, $comrId);
        $this->assertNotFalse($result);
    }

}
