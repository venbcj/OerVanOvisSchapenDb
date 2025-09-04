<?php

use PHPUnit\Framework\TestCase;

class EndToEndCase extends TestCase {

    protected string $output = '';

    private function simulateWebRequest($path) {
        $_SERVER['HTTP_HOST'] = 'basq';
        $_SERVER['REQUEST_URI'] = $path;
    }

    protected function visit($path) {
        $this->simulateWebRequest($path);
        ob_start();
        include getcwd().$path;
        $this->output = ob_get_clean();
    }

    protected function approve() {
        $file = getcwd().'/tests/approval/'.$this->name();
        $expected_file = $file.'.expected';
        $actual_file = $file.'.actual';
        if (!file_exists($expected_file)) {
            touch($expected_file);
        }
        file_put_contents($actual_file, $this->output);
        if (file_get_contents($expected_file) != $this->output) {
            $this->fail("$actual_file is not same as $expected_file");
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
