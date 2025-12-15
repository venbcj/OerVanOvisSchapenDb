<?php

class MeldAanvoerTest extends IntegrationCase {

    public function testGet() {
        $this->runfixture('aanvoer');
        $this->runfixture('versie-1');
        $this->get('/MeldAanvoer.php', [
            'ingelogd' => 1,
        ]);
        $this->assertNoNoise();
    }

    # case voor zoek_controle_melding && aantal_melden
}
