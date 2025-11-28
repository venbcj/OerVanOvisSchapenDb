<?php

class Logger {

    public static function instance() {
        return new self();
    }

    public function error($msg) {
        error_log($this->moment().' [ERROR] '.$msg.PHP_EOL, 3, APP.'log/development.log');
        error_log($this->trace(), 3, APP.'log/development.log');
        # throw new Exception($msg);
    }

    public function debug($msg) {
        error_log($this->moment().' [DEBUG] '.$msg.PHP_EOL, 3, APP.'log/development.log');
        # throw new Exception($msg);
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
            $res .= " from " . str_replace(APP, '', $file);
        }
        if (isset($line)) {
            $res .= "($line)";
        }
        return $res;
    }

}
