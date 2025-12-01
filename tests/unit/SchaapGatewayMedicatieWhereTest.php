<?php

class SchaapGatewayMedicatieWhereTest extends UnitCase {

    private $sut;

    public function setup(): void {
        $this->sut = new SchaapGateway();
    }

    protected function assertEqualsIgnoringWhitespace($expected, $actual) {
        $this->assertEquals($this->lintSpaces($expected), $this->lintSpaces($actual));
    }

    private function lintSpaces($str) {
        return trim(preg_replace('/\s+/', ' ', $str));
    }

    public function test_nofilters() {
        $post = [];
        [$actual_filter, $actual_filt_mdr] = $this->sut->getMedicatieWhere($post);
        $this->assertEquals('', $actual_filter);
    }

    public function test_levnr() {
        $post = [
            'kzlLevnr' => 1,
        ];
        [$actual_filter, $actual_filt_mdr] = $this->sut->getMedicatieWhere($post);
        $this->assertEqualsIgnoringWhitespace("schaapId = '1'", $actual_filter);
    }

    public function test_werknr() {
        $post = [
            'kzlWerknr' => 2,
        ];
        [$actual_filter, $actual_filt_mdr] = $this->sut->getMedicatieWhere($post);
        $this->assertEqualsIgnoringWhitespace("schaapId = '2'", $actual_filter);
    }

    public function test_levnr_werknr() {
        $post = [
            'kzlLevnr' => 1,
            'kzlWerknr' => 2,
        ];
        [$actual_filter, $actual_filt_mdr] = $this->sut->getMedicatieWhere($post);
        $this->assertEqualsIgnoringWhitespace("schaapId = '1' and schaapId = '2'", $actual_filter);
    }

    public function test_halsnr() {
        $post = [
            'kzlHalsnr' => 3,
        ];
        [$actual_filter, $actual_filt_mdr] = $this->sut->getMedicatieWhere($post);
        $this->assertEqualsIgnoringWhitespace("schaapId = '3'", $actual_filter);
        $this->assertEquals('', $actual_filt_mdr);
    }

    public function test_levnr_halsnr() {
        $post = [
            'kzlLevnr' => 1,
            'kzlHalsnr' => 3,
        ];
        [$actual_filter, $actual_filt_mdr] = $this->sut->getMedicatieWhere($post);
        $this->assertEqualsIgnoringWhitespace("schaapId = '1' and schaapId = '3'", $actual_filter);
        $this->assertEquals('', $actual_filt_mdr);
    }

    public function test_ooi() {
        $post = [
            'chbOoi' => 1,
        ];
        [$actual_filter, $actual_filt_mdr] = $this->sut->getMedicatieWhere($post);
        $this->assertEqualsIgnoringWhitespace("geslacht = 'ooi' and aanw is not null", $actual_filter);
        $this->assertEquals('', $actual_filt_mdr);
    }

    public function test_levnr_ooi() {
        $post = [
            'kzlLevnr' => 1,
            'chbOoi' => 1,
        ];
        [$actual_filter, $actual_filt_mdr] = $this->sut->getMedicatieWhere($post);
        $this->assertEqualsIgnoringWhitespace("schaapId = '1' and geslacht = 'ooi' and aanw is not null", $actual_filter);
        $this->assertEquals('', $actual_filt_mdr);
    }

    public function test_hok_lam() {
        $post = [
            'kzlHok' => 1,
            'radHok' => 1,
        ];
        [$actual_filter, $actual_filt_mdr] = $this->sut->getMedicatieWhere($post);
        $this->assertEqualsIgnoringWhitespace("hokId = '1' and generatie = 'lam'", $actual_filter);
        $this->assertEquals('', $actual_filt_mdr);
    }

    public function test_hok_ouder() {
        $post = [
            'kzlHok' => 1,
            'radHok' => 2,
        ];
        [$actual_filter, $actual_filt_mdr] = $this->sut->getMedicatieWhere($post);
        $this->assertEqualsIgnoringWhitespace("hokId = '1' and generatie = 'ouder'", $actual_filter);
        $this->assertEquals('', $actual_filt_mdr);
    }

    public function test_hok() {
        $post = [
            'kzlHok' => 1,
            'radHok' => 3,
        ];
        [$actual_filter, $actual_filt_mdr] = $this->sut->getMedicatieWhere($post);
        $this->assertEqualsIgnoringWhitespace("hokId = '1'", $actual_filter);
        $this->assertEquals('', $actual_filt_mdr);
    }

    // TODO: #0004203 ALLEEN als kzlHok is gezet EN er zijn andere filters komt er iets in filt_mdr ... ? Wat moet dat doen dan?
    public function test_levnr_hok() {
        $post = [
            'kzlLevnr' => 1,
            'kzlHok' => 2,
            'radHok' => 3,
        ];
        [$actual_filter, $actual_filt_mdr] = $this->sut->getMedicatieWhere($post);
        $this->assertEqualsIgnoringWhitespace("schaapId = '1' and hokId = '2'", $actual_filter);
        $this->assertEqualsIgnoringWhitespace("schaapId = '1' and hokId = '2'", $actual_filt_mdr);
    }

    public function test_datum() {
        $post = [
            'txtGeb_van' => '2020-01-01',
        ];
        [$actual_filter, $actual_filt_mdr] = $this->sut->getMedicatieWhere($post);
        $this->assertEqualsIgnoringWhitespace("dmgeb >= '2020-01-01' and dmgeb <= '" . date('Y-m-d') . "'", $actual_filter);
        $this->assertEquals('', $actual_filt_mdr);
    }

    public function test_levnr_datum() {
        $post = [
            'kzlLevnr' => 1,
            'txtGeb_van' => '2020-01-01',
        ];
        [$actual_filter, $actual_filt_mdr] = $this->sut->getMedicatieWhere($post);
        $this->assertEqualsIgnoringWhitespace("schaapId = '1' and dmgeb >= '2020-01-01' and dmgeb <= '" . date('Y-m-d') . "'", $actual_filter);
        $this->assertEquals('', $actual_filt_mdr);
    }

}
