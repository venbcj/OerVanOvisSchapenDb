<?php

class SetupStateTest extends IntegrationCase {

    public function teardown(): void {
        // NOTE: alle tests gaan er dus van uit dat er geen versiebestanden zijn voor de ingelogde gebruiker ... ? Correct?
        $this->makeUserFilesAbsent();
        parent::teardown();
    }

    public function test_versies_bijgewerkt() {
        // in VersiebeheerGatewayTest zie je dat de huidige versie 1 is, de readersetup/appfile "appfile" heet,
        // en de readertaken/readerfile "readerfile"
        // De stand heet 'actueel' als in de user-map van de ingelogde gebruiker een map 'Readerversies' zit met deze bestanden er in.
        $sut = new SetupState();
        $root_dir = $this->makeUserFilesPresent();
        $vars = $sut->versies($root_dir.'/user_1');
        $this->assertTrue($vars['appfile_exists'], 'appfile verwacht');
        $this->assertTrue($vars['takenfile_exists'], 'takenfile verwacht');
        $this->assertEquals('Ja', $vars['actuele_versie']);
    }

    public function test_versies_achterlopend() {
        $sut = new SetupState();
        $root_dir = $this->makeUserFilesAbsent();
        $vars = $sut->versies($root_dir.'/user_1');
        $this->assertFalse($vars['appfile_exists'], 'appfile lukt niet');
        $this->assertFalse($vars['takenfile_exists'], 'takenfile lukt niet');
        $this->assertEquals('', $vars['actuele_versie']);
    }

    public function test_zonder_laatste_versie_altijd_bijgewerkt() {
        $this->runSQL("DELETE FROM tblVersiebeheer");
        $sut = new SetupState();
        $root_dir = $this->makeUserFilesAbsent();
        $vars = $sut->versies($root_dir.'/user_1');
        $this->assertTrue($vars['appfile_exists'], 'appfile moet ok zeggen');
        $this->assertTrue($vars['takenfile_exists'], 'takenfile moet ok zeggen');
        $this->assertEquals('Ja', $vars['actuele_versie']);
    }

}
