<?php

class UbnGatewayTest extends GatewayCase {

    protected static $sutname = 'UbnGateway';

    public function test_exists_not_when_no_data() {
        $ubn = null;
        $result = $this->sut->exists($ubn);
        $this->assertFalse($result);
    }

    public function test_exists() {
        $ubn = '63'; // ik had eerst 99 maar die bestaat "soms" niet.
        $result = $this->sut->exists($ubn);
        $this->assertTrue($result);
    }

    public function test_not_exists_for_lid() {
        $ubn = '99';
        $result = $this->sut->exists_for_lid($ubn, self::LIDID);
        $this->assertFalse($result);
    }

    public function test_exists_for_lid() {
        $ubn = '63';
        $result = $this->sut->exists_for_lid($ubn, self::LIDID);
        $this->assertTrue($result);
    }

    public function test_insert() {
        $ubn = 1;
        $result = $this->sut->insert(self::LIDID, $ubn);
        $this->assertNotFalse($result);
    }

    public function test_insert_with_plaats() {
        $new_ubn = 1;
        $new_adres = null;
        $new_plaats = null;
        $result = $this->sut->insert_with_plaats(self::LIDID, $new_ubn, $new_adres, $new_plaats);
        $this->assertNotFalse($result);
    }

    public function test_zoek_met_plaats() {
        $result = $this->sut->zoek_met_plaats(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_op_id_met_plaats() {
        $ubnId = null;
        $result = $this->sut->zoek_op_id_met_plaats($ubnId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_relatie() {
        $ubnId = null;
        $result = $this->sut->zoek_relatie($ubnId);
        $this->assertNotFalse($result);
    }

    public function test_delete_by_id() {
        $ubnId = null;
        $result = $this->sut->delete_by_id($ubnId);
        $this->assertNotFalse($result);
    }

    public function test_update_adres() {
        $ubnId = null;
        $adres = null;
        $result = $this->sut->update_adres($ubnId, $adres);
        $this->assertNotFalse($result);
    }

    public function test_update_plaats() {
        $ubnId = null;
        $plaats = null;
        $result = $this->sut->update_plaats($ubnId, $plaats);
        $this->assertNotFalse($result);
    }

    public function test_update_actief() {
        $ubnId = null;
        $actief = null;
        $result = $this->sut->update_actief($ubnId, $actief);
        $this->assertNotFalse($result);
    }

    public function test_lijst() {
        $result = $this->sut->lijst(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_lijstKV() {
        $result = $this->sut->lijstKV(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_countPerLid() {
        $result = $this->sut->countPerLid(self::LIDID);
        $this->assertNotFalse($result);
    }

}
