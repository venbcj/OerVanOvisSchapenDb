<?php

class Session {

    private static $instance;

    public static function get($name) {
        static::ensure_instance();
        return static::$instance->getkey($name);
    }

    public static function set($name, $value) {
        static::ensure_instance();
        return static::$instance->setkey($name, $value);
    }

    public static function start() {
        static::ensure_instance();
        static::$instance->ensure_session();
    }

    private static function ensure_instance() {
        if (isset(static::$instance)) {
            return;
        }
        // het is niet de bedoeling dat je TestSession::get() enz gaat gebruiken
        // daarom hier niet new static
        static::$instance = new self();
    }

    // te gebruiken in tests
    public static function set_instance($session) {
        static::$instance = $session;
    }

    protected function ensure_session() {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function getkey($name) {
        if (array_key_exists($name, $_SESSION)) {
            return $_SESSION[$name];
        }
        return null;
    }

}
