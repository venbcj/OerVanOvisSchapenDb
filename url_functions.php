<?php

function redirect($path) {
    header("Location: ".getWebroot().$path);
    exit();
}

function is_logged_in() {
    return isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"]);
}

function getTagId() {
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
        $tagid = 'balkCoach';
    }
    return $tagid;
}

# hiermee maak je een complete menu-link
function link_to($caption, $path, $attributes = []) {
    $modern = true;
    if ($modern) {
        $attribute_clause = implode(
            ' ',
            array_map(
                function ($attr, $val) {
                    return " $attr=\"$val\"";
                },
                array_keys($attributes),
                array_values($attributes)
            )
        );
    } else {
        $attribute_clause = '';
        if ($attributes) {
            $attribute_clause = " style = 'color : ".current($attributes)."'";
        }
    }
    return "<a href=\"".getWebroot()."$path\"$attribute_clause>$caption</a>";
}

function getWebroot() {
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
