<?php

class InvSchaapPageTest extends IntegrationCase {

    public function testGetInvSchaap() {
        $this->runfixture('user-1-more-ubns');
        $this->runfixture('hok');
        $this->get('/InvSchaap.php', ['ingelogd' => 1]);
        $this->approve();
    }

}
