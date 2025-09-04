<?php
/* 
21-12-2019 : Host, inlognaam, wachtwoord en databasenaam in een apart document geplaatst. 
Bij de pdf scripts kan (in de productieomgeving) connect_db.php niet worden gebruikr om connectie met de database te maken. Om ergens eenmalig de inloggevens vast te leggen is dit database.php bestand gemaakt.

toegepast in :
- connect_db.php
- Bezet_pdf.php */

// varaibele t.b.v. Maandtotalen.php
$db_p = 'k36098_bvdvSchapenDb';
$db_d = 'k36098_bvdvschapendbs';
$db_t = 'k36098_bvdvSchapenDbT';

$host = "localhost"; $user = "root"; $pw = "usbw"; $dtb = "SchapenDb1";
if (php_uname('n') == 'basq') {
    $user = 'oer';
    $pw = 'schaapn';
}


?>
