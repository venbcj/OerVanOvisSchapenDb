<?php

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
    return "<a href=\"{$GLOBALS['url']}$path\"$attribute_clause>$caption</a>";
}
