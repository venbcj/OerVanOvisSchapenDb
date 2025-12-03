<?php

class ResultFaker {

    private $dataset;
    private $pointer = 0;

    public function __construct($dataset) {
        $this->dataset = $dataset;
    }

    public function fetch_array() {
        if ($this->pointer >= count($this->dataset)) {
            return false;
        }
        // eigenlijk niet goed; fetch_array zou alle velden zowel onder sleutel als onder volgnummer moeten teruggeven
        return $this->dataset[$this->pointer++];
    }

    public function fetch_assoc() {
        if ($this->pointer >= count($this->dataset)) {
            return false;
        }
        return $this->dataset[$this->pointer++];
    }

}
