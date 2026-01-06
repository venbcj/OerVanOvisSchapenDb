<?php

class HokGatewayTest extends GatewayCase {

    protected static $sutname = 'HokGateway';

    public function test_findLongestHoknr() {
        $result = $this->sut->findLongestHoknr(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_findHoknrById() {
        $hokId = null;
        $result = $this->sut->findHoknrById($hokId);
        $this->assertNotFalse($result);
    }

    public function test_findSortActief() {
        $hokId = null;
        $result = $this->sut->findSortActief($hokId);
        $this->assertNotFalse($result);
    }

    public function test_updateSort() {
        $hokId = null;
        $sort = null;
        $result = $this->sut->updateSort($hokId, $sort);
        $this->assertNotFalse($result);
    }

    public function test_leeg_hok_is_niet_aanwezig() {
        $hok = null;
        $result = $this->sut->is_aanwezig(self::LIDID, $hok);
        $this->assertFalse($result);
    }

    public function test_hok_is_aanwezig() {
        $this->runfixture('hok');
        $hok = 1;
        $result = $this->sut->is_aanwezig(self::LIDID, $hok);
        $this->assertTrue($result);
    }

    public function test_kzlHok() {
        $result = $this->sut->kzlHok(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_kzlHokKV() {
        $result = $this->sut->kzlHokKV(self::LIDID);
        $this->assertNotFalse($result);
    }

    // @FRAGILE test implementatie. Methode moet sowieso verbouwd worden om KV te gebruiken
    public function test_items_without_one() {
        $this->runfixture('hok');
        $hokId = 97;
        $result = $this->sut->items_without_one(self::LIDID, $hokId);
        // af fixture zit er 1 hok in de tabel
        $this->assertCount(1, $result[0]);
    }

    // @FRAGILE test implementatie. Methode moet sowieso verbouwd worden om KV te gebruiken
    public function test_items_without_one_is_leeg() {
        $hokId = 1;
        $result = $this->sut->items_without_one(self::LIDID, $hokId);
        // af fixture zit er 1 hok in de tabel
        $this->assertCount(0, $result[0]);
    }

    public function test_lidIdByHokId() {
        $hok = null;
        $result = $this->sut->lidIdByHokId($hok);
        $this->assertNotFalse($result);
    }

    public function test_zoek_verblijf() {
        $result = $this->sut->zoek_verblijf(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_hoknummer() {
        $result = $this->sut->hoknummer(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_countVerblijven() {
        $artId = null;
        $doelId = null;
        $result = $this->sut->countVerblijven(self::LIDID, $artId, $doelId);
        $this->assertNotFalse($result);
    }

    public function test_kzlHokVoer() {
        $artId = null;
        $result = $this->sut->kzlHokVoer(self::LIDID, $artId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_hok() {
        $schaapId = null;
        $result = $this->sut->zoek_hok($schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_lid_hok() {
        $result = $this->sut->zoek_lid_hok(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_nummers_bij_lid() {
        $result = $this->sut->nummers_bij_lid(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_actieve_nummers_bij_lid() {
        $result = $this->sut->actieve_nummers_bij_lid(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_resultaten() {
        $result = $this->sut->resultaten(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_insert() {
        $hoknr = null;
        $sort = null;
        $result = $this->sut->insert(self::LIDID, $hoknr, $sort);
        $this->assertNotFalse($result);
    }

    public function test_countHokRelatiesAlle() {
        $actief = null;
        $result = $this->sut->countHokRelatiesAlle(self::LIDID, $actief);
        $this->assertNotFalse($result);
    }

    public function test_zoekRelatiesAlle() {
        $actief = null;
        $result = $this->sut->zoekRelatiesAlle(self::LIDID, $actief);
        $this->assertNotFalse($result);
    }

    public function test_zoek_relatie() {
        $hokId = null;
        $result = $this->sut->zoek_relatie($hokId);
        $this->assertNotFalse($result);
    }

    public function test_hokn_beschikbaar() {
        $hokId = null;
        $result = $this->sut->hokn_beschikbaar(self::LIDID, $hokId);
        $this->assertNotFalse($result);
    }

    public function test_set_actief() {
        $hokId = null;
        $actief = null;
        $result = $this->sut->set_actief($hokId, $actief);
        $this->assertNotFalse($result);
    }

    public function test_delete() {
        $hokId = null;
        $result = $this->sut->delete($hokId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_lambar() {
        $result = $this->sut->zoek_lambar(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_insert_lambar() {
        $result = $this->sut->insert_lambar(self::LIDID);
        $this->assertNotFalse($result);
    }

}
