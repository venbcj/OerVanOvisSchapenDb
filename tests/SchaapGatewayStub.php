<?php

class SchaapGatewayStub {

    private $original;
    private $datasets = [];

    public function __construct() {
        // om methoden die we niet hoeven/willen stubben direct aan door te geven
        $this->original = new SchaapGateway();
    }

    // zet een antwoord klaar in datasets
    public function prime($name, $records) {
        $this->datasets[$name] = $records;
    }

    // om methoden die we niet implementeren zo door te sturen aan original
    // Als we meer gateways gaan stubben, kan dit naar de nieuwe parent GatewayStub
    public function __call($method, $arguments) {
        call_user_func_array([$this->original, $method], $arguments);
    }

    public function zoek_medicatie_lijst($lidId, $afvoer) {
        return new ResultFaker($this->datasets[__function__]);
    }

    public function zoek_medicatielijst_werknummer($lidId, $afvoer) {
        return new ResultFaker($this->datasets[__function__]);
    }

    public function zoek_schaapgegevens($lidId, $Karwerk, $afvoer, $filter) {
        return new ResultFaker($this->datasets[__function__]);
    }

}
