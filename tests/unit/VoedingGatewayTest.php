<?php

class VoedingGatewayTest extends GatewayCase {

    protected static $sutname = 'VoedingGateway';

    public function test_inlezen() {
        $periode_id = null;
        $inkId = null;
        $rest_ink_vrd = null;
        $stdat = null;
        $toediendatum = null;
        $readerid = null;
        $result = $this->sut->inlezen($periode_id, $inkId, $rest_ink_vrd, $stdat, $toediendatum, $readerid);
        $this->assertNotFalse($result);
    }

}
