<?php

class MeldenFunctionsTest extends UnitCase {

    public function setup() : void {
        $this->setupServer();
        require_once "melden_functions.php";
        require_once "basisfuncties.php";
        require_once "connect_db.php";
        $this->db = $db;
    }

    public function test_geenGeboortes_geenItem() {
        $this->runfixture('request-none');
        [$target, $caption, $remark] = melden_menu($this->db, 1);
        $this->assertEquals('Melden.php', $target['geboorte']);
    }

    public function test_geboorte() {
        $this->runfixture('request-lid-codes');
        // TODO deze interface verbeteren?
        [$target, $caption, $remark] = melden_menu($this->db, 1);
        $this->assertEquals('&nbsp 1 geboorte(s) te melden.', $remark['geboorte']);
    }

    public function test_meerdan60_afvoer() {
        $this->runfixture('request-61-afvoer');
        [$target, $caption, $remark] = melden_menu($this->db, 1);
        // TODO merk op dat nbsp nu eens wel, dan weer geen puntkomma heft
        $this->assertEquals('&nbsp; 61 afvoer te melden.&nbsp&nbsp&nbsp U ziet per melding max. 60 schapen. ', $remark['afvoer']);
    }

}
