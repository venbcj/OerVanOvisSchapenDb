<?php

class InsGrWijzigingUbnTest extends IntegrationCase {

    public function test_get() {
        $this->get('/InsGrWijzigingUbn.php', [
            'ingelogd' => 1,
        ]);
        $this->assertNoNoise();
    }

    public function test_post() {
        $this->runSQL("INSERT INTO tblUbn(ubnId, ubn, lidId) VALUES(3, 13, 2)"); // ubn 13 is in gebruik bij debiteur, 14 bij crediteur
        $this->runSQL("INSERT INTO impAgrident(levensnummer, ubnId) VALUES('4', 3)");
        $this->runfixture('schaap-4');
        // nu moet een stalrecord bestaan, anders crasht het op onbekende fldLevnr. fixture schaap-4 doet dat goed.
        $this->runfixture('debiteur');
        $this->runfixture('crediteur');
        // even. ubn_herk is het ubn (niet id) van het ingelogde lid. rel_herk wordt tblPartij.relId voor crediteur tblPartij.ubn = ubn_herk
        $this->runSQL("UPDATE tblPartij SET ubn=63 WHERE partId=5");
        $this->post('/InsGrWijzigingUbn.php', [
            'ingelogd_' => 1,
            'knpInsert_' => 1,
            'chbkies_1' => 1,
            'chbDel_1' => 0,
            'txtAfvoerdag_1' => '2010-01-01',
            'txtKg_1' => 42,
        ]);
        $this->assertNoNoise();
    }

    public function test_del() {
        $this->post('/InsGrWijzigingUbn.php', [
            'ingelogd_' => 1,
            'knpInsert_' => 1,
            'chbkies_1' => 0,
            'chbDel_1' => 1,
        ]);
        $this->assertNoNoise();
    }

}
