<?php

class HokSpenenTest extends IntegrationCase {

    public function test_get() {
        $this->runfixture('bezet-na-spenen');
        $this->runfixture('hok');
        $this->get('/HokSpenen.php', [
            'ingelogd' => 1,
            'pstId' => 1,
        ]);
        $this->assertNoNoise();
    }

    public function test_post() {
        $this->runfixture('schaap-4');
        $this->post('/HokSpenen.php', [
            'ingelogd_' => 1,
            'knpSave_' => 1,
            'chbkies_4' => 1,
            'txtDatum_4' => '01-02-2020',
            'txtKg_4' => 2,
        ]);
        $this->assertNoNoise();
    }

}
