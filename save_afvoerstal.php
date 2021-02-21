<?php
/* 18-9-2016 gemaakt */

include "url.php";

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
   
// Id ophalen
#echo $recId.'<br>'; 
// Einde Id ophalen


 foreach($id as $key => $value) {
 if ($key == 'chbKies' && $value == 1 ) 	{ /* Alleen als checkbox chbKies de waarde 1 heeft  /*echo $key.'='.$value.' ';*/  $box = $value ;
 
  foreach($id as $key => $value) {
	if ($key == 'txtDatum' && !empty($value)) { $dag = date_create($value); $fldDag =  date_format($dag, 'Y-m-d'); }
	
	if ($key == 'kzlBest' && !empty($value)) { $fldBest = $value; /*echo 'kzlBestemm : '.$value.'<br>';*/ }
	
	if ($key == 'chbDood' ) { $fldDood = $value; /*echo 'Chekbox : '.$value.'<br>';*/ }
		
									}
if($recId > 0) {							
$zoek_maxDatum = mysqli_query($db,"
select datum date, date_format(datum,'%d-%m-%Y') datum
from tblHistorie h
 join (
	select max(hisId) hisId
	from tblHistorie
	where stalId = ".mysqli_real_escape_string($db,$recId)."
 ) mh on (h.hisId = mh.hisId)
") or die(mysqli_error($db));

while ($maxD = mysqli_fetch_assoc($zoek_maxDatum)) { $maxdm = $maxD['datum']; $dmmax = $maxD['date']; }
}
/*echo "txtDatum = ".$fldDag.'<br>' ; 
echo "cntrDatum = ".$maxdm.'<br>' ; 
echo "cntrDay = ".$dmmax.'<br>' ; */

// CONTROLE op alle verplichten velden bij afvoer stal
if ( isset($fldDag) && $dmmax <= $fldDag && ( (isset($fldBest) && !isset($fldDood)) || (!isset($fldBest) && isset($fldDood)) ) )
{
if(isset($fldBest)) { $actId = 12; $meldafvoer = 'AFV'; }
if(isset($fldDood)) { $actId = 14; $meldafvoer = 'DOO'; $fldBest = $rendac_Id; }

	$insert_tblHistorie = "INSERT INTO tblHistorie set stalId = ".mysqli_real_escape_string($db,$recId).", datum = '".mysqli_real_escape_string($db,$fldDag)."', actId = ".mysqli_real_escape_string($db,$actId)." ";
	/*echo $insert_tblHistorie.'<br>';*/	mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));

if ($modmeld == 1 ) {
$Melding = $meldafvoer;
$afvoerd = $fldDag;

$zoek_hisId = mysqli_query($db,"
select max(hisId) hisId
from tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and h.actId = ".mysqli_real_escape_string($db,$actId)."
") or die(mysqli_error($db));

while ($per = mysqli_fetch_assoc($zoek_hisId)) { $hisId = $per['hisId']; }

include "maak_request.php";

}
	$update_tblStal = "UPDATE tblStal set rel_best = ".mysqli_real_escape_string($db,$fldBest)." WHERE stalId = ".mysqli_real_escape_string($db,$recId)." ";	
/*echo $update_tblStal.'<br>';*/		mysqli_query($db,$update_tblStal) or die (mysqli_error($db));



}
// EINDE CONTROLE op alle verplichten velden bij afvoer stal
// unset($fldDag); Deze unset staat hier te vroeg. Nl. nog nodig bij foutmelding in Afvoerstal.php
unset($fldBest);
unset($fldDood);


										} // EINDE Alleen als checkbox chbKies de waarde 1 heeft
    }
	
	
	}

?>
					
	