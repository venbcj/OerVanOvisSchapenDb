<?php
/* 22-11-2015 gemaakt 
20-1-2017 : query aangepast n.a.v. nieuwe tblDoel. Speengewicht niet verplicht gemaakt	22-1-2017 tblBezetting gewijzigd naar tblBezet 
13-2-2017 : tblPeriode verwijderd en verblijf opgeslagen in tblBezet
13-4-2019 : Volwassendieren kunnen ook uit verblijf worden gehaald door overplaasten of verlaten 
26-4-2020 : $minDag als extra ontrole weggehaald. Controle zit ook al in HokSpenen.php*/

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
// recId ophalen
//echo '$recId = '.$recId.'<br>';
// Einde recId ophalen
   
 foreach($id as $key => $value) {
 if ($key == 'chbkies' && $value == 1 ) 	{ /* Alleen als checkbox chbkies de waarde 1 heeft  /*echo $key.'='.$value.' ';*/  $box = $value ;

	
  foreach($id as $key => $value) {
	if ($key == 'txtDatum' ) { $dag = date_create($value); $updDag =  date_format($dag, 'Y-m-d');  }
	
	if ($key == 'txtKg' && !empty($value)) { $updKg = $value; } else if ($key == 'txtKg' && empty($value)) { $updKg = 'NULL'; }

	unset($kzlHok);
	if ($key == 'kzlHok' && !empty($value)) { $kzlHok = $value; }
		
									}

$zoek_generatie = mysqli_query($db,"
select hisId
from tblStal st
 join tblHistorie h on (st.stalId = h.stalId)
where st.schaapId = ".mysqli_real_escape_string($db,$recId)." and h.actId = 3
") or die(mysqli_error($db));

	while ($ge = mysqli_fetch_assoc($zoek_generatie)) { $aanw = $ge['hisId']; }

	if(isset($aanw)) { $gener = 'ouder'; } else { $gener = 'lam'; }

// CONTROLE op alle verplichten velden bij spenen lam
if (isset($recId) && $recId >0 && !empty($updDag))
{ 
/*
echo "Datum = ".$updDag.'<br>' ; 
echo "Kg = ".$updKg.'<br>' ; 
echo "hokId = ".$newHok.'<br><br>' ; */

$zoek_stalId = mysqli_query($db,"
select stalId
from tblStal st
where isnull(st.rel_best) and st.schaapId = ".mysqli_real_escape_string($db,$recId)." and st.lidId = ".mysqli_real_escape_string($db,$lidId)."
") or die(mysqli_error($db));

	while ($st = mysqli_fetch_assoc($zoek_stalId)) { $stalId = $st['stalId']; }
//echo '$stalId = '.$stalId.'<br>';

if(isset($kzlHok)) { if($gener == 'lam') { $actId = 4; } else { $actId = 5; } } else { $actId = 7; }

$insert_tblHistorie = "
INSERT INTO tblHistorie
set stalId = ".mysqli_real_escape_string($db,$stalId).", datum = '".mysqli_real_escape_string($db,$updDag)."', kg = ".mysqli_real_escape_string($db,$updKg).", actId = ".mysqli_real_escape_string($db,$actId)."
";
/*echo $insert_tblHistorie.'<br>';*/	mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));

if(isset($kzlHok)) { // Als moet worden overgplaatst en dus niet volwassen dieren die verblijf alleen verlaten

$zoek_hisId = mysqli_query($db,"
select max(hisId) hisId
from tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and (h.actId = 4 or h.actId = 5)
") or die(mysqli_error($db));

	while ($hi = mysqli_fetch_assoc($zoek_hisId)) { $hisId = $hi['hisId']; }

if(!isset($newHok) || (isset($newHok) && $kzlHok <> $newHok)) {
$newHok = $kzlHok;
/*
$zoek_periId = mysqli_query($db,"
select periId
from tblPeriode p
 join tblHok h on (p.hokId = h.hokId)
where isnull(p.dmafsluit) and p.hokId = ".mysqli_real_escape_string($db,$newHok)."
") or die(mysqli_error($db));

while ($per = mysqli_fetch_assoc($zoek_periId)) { $spn_periId = $per['periId']; }

if(!isset($spn_periId)) {
	$insert_tblPeriode = "INSERT INTO tblPeriode set hokId = ".mysqli_real_escape_string($db,$newHok).", doelId = 2 "; 
		mysqli_query($db,$insert_tblPeriode) or die (mysqli_error($db)); 
		
$zoek_periId = mysqli_query($db,"
select periId
from tblPeriode p
 join tblHok h on (p.hokId = h.hokId)
where isnull(p.dmafsluit) and p.hokId = ".mysqli_real_escape_string($db,$newHok)."
") or die(mysqli_error($db));

while ($per = mysqli_fetch_assoc($zoek_periId)) { $spn_periId = $per['periId']; }		
		
}*/
}
if(isset($hisId)) { // $hisId bestaat niet bij verlaten volwassen dieren

$insert_tblBezet = "INSERT INTO tblBezet set hisId = ".mysqli_real_escape_string($db,$hisId).", hokId = ".mysqli_real_escape_string($db,$newHok)." "; 
/*echo $insert_tblBezet.'<br>';*/		mysqli_query($db,$insert_tblBezet) or die (mysqli_error($db)); 
				}

} // Einde if(isset($kzlHok))

}
// EINDE CONTROLE op alle verplichten velden bij spenen lam





										} // EINDE Alleen als checkbox chbkies de waarde 1 heeft
    }


	
	
	
	}

?>
					
	