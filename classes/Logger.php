<?php

class Logger {

    public static function error($msg) {
        error_log($msg.PHP_EOL, 3, 'log/development.log');
        # throw new Exception($msg);
    }

}
