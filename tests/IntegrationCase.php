<?php

class IntegrationCase extends UnitCase {

    # php-8
    # protected string $output = '';
    # protected bool $redirected = false;
    protected $output = '';
    protected $redirected = false;
    protected $tablecounts = [];
    protected $expectedincrements = [];

    protected $restore_keys_before = false;
    protected $restore_keys_after = false;

    public function setup(): void {
        $this->uses_db();
        if ($this->restore_keys_before) {
            $this->restore_keys();
        }
        $this->db->begin_transaction();
    }

    public function teardown(): void {
        $this->db->rollback();
        if ($this->restore_keys_after) {
            $this->restore_keys();
        }
    }

    // subclass may implement this and set restore_keys_(before,after)
    protected function restore_keys() {
    }

    protected function simulateGetRequest($path, $data = []) {
        $_SERVER['HTTP_HOST'] = 'oer-dev';
        $_SERVER['REQUEST_SCHEME'] = 'http';
        $_SERVER['REQUEST_URI'] = $path;
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = [];
        $_POST = [];
        foreach ($data as $key => $value) {
            $_GET[$key] = $value;
        }
        $_REQUEST = $_GET;
    }

    protected function simulatePostRequest($path, $data = []) {
        $_SERVER['HTTP_HOST'] = 'oer-dev';
        $_SERVER['REQUEST_SCHEME'] = 'http';
        $_SERVER['REQUEST_URI'] = $path;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_GET = [];
        $_POST = [];
        foreach ($data as $key => $value) {
            $_POST[$key] = $value;
        }
        $_REQUEST = $_POST;
    }

    protected function get($path, $data = []) {
        $this->simulateGetRequest($path, $data);
        $this->visit($path);
    }

    protected function post($path, $data = []) {
        $this->simulatePostRequest($path, $data);
        $this->visit($path);
    }

    protected function patch($path, $data = []) {
        $this->simulatePostRequest($path, $data);
        $_SERVER['REQUEST_METHOD'] = 'PATCH';
        $this->visit($path);
    }

    protected function visit($path) {
        extract($GLOBALS);
        ob_start();
    #    try {
            include getcwd().$path;
    #    } catch (Exception $e) {
    #        echo $e->getMessage();
    #    }
        $this->output = ob_get_clean();
    }

    protected function assertRedirected() {
        $this->assertTrue(Response::isRedirected());
    }

    protected function assertNoNoise() {
        $this->assertStringNotContainsString('Notice', $this->output);
        $this->assertStringNotContainsString('Warning', $this->output);
        $this->assertStringNotContainsString('Error', $this->output);
    }

    protected function assertWhiteBody() {
        $matches = [];
        // NOTE de s-modifier van het patroon zorgt ervoor dat . ook newlines matcht.
        $this->assertEquals(1, preg_match('#<body>(.*)</body>#s', $this->output, $matches), 'Geen body in pagina?'.$this->output);
        $this->assertEquals('', $matches[1]);
    }

    protected function approve() {
        # phpunit 8:
        $file = getcwd().'/tests/approval/'.$this->getName();
        # phpunit 12:
        # $file = getcwd().'/tests/approval/'.$this->name();
        $expected_file = $file.'.expected';
        $actual_file = $file.'.actual';
        if (!file_exists($expected_file)) {
            touch($expected_file);
        }
        file_put_contents($actual_file, $this->output);
        $expected = file_get_contents($expected_file);
        if ($expected != $this->output) {
            $expected_nowhitespace = preg_replace('/\s/', '', $expected);
            $actual_nowhitespace = preg_replace('/\s/', '', $this->output);
            if ($expected_nowhitespace != $actual_nowhitespace) {
                $diff = Stringdiff::create(5, $expected_nowhitespace, $actual_nowhitespace);
                $this->fail($diff->diff());
            }
        }
        $this->assertTrue(true); // anders maakt phpunit er een risky test van
    }

    protected function assertPresent($string) {
        if (false === strpos($this->output, $string)) {
            $this->fail("pagina moet $string bevatten");
        }
        $this->assertTrue(true);
    }

    protected function assertAbsent($string) {
        $this->assertStringNotContainsString($string, $this->output);
    }

    // NOTE: rekent een gedeeltelijke match ook goed.
    protected function assertFout($str) {
        if (false == strpos($this->output, $str)) {
            $complaint = 'Er is geen foutmelding.';
            if (preg_match("/alert\(('[^']*')\)/", $this->output, $matches)) {
                $complaint = PHP_EOL . '   Vindt ' . $matches[1];
            }
            $this->fail("Verwacht '$str' in de 'foutmelding'. $complaint");
        }
        $this->assertTrue(true); // anders krijg ik een Risky Test
    }

    protected function assertNotFout() {
        if ($found = preg_match("/alert\(('[^']*')\)/", $this->output, $matches)) {
            $complaint = PHP_EOL.'Er zou geen melding-alert in de pagina moeten zitten. Vindt '.$matches[1];
            $this->assertFalse($found, $complaint);
        }
        $this->assertTrue(true); // anders krijg ik een Risky Test
    }

    // Heeft <select name=$name> $count <option>s?
    protected function assertOptieCount($name, $count) {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($this->output);
        $path = new DOMXPath($dom);
        $select = $path->query('//select[@name="'.$name.'"]');
        $this->assertCount(1, $select, "kan select met name=$name niet vinden");
        $options = $path->query('//select[@name="'.$name.'"]/option');
        $this->assertCount($count, $options, "select heeft niet de verwachte $count opties");
    }

    // heeft <table id=$id> $count <tr>s?
    protected function assertTrCount($id, $count) {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($this->output);
        $path = new DOMXPath($dom);
        $table = $path->query('//table[@id="'.$id.'"]');
        $this->assertCount(1, $table, "kan table met id $id niet vinden");
        $rows = $path->query('//table[@id="'.$id.'"]/tr');
        $this->assertCount($count, $rows, "table heeft niet de verwachte $count rijen");
    }

    protected function makeUserFilesPresent() {
        $workdir = getcwd();
        chdir('../..');
        $root_dir = getcwd();
        if (!file_exists('./user_1/Readerversies')) {
            mkdir('./user_1/Readerversies', 0777, true);
        }
        touch('./user_1/Readerversies/appfile');
        touch('./user_1/Readerversies/readerfile');
        chdir($workdir);
        return $root_dir;
    }

    protected function makeUserFilesAbsent() {
        $workdir = getcwd();
        chdir('../..');
        $root_dir = getcwd();
        if (file_exists($path = './user_1/Readerversies/appfile')) {
            $this->assertTrue(unlink($path), 'cannot remove appfile?');
        }
        if (file_exists($path = './user_1/Readerversies/readerfile')) {
            $this->assertTrue(unlink($path), 'cannot remove appfile?');
        }
        if (file_exists($path = './user_1/Readerversies')) {
            rmdir('./user_1/Readerversies');
        }
        chdir($workdir);
        return $root_dir;
    }

}
