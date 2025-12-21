<?php

class Logger {

    public static function instance() {
        return new self();
    }

    public function error($msg, $with_trace = true) {
        error_log($this->moment() . ' [ERROR] ' . $msg . PHP_EOL, 3, $this->file());
        if ($with_trace) {
            error_log($this->trace(), 3, $this->file());
        }
        # throw new Exception($msg);
    }

    private function file() {
        if (!defined('APP')) {
            return 'log/development.log';
        }
        return APP . 'log/development.log';
    }

    public function debug($msg, $with_trace = false) {
        error_log($this->moment() . ' [DEBUG] ' . $msg . PHP_EOL, 3, $this->file());
        if ($with_trace) {
            error_log($this->trace(), 3, $this->file());
        }
    }

    private function moment() {
        return date("Y-m-d H:i:s");
    }

    private function trace() {
        $tr = debug_backtrace();
        array_shift($tr);
        #array_shift($tr);
        $el = array_map([__class__, 'line'], $tr);
        return PHP_EOL . implode(PHP_EOL, $el) . PHP_EOL;
    }

    private function line($el) {
        $res = '';
        extract($el);
        if (isset($class)) {
            $res .= $class . '.';
        }
        if (isset($function)) {
            if ($function == '__callStatic') {
                $function = '*' . $args[0];
            }
            $res .= $function . '()';
        }
        if (isset($file)) {
            $res .= " from " . $this->just_file($file);
        }
        if (isset($line)) {
            $res .= "($line)";
        }
        return $res;
    }

    private function just_file($file) {
        if (!defined('APP')) {
            return $file;
        }
        return str_replace(APP, '', $file);
    }

}
