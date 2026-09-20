<?php

class KoppelService 
{

	private $volwas_gateway;

	public function __CONSTRUCT($volwas_gateway = null)
	{
		$this->volwas_gateway = $volwas_gateway ?? new VolwasGateway();
	}

	public function vindKoppelVanWorp($moeder, $datum)
	{
		$volwId = $this->volwas_gateway->zoek_actuele_worp($moeder, $datum);

		if(!$volwId) {
			return null;
		}

		return null;
	}
}