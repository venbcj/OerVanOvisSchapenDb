<?php

class InkoopGatewayTest extends GatewayCase {

    private const DMINK = '2010-01-01';

    protected static $sutname = 'InkoopGateway';

    public function test_findArtikel() {
        $ink_id = null;
        $result = $this->sut->findArtikel($ink_id);
        $this->assertNotFalse($result);
    }

    public function test_zoek_afgeboekt() {
        $Id = null;
        $result = $this->sut->zoek_afgeboekt($Id);
        $this->assertNotFalse($result);
    }

    public function test_countArtikel() {
        $artId = null;
        $result = $this->sut->countArtikel($artId);
        $this->assertNotFalse($result);
    }

    public function test_eerste_inkoopdatum_zonder_nuttiging() {
        $artikel = null;
        $result = $this->sut->eerste_inkoopdatum_zonder_nuttiging($artikel);
        $this->assertNotFalse($result);
    }

    public function test_eerste_inkoopid_op_datum() {
        $artikel = null;
        $result = $this->sut->eerste_inkoopid_op_datum($artikel, self::DMINK);
        $this->assertNotFalse($result);
    }

    // todo case met ook een gekoppeld voeding-record
    public function test_eerste_inkoopdatum_zonder_voeding_alleen_inkoop() {
        $artikel = 1;
        $date = '2010-01-01';
        $this->runSQL("INSERT INTO tblInkoop(inkId, dmink, artId, inkat, enhuId, prijs) VALUES(1, '$date', 1, 10, 1, 1)");
        $result = $this->sut->eerste_inkoopdatum_zonder_voeding($artikel);
        $this->assertEquals($date, $result);
    }

    public function test_eerste_inkoopid_voeding_op_datum() {
        $artikel = 1;
        $this->runSQL("INSERT INTO tblInkoop(inkId, dmink, artId, inkat, enhuId, prijs) VALUES(1, '".self::DMINK."', 1, 10, 1, 1)");
        $result = $this->sut->eerste_inkoopid_voeding_op_datum($artikel, self::DMINK);
        $this->assertEquals(1, $result);
    }

    public function test_zoek_inkoop() {
        // verplichte velden, niet relevant voor deze testcase: enhuId, prijs
        $this->runSQL("INSERT INTO tblInkoop(inkId, dmink, artId, inkat, enhuId, prijs) VALUES(1, '".self::DMINK."', 1, 10, 1, 1)");
        // verplichte velden, niet relevant voor deze testcase: soort, naam
        $this->runSQL("INSERT INTO tblArtikel(artId, soort, naam, stdat) VALUES(1, 1, 1, 3)");
        $new_inkId = $this->db->insert_id;
        $result = $this->sut->zoek_inkoop($new_inkId);
        // inkid, inkat, a.stdat
        $expected = [1, 10, 3];
        $this->assertEquals($expected, $result);
    }

    public function test_laatst_aangesproken_voorraad() {
        $artikel = 1;
        // verplichte velden: soort, naam
        $this->runSQL("INSERT INTO tblArtikel(artId, stdat, soort, naam) VALUES(1, 2, 1, 1)");
        $this->runSQL("INSERT INTO tblInkoop(inkId, dmink, artId, inkat, enhuId, prijs) VALUES(1, '".self::DMINK."', 1, 10, 1, 1)");
        $this->runSQL("INSERT INTO tblNuttig(nutat, inkId, stdat) VALUES(1, 1, 3)");
        $result = $this->sut->laatst_aangesproken_voorraad($artikel);
        $this->assertNotFalse($result);
        // 7 omdat 10 inkoop en 3 nuttig
        $expected = [1, 7, 2];
        $this->assertEquals($expected, $result);
    }

    public function test_laatst_aangesproken_voorraad_voer() {
        $artId = null;
        $result = $this->sut->laatst_aangesproken_voorraad_voer($artId);
        $this->assertNotFalse($result);
    }

    public function test_set_prijs() {
        $prijs = null;
        $inkId = null;
        $result = $this->sut->set_prijs($prijs, $inkId);
        $this->assertNotFalse($result);
    }

    public function test_remove() {
        $inkId = null;
        $result = $this->sut->remove($inkId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_voorraad() {
        $artId = null;
        $result = $this->sut->zoek_voorraad(self::LIDID, $artId);
        $this->assertNotFalse($result);
    }

    public function test_porties() {
        $artId = null;
        $result = $this->sut->porties(self::LIDID, $artId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_voorraad_artikel() {
        $artId = null;
        $result = $this->sut->zoek_voorraad_artikel($artId);
        $this->assertNotFalse($result);
    }

}
