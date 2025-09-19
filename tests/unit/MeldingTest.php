<?php

class MeldingTest extends IntegrationCase {
    # extend is luiheid; ik wil runfixture gebruiken.
    # Nu teveel gedoe om dat in een trait te stoppen

    private $db;

    public function setup(): void {
        $this->runfixture('melding-4');
        require_once "autoload.php";
        $_SERVER['HTTP_HOST'] = 'basq';
        $_SERVER['REQUEST_SCHEME'] = 'http';
        $_SERVER['REQUEST_URI'] = 'Meldingen.php';
    }

    # TODO: testcases waar "datum" voor de eerst mogelijke, of na de laatst mogelijke ligt.
    # Struikelblokken:
    # - bepaling van eerste moet uit een fixture volgen die "zoek_eerste_datum_stalop" goed laat werken
    # - bepaling van laatste volgt uit systeemdatum, dat is nog niet testbaar

    public function testWeigerNuldatum() {
        # GIVEN
        # om het uberhaupt te laten werken
        # ... dit zet nog allemaal globals, dus kan helaas niet in setup()
        require_once "basisfuncties.php";
        include "connect_db.php";
        $this->db = $db;
        # AND
        # specifiek voor de testcase
        $_POST = [
            'txtSchaapdm_4' => '00-00-0000',
        ];
        $code = 'GER'; # AAN AFV DOO GER GMD
        # WHEN
        ob_start();
        include "save_melding.php";
        $res = ob_get_clean();
        # THEN
        $this->assertTableWithPK('tblMelding', 'meldId', 4, ['fout' => 'De datum is onjuist']);
    }

    public function testWeigerTeVroeg() {
        # GIVEN
        # om het uberhaupt te laten werken
        # ... dit zet nog allemaal globals, dus kan helaas niet in setup()
        require_once "basisfuncties.php";
        include "connect_db.php";
        $this->db = $db;
        # AND
        # specifiek voor de testcase
        $_POST = [
            'txtSchaapdm_4' => '1966-04-04', # fixture geeft actie 1 in 1972
        ];
        $code = 'AAN'; # AAN AFV DOO GER GMD
        # WHEN
        ob_start();
        include "save_melding.php";
        $res = ob_get_clean();
        # THEN
        # merk op dat de datum in de foutmelding niet de exacte POST-waarde is
        $this->assertTableWithPK('tblMelding', 'meldId', 4, ['fout' => 'De datum (04-04-1966) kan niet voor 12-05-1972 liggen']);
    }

    public function testWeigerTeLaatGeboorte() {
        # GIVEN
        # om het uberhaupt te laten werken
        # ... dit zet nog allemaal globals, dus kan helaas niet in setup()
        require_once "basisfuncties.php";
        include "connect_db.php";
        $this->db = $db;
        # AND
        # specifiek voor de testcase
        $_POST = [
            # 'txtSchaapdm_4' => '02-01-2020',
            'txtSchaapdm_4' => date('d-m-Y', strtotime('1 day')),
        ];
        $code = 'AAN'; # AAN AFV DOO GER GMD
        # WHEN
        ob_start();
        include "save_melding.php";
        $res = ob_get_clean();
        # THEN
        $this->assertTableWithPK('tblMelding', 'meldId', 4, ['fout' => $_POST['txtSchaapdm_4'].' ligt voor RVO te ver in de toekomst']);
    }

    public function testAfvoer1dagIsOpTijd() {
        # GIVEN
        # om het uberhaupt te laten werken
        # ... dit zet nog allemaal globals, dus kan helaas niet in setup()
        require_once "basisfuncties.php";
        include "connect_db.php";
        $this->db = $db;
        # AND
        # specifiek voor de testcase
        $_POST = [
            'txtSchaapdm_4' => '03-01-2020',
        ];
        $code = 'AFV'; # AAN AFV DOO GER GMD
        # WHEN
        ob_start();
        include "save_melding.php";
        $res = ob_get_clean();
        # THEN
        $this->assertTableWithPK('tblMelding', 'meldId', 4, ['fout' =>null]);
    }

