<?php

class Db {

    private static $instance = null;
    private $connection = null;

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        include "database.php";
        $db = mysqli_connect($host, $user, $pw, $dtb);
        if ($db == false) {
            throw new Exception('Connectie database niet gelukt');
        }
        $this->connection = $db;
    }

    public function query($statement) {
        return $this->connection->query($statement);
    }

}
