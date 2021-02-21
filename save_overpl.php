<?php
/* 22-11-2015 gemaakt 
20-1-2017 : Query aangepast n.a.v. nieuwe tblDoel		22-1-2017 : tblBezetting gewijzigd naar tblBezet
11-2-2017 : insert tblPeriode verwijderd */

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
foreach($array as $recId => $id) { //recId is hier schaapId
   
 foreach($id as $key => $value) {
 if ($key == 'chbkies' && $value == 1 ) 	{ /* Alleen als checkbox chbkies de waarde 1 heeft  /*echo $key.'='.$value.' ';*/  $box = $value ;

	
  foreach($id as $key => $value) {
	if ($key == 'txtDatum' ) { $dag = date_create($value); $updDag =  date_format($dag, 'Y-m-d');  }
	
	if ($key == 'kzlHok' && !empty($value)) {  $kzlHok = $value; }
		
									}
$zoek_mindag = mysqli_query($db,"
select hm.datum
from tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join (
	select max(hisId) hisId, stalId
	from tblHistorie
	group by stalId
 ) hmax on (hmax.stalId = st.stalId)
 join tblHistorie hm on (hm.hisId = hmax.hisId)
where s.schaapId = ".mysqli_real_escape_string($db,$recId)."
 ") or die(mysqli_error($db));
	
	while($row = mysqli_fetch_assoc($zoek_mindag)) {	$dmmin = $row['datum']; }


// CONTROLE op alle verplichten velden bij overplaatsen schaap
if (!empty($updDag) && $updDag >= $dmmin && !empty($kzlHok))
{
$zoek_stalId = mysqli_query($db,"
select stalId
from tblStal st
 join tblSchaap s on (st.schaapId = s.schaapId)
where isnull(st.rel_best) and s.schaapId = ".mysqli_real_escape_string($db,$recId)." and st.lidId = ".mysqli_real_escape_string($db,$lidId)."
") or die(mysqli_error($db));

	while ($st = mysqli_fetch_assoc($zoek_stalId)) { $stalId = $st['stalId']; } 

$insert_tblHistorie = "
insert into tblHistorie 
set stalId = ".mysqli_real_escape_string($db,$stalId).", datum = '".mysqli_real_escape_string($db,$updDag)."', actId = 5
";
	mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));

$zoek_hisId = mysqli_query($db,"
select max(hisId) hisId
from tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and h.actId = 5
") or die(mysqli_error($db));

	while ($hi = mysqli_fetch_assoc($zoek_hisId)) { $hisId = $hi['hisId']; }


if(!isset($newHok) || $kzlHok <> $newHok) { // Als het gekozen verblijf is ongelijk aan verblijf van de vorige regel (record)

$newHok = $kzlHok; /*unset($ovp_periId); // Periode van voorgaande overplaats-record mag niet meer bestaan.

$zoek_periId = mysqli_query($db,"
select periId
from tblPeriode
where isnull(dmafsluit) and hokId = ".mysqli_real_escape_string($db,$newHok)."
") or die(mysqli_error($db));

	while ($pe = mysqli_fetch_assoc($zoek_periId)) { $ovp_periId = $pe['periId']; }
	
if(!isset($ovp_periId)) {

$insert_tblPeriode = "
insert into tblPeriode
set hokId = ".mysqli_real_escape_string($db,$newHok).", doelId = '".mysqli_real_escape_string($db,$doelId)."'
";
	mysqli_query($db,$insert_tblPeriode) or die (mysqli_error($db));

$zoek_periId = mysqli_query($db,"
select periId
from tblPeriode
where isnull(dmafsluit) and hokId = ".mysqli_real_escape_string($db,$newHok)."
") or die(mysqli_error($db));

	while ($pe = mysqli_fetch_assoc($zoek_periId)) { $ovp_periId = $pe['periId']; }
}*/
}

$insert_tblBezet = "
insert into tblBezet
set hisId = ".mysqli_real_escape_string($db,$hisId).", hokId = ".mysqli_real_escape_string($db,$newHok)."
";
	mysqli_query($db,$insert_tblBezet) or die (mysqli_error($db));

	

}
// EINDE CONTROLE op alle verplichten velden bij spenen lam





										} // EINDE Alleen als checkbox chbkies de waarde 1 heeft
    }


	
	
	
	}

?>
					
	