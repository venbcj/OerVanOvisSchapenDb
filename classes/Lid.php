<?php
class Lid {

    private $lid_gateway;
    private $id;

    public function __construct(int $id, Gateway $lid_gateway = null) { 
        if (is_null($lid_gateway)) {
            $lid_gateway = new LidGateway();
        }
        $this->id = $id;
        $this->lid_gateway = $lid_gateway;
    }

    public function bestaat_stallijst(): bool {
        return $this->lid_gateway->zoek_lege_stallijst($this->id) > 0;
    }

} 