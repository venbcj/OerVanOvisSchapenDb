<?php

class CurrentUserTest extends UnitCase {

    public function testUnregistered_value_throws_exception() {
        CurrentUser::set_space([]);
        $this->expectException(Exception::class);
        CurrentUser::Karwerk();
    }

    public function testRegistered_value_returns() {
        CurrentUser::register('Karwerk', 4);
        $this->assertEquals(4, CurrentUser::Karwerk());
    }

    public function testSetup_loads_defaults() {
        Session::set('I1', 1); // je moet ingelogd zijn
        CurrentUser::setup();
        // in de db/set/user-1 zit kar_werknr=5
        $this->assertEquals(5, CurrentUser::Karwerk());
    }

}
