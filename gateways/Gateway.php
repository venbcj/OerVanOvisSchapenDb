<?php

class Gateway {

    protected const TXT = 'txt';
    protected const INT = 'int';
    protected const BOOL = 'bool';
    protected const DATE = 'date';

    protected $db;

    public function __construct($db = null) {
        if (is_null($db)) {
            $db = Db::instance();
        }
        if (is_a($db, Db::class) || is_a($db, Mysqli::class)) {
            $this->db = $db;
        } else {
            throw new Exception("Parameter is not usable to set up database connection");
        }
    }

    protected function run_query($SQL, $args = []) {
        // TODO: ik kan niet wachten tot er config() is, voor een setting debug.log_queries
        $statement = $this->expand($SQL, $args);
        # Logger::debug($statement);
        $res = $this->db->query($statement);
        # Logger::debug($this->db->error);
        return $res;
    }

    // returns table row as array with just values
    protected function first_row($SQL, $args = [], $default = null) {
        $view = $this->run_query($SQL, $args);
        if ($this->view_has_rows($view)) {
            return $view->fetch_row();
        }
        return $default;
    }

    protected function first_field($SQL, $args = [], $default = null) {
        $view = $this->run_query($SQL, $args);
        if ($this->view_has_rows($view)) {
            return $view->fetch_row()[0];
        }
        return $default;
    }

    private function view_has_rows($view) {
        if ($view === false || $view === true) {
            return false;
        }
        if ($view->num_rows == 0) {
            return false;
        }
        return true;
    }

    // in args zit een array van benoemde parameters:
    // parameter is een [naam, waarde, formaat]
    // naam is bijvoorbeeld :id
    // waarde is bijvoorbeeld 4
    // formaat kan zijn self::INT, self::TXT, self::BOOL, self::DATE
    // TODO meer formaten
    private function expand($SQL, $args = []) {
        foreach ($args as $arg) {
            $arg = $this->validateArg($arg);
            [$key, $value, $format] = $arg;
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
            $SQL = str_replace($key, $value, $SQL);
        }
        Logger::instance()->debug($SQL);
        return $SQL;
    }

    private function validateArg($arg) {
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
        return $arg;
    }

}
