<?php

class DeklijstTest extends IntegrationCase {

    // @TODO dit "dekt" create_deklijst, maar er mag wel een assert of wat bij
    public function testCreate() {
        $this->post('/Deklijst.php', [
            'ingelogd' => 1,
            'knpCreate_' => 1,
        ]);
        $this->assertNoNoise();
    }

}
