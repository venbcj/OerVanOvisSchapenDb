<?php
/* include "passw.php" toegepast in :
- connect_db.php
- post_readerGeb.php */

include "database.php";
 
$db = mysqli_connect($host, $user, $pw, $dtb);
if ($db == false) {
    throw new Exception('Connectie database niet gelukt');
}

include "passw.php";
