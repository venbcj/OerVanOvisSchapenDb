<!-- 3-9-2016 : sql beveiligd
20-1-2017 : Query's aangepast n.a.v. nieuwe tblDoel en hidden velden in insOverplaats.php verwijderd en codering hier aangepast 		22-1-2017 : tblBezetting gewijzigd naar tblBezet 
28-6-2017 : insert tblPeriode verwijderd Priode wordt sinds 12-2-2017 niet meer opgeslagen in tblBezet.
11-6-2020 : onderscheid gemaakt tussen reader Agrident en Biocontrol 
13-7-2020 : impVerplaatsing gewijzigd in impAgrident 
27-2-2021 : Opslaan transponder bij schaap als deze niet bestaat -->

<?php
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
//echo '$recId = '.$recId.'<br>'; 
// Einde Id ophalen

 foreach($id as $key => $value) {
 if ($key == 'chbkies' && $value == 1 ) 	{ /* Alleen als checkbox chbkies de waarde 1 heeft  /*echo $key.'='.$value.' ';*/  $box = $value ;
 foreach($id as $key => $value) {
 if ($key == 'chbDel' && $value == 0 ) 	{ /* Alleen als checkbox Del de waarde 0 heeft  /*echo $key.'='.$value.' ';*/ ;
 
  foreach($id as $key => $value) {

	if ($key == 'txtOvpldag' ) { $dag = date_create($value); $valuedatum =  date_format($dag, 'Y-m-d'); 
									/*echo $key.'='.$valuedatum.' ';*/ $fldDag = $valuedatum; }

	if ($key == 'kzlHok' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldHok = $value; }

									}




if($reader == 'Agrident') {
$zoek_transponder = mysqli_query($db, "
SELECT transponder tran, levensnummer levnr
FROM impAgrident
WHERE Id = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db)); 

    while( $zt = mysqli_fetch_assoc($zoek_transponder)) { 
      $tran_rd  = $zt['tran']; 
      $fldLevnr = $zt['levnr']; }


$zoek_transponder_schaap = mysqli_query($db, "
SELECT schaapId, transponder tran
FROM tblSchaap
WHERE levensnummer = '".mysqli_real_escape_string($db,$fldLevnr)."'
") or die (mysqli_error($db)); 

    while( $zts = mysqli_fetch_assoc($zoek_transponder_schaap)) { 
    	$schaapId = $zt['schaapId'];
    	$tran_db = $zt['tran']; }

if(isset($tran_rd) && !isset($tran_db)) {
  $update_tblSchaap = "UPDATE tblSchaap set transponder = '".mysqli_real_escape_string($db,$tran_rd)."' WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."' ";

  /*echo $update_tblSchaap.'<br>';*/  mysqli_query($db,$update_tblSchaap) or die (mysqli_error($db));
}

}




// CONTROLE op alle verplichten velden bij overplaatsen lam
if ( !empty($fldDag) && isset($fldHok))
{

if($reader == 'Agrident') {
$zoek_levensnummer_doelgroep = mysqli_query($db,"
SELECT rd.levensnummer levnr, p.doelId
FROM impAgrident rd
 left join (
	SELECT s.levensnummer, p.doelId
	FROM tblBezet b
	 left join (
		SELECT h1.hisId hisv, min(h2.hisId) hist
		FROM tblHistorie h1
		 join tblActie a1 on (a1.actId = h1.actId)
		 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
		 join tblActie a2 on (a2.actId = h2.actId)
		 join tblStal st on (h1.stalId = st.stalId)
		WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
		GROUP BY h1.hisId
	 ) tot on (b.hisId = tot.hisv)
	 join tblPeriode p on (p.periId = b.periId)
	 join tblHistorie h on (b.hisId = h.hisId)
	 join tblStal st on (h.stalId = st.stalId)
	 join tblSchaap s on (s.schaapId = st.schaapId)
	WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(tot.hist) and h.skip = 0
 ) p on (rd.levensnummer = p.levensnummer)
WHERE rd.Id = ".mysqli_real_escape_string($db,$recId)."
") or die (mysqli_error($db));
}
else {
$zoek_levensnummer_doelgroep = mysqli_query($db,"
SELECT rd.levnr_ovpl levnr, p.doelId
FROM impReader rd
 left join (
	SELECT s.levensnummer, p.doelId
	FROM tblBezet b
	 left join (
		SELECT h1.hisId hisv, min(h2.hisId) hist
		FROM tblHistorie h1
		 join tblActie a1 on (a1.actId = h1.actId)
		 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
		 join tblActie a2 on (a2.actId = h2.actId)
		 join tblStal st on (h1.stalId = st.stalId)
		WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
		GROUP BY h1.hisId
	 ) tot on (b.hisId = tot.hisv)
	 join tblPeriode p on (p.periId = b.periId)
	 join tblHistorie h on (b.hisId = h.hisId)
	 join tblStal st on (h.stalId = st.stalId)
	 join tblSchaap s on (s.schaapId = st.schaapId)
	WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(tot.hist) and h.skip = 0
 ) p on (rd.levnr_ovpl = p.levensnummer)
WHERE rd.readId = ".mysqli_real_escape_string($db,$recId)."
") or die (mysqli_error($db));
}
	while ($dl = mysqli_fetch_assoc($zoek_levensnummer_doelgroep)) { $levnr = $dl['levnr']; /*$doelId = $dl['doelId'];*/ }
//echo '$levnr = '.$levnr.'<br>';

$zoek_stalId = mysqli_query($db,"
SELECT stalId
FROM tblStal st
 join tblSchaap s on (st.schaapId = s.schaapId)
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and s.levensnummer = '".mysqli_real_escape_string($db,$levnr)."' and isnull(st.rel_best)
") or die (mysqli_error($db));
	while ($st = mysqli_fetch_assoc($zoek_stalId)) { $stalId = $st['stalId']; }
//echo '$stalId = '.$stalId.'<br>';

/*$zoek_doelgr = /* Nodig als nieuw hok nog leeg is */ /*mysqli_query($db," 
SELECT p.doelId
FROM tblPeriode p 
 join tblBezet b on (b.periId = p.periId)
 join (
	SELECT max(bezId) bezId
	FROM tblBezet b
	 join tblHistorie h on (b.hisId = h.hisId)
	 join tblStal st on (st.stalId = h.stalId)
	WHERE st.stalId = ".mysqli_real_escape_string($db,$stalId)."
 ) mb on (mb.bezId = b.bezId)
") or die (mysqli_error($db));
	while ($dl = mysqli_fetch_assoc($zoek_doelgr)) { $doelId = $dl['doelId']; }*/

	
	$insert_tblHistorie = "INSERT INTO tblHistorie set stalId = ".mysqli_real_escape_string($db,$stalId).", datum = '".mysqli_real_escape_string($db,$fldDag)."', actId = 5 ";
		mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));

$zoek_hisId = mysqli_query($db,"
SELECT max(hisId) hisId
FROM tblHistorie h 
 join tblStal st on (h.stalId = st.stalId)
WHERE st.stalId = ".mysqli_real_escape_string($db,$stalId)." and actId = 5
") or die (mysqli_error($db));
	while ($hi = mysqli_fetch_assoc($zoek_hisId)) { $hisId = $hi['hisId']; }


		
	$insert_tblBezet = "INSERT INTO tblBezet set hisId = ".mysqli_real_escape_string($db,$hisId).", hokId = ".mysqli_real_escape_string($db,$fldHok)." ";
/*echo $insert_tblBezet.'<br>';*/		mysqli_query($db,$insert_tblBezet) or die (mysqli_error($db));
unset($periId);

if($reader == 'Agrident') {		
	$updateReader = "UPDATE impAgrident SET verwerkt = 1 WHERE Id = ".mysqli_real_escape_string($db,$recId)." ";
}
else {	
	$updateReader = "UPDATE impReader SET verwerkt = 1 WHERE readId = ".mysqli_real_escape_string($db,$recId)." ";
}
		mysqli_query($db,$updateReader) or die (mysqli_error($db));	
}
// EINDE CONTROLE op alle verplichten velden bij spenen lam
		
										} // EINDE Alleen als checkbox Del de waarde 0 heeft  
    }

										} // EINDE Alleen als checkbox chbkies de waarde 1 heeft
	}

 foreach($id as $key => $value) {
 if ($key == 'chbkies' && $value == 0 ) 	{ /* Alleen als checkbox chbkies de waarde 0 heeft  /*echo $key.'='.$value.' ';*/  $box = $value ;
 foreach($id as $key => $value) {
 if ($key == 'chbDel' && $value == 1 ) 	{ /* Alleen als checkbox Del de waarde 1 heeft  /*echo $key.'='.$value.' ';*/ ;
	
if($reader == 'Agrident') {
   	$updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = ".mysqli_real_escape_string($db,$recId)." " ;
}
else {
    $updateReader = "UPDATE impReader set verwerkt = 1 WHERE readId = ".mysqli_real_escape_string($db,$recId)." " ;
}
	mysqli_query($db,$updateReader) or die (mysqli_error($db));

										} // EINDE Alleen als checkbox Del de waarde 1 heeft 
	}
										} // EINDE Alleen als checkbox chbkies de waarde 0 heeft
    }


	
	}
?>
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
	