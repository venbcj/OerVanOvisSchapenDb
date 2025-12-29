<?php

class ImpAgridentGatewayTest extends GatewayCase {

    protected static $sutname = 'ImpAgridentGateway';

    public function test_zoek_aantal_uit_reader() {
        $Id = null;
        $result = $this->sut->zoek_aantal_uit_reader($Id);
        $this->assertNotFalse($result);
    }

    public function test_update() {
        $id = null;
        $aantal = null;
        $result = $this->sut->update($id, $aantal);
        $this->assertNotFalse($result);
    }

    public function test_set_verwerkt() {
        $recId = null;
        $result = $this->sut->set_verwerkt($recId);
        $this->assertNotFalse($result);
    }

    public function test_update_hok() {
        $Id = null;
        $hokId = null;
        $result = $this->sut->update_hok($Id, $hokId);
        $this->assertNotFalse($result);
    }

    public function test_getInsAanvoerFrom() {
        
        $result = $this->sut->getInsAanvoerFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsAanvoerWhere() {
        $lidId = null;
        $result = $this->sut->getInsAanvoerWhere($lidId);
        $this->assertNotFalse($result);
    }

    public function test_getInsAdoptieFrom() {
        
        $result = $this->sut->getInsAdoptieFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsAdoptieWhere() {
        $lidId = null;
        $result = $this->sut->getInsAdoptieWhere($lidId);
        $this->assertNotFalse($result);
    }

    public function test_getInsAfvoerFrom() {
        
        $result = $this->sut->getInsAfvoerFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsAfvoerWhere() {
        $lidId = null;
        $result = $this->sut->getInsAfvoerWhere($lidId);
        $this->assertNotFalse($result);
    }

    public function test_getInsDekkenFrom() {
        
        $result = $this->sut->getInsDekkenFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsDekkenWhere() {
        $lidId = null;
        $result = $this->sut->getInsDekkenWhere($lidId);
        $this->assertNotFalse($result);
    }

    public function test_getInsDrachtFrom() {
        
        $result = $this->sut->getInsDrachtFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsDrachtWhere() {
        $lidId = null;
        $result = $this->sut->getInsDrachtWhere($lidId);
        $this->assertNotFalse($result);
    }

    public function test_getInsGeboortesFrom() {
        
        $result = $this->sut->getInsGeboortesFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsGeboortesWhere() {
        $lidId = null;
        $result = $this->sut->getInsGeboortesWhere($lidId);
        $this->assertNotFalse($result);
    }

    public function test_getInsGrWijzigingUbnFrom() {
        
        $result = $this->sut->getInsGrWijzigingUbnFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsGrWijzigingUbnWhere() {
        $lidId = null;
        $result = $this->sut->getInsGrWijzigingUbnWhere($lidId);
        $this->assertNotFalse($result);
    }

    public function test_getInsHalsnummersFrom() {
        
        $result = $this->sut->getInsHalsnummersFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsHalsnummersWhere() {
        $lidId = null;
        $result = $this->sut->getInsHalsnummersWhere($lidId);
        $this->assertNotFalse($result);
    }

    public function test_getInsLambarFrom() {
        
        $result = $this->sut->getInsLambarFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsLambarWhere() {
        $lidId = null;
        $result = $this->sut->getInsLambarWhere($lidId);
        $this->assertNotFalse($result);
    }

    public function test_getInsMedicijnAgridentFrom() {
        
        $result = $this->sut->getInsMedicijnAgridentFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsMedicijnBiocontrolFrom() {
        
        $result = $this->sut->getInsMedicijnBiocontrolFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsMedicijnAgridentWhere() {
        $lidId = null;
        $result = $this->sut->getInsMedicijnAgridentWhere($lidId);
        $this->assertNotFalse($result);
    }

    public function test_getInsMedicijnBiocontrolWhere() {
        $lidId = null;
        $result = $this->sut->getInsMedicijnBiocontrolWhere($lidId);
        $this->assertNotFalse($result);
    }

    public function test_getInsOmnummerenFrom() {
        
        $result = $this->sut->getInsOmnummerenFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsOmnummerenWhere() {
        $lidId = null;
        $result = $this->sut->getInsOmnummerenWhere($lidId);
        $this->assertNotFalse($result);
    }

    public function test_getInsOverplaatsAgridentFrom() {
        
        $result = $this->sut->getInsOverplaatsAgridentFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsOverplaatsAgridentWhere() {
        $lidId = null;
        $result = $this->sut->getInsOverplaatsAgridentWhere($lidId);
        $this->assertNotFalse($result);
    }

    public function test_getInsOverplaatsBiocontrolFrom() {
        
        $result = $this->sut->getInsOverplaatsBiocontrolFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsOverplaatsBiocontrolWhere() {
        $lidId = null;
        $result = $this->sut->getInsOverplaatsBiocontrolWhere($lidId);
        $this->assertNotFalse($result);
    }

    public function test_getInsSpenenAgridentFrom() {
        
        $result = $this->sut->getInsSpenenAgridentFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsSpenenAgridentWhere() {
        $lidId = null;
        $result = $this->sut->getInsSpenenAgridentWhere($lidId);
        $this->assertNotFalse($result);
    }

    public function test_getInsSpenenBiocontrolFrom() {
        
        $result = $this->sut->getInsSpenenBiocontrolFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsSpenenBiocontrolWhere() {
        $lidId = null;
        $result = $this->sut->getInsSpenenBiocontrolWhere($lidId);
        $this->assertNotFalse($result);
    }

    public function test_getInsStallijstscanFrom() {
        
        $result = $this->sut->getInsStallijstscanFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsStallijstscanWhere() {
        $lidId = null;
        $result = $this->sut->getInsStallijstscanWhere($lidId);
        $this->assertNotFalse($result);
    }

    public function test_aantal_niet_op_stallijst() {
        $lidId = null;
        $result = $this->sut->aantal_niet_op_stallijst($lidId);
        $this->assertNotFalse($result);
    }

    public function test_getInsStallijstscanNieuweklantFrom() {
        
        $result = $this->sut->getInsStallijstscanNieuweklantFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsStallijstscanNieuweklantWhere() {
        $lidId = null;
        $result = $this->sut->getInsStallijstscanNieuweklantWhere($lidId);
        $this->assertNotFalse($result);
    }

    public function test_getInsTvUitscharenFrom() {
        
        $result = $this->sut->getInsTvUitscharenFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsTvUitscharenWhere() {
        $lidId = null;
        $result = $this->sut->getInsTvUitscharenWhere($lidId);
        $this->assertNotFalse($result);
    }

    public function test_getInsUitscharenFrom() {
        
        $result = $this->sut->getInsUitscharenFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsUitscharenWhere() {
        $lidId = null;
        $result = $this->sut->getInsUitscharenWhere($lidId);
        $this->assertNotFalse($result);
    }

    public function test_getInsUitvalAgridentFrom() {
        
        $result = $this->sut->getInsUitvalAgridentFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsUitvalAgridentWhere() {
        $lidId = null;
        $result = $this->sut->getInsUitvalAgridentWhere($lidId);
        $this->assertNotFalse($result);
    }

    public function test_getInsUitvalBiocontrolFrom() {
        
        $result = $this->sut->getInsUitvalBiocontrolFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsUitvalBiocontrolWhere() {
        $lidId = null;
        $result = $this->sut->getInsUitvalBiocontrolWhere($lidId);
        $this->assertNotFalse($result);
    }

    public function test_getInsVoerregistratieFrom() {
        
        $result = $this->sut->getInsVoerregistratieFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsVoerregistratieWhere() {
        $lidId = null;
        $result = $this->sut->getInsVoerregistratieWhere($lidId);
        $this->assertNotFalse($result);
    }

    public function test_getInsWegenFrom() {
        
        $result = $this->sut->getInsWegenFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsWegenWhere() {
        $lidId = null;
        $result = $this->sut->getInsWegenWhere($lidId);
        $this->assertNotFalse($result);
    }

    public function test_getLoslopersPlaatsenFrom() {
        
        $result = $this->sut->getLoslopersPlaatsenFrom();
        $this->assertNotFalse($result);
    }

    public function test_getLoslopersPlaatsenWhere() {
        $lidId = null;
        $result = $this->sut->getLoslopersPlaatsenWhere($lidId);
        $this->assertNotFalse($result);
    }

    public function test_getLoslopersVerkopenFrom() {
        
        $result = $this->sut->getLoslopersVerkopenFrom();
        $this->assertNotFalse($result);
    }

    public function test_getLoslopersVerkopenWhere() {
        $lidId = null;
        $result = $this->sut->getLoslopersVerkopenWhere($lidId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_voerregels_reader() {
        $hokid = null;
        $artId = null;
        $doelId = null;
        $result = $this->sut->zoek_voerregels_reader($hokid, $artId, $doelId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_readerregel_verwerkt() {
        $recId = null;
        $result = $this->sut->zoek_readerregel_verwerkt($recId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_levnr_reader() {
        $recId = null;
        $result = $this->sut->zoek_levnr_reader($recId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_lambar_record() {
        $lidId = null;
        $result = $this->sut->zoek_lambar_record($lidId);
        $this->assertNotFalse($result);
    }

}
