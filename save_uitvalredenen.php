<?php
/* 15-11-2015 : gemaakt 
28-12-2016 : txtId verwijderd en $recId toegevoegd. In $update_reden veld `redId` gewijzigd in `reduId` binnen where clause
11-3-2017 :  Naast $Id ook $it (item) toegevoegd aan naam van de velden om opslaan reden en moment te kunnen splitsen. Hidden velden verwijderd in Uitval.php.
3-5-2020 : Aangepast voor Agrident reader 
1-6-2020 : veld afvoer toegevoegd
12-02-2021 : veld sterfte toegevoegd. SQL beveiligd met quotes */


/* toegepast in :
	- Uitval.php */
	
function getNaamFromKey($string) {
    $split_naam = explode('_', $string);
    return $split_naam[0];
}

function getItemFromKey($string) {
    $split_item = explode('_', $string); 
    return $split_item[1];
}

function getIdFromKey($string) {
    $split_Id = explode('_', $string); 
    return $split_Id[2];
}

foreach($_POST as $fldname => $fldvalue) {  //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    
    $multip_array[getIdFromKey($fldname)][getItemFromKey($fldname)][getNaamFromKey($fldname)] = $fldvalue;  // Opbouwen van een Multidimensional array met3 indexen.  [Id] [item] [naamveld] en een waarde nl. de veldwaarde.  
}


foreach($multip_array as $recId => $id) {  
//echo '$recId = '.$recId.'  ===>  ';

foreach($id as $item => $id) {
// Item (reden of moment)  ophalen
//echo 'Reden of moment = '.$item.'<br>'; 

foreach($id as $key => $value) {
		 if ($key == 'chbUitval') {  $fldUitv = $value; /*echo $key.'='.$value."<br/>";*/ }	
		 if ($key == 'chbPil') {  $fldPil = $value; /*echo $key.'='.$value."<br/>";*/  }
		 if ($key == 'chbAfvoer') {  $fldAfoer = $value; /*echo $key.'='.$value."<br/>";*/  }
		 if ($key == 'chbSterfte') {  $fldSterfte = $value; /*echo $key.'='.$value."<br/>";*/  }
	
	
		 if ($key == 'txtScan' && !empty($value)) {  $fldScan = "'".$value."'"; }
   		 if ($key == 'chbActief') {  $fldActief = $value; /*echo $key.'='.$value."<br/>";*/  }  	/*echo '$fldActief = '.$fldActief.'<br>'; */
}



/*** CODE M.B.T. REDEN ***/
if(isset($recId) && $recId >0 && $item == 'reden') {


 
					/*echo $fldUitv."<br/>";
					echo $fldPil."<br/>";*/
		
$zoek_in_db = "SELECT uitval, pil, afvoer, sterfte FROM tblRedenuser WHERE reduId = '".mysqli_real_escape_string($db,$recId)."' ";
/*echo $zoek_in_db."<br/>";*/ $zoek_actief = mysqli_query($db,$zoek_in_db) or die (mysqli_error($db));
while ($act = mysqli_fetch_assoc($zoek_actief)) { 
	$dbUitv = $act['uitval']; 
	$dbPil = $act['pil']; 
	$dbAfv = $act['afvoer']; 
	$dbSterf = $act['sterfte']; 
}


if($fldUitv <> $dbUitv) {					
	$update_reden = "UPDATE tblRedenuser SET uitval = $fldUitv WHERE reduId = '".mysqli_real_escape_string($db,$recId)."' 	";
/*echo $update_reden.'<br>';*/		mysqli_query($db,$update_reden) or die (mysqli_error($db));  
		
 } 

if($fldPil <> $dbPil) {
	$update_reden = "UPDATE tblRedenuser SET pil = $fldPil WHERE reduId = '".mysqli_real_escape_string($db,$recId)."' 	";
/*echo $update_reden.'<br>';*/		mysqli_query($db,$update_reden) or die (mysqli_error($db));  
		
 }

if($fldAfoer <> $dbAfv) {
	$update_reden = "UPDATE tblRedenuser SET afvoer = $fldAfoer WHERE reduId = '".mysqli_real_escape_string($db,$recId)."' 	";
/*echo $update_reden.'<br>';*/		mysqli_query($db,$update_reden) or die (mysqli_error($db));  
		
 }

if($fldSterfte <> $dbSterf) {
	$update_reden = "UPDATE tblRedenuser SET sterfte = $fldSterfte WHERE reduId = '".mysqli_real_escape_string($db,$recId)."' 	";
/*echo $update_reden.'<br>';*/		mysqli_query($db,$update_reden) or die (mysqli_error($db));  
		
 }

}
/*** EINDE   CODE M.B.T. REDEN   EINDE ***/

/*** CODE M.B.T. UITVALMOMENT ***/

if(isset($recId) && $recId >0 && $item == 'moment') {

$zoek_scan = mysqli_query($db,"
SELECT scan
FROM tblMomentuser
WHERE momuId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));
	while( $m = mysqli_fetch_assoc($zoek_scan)) { $dbScan = $m['scan']; }

unset($scan_aant);
if(isset($fldScan)) {
$zoek_dubbele_scan = mysqli_query($db,"
SELECT count(scan) aant
FROM tblMomentuser
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and scan = '".mysqli_real_escape_string($db,$fldScan)."'
") or die (mysqli_error($db));
	while( $sc = mysqli_fetch_assoc($zoek_dubbele_scan)) { $scan_aant = $sc['aant']; }
}

	if(!isset($dbScan)) { $dbScan = 'NULL'; } else {$dbScan = "'".$dbScan."'"; } /*echo '$dbScan = '.$dbScan.'<br>';*/

				
if(isset($fldScan) && $fldScan <> $dbScan && isset($scan_aant) && $scan_aant > 0) { $fout = "Deze scancode bestaat al."; }

else if(isset($fldScan) && $fldScan <> $dbScan) { // Bij Agrident raeder bestaat $fldScan niet 				
	$update_scan = "UPDATE tblMomentuser SET scan = '".mysqli_real_escape_string($db,$fldScan)."' WHERE momuId = '".mysqli_real_escape_string($db,$recId)."'	";
/*echo $update_scan.$recId."<br/>";*/		mysqli_query($db,$update_scan) or die (mysqli_error($db));  
 }

$zoek_actief = mysqli_query($db,"
SELECT actief
FROM tblMomentuser
WHERE momuId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));
	while( $ac = mysqli_fetch_assoc($zoek_actief)) { $dbActief = $ac['actief']; } /*echo '$dbActief = '.$dbActief.'<br>';*/
	
	
	
	
if($fldActief <> $dbActief) {
	$update_actief = "UPDATE tblMomentuser SET actief = '".mysqli_real_escape_string($db,$fldActief)."' WHERE momuId = '".mysqli_real_escape_string($db,$recId)."'	";
/*echo $update_actief."<br/>";*/		mysqli_query($db,$update_actief) or die (mysqli_error($db));  

 }  

}
/***  EINDE   CODE M.B.T. UITVALMOMENT   EINDE ***/



								

						
    


}						
}
?>
					
	