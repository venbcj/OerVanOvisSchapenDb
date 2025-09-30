<?php

class AppTest extends UnitCase {

    public function testUnregistered_value_throws_exception() {
        App::set_space([]);
        $this->expectException(Exception::class);
        App::Karwerk();
    }

    public function testRegistered_value_returns() {
        App::register('Karwerk', 4);
        $this->assertEquals(4, App::Karwerk());
    }

    public function testSetup_loads_defaults() {
        Session::set('I1', 1); // je moet ingelogd zijn
        App::setup();
        // in de db/set/user-1 zit kar_werknr=5
        $this->assertEquals(5, App::Karwerk());
    }

}
