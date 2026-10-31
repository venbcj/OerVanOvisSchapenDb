<?php //Deze klasse Schaap coördineert wat er allemaal moet gebeuren.

class Schaap {

private $schaapId;
private $moederId;
private $vaderId = null;
private $geboortedatum;
private $koppel;

private $koppel_service;

	public function __CONSTRUCT($koppel_service = null)
	{
		$this->koppel_service = $koppel_service ?? new KoppelService();
	}

	public function bepaalKoppel($moeder, $datum, $vader = null) {

		$this->moederId = $moeder;
		$this->geboortedatum = $datum;
		$this->vaderId = $vader;

		$this->koppel = $this->koppel_service->bepaalKoppel($moeder, $datum, $vader);

		return $this->koppel;
	}


	public function controleerMomentUitval($moment, $geboortedatum, $uitvaldatum)
	{
		if ($moment == 3 && $uitvaldatum <= $geboortedatum) {
			return 'Dit moment van uitval moet na de geboortedatum liggen';
		}

		return null;
	}


}