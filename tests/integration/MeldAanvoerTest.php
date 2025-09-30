<?php

class MeldAanvoerTest extends IntegrationCase {

    private function simulateLogin() {
        $_GET['ingelogd'] = true;
        $_SERVER['HTTP_HOST'] = 'basq';
        $_SERVER['REQUEST_SCHEME'] = 'http';
        $_SERVER['REQUEST_URI'] = 'MeldAanvoer.php';
    }

    public function testGet() {
        $this->simulateLogin();
        $this->runfixture('aanvoer');
        $this->runfixture('versie-1');
        ob_start();
        include "MeldAanvoer.php";
        $output = ob_get_clean();
        $this->assertTrue(true);
    }

    # case voor zoek_controle_melding && aantal_melden
}
