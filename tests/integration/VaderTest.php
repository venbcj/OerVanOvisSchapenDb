<?php

class VaderTest extends IntegrationCase {

    public function testList() {
        $this->runfixture('vaders');
        $this->get('/Vader.php', ['ingelogd' => 1]);
        $this->assertNoNoise();
        $this->assertPresent('tr class="schaap"');
    }

}
