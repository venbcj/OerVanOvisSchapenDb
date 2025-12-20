<?php

class JsonAgridentParserTest extends IntegrationCase {

    private const LIDID = 1;

    public function test_no_data_does_nothing() {
        $sut = new JsonAgridentParser([], self::LIDID);
        $this->assertEquals('', $sut->execute());
    }

    public function testWorpregistratie() {
        $sut = new JsonAgridentParser($this->incoming([
            'Worpregistratie' => [
                'olifant' => (object)[
                    'ActId' => 1,
                    'MoederTransponder' => 1,
                    'Moeder' => 1,
                    'Datum' => 1,
                    'RasId' => 1,
                    'HokId' => 1,
                    'Verloop' => 1,
                    'Geboren' => 1,
                    'Levend' => 1,
                    'Reden' => 1,
                    'Lammeren' => [],
                ],
            ],
        ])
        , self::LIDID);
        $out = $sut->execute();
        $this->assertEquals('', $out);
    }

    private function incoming($extra = []) {
        return [(object)array_merge_recursive([
            'Worpregistratie' => [],
            'Doodgeboren' => [],
            'Groepsgeboorte' => [],
            'Verplaatsing' => [],
            'Spenen' => [],
            'Afvoer' => [],
            'Aanvoer' => [],
            'Omnummeren' => [],
            'Medicaties' => [],
            'Halsnummers' => [],
            'Groepsafvoer' => [],
            'Voerregistratie' => [],
            'Dekken' => [],
            'Dracht' => [],
        ], $extra)];
    }

}
