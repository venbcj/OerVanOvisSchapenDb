<?php

class ElementGatewayTest extends GatewayCase {

    public static $sutname = 'ElementGateway';

    public function test_update() {
        $recId = null;
        $fldWaarde = null;
        $fldActief = null;
        $fldSalber = null;
        $result = $this->sut->update($recId, $fldWaarde, $fldActief, $fldSalber);
        $this->assertNotFalse($result);
    }

    public function test_zoek_prijs_lam() {
        $result = $this->sut->zoek_prijs_lam(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_worpgrootte() {
        $result = $this->sut->zoek_worpgrootte(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_sterfte() {
        $result = $this->sut->zoek_sterfte(self::LIDID);
        $this->assertNotFalse($result);
    }

}
