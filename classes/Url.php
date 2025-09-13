<?php

class Url {

    public static function getNameFromKey($key) {
        $array = explode('_', $key);
        return $array[0];
    }

    public static function getIdFromKey($key) {
        $array = explode('_', $key);
        return $array[1];
    }

    public static function redirect($path) {
        header("Location: ".self::getWebroot().$path);
        exit();
    }

    public static function getTagId() {
        $host = $_SERVER['HTTP_HOST'];
        if ($host == 'localhost:8080') {
            $tagid = 'balkOntw';
        }
        if ($host == 'test.oervanovis.nl') {
            $tagid = 'balkTest';
        }
        if ($host == 'demo.oervanovis.nl') {
            $tagid = 'balkDemo';
        }
        if ($host == 'ovis.oervanovis.nl') {
            $tagid = 'balkProd';
        }
        if (php_uname('n') == 'basq') {
            $tagid = 'balkOntw';
        }
        return $tagid;
    }

    public static function getWebroot() {
        #$root = "http://localhost:8080/Schapendb/";
        if ($_SERVER['HTTP_HOST'] == 'localhost:8080') {
            $root = 'http://'.$_SERVER['HTTP_HOST'].'/Schapendb/';
        } else {
            $root = 'https://'.$_SERVER['HTTP_HOST'].'/';
        }
        #$root = "http://testapp.masterwebsite.nl/";
        if (php_uname('n') == 'basq') {
            $root = 'http://oer-dev/';
        }
        return $root;
    }

}
