<?php

class GatewayCase extends UnitCase {

    protected const LIDID = 1;

    protected $sut;
    protected static $sutname = '';

    public function setup(): void {
        $this->uses_db();
        $this->db->begin_transaction();
        $this->assertNotEquals('', static::$sutname, 'Vul $sutname in je testcase');
        $this->sut = new static::$sutname($this->db);
    }

    public function teardown(): void {
        $this->db->rollback();
    }

}
