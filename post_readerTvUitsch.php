<!-- 09-11-2024 : Kopie gemaakt van post_readerAanv.php -->
<!-- 10-08-2024 : Opslaan ubn in tblStal toegevoegd -->

<?php

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
//echo $recId.'<br>'; 
// Einde Id ophalen
	
unset($fldUbn);
unset($fldHok);

  foreach($id as $key => $value) {

if ($key == 'chbKies')   { $fldKies = $value; }
if ($key == 'chbDel')   { $fldDel = $value; }

if ($key == 'txtAanvdm' && !empty($value)) { $dag = date_create($value); $valuedag = date_format($dag, 'Y-m-d'); 
								$fldDay = $valuedag; }

if ($key == 'kzlUbn' && !empty($value)) {  $fldUbn = $value; }

if ($key == 'kzlHok' && !empty($value)) {  $fldHok = $value; }
	 
									}

// (extra) controle of readerregel reeds is verwerkt. Voor als de pagina 2x wordt verstuurd bij fouten op de pagina
unset($verwerkt);

$zoek_readerRegel_verwerkt = mysqli_query($db,"
SELECT verwerkt
FROM impAgrident
WHERE Id = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db)); 


while($verw = mysqli_fetch_array($zoek_readerRegel_verwerkt))
{ $verwerkt = $verw['verwerkt']; }
// Einde (extra) controle of readerregel reeds is verwerkt.

if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt)) { // isset($verwerkt) is een extra controle om dubbele invoer te voorkomen

// Levensnummer ophalen
$zoek_levnr = mysqli_query($db,"
SELECT levensnummer levnr_aanv, transponder
FROM impAgrident
WHERE Id = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));

	while ( $lv = mysqli_fetch_assoc($zoek_levnr)) { $dbLevnr = $lv['levnr_aanv']; $transp_rd = $lv['transponder']; }



// CONTROLE op alle verplichten velden
if (isset($fldDay) && isset($fldUbn) && isset($dbLevnr) )
{

$zoek_schaapId = mysqli_query($db,"
SELECT schaapId, transponder
FROM tblSchaap
WHERE levensnummer = '".mysqli_real_escape_string($db,$dbLevnr)."'
") or die (mysqli_error($db));
	while ( $sId = mysqli_fetch_assoc($zoek_schaapId)) { $schaapId = $sId['schaapId']; $transp_db = $sId['transponder'];}

// Transpondernummer inlezen
if(!isset($transp_db) && isset($transp_rd)) {
	$update_tblSchaap = "UPDATE tblSchaap set transponder = '".mysqli_real_escape_string($db,$transp_rd)."' WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."' ";	
/*echo $update_tblSchaap.'<br>';*/		mysqli_query($db,$update_tblSchaap) or die (mysqli_error($db));	
}
// Einde Transpondernummer inlezen

// Zoek relId van herkomst (crediteur dus)
unset($max_his_af);
$zoek_laatste_keer_van_stallijst_af = mysqli_query($db,"
SELECT max(hisId) hisId
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
 join tblActie a on (h.actId = a.actId)
WHERE a.af = 1 and schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and lidId = '".mysqli_real_escape_string($db,$lidId)."'
");

while ($zlksa = mysqli_fetch_assoc($zoek_laatste_keer_van_stallijst_af)) { $max_his_af = $zlksa['hisId']; }


unset($stalId_uitsch);
$zoek_uitscharen = mysqli_query($db,"
SELECT h.stalId
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
WHERE h.actId = 10 and h.hisId = '".mysqli_real_escape_string($db,$max_his_af)."'
");

while ($zu = mysqli_fetch_assoc($zoek_uitscharen)) { $stalId_uitsch = $zu['stalId']; }


unset($ubn_best);
unset($partij);
$zoek_ubn_bestemming = mysqli_query($db,"
SELECT p.ubn, p.naam partij
FROM tblStal st
 join tblRelatie r on (st.rel_best = r.relId)
 join tblPartij p on (r.partId = p.partId)
WHERE stalId = '".mysqli_real_escape_string($db,$stalId_uitsch)."'
");

while ($zub = mysqli_fetch_assoc($zoek_ubn_bestemming)) { $ubn_best = $zub['ubn']; $partij = $zub['partij']; }


unset($relId_herk);
$zoek_crediteur_van_ubn = mysqli_query($db,"
SELECT relId
FROM tblRelatie r
 join tblPartij p on (r.partId = p.partId)
WHERE r.relatie = 'cred' and p.ubn = '".mysqli_real_escape_string($db,$ubn_best)."' and p.lidId = '".mysqli_real_escape_string($db,$lidId)."'
");

while ($zcu = mysqli_fetch_assoc($zoek_crediteur_van_ubn)) { $fldHerk = $zcu['relId']; }

// Einde Zoek relId van herkomst (crediteur dus)


// Insert tblStal
	$insert_tblStal= "INSERT INTO tblStal set lidId = '".mysqli_real_escape_string($db,$lidId)."', ubnId = '".mysqli_real_escape_string($db,$fldUbn)."', schaapId = '".mysqli_real_escape_string($db,$schaapId)."', rel_herk = " . db_null_input($fldHerk) . " ";

/*echo $insert_tblStal.'<br>';*/		mysqli_query($db,$insert_tblStal) or die (mysqli_error($db));
// Einde Insert tblStal

// Insert tblHistorie
$stalId = zoek_max_stalId($lidId,$schaapId);


  // Insert aanvoer	
	$insert_tblHistorie_aank = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$fldDay)."', actId = 11 ";
/*echo $insert_tblHistorie_aank.'<br>';*/		mysqli_query($db,$insert_tblHistorie_aank) or die (mysqli_error($db));

if(isset($fldHok)) { 

$hisId_aanv = zoek_hisId_stal($stalId,11);

	$insert_tblBezet = "INSERT INTO tblBezet set hisId = '".mysqli_real_escape_string($db,$hisId_aanv)."', hokId = '".mysqli_real_escape_string($db,$fldHok)."' ";
/*echo $insert_tblBezet.'<br>';*/		mysqli_query($db,$insert_tblBezet) or die (mysqli_error($db));
	}
  // Einde Insert aanvoer

// Einde Insert tblHistorie

	$updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' ";

/*echo $updateReader.'<br>';*/		mysqli_query($db,$updateReader) or die (mysqli_error($db));

if ($modmeld == 1 ) {	// Insert tblMeldingen
$hisId = zoek_hisId_stal($stalId,11);
$Melding = 'AAN';
include "maak_request.php";
	// Einde Insert tblMeldingen	
}		
unset($schaapId); }
// EINDE CONTROLE op alle verplichten velden


	
	} // Einde if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt))					

	
  if($fldKies == 0 && $fldDel == 1) {	
	
    $updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' " ;

/*echo $updateReader.'<br>';*/		mysqli_query($db,$updateReader) or die (mysqli_error($db));
	}





unset($dbLevnr);
	} // Einde foreach($array as $recId => $id)
?>
					
	