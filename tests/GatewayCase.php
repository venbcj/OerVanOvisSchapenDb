<?php

class GatewayCase extends UnitCase {

    protected $sut;
    protected static $sutname = '';

    public function setup(): void {
        $this->assertNotEquals('', static::$sutname, 'Vul $sutname in je testcase');
        $this->uses_db();
        $this->sut = new static::$sutname($this->db);
    }

}
