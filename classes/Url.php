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
    }

    public static function getTagId() {
        $host = $_SERVER['HTTP_HOST'];
        switch ($host) {
        case 'oer-dev':
        case 'localhost:8080':
            $tagid = 'balkOntw';
            break;
        case 'test.oervanovis.nl':
            $tagid = 'balkTest';
            break;
        case 'demo.oervanovis.nl':
            $tagid = 'balkDemo';
            break;
        case 'ovis.oervanovis.nl':
            $tagid = 'balkProd';
            break;
        case 'ovis.alexander-ict.nl':
            $tagid = 'balkTest';
            break;

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
