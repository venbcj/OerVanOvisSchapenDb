<?php
/* 6-3-2015 : sql beveiligd 
30-5-2020 : hidden velden ctrScan en ctrActief verwijderd en aangepast op Agrident reader
02-08-2020 : veld sort toegevoegd */

function getNaamFromKey($string) {
    $split_naam = explode('_', $string);
    return $split_naam[0];
}

function getIdFromKey($string) {
    $split_Id = explode('_', $string); 
    return $split_Id[1];
}

foreach($_POST as $fldname => $fldvalue) {  //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    
    $multip_array[getIdFromKey($fldname)][getNaamFromKey($fldname)] = $fldvalue;  // Opbouwen van een Multidimensional array met 2 indexen. [Id] [naamveld] en een waarde nl. de veldwaarde. 
}
foreach($multip_array as $recId => $id) {  
unset($fldScan);
unset($fldSort);

#echo '<br>'.'$recId = '.$recId.'<br>';

if(!empty($recId)) {


foreach($id as $key => $value) {

	if ($key == 'txtScan' && !empty($value)) { $fldScan = $value; }

	if ($key == 'txtSort' && !empty($value)) { $fldSort = $value; }
	
	if ($key == 'chkActief' ) { $fldActief = $value; /*echo $key.'='.$value."<br/>";*/ }

								}


$zoek_db_waardes = mysqli_query($db,"
SELECT scan, sort, actief
FROM tblHok
WHERE hokId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));


while($row = mysqli_fetch_assoc($zoek_db_waardes))
	{ $dbScan = $row['scan'];
	  $dbSort = $row['sort'];
	$dbActief = $row['actief']; }

/*echo '$fldScan = '.$fldScan.'<br>';									
echo '$dbScan = '.$dbScan.'<br>';	*/
/*echo '$fldSort = '.$fldSort.'<br>';									
echo '$dbSort = '.$dbSort.'<br>';	*/

if($reader == 'Biocontrol' && $fldScan <> $dbScan) { //$fldScan bestaat niet bij Agrident reader
// Zoeken naar dubbel scancode
$dublicate_scannr = mysqli_query($db,"
SELECT hoknr, scan
FROM tblHok
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and ". db_null_filter('scan', $fldScan) ." and scan is not NULL
") or die(mysqli_error($db));
	while ( $zoekscannr = mysqli_fetch_assoc($dublicate_scannr)) { $hoknum = $zoekscannr['hoknr']; $aantsc = $zoekscannr['scan']; }
// EINDE Zoeken naar dubbel scancode
if(isset($aantsc))	{ $fout = " Het scannr bestaat al bij verblijf ".$hoknum."."; }
else {
$updateScan = "UPDATE tblHok SET scan = ". db_null_input($fldScan) ." WHERE hokId = '".mysqli_real_escape_string($db,$recId)."' ";	
		mysqli_query($db,$updateScan) or die (mysqli_error($db));
	}
		}

if($reader == 'Agrident' && $fldSort <> $dbSort) {

	$updateSort = "UPDATE tblHok SET sort = ". db_null_input($fldSort) ." WHERE hokId = '".mysqli_real_escape_string($db,$recId)."' ";
	/*echo $updateSort; */ mysqli_query($db, $updateSort) or die (mysqli_error($db));
}

if($fldActief <> $dbActief) {	
// Zoeken naar hoeveelheid schapen per hok
$aanwezige_schapen = mysqli_query($db,"SELECT hoknr, nu aantal FROM (".$vw_HoknBeschikbaar.") hb WHERE hokId = ".mysqli_real_escape_string($db,$recId)." ") or die (mysqli_error($db));
	while ($rij = mysqli_fetch_assoc($aanwezige_schapen))
		{	
			$hoknr = "{$rij['hoknr']}";
			$inhok = "{$rij['aantal']}";
		}
// EINDE Zoeken naar hoeveelheid schapen per hok




	if (isset($inhok) && $inhok > 0 && $fldActief == 0 ) 
	 {	if ($inhok == 1 ) {$fout = "$hoknr kan niet buiten gebruik worden gesteld omdat er nog 1 schaap in zit.";}	
		else {$fout = "$hoknr kan niet buiten gebruik worden gesteld omdat er nog $inhok schapen in zitten.";}
	 }

else {
	$updateHok = "UPDATE tblHok SET actief = '". mysqli_real_escape_string($db,$fldActief) ."' WHERE hokId = '".mysqli_real_escape_string($db,$recId)."' ";
		mysqli_query($db,$updateHok) or die (mysqli_error($db));
	 }

}







	
	
	}

}

?>
					
	