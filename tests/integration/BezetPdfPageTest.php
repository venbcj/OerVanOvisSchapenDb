<?php

class BezetPdfPageTest extends IntegrationCase {

    /**
     * omdat deze code direct in pdf schrijft, is de uitvoer lastig te testen.
     * Ik voeg sensing variables toe aan FPDF;
     * zodra de echte library wordt bijgevoegd, moet de test er zijn
     * testbare pdf tussen schuiven.
     * Dat wordt een dependency-verbouwing.
     */

    public function testGetBezetHokLammerenVoorSpenen() {
        $this->runfixture('bezet-voor-spenen');
        $GLOBALS['Karwerk'] = 1;
        $this->get('/Bezet_pdf.php', ['Id' => 1]);
        $this->assertNoNoise();
    }

    // dit raakt ook de code-voor-spenen
    public function testGetBezetHokLammerenVolwassenen() {
        $this->runfixture('bezet-volwassenen');
        $GLOBALS['Karwerk'] = 1;
        $this->get('/Bezet_pdf.php', ['Id' => 1]);
        $this->assertNoNoise();
    }

    public function testGetBezetHokLammerenNaSpenen() {
        $this->runfixture('bezet-na-spenen');
        $GLOBALS['Karwerk'] = 1;
        $this->get('/Bezet_pdf.php', ['Id' => 1]);
        $this->assertNoNoise();
    }

}
