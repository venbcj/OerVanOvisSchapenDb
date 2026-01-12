<?php

class Meerlingen4Test extends IntegrationCase {

    public function test_get() {
        $this->runfixture('meerlingen4');
        $this->get('/Meerlingen4.php', [
            'ingelogd' => 1,
        ]);
        $this->assertNoNoise();
    }

}
