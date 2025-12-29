<?php

class InkoopGatewayTest extends GatewayCase {

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
        $dmink = null;
        $result = $this->sut->eerste_inkoopid_op_datum($artikel, $dmink);
        $this->assertNotFalse($result);
    }

    public function test_eerste_inkoopdatum_zonder_voeding() {
        $artikel = null;
        $result = $this->sut->eerste_inkoopdatum_zonder_voeding($artikel);
        $this->assertNotFalse($result);
    }

    public function test_eerste_inkoopid_voeding_op_datum() {
        $artikel = null;
        $dmink = null;
        $result = $this->sut->eerste_inkoopid_voeding_op_datum($artikel, $dmink);
        $this->assertNotFalse($result);
    }

    public function test_zoek_inkoop() {
        $new_inkId = null;
        $result = $this->sut->zoek_inkoop($new_inkId);
        $this->assertNotFalse($result);
    }

    public function test_laatst_aangesproken_voorraad() {
        $artikel = null;
        $result = $this->sut->laatst_aangesproken_voorraad($artikel);
        $this->assertNotFalse($result);
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
        $lidId = null;
        $artId = null;
        $result = $this->sut->zoek_voorraad($lidId, $artId);
        $this->assertNotFalse($result);
    }

    public function test_porties() {
        $lidId = null;
        $artId = null;
        $result = $this->sut->porties($lidId, $artId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_voorraad_artikel() {
        $artId = null;
        $result = $this->sut->zoek_voorraad_artikel($artId);
        $this->assertNotFalse($result);
    }

}
