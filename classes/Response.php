<?php

class Response {

    const PRODUCTION = 1;
    const TEST = 2;

    private static $redirected = false;
    private static $mode = self::PRODUCTION;

    public static function isRedirected() {
        return self::$redirected;
    }

    public static function setTest() {
        self::$mode = self::TEST;
    }

    public static function setProduction() {
        self::$mode = self::PRODUCTION;
    }

    public static function redirect($location) {
        Logger::debug("redirect to $location, mode=".self::$mode);
        self::$redirected = true;
        if (self::$mode == self::PRODUCTION) {
            header("Location: $location");
        }
    }

}
