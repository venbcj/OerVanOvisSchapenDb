<?php

class Session {

    private static $instance;

    public static function get($name) {
        self::ensure_instance();
        return self::$instance->getkey($name);
    }

    public static function start() {
        self::ensure_instance();
        self::ensure_session();
    }

    public static function ensure_instance() {
        if (isset(self::$instance)) {
            return;
        }
        self::$instance = new self();
    }

    public function getkey($name) {
        if (array_key_exists($name, $_SESSION)) {
            return $_SESSION[$name];
        }
        return null;
    }

    private static function ensure_session() {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

}
