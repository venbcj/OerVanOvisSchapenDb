<?php

class InvSchaapTest extends IntegrationCase {

    private const PAGE = '/InvSchaap.php';

    private const LEVNR_NIET_IN_DB = '9';
    private const LEVNR_IN_FIXTURE = '4';
    private const EEN_RAS = '3';

    public function teardown(): void {
        parent::teardown();
        unset($GLOBALS['schaap_gateway']);
    }

    public function testPost() {
        $this->post(self::PAGE, ['ingelogd' => 1, 'txtLevnr' => 1]);
        $this->assertNoNoise();
        // TODO: #0004113 case met uitgeschaard
    }

    public function testSaveZonderLevnr() {
        $this->post(self::PAGE, $this->minimal());
        $this->assertNoNoise();
        // dit test ook 1 validatie, omdat levnr en txtDmuitv beide ontbreken.
        $this->assertFout('Bij overlijden moet datum t.b.v. uitval zijn ingevuld.');
    }

    public function testSaveMetLevnr() {
        $this->uses_db();
        $this->post(self::PAGE, $this->minimal([
            'txtLevnr' => self::LEVNR_NIET_IN_DB,
        ]));
        $this->assertNoNoise();
        $this->assertFout('Het geslacht moet zijn ingevuld.');
    }

    public function testValidatieRas() {
        $this->post(self::PAGE, $this->minimal([
            'txtLevnr' => self::LEVNR_NIET_IN_DB,
            'txtDmuitv' => '1-1-1980',
            'kzlSekse' => 'ram',
        ]));
        $this->assertNoNoise();
        $this->assertFout('Het ras moet zijn ingevuld.');
    }

