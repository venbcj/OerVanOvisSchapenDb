<?php

class UserTest extends UnitCase {

    public function testUnregistered_value_throws_exception() {
        User::set_space([]);
        $this->expectException(Exception::class);
        User::Karwerk();
    }

    public function testRegistered_value_returns() {
        User::register('Karwerk', 4);
        $this->assertEquals(4, User::Karwerk());
    }

    public function testSetup_loads_defaults() {
        Session::set('I1', 1); // je moet ingelogd zijn
        User::setup();
        // in de db/set/user-1 zit kar_werknr=5
        $this->assertEquals(5, User::Karwerk());
    }

}
