<?php

class PeriodeGatewayTest extends GatewayCase {

    protected static $sutname = 'PeriodeGateway';

    public function testGeenJaarmaanden() {
        $this->runSQL("DELETE FROM tblPeriode");
        $artId = 1;
        $doelId = 1;
        $actual = $this->sut->aantal_jaarmaanden(self::LIDID, $artId, $doelId);
        $this->assertEquals(0, $actual);
    }

    public function testEenJaarmaanden() {
        $this->runfixture('jaarmaanden-1');
        $artId = 1;
        $doelId = 1;
        $actual = $this->sut->aantal_jaarmaanden(self::LIDID, $artId, $doelId);
        $this->assertEquals(1, $actual);
    }

    // uit phpunit seed 1765720329 blijkt dat dit een vals-positief is.
    // Alleen de fixture jaarmaanden-n is NIET GENOEG.
    public function testMeerJaarmaanden() {
        $this->runfixture('voervoorraad');
        $this->runfixture('jaarmaanden-n');
        $artId = 1;
        $doelId = 1;
        $actual = $this->sut->aantal_jaarmaanden(self::LIDID, $artId, $doelId);
        $this->assertEquals(2, $actual);
    }

    // ***********************************************************

    public function test_zoek_laatste_afsluitdm_geb() {
        $hokId = null;
        $result = $this->sut->zoek_laatste_afsluitdm_geb($hokId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_laatste_afsluitdm_spn() {
        $hokId = null;
        $result = $this->sut->zoek_laatste_afsluitdm_spn($hokId);
        $this->assertNotFalse($result);
    }

    public function test_aantal_jaarmaanden() {
        $artId = null;
        $doelId = null;
        $result = $this->sut->aantal_jaarmaanden(self::LIDID, $artId, $doelId);
        $this->assertNotFalse($result);
    }

    public function test_kzlJaarmaand() {
        $fldVoer = null;
        $result = $this->sut->kzlJaarmaand(self::LIDID, $fldVoer);
        $this->assertNotFalse($result);
    }

    public function test_maandjaren_hok_voer() {
        $artId = null;
        $doelId = null;
        $resJrmnd = 'true';
        $resHok = 'true';
        $result = $this->sut->maandjaren_hok_voer(self::LIDID, $artId, $doelId, $resJrmnd, $resHok);
        $this->assertNotFalse($result);
    }

    public function test_begin_eind_periode() {
        $doelId = null;
        $artId = null;
        $jrmnd = null;
        $result = $this->sut->begin_eind_periode(self::LIDID, $doelId, $artId, $jrmnd);
        $this->assertNotFalse($result);
    }

    public function test_periode_totalen() {
        $hokId = null;
        $fldVoer = null;
        $doelId = null;
        $filterDoel = null;
        $resHok = 'true';
        $dmstart = '2010-01-01';
        $dmbegin = '2010-01-01';
        $dmeind = '2010-01-01';
        $jrmnd = null;
        $result = $this->sut->periode_totalen(self::LIDID, $hokId, $fldVoer, $doelId, $filterDoel, $resHok, $dmstart, $dmbegin, $dmeind, $jrmnd);
        $this->assertNotFalse($result);
    }

    public function test_periode_totalen_met_voer_zonder_schapen() {
        $hokId = null;
        $artId = null;
        $doelId = null;
        $resHok = 'true';
        $dmstart = '2010-10-10';
        $jrmnd = null;
        $result = $this->sut->periode_totalen_met_voer_zonder_schapen(self::LIDID, $hokId, $artId, $doelId, $resHok, $dmstart, $jrmnd);
        $this->assertNotFalse($result);
    }

    public function test_findByHokAndDoel() {
        $hokId = null;
        $doelId = null;
        $datum = null;
        $result = $this->sut->findByHokAndDoel($hokId, $doelId, $datum);
        $this->assertNotFalse($result);
    }

    public function test_zoek_doelid() {
        $periId = null;
        $result = $this->sut->zoek_doelid($periId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_start_periode() {
        $hokId = null;
        $doelId = null;
        $dmafsl = null;
        $result = $this->sut->zoek_start_periode($hokId, $doelId, $dmafsl);
        $this->assertNotFalse($result);
    }

    public function test_insert() {
        $hokId = null;
        $doelId = null;
        $dmafsluit = null;
        $result = $this->sut->insert($hokId, $doelId, $dmafsluit);
        $this->assertNotFalse($result);
    }

}
