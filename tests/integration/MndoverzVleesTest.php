<?php

class MndoverzVleesTest extends IntegrationCase {

    public function testGet() {
        $this->get('/Mndoverz_vlees.php', ['ingelogd' => 1]);
        $this->assertNoNoise();
    }

    public function testFormGet() {
        $this->get('/Mndoverz_vlees.php', [
            'ingelogd' => 1,
            'maand' => 3,
        ]);
        $this->assertNoNoise();
    }

}

