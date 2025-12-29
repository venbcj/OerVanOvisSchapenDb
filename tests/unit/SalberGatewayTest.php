<?php

class SalberGatewayTest extends GatewayCase {

    protected static $sutname = 'SalberGateway';

    public function test_zoek_jaar() {
        $result = $this->sut->zoek_jaar(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_insertJaar() {
        $nextjaar = null;
        $result = $this->sut->insertJaar(self::LIDID, $nextjaar);
        $this->assertNotFalse($result);
    }

    public function test_countGeborenInJaar() {
        $jaar = null;
        $result = $this->sut->countGeborenInJaar(self::LIDID, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_jaren() {
        $result = $this->sut->jaren(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_rekencomponenten() {
        $jaar = null;
        $result = $this->sut->zoek_rekencomponenten(self::LIDID, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_zoek_element_vervanging_ooi() {
        $jaar = null;
        $result = $this->sut->zoek_element_vervanging_ooi(self::LIDID, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_zoek_element() {
        $jaar = null;
        $result = $this->sut->zoek_element(self::LIDID, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_jaarbasis() {
        $kzlJaar = null;
        $p_ooital = null;
        $p_afv = null;
        $verv_ooi = null;
        $result = $this->sut->jaarbasis(self::LIDID, $kzlJaar, $p_ooital, $p_afv, $verv_ooi);
        $this->assertNotFalse($result);
    }

    public function test_update() {
        $recId = null;
        $waarde = null;
        $result = $this->sut->update($recId, $waarde);
        $this->assertNotFalse($result);
    }

}
