<?php

use PHPUnit\Framework\TestCase;

class UnitCase extends TestCase {

    protected $db;

    protected function uses_db() {
        require_once "just_connect_db.php";
        global $db;
        $this->db = $db;
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

    protected static function runsetup($name) {
        if (file_exists($file = getcwd()."/db/setup/$name.sql")) {
            self::performStatementsIn($file);
        } else {
            throw new Exception("fixture $name not found as $file.");
        }
    }

    private static function performStatementsIn($file) {
        global $db;
        foreach (explode(';', file_get_contents($file)) as $SQL) {
            if (trim($SQL)) {
            $db->query($SQL);
        }
        }
        # system("cat $file | scripts/console");
    }

    protected function runSQL($SQL) {
        $this->db->query($SQL);
        $this->assertEquals('', $this->db->error, "$SQL\nFout in query");
    }

    protected function assertTableWithPK($table, $pk, $id, $values = []) {
        $vw = $this->db->query("SELECT * FROM $table WHERE $pk=$id");
        $this->assertInstanceOf(mysqli_result::class, $vw, $this->db->error);
        $this->assertEquals(1, $vw->num_rows);
        $row = $vw->fetch_assoc();
        foreach ($values as $key => $expected) {
            $this->assertEquals($expected, $row[$key], "verwacht $table:$pk $key=$expected");
        }
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
