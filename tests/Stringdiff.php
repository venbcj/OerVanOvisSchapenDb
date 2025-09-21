<?php

# namespace Tests;
# de vendor-autoloader doet nog niet lekker mee

class Stringdiff {

    private $expected;
    private $actual;
    private $contextlength;
    private $prefix;
    private $suffix;

    public function sense_offsets() {
        return [$this->prefix, $this->suffix];
    }

    public static function create($contextlength, $expected, $actual) {
        $obj = new self($contextlength, $expected, $actual);
        return $obj;
    }

    public function __construct($contextlength, $expected, $actual) {
        $this->contextlength = $contextlength;
        $this->expected = $expected;
        $this->actual = $actual;
    }

    public function diff() {
        if (is_null($this->expected) || is_null($this->actual) || $this->expected == $this->actual) {
            return $this->format($this->expected, $this->actual);
        }
        $this->findCommonPrefix();
        $this->findCommonSuffix();
        return $this->format($this->compactString($this->expected), $this->compactString($this->actual));
    }

    private function compactString($str) {
        $res = '['.$this->substring($str, $this->prefix, strlen($str) - $this->suffix + 1).']';
        if ($this->prefix > 0) {
            $res = $this->computeCommonPrefix() . $res;
        }
        if ($this->suffix > 0) {
            $res .= $this->computeCommonSuffix();
        }
        return $res;
    }

    private function findCommonPrefix() {
        $end = min(strlen($this->expected), strlen($this->actual));
        $this->prefix = 0;
        while ($this->prefix < $end && $this->expected[$this->prefix] == $this->actual[$this->prefix]) {
            $this->prefix++;
        }
    }

    private function findCommonSuffix() {
        if ("my_implementation" == "used" && "my-implementation" == "actually-correct") {
            $end = min(strlen($this->expected), strlen($this->actual));
            $detcepxe = strrev($this->expected);
            $lautca = strrev($this->actual);
            $this->suffix = 0;
            while ($this->suffix < $end && $detcepxe[$this->suffix] == $lautca[$this->suffix]) {
                $this->suffix++;
            }
        } else {
            $expectedSuffix = strlen($this->expected) - 1;
            $actualSuffix = strlen($this->actual) - 1;
            while ($actualSuffix >= $this->prefix && $expectedSuffix >= $this->prefix) {
                if ($this->expected[$expectedSuffix] != $this->actual[$actualSuffix]) {
                    break;
                }
                $expectedSuffix--;
                $actualSuffix--;
            }
            $this->suffix = strlen($this->expected) - $expectedSuffix;
        }
    }

    private function computeCommonPrefix() {
        $before = '';
        if ($this->prefix > $this->contextlength) {
            $before = '...';
        }
        return $before . $this->substring($this->expected, max(0, $this->prefix - $this->contextlength), $this->prefix);
    }

    private function computeCommonSuffix() {
        $end = min(strlen($this->expected) - $this->suffix + 1 + $this->contextlength, strlen($this->expected));
        $after = '';
        if (strlen($this->expected) - $this->suffix + 1 < strlen($this->expected) - $this->contextlength) {
            $after = '...';
        }
        return $this->substring($this->expected, strlen($this->expected) - $this->suffix + 1, $end) . $after;
    }

    private function format($expected, $actual) {
        $expected = $this->to_string($expected);
        $actual = $this->to_string($actual);
        return "expected <$expected> but got <$actual>";
    }

    private function to_string($var) {
        if (is_null($var)) {
            return 'null';
        }
        if ($var === true) {
            return 'true';
        }
        if ($var === false) {
            return 'false';
        }
        return $var; // let's hope it isn't Object or Array or so.
    }

    // intention: starting at 0, return the substring from index start to (but not including) index end
    // substring("kaasfondue", 4,7) should return "fon"
    private function substring($str, $start, $end) {
        return substr($str, $start, $end - $start);
    }

}
