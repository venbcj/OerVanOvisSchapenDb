<?php

use PHPUnit\Framework\TestCase;

class UnitCase extends TestCase {

    // Database aanspreken in unit-tests vind ik eigenlijk niet okee. Code is nog niet ver genoeg --BCB

    protected $db;
    private $tablecounts = [];
    private $expectedincrements = [];

    protected function uses_db() {
        require_once "just_connect_db.php";
        global $db;
        $this->db = Db::instance($db);
    }

    protected function snapshot($tables) {
        $trace = debug_backtrace();
        array_shift($trace);
        $test = $trace[0]['function'];
        $res = '';
        foreach ($tables as $table) {
            $records = $this->db->query("SELECT * FROM $table")->fetch_all(MYSQLI_ASSOC);
            $fields = '';
            if (count($records)) {
                $fields = ' ('.implode(',', array_keys($records[0])).')';
            }
            $res .= $table.$fields.PHP_EOL;
            $res .= implode(PHP_EOL, array_map(function ($rec) { return implode(',', array_map(
                function ($col) {
                    return is_null($col) ? 'NULL' : $col;
                },
                $rec)
            ); },
            $records)).PHP_EOL;
        }
        $file = './snapshot-'.$test.'-'.date("Y-m-d-H-i-s").'.txt';
        $this->assertNotFalse(file_put_contents($file, $res), 'snapshot failed');
    }

    protected function setupServer($path = 'Maaktnietuit.php') {
        $_SERVER['HTTP_HOST'] = 'oer-dev';
        $_SERVER['REQUEST_SCHEME'] = 'http';
        $_SERVER['REQUEST_URI'] = $path;
    }

    // TODO: dit is herhaald in bootstrap. Uitbouwen in nieuw object Fixture?
    protected static function runfixture($name) {
        if (file_exists($file = getcwd()."/tests/fixtures/$name.sql")) {
            self::performStatementsIn($file);
        } else {
            throw new Exception("fixture $name not found as $file.");
        }
    }

    // dit moet je in tests niet willen.
    protected static function runsetup($name) {
        if (file_exists($file = getcwd()."/db/setup/$name.sql")) {
            self::performStatementsIn($file);
        } else {
            throw new Exception("fixture $name not found as $file.");
        }
    }

    private static function performStatementsIn($file) {
        // if (windows)
        global $db;
        $logger = Logger::instance();
        foreach (explode(';', file_get_contents($file)) as $SQL) {
            if (trim($SQL)) {
                if (LOG_QUERIES) {
                    $logger->debug($SQL);
                }
                $db->query($SQL);
            }
        }
        // else
        # system("cat $file | scripts/console");
        // fi
    }

    protected function runSQL($SQL) {
        $this->db->query($SQL);
        $this->assertEquals('', $this->db->error, "$SQL\nFout in query");
    }

    protected function assertTableWhereHas($table, $where = [], $has_values = []) {
        $where_clause = implode(' AND ', array_map(
            function ($field, $val) { return "$field = '$val'"; }, array_keys($where), array_values($where)
        ));
        $where = '';
        if ($where_clause) {
            $where = "WHERE $where_clause ";
        }
        $vw = $this->db->query("SELECT * FROM $table $where");
        $this->assertInstanceOf(mysqli_result::class, $vw, $this->db->error);
        $this->assertEquals(1, $vw->num_rows, "kan record met $where_clause niet vinden");
        $row = $vw->fetch_assoc();
        foreach ($has_values as $key => $expected) {
            $this->assertEquals($expected, $row[$key], "verwacht $table:$where_clause $key=$expected");
        }
    }

    // TODO alle callers omleiden naar assertTableWhereHas
    protected function assertTableWithPK($table, $pk, $id, $values = []) {
        return $this->assertTableWhereHas($table, [$pk => $id], $values);
    }

    protected function assertTableRowcount($table, $count) {
        $this->assertEquals($count, $this->tableRowcount($table));
    }

    protected function tableRowcount($table) {
        $vw = $this->db->query("SELECT COUNT(*) FROM $table");
        $this->assertInstanceOf(mysqli_result::class, $vw, "vw is boolean " . ($vw ? 'true' : 'false') . " .  Error? " . $this->db->error);
        return $vw->fetch_row()[0];
    }

    protected function expectNewRecordsInTables(array $tables) {
        foreach ($tables as $table => $expected) {
            $this->tablecounts[$table] = $this->tableRowcount($table);
            $this->expectedincrements[$table] = $expected;
        }
    }

    protected function assertTablesGrew() {
        foreach ($this->tablecounts as $table => $count) {
            $this->assertEquals($this->expectedincrements[$table], $this->tableRowcount($table) - $count, "Unexpected rowcount in $table.");
        }
    }

}
