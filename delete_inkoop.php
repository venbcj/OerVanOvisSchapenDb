<?php


$pstId = $_GET['delete_id'];

$delete_inkoop = "DELETE FROM tblInkoop WHERE inkId = ".mysqli_real_escape_string($db,$pstId) ;
	mysqli_query($db,$delete_inkoop) or die (mysqli_error($db));


?>