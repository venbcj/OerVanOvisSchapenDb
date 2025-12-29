<?php

class ImpReaderGatewayTest extends GatewayCase {

    protected static $sutname = 'ImpReaderGateway';

    public function test_zoek_readerregel_verwerkt() {
        $readId = null;
        $result = $this->sut->zoek_readerregel_verwerkt($readId);
        $this->assertNotFalse($result);
    }

}
