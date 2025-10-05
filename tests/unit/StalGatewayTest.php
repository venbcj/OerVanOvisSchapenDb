<?php

class StalGatewayTest extends GatewayCase {

    protected static $sutname = 'StalGateway';

    public function testUpdateHerkomstByMelding() {
        $this->runfixture('melding-4');
        $recId = 4; // uit de fixture.
        $fldHerk = 9;
        $this->sut->updateHerkomstByMelding($recId, $fldHerk);
        $this->assertTableWithPK('tblStal', 'stalId', 49, ['rel_herk' => 9]);
    }

    public function testZoekKleurHalsnr() {
        $this->runSQL("DELETE FROM tblStal");
        $this->assertNull($this->sut->zoekKleurHalsnr(self::LIDID, 1)['stalId']);
    }

    public function testZoekLaatsteStalId() {
        $this->runSQL("DELETE FROM tblStal");
        $this->assertNull($this->sut->zoek_laatste_stalId(self::LIDID, 4));
    }

}
