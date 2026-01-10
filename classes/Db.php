<?php

class Db {

    private static $instance = null;
    private $connection = null;
    private $logger;

    public static function instance($db = null) {
        if (is_null(self::$instance)) {
            self::$instance = new self(null, $db);
        }
        return self::$instance;
    }

    public function __get($name) {
        switch ($name) {
        case 'error':
            return $this->connection->error;
        case 'insert_id':
            return $this->connection->insert_id;
        }
        throw new Exception("Unknown property $name");
    }

    private function __construct($logger = null, $db = null) {
        if (is_null($logger)) {
            $logger = Logger::instance();
        }
        $this->logger = $logger;
        // duplicated code in basisfuncties:setup_db() en Db::__construct
        if ($db === null) {
            include "database.php";
            global $db;
            if (!isset($db) || $db === false) {
                $db = mysqli_connect($host, $user, $pw, $dtb);
                if ($db == false) {
                    throw new Exception('Connectie database niet gelukt');
                }
            }
        }
        $this->connection = $db;
        $this->builder = new SqlBuilder($this);
    }

    // oude interface met mysqli
    // deze methoden deprecated maken (loggen in deprecated.log?)

    public function query($statement) {
        return $this->connection->query($statement);
    }

    public function real_escape_string($arg) {
        return $this->connection->real_escape_string($arg);
    }

    // nieuwe interface in de richting van PDO

    public function run_query($SQL, $args = []) {
        return $this->connection->query($this->builder->statement($SQL, $args));
    }

    public function begin_transaction() {
        $this->connection->begin_transaction();
    }

    public function rollback() {
        $this->connection->rollback();
    }

}