    public function testValidatieMoeder() {
        $this->post(self::PAGE, $this->minimal([
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

    public function testValidatieGeenOverlijden() {
        $this->post(self::PAGE, $this->minimal([
            'txtLevnr' => self::LEVNR_NIET_IN_DB,
            'kzlSekse' => 'ram',
            'kzlFase' => 'lam',
            'kzlRas' => self::EEN_RAS,
            'kzlMoment' => '4',
            // testcase = niet in de post: txtUitvdm
        ]));
        $this->assertNoNoise();
        $this->assertFout('Bij overlijden moet datum t.b.v. uitval zijn ingevuld.');
        // TODO: er zijn nog twee redenen voor deze fout
    }

    public function testValidatiePrematuurOverlijden() {
        $this->post(self::PAGE, $this->minimal([
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

    public function testValidatieOverlijdenVoorAanschaf() {
        $this->post(self::PAGE, $this->minimal([
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

    public function testValidatiePrematureAanschaf() {
        $this->post(self::PAGE, $this->minimal([
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

    public function testValidatieOngeplaatstLam() {
        $this->post(self::PAGE, $this->minimal([
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

    public function testValidatieAlAanwezig() {
        $this->runfixture('schaap-4');
        $this->post(self::PAGE, $this->minimal([
            'txtLevnr' => self::LEVNR_IN_FIXTURE,
            'kzlSekse' => 'ram',
            'kzlFase' => 'lam',
            'kzlMoment' => 5, // <= dit is 1 van de mogelijkheden. TODO: ook met uitvaldatum of reden
            'txtUitvdm' => '1-1-2001',
        ]));
        $this->assertNoNoise();
        $this->assertFout('Dit dier staat al op de stallijst.');
    }

    public function testValidatieVolwassenAanschaf() {
        $this->post(self::PAGE, $this->minimal([
            'txtLevnr' => self::LEVNR_NIET_IN_DB,
            'kzlSekse' => 'ram',
            'kzlFase' => 'vader', // nog een testcase evenzo 'moeder'
            'kzlRas' => self::EEN_RAS,
            'txtGebdm' => '5-5-1970',
        ]));
        $this->assertNoNoise();
        $this->assertFout('Bij invoer van een volwassen dier is de aanschafdatum verplicht.');
    }

    public function testValidatieGeborenVoorAanschaf() {
        $this->runfixture('schaap-4');
        $this->runfixture('moeder-4');
        $this->post(self::PAGE, $this->minimal([
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

    public function testValidatieGeborenNaAfvoer() {
        $this->runfixture('schaap-4');
        $this->runfixture('moeder-4-afgevoerd');
        $this->post(self::PAGE, $this->minimal([
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

    public function testValidatieGeborenNaEerstedm() {
        $this->runfixture('schaap-4');
        $this->runfixture('moeder-4');
        $fake = new SchaapGatewayStub();
        $fake->prime('zoek_eerder_levensnummer', [
            [
                'schaapId' => 6,
                'mdrId' => null,
                'volwId' => null,
                'dmgeb' => null,
                'dmeerste' => '1920-01-01', // de veroorzaker: txtGebdm ligt hier na
                'eerstedm' => '01-01-1920',
                'dmaanw' => null,
                'dmafv' => null,
                'afvdm' => null,
            ]
        ]);
        $GLOBALS['schaap_gateway'] = $fake;
        $this->post(self::PAGE, $this->minimal([
            # 'txtLevnr' => self::LEVNR_NIET_IN_DB, # dit levert fout op: schaapId wordt niet gezet en vervolgens bevraagd.
            'txtLevnr' => 6,
            'kzlSekse' => 'ram',
            'kzlFase' => 'lam',
            'kzlRas' => self::EEN_RAS,
            'txtGebdm' => '5-5-2000',
            'kzlHok' => 3,
            'kzlOoi' => 9,
        ]));
        $this->assertNoNoise();
        $this->assertFout('Geboortedatum kan niet na 01-01-1920 liggen.');
    }

    // todo: wat betekent dit? aanvoer is toch altijd voor afvoer?
    public function testValidatieAanvoerVoorAfvoer() {
        $this->runfixture('schaap-4');
        $this->runfixture('moeder-4');
        $fake = new SchaapGatewayStub();
        $fake->prime('zoek_eerder_levensnummer', [
            [
                'schaapId' => 6,
                'mdrId' => null,
                'volwId' => null,
                'dmgeb' => null,
                'dmeerste' => '2020-01-01',
                'eerstedm' => '01-01-1920',
                'dmaanw' => null,
                'dmafv' => '2010-01-01', // de veroorzaker: txtDmaanv ligt hier voor
                'afvdm' => '01-01-2010',
            ]
        ]);
        $GLOBALS['schaap_gateway'] = $fake;
        $this->post(self::PAGE, $this->minimal([
            'txtLevnr' => 6,
            'kzlSekse' => 'ram',
            'kzlFase' => 'moeder',
            'kzlRas' => self::EEN_RAS,
            'txtAanv' => '01-01-2000',
            'kzlHok' => 3,
            'kzlOoi' => 9,
        ]));
        $this->assertNoNoise();
        $this->assertFout('Aanvoerdatum kan niet voor 01-01-2010 liggen.');
    }

    public function testValidatieDood() {
        $this->runfixture('schaap-4-dood');
        $fake = new SchaapGatewayStub();
        $fake->prime('zoek_eerder_levensnummer', [
            [
                'schaapId' => 6,
                'mdrId' => null,
                'volwId' => null,
                'dmgeb' => null,
                'dmeerste' => '2020-01-01',
                'eerstedm' => '01-01-1920',
                'dmaanw' => null,
                'dmafv' => '2010-01-01',
                'afvdm' => '01-01-2010',
            ]
        ]);
        $GLOBALS['schaap_gateway'] = $fake;
        $this->post(self::PAGE, $this->minimal([
            'txtLevnr' => 4,
            'kzlSekse' => 'ram',
            'kzlFase' => 'moeder',
            'kzlReden' => 1,
            'txtUitvdm' => '2010-12-12',
            'kzlRas' => self::EEN_RAS,
            'txtAanv' => '05-05-2010',
            'kzlHok' => 3,
            'kzlOoi' => 9,
        ]));
        $this->assertNoNoise();
        $this->assertFout('Dit is een overleden schaap.');
    }

    public function testValidatieLevensnummerBestaat() {
        $this->runfixture('schaap-4');
        $fake = new SchaapGatewayStub();
        $fake->prime('zoek_eerder_levensnummer', [
            [
                'schaapId' => 6,
                'mdrId' => null,
                'volwId' => null,
                'dmgeb' => null,
                'dmeerste' => '2020-01-01',
                'eerstedm' => '01-01-1920',
                'dmaanw' => null,
                'dmafv' => '2010-01-01',
                'afvdm' => '01-01-2010',
            ]
        ]);
        $GLOBALS['schaap_gateway'] = $fake;
        $this->post(self::PAGE, $this->minimal([
            'txtLevnr' => 6,
            'kzlSekse' => 'ram',
            'kzlFase' => 'moeder',
            'kzlReden' => 1,
            'txtUitvdm' => '2010-12-12',
            'kzlRas' => self::EEN_RAS,
            'txtAanv' => '05-05-2010',
            'kzlHok' => 3,
            'kzlOoi' => 9,
        ]));
        $this->assertNoNoise();
        $this->assertFout('Dit levensnummer bestaat al.');
    }

    // TODO: case korte draagtijd (BCB: is dat juist? Kan dit ook: lam 1 in september, schaap wordt meteen weer zwanger, lam 2 doodgeboren in november?)
    // TODO: case te korte draagtijd

    // TODO: case levnr niet in db en geen aanvoer
    // TODO: gevallen "levnr in db, geen ouders" en "aanvoer met registratie ouders"
    // TODO: case vorige worp minder dan 6 maanden terug
    // TODO: case volgende worp meer dan 6 maanden later (aha, achterlopende administratie)

    // TODO: $scenario sensen? Alleen nodig als je de verschillen niet aan uitvoer kunt aflezen (BCB)
    
    // TODO: test voor knpZoek

    // TODO: case aantal_ubn > 1
    public function testMeerdereUbnsToontKeuzelijst() {
        $this->runfixture('user-1-more-ubns');
        $this->get(self::PAGE, ['ingelogd' => 1]);
        $this->assertOptieCount('kzlUbn', 3);
    }

    // TODO ontruisen: test gaat over ubn, maar neemt het formulier van een validatiefout uit GeborenNaAfvoer
    public function testMeerdereUbnsKeuze() {
        $this->runfixture('schaap-4');
        $this->runfixture('moeder-4-afgevoerd');
        $this->post(self::PAGE, $this->minimal([
            'txtLevnr' => self::LEVNR_NIET_IN_DB,
            'kzlSekse' => 'ram',
            'kzlFase' => 'lam',
            'kzlRas' => self::EEN_RAS,
            'txtGebdm' => '5-5-1980', // schaap 9, de moeder, is in 1970 afgevoerd volgens fixture moeder-4-afgevoerd
            'kzlHok' => 3,
            'kzlOoi' => 9,
            'kzlUbn' => 1,
        ]));
        $this->assertNoNoise();
        $this->assertFout('Geboortedatum kan niet na afvoerdatum van moederdier liggen.');
    }


    // TODO: fixtures voor tblRas, tblRasuser; vw_kzlOoien; resultvader; moment uitval; reden uitval; tblHok
    // ==> welke asserts passen daar bij? aantal option-tags tellen binnen een benoemde select?

    public function testPostGroenLam() {
        $this->post(self::PAGE, $this->minimal([
            'txtLevnr' => '4',
            'kzlSekse' => 1,
            'kzlRas' => 1,
            // met alleen deze drie velden loop ik zo door naar een "undefined variable volwId".
            // het scenario blijkt aanvoer_ouder
            'kzlFase' => 'lam', // niet genoeg: "plaats het lam ook nog in een verblijf"
            'kzlHok' => 1, // crasht op ongedefinieerde kzlReden
            'kzlReden' => 1, // maar als ik een reden opgeef is het blijkbaar uitval, en is txtDmuitv nodig. Oh. txtUitvdm
            'txtUitvdm' => '2010-01-01', // okee, nu crasht het op "undefined variable schaapId". Heb je een ingevoerd record nodig om er een in te voeren?
        ]));
        $this->assertNotFout();
    }

    # inschakelen in overleg met BvdV
    /*
    Laten we uitgaan van Scenario 'aankoop van een moederdier' dat niet voorkomt in de database. Dit is het meest eenvoudige scenario. Dit dier heeft dan geen ouders, zit niet in een verblijf en heeft verder geen extra info zoals geboortedatum, aankoopgewicht.

Verplichte velden zijn 
Levensnummer bijv 100073436679
Generatie moeder = moeder -> key-waarde keuzelijst 'moeder'
Geslacht  = ooi -> key-waarde keuzelijst 'ooi'
Ras  = Rijnlam -> key-waarde keuzelijst '77'
Aanvoerdatum (txtAanv) bijv 2026-01-19

Volgens mij moet je in de database alleen een geldige gebruiker hebben in tblLeden en in tblUbn moet een 7 cijferig ubn bestaan van deze gebruiker (lidId)
Functioneel moet het keuzelijst Ras wel Rijnlam bevatten.
    In tblRas -> rasId = 77, Ras = Rijnlam en actief = 1
    in tblRasuser -> LidId = 1 (lidId in tblLeden), rasId = 77 en actief = 1

    Kun je hiermee uit de voeten?
*/
    public function testPostGroenOoi() {
        $this->post(self::PAGE, $this->minimal([
            'txtLevnr' => '100073436679',
            'kzlSekse' => 'ooi',
            'kzlRas' => 77, // rijnlam
            'txtAanv' => '2026-01-19',
            'kzlFase' => 'moeder',
            # 'txtGebdm' => '2010-01-01',
        ]));
        $this->assertNotFout();
    }

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
