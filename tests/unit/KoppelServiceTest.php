<?php

class KoppelServiceTest extends UnitCase{

    private const ACTUEEL_KOPPEL = 123;
    private const NIEUW_KOPPEL = 456;
    private const MOEDER = 10;
    private const VADER = 20;
    private const DATUM = '2026-10-04';

    public function test_aanmaken()
    {
        $service = new KoppelService(); // maak een instantie/object aan van de class KoppelService

        $this->assertInstanceOf(KoppelService::class, $service);  //Controleer of $service een instantie is van de class KoppelService
    }

    public function test_bepaalKoppel_vindt_actuele_worp()
    {
        $stub = $this->createStub(VolwasGateway::class);
        $stub->method('zoek_actuele_worp')->willReturn(self::ACTUEEL_KOPPEL);
        $stub->method('zoekKoppel')->willReturn([self::ACTUEEL_KOPPEL, self::MOEDER, self::VADER]);

        $service = new KoppelService($stub);

        $koppel = $service->bepaalKoppel(self::MOEDER, self::DATUM);

        $this->assertEquals(self::ACTUEEL_KOPPEL, $koppel->koppelnr());
    }

    public function test_bepaalKoppel_vindt_actuele_dracht()
    {
 		$stub = $this->createStub(VolwasGateway::class);
 		$stub->method('zoek_actuele_worp')->willReturn(null);
 		$stub->method('zoek_actuele_dracht')->willReturn(self::ACTUEEL_KOPPEL);
 		$stub->method('zoekKoppel')->willReturn([self::ACTUEEL_KOPPEL, self::MOEDER, null]);

        $service = new KoppelService($stub);

        $koppel = $service->bepaalKoppel(self::MOEDER, self::DATUM);

        $this->assertEquals(self::ACTUEEL_KOPPEL, $koppel->koppelnr());
    }

    public function test_bepaalKoppel_vindt_actuele_dekking()
    {
    	$stub = $this->createStub(VolwasGateway::class);
    	$stub->method('zoek_actuele_worp')->willReturn(null);
    	$stub->method('zoek_actuele_dracht')->willReturn(null);
    	$stub->method('zoek_actuele_dekking')->willReturn(self::ACTUEEL_KOPPEL);
    	$stub->method('zoekKoppel')->willReturn([self::ACTUEEL_KOPPEL, self::MOEDER, null]);

        $service = new KoppelService($stub);

        $koppel = $service->bepaalKoppel(self::MOEDER, self::DATUM);

        $this->assertEquals(self::ACTUEEL_KOPPEL, $koppel->koppelnr());
    }

    public function test_maakKoppel()
    {
        $stub = $this->createStub(VolwasGateway::class);
        $stub->method('zoek_actuele_worp')->willReturn(null);
        $stub->method('zoek_actuele_dracht')->willReturn(null);
        $stub->method('zoek_actuele_dekking')->willReturn(null);
        $stub->method('maak_koppel')->willReturn(self::NIEUW_KOPPEL);
        $stub->method('zoekKoppel')->willReturn([self::NIEUW_KOPPEL, self::MOEDER, null]);

        $service = new KoppelService($stub);

        $koppel = $service->bepaalKoppel(self::MOEDER, self::DATUM, null);

        $this->assertEquals(self::NIEUW_KOPPEL, $koppel->koppelnr());
    }
}
