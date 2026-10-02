<?php

class Koppel {

	private $koppelnr;
	private $moeder;
	private $vader;

	public function __CONSTRUCT($koppelnr, $moeder, $vader = null)
	{
		$this->koppelnr = $koppelnr;
		$this->moeder = $moeder;
		$this->vader = $vader;
	}

	public function koppelnr()
	{
		return $this->koppelnr;
	}
	
	public function heeftVader()
	{
		return $this->vader !== null;
	}
}