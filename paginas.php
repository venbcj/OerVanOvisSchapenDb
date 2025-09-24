<?php

/********************   MEERDERE PAGINA'S  ********************/
$page_nums = new Page_numbers($tabel, $WHERE, $db, $pag, $RPP, $_SERVER['PHP_SELF']);

Session::set("RPP", $page_nums->rpp);
$RPP = Session::get("RPP"); // zorgt dat regels per pagina wordt onthouden bij het opnieuw laden van de pagina

$page_numbers = $page_nums->show_page_numbers(7);
Session::set("PA", $page_nums->page);
$pag = Session::get("PA"); // zorgt dat paginanummer wordt onthouden bij het opnieuw laden van de pagina

$kzlRpp = $page_nums->show_rpp();
/********************   EINDE   MEERDERE PAGINA'S  EINDE    ********************/
