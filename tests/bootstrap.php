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
if (php_uname('n') == 'brave-beaver.85-215-36-84.plesk.page') {
    $_SERVER['HTTP_HOST'] = 'ovis.alexander-ict.nl';
}
set_include_path(get_include_path() . PATH_SEPARATOR . 'tests');
Session::set_instance(new TestSession());
// jammer: omdat onder windows 'cat' niet bestaat, moeten we hier de database-connectie alvast maken
require_once "just_connect_db.php";
global $db;
foreach (
    [
        // stamtabellen
        'tblActie',
        'tblDoel',
        'tblEenheid',
        'tblElement',
        'tblMoment',
        'tblRas',
        'tblRasuser',
        'tblReden',
        'tblRubriek',
        // minimale vulling
        'user-1',
        'tblLeden',
    ] as $name
) {
    if (file_exists($file = getcwd() . "/db/setup/$name.sql")) {
        foreach (explode(';', file_get_contents($file)) as $SQL) {
            if (trim($SQL)) {
                $db->query($SQL);
            }
        }
    } else {
        throw new Exception("setup $name not found as $file.");
    }
}
