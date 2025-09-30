<?php

class Gateway {

    const TXT = 'txt';
    const INT = 'int';
    const BOOL = 'bool';

    public function __construct($db) {
        $this->db = $db;
    }

    protected function run_query($SQL, $args = []) {
        return mysqli_query($this->db, $this->expand($SQL, $args));
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
        Logger::debug($SQL);
        return $SQL;
    }

}
