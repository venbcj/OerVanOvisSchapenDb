<?php

class LidGatewayTest extends UnitCase {

    private $sut;

    public function setup() : void {
        require_once "just_connect_db.php";
        $this->sut = new LidGateway($GLOBALS['db']);
    }

    public function testFindCrediteur() {
        $this->runfixture('crediteur');
        $res = $this->sut->findCrediteur(1);
        $this->assertEquals([4, 13], $res);
    }

}
