<?php

class Db {

    public const TXT = 'txt';
    public const INT = 'int';
    public const BOOL = 'bool';
    public const DATE = 'date';

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
        return $this->connection->query($this->expand($SQL, $args));
    }

    public function begin_transaction() {
        $this->connection->begin_transaction();
    }

    public function rollback() {
        $this->connection->rollback();
    }

    // in args zit een array van benoemde parameters:
    // parameter is een [naam, waarde, formaat]
    // naam is bijvoorbeeld :id
    // waarde is bijvoorbeeld 4
    // formaat kan zijn self::INT, self::TXT, self::BOOL
    // TODO meer formaten
    private function expand($SQL, $args = []) {
        foreach ($args as $arg) {
            if (!is_array($arg)) {
                throw new Exception("Query-parameters: verwacht een array van arrays.");
            }
            // default formaat is TXT
            if (count($arg) == 2) {
                $arg[] = self::TXT;
            }
            if (count($arg) != 3) {
                throw new Exception("Query-parameters: een parameter moet twee of drie onderdelen bevatten.");
            }
            [$name, $value, $format] = $arg;
            if (is_null($value)) {
                $value = 'NULL';
            } else {
                switch ($arg[2]) {
                case self::TXT:
                case self::DATE:
                    $value = "'" . $this->db->real_escape_string($value) . "'";
                    break;
                case self::INT:
                    $value = (int) $value;
                    break;
                case self::BOOL:
                    $value = $value ? 'true' : 'false';
                    break;
                }
            }
            $SQL = preg_replace("#$name\b#", $value, $SQL);
        }
        if (LOG_QUERIES) {
            $this->logger->debug($SQL);
        }
        return $SQL;
    }

}
