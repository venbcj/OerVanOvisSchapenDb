<?php

use Tests\Stringdiff;

use PHPUnit\Framework\TestCase;

class EndToEndCase extends TestCase {

    protected string $output = '';
    protected bool $redirected = false;

    protected static function runfixture($name) {
        if (file_exists($file = getcwd()."/tests/fixtures/$name.sql")) {
            system("cat $file | scripts/console");
        } else {
            throw new Exception("fixture $name not found as $file.");
        }
    }

    private function simulateGetRequest($path, $data) {
        $_SERVER['HTTP_HOST'] = 'basq';
        $_SERVER['REQUEST_SCHEME'] = 'http';
        $_SERVER['REQUEST_URI'] = $path;
        $_SERVER['PHP_SELF'] = $path; // TODO: (BCB) hier niet meer om vragen in HokAfleveren
        $_GET = [];
        $_POST = [];
        foreach ($data as $key => $value) {
            $_GET[$key] = $value;
        }
    }

    private function simulatePostRequest($path, $data) {
        $_SERVER['HTTP_HOST'] = 'basq';
        $_SERVER['REQUEST_SCHEME'] = 'http';
        $_SERVER['REQUEST_URI'] = $path;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_GET = [];
        $_POST = [];
        foreach ($data as $key => $value) {
            $_POST[$key] = $value;
        }
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
        $file = getcwd().'/tests/approval/'.$this->name();
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

}
