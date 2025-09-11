<?php
// 20-12-2019 : http gewijzigd naar https
/* Naast verderop genoemde include bestanden toegepast in :
    - Eenheden.php
    - InsAanwas.php
    - InsAfleveren.php
    - InsGeboortes.php Ivm noodzakelijk voor include header
    - InsMedicijn.php
    - InsOverplaats.php
    - InsSpenen.php
    - InsUitval.php
    - Leveranciers.php
    - Uitval.php
    - Voer.php
 */


global $url;
$url = Url::getWebroot();

// strtok zorgt ervoor dat alles na de paginanaam wordt verwijderd. bron : http://stackoverflow.com/questions/6969645/how-to-remove-the-querystring-and-get-only-the-url
$curr_url = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].strtok($_SERVER["REQUEST_URI"], '?');
// TODO: whitelisten is veiliger dan dit blacklisten
// TODO: dit wordt sowieso nog anders als je eenmaal een front controller hebt. --BCB
$forbidden_files = [
    "connect_db.php",
    "header.php",
    "importReader.php",
    "importRespons.php",
    "kzl.php",
    "login.php",
    "maak_Request.php",
    "menu1.php",
    "menuBeheer.php",
    "menuFinance.php",
    "menuInkoop.php",
    "menuRapport.php",
    "msg.php",
    "passw.php",
    "post_readerAanw.php",
    "post_readerAflev.php",
    "post_readerGeb.php",
    "post_readerMed.php",
    "post_readerOvp.php",
    "post_readerSpn.php",
    "post_readerUitv.php",
    "responscheck.php",
    "titel.php",
    "uploadReader.php",
    "url.php",
    "vw_Reader.php",
];
foreach ($forbidden_files as $controller_name) {
    if ($curr_url == $url.$controller_name) {
        Url::redirect('index.php');
    }
}
