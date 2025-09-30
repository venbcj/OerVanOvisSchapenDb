<?php

class ArtikelGatewayTest extends UnitCase {

    private $sut;

    public function setup(): void {
        $this->uses_db();
        $this->sut = new ArtikelGateway($this->db);
    }

    public function test_voer() {
        $this->runfixture('voervoorraad');
        $res = $this->sut->voer(1);
        $this->assertInstanceOf(Mysqli_result::class, $res);
        $this->assertEquals(1, $res->num_rows);
    }

}
