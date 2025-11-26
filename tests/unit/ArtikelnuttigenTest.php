<?php

class ArtikelnuttigenTest extends UnitCase {

    public function setup(): void {
        require_once "func_artikelnuttigen.php";
        $this->uses_db();
    }

    public function test_inlezen_pil() {
        // GIVEN
        // zet de database goed
        // WHEN
        // voer inlezen_pil uit met de 6 parameters... er zijn heel wat scenario's denkbaar
        $hisid = 0;
        $artid = null;
        $rest_toedat = '';
        $toediendatum = '';
        $reduid = 0;
        inlezen_pil($this->db, $hisid, $artid, $rest_toedat, $toediendatum, $reduid);
        // THEN
        // controleer dat de juiste dingen in de database zijn gewijzigd
    }

    # inlezen_voer
    # volgende_inkoop_pil
    # volgende_inkoop_voer
    # zoek_voorraad_oudste_inkoop_pil
    # zoek_voorraad_oudste_inkoop_voer
}
