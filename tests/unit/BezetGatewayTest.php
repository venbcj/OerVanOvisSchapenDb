<?php

class BezetGatewayTest extends GatewayCase {

    public static $sutname = 'BezetGateway';

    public function test_zoek_verblijven_ingebruik_zonder_speendm() {
        $result = $this->sut->zoek_verblijven_ingebruik_zonder_speendm(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_verblijven_ingebruik_met_speendm() {
        $result = $this->sut->zoek_verblijven_ingebruik_met_speendm(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_schapen_zonder_verblijf() {
        $result = $this->sut->zoek_schapen_zonder_verblijf(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_verblijven_in_gebruik() {
        $result = $this->sut->zoek_verblijven_in_gebruik(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_nu_in_verblijf_geb_spn() {
        $hokId = null;
        $result = $this->sut->zoek_nu_in_verblijf_geb_spn($hokId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_nu_in_verblijf_geb() {
        $hokId = null;
        $result = $this->sut->zoek_nu_in_verblijf_geb($hokId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_nu_in_verblijf_spn() {
        $hokId = null;
        $result = $this->sut->zoek_nu_in_verblijf_spn($hokId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_nu_in_verblijf_prnt() {
        $hokId = null;
        $result = $this->sut->zoek_nu_in_verblijf_prnt($hokId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_nu_in_verblijf_parent() {
        $hokId = null;
        $result = $this->sut->zoek_nu_in_verblijf_parent($hokId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_verlaten_geb_excl_overpl_en_uitval() {
        $hokId = null;
        $dmstopgeb = null;
        $result = $this->sut->zoek_verlaten_geb_excl_overpl_en_uitval($hokId, $dmstopgeb);
        $this->assertNotFalse($result);
    }

    public function test_zoek_verlaten_spn_excl_overpl_en_uitval() {
        $hokId = null;
        $dmstopspn = null;
        $result = $this->sut->zoek_verlaten_spn_excl_overpl_en_uitval($hokId, $dmstopspn);
        $this->assertNotFalse($result);
    }

    public function test_zoek_overplaatsing_geb() {
        $hokId = null;
        $dmstopgeb = null;
        $result = $this->sut->zoek_overplaatsing_geb($hokId, $dmstopgeb);
        $this->assertNotFalse($result);
    }

    public function test_zoek_overplaatsing_spn() {
        $hokId = null;
        $dmstopspn = null;
        $result = $this->sut->zoek_overplaatsing_spn($hokId, $dmstopspn);
        $this->assertNotFalse($result);
    }

    public function test_zoek_overleden_geb() {
        $hokId = null;
        $dmstopgeb = null;
        $result = $this->sut->zoek_overleden_geb($hokId, $dmstopgeb);
        $this->assertNotFalse($result);
    }

    public function test_zoek_overleden_spn() {
        $hokId = null;
        $dmstopspn = null;
        $result = $this->sut->zoek_overleden_spn($hokId, $dmstopspn);
        $this->assertNotFalse($result);
    }

    public function test_zoek_moeders_van_lam() {
        $hokId = null;
        $result = $this->sut->zoek_moeders_van_lam($hokId);
        $this->assertNotFalse($result);
    }

    public function test_aantal_laatste_dekkingen_van_moeders_uit_gekozen_verblijf_met_laatste_dekkingen_met_gekozen_vader() {
        $txtDay = null;
        $kzlHok = null;
        $kzlVdr = null;
        $result = $this->sut->aantal_laatste_dekkingen_van_moeders_uit_gekozen_verblijf_met_laatste_dekkingen_met_gekozen_vader($txtDay, $kzlHok, $kzlVdr);
        $this->assertNotFalse($result);
    }

    public function test_zoek_moeders_in_verblijf() {
        $kzlHok = null;
        $result = $this->sut->zoek_moeders_in_verblijf($kzlHok);
        $this->assertNotFalse($result);
    }

    public function testGeenMoedersInVerblijf() {
        $kzlHok = 1;
        $this->assertEquals(0, $this->sut->zoek_moeders_in_verblijf($kzlHok)->num_rows);
    }

    public function testMoedersInVerblijf() {
        $kzlHok = 1;
        $this->runfixture('schaap-4');
        $this->runfixture('moeders-in-verblijf');
        $this->assertEquals(1, $this->sut->zoek_moeders_in_verblijf($kzlHok)->num_rows);
    }

    public function test_schaap_gegevens() {
        $hokId = null;
        $dmbegin = '1900-01-01';
        $dmeind = '2030-12-31';
        $dagkg = null;
        $filterDoel = '';
        $doelId = null;
        $result = $this->sut->schaap_gegevens(self::LIDID, $hokId, $dmbegin, $dmeind, $dagkg, $filterDoel, $doelId);
        $this->assertNotFalse($result);
    }

    public function test_insert() {
        $hisId = null;
        $hokId = null;
        $result = $this->sut->insert($hisId, $hokId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_verblijven() {
        $result = $this->sut->zoek_verblijven(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_getHokAfleverenFrom() {
        
        $result = $this->sut->getHokAfleverenFrom();
        $this->assertNotFalse($result);
    }

    // WARNING test bindt aan implementatie. Breekt het? Verwijder de test.
    // (methode get...Where zou niet public moeten zijn. Lossen we later op in de Transaction-fase, hoop ik)
    public function test_getHokAfleverenWhereNodata() {
        $pagina = null;
        $fase = null;
        $hokId = null;
        $result = $this->sut->getHokAfleverenWhere($pagina, $fase, self::LIDID, $hokId);
        // parameters hebben geen invloed op de args die terugkomen.
        $expected_args = [
                [':hokId', null, 'int'],
                [':lidId', 1, 'int'],
            ];
        $this->assertEquals([
            'WHERE b.hokId = :hokId and isnull(uit.bezId) and h.skip = 0',
            $expected_args
        ], $result);
    }

    public function test_getHokAfleverenWhereAfleveren() {
        $pagina = 'Afleveren';
        $fase = null;
        $hokId = null;
        $result = $this->sut->getHokAfleverenWhere($pagina, $fase, self::LIDID, $hokId);
        $this->assertEquals('WHERE b.hokId = :hokId and isnull(uit.bezId) and h.skip = 0 and spn.schaapId is not null and prnt.schaapId is null', $result[0]);
    }

    public function test_getHokAfleverenWhereVerkopen() {
        $pagina = 'Verkopen';
        $fase = null;
        $hokId = null;
        $result = $this->sut->getHokAfleverenWhere($pagina, $fase, self::LIDID, $hokId);
        $this->assertEquals('WHERE b.hokId = :hokId and isnull(uit.bezId) and h.skip = 0 and prnt.schaapId is not null', $result[0]);
    }

    public function test_getHokAfleverenWhereUitscharen1() {
        $pagina = 'Uitscharen';
        $fase = 1;
        $hokId = null;
        $result = $this->sut->getHokAfleverenWhere($pagina, $fase, self::LIDID, $hokId);
        $this->assertEquals('WHERE b.hokId = :hokId and isnull(uit.bezId) and h.skip = 0 and prnt.schaapId is null', $result[0]);
    }

    public function test_getHokAfleverenWhereUitscharen3() {
        $pagina = 'Uitscharen';
        $fase = 3;
        $hokId = null;
        $result = $this->sut->getHokAfleverenWhere($pagina, $fase, self::LIDID, $hokId);
        $this->assertEquals('WHERE b.hokId = :hokId and isnull(uit.bezId) and h.skip = 0 and prnt.schaapId is not null', $result[0]);
    }

    public function test_zoek_periode_met_aantal_schapen() {
        $hokId = null;
        $dmafsl = '2020-01-01';
        $dmStartPeriode = '2020-01-01';
        $fase_tijdens_betreden_verblijf = 'true';
        $result = $this->sut->zoek_periode_met_aantal_schapen(self::LIDID, $hokId, $dmafsl, $dmStartPeriode, $fase_tijdens_betreden_verblijf);
        $this->assertNotFalse($result);
    }

    public function test_zoek_inhoud_periode() {
        $hokId = null;
        $dmafsl = '2020-01-01';
        $dmStartPeriode = '2020-01-01';
        $fase_tijdens_betreden_verblijf = 'true';
        $Karwerk = 5;
        $result = $this->sut->zoek_inhoud_periode(self::LIDID, $hokId, $dmafsl, $dmStartPeriode, $fase_tijdens_betreden_verblijf, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_zoek_hok_ingebruik_geb() {
        $result = $this->sut->zoek_hok_ingebruik_geb(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_hok_ingebruik_spn() {
        $result = $this->sut->zoek_hok_ingebruik_spn(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_hoklijst_zoek_nu_in_verblijf_geb() {
        $hokId = null;
        $result = $this->sut->hoklijst_zoek_nu_in_verblijf_geb($hokId);
        $this->assertNotFalse($result);
    }

    public function test_hoklijst_zoek_nu_in_verblijf_spn() {
        $hokId = null;
        $result = $this->sut->hoklijst_zoek_nu_in_verblijf_spn($hokId);
        $this->assertNotFalse($result);
    }

}