    public function testWeigerTeLaatAfvoer() {
        # GIVEN
        # om het uberhaupt te laten werken
        # ... dit zet nog allemaal globals, dus kan helaas niet in setup()
        require_once "basisfuncties.php";
        include "connect_db.php";
        $this->db = $db;
        # AND
        # specifiek voor de testcase
        $_POST = [
            # 'txtSchaapdm_4' => '05-01-2020',
            'txtSchaapdm_4' => date('d-m-Y', strtotime('4 days')),
        ];
        $code = 'AFV'; # AAN AFV DOO GER GMD
        # WHEN
        ob_start();
        include "save_melding.php";
        $res = ob_get_clean();
        # THEN
        $this->assertTableWithPK('tblMelding', 'meldId', 4, ['fout' => $_POST['txtSchaapdm_4'].' ligt voor RVO te ver in de toekomst']);
    }

    public function testWeigerNuldatumEnNullevensnummer() {
        # GIVEN
        # om het uberhaupt te laten werken
        # ... dit zet nog allemaal globals, dus kan helaas niet in setup()
        require_once "basisfuncties.php";
        include "connect_db.php";
        $this->db = $db;
        # AND
        # specifiek voor de testcase
        $_POST = [
            'txtSchaapdm_4' => '00-00-0000',
            'txtLevnr_4' => '0000000000',
        ];
        $code = 'GER'; # AAN AFV DOO GER GMD
        # WHEN
        ob_start();
        include "save_melding.php";
        $res = ob_get_clean();
        # THEN
        $this->assertTableWithPK('tblMelding', 'meldId', 4, ['fout' => 'De datum is onjuist en het levensnummer is onjuist']);
    }

    public function testWeigerNuldatumEnLevnrDuplicaat() {
        # GIVEN
        # om het uberhaupt te laten werken
        # ... dit zet nog allemaal globals, dus kan helaas niet in setup()
        require_once "basisfuncties.php";
        include "connect_db.php";
        $this->db = $db;
        # AND
        # specifiek voor de testcase
        $_POST = [
            'txtSchaapdm_4' => '00-00-0000',
            'txtLevnr_4' => '524288',
            'kzlSekse_4' => 'ooi',
            'kzlHerk_4' => '1332',
            'kzlBest_4' => '271',
            'chbSkip_4' => '0',
        ];
        $code = 'GER'; # AAN AFV DOO GER GMD
        # WHEN
        ob_start();
        include "save_melding.php";
        $res = ob_get_clean();
        # THEN
        $this->assertTableWithPK('tblMelding', 'meldId', 4, ['fout' => 'De datum is onjuist en levensummer 524288 bestaat al']);
    }

    public function testWeigerNuldatumEnKortLevnr() {
        # GIVEN
        # om het uberhaupt te laten werken
        # ... dit zet nog allemaal globals, dus kan helaas niet in setup()
        require_once "basisfuncties.php";
        include "connect_db.php";
        $this->db = $db;
        # AND
        # specifiek voor de testcase
        $_POST = [
            'txtSchaapdm_4' => '00-00-0000',
            'txtLevnr_4' => '401404',
        ];
        $code = 'GER'; # AAN AFV DOO GER GMD
        # WHEN
        ob_start();
        include "save_melding.php";
        $res = ob_get_clean();
        # THEN
        $this->assertTableWithPK('tblMelding', 'meldId', 4, ['fout' => 'De datum is onjuist en 401404 is geen 12 karakters lang']);
    }

    public function testWeigerNuldatumEnLetterLevnr() {
        # GIVEN
        # om het uberhaupt te laten werken
        # ... dit zet nog allemaal globals, dus kan helaas niet in setup()
        require_once "basisfuncties.php";
        include "connect_db.php";
        $this->db = $db;
        # AND
        # specifiek voor de testcase
        $_POST = [
            'txtSchaapdm_4' => '00-00-0000',
            'txtLevnr_4' => '10000000000X',
        ];
        $code = 'GER'; # AAN AFV DOO GER GMD
        # WHEN
        ob_start();
        include "save_melding.php";
        $res = ob_get_clean();
        # THEN
        $this->assertTableWithPK('tblMelding', 'meldId', 4, ['fout' => 'De datum is onjuist en 10000000000X bevat een letter']);
    }

    public function testGeboorteLevnr0() {
        # TODO deze case gecombineerd met verkeerde dag
        # GIVEN
        # om het uberhaupt te laten werken
        # ... dit zet nog allemaal globals, dus kan helaas niet in setup()
        require_once "basisfuncties.php";
        include "connect_db.php";
        $this->db = $db;
        # AND
        # specifiek voor de testcase
        $_POST = [
            # 'kzlDef_' => 'q', # als deze sleutel ontbreekt wordt fldDef 'N' <== testcase
            'txtSchaapdm_4' => '13-01-1976',
            'txtLevnr_4' => '0000000000',
        ];
        $code = 'GER'; # AAN AFV DOO GER GMD
        # WHEN
        ob_start();
        include "save_melding.php";
        $res = ob_get_clean();
        # THEN
        $this->assertTableWithPK('tblMelding', 'meldId', 4, ['fout' => 'Het levensnummer is onjuist']);
    }

    public function testGeboorteLevnrDuplicaat() {
        # TODO deze case gecombineerd met verkeerde dag
        # GIVEN
        # om het uberhaupt te laten werken
        # ... dit zet nog allemaal globals, dus kan helaas niet in setup()
        require_once "basisfuncties.php";
        include "connect_db.php";
        $this->db = $db;
        # AND
        # specifiek voor de testcase
        $_POST = [
            'txtSchaapdm_4' => '13-01-1976',
            'txtLevnr_4' => '524288',
            'kzlSekse_4' => 'ooi',
            'kzlHerk_4' => '1332',
            'kzlBest_4' => '271',
            'chbSkip_4' => '0',
        ];
        $code = 'GER'; # AAN AFV DOO GER GMD
        # WHEN
        ob_start();
        include "save_melding.php";
        $res = ob_get_clean();
        # THEN
        $this->assertTableWithPK('tblMelding', 'meldId', 4, ['fout' => 'Levensummer 524288 bestaat al']);
    }

    public function testGeboorteLevnrSpelfout() {
        # TODO deze case gecombineerd met verkeerde dag
        # GIVEN
        # om het uberhaupt te laten werken
        # ... dit zet nog allemaal globals, dus kan helaas niet in setup()
        require_once "basisfuncties.php";
        include "connect_db.php";
        $this->db = $db;
        # AND
        # specifiek voor de testcase
        $_POST = [
            'txtSchaapdm_4' => '13-01-1976',
            'txtLevnr_4' => '10000000000X',
            'kzlSekse_4' => 'ooi',
            'kzlHerk_4' => '1332',
            'kzlBest_4' => '271',
            'chbSkip_4' => '0',
        ];
        $code = 'GER'; # AAN AFV DOO GER GMD
        # WHEN
        ob_start();
        include "save_melding.php";
        $res = ob_get_clean();
        # THEN
        $this->assertTableWithPK('tblMelding', 'meldId', 4, ['fout' => '10000000000X bevat een letter']);
    }

    public function testGeboorteLevnrTekortNietSkipped() {
        # GIVEN
        # om het uberhaupt te laten werken
        # ... dit zet nog allemaal globals, dus kan helaas niet in setup()
        require_once "basisfuncties.php";
        include "connect_db.php";
        $this->db = $db;
        # AND
        # specifiek voor de testcase
        $_POST = [
            # 'kzlDef_' => 'q', # als deze sleutel ontbreekt wordt fldDef 'N' <== best aparte testcase maken
            'txtSchaapdm_4' => '13-01-1976',
            'txtLevnr_4' => '131072',
            'kzlSekse_4' => 'ooi',
            'kzlHerk_4' => '1332',
            'kzlBest_4' => '271',
        ];
        $code = 'GER'; # AAN AFV DOO GER GMD
        # WHEN
        ob_start();
        include "save_melding.php";
        $res = ob_get_clean();
        # THEN
        $this->assertTableWithPK('tblMelding', 'meldId', 4, ['fout' => '131072 is geen 12 karakters lang']);
        $this->assertTableWithPK('tblStal', 'stalId', 49, ['rel_herk' => 1332, 'rel_best' => 271]);
        $this->assertTableWithPK('tblRequest', 'reqId', 5, ['def' => 'N']);
    }

    public function testGeboorteOngeldigLevnrSkipped() {
        # GIVEN
        # om het uberhaupt te laten werken
        # ... dit zet nog allemaal globals, dus kan helaas niet in setup()
        require_once "basisfuncties.php";
        include "connect_db.php";
        $this->db = $db;
        # AND
        # specifiek voor de testcase
        $_POST = [
            'kzlDef_' => 'N', # (N,J) default N.
            'txtSchaapdm_4' => '13-01-1976',
            'txtLevnr_4' => '131072', # dit moet voorkomen in tblSchaap. Aha.
            'kzlSekse_4' => 'ooi',
            'kzlHerk_4' => '1332',
            'kzlBest_4' => '271',
            'chbSkip_4' => '1',
        ];
        $code = 'GER'; # AAN AFV DOO GER GMD
        # WHEN
        ob_start();
        include "save_melding.php";
        $res = ob_get_clean();
        # THEN
        $this->assertTableWithPK('tblMelding', 'meldId', 4, ['fout' => null]);
        $this->assertTableWithPK('tblStal', 'stalId', 49, ['rel_herk' => 1332, 'rel_best' => 271]);
        $this->assertTableWithPK('tblRequest', 'reqId', 5, ['def' => 'N']);
    }

    public function testAfvOngeldigLevnrSkipped() {
        # GIVEN
        # om het uberhaupt te laten werken
        # ... dit zet nog allemaal globals, dus kan helaas niet in setup()
        require_once "basisfuncties.php";
        include "connect_db.php";
        $this->db = $db;
        # AND
        # specifiek voor de testcase
        $_POST = [
            'kzlDef_' => 'N',
            'txtSchaapdm_4' => '13-01-1976',
            'txtLevnr_4' => '131072', # dit moet voorkomen in tblSchaap. Aha.
            'kzlSekse_4' => 'ooi',
            'kzlHerk_4' => '1332',
            'kzlBest_4' => '271',
            'chbSkip_4' => '1',
        ];
        $code = 'AFV'; # AAN AFV DOO GER GMD
        # WHEN
        ob_start();
        include "save_melding.php";
        $res = ob_get_clean();
        # THEN
        $this->assertTableWithPK('tblMelding', 'meldId', 4, ['fout' => null]);
        $this->assertTableWithPK('tblStal', 'stalId', 49, ['rel_herk' => 1332, 'rel_best' => 271]);
        $this->assertTableWithPK('tblRequest', 'reqId', 5, ['def' => 'N']);
    }

