<?php

class InvSchaapTest extends IntegrationCase {

    private const LEVNR_NIET_IN_DB = '9';
    private const LEVNR_IN_FIXTURE = '4';
    private const EEN_RAS = 'kool';

    public function testPostInvSchaap() {
        $this->post("/InvSchaap.php", ['ingelogd' => 1, 'txtLevnr' => 1]);
        $this->assertNoNoise();
        // todo case met uitgeschaard
    }

    public function testSaveInvSchaapZonderLevnr() {
        $this->post("/InvSchaap.php", $this->minimal());
        $this->assertNoNoise();
        // dit test ook 1 validatie, omdat levnr en txtDmuitv beide ontbreken.
        $this->assertFout('Bij overlijden moet datum t.b.v. uitval zijn ingevuld.');
    }

    public function testSaveInvSchaapMetLevnr() {
        $this->post("/InvSchaap.php", $this->minimal([
            'txtLevnr' => self::LEVNR_NIET_IN_DB,
        ]));
        $this->assertNoNoise();
        $this->assertFout('Het geslacht moet zijn ingevuld.');
    }

    public function testValidatieInvSchaapRas() {
        $this->post("/InvSchaap.php", $this->minimal([
            'txtLevnr' => self::LEVNR_NIET_IN_DB,
            'txtDmuitv' => '1-1-1980',
            'kzlSekse' => 'ram',
        ]));
        $this->assertNoNoise();
        $this->assertFout('Het ras moet zijn ingevuld.');
    }

    public function testValidatieInvSchaapMoeder() {
        $this->post("/InvSchaap.php", $this->minimal([
            'txtLevnr' => self::LEVNR_NIET_IN_DB,
            'txtDmuitv' => '1-1-1980',
            'kzlSekse' => 'ram',
            'kzlFase' => 'lam',
            'kzlOoi' => null,
            'kzlRas' => self::EEN_RAS,
        ]));
        $this->assertNoNoise();
        $this->assertFout('Het moederdier moet zijn ingevuld.');
    }

    public function testValidatieInvSchaapGeenOverlijden() {
        $this->post("/InvSchaap.php", $this->minimal([
            'txtLevnr' => self::LEVNR_NIET_IN_DB,
            'kzlSekse' => 'ram',
            'kzlFase' => 'lam',
            'kzlRas' => self::EEN_RAS,
            'kzlMoment' => '4',
            // testcase = niet in de post: txtUitvdm
        ]));
        $this->assertNoNoise();
        $this->assertFout('Bij overlijden moet datum t.b.v. uitval zijn ingevuld.');
        // todo: er zijn nog twee redenen voor deze fout
    }

    public function testValidatieInvSchaapPrematuurOverlijden() {
        $this->post("/InvSchaap.php", $this->minimal([
            'txtLevnr' => self::LEVNR_NIET_IN_DB,
            'kzlSekse' => 'ram',
            'kzlFase' => 'lam',
            'kzlRas' => self::EEN_RAS,
            'kzlMoment' => '4',
            'txtGebdm' => '10-10-1991',
            'txtUitvdm' => '1-1-1900',
        ]));
        $this->assertNoNoise();
        $this->assertFout('Datum overlijden kan niet voor geboortedatum liggen !');
    }

    public function testValidatieInvSchaapOverlijdenVoorAanschaf() {
        $this->post("/InvSchaap.php", $this->minimal([
            'txtLevnr' => self::LEVNR_NIET_IN_DB,
            'kzlSekse' => 'ram',
            'kzlFase' => 'lam',
            'kzlRas' => self::EEN_RAS,
            'kzlMoment' => '4',
            'txtGebdm' => '5-5-1980',
            'txtAanv' => '10-10-1991', // <== merkwaardige afwijking in de naamgeving?
            'txtUitvdm' => '1-1-1990',
        ]));
        $this->assertNoNoise();
        $this->assertFout('Datum overlijden kan niet voor aanschafdatum liggen !');
    }

    public function testValidatieInvSchaapPrematureAanschaf() {
        $this->post("/InvSchaap.php", $this->minimal([
            'txtLevnr' => self::LEVNR_NIET_IN_DB,
            'kzlSekse' => 'ram',
            'kzlFase' => 'lam',
            'kzlRas' => self::EEN_RAS,
            'kzlMoment' => '4',
            'txtGebdm' => '5-5-1980',
            'txtAanv' => '10-10-1971', // <== merkwaardige afwijking in de naamgeving?
            'txtUitvdm' => '1-1-1990',
        ]));
        $this->assertNoNoise();
        $this->assertFout('Datum aanschaf kan niet voor geboortedatum liggen !');
    }

