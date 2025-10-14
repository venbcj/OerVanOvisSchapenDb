<?php

if (php_uname('n') != 'basq' && $_SERVER['HTTP_HOST'] != 'ovis.alexander-ict.nl') {
    return;
}

$id = $_GET['id'] ?? 1;

session_start();
$_SESSION['U1'] = $id;
$_SESSION['W1'] = $id;
$_SESSION['I1'] = $id;
include "just_connect_db.php";
$vw = $db->query("SELECT lidId FROM tblLeden");
echo '<select size="'.$vw->num_rows.'">';
while ($row = $vw->fetch_row()) {
    echo '<option>'.$row[0].'</option>';
}
echo '</select>';