    public function testVerwijderdHersteld() {
        # GIVEN
        # om het uberhaupt te laten werken
        # ... dit zet nog allemaal globals, dus kan helaas niet in setup()
        require_once "basisfuncties.php";
        include "connect_db.php";
        $this->db = $db;
        # AND
        # specifiek voor de testcase
        $_POST = [
            'kzlDef_' => 'N', # (N,J) default N. Als deze sleutel ontbreekt wordt fldDef 'N' <== testcase
            'txtSchaapdm_4' => '13-01-1976',
            'kzlSekse_4' => 'ooi',
            'kzlHerk_4' => '1332',
            'kzlBest_4' => '271',
            'chbSkip_4' => '0',
        ];
        $this->runfixture('melding-4-skip');
        $code = 'AFV'; # AAN AFV DOO GER GMD
        # WHEN
        ob_start();
        include "save_melding.php";
        $res = ob_get_clean();
        # THEN
        $this->assertTableWithPK('tblMelding', 'meldId', 4, ['fout' => null]);
        // rel_best blijft de fixture-waarde
        $this->assertTableWithPK('tblStal', 'stalId', 49, ['rel_herk' => 1332, 'rel_best' => 13]);
        $this->assertTableWithPK('tblRequest', 'reqId', 5, ['def' => 'N']);
    }

    public function testWijzigLevensnummer() {
        # GIVEN
        # om het uberhaupt te laten werken
        # ... dit zet nog allemaal globals, dus kan helaas niet in setup()
        require_once "basisfuncties.php";
        include "connect_db.php";
        $this->db = $db;
        # AND
        # specifiek voor de testcase
        $_POST = [
            'txtSchaapdm_4' => '13-01-1976',
            'txtLevnr_4' => '123456789012',
        ];
        $code = 'AFV'; # AAN AFV DOO GER GMD
        # WHEN
        ob_start();
        include "save_melding.php";
        $res = ob_get_clean();
        # THEN
        $this->assertTableWithPK('tblSchaap', 'schaapId', 72, ['levensnummer' => '123456789012']);
    }

    public function testWijzigGeslacht() {
        # GIVEN
        # om het uberhaupt te laten werken
        # ... dit zet nog allemaal globals, dus kan helaas niet in setup()
        require_once "basisfuncties.php";
        include "connect_db.php";
        $this->db = $db;
        # AND
        # specifiek voor de testcase
        $_POST = [
            'txtSchaapdm_4' => '13-01-1976',
            'kzlSekse_4' => 'ram', # in de fixture is het een ooi
        ];
        $code = 'AFV'; # AAN AFV DOO GER GMD
        # WHEN
        ob_start();
        include "save_melding.php";
        $res = ob_get_clean();
        # THEN
        $this->assertTableWithPK('tblSchaap', 'schaapId', 72, ['geslacht' => 'ram']);
    }

    public function testHerkomstNietGezet() {
        # TODO: cases die combineren met wrong_levnr en wrong_dag
        # GIVEN
        # om het uberhaupt te laten werken
        # ... dit zet nog allemaal globals, dus kan helaas niet in setup()
        require_once "basisfuncties.php";
        include "connect_db.php";
        $this->db = $db;
        # AND
        # specifiek voor de testcase
        $_POST = [
            'txtSchaapdm_4' => '13-01-1976',
            # geen kzlHerk in het request
        ];
        $this->runfixture('melding-4-AAN');
        $code = 'AAN'; # AAN AFV DOO GER GMD
        # WHEN
        ob_start();
        include "save_melding.php";
        $res = ob_get_clean();
        # THEN
        $this->assertTableWithPK('tblMelding', 'meldId', 4, ['fout' => 'Herkomst moet zijn gevuld.']);
    }

    public function testHerkomstNietGezetEnLevnrFout() {
        # TODO: cases die combineren met wrong_levnr en wrong_dag
        # GIVEN
        # om het uberhaupt te laten werken
        # ... dit zet nog allemaal globals, dus kan helaas niet in setup()
        require_once "basisfuncties.php";
        include "connect_db.php";
        $this->db = $db;
        # AND
        # specifiek voor de testcase
        $_POST = [
            'txtSchaapdm_4' => '13-01-1976',
            'txtLevnr_4' => 'dertien',
            # geen kzlHerk in het request
        ];
        $this->runfixture('melding-4-AAN');
        $code = 'AAN'; # AAN AFV DOO GER GMD
        # WHEN
        ob_start();
        include "save_melding.php";
        $res = ob_get_clean();
        # THEN
        $this->assertTableWithPK('tblMelding', 'meldId', 4, ['fout' => 'Het levensnummer is onjuist en herkomst moet zijn gevuld.']);
    }

