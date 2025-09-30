<?php

class App {

    private static $instance;
    private $space = [];

    // zou ik dit in TestApp moeten zetten?
    public static function set_space($hash) {
        static::ensure_instance();
        static::$instance->replace($hash);
    }

    public static function register($key, $value) {
        static::ensure_instance();
        static::$instance->store($key, $value);
    }

    // publiceer alle sleutels als statische methoden.
    // App::Karwerk() geeft terug wat er via register() [of straks door setup()] in is gezet
    public static function __callStatic($name, $args) {
        static::ensure_instance();
        if (static::$instance->exists($name)) {
            return static::$instance->retrieve($name);
        }
        throw new Exception("$name was not set up");
    }

    public static function setup() {
        static::ensure_instance();
        static::$instance->load_defaults();
    }

    private static function ensure_instance() {
        if (!static::$instance) {
            static::$instance = new static();
        }
    }

    private function store($key, $value) {
        $this->space[$key] = $value;
    }

    private function retrieve($key) {
        return $this->space[$key];
    }

    private function exists($key) {
        return array_key_exists($key, $this->space);
    }

    private function replace($hash) {
        $this->space = $hash;
    }

    private function load_defaults() {
        $this->store('lidId', Session::get('I1'));
        require_once "just_connect_db.php";
        $this->store('db', $GLOBALS['db']);
        $lid_gateway = new LidGateway(static::db());
        $this->store('Karwerk', $lid_gateway->zoek_karwerk(static::lidId()));
    }

}
