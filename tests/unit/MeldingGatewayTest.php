<?php

class MeldingGatewayTest extends GatewayCase {

    protected static $sutname = 'MeldingGateway';

    public function test_zoek_bestemming() {
        $recId = null;
        $result = $this->sut->zoek_bestemming($recId);
        $this->assertNotFalse($result);
    }

    public function test_updateSkip() {
        $recId = null;
        $fldSkip = null;
        $result = $this->sut->updateSkip($recId, $fldSkip);
        $this->assertNotFalse($result);
    }

    public function test_updateFout() {
        $recId = null;
        $wrong = null;
        $result = $this->sut->updateFout($recId, $wrong);
        $this->assertNotFalse($result);
    }

    public function test_aantal_oke_Omnum() {
        $fldReqId = null;
        $result = $this->sut->aantal_oke_Omnum($fldReqId);
        $this->assertNotFalse($result);
    }

    public function test_aantal_oke_uitv() {
        $fldReqId = null;
        $nestHistorieDm = null;
        $result = $this->sut->aantal_oke_uitv($fldReqId, $nestHistorieDm);
        $this->assertNotFalse($result);
    }

    public function test_aantal_oke_afv() {
        $reqId = null;
        $nestHistorieDm = null;
        $result = $this->sut->aantal_oke_afv($reqId, $nestHistorieDm);
        $this->assertNotFalse($result);
    }

    public function test_insert() {
        $reqId = null;
        $hisId = null;
        $result = $this->sut->insert($reqId, $hisId);
        $this->assertNotFalse($result);
    }

}
