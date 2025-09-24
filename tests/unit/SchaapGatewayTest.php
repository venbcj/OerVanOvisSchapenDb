<?php

class SchaapGatewayTest extends UnitCase {

    public function setup() : void {
        require_once('just_connect_db.php');
        $this->uses_db();
        $this->sut = new SchaapGateway($this->db);
    }

    // deze tests zijn inhoudelijk nog zwak. Er kan van alles varieren.

    public function testMedAantalFase() {
        $this->runfixture('schaap-2-lam');
        $lidid = 1;
        $M = 1; // "maandnummer" ?
        $J = 1970; // "jaar" ?
        $V = 1; // iets uit "kzlPil"
        # todo: deze twee inpakken. Veel queries hebben drie verschijningsvormen
        $Sekse = 's.geslacht is not null'; // ah dit gaat over lammeren?
        $Ouder = 'isnull(oudr.hisId)';
        $actual = $this->sut->med_aantal_fase($lidid, $M, $J, $V, $Sekse, $Ouder);
        $this->assertEquals(2, $actual);
    }

    public function testVoerFase() {
        // med-aantal-fase en voer-fase verlangen vergelijkbare datasets,
        // alleen in med* is historie.actId=8 gewenst,
        // in voer* waarden in nuttig.nutat en .stdat
        // -> rechtvaardigt dat twee fixtures? 't Is niet echt shared.
        $this->runfixture('schaap-2-lam');
        $lidid = 1;
        $M = 1; // "maandnummer" ?
        $J = 1970; // "jaar" ?
        $V = 1; // iets uit "kzlPil"
        # todo: deze twee inpakken. Veel queries hebben drie verschijningsvormen
        $Sekse = 's.geslacht is not null'; // ah dit gaat over lammeren?
        $Ouder = 'isnull(oudr.hisId)';
        $actual = $this->sut->voer_fase($lidid, $M, $J, $V, $Sekse, $Ouder);
        $this->assertEquals(2, $actual);
    }

    public function testEenheidFase() {
        $this->runfixture('eenheid-fase');
        $lidid = 1;
        $M = 1; // "maandnummer" ?
        $J = 1970; // "jaar" ?
        $V = 1; // iets uit "kzlPil"
        # todo: deze twee inpakken. Veel queries hebben drie verschijningsvormen
        $Sekse = 's.geslacht is not null';
        $Ouder = 'isnull(oudr.hisId)';
        $this->assertEquals('kg', $this->sut->eenheid_fase($lidid, $M, $J, $V, $Sekse, $Ouder));
    }

    public function testZoekStapel() {
        $this->runfixture('schaap-4');
        $this->assertEquals(1, $this->sut->zoekStapel(1));
    }

    // todo: dit is een zwakke test, en hoe zit dat met actId=10? De fixture voert die niet in.
    public function testCountUitgeschaarden() {
        $this->runfixture('schaap-4');
        $this->assertEquals(1, $this->sut->countUitgeschaarden(1));
    }

}
