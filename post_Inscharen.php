<!-- 09-10-2026 : Kopie gemaakt van post_readerTvUitsch.php -->

<?php

$array = array();
foreach ($_POST as $key => $value) {    
    $array[Url::getIdFromKey($key)][Url::getNameFromKey($key)] = $value;
}
foreach($array as $schaapId => $id) {
if (!$schaapId) {
        continue;
    }
// Id ophalen
//echo $schaapId.'<br>'; 
// Einde Id ophalen
		unset($fldUbn);
		unset($fldHok);
	  foreach ($id as $key => $value) {
				if ($key == 'chbKies') {
             $fldKies = $value;
        }
				if ($key == 'txtAanvdm' && !empty($value)) { 
						$dag = date_create($value); 
						$valuedag = date_format($dag, 'Y-m-d'); 
																$fldDay = $valuedag;
				}
				if ($key == 'kzlUbn' && !empty($value)) { 
						$fldUbn = $value;
				}
				if ($key == 'kzlHok' && !empty($value)) {
						$fldHok = $value;
				}
	 
		}

if(!isset($fldUbn))
{
	$zoek_ubn = mysqli_query($db,"
	SELECT ubnId
	FROM tblUbn
	WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and lidubn = 1 and actief = 1
	") or die (mysqli_error($db));

	while ($zu = mysqli_fetch_assoc($zoek_ubn)) 
 { 
   $fldUbn = $zu['ubnId'];
 }
}

if ($fldKies == 1) {

// Levensnummer ophalen
$zoek_levnr = mysqli_query($db,"
SELECT levensnummer levnr_aanv
FROM tblSchaap
WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
") or die (mysqli_error($db));

	while ( $lv = mysqli_fetch_assoc($zoek_levnr)) { $levnr = $lv['levnr_aanv']; }



// CONTROLE op alle verplichten velden
if (isset($fldDay) && isset($fldUbn) && isset($levnr) )
{

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

/*echo '$insert_tblStal = '.$insert_tblStal.'<br>';*/	mysqli_query($db,$insert_tblStal) or die (mysqli_error($db));
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

if ($modmeld == 1 ) {	// Insert tblMeldingen
$hisId = zoek_hisId_stal($stalId,11);
$Melding = 'AAN';
include "maak_request.php";
	// Einde Insert tblMeldingen	
}
unset($schaapId); }
// EINDE CONTROLE op alle verplichten velden

	} // Einde if ($fldKies == 1)

unset($levnr);
	} // Einde foreach($array as $schaapId => $id)
?>