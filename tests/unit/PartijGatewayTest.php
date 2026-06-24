<?php

class PartijGatewayTest extends GatewayCase {

    protected static $sutname = 'PartijGateway';

    private const PARTIJNAAM_IN_FIXTURE = 'Henk';

    public function test_findLeverancier() {
        $result = $this->sut->findLeverancier(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_findKlant() {
        $result = $this->sut->findKlant(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_relatienummers() {
        $result = $this->sut->relatienummers(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_find_debiteur() {
        $result = $this->sut->find_relatie(self::LIDID, 'deb');
        $this->assertNotFalse($result);
    }

    public function test_find_crediteur() {
        $result = $this->sut->find_relatie(self::LIDID, 'cred');
        $this->assertNotFalse($result);
    }

    public function test_findNaam() {
        $partId = null;
        $result = $this->sut->findNaam($partId);
        $this->assertNotFalse($result);
    }

    public function test_vindt_bestaande_partij() {
        $this->runfixture('partij-1');
        $result = $this->sut->has_partij(self::LIDID, self::PARTIJNAAM_IN_FIXTURE);
        $this->assertTrue($result);
    }

    public function test_vindt_geen_bestaande_partij() {
        $partij = 'bestaatniet';
        $this->runfixture('partij-1');
        $result = $this->sut->has_partij(self::LIDID, $partij);
        $this->assertFalse($result);
    }

}
