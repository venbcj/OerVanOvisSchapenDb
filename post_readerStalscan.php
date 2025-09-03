<!-- 06-08-2023 : Gekopieerd van post_readerAanv.php  Er zijn met 5 seneario's rekening gehouden.
1. Een schaap wordt gevonden in de stallijst met verblijf tijdens een controle wat een gewenste situatie is
2. Een schaap wordt gevonden in de stallijst zonder verblijf tijdens een controle
3. Een schaap komt nog niet voor in de database
4. Een schaap staat niet op de stallijst maar bestaat wel in de database met geboortedatum
5. Een schaap staat niet op de stallijst maar bestaat wel in de database zonder geboortedatum

15-07-2025 : Opslaan van ubn in tblStal toegevoegd

-->

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

unset($schaapId_db);
unset($fldUbn);
unset($Levnr_rd);
unset($fldGebday);
unset($fldSekse);
unset($fldFase);
unset($fldRas);
unset($fldHok);
unset($fldActie);

if(!isset($actId))
{
	$zoek_actId = mysqli_query($db,"
	SELECT actId
	FROM impAgrident
	WHERE Id = '" . mysqli_real_escape_string($db,$recId) . "'
	") or die (mysqli_error($db));

	while ($za = mysqli_fetch_array($zoek_actId))
		{ 
			$actId = $za['actId'];
		}
}	

  foreach($id as $key => $value) {

if ($key == 'chbkies')   { $fldKies = $value; }
if ($key == 'chbDel')   { $fldDel = $value; }

if ($key == 'txtScandm' && !empty($value)) { $dag = date_create($value); $valuedag = date_format($dag, 'Y-m-d'); 
								$fldDay = $valuedag; }

if ($key == 'kzlUbn' && !empty($value)) { $fldUbn = $value; }

if ($key == 'txtGebdm' && !empty($value)) { $dag = date_create($value); $valuedag = date_format($dag, 'Y-m-d'); 
								$fldGebday = $valuedag; }

if ($key == 'kzlSekse' && !empty($value)) {  $fldSekse = $value; }
// else if ($key == 'kzlSekse' && empty($value)) {  $fldSekse = 'NULL' ; }

if ($key == 'kzlFase' && !empty($value)) {  $fldFase = $value; }
// else if ($key == 'kzlFase' && empty($value)) {  $fldFase = 'NULL' ; }

if ($key == 'kzlRas' && !empty($value)) {  $fldRas = $value; }
 //else if ($key == 'kzlRas' && empty($value)) {  $fldRas = '' ; }

if ($key == 'kzlHok' && !empty($value)) {  $fldHok = $value; } 

if ($key == 'kzlActie' && !empty($value))   { $fldActie = $value; }

if ($key == 'chbRvo')   { $fldRvo = $value; }

	 	if($fldFase == 'moeder' && !isset($fldSekse)) { $fldSekse = 'ooi'; }
 	else if($fldFase == 'vader' && !isset($fldSekse)) { $fldSekse = 'ram'; }

	 
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

// Levensnummer ophalen uit reader
$zoek_levnr_reader = mysqli_query($db,"
SELECT levensnummer levnr, transponder
FROM impAgrident
WHERE Id = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));

	while ( $zlr = mysqli_fetch_assoc($zoek_levnr_reader)) { $Levnr_rd = $zlr['levnr']; $transp_rd = $zlr['transponder']; }

// SchaapId ophalen uit database (t.b.v. inlezen stallijst nieuwe klanten)

$schaapId_db 	 = zoek_schaapId_in_database($Levnr_rd);
$schaapId_stal = zoek_schaapId_in_stallijst($lidId,$Levnr_rd);
$transp_db 	   = zoek_transponder_in_database($Levnr_rd);

//***************************************************************************************************

// INLEZEN CONTROLE met schapen op eigen stallijst
if ($actId == 22 && isset($schaapId_stal)) {

// Transpondernummer inlezen
if(!isset($transp_db) && isset($transp_rd)) {
	$update_tblSchaap = "UPDATE tblSchaap set transponder = '".mysqli_real_escape_string($db,$transp_rd)."' WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId_stal)."' ";	
/*echo $update_tblSchaap.'<br>';*/		mysqli_query($db,$update_tblSchaap) or die (mysqli_error($db));	
}
// Einde Transpondernummer inlezen

$stalId = zoek_stalId_in_stallijst($lidId,$schaapId_stal);

// Insert historie stallijstscan
	$insert_tblHistorie_scan = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$fldDay)."', actId = '".mysqli_real_escape_string($db,$actId)."' ";
/*echo $insert_tblHistorie_scan.'<br>';*/		mysqli_query($db,$insert_tblHistorie_scan) or die (mysqli_error($db));
// Einde Insert historie stallijstscan

if(isset($fldHok)) { 

$hisId_scan = zoek_hisId_stal($stalId,$actId);

	$insert_tblBezet = "INSERT INTO tblBezet set hisId = '".mysqli_real_escape_string($db,$hisId_scan)."', hokId = '".mysqli_real_escape_string($db,$fldHok)."' ";
/*echo $insert_tblBezet.'<br>';*/		mysqli_query($db,$insert_tblBezet) or die (mysqli_error($db));
	}

} // Einde if ($actId == 22 && isset($schaapId_stal))

