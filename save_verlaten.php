<?php
/* 22-11-2015 gemaakt 
15-2-2017 : gewicht niet verplicht gemaakt en (extra) controle op 'maximale datum uit historie' verwijderd 
6-6-2018 : kg met komma wordt omgezet naar kg met punt 
30-12-2023 : sql beveiligd met quotes */

include "url.php";



$array = array();

foreach($_POST as $key => $value) {
    
    $array[Url::getIdFromKey($key)][Url::getNameFromKey($key)] = $value;
}
foreach($array as $recId => $id) {
   
 foreach($id as $key => $value) {
 if ($key == 'chbkies' && $value == 1 ) 	{ /* Alleen als checkbox chbkies de waarde 1 heeft  /*echo $key.'='.$value.' ';*/  $box = $value ;

	
  foreach($id as $key => $value) {
	if ($key == 'txtDatum' ) { $dag = date_create($value); $updDag =  date_format($dag, 'Y-m-d');  }
		
									}

// CONTROLE op alle verplichten velden bij spenen lam
if (!empty($updDag))
{
/*
echo "Datum = ".$updDag.'<br>' ; 
echo "Kg = ".$updKg.'<br>' ; 
echo "relatId = ".$updRelId.'<br><br>' ; */

$zoek_stalId = mysqli_query($db,"
SELECT stalId
FROM tblStal st
WHERE isnull(st.rel_best) and st.schaapId = '".mysqli_real_escape_string($db,$recId)."' and st.lidId = ".mysqli_real_escape_string($db,$lidId)."
") or die(mysqli_error($db));

while ($st = mysqli_fetch_assoc($zoek_stalId)) { $stalId = $st['stalId']; }

	$insert_tblHistorie = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$updDag)."', actId = 7";
		mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));

}
// EINDE CONTROLE op alle verplichten velden bij spenen lam





										} // EINDE Alleen als checkbox chbkies de waarde 1 heeft
    }


	
	
	
	}

?>
					
	