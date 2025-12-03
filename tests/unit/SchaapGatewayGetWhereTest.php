<?php

class SchaapGatewayGetWhereTest extends UnitCase {

    private $sut;

    public function setup(): void {
        $this->sut = new SchaapGateway(null);
    }

    protected function assertEqualsIgnoringWhitespace($expected, $actual) {
        $this->assertEquals($this->lintSpaces($expected), $this->lintSpaces($actual));
    }

    private function lintSpaces($str) {
        return trim(preg_replace('/\s+/', ' ', $str));
    }

    // komt niet voor
    public function testWhereNothing() {
        $postdata = [
            'kzlLevnr_' => '',
            'kzlWerknr_' => '',
            'kzlHalsnr_' => '',
            'kzlOoi_' => '',
            'kzlRam_' => '',
        ];
        $where = $this->sut->getZoekWhere($postdata);
        $this->assertEqualsIgnoringWhitespace('', $where);
    }

    public function testWhereLevnr() {
        $postdata = [
            'kzlLevnr_' => '1',
            'kzlWerknr_' => '',
            'kzlHalsnr_' => '',
            'kzlOoi_' => '',
            'kzlRam_' => '',
        ];
        $where = $this->sut->getZoekWhere($postdata);
        // dit is wel maf... je zoekt een levensnummer, en vindt een schaapid.
        $this->assertEqualsIgnoringWhitespace('s.schaapId = 1', $where);
    }

    public function testWhereLevnrGeen() {
        $postdata = [
            'kzlLevnr_' => 'Geen',
            'kzlWerknr_' => '',
            'kzlHalsnr_' => '',
            'kzlOoi_' => '',
            'kzlRam_' => '',
        ];
        $where = $this->sut->getZoekWhere($postdata);
        $this->assertEqualsIgnoringWhitespace('isnull(s.levensnummer)', $where);
    }

    public function testWhereWerknrGeen() {
        $postdata = [
            'kzlLevnr_' => '',
            'kzlWerknr_' => 'Geen',
            'kzlHalsnr_' => '',
            'kzlOoi_' => '',
            'kzlRam_' => '',
        ];
        $where = $this->sut->getZoekWhere($postdata);
        $this->assertEqualsIgnoringWhitespace('isnull(s.levensnummer)', $where);
    }

    public function testWhereWerknr() {
        $postdata = [
            'kzlLevnr_' => '',
            'kzlWerknr_' => '131',
            'kzlHalsnr_' => '',
            'kzlOoi_' => '',
            'kzlRam_' => '',
        ];
        $where = $this->sut->getZoekWhere($postdata);
        $this->assertEqualsIgnoringWhitespace('s.schaapId = 131', $where);
    }

    public function testWhereWerknrAndLevnr() {
        $postdata = [
            'kzlLevnr_' => '1',
            'kzlWerknr_' => '131',
            'kzlHalsnr_' => '',
            'kzlOoi_' => '',
            'kzlRam_' => '',
        ];
        $where = $this->sut->getZoekWhere($postdata);
        $this->assertEqualsIgnoringWhitespace('s.schaapId = 1 and s.schaapId = 131', $where);
    }

    public function testWhereHalsnr() {
        $postdata = [
            'kzlLevnr_' => '',
            'kzlWerknr_' => '',
            'kzlHalsnr_' => '3',
            'kzlOoi_' => '',
            'kzlRam_' => '',
        ];
        $where = $this->sut->getZoekWhere($postdata);
        $this->assertEqualsIgnoringWhitespace('s.schaapId = 3', $where);
    }

    public function testWhereHalsnrAndLevnr() {
        $postdata = [
            'kzlLevnr_' => '4',
            'kzlWerknr_' => '',
            'kzlHalsnr_' => '3',
            'kzlOoi_' => '',
            'kzlRam_' => '',
        ];
        $where = $this->sut->getZoekWhere($postdata);
        $this->assertEqualsIgnoringWhitespace('s.schaapId = 4 and s.schaapId = 3', $where);
    }

    public function testWhereOoi() {
        $postdata = [
            'kzlLevnr_' => '',
            'kzlWerknr_' => '',
            'kzlHalsnr_' => '',
            'kzlOoi_' => '4',
            'kzlRam_' => '',
        ];
        $where = $this->sut->getZoekWhere($postdata);
        $this->assertEqualsIgnoringWhitespace('mdr.schaapId = 4', $where);
    }

    public function testWhereOoiAndLevnr() {
        $postdata = [
            'kzlLevnr_' => '4',
            'kzlWerknr_' => '',
            'kzlHalsnr_' => '',
            'kzlOoi_' => '5',
            'kzlRam_' => '',
        ];
        $where = $this->sut->getZoekWhere($postdata);
        $this->assertEqualsIgnoringWhitespace('s.schaapId = 4 and mdr.schaapId = 5', $where);
    }

    public function testWhereRam() {
        $postdata = [
            'kzlLevnr_' => '',
            'kzlWerknr_' => '',
            'kzlHalsnr_' => '',
            'kzlOoi_' => '',
            'kzlRam_' => '5',
        ];
        $where = $this->sut->getZoekWhere($postdata);
        $this->assertEqualsIgnoringWhitespace('vdr.schaapId = 5', $where);
    }

    public function testWhereRamAndLevnr() {
        $postdata = [
            'kzlLevnr_' => '4',
            'kzlWerknr_' => '',
            'kzlHalsnr_' => '',
            'kzlOoi_' => '',
            'kzlRam_' => '6',
        ];
        $where = $this->sut->getZoekWhere($postdata);
        $this->assertEqualsIgnoringWhitespace('s.schaapId = 4 and vdr.schaapId = 6', $where);
    }

}
