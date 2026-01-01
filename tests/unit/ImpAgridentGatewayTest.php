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
        $result = $this->sut->getInsAanvoerWhere(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_getInsAdoptieFrom() {
        
        $result = $this->sut->getInsAdoptieFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsAdoptieWhere() {
        $result = $this->sut->getInsAdoptieWhere(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_getInsAfvoerFrom() {
        
        $result = $this->sut->getInsAfvoerFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsAfvoerWhere() {
        $result = $this->sut->getInsAfvoerWhere(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_getInsDekkenFrom() {
        
        $result = $this->sut->getInsDekkenFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsDekkenWhere() {
        $result = $this->sut->getInsDekkenWhere(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_getInsDrachtFrom() {
        
        $result = $this->sut->getInsDrachtFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsDrachtWhere() {
        $result = $this->sut->getInsDrachtWhere(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_getInsGeboortesFrom() {
        
        $result = $this->sut->getInsGeboortesFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsGeboortesWhere() {
        $result = $this->sut->getInsGeboortesWhere(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_getInsGrWijzigingUbnFrom() {
        
        $result = $this->sut->getInsGrWijzigingUbnFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsGrWijzigingUbnWhere() {
        $result = $this->sut->getInsGrWijzigingUbnWhere(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_getInsHalsnummersFrom() {
        
        $result = $this->sut->getInsHalsnummersFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsHalsnummersWhere() {
        $result = $this->sut->getInsHalsnummersWhere(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_getInsLambarFrom() {
        
        $result = $this->sut->getInsLambarFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsLambarWhere() {
        $result = $this->sut->getInsLambarWhere(self::LIDID);
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
        $result = $this->sut->getInsMedicijnAgridentWhere(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_getInsMedicijnBiocontrolWhere() {
        $result = $this->sut->getInsMedicijnBiocontrolWhere(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_getInsOmnummerenFrom() {
        
        $result = $this->sut->getInsOmnummerenFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsOmnummerenWhere() {
        $result = $this->sut->getInsOmnummerenWhere(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_getInsOverplaatsAgridentFrom() {
        
        $result = $this->sut->getInsOverplaatsAgridentFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsOverplaatsAgridentWhere() {
        $result = $this->sut->getInsOverplaatsAgridentWhere(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_getInsOverplaatsBiocontrolFrom() {
        
        $result = $this->sut->getInsOverplaatsBiocontrolFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsOverplaatsBiocontrolWhere() {
        $result = $this->sut->getInsOverplaatsBiocontrolWhere(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_getInsSpenenAgridentFrom() {
        
        $result = $this->sut->getInsSpenenAgridentFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsSpenenAgridentWhere() {
        $result = $this->sut->getInsSpenenAgridentWhere(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_getInsSpenenBiocontrolFrom() {
        
        $result = $this->sut->getInsSpenenBiocontrolFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsSpenenBiocontrolWhere() {
        $result = $this->sut->getInsSpenenBiocontrolWhere(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_getInsStallijstscanFrom() {
        
        $result = $this->sut->getInsStallijstscanFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsStallijstscanWhere() {
        $result = $this->sut->getInsStallijstscanWhere(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_aantal_niet_op_stallijst() {
        $result = $this->sut->aantal_niet_op_stallijst(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_getInsStallijstscanNieuweklantFrom() {
        
        $result = $this->sut->getInsStallijstscanNieuweklantFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsStallijstscanNieuweklantWhere() {
        $result = $this->sut->getInsStallijstscanNieuweklantWhere(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_getInsTvUitscharenFrom() {
        
        $result = $this->sut->getInsTvUitscharenFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsTvUitscharenWhere() {
        $result = $this->sut->getInsTvUitscharenWhere(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_getInsUitscharenFrom() {
        
        $result = $this->sut->getInsUitscharenFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsUitscharenWhere() {
        $result = $this->sut->getInsUitscharenWhere(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_getInsUitvalAgridentFrom() {
        
        $result = $this->sut->getInsUitvalAgridentFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsUitvalAgridentWhere() {
        $result = $this->sut->getInsUitvalAgridentWhere(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_getInsUitvalBiocontrolFrom() {
        
        $result = $this->sut->getInsUitvalBiocontrolFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsUitvalBiocontrolWhere() {
        $result = $this->sut->getInsUitvalBiocontrolWhere(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_getInsVoerregistratieFrom() {
        
        $result = $this->sut->getInsVoerregistratieFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsVoerregistratieWhere() {
        $result = $this->sut->getInsVoerregistratieWhere(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_getInsWegenFrom() {
        
        $result = $this->sut->getInsWegenFrom();
        $this->assertNotFalse($result);
    }

    public function test_getInsWegenWhere() {
        $result = $this->sut->getInsWegenWhere(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_getLoslopersPlaatsenFrom() {
        
        $result = $this->sut->getLoslopersPlaatsenFrom();
        $this->assertNotFalse($result);
    }

    public function test_getLoslopersPlaatsenWhere() {
        $result = $this->sut->getLoslopersPlaatsenWhere(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_getLoslopersVerkopenFrom() {
        
        $result = $this->sut->getLoslopersVerkopenFrom();
        $this->assertNotFalse($result);
    }

    public function test_getLoslopersVerkopenWhere() {
        $result = $this->sut->getLoslopersVerkopenWhere(self::LIDID);
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
        $result = $this->sut->zoek_lambar_record(self::LIDID);
        $this->assertNotFalse($result);
    }

}
