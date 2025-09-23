<?php

class Logger {

    public static function error($msg) {
        error_log($msg, 3, 'log/development.log');
        # exit;
    }

}
