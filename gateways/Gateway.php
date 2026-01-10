<?php

/*
 * Geef in je afgeleide klassen het return-type van de methode, tenzij dat mysqli_result is
 * (we streven ernaar zulke methoden af te voeren, en alleen resultsets terug te geven)
 */

class Gateway {

    protected $db;
    protected $builder;

    public function __construct($db = null) {
        if (is_null($db)) {
            $db = Db::instance();
        }
        if (!(is_a($db, Db::class) || is_a($db, Mysqli::class))) {
            throw new Exception("Parameter is not usable to set up database connection");
        }
        $this->db = $db;
        $this->builder = new SqlBuilder($db);
    }

    // TODO: ipv LOG_QUERIES... ik kan niet wachten tot er config() is, voor een setting debug.log_queries
    protected function run_query($SQL, $args = [], $types = []) {
        if (empty($types)) {
            $types = Schema::dictionary();
        }
        $statement = $this->builder->statement($SQL, $args, $types);
        if (LOG_QUERIES) {
            Logger::instance()->debug($statement);
        }
        $res = $this->db->query($statement);
        if (LOG_QUERIES && $this->db->error) {
            Logger::instance()->warning($this->db->error);
        }
        return $res;
    }

    // BCB: Afgeraden. Los dit op in je client-code.
    // public function explain_run_query($parSQL, $parArgs = []) {
    //     $result = $this->run_query($parSQL, $parArgs);
    //     return $result->fetch_all(MYSQLI_ASSOC);
    // }
        
    // returns table row as assoc-array
    protected function first_record($SQL, $args = [], $default = null) {
        $view = $this->run_query($SQL, $args);
        if ($this->view_has_rows($view)) {
            return $view->fetch_assoc();
        }
        return $default;
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

    // haalt een associatieve array uit een resultaatset.
    // Standaard wordt de eerste kolom uit de SELECT de sleutel van de array, en de tweede kolom de waarde.
    // Als je dat niet wil, moet je een closure meegeven die van een record uit de resultaatset een tweekoloms-array maakt.
    protected function KV($vw, $row_former = null) {
        $res = [];
        if (is_null($row_former)) {
            $row_former = function ($rec) {
                return $rec;
            };
        }
        while ($rec = $vw->fetch_array()) {
            $row = $row_former($rec);
            $res[$row[0]] = $row[1];
        }
        return $res;
    }

    // haalt een lijst uit een resultset: 1 kolom in SELECT
    protected function collect_list($SQL, $args = []) {
        $vw = $this->run_query($SQL, $args);
        $res = [];
        while ($row = $vw->fetch_row()) {
            $res[] = $row[0];  
        }
        return $res;
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

    // werkbespaarder: als je een formulier met veel parameters aanlevert,
    //   zet deze methode ze allemaal om in pdo-string-argumenten
    // Uitschrijven is uiteraard preciezer.
    protected function struct_to_args($form) {
        return array_map(function($key, $value) {
            return [":$key", $value];
        }, array_keys($form), array_values($form)
        );
    }

}
