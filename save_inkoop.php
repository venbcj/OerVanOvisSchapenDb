<!-- 16-6-2018 gemaakt 
	28-11-2020 velde chkDel tegevoegd
	18-1-2022 SQL beveiligd met quotes
-->

<?php
/*Save_Artikel.php toegpast in :
	- Inkopen.php	*/


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
//echo '<br>'.'$recId = '.$recId.'<br>';
	
unset($updPrijs);
unset($delRec);

  foreach($id as $key => $value) {
	
	if ($key == 'txtPrijs' && !empty($value)){  $updPrijs = str_replace(',', '.', $value);  } 
	// else if ($key == 'txtPrijs' && empty($value)){ $updPrijs = 'NULL'; }

	if ($key == 'chkDel'){  $delRec = $value;  } 


									}

if(isset($recId) and $recId > 0) {

/*Wijzig prijs */
$wijzig_prijs = "UPDATE tblInkoop set prijs = '".mysqli_real_escape_string($db,$updPrijs)."' WHERE inkId = '".mysqli_real_escape_string($db,$recId)."' 	";
/*echo $wijzig_prijs.'<br>';*/		mysqli_query($db,$wijzig_prijs) or die (mysqli_error($db));


if(isset($delRec)) {
	$delete_inkoop = "DELETE FROM tblInkoop WHERE inkId = '".mysqli_real_escape_string($db,$recId)."' " ;
	mysqli_query($db,$delete_inkoop) or die (mysqli_error($db));

	
}
	
}

	}
				

?>