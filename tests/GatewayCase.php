<?php

class GatewayCase extends UnitCase {

    protected const LIDID = 1;

    protected $sut;
    protected static $sutname = '';

    public function setup(): void {
        $this->uses_db();
        $this->db->begin_transaction();
        $this->assertNotEquals('', static::$sutname, 'Voeg `protected static $sutname = \'\'` toe in je testcase (en vul de naam in)');
        $this->sut = new static::$sutname($this->db);
    }

    public function teardown(): void {
        $this->db->rollback();
    }

}
