<?php

class Logger {

    public static function error($msg) {
        error_log('ERROR: '.$msg.PHP_EOL, 3, 'log/development.log');
        # throw new Exception($msg);
    }

    public static function debug($msg) {
        error_log('DEBUG: '.$msg.PHP_EOL, 3, 'log/development.log');
        # throw new Exception($msg);
    }

}
