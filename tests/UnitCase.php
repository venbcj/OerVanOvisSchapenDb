<?php

use PHPUnit\Framework\TestCase;

class UnitCase extends TestCase {

    protected $db;

    protected static function runfixture($name) {
        if (file_exists($file = getcwd()."/tests/fixtures/$name.sql")) {
            system("cat $file | scripts/console");
        } else {
            throw new Exception("fixture $name not found as $file.");
        }
    }

    protected function assertTableWithPK($table, $pk, $id, $values) {
        $vw = $this->db->query("SELECT * FROM $table WHERE $pk=$id");
        $this->assertEquals(1, $vw->num_rows);
        $row = $vw->fetch_assoc();
        foreach ($values as $key => $expected) {
            $this->assertEquals($expected, $row[$key]);
        }
    }

}