    public function testValidatieInvSchaapOngeplaatstLam() {
        $this->post("/InvSchaap.php", $this->minimal([
            'txtLevnr' => self::LEVNR_NIET_IN_DB,
            'kzlSekse' => 'ram',
            'kzlFase' => 'lam',
            'kzlRas' => self::EEN_RAS,
            'txtGebdm' => '5-5-1970',
            'txtAanv' => '10-10-1971', // <== merkwaardige afwijking in de naamgeving?
        ]));
        $this->assertNoNoise();
        $this->assertFout('Plaats het lam ook nog in een verblijf.');
    }

    public function testValidatieInvSchaapAlAanwezig() {
        $this->runfixture('schaap-4');
        $this->post("/InvSchaap.php", $this->minimal([
            'txtLevnr' => self::LEVNR_IN_FIXTURE,
            'kzlSekse' => 'ram',
            'kzlFase' => 'lam',
            'kzlMoment' => 5, // <= dit is 1 van de mogelijkheden. todo ook met uitvaldatum of reden
            'txtUitvdm' => '1-1-2001',
        ]));
        $this->assertNoNoise();
        $this->assertFout('Dit dier staat al op de stallijst.');
    }

    public function testValidatieInvSchaapVolwassenAanschaf() {
        $this->post("/InvSchaap.php", $this->minimal([
            'txtLevnr' => self::LEVNR_NIET_IN_DB,
            'kzlSekse' => 'ram',
            'kzlFase' => 'vader', // nog een testcase evenzo 'moeder'
            'kzlRas' => self::EEN_RAS,
            'txtGebdm' => '5-5-1970',
        ]));
        $this->assertNoNoise();
        $this->assertFout('Bij invoer van een volwassen dier is de aanschafdatum verplicht.');
    }

    public function testValidatieInvSchaapGeborenVoorAanschaf() {
        $this->runfixture('schaap-4');
        $this->runfixture('moeder-4');
        $this->post("/InvSchaap.php", $this->minimal([
            'txtLevnr' => self::LEVNR_NIET_IN_DB,
            'kzlSekse' => 'ram',
            'kzlFase' => 'lam',
            'kzlRas' => self::EEN_RAS,
            'txtGebdm' => '5-5-1980', // schaap 9, de moeder, is in 1990 geboren volgens fixture moeder-4
            'kzlHok' => 3,
            'kzlOoi' => 9,
        ]));
        $this->assertNoNoise();
        $this->assertFout('Geboortedatum kan niet voor aanvoerdatum van moederdier liggen.');
    }

    public function testValidatieInvSchaapGeborenNaAfvoer() {
        $this->runfixture('schaap-4');
        $this->runfixture('moeder-4-afgevoerd');
        $this->post("/InvSchaap.php", $this->minimal([
            'txtLevnr' => self::LEVNR_NIET_IN_DB,
            'kzlSekse' => 'ram',
            'kzlFase' => 'lam',
            'kzlRas' => self::EEN_RAS,
            'txtGebdm' => '5-5-1980', // schaap 9, de moeder, is in 1970 afgevoerd volgens fixture moeder-4-afgevoerd
            'kzlHok' => 3,
            'kzlOoi' => 9,
        ]));
        $this->assertNoNoise();
        $this->assertFout('Geboortedatum kan niet na afvoerdatum van moederdier liggen.');
    }

    // todo case "geboortedatum na eerste geboortedatum
    // todo case aanvoerdatum voor laatste afvoerdatum
    // todo case dood schaap
    // todo case levensnummer komt al voor
    // todo case korte draagtijd (BCB: is dat juist? Kan dit ook: lam 1 in september, schaap wordt meteen weer zwanger, lam 2 doodgeboren in november?)
    // todo case te korte draagtijd

    // todo case levnr niet in db en geen aanvoer
    // todo gevallen "levnr in db, geen ouders" en "aanvoer met registratie ouders"
    // todo case vorige worp minder dan 6 maanden terug
    // todo case volgende worp meer dan 6 maanden later (aha, achterlopende administratie)

    // todo $scenario sensen? Alleen nodig als je de verschillen niet aan uitvoer kunt aflezen (BCB)
    
    // todo test voor knpZoek

    // todo case aantal_ubn > 1

    // todo fixtures voor tblRas, tblRasuser; vw_kzlOoien; resultvader; moment uitval; reden uitval; tblHok
    // ==> welke asserts passen daar bij? aantal option-tags tellen binnen een benoemde select?

    private function minimal($data = []) {
        return array_merge([
            'ingelogd' => 1,
            'knpSave' => 1,
            'kzlFase' => 1,
            'kzlOoi' => 1, // als txtGebdm uitblijft, moet array_worp ook gevuld worden. Dit is data-afhankelijk. TODO: maak fixture
            'txtGebdm' => '12-01-1920',
        ], $data);
    }

}
