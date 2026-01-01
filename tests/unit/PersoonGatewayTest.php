<?php

class PersoonGatewayTest extends GatewayCase {

    protected static $sutname = 'PersoonGateway';

    public function test_insert() {
        $partId = 1;
        # You Wish. Verbouw dit maar snel.
        # $data = [
        #     'roep' => '',
        #     'letter' => '',
        #     'voeg' => '',
        #     'naam' => '',
        #     'geslacht' => '',
        #     'tel' => '',
        #     'gsm' => '',
        #     'mail' => '',
        #     'functie' => '',
        # ];
        $data = [
            'insRoep_' => '',
            'insLetter_' => '',
            'insVgsl_' => '',
            'insNaam_' => '',
            'kzlSekse_' => '',
            'insTel_' => '',
            'insGsm_' => '',
            'insMail_' => '',
            'insFunct_' => '',
        ];
        $result = $this->sut->insert($partId, $data);
        $this->assertNotFalse($result);
    }

    public function test_zoek_bij_partij() {
        $partId = null;
        $result = $this->sut->zoek_bij_partij($partId);
        $this->assertNotFalse($result);
    }

    public function test_find() {
        $id = null;
        $result = $this->sut->find($id);
        $this->assertNotFalse($result);
    }

}
