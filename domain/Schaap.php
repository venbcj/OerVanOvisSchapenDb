<?php //Deze klasse Schaap coördineert wat er allemaal moet gebeuren.

class Schaap {

	private $koppel_service;

	public function __CONSTRUCT($koppel_service = null)
	{
		$this->koppel_service = $koppel_service ?? new KoppelService();
	}

	public function bepaalKoppel($moeder, $datum, $vader = null) {

		return $this->koppel_service->bepaalKoppel($moeder, $datum, $vader);
	}

} ?>