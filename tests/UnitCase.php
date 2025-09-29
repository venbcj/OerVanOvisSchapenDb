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
            system("cat $file | scripts/console");
        } else {
            throw new Exception("fixture $name not found as $file.");
        }
    }

    protected static function runsetup($name) {
        if (file_exists($file = getcwd()."/db/setup/$name.sql")) {
            system("cat $file | scripts/console");
        } else {
            throw new Exception("fixture $name not found as $file.");
        }
    }

    protected function runSQL($SQL) {
        $this->db->query($SQL);
        $this->assertEquals('', $this->db->error, "$SQL\nFout in query");
    }

    protected function assertTableWithPK($table, $pk, $id, $values = []) {
        $vw = $this->db->query("SELECT * FROM $table WHERE $pk=$id");
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

}
