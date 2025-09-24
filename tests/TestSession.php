<?php

class TestSession extends Session {

    private $storage = [];

    public function getkey($name) {
        return $this->storage[$name] ?? null;
    }

    public function setkey($name, $value) {
        $this->storage[$name] = $value;
    }

    protected function ensure_session() {
        // does nothing
    }

}