    public function testHerkomstNietGezetEnDatumFout() {
        # TODO: cases die combineren met wrong_levnr en wrong_dag
        # GIVEN
        # om het uberhaupt te laten werken
        # ... dit zet nog allemaal globals, dus kan helaas niet in setup()
        require_once "basisfuncties.php";
        include "connect_db.php";
        $this->db = $db;
        # AND
        # specifiek voor de testcase
        $_POST = [
            'txtSchaapdm_4' => '00-00-0000',
            # geen kzlHerk in het request
        ];
        $this->runfixture('melding-4-AAN');
        $code = 'AAN'; # AAN AFV DOO GER GMD
        # WHEN
        ob_start();
        include "save_melding.php";
        $res = ob_get_clean();
        # THEN
        $this->assertTableWithPK('tblMelding', 'meldId', 4, ['fout' => 'De datum is onjuist en herkomst moet zijn gevuld.']);
    }

    public function testBestemmingNietGezet() {
        # GIVEN
        # om het uberhaupt te laten werken
        # ... dit zet nog allemaal globals, dus kan helaas niet in setup()
        require_once "basisfuncties.php";
        include "connect_db.php";
        $this->db = $db;
        # AND
        # specifiek voor de testcase
        $_POST = [
            'txtSchaapdm_4' => '13-01-1976',
            # geen kzlBest in het request
        ];
        $this->runfixture('melding-4-AFV');
        $code = 'AAN'; # AAN AFV DOO GER GMD
        # WHEN
        ob_start();
        include "save_melding.php";
        $res = ob_get_clean();
        # THEN
        $this->assertTableWithPK('tblMelding', 'meldId', 4, ['fout' => 'Bestemming moet zijn gevuld.']);
    }

    public function testBestemmingNietGezetEnLevnrFout() {
        # GIVEN
        # om het uberhaupt te laten werken
        # ... dit zet nog allemaal globals, dus kan helaas niet in setup()
        require_once "basisfuncties.php";
        include "connect_db.php";
        $this->db = $db;
        # AND
        # specifiek voor de testcase
        $_POST = [
            'txtSchaapdm_4' => '13-01-1976',
            'txtLevnr_4' => 'dertien',
            # geen kzlBest in het request
        ];
        $this->runfixture('melding-4-AFV');
        $code = 'AAN'; # AAN AFV DOO GER GMD
        # WHEN
        ob_start();
        include "save_melding.php";
        $res = ob_get_clean();
        # THEN
        $this->assertTableWithPK('tblMelding', 'meldId', 4, ['fout' => 'Het levensnummer is onjuist en bestemming moet zijn gevuld.']);
    }

    public function testBestemmingNietGezetEnDatumFout() {
        # TODO: cases die combineren met wrong_levnr en wrong_dag
        # GIVEN
        # om het uberhaupt te laten werken
        # ... dit zet nog allemaal globals, dus kan helaas niet in setup()
        require_once "basisfuncties.php";
        include "connect_db.php";
        $this->db = $db;
        # AND
        # specifiek voor de testcase
        $_POST = [
            'txtSchaapdm_4' => '00-00-0000',
            # geen kzlBest in het request
        ];
        $this->runfixture('melding-4-AFV');
        $code = 'AAN'; # AAN AFV DOO GER GMD
        # WHEN
        ob_start();
        include "save_melding.php";
        $res = ob_get_clean();
        # THEN
        $this->assertTableWithPK('tblMelding', 'meldId', 4, ['fout' => 'De datum is onjuist en bestemming moet zijn gevuld.']);
    }

}
