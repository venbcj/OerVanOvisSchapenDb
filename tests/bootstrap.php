<?php

// this pulls UnitCase, IntegrationCase, Stringdiff in scope
require 'vendor/autoload.php';
require "autoload.php";
        $_SERVER['HTTP_HOST'] = 'oer-dev';
        $_SERVER['REQUEST_SCHEME'] = 'http';
        $_SERVER['REQUEST_URI'] = 'tester';
if (php_uname('n') == 'LAPTOP-GCTJE203') {
        $_SERVER['HTTP_HOST'] = 'localhost';
}
set_include_path(get_include_path() . ':tests');
Session::set_instance(new TestSession());
require_once "just_connect_db.php";
global $db;
foreach ([
    // stamtabellen
    'tblActie',
    'tblDoel',
    'tblEenheid',
    'tblElement',
    'tblMoment',
    'tblRas',
    'tblReden',
    'tblRubriek',
    // minimale vulling
    'user-1',
    'tblLeden',
] as $name) {
if (file_exists($file = getcwd()."/db/setup/$name.sql")) {
        foreach (explode(';', file_get_contents($file)) as $SQL) {
            if (trim($SQL)) {
            $db->query($SQL);
    }
        }

} else {
    throw new Exception("setup $name not found as $file.");
}
}
