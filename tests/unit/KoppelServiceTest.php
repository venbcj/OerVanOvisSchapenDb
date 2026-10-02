<?php

class TestVolwasGateway_VindKoppelVanWorp
{
    public function zoek_actuele_worp($moeder, $datum)
    {
        return 123;
    }

    public function zoekKoppel($koppelnr)
    {
        return [$koppelnr, 10, 20];
    }
}


class TestVolwasGateway_VindKoppelVanDracht
{
    public function zoek_actuele_worp($moeder, $datum)
    {
        return null;
    }

    public function zoek_vorige_worp($moeder, $datum)
    {
        return 456;
    }

    public function zoek_actuele_dracht($moeder, $vorige_koppel)
    {
        return 789;
    }
    
    public function zoekKoppel($koppelnr)
    {
        return [$koppelnr, 10, 20];
    }
}

class TestVolwasGateway_VindKoppelVanDekking
{
    public function zoek_actuele_worp($moeder, $datum)
    {
        return null;
    }

    public function zoek_vorige_worp($moeder, $datum)
    {
        return 654;
    }

    public function zoek_actuele_dracht($moeder, $vorige_koppel)
    {
        return null;
    }

    public function zoek_actuele_dekking($moeder, $vorige_koppel)
    {
        return 987;
    }

    public function zoekKoppel($koppelnr)
    {
        return [$koppelnr, 10, 20];
    }
}

class TestVolwasGateway_MaakKoppel
{
    public function zoek_actuele_worp($moeder, $datum)
    {
        return null;
    }

    public function zoek_vorige_worp($moeder, $datum)
    {
        return 654;
    }

    public function zoek_actuele_dracht($moeder, $vorige_koppel)
    {
        return null;
    }

    public function zoek_actuele_dekking($moeder, $vorige_koppel)
    {
        return null;
    }

    public function maak_koppel($moeder, $vader)
    {
        return 1234;
    }

    public function zoekKoppel($koppelnr)
    {
        return [$koppelnr, 10, 20];
    }
}

class KoppelServiceTest extends UnitCase{
    public function test_aanmaken()
    {
        $service = new KoppelService(new TestVolwasGateway_VindKoppelVanWorp()); // maak een instantie/object aan van de class KoppelService

        $this->assertInstanceOf(KoppelService::class, $service);  //Controleer of $service een instantie is van de class KoppelService
    }

    public function test_bepaalKoppel_vindt_actuele_worp()
    {
        $stub = $this->createStub(VolwasGateway::class);
        $stub->method('zoek_actuele_worp')->willReturn(123);
        $stub->method('zoekKoppel')->willReturn([123, 10, 20]);

        $service = new KoppelService($stub);

        $koppel = $service->bepaalKoppel(10, '2026-09-26');

        $this->assertEquals(123, $koppel->koppelnr());
    }

    public function test_bepaalKoppel_vindt_actuele_dracht()
    {
        $service = new KoppelService(new TestVolwasGateway_VindKoppelVanDracht());

        $koppel = $service->bepaalKoppel(10, '2026-09-26');

        $this->assertInstanceOf(Koppel::class, $koppel);
        $this->assertEquals(789, $koppel->koppelnr());
    }

    public function test_bepaalKoppel_vindt_actuele_dekking()
    {
        $service = new KoppelService(new TestVolwasGateway_VindKoppelVanDekking());

        $koppel = $service->bepaalKoppel(10, '2026-09-29');

        $this->assertInstanceOf(Koppel::class, $koppel);
        $this->assertEquals(987, $koppel->koppelnr());
    }

    public function test_maakKoppel()
    {
        $service = new KoppelService(new TestVolwasGateway_MaakKoppel());

        $koppel = $service->bepaalKoppel(10, '2026-09-30', 20);

        $this->assertInstanceOf(Koppel::class, $koppel);
        $this->assertEquals(1234, $koppel->koppelnr());
    }
}
