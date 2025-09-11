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

}
