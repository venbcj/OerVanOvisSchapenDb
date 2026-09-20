<?php

class Koppel {

	private $id;
	private $moeder;
	private $vader;

	public function __CONSTRUCT($id, $moeder, $vader = null)
	{
		$this->id = $id;
		$this->moeder = $moeder;
		$this->vader = $vader;
	}

	public function heeftVader()
	{
		return $this->vader !== null;
	}
}