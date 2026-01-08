<?php

class OmnSchaapTest extends IntegrationCase {

    public function test_get() {
        $this->get('/OmnSchaap.php', [
            'ingelogd' => 1,
        ]);
        $this->assertNoNoise();
    }

    // onduidelijk wat dit nu uitmaakt
    public function test_get_met_parameter() {
        $this->runfixture('schaap-4');
        $this->get('/OmnSchaap.php', [
            'ingelogd' => 1,
            'pstschaap' => 4,
        ]);
        $this->assertNoNoise();
    }

    public function test_post() {
        $this->runfixture('schaap-4');
        $this->post('/OmnSchaap.php', [
            'pstschaap_4' => 4, // code rekent op volgorde van velden in POST ...
            'knpSave_4' => 4,
            'ingelogd_' => 1,
            'txtLevnrNew' => '57',
            'txtDag' => '2010-01-01',
        ]);
        $this->assertNoNoise();
    }

}
