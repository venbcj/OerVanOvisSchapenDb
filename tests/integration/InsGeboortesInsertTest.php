<?php

class InsGeboortesInsertTest extends IntegrationCase {

    protected $restore_keys_before = true;
    protected $restore_keys_after = true;

    protected function restore_keys(): void {
        $this->db->query("ALTER TABLE tblVolwas AUTOINCREMENT=1");
        $this->db->query("ALTER TABLE tblStal AUTOINCREMENT=1");
    }

    public function test_post_zonder_ooi() {
        $this->runfixture('impagrident-moeder');
        $this->runfixture('moeder-4');
        $this->post('/InsGeboortes.php', [
            'ingelogd_' => 1,
            'knpInsert_' => 1,
            'chbkies_1' => 1,
            'chbDel_1' => 0, // de sleutel moet aanwezig zijn
        ]);
        $this->assertNoNoise();
    }

    public function test_post_met_bestaande_ooi() {
        $this->runfixture('moeder-4');
        $this->post('/InsGeboortes.php', [
            'ingelogd_' => 1,
            'knpInsert_' => 1,
            'chbkies_1' => 1,
            'chbDel_1' => 0, // de sleutel moet aanwezig zijn
            'kzlOoi_1' => 2, // moet dan in stal te vinden zijn. Zie fixture moeder-4. Vereist txtDatum, en een historie-record met actie.op=1 (zit ook al in deze fixture)
            'txtDatum_1' => '2010-01-01',
            'kzlRas_1' => 1, // verplicht bij elk scenario
            'kzlSekse_1' => 1, // idem
            'kzlMom_1' => 1, // idem
            'kzlRed_1' => 1, // idem
            'txtKg_1' => 1, // idem
            'kzlHok_1' => 1, //specifiek voor scenario 'modmeld, geen rel_best dwz niet dood'
        ]);
        $this->assertNoNoise();
    }

    public function test_post_met_ooi_andere_transponder() {
        $this->runfixture('impagrident-moeder');
        $this->runfixture('moeder-4');
        $this->runSQL("UPDATE tblSchaap SET levensnummer='4', transponder='ietsanders'"); // fixture moeder-4 vult levensnummer niet in, moet matchen met de "ingelezen" waarde in fixture impagrident-moeder
        $this->runSQL("UPDATE impAgrident SET moedertransponder='131072'");
        // TODO kolom 'moeder' in impAgrident vullen met levensnummer-achtige waarde, in fixture. Mag andere tests niet breken.
        $this->post('/InsGeboortes.php', [
            'ingelogd_' => 1,
            'knpInsert_' => 1,
            'chbkies_1' => 1,
            'chbDel_1' => 0,
            'kzlOoi_1' => 2,
            'txtDatum_1' => '2010-01-01',
        ]);
        $this->assertNoNoise();
        $this->assertTableWhereHas('tblSchaap', ['schaapId' => 9], ['transponder' => '131072']);
    }

    public function test_post_met_ander_verloop() {
        $this->runfixture('impagrident-moeder');
        $this->runfixture('moeder-4');
        $this->runSQL("UPDATE impAgrident SET verloop='131072'");
        $this->post('/InsGeboortes.php', [
            'ingelogd_' => 1,
            'knpInsert_' => 1,
            'chbkies_1' => 1,
            'chbDel_1' => 0,
            'kzlOoi_1' => 2,
            'txtDatum_1' => '2010-01-01',
        ]);
        $this->assertNoNoise();
        // ik stel dat volwId 1 is, omdat de tabel leeg was
        // Kost enige moeite, zie setup/teardown
        $this->assertTableWhereHas('tblVolwas', [], ['verloop' => '131072']);
    }

    public function test_post_scenario_geborenlam_geen_tech() {
        $this->runSQL("UPDATE tblLeden SET tech=0");
        $this->runfixture('impagrident-moeder');
        $this->runfixture('moeder-4');
        $this->post('/InsGeboortes.php', [
            'ingelogd_' => 1,
            'knpInsert_' => 1,
            'chbkies_1' => 1,
            'chbDel_1' => 0,
            'kzlOoi_1' => 2,
            'txtDatum_1' => '2010-01-01',
            'kzlRas_1' => 1, // verplicht bij elk scenario
            'kzlSekse_1' => 1, // idem
            'kzlMom_1' => 1, // idem
            'kzlRed_1' => 1, // idem
            'txtKg_1' => 1, // idem
        ]);
        $this->assertNoNoise();
    }

    public function test_post_scenario_geborenlam_wel_tech() {
        /* flddag: door txtDatum
         * fldLevnr: door een record in impAgrident, kolom 'levensnummer'. Af fixture is het 331
         * fldStalIdMdr: door kzlOoi
         * dmaanv_1_mdr: door een record in "eerste aanvoerdatum moeder", tblHistorie.datum. Af fixture 1-1-1990
         * fldDag > dmaanv_1_mdr: door txtDatum
         * dmafv_mdr: door een record in "datum_afvoer_moeder", tblHistorie.datum met een tblActie.af=1 (10, 12, 13, 14, 20)
         *   af fixture niet gezet
         * fldHok: door kzlHok
         */
        $this->runfixture('impagrident-moeder');
        $this->runfixture('moeder-4');
        $this->post('/InsGeboortes.php', [
            'ingelogd_' => 1,
            'knpInsert_' => 1,
            'chbkies_1' => 1,
            'chbDel_1' => 0,
            'kzlOoi_1' => 2,
            'txtDatum_1' => '2010-01-01',
            'kzlHok_1' => 1,
            'kzlRas_1' => 1, // verplicht bij elk scenario
            'kzlSekse_1' => 1, // idem
            'kzlMom_1' => 1, // idem
            'kzlRed_1' => 1, // idem
            'txtKg_1' => 1, // idem
        ]);
        $this->assertNoNoise();
    }

    public function test_post_scenario_doodgeboren_geen_tech() {
        $this->runSQL("UPDATE tblLeden SET tech=0");
        $this->runfixture('impagrident-moeder');
        $this->runSQL("UPDATE impAgrident SET levensnummer=null");
        $this->runfixture('moeder-4');
        $this->runfixture('crediteur'); // levert rendac_Id=4
        $this->post('/InsGeboortes.php', [
            'ingelogd_' => 1,
            'knpInsert_' => 1,
            'chbkies_1' => 1,
            'chbDel_1' => 0,
            'kzlOoi_1' => 2,
            'txtDatum_1' => '2010-01-01',
            'kzlRas_1' => 1, // verplicht bij elk scenario
            'kzlSekse_1' => 1, // idem
            'kzlMom_1' => 1, // idem
            'kzlRed_1' => 1, // idem
            'txtKg_1' => 1, // idem
        ]);
        $this->assertNoNoise();
        $this->assertTableWhereHas('tblHistorie', ['actId' => '14'], []);
    }

    public function test_post_scenario_doodgeboren_wel_tech() {
        $this->runfixture('impagrident-moeder');
        $this->runSQL("UPDATE impAgrident SET levensnummer=null");
        $this->runfixture('moeder-4');
        $this->runfixture('crediteur'); // levert rendac_Id=4
        $this->post('/InsGeboortes.php', [
            'ingelogd_' => 1,
            'knpInsert_' => 1,
            'chbkies_1' => 1,
            'chbDel_1' => 0,
            'kzlOoi_1' => 2,
            'txtDatum_1' => '2010-01-01',
            'kzlRas_1' => 1, // verplicht bij elk scenario
            'kzlSekse_1' => 1, // idem
            'kzlMom_1' => 1, // idem
            'kzlRed_1' => 1, // idem
            'txtKg_1' => 1, // idem
        ]);
        $this->assertNoNoise();
        $this->assertTableWhereHas('tblHistorie', ['actId' => '14'], []);
    }

    public function test_post_del_agrident() {
        $this->runfixture('impagrident');
        $this->runSQL("UPDATE tblLeden SET reader='Agrident'");
        $this->post('/InsGeboortes.php', [
            'ingelogd_' => 1,
            'knpInsert_' => 1,
            'chbkies_1' => 0,
            'chbDel_1' => 1,
        ]);
        $this->assertNoNoise();
        $this->assertTableWhereHas('impAgrident', ['levensnummer' => '331'], ['verwerkt' => 1]);
    }

    public function test_post_del_biocontrol() {
        $this->runfixture('impreader');
        $this->runSQL("UPDATE tblLeden SET reader='Biocontrol'");
        $this->post('/InsGeboortes.php', [
            'ingelogd_' => 1,
            'knpInsert_' => 1,
            'chbkies_1' => 0,
            'chbDel_1' => 1,
        ]);
        $this->assertNoNoise();
        $this->assertTableWhereHas('impReader', [], ['verwerkt' => 1]);
    }

}
