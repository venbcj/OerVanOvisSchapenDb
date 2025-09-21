<?php

class IntegrationCase extends UnitCase {

    # php-8
    # protected string $output = '';
    # protected bool $redirected = false;
    protected $output = '';
    protected $redirected = false;

    protected function simulateGetRequest($path, $data = []) {
        $_SERVER['HTTP_HOST'] = 'oer-dev';
        $_SERVER['REQUEST_SCHEME'] = 'http';
        $_SERVER['REQUEST_URI'] = $path;
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['PHP_SELF'] = $path; // TODO: (BCB) hier niet meer om vragen in HokAfleveren
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

    protected function visit($path) {
        extract($GLOBALS);
        ob_start();
        include getcwd().$path;
        $this->output = ob_get_clean();
        $this->redirected = (http_response_code() == 302);
    }

    protected function assertRedirected() {
        $this->assertTrue($this->redirected);
    }

    protected function assertNoNoise() {
        $this->assertStringNotContainsString('Notice', $this->output);
        $this->assertStringNotContainsString('Warning', $this->output);
        $this->assertStringNotContainsString('Error', $this->output);
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
                $diff = StringDiff::create(5, $expected_nowhitespace, $actual_nowhitespace);
                $this->fail($diff->diff());
            }
        }
        $this->assertTrue(true); // anders maakt phpunit er een risky test van
    }

    protected function assertPresent($string) {
        $this->assertStringContainsString($string, $this->output);
    }

    protected function assertAbsent($string) {
        $this->assertStringNotContainsString($string, $this->output);
    }

    protected function assertFout($str) {
        if (false == strpos($this->output, $str)) {
            $complaint = 'Er is geen foutmelding.';
            if (preg_match("/alert\(('[^']*')\)/", $this->output, $matches)) {
                $complaint = PHP_EOL.'   Vindt '.$matches[1];
            }
            $this->fail("Verwacht '$str' in de 'foutmelding'. $complaint");
        }
    }

    protected function assertNotFout() {
        if ($found = preg_match("/alert\(('[^']*')\)/", $this->output, $matches)) {
            $complaint = PHP_EOL.'   Vindt '.$matches[1];
            $this->assertFalse($found, $complaint);
        }
    }

    protected function assertOptieCount($name, $count) {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($this->output);
        $path = new DOMXPath($dom);
        $select = $path->query('//select[@name="'.$name.'"]');
        $this->assertCount(1, $select, "kan select met name $name niet vinden");
        $options = $path->query('//select[@name="'.$name.'"]/option');
        $this->assertCount($count, $options, "select heeft niet de verwachte $count opties");
    }

}