// EINDE INLEZEN CONTROLE met schapen op eigen stallijst

//***************************************************************************************************

// INLEZEN NIET BESTAANDE DIEREN zowel bij controle (actId == 22) als nieuwe klant ($fldFase en $fldSekse bestaat alleen als het geen bestaand dier is !!)
// CONTROLE op alle verplichten velden
else if (!isset($schaapId_db) && isset($fldDay) && isset($fldUbn) && isset($Levnr_rd) && (($fldFase == 'moeder' && $fldSekse == 'ooi') || ($fldFase == 'vader' && $fldSekse == 'ram') || ($fldFase == 'lam' && isset($fldSekse) )) )
{


// Insert tblSchapen
	$insert_tblSchaap = "INSERT INTO tblSchaap set levensnummer = '".mysqli_real_escape_string($db,$Levnr_rd)."', rasId = " . db_null_input($fldRas) . ", geslacht = '".mysqli_real_escape_string($db,$fldSekse)."' ";	
/*echo $insert_tblSchaap.'<br>';*/		mysqli_query($db,$insert_tblSchaap) or die (mysqli_error($db));	
// Einde Insert tblSchapen

$schaapId_db = zoek_schaapId_in_database($Levnr_rd);

// Transpondernummer inlezen
if(isset($transp_rd)) {
	$update_tblSchaap = "UPDATE tblSchaap set transponder = '".mysqli_real_escape_string($db,$transp_rd)."' WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId_db)."' ";	
/*echo $update_tblSchaap.'<br>';*/		mysqli_query($db,$update_tblSchaap) or die (mysqli_error($db));	
}
// Einde Transpondernummer inlezen

// Insert tblStal
	$insert_tblStal = "INSERT INTO tblStal set lidId = '".mysqli_real_escape_string($db,$lidId)."', ubnId = '".mysqli_real_escape_string($db,$fldUbn)."', schaapId = '".mysqli_real_escape_string($db,$schaapId_db)."' ";

/*echo $insert_tblStal.'<br>';*/		mysqli_query($db,$insert_tblStal) or die (mysqli_error($db));
// Einde Insert tblStal


$stalId = zoek_stalId_in_stallijst($lidId,$schaapId_db);

  // Insert historie stallijstscan
	$insert_tblHistorie_scan = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$fldDay)."', actId = '".mysqli_real_escape_string($db,$actId)."' ";
/*echo $insert_tblHistorie_scan.'<br>';*/		mysqli_query($db,$insert_tblHistorie_scan) or die (mysqli_error($db));
  // Einde Insert historie stallijstscan

// Inlezen geboortedatum
if(isset($fldGebday)) {

	$insert_tblHistorie_geboren = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$fldGebday)."', actId = 1 ";
/*echo $insert_tblHistorie_geboren.'<br>';*/		mysqli_query($db,$insert_tblHistorie_geboren) or die (mysqli_error($db));
}
// Einde Inlezen geboortedatum

// Aanwas inlezen
if($fldFase == 'moeder' || $fldFase == 'vader') {

	$insert_tblHistorie = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$fldDay)."', actId = 3 ";
/*echo $insert_tblHistorie.'<br>';*/		mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));
}
// Einde Aanwas inlezen


if($fldActie == 2 || $fldActie == 11) { // $fldActie bestaat alleen bij controle scan niet bij nieuwe klanten

	$insert_tblHistorie_aanvoer = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$fldDay)."', actId = '".mysqli_real_escape_string($db,$fldActie)."' ";
/*echo $insert_tblHistorie_aanvoer.'<br>';*/		mysqli_query($db,$insert_tblHistorie_aanvoer) or die (mysqli_error($db));
 }


if(isset($fldHok)) { 

$hisId_scan = zoek_hisId_stal($stalId,$actId);

	$insert_tblBezet = "INSERT INTO tblBezet set hisId = '".mysqli_real_escape_string($db,$hisId_scan)."', hokId = '".mysqli_real_escape_string($db,$fldHok)."' ";
/*echo $insert_tblBezet.'<br>';*/		mysqli_query($db,$insert_tblBezet) or die (mysqli_error($db));
	}


