<?php

class AfleverlijstTest extends IntegrationCase {

    use Expectations;

    public function test_bestemming_ingevuld() {
        $this->setupStalFixture();
        $this->post('/AfleverLijst.php', [
            'ingelogd' => 1,
            'kzlPost' => 1,
        ]);
        $this->assertPresent('Stempelmans');
    }

    public function test_schaaplijst_aanwezig() {
        $stub = new SchaapGatewayStub();
        $GLOBALS['schaap_gateway'] = $stub;
        $stub->prime('zoek_schaap_aflever', $this->getExpected('zoek_schaap_aflever'));
        $this->post('/AfleverLijst.php', [
            'ingelogd' => 1,
            'kzlPost' => 1,
        ]);
        $this->assertTrCount('schaapdetails', 5 + 1); // tabel heeft 5 vaste rijen
    }

    private function setupStalFixture() {
        $this->uses_db();
        $this->runSQL("truncate tblPartij");
        $this->runSQL("truncate tblRelatie");
        $this->runSQL("truncate tblStal");
        $this->runSQL("truncate tblHistorie");
        $this->runSQL("INSERT INTO tblPartij(naam, partId, lidId) VALUES('Stempelmans', 1, 1)");
        $this->runSQL("INSERT INTO tblRelatie(relId, partId, relatie) VALUES(1, 1, 'test')");
        $this->runSQL("INSERT INTO tblStal(stalId, rel_best, schaapId) VALUES(1, 1, 4)");
        $this->runSQL("INSERT INTO tblHistorie(hisId, stalId, actId, datum) VALUES(1, 1, 12, '2010-01-01')");
    }

}
