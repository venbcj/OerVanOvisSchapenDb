<?php

class TestSession extends Session {

    private $storage = [];

    public function __construct() {
        $this->storage = [];
    }

    public function getkey($name) {
        return $this->storage[$name] ?? null;
    }

    public function setkey($name, $value) {
        $this->storage[$name] = $value;
    }

    public function issetkey($name) {
        return array_key_exists($name, $this->storage);
    }

    protected function ensure_session() {
        // does nothing
    }

    protected function kill_session() {
        $this->storage = [];
    }

}