if ($modmeld == 1 && $fldRvo == 1 && isset($fldActie)) {

if($fldActie == 1 ) { $Melding = 'GER'; }
if($fldActie == 2 || $fldActie == 11) { $Melding = 'AAN'; }

$hisId = zoek_hisId_stal($stalId,$fldActie);

include "maak_request.php";

}


 }

// EINDE CONTROLE op alle verplichten velden
// EINDE INLEZEN NIET BESTAANDE DIEREN zowel bij controle (actId == 22) als nieuwe klant

//***************************************************************************************************

// INLEZEN BESTAANDE DIEREN zowel controle (actId = 22) als nieuwe klant
// CONTROLE op alle verplichten velden
else if ( 
	/*($actId == 21 && isset($fldDay) && isset($Levnr_rd) && isset($schaapId_db) ) ||
	($actId == 22 && isset($fldDay) && isset($Levnr_rd) && !isset($schaapId_stal) && isset($schaapId_db))*/
	isset($fldDay) && isset($Levnr_rd) && !isset($schaapId_stal) && isset($schaapId_db)
)
{
// Transpondernummer inlezen
if(!isset($transp_db) && isset($transp_rd)) {
	$update_tblSchaap = "UPDATE tblSchaap set transponder = '".mysqli_real_escape_string($db,$transp_rd)."' WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId_db)."' ";	
/*echo $update_tblSchaap.'<br>';*/		mysqli_query($db,$update_tblSchaap) or die (mysqli_error($db));	
}
// Einde Transpondernummer inlezen

// Insert tblStal
	$insert_tblStal= "INSERT INTO tblStal set lidId = '".mysqli_real_escape_string($db,$lidId)."', schaapId = '".mysqli_real_escape_string($db,$schaapId_db)."' ";

/*echo $insert_tblStal.'<br>';*/		mysqli_query($db,$insert_tblStal) or die (mysqli_error($db));
// Einde Insert tblStal


$stalId = zoek_stalId_in_stallijst($lidId,$schaapId_db);

  // Insert historie stallijstscan
	$insert_tblHistorie_scan = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$fldDay)."', actId = '".mysqli_real_escape_string($db,$actId)."' ";
/*echo $insert_tblHistorie_scan.'<br>';*/		mysqli_query($db,$insert_tblHistorie_scan) or die (mysqli_error($db));


// Inlezen geboortedatum
if(isset($fldGebday)) {

	$insert_tblHistorie_geboren = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$fldGebday)."', actId = 1 ";
/*echo $insert_tblHistorie_geboren.'<br>';*/		mysqli_query($db,$insert_tblHistorie_geboren) or die (mysqli_error($db));
}
// Einde Inlezen geboortedatum


if($fldActie == 2 || $fldActie == 11) { // alleen bij controle scan niet bij nieuwe klanten

	$insert_tblHistorie_aanvoer = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$fldDay)."', actId = '".mysqli_real_escape_string($db,$fldActie)."' ";
/*echo $insert_tblHistorie_aanvoer.'<br>';*/		mysqli_query($db,$insert_tblHistorie_aanvoer) or die (mysqli_error($db));
 }


if(isset($fldHok)) { 

$hisId_scan = zoek_hisId_stal($stalId,$fldActie);

	$insert_tblBezet = "INSERT INTO tblBezet set hisId = '".mysqli_real_escape_string($db,$hisId_scan)."', hokId = '".mysqli_real_escape_string($db,$fldHok)."' ";
/*echo $insert_tblBezet.'<br>';*/		mysqli_query($db,$insert_tblBezet) or die (mysqli_error($db));
	}
  // Einde Insert historie stallijstscan



if ($modmeld == 1 && $fldRvo == 1 && isset($fldActie)) {

if($fldActie == 1 ) { $Melding = 'GER'; 

$hisId = zoek_hisId_schaap($schaapId_db,$fldActie);
}

if($fldActie == 2 || $fldActie == 11) { $Melding = 'AAN'; 

$hisId = zoek_hisId_stal($stalId,$fldActie);
}


include "maak_request.php";

}


}
// Einde CONTROLE op alle verplichten velden
// EINDE INLEZEN BESTAANDE DIEREN zowel controle (actId = 22) als nieuwe klant

//***************************************************************************************************

	$updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' ";

/*echo $updateReader.'<br>';*/		mysqli_query($db,$updateReader) or die (mysqli_error($db));








	} // Einde if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt))					


  if($fldKies == 0 && $fldDel == 1) {	
	
    $updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' " ;

/*echo $updateReader.'<br>';*/		mysqli_query($db,$updateReader) or die (mysqli_error($db));
	}






	} // Einde foreach($array as $recId => $id)
?>
					
	