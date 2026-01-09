<?php

class LidGatewayTest extends GatewayCase {

    protected static $sutname = 'LidGateway';

    protected $restore_keys_after = true;

    protected function restore_keys(): void {
        $this->db->query("ALTER TABLE tblLeden AUTOINCREMENT=42");
    }

    public function testFindCrediteur() {
        $this->runfixture('crediteur');
        $res = $this->sut->findCrediteur(self::LIDID);
        $this->assertEquals([4, 13], $res);
    }

    public function test_save_new() {
        $this->wants_autoincrement_restore = true;
        $form = [
            // uit een POST
            'roep' => 'a',
            'voegsel' => 'a',
            'naam' => 'a',
            'tel' => 'a',
            'mail' => 'a',
            'ubn' => 'a',
            'relnr' => 1,
            'urvo' => 'a',
            'prvo' => 'a',
            'reader' => 'a',
            'meld' => 1,
            'tech' => 1,
            'fin' => 1,
            // aangevuld in NewUser
            'login' => 'ubn',
            'alias' => 'ubn05',
            'passw' => 'complexe string die nog moet worden opgedroogd',
            'readerkey' => '44',
            'kar_werknr' => 3,
            'actief' => 1,
            'ingescand' => '2010-02-02',
            'beheer' => 0,
            'histo' => 1,
        ];
        $new_id = $this->sut->save_new($form);
        $this->assertEquals('', $this->db->error);
        $this->assertTableWithPK('tblLeden', 'lidId', $new_id, []);
    }


    // ***************************************************************************

    public function test_zoek_lege_stallijst() {
        $result = $this->sut->zoek_lege_stallijst(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_karwerk() {
        $result = $this->sut->zoek_karwerk(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_rechten() {
        $result = $this->sut->rechten(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_hasCompleteRvo() {
        $result = $this->sut->hasCompleteRvo(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_findByUserPassword() {
        $user = null;
        $password = null;
        $result = $this->sut->findByUserPassword($user, $password);
        $this->assertNotFalse($result);
    }

    public function test_findByRas() {
        $rasnr = null;
        $result = $this->sut->findByRas($rasnr);
        $this->assertNotFalse($result);
    }

    public function test_findByReaderkey() {
        $key = null;
        $result = $this->sut->findByReaderkey($key);
        $this->assertNotFalse($result);
    }

    public function test_findLididByUbn() {
        $ubn = null;
        $result = $this->sut->findLididByUbn($ubn);
        $this->assertNotFalse($result);
    }

    public function test_countWithReaderkey() {
        $apikey = null;
        $result = $this->sut->countWithReaderkey($apikey);
        $this->assertNotFalse($result);
    }

    public function test_countUserByLoginPassw() {
        $login = null;
        $passw = null;
        $result = $this->sut->countUserByLoginPassw($login, $passw);
        $this->assertNotFalse($result);
    }

    public function test_update_username() {
        $login = null;
        $result = $this->sut->update_username(self::LIDID, $login);
        $this->assertNotFalse($result);
    }

    public function test_findLoginPasswById() {
        $result = $this->sut->findLoginPasswById(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_findUbn() {
        $result = $this->sut->findUbn(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_findUbns() {
        $result = $this->sut->findUbns(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_findAlias() {
        $result = $this->sut->findAlias(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_findIdByAlias() {
        $alias = null;
        $result = $this->sut->findIdByAlias($alias);
        $this->assertNotFalse($result);
    }

    public function test_countWithAlias() {
        $alias = null;
        $result = $this->sut->countWithAlias($alias);
        $this->assertNotFalse($result);
    }

    public function test_createLambar() {
        $result = $this->sut->createLambar(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_createMoments() {
        $result = $this->sut->createMoments(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_getMoments() {
        $schaapId = null;
        $where = true;
        $result = $this->sut->getMoments(self::LIDID, $schaapId, $where);
        $this->assertNotFalse($result);
    }

    public function test_createEenheden() {
        $result = $this->sut->createEenheden(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_createElementen() {
        $result = $this->sut->createElementen(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_createPartij() {
        $result = $this->sut->createPartij(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_createRelatie() {
        $result = $this->sut->createRelatie(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_createRubriek() {
        $result = $this->sut->createRubriek(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_storeUitvalOm_insert() {
        $this->runSQL("DELETE FROM tblRedenuser WHERE redId=3");
        $this->expectNewRecordsInTables(['tblRedenuser' => 1]);
        $redId = 3; // reden 3 is geen uitval noch afvoer, in de fixtures
        $id = $this->sut->storeUitvalOm(self::LIDID, $redId);
        $this->assertTablesGrew();
        $this->assertTableWithPK('tblRedenuser', 'reduId', $id, ['uitval' => 1]);
    }

    public function test_storeUitvalOm_update() {
        $this->expectNewRecordsInTables(['tblRedenuser' => 0]);
        $redId = 3;
        $id = $this->sut->storeUitvalOm(self::LIDID, $redId);
        $this->assertTableWhereHas('tblRedenuser', ['lidId' => self::LIDID, 'redId' => $redId], ['uitval' => 1]);
    }

    public function test_storeAfvoerOm_insert() {
        $this->runSQL("DELETE FROM tblRedenuser WHERE redId=3");
        $this->expectNewRecordsInTables(['tblRedenuser' => 1]);
        $redId = 3; // reden 3 is geen uitval noch afvoer, in de fixtures
        $id = $this->sut->storeAfvoerOm(self::LIDID, $redId);
        $this->assertTablesGrew();
        $this->assertTableWithPK('tblRedenuser', 'reduId', $id, ['afvoer' => 1]);
    }

    public function test_storeAfvoerOm_update() {
        $this->expectNewRecordsInTables(['tblRedenuser' => 0]);
        $redId = 3;
        $id = $this->sut->storeAfvoerOm(self::LIDID, $redId);
        $this->assertTableWhereHas('tblRedenuser', ['lidId' => self::LIDID, 'redId' => $redId], ['afvoer' => 1]);
    }

    public function test_findReader() {
        $result = $this->sut->findReader(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_findCrediteur() {
        $result = $this->sut->findCrediteur(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_startdatum() {
        $result = $this->sut->zoek_startdatum(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_toon_historie() {
        $result = $this->sut->toon_historie(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_kzlRas() {
        $result = $this->sut->kzlRas(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_kzlBestemming() {
        $result = $this->sut->kzlBestemming(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_kzlHokkeuze() {
        $result = $this->sut->kzlHokkeuze(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_kzlReden() {
        $result = $this->sut->kzlReden(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_user_eenheden() {
        $result = $this->sut->user_eenheden(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_user_componenten() {
        $eenh = null;
        $result = $this->sut->user_componenten(self::LIDID, $eenh);
        $this->assertNotFalse($result);
    }

    public function test_countInactiveComponents() {
        $result = $this->sut->countInactiveComponents(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_inactieve_componenten() {
        $result = $this->sut->zoek_inactieve_componenten(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_user_inactieve_componenten() {
        $eenh = null;
        $result = $this->sut->user_inactieve_componenten(self::LIDID, $eenh);
        $this->assertNotFalse($result);
    }

    public function test_zoek_ingescand() {
        $result = $this->sut->zoek_ingescand(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_get_data() {
        $result = $this->sut->get_data(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_update_details() {
        $data = [];
        $result = $this->sut->update_details($data);
        $this->assertNotFalse($result);
    }

    public function test_zoek_redenen_uitval() {
        $result = $this->sut->zoek_redenen_uitval(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_redenen_afvoer() {
        $result = $this->sut->zoek_redenen_afvoer(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_groei() {
        $result = $this->sut->zoek_groei(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_crediteur_vermist() {
        $result = $this->sut->zoek_crediteur_vermist(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_all() {
        
        $result = $this->sut->all();
        $this->assertNotFalse($result);
    }

    public function test_update_password() {
        $wwnew = null;
        $result = $this->sut->update_password(self::LIDID, $wwnew);
        $this->assertNotFalse($result);
    }

    public function test_empty_ubn_not_exists() {
        $ubn = null;
        $result = $this->sut->ubn_exists($ubn);
        $this->assertFalse($result);
    }

    public function test_ubn_exists() {
        $ubn = '63'; // het fixture-ubn van lid 1
        $result = $this->sut->ubn_exists($ubn);
        $this->assertTrue($result);
    }

    public function test_store() {
        $this->wants_autoincrement_restore = true;
        $ubn = null;
        $passw = null;
        $tel = null;
        $mail = null;
        $result = $this->sut->store($ubn, $passw, $tel, $mail);
        $this->assertNotFalse($result);
    }

    public function test_update_formdetails() {
        $data = [];
        $result = $this->sut->update_formdetails($data);
        $this->assertNotFalse($result);
    }

    public function test_get_form() {
        $result = $this->sut->get_form(self::LIDID);
        $this->assertNotFalse($result);
    }

}
