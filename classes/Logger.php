<?php

define('APP', '/usr/local/var/chi/proj/OerVanOvisSchapenDb/');

class Logger {

    public static function error($msg) {
        error_log('ERROR: '.$msg.PHP_EOL, 3, 'log/development.log');
        error_log(self::trace(), 3, 'log/development.log');
        # throw new Exception($msg);
    }

    public static function debug($msg) {
        error_log('DEBUG: '.$msg.PHP_EOL, 3, 'log/development.log');
        # throw new Exception($msg);
    }

    private static function trace() {
        $tr = debug_backtrace();
        array_shift($tr);
        #array_shift($tr);
        $el = array_map([__class__, 'line'], $tr);
        return PHP_EOL . implode(PHP_EOL, $el) . PHP_EOL;
    }

    private static function line($el) {
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
