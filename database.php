<?php
/*
21-12-2019 : Host, inlognaam, wachtwoord en databasenaam in een apart document geplaatst.
Bij de pdf scripts kan (in de productieomgeving) connect_db.php niet worden gebruikr om connectie met de database te maken. Om ergens eenmalig de inloggevens vast te leggen is dit database.php bestand gemaakt.

toegepast in :
- connect_db.php
- Bezet_pdf.php
 */

// varaibele t.b.v. Maandtotalen.php
$db_p = 'k36098_bvdvSchapenDb';
$db_d = 'k36098_bvdvschapendbs';
$db_t = 'k36098_bvdvSchapenDbT';

$host = "localhost";
$env = 'development';

switch ($_SERVER['HTTP_HOST']) {
case 'localhost:8080':
    $dtb = 'SchapenDb1';
    $user = 'root';
    $pw = 'usbw';
    break;
case 'test.oervanovis.nl':
    $dtb = 'k36098_bvdvSchapenDbT';
    $user = 'bvdvschaapt';
    $pw = 'MSenWL44';
    $env = 'test';
    break;
case 'ovis.oervanovis.nl':
    $dtb = 'k36098_bvdvSchapenDb';
    $user = 'bvdvschaapovis';
    $pw = 'MSenWL44';
    $env = 'production';
    break;
case 'ovis.alexander-ict.nl':
    $dtb = 'schapen';
    $user = 'varken';
    $pw = 'hok33hok77';
    $app = '/var/www/vhosts/alexander-ict.nl/ovis.alexander-ict.nl/';
    $env = 'test';
    break;
case 'oer-dev':
case 'basq':
    $dtb = "SchapenDb1";
    $user = 'oer';
    $pw = 'schaapn';
    $app = '/home/bas/html/oer/';
    break;
default:
    throw new Exception("No configuration for {$_SERVER['HTTP_HOST']}");
}
if (!defined('APP')) {
    define('APP', $app);
}
if (!defined('ENV')) {
    define('ENV', $env);
}
