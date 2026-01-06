<?php

class HistorieGatewayTest extends GatewayCase {

    public static $sutname = 'HistorieGateway';

    public function test_zoek_einddatum() {
        // actie 12 is afgeleverd
        $this->runSQL("INSERT INTO tblHistorie(actId, stalId, skip, datum) VALUES(12, 1, 0, '2010-01-01')");
        $res = $this->sut->zoek_einddatum(1);
        $this->assertEquals(['2010-01-01', '01-01-2010'], $res);
    }

    public function test_zoek_eerste_datum_stalop_leeg() {
        $actual = $this->sut->zoek_eerste_datum_stalop(1);
        $this->assertEquals([null, null], $actual);
    }

    public function test_zoek_eerste_datum_stalop_data() {
        $this->runSQL("INSERT INTO tblStal(stalId, schaapId) VALUES(1,1)");
        $this->runSQL("INSERT INTO tblMelding(meldId, hisId) VALUES(1,2)");
        $this->runSQL("INSERT INTO tblHistorie(hisId, actId, stalId, skip, datum) VALUES(1, 1, 1, 0, '2010-01-01')");
        $this->runSQL("INSERT INTO tblHistorie(hisId, actId, stalId, skip, datum) VALUES(2, 1, 1, 0, '2010-01-01')");
        $actual = $this->sut->zoek_eerste_datum_stalop(1);
        $this->assertEquals(['2010-01-01', '01-01-2010'], $actual);
    }

    public function test_setDatum() {
        $this->runSQL("INSERT INTO tblHistorie(hisId, actId, stalId, skip, datum) VALUES(2, 1, 1, 0, '2010-01-01')");
        $this->runSQL("INSERT INTO tblMelding(meldId, hisId) VALUES(1,2)");
        $this->sut->setDatum('2020-02-02', 1);
        // THEN
        // moet je wel snappen dat record "2" in tblHistorie wordt gewijzigd.
        $this->assertTableWithPK('tblHistorie', 'hisId', 2, ['datum' => '2020-02-02']);
    }

    public function test_zoek_dekdatum_leeg() {
        $this->assertEquals([null, null], $this->sut->zoek_dekdatum(1));
    }

    public function test_zoek_dekdatum_data() {
        $this->runSQL("INSERT INTO tblHistorie(hisId, actId, stalId, skip, datum) VALUES(2, 1, 1, 0, '2010-01-01')");
        $this->assertEquals(['01-01-2010', 2010], $this->sut->zoek_dekdatum(2));
    }

    public function test_zoek_drachtdatum() {
        $drachtMoment = null;
        $result = $this->sut->zoek_drachtdatum($drachtMoment);
        $this->assertNotFalse($result);
    }

    public function test_zoek_jaartal_eerste_dekking_dracht() {
        $een_startjaar_eerder_gebruiker = null;
        $result = $this->sut->zoek_jaartal_eerste_dekking_dracht(self::LIDID, $een_startjaar_eerder_gebruiker);
        $this->assertNotFalse($result);
    }

    public function test_zoek_datum_verblijf_tijdens_dekking() {
        $mdrId = null;
        $dmdek = null;
        $result = $this->sut->zoek_datum_verblijf_tijdens_dekking(self::LIDID, $mdrId, $dmdek);
        $this->assertNotFalse($result);
    }

    public function test_zoek_hisId_verblijf_tijdens_dekking() {
        $mdrId = null;
        $date_verblijf = null;
        $result = $this->sut->zoek_hisId_verblijf_tijdens_dekking(self::LIDID, $mdrId, $date_verblijf);
        $this->assertNotFalse($result);
    }

    public function test_zoek_verblijf_tijdens_dekking() {
        $hisId_verblijf = null;
        $dmdek = null;
        $result = $this->sut->zoek_verblijf_tijdens_dekking(self::LIDID, $hisId_verblijf, $dmdek);
        $this->assertNotFalse($result);
    }

