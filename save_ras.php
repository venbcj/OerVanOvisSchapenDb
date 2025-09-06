<!-- 15-11-2015 : gemaakt 
30-5-2020 : hidden velden txtId, ctrScan en ctrActief verwijderd -->

<?php
/* toegepast in :
	- Ras.php */
	
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

//echo '<br>'.'$recId = '.$recId.'<br>';

if(!empty($recId)) {


 foreach($id as $key => $value) { 

    if ($key == 'txtScan' && !empty($value)) { $fldScan = $value; } 

    if ($key == 'txtSort' && !empty($value)) { $fldSort = $value; } 
	
    if ($key == 'chbActief') {  $fldActief = $value; /*echo $key.'='.$value."<br/>";*/  }  	 

								}	


$zoek_db_waardes = mysqli_query($db,"
SELECT ru.scan, ru.sort, ru.actief
FROM tblRas r
 join tblRasuser ru on (r.rasId = ru.rasId)
WHERE r.rasId = '". mysqli_real_escape_string($db,$recId) ."' and ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."'
") or die (mysqli_error($db));

while($row = mysqli_fetch_assoc($zoek_db_waardes))
	{ $dbScan = $row['scan'];
	  $dbSort = $row['sort'];
	$dbActief = $row['actief']; }

/*echo '$fldScan = '.$fldScan.'<br>';
echo '$dbScan = '.$dbScan.'<br>'; */

if($reader == 'Biocontrol' && $fldScan <> $dbScan) { //$fldScan bestaat niet bij Agrident reader
// Zoeken naar dubbel scancode
$dublicate_scannr = mysqli_query($db,"
SELECT scan
FROM tblRasuser
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and ". db_null_filter('scan',$fldScan) ." and scan is not NULL
") or die(mysqli_error($db));
	while ( $zoekscannr = mysqli_fetch_assoc($dublicate_scannr)) { $aantsc = $zoekscannr['scan']; }
// EINDE Zoeken naar dubbel scancode
if(isset($aantsc))	{ $fout = " Het scannr bestaat al."; }
else {					
	$update_scan = "UPDATE tblRasuser SET scan = ". db_null_input($fldScan) ." WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and rasId = '". mysqli_real_escape_string($db,$recId) ."' 	";
		mysqli_query($db,$update_scan) or die (mysqli_error($db));  
		//echo 'wijzig scan naar '.$fldScan.' bij '.$recId."<br/>";
 	} 
 }

 if($reader == 'Agrident' && $fldSort <> $dbSort) {

 	$update_sort = "UPDATE tblRasuser SET sort = ". db_null_input($fldSort) ." WHERE lidId = '". mysqli_real_escape_string($db,$lidId) ."' and rasId = '". mysqli_real_escape_string($db, $recId) ."' ";
 	mysqli_query($db,$update_sort) or die (mysqli_error($db));
 }

if($fldActief <> $dbActief) {
	$update_ras = "UPDATE tblRasuser SET actief = '". mysqli_real_escape_string($db,$fldActief) ."' WHERE rasId = '". mysqli_real_escape_string($db,$recId) ."'	";
    mysqli_query($db,$update_ras) or die (mysqli_error($db)); 
    header("Location:".$url."Ras.php");
		//echo 'wijzig Actief naar '.$fldActief.' bij '.$recId."<br/>";
 }  

}

		}
?>
					
	
