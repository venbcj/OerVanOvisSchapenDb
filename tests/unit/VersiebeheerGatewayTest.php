<?php

class VersiebeheerGatewayTest extends GatewayCase {

    protected static $sutname = 'VersiebeheerGateway';

    private const FIXTURE_VERSION = 1;

    public function test_zoek_laatste_versie() {
        $result = $this->sut->zoek_laatste_versie();
        $this->assertEquals(self::FIXTURE_VERSION, $result);
    }

    public function test_zoek_readersetup_in() {
        $last_versieId = self::FIXTURE_VERSION;
        $result = $this->sut->zoek_readersetup_in($last_versieId);
        $this->assertEquals('appfile', $result);
    }

    public function test_zoek_readertaken_in() {
        $last_versieId = self::FIXTURE_VERSION;
        $result = $this->sut->zoek_readertaken_in($last_versieId);
        $this->assertEquals('readerfile', $result);
    }

    public function test_insert_app() {
        $insDate = '2010-01-01';
        $insVersie = 10;
        $insNaamApp = 'app-test';
        $insToel = 'toelichting';
        $result = $this->sut->insert_app($insDate, $insVersie, $insNaamApp, $insToel);
        $this->assertEquals('', $this->db->error);
    }

    public function test_zoek_versieId() {
        $insNaamApp = 'appfile';
        $result = $this->sut->zoek_versieId($insNaamApp);
        $this->assertEquals('', $this->db->error);
        $this->assertEquals(self::FIXTURE_VERSION, $result);
    }

    public function test_insert_taak_versie() {
        $versieId = 10;
        $insDate = '2010-01-01';
        $insVersie = 'v2';
        $insNaamTaak = 'naam-taak';
        $insToel = 'toelichting';
        $result = $this->sut->insert_taak_versie($versieId, $insDate, $insVersie, $insNaamTaak, $insToel);
        $this->assertEquals('', $this->db->error);
    }

    public function test_insert_taak() {
        $insDate = '2010-01-01';
        $insVersie = 'v2';
        $insNaamTaak = 'naam-taak';
        $insToel = 'toelichting';
        $result = $this->sut->insert_taak($insDate, $insVersie, $insNaamTaak, $insToel);
        $this->assertEquals('', $this->db->error);
    }

    public function test_zoek_versies() {
        $dmStart = '2000-01-01';
        $last_versieId = 0;
        $hisVersies = 10; // wordt een LIMIT clause
        $result = $this->sut->zoek_versies($dmStart, $last_versieId, $hisVersies);
        $this->assertEquals('', $this->db->error);
    }

    public function test_zoek_huidige_versie() {
        $last_versieId = null;
        $result = $this->sut->zoek_huidige_versie($last_versieId);
        $this->assertEquals('', $this->db->error);
    }

}
