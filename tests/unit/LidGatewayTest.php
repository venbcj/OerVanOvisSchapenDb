<?php

class LidGatewayTest extends GatewayCase {

    protected static $sutname = 'LidGateway';

    public function testFindCrediteur() {
        $this->runfixture('crediteur');
        $res = $this->sut->findCrediteur(self::LIDID);
        $this->assertEquals([4, 13], $res);
    }

    public function test_save_new() {
        $form = [
            // uit een POST
            'roep' => 'a',
            'voegsel' => 'a',
            'naam' => 'a',
            'tel' => 'a',
            'mail' => 'a',
            'ubn' => 'a',
            'relnr' => 1,
            'urvo' => 'a',
            'prvo' => 'a',
            'reader' => 'a',
            'meld' => 1,
            'tech' => 1,
            'fin' => 1,
            // aangevuld in NewUser
            'login' => 'ubn',
            'alias' => 'ubn05',
            'passw' => 'complexe string die nog moet worden opgedroogd',
            'readerkey' => '44',
            'kar_werknr' => 3,
            'actief' => 1,
            'ingescand' => '2010-02-02',
            'beheer' => 0,
            'histo' => 1,
        ];
        $new_id = $this->sut->save_new($form);
        $this->assertEquals('', $this->db->error);
        $this->assertTableWithPK('tblLeden', 'lidId', $new_id, []);
    }

}
