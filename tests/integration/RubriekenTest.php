<?php

class RubriekenTest extends IntegrationCase {

    public function test_get() {
        $this->runSQL("INSERT INTO tblRubriekhfd(rubhId, actief, rubriek, sort) VALUES(2, 1, 'test', 1)");
        # rubrieken worden dan weer wel uit de standaardfixture gevuld. "aankoop schapen" is hoofdrubriek 2.
        # $this->runSQL("INSERT INTO tblRubriek(rubId, rubhId, actief, credeb) VALUES(1, 1, 1, 'c')");
        $this->runSQL("INSERT INTO tblRubriekuser(rubId, lidId, sal, actief) VALUES(1, 1, 1, 1)");
        // bereik ook de inactieve rubrieken
        $this->runSQL("INSERT INTO tblRubriekuser(rubId, lidId, sal, actief) VALUES(2, 1, 0, 0)");
        // ... met inactieve hoofdrubriek
        $this->runSQL("INSERT INTO tblRubriekhfd(rubhId, actief, rubriek, sort) VALUES(1, 0, 'test-inactief', 1)");
        $this->get('/Rubrieken.php', [
            'ingelogd' => 1,
        ]);
        $this->assertNoNoise();
    }

    public function test_post_save() {
        $this->runSQL("INSERT INTO tblRubriekuser(rubuId, rubId, lidId, sal, actief) VALUES(9, 1, 1, 1, 1)");
        $this->post('/Rubrieken.php', [
            'ingelogd_' => 1,
            'knpSave_' => 1,
            'chkActief_9' => 0,
            'chkSalber_9' => 0,
        ]);
        $this->assertNoNoise();
        $this->assertTableWhereHas('tblRubriekuser', ['rubuId' => 9], ['actief' => 0, 'sal' => 0]);
    }

    public function test_geen_rechten() {
        $this->runSQL("UPDATE tblLeden SET fin=0");
        $this->get('/Rubrieken.php', [
            'ingelogd' => 1,
        ]);
        $this->assertNoNoise();
        $this->assertPresent('<img src="rubrieken_php');
    }

}
