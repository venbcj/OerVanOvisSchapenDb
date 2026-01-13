<?php

class InlezenReaderTest extends IntegrationCase {

    public function test_get() {
        $this->get('/InlezenReader.php', [
            'ingelogd' => 1,
        ]);
        $this->assertNoNoise();
    }

}
