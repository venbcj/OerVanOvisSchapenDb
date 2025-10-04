<?php

class ImpResponseGateway extends Gateway {

    public function updateLevensnummer($from, $to) {
        $this->db->query(" UPDATE impRespons set levensnummer = '".$this->db->real_escape_string($to)."' WHERE levensnummer = '".$this->db->real_escape_string($from)."' ");
    }

}
