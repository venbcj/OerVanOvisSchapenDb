<?php

class RelatiesTest extends IntegrationCase {

    public function test_get() {
        $this->get('/Relaties.php', [
            'ingelogd' => 1,
        ]);
        $this->assertNoNoise();
    }

    public function test_post_insert() {
        $this->post('/Relaties.php', [
            'ingelogd' => 1,
            'knpInsert_' => 1,
            'insPartij_' => 1, // blijkbaar nodig in de post.
        ]);
        $this->assertNoNoise();
    }

}
