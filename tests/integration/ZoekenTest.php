<?php

class ZoekenTest extends IntegrationCase {

    public function testZoek() {
        $this->post('/Zoeken.php', [
            'ingelogd' => 1,
            'knpZoek_' => 1,
            'radHis_' => 1,
            'radOud_' => 0,
            // minimaal 1 van de volgende 5 moet not-empty zijn. De selects hebben een lege optie, en een optie Geen.
            'kzlLevnr_' => '13',
            'kzlWerknr_' => '',
            'kzlHalsnr_' => '',
            'kzlOoi_' => '',
            'kzlRam_' => '',
        ]);
        $this->assertNoNoise();
        $this->assertFout('Het zoek criterium heeft geen resulta');
    }

    public function testVind() {
        $this->runfixture('schaap-met-ouders');
        $this->post('/Zoeken.php', [
            'ingelogd' => 1,
            'knpZoek_' => 1,
            'radHis_' => 1,
            'radOud_' => 0,
            // minimaal 1 van de volgende 5 moet not-empty zijn. De selects hebben een lege optie, en een optie Geen.
            'kzlLevnr_' => '1',
            'kzlWerknr_' => '',
            'kzlHalsnr_' => '',
            'kzlOoi_' => '',
            'kzlRam_' => '',
        ]);
        $this->assertNoNoise();
        $this->assertNotFout();
    }

}
