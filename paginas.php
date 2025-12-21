<?php

$paginator = new Paginator($tabel, $WHERE, null, Session::get('PA'), Session::get('RPP'), $_SERVER['PHP_SELF']);
Session::set("RPP", $paginator->records_per_page);
Session::set("PA", $paginator->page);
