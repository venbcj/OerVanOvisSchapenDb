<?php //Deze klasse Schaap coördineert wat er allemaal moet gebeuren.

class Schaap {

	private $volwas_gateway;

	public function __CONSTRUCT($volwas_gateway = null)
	{
		$this->volwas_gateway = $volwas_gateway ?? new VolwasGateway();
	}

	public function bepaalVolwId($moeder, $datum, $vader = null) {

		$volwId = $this->volwas_gateway->zoek_actuele_worp($moeder, $datum);

		if(!isset($volwId)) {
			$lst_volwId = $this->volwas_gateway->zoek_vorige_worp($moeder, $datum);
			$volwId = $this->volwas_gateway->zoek_actuele_dracht($moeder, $lst_volwId);
		}

		if(!isset($volwId)) {
			$volwId = $this->volwas_gateway->zoek_actuele_dekking($moeder, $lst_volwId);
		}

		if (isset($volwId) && isset($vader)) {
            $vader_bestaand = $this->volwas_gateway->zoek_vader_uit_koppel($volwId);

            if (!isset($vader_bestaand)) {
                $this->volwas_gateway->update_koppel($vader, $volwId);
            }
		}

		if (!isset($volwId)) {
            $volwId = $this->volwas_gateway->maak_koppel($moeder, $vader);
        }

        return $volwId;
	}

} ?>