<?php

// this pulls UnitCase, IntegrationCase, Stringdiff in scope
set_include_path(get_include_path() . ':tests');
require 'vendor/autoload.php';
require "autoload.php";
        $_SERVER['HTTP_HOST'] = 'oer-dev';
        $_SERVER['REQUEST_SCHEME'] = 'http';
        $_SERVER['REQUEST_URI'] = 'tester';
Session::set_instance(new TestSession());
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
    system("cat $file | scripts/console");
} else {
    throw new Exception("setup $name not found as $file.");
}
}