    public function test_dagwegingen() {
        $schaapId = null;
        $datum = null;
        $result = $this->sut->dagwegingen(self::LIDID, $schaapId, $datum);
        $this->assertNotFalse($result);
    }

    public function test_eerste_datum_schaap() {
        $stalId = null;
        $result = $this->sut->eerste_datum_schaap($stalId);
        $this->assertNotFalse($result);
    }

    public function test_laatste_datum_schaap() {
        $stalId = null;
        $result = $this->sut->laatste_datum_schaap($stalId);
        $this->assertNotFalse($result);
    }

    public function test_wegen_invoeren() {
        $stalId = null;
        $datum = null;
        $newkg = null;
        $result = $this->sut->wegen_invoeren($stalId, $datum, $newkg);
        $this->assertNotFalse($result);
    }

    public function test_herstel_invoeren() {
        $stalId = null;
        $datum = null;
        $kg = null;
        $actId = null;
        $result = $this->sut->herstel_invoeren($stalId, $datum, $kg, $actId);
        $this->assertNotFalse($result);
    }

    public function test_medicijn_invoeren() {
        $stalId = null;
        $datum = null;
        $result = $this->sut->medicijn_invoeren($stalId, $datum);
        $this->assertNotFalse($result);
    }

    public function test_weegaantal() {
        $schaapId = null;
        $result = $this->sut->weegaantal(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_weeg() {
        $schaapId = null;
        $result = $this->sut->weeg(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_geboorte() {
        $schaapId = null;
        $result = $this->sut->zoek_geboorte($schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_eerste_datum() {
        $schaapId = null;
        $result = $this->sut->zoek_eerste_datum($schaapId);
        $this->assertNotFalse($result);
    }

    public function test_insert_geboorte() {
        $stalId = null;
        $datum = null;
        $result = $this->sut->insert_geboorte($stalId, $datum);
        $this->assertNotFalse($result);
    }

    public function test_zoek_aanwasdatum() {
        $schaapId = null;
        $result = $this->sut->zoek_aanwasdatum($schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_nietvoor_datum() {
        $schaapId = null;
        $result = $this->sut->zoek_nietvoor_datum(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_nietvoor_datum_456() {
        $schaapId = null;
        $result = $this->sut->zoek_nietvoor_datum_456(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_afvoer_nietvoor_datum() {
        $schaapId = null;
        $result = $this->sut->zoek_afvoer_nietvoor_datum(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_nietna_datum() {
        $schaapId = null;
        $result = $this->sut->zoek_nietna_datum(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_update_aanwas() {
        $hisId = null;
        $datum = null;
        $result = $this->sut->update_aanwas($hisId, $datum);
        $this->assertNotFalse($result);
    }

    public function test_zoek_speendm() {
        $schaapId = null;
        $result = $this->sut->zoek_speendm($schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_speen_nietvoor_datum() {
        $schaapId = null;
        $result = $this->sut->zoek_speen_nietvoor_datum(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_controle_nietna_datum() {
        $schaapId = null;
        $result = $this->sut->controle_nietna_datum(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_update_speendatum() {
        $hisId = null;
        $datum = null;
        $result = $this->sut->update_speendatum($hisId, $datum);
        $this->assertNotFalse($result);
    }

    public function test_zoek_speenkg() {
        $schaapId = null;
        $result = $this->sut->zoek_speenkg($schaapId);
        $this->assertNotFalse($result);
    }

    public function test_update_speenkg() {
        $hisId = null;
        $kg = null;
        $result = $this->sut->update_speenkg($hisId, $kg);
        $this->assertNotFalse($result);
    }

    public function test_zoek_afvoerdm() {
        $schaapId = null;
        $result = $this->sut->zoek_afvoerdm($schaapId);
        $this->assertNotFalse($result);
    }

    public function test_update_afvoerdm() {
        $hidId = null;
        $datum = null;
        $result = $this->sut->update_afvoerdm($hidId, $datum);
        $this->assertNotFalse($result);
    }

    public function test_zoek_afvoerkg() {
        $schaapId = null;
        $result = $this->sut->zoek_afvoerkg($schaapId);
        $this->assertNotFalse($result);
    }

    public function test_update_afvoerkg() {
        $schaapId = null;
        $kg = null;
        $result = $this->sut->update_afvoerkg($schaapId, $kg);
        $this->assertNotFalse($result);
    }

    public function test_insert_afvoer() {
        $stalId = null;
        $dmafv = '2020-01-01';
        $result = $this->sut->insert_afvoer($stalId, $dmafv);
        $this->assertNotFalse($result);
    }

    public function test_insert_act_3() {
        $stalId = null;
        $datum = null;
        $kg = null;
        $result = $this->sut->insert_act_3($stalId, $datum, $kg);
        $this->assertNotFalse($result);
    }

    public function test_insert_afvoer_act() {
        $stalId = null;
        $datum = null;
        $actId = null;
        $result = $this->sut->insert_afvoer_act($stalId, $datum, $actId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_vorige_weging() {
        $schaapId = null;
        $date = null;
        $result = $this->sut->zoek_vorige_weging($schaapId, $date);
        $this->assertNotFalse($result);
    }

    public function test_zoek_actie_vorige_weging() {
        $hisId = null;
        $result = $this->sut->zoek_actie_vorige_weging($hisId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_acties() {
        $result = $this->sut->zoek_acties(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_datum_na() {
        $schaapId = null;
        $result = $this->sut->zoek_datum_na(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_datum_vanaf() {
        $schaapId = null;
        $result = $this->sut->zoek_datum_vanaf(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_uitschaardatum() {
        $last_stalId = null;
        $result = $this->sut->zoek_uitschaardatum($last_stalId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_laatste_hisid() {
        $schaapId = null;
        $result = $this->sut->zoek_laatste_hisid(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_afgevoerd() {
        $maxhis = null;
        $result = $this->sut->zoek_afgevoerd($maxhis);
        $this->assertNotFalse($result);
    }

    public function test_zoek_laatste_verblijf() {
        $schaapId = null;
        $result = $this->sut->zoek_laatste_verblijf(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_dier_uit_verblijf() {
        $lst_bezet = null;
        $schaapId = null;
        $result = $this->sut->zoek_dier_uit_verblijf($lst_bezet, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_skip() {
        $hisId = null;
        $result = $this->sut->skip($hisId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_laatste() {
        $stalId = null;
        $datum = null;
        $result = $this->sut->zoek_laatste($stalId, $datum);
        $this->assertNotFalse($result);
    }

    public function test_zoek_verblijf_moeder() {
        $stalId = null;
        $result = $this->sut->zoek_verblijf_moeder($stalId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_nu_in_verblijf_geb_spn() {
        $result = $this->sut->zoek_nu_in_verblijf_geb_spn(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_nu_in_verblijf_parent() {
        $result = $this->sut->zoek_nu_in_verblijf_parent(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_commentaar() {
        $hisId = null;
        $result = $this->sut->zoek_commentaar($hisId);
        $this->assertNotFalse($result);
    }

    public function test_update_commentaar() {
        $hisId = null;
        $comment = null;
        $result = $this->sut->update_commentaar($hisId, $comment);
        $this->assertNotFalse($result);
    }

    public function test_wis_commentaar() {
        $hisId = null;
        $result = $this->sut->wis_commentaar($hisId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_afvoerdatum() {
        $stalId = null;
        $result = $this->sut->zoek_afvoerdatum($stalId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_afleverlijst() {
        $his = null;
        $result = $this->sut->zoek_afleverlijst($his);
        $this->assertNotFalse($result);
    }

    public function test_count_afleverlijst() {
        $datum = null;
        $relId = null;
        $result = $this->sut->count_afleverlijst(self::LIDID, $datum, $relId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_schaap() {
        $datum = null;
        $relId = null;
        $Karwerk = 5;
        $result = $this->sut->zoek_schaap(self::LIDID, $datum, $relId, $Karwerk);
        $this->assertNotFalse($result);
    }

    public function test_findIdByAct() {
        $actId = null;
        $result = $this->sut->findIdByAct(self::LIDID, $actId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_maxdatum() {
        $stalId = null;
        $result = $this->sut->zoek_maxdatum($stalId);
        $this->assertNotFalse($result);
    }

    public function test_insert() {
        $stalId = null;
        $datum = null;
        $actId = null;
        $result = $this->sut->insert($stalId, $datum, $actId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_aantal_afleveren_per_jaar() {
        $jaar = null;
        $result = $this->sut->zoek_aantal_afleveren_per_jaar(self::LIDID, $jaar);
        $this->assertNotFalse($result);
    }

    public function test_zoek_aantal_lammeren() {
        $jaarweek = null;
        $result = $this->sut->zoek_aantal_lammeren(self::LIDID, $jaarweek);
        $this->assertNotFalse($result);
    }

    public function test_zoek_aantal_afvoer() {
        $jaarweek = null;
        $result = $this->sut->zoek_aantal_afvoer(self::LIDID, $jaarweek);
        $this->assertNotFalse($result);
    }

    public function test_zoek_aantal_geboortes_per_week() {
        $jaarweek = null;
        $result = $this->sut->zoek_aantal_geboortes_per_week(self::LIDID, $jaarweek);
        $this->assertNotFalse($result);
    }

    public function test_zoek_aantal_afvoer_per_week() {
        $jaarweek = null;
        $result = $this->sut->zoek_aantal_afvoer_per_week(self::LIDID, $jaarweek);
        $this->assertNotFalse($result);
    }

    public function test_zoek_aantal_lammeren_per_maand() {
        $van = null;
        $tot = null;
        $result = $this->sut->zoek_aantal_lammeren_per_maand(self::LIDID, $van, $tot);
        $this->assertNotFalse($result);
    }

    public function test_zoek_aantal_afleveren_per_maand() {
        $van = null;
        $tot = null;
        $result = $this->sut->zoek_aantal_afleveren_per_maand(self::LIDID, $van, $tot);
        $this->assertNotFalse($result);
    }

    public function test_zoek_actId() {
        $stalId = null;
        $actId = null;
        $result = $this->sut->zoek_actId($stalId, $actId);
        $this->assertNotFalse($result);
    }

    public function test_huidig_aantal_ooien_persaldo() {
        $result = $this->sut->huidig_aantal_ooien_persaldo(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_eerste_jaar_tbv_testen() {
        $result = $this->sut->eerste_jaar_tbv_testen(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_huidig_aantal_rammen_persaldo() {
        $result = $this->sut->huidig_aantal_rammen_persaldo(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_result_per_maand() {
        $j = null;
        $jm = null;
        $endjrmnd = null;
        $result = $this->sut->result_per_maand($j, $jm, $endjrmnd, self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_hisId_tbv_tblBezet() {
        $stalId = null;
        $result = $this->sut->zoek_hisId_tbv_tblBezet($stalId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_hisIdaanv() {
        $stalId = null;
        $actId = null;
        $result = $this->sut->zoek_hisIdaanv($stalId, $actId);
        $this->assertNotFalse($result);
    }

    public function test_historie_invschaap() {
        $schaapId = null;
        $result = $this->sut->historie_invschaap(self::LIDID, $schaapId);
        $this->assertNotFalse($result);
    }

    public function test_zoek_aantal_doelgroep1() {
        $result = $this->sut->zoek_aantal_doelgroep1(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_aantal_doelgroep2() {
        $result = $this->sut->zoek_aantal_doelgroep2(self::LIDID);
        $this->assertNotFalse($result);
    }

    public function test_zoek_aantal_doelgroep3() {
        $result = $this->sut->zoek_aantal_doelgroep3(self::LIDID);
        $this->assertNotFalse($result);
    }

}
