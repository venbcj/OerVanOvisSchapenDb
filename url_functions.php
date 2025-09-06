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
