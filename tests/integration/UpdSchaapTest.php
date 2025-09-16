<?php

class UpdSchaapTest extends IntegrationCase {

    public function testGetUpdSchaap() {
        $this->get("/UpdSchaap.php", ['ingelogd' => 1, 'pstschaap' => 1]);
        $this->assertNoNoise();
        // todo:
        // records in $show -- ik denk eigenlijk maar 1 record
        // $volwas gezet via zoek_in_tblvolwas (nog extracten)
        // records in $rsnum, qry_ras
        // records in vw_kzlooien, tbv kzlOoi. Ach die komt uit een include.
        // records in resultram, tbv kzlRam
        // stukje geen spndm en wel aanwdm (1 tabelrij)
        // stkje afvhis en actid_afv in {12, 13}
    }

    public function testSaveUpdSchaap() {
        $this->post("/UpdSchaap.php", [
            'ingelogd' => 1,
            'pstschaap' => 1,
            'knpSave' => 1,
            'txtSchaapId' => 1,
            'kzlOoi' => 1,
            'kzlRam' => 1,
        ]);
        $this->assertNoNoise();
        // todo:
        // txtLevnr in de post -> effect in tblSchaap en impRespons
        // txtFokrnr in de post -> effect in tblSchaap
        // kzlKleur en/of txtHnr in de post, verschillend van de waarden in tblStal -> effect in tblStal
        // kzlRas in de post -> effect in tblSchaap
        // txtGebdm in de post -> effect in tblHistorie
        // txtaanw in de post -> effect in tblHistorie
        // kzlOoi in de post -> effect in tblVolwas
        // kzlRam in de post -> effect in tblVolwas
        // txtSpndm in de post -> effect in tblHistorie
        // txtSpnkg in de post -> effect in tblHistorie
        // kzlBestupd in de post -> effect in tblStal
        // txtAfvdm in de post -> effect in tblHistorie
        // txtAfvkg in de post -> effect in tblHistorie
        // kzlReden -> tblSchaap
        // radAfv, samengestelde beslissing met txtEinddm, kzlBstm -> tblHistorie, tblStal
        //   includes maak_request
        // OOH radAfv in {14,20} -> tblStal, tblSchaap
        // kzlHokOoi
        // txtTerugdm
        //
        // todo tests voor andere knoppen:
        // knpUitHok -> tblHistorie
        // knpHerstel, met radHerst. Yay, switch!
    }

}
