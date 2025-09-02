<?php

/*
$pstId = $_GET['delete_id'];

$delete_inkoop = "DELETE FROM tblInkoop WHERE inkId = ".mysqli_real_escape_string($db,$pstId) ;
//	mysqli_query($db,$delete_inkoop) or die (mysqli_error($db));

echo $delete_inkoop;*/

function getNameFromKey($key) {
    $array = explode('_', $key);
    return $array[0];
}

function getIdFromKey($key) {
    $array = explode('_', $key);
    return $array[1];
}

$array = array();

foreach($_POST as $key => $value) {
    
    $array[getIdFromKey($key)][getNameFromKey($key)] = $value;
}
foreach($array as $recId => $id) {
echo '<br>'.'$recId = '.$recId.'<br>';
}

?>