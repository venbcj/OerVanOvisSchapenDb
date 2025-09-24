<?php

// this pulls UnitCase, IntegrationCase, Stringdiff in scope
set_include_path(get_include_path() . ':tests');
require 'vendor/autoload.php';
require "autoload.php";
        $_SERVER['HTTP_HOST'] = 'oer-dev';
        $_SERVER['REQUEST_SCHEME'] = 'http';
        $_SERVER['REQUEST_URI'] = 'tester';
Session::set_instance(new TestSession());
