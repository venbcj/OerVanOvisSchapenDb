<?php

class Db {

    private static $instance = null;
    private $connection = null;
    private $logger;

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __get($name) {
        if ($name == 'error') {
            return $this->connection->error;
        }
        throw new Exception("Unknown property $name");
    }

    private function __construct($logger = null) {
        if (is_null($logger)) {
            $logger = Logger::instance();
        }
        $this->logger = $logger;
        include "database.php";
        $db = mysqli_connect($host, $user, $pw, $dtb);
        if ($db == false) {
            throw new Exception('Connectie database niet gelukt');
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

    protected function run_query($SQL, $args = []) {
        return $this->connection->query($this->expand($SQL, $args));
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
            [$key, $value, $format] = $arg;
            if (is_null($value)) {
                $value = 'NULL';
            } else {
                switch ($arg[2]) {
                case self::TXT:
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
            $SQL = str_replace($key, $value, $SQL);
        }
        $this->logger->debug($SQL);
        return $SQL;
    }

}
