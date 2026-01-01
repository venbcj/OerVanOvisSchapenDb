<?php

class VoedingGatewayTest extends GatewayCase {

    protected static $sutname = 'VoedingGateway';

    public function test_inlezen() {
        $periode_id = 1;
        $inkId = 1;
        $rest_ink_vrd = null;
        $stdat = null;
        $toediendatum = null;
        $readerid = 1;
        $result = $this->sut->inlezen($periode_id, $inkId, $rest_ink_vrd, $stdat, $toediendatum, $readerid);
        $this->assertNotFalse($result);
    }

}
