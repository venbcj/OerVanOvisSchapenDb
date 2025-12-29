<?php

class OpgaafGatewayTest extends GatewayCase {

    protected static $sutname = 'OpgaafGateway';

    public function test_clear_history() {
        $recId = null;
        $result = $this->sut->clear_history($recId);
        $this->assertNotFalse($result);
    }

    public function test_insert() {
        $rubr = null;
        $day = null;
        $bedrag = null;
        $toel = null;
        $insLiq = null;
        $result = $this->sut->insert($rubr, $day, $bedrag, $toel, $insLiq);
        $this->assertNotFalse($result);
    }

    public function test_inboekingen() {
        $result = $this->sut->inboekingen(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_jaaropbrengst() {
        $rubuId = null;
        $jaar = null;
        $result = $this->sut->jaaropbrengst($rubuId, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_zoek_afleverbedrag_per_maand() {
        $rubuId = null;
        $van = null;
        $tot = null;
        $result = $this->sut->zoek_afleverbedrag_per_maand($rubuId, $van, $tot);
        $this->assertNotFalse($result);
    }

}
