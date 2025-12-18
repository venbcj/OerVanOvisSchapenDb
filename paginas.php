<?php

/********************   MEERDERE PAGINA'S  ********************/
$pag = Session::get('PA'); // paginanummer dat moet worden ontouden als de pagina wordt ververst
$RPP = Session::get('RPP'); // standaard aantal regels per pagina
$paginator = new Paginator($tabel, $WHERE, $db, $pag, $RPP, $_SERVER['PHP_SELF']);

Session::set("RPP", $paginator->rpp);
$RPP = Session::get("RPP"); // zorgt dat regels per pagina wordt onthouden bij het opnieuw laden van de pagina

$page_numbers = $paginator->show_page_numbers(7);
Session::set("PA", $paginator->page);
$pag = Session::get("PA"); // zorgt dat paginanummer wordt onthouden bij het opnieuw laden van de pagina

$kzlRpp = $paginator->show_rpp();
/********************   EINDE   MEERDERE PAGINA'S  EINDE    ********************/
