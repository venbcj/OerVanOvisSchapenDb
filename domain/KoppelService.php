<?php

class KoppelService 
{

	private $volwas_gateway;

	public function __CONSTRUCT($volwas_gateway = null)
	{
		$this->volwas_gateway = $volwas_gateway ?? new VolwasGateway();
	}

	private function vindKoppelVanWorp($moeder, $datum)
	{
		return $this->volwas_gateway->zoek_actuele_worp($moeder, $datum);
	}

	private function vindKoppelVanDracht($moeder, $datum)
	{
		return $this->volwas_gateway->zoek_actuele_dracht($moeder, $datum);
	}

	private function vindKoppelVanDekking($moeder, $datum)
	{
		return $this->volwas_gateway->zoek_actuele_dekking($moeder, $datum);
	}

	private function maakNieuwKoppel($moeder, $vader)
	{
		return $this->volwas_gateway->maak_koppel($moeder, $vader);
	}

	public function bepaalKoppel($moeder, $datum, $vader = null)
	{
		$koppelnr = $this->vindKoppelVanWorp($moeder, $datum);

		if(!$koppelnr) {
			$koppelnr = $this->vindKoppelVanDracht($moeder, $datum);
		}

		if(!$koppelnr){
			$koppelnr = $this->vindKoppelVanDekking($moeder, $datum);
		}

		if(!$koppelnr){
			$koppelnr = $this->maakNieuwKoppel($moeder, $vader);
		}

		[$koppelnr, $moeder, $vader] = $this->volwas_gateway->zoekKoppel($koppelnr);

		return new Koppel($koppelnr, $moeder, $vader);
	}
}