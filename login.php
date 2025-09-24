<?php
/* 8-4-2015 : sql beveiligd
23-11-2015 : Berekening breddte kzlWerknr toegevoegd en query en berekening kzlHoknr toegevoegd 13-2-2017 : breedte kan niet kleiner zijn dan 60
3-12-2015 : ubn aan sessie toegevoegd
12-12-2015 : naast Id ook ubn rendac opgevragd
19-12-2015 : modfin toegevoegd
16-09-2016 : modules gesplitst
29-10-2016 : query modules bij inloggen toegevoegd zodat menu1.php goed wordt opgebouwd bij alleen melden
27-07-2017 : modbeheer toegevoegd
18-03-2018 : _SESSION["PA"]; en _SESSION["RPP"]; toegevoegd.
13-05-2018 : _SESSION["ID"]  _SESSION["DT1"]  _SESSION["BST"] toegevoegd
15-03-2020 : gebruik van welke reader toegevoegd
16-01-2021 : function db_quote toegevoegd
12-08-2023 : include basisfuncties toegevoegd en alle functions daar naar verplaatst
24-10-2023 : zoek_laatste_versie toegevoegd 26-10-2023 update_tblLeden toegevoegd
12-01-2024 : _SESSION["KZ"]; toegevoegd. 14-01-2024 controle toegevoegd op juiste connectie met de database
09-11-2024 : w_hok = 12+(8*lengte); gewijzigd naar w_hok = 15+(9*lengte);
04-01-2025 : include header.php en include header_logout.php hier in geplaatst
23-02-2025 : _SESSION["Fase"] en _SESSION["CNT"] toegevoegd
15-07-2015 : ubn uit sessie gehaald omdat er per 10-7-2025 meerdere ubn's bij 1 gebruiker kunnen bestaan.
 */

include "login_logic.php";
foreach ($output as $view) {
    include $view;
}
