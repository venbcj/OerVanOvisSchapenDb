<?php

class OoikaartTest extends IntegrationCase {

    public function test_get() {
        $this->get('/Ooikaart.php', [
            'ingelogd' => 1,
        ]);
        $this->assertNoNoise();
    }

    public function test_post_keuzes() {
        $this->post('/Ooikaart.php', [
            'ingelogd' => 1,
            'kzllevnr' => 1,
            'kzlwerknr' => 1,
            'kzlHalsnr' => 1,
        ]);
        $this->assertNoNoise();
    }

    // TODO case met meerdere keuzes... ik wil er graag een use case bij zien, op termijn
    // Ik stel deze test nog even uit tot ik de queries behoorlijk kan vullen
    # public function test_post_ooien() {
    #     $this->runSQL("INSERT INTO tblSchaap(schaapId, levensnummer, volwId) VALUES(1, 1, 2), (2, 2, null)");
    #     $this->runSQL("INSERT INTO tblStal(stalId, ubnId, schaapId) VALUES(1, 1, 1), (2, 1, 2)");
    #     $this->runSQL("INSERT INTO tblHistorie(hisId, stalId, actId) VALUES(1, 2, 3)");
    #     $this->post('/Ooikaart.php', [
    #         'ingelogd' => 1,
    #         'kzllevnr' => 1,
    #     ]);
    #     $this->assertNoNoise();
    #     $this->assertTrCount('schapen', 3 + 3); // 3 basis, en 3 per schaap -- klopt dit, of spelen er twee queries een rol?
    # }

}
