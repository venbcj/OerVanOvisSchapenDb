<?php

class OoilamSelectieTest extends IntegrationCase {

    public function test_get() {
        $this->get('/OoilamSelectie.php', [
            'ingelogd' => 1,
        ]);
        $this->assertNoNoise();
    }

    public function test_post_stuur() {
        // todo fixture waarmee zoek_dieren data oplevert
        $this->post('/OoilamSelectie.php', [
            'ingelogd_' => 1,
            'knpStuur_' => 1,
            'txtWorpVan_' => '2010-01-01',
            'txtWorpTot_' => '2010-01-01',
            'check_1' => 1,
        ]);
        $this->assertNoNoise();
    }

    public function test_post_zoek() {
        // todo fixture waarmee toon_meerlingen data oplevert
        global $schaap_gateway;
        $schaap_gateway = new SchaapGatewayStub();
        $schaap_gateway->prime('toon_meerlingen', [
            ['aantal' => 4, 'worp' => 4]
        ]);
        $this->post('/OoilamSelectie.php', [
            'ingelogd_' => 1,
            'knpZoek_' => 1,
            'txtWorpVan_' => '2000-01-01',
            'txtWorpTot_' => '2020-01-01',
        ]);
        $this->assertNoNoise();
    }

    public function test_geen_rechten() {
        $this->runSQL("UPDATE tblLeden SET tech=0");
        $this->get('/OoilamSelectie.php', [
            'ingelogd' => 1,
        ]);
        $this->assertPresent('<img src="ooikaart_php');
    }

}
