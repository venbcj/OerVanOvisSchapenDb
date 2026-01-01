<?php

class NuttigGatewayTest extends GatewayCase {

    protected static $sutname = 'NuttigGateway';

    public function test_nuttig_pil() {
        $hisId = null;
        $inkId = 1;
        $stdat = null;
        $reduId = null;
        $aantal = null;
        $result = $this->sut->nuttig_pil($hisId, $inkId, $stdat, $reduId, $aantal);
        $this->assertNotFalse($result);
    }

    public function test_periode_medicijnen() {
        $maand = null;
        $jaar = null;
        $artId = null;
        $Karwerk = 5;
        $result = $this->sut->periode_medicijnen(self::LIDID, $maand, $jaar, $artId, $Karwerk);
        $this->assertNotFalse($result);
    }

}
