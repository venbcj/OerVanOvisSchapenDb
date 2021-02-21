<?php
/* 17-05-2019 gemaakt */

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
   
 foreach($id as $key => $value) {
 if ($key == 'chbkies' && $value == 1 ) 	{ /* Alleen als checkbox chbkies de waarde 1 heeft  /*echo $key.'='.$value.' ';*/  $box = $value ;

	
  foreach($id as $key => $value) {
	if ($key == 'txtDatum' ) { $dag = date_create($value); $updDag =  date_format($dag, 'Y-m-d');  }
	
	if ($key == 'txtKg' && !empty($value)) { $updKg = str_replace(',', '.', $value); } else if ($key == 'txtKg' && empty($value)) { $updKg = 'NULL'; }
		
									}

// CONTROLE op alle verplichten velden bij aanwas lam
if ( !empty($updDag))
{
/*
echo "Datum = ".$updDag.'<br>' ; 
echo "Kg = ".$updKg.'<br>' ; */

$zoek_stalId = mysqli_query($db,"
select stalId
from tblStal st
where isnull(st.rel_best) and st.schaapId = ".mysqli_real_escape_string($db,$recId)." and st.lidId = ".mysqli_real_escape_string($db,$lidId)."
") or die(mysqli_error($db));

while ($st = mysqli_fetch_assoc($zoek_stalId)) { $stalId = $st['stalId']; }

	$insert_tblHistorie = "INSERT INTO tblHistorie set stalId = ".mysqli_real_escape_string($db,$stalId).", datum = '".mysqli_real_escape_string($db,$updDag)."', kg = ".mysqli_real_escape_string($db,$updKg).", actId = 3 ";
/*echo $insert_tblHistorie.'<br>';*/	mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));


$zoek_historie = mysqli_query($db,"
select hisId 
from tblHistorie
where stalId = ".mysqli_real_escape_string($db,$stalId)." and actId = 3 
") or die(mysqli_error($db));

while ($hi = mysqli_fetch_assoc($zoek_historie)) { $hisId = $hi['hisId']; }

	$insert_tblBezet = "INSERT INTO tblBezet set hisId = ".mysqli_real_escape_string($db,$hisId).", hokId = ".mysqli_real_escape_string($db,$ID)." "; // $ID zit in SESSION zie HokAanwas.php

/*echo $insert_tblBezet.'<br>';*/	mysqli_query($db,$insert_tblBezet) or die (mysqli_error($db));

}
// EINDE CONTROLE op alle verplichten velden bij aanwas lam





										} // EINDE Alleen als checkbox chbkies de waarde 1 heeft
    }


	
	
	
	}

?>
					
	