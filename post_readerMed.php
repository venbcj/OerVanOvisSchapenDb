<!--  18-12-2014 : Gemaakt 
 12-03-2017 : Aangpast na.v. Release 2 of wel nieuwe databasestructuur, verwijderen mogelijk gemaakt en mysql verandert in mysqli 
Toegepast in : InsMedicijn.php 
15-11-2020 : onderscheid gemaakt tussen reader Agrident en Biocontrol 
24-1-2021 : Sql beveiligd met quotes. Transponder nummer opslaan in tblSchaap als deze nog niet bestaat Verschil tussen kiezen of verwijderen herschreven 31-1 $schaapId gewijzigd naar $schaapId_db -->

<?php
// include "url.php"; Zit al in InsMedicijn.php

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

	//echo '<br>'.'$recId = '.$recId;
	
  foreach($id as $key => $value) {

  	if ($key == 'chbkies') 	{ $fldKies = $value; }
  	if ($key == 'chbDel') 	{ $fldDel = $value; }

	if ($key == 'txtDatum' && !empty($value)) { $dag = date_create($value); $valuedatum =  date_format($dag, 'Y-m-d'); 
									/*echo $key.'='.$valuedatum.' ';*/ $flddag = $valuedatum; }
	
	if ($key == 'kzlPil' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldinkId = $value; }
	if ($key == 'txtAantal' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldAantal = $value; }

	if ($key == 'kzlReden' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldreden = $value; }
	 //else if ($key == 'kzlReden' && empty($value)) { /*echo $key.'='.$value.' ';*/ $fldreden = ''; }
	 
									}
// Transponder nummer inlezen als deze nog niet bestaat in tblSchaap
if($reader == 'Agrident'){
$zoek_transp_rd = mysqli_query($db,"
SELECT transponder, levensnummer
FROM impAgrident
WHERE Id = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));

	while( $ztr = mysqli_fetch_assoc($zoek_transp_rd)) { $tran_rd = $ztr['transponder']; $levnr_rd = $ztr['levensnummer']; }


$zoek_transp_db = mysqli_query($db,"
SELECT schaapId, transponder
FROM tblSchaap
WHERE levensnummer = '".mysqli_real_escape_string($db,$levnr_rd)."'
") or die (mysqli_error($db));

	while( $ztd = mysqli_fetch_assoc($zoek_transp_db)) { $schaapId_db = $ztd['schaapId']; $tran_db = $ztd['transponder']; }

if (isset($schaapId_db) && $tran_rd <> $tran_db) {
	$updateSchaap = "UPDATE tblSchaap set transponder = '".mysqli_real_escape_string($db,$tran_rd)."' WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId_db)."' " ;

	/*echo $updateSchaap.'<br>';*/	mysqli_query($db,$updateSchaap) or die (mysqli_error($db));
}


}
// Einde Transponder nummer inlezen als deze nog niet bestaat in tblSchaap

if ($fldKies == 1 && $fldDel == 0) {

// CONTROLE op alle verplichten velden bij medicatie
if (isset($flddag) && isset($fldAantal) && isset($fldinkId))
{

if($reader == 'Agrident') {	
$zoek_stalId = mysqli_query($db,"
SELECT max(stalId) stalId
FROM tblStal st
 join tblSchaap s on (st.schaapId = s.schaapId)
 join impAgrident rd on (rd.levensnummer = s.levensnummer)
WHERE rd.Id = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));
}
else {
$zoek_stalId = mysqli_query($db,"
SELECT max(stalId) stalId
FROM tblStal st
 join tblSchaap s on (st.schaapId = s.schaapId)
 join impReader rd on (rd.levnr_pil = s.levensnummer)
WHERE rd.readId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));	
}

	while( $st = mysqli_fetch_assoc($zoek_stalId)) { $stalId = $st['stalId']; }

/* CONTROLE */
// Controle op afvoerdatum
$zoek_afvoerdatum = mysqli_query($db,"
SELECT h.datum date, date_format(h.datum,'%d-%m-%Y') datum
FROM tblHistorie h
 join tblActie a on (a.actId = h.actId)
WHERE h.stalId = '".mysqli_real_escape_string($db,$stalId)."' and a.af = 1
") or die (mysqli_error($db));
	while( $afv = mysqli_fetch_assoc($zoek_afvoerdatum)) { $dmafv = $afv['date']; $afvdm = $afv['datum']; }
// Einde Controle op afvoerdatum



if(isset($dmafv) && $dmafv <= $flddag) {
$zoek_levensnummer = mysqli_query($db,"
SELECT s.levensnummer
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
WHERE st.stalId = '".mysqli_real_escape_string($db,$stalId)."'
") or die (mysqli_error($db));
	while( $lev = mysqli_fetch_assoc($zoek_levensnummer)) { $levnr = $lev['levensnummer']; }

	$fout = 'De datum bij '.$levnr.' moet voor '.$afvdm.' liggen.';

} /* EINDE CONTROLE */




else
{ /* INVOEREN */ 
$zoek_artikel = mysqli_query($db,"
SELECT a.artId, a.naam, a.stdat
FROM tblArtikel a
 join tblInkoop i on (a.artId = i.artId)
WHERE i.inkId = '".mysqli_real_escape_string($db,$fldinkId)."'
") or die (mysqli_error($db));
	while( $std = mysqli_fetch_assoc($zoek_artikel)) { $artId = $std['artId']; $naam = $std['naam']; $stdat = $std['stdat']; }

$hoeveel = $fldAantal*$stdat;


$zoek_inkIds_met_voorraad = mysqli_query($db,"
SELECT sum(vrdat) vrdat,stdat
FROM (
	SELECT i.inkat - sum(coalesce(n.nutat,0)) vrdat, a.stdat
	FROM tblArtikel a
	 join tblInkoop i on (a.artId = i.artId)
	 left join (
	 	SELECT inkId, sum(nutat*stdat) nutat
	 	FROM tblNuttig 
	 ) n on (i.inkId = n.inkId)
	WHERE i.artId = '".mysqli_real_escape_string($db,$artId)."'
	GROUP BY i.inkat, a.stdat
 ) a
GROUP BY stdat
") or die (mysqli_error($db));
	while ($check = mysqli_fetch_assoc($zoek_inkIds_met_voorraad)) { $tot_vrd = $check['vrdat']; }

	
$zoek_voorraad_laatste_ink = mysqli_query ($db,"
SELECT i.inkat - sum(coalesce(n.nutat*n.stdat,0)) vrdat, a.stdat
FROM tblInkoop i
 join tblArtikel a on (a.artId = i.artId)
 left join tblNuttig n on (i.inkId = n.inkId)
WHERE i.inkId = '".mysqli_real_escape_string($db,$fldinkId)."'
GROUP BY i.inkat, a.stdat
") or die (mysqli_error($db));
	while ($i_vrd = mysqli_fetch_assoc($zoek_voorraad_laatste_ink)) { $ink_vrd = $i_vrd['vrdat']; }


if ($tot_vrd < $hoeveel)	{$fout = "De voorraad van ".$naam." is niet toereikend";}
else if($ink_vrd < $hoeveel)	{ // Als de voorraad van de inkoophoeveelheid niet toereikend is
	$rest_vrd = $ink_vrd; $new_vrd = $hoeveel-$ink_vrd;
	$hoev1 = $rest_vrd/$stdat ;
	$hoev2 = $new_vrd/$stdat ;

/* Restant inlezen */
$insert_tblHistorie = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$flddag)."', actId = 8 ";
/*echo $insert_tblHistorie.'<br>';*/		mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));

$zoek_hisId = mysqli_query ($db,"
SELECT max(hisId) hisId
FROM tblHistorie
WHERE actId = 8 and stalId = '".mysqli_real_escape_string($db,$stalId)."'
") or die (mysqli_error($db));
	while ($hi = mysqli_fetch_assoc($zoek_hisId)) { $hisId1 = $hi['hisId']; }

$insertNuttig_rest = "INSERT INTO tblNuttig SET hisId = '".mysqli_real_escape_string($db,$hisId1)."', inkId = '".mysqli_real_escape_string($db,$fldinkId)."', nutat = '".mysqli_real_escape_string($db,$hoev1)."', stdat = '".mysqli_real_escape_string($db,$stdat)."', reduId = " . db_null_input($fldreden);	
/*echo $insertNuttig_rest.'<br>';*/		mysqli_query($db,$insertNuttig_rest) or die (mysqli_error($db));
/* Einde Restant inlezen */

/* Eerst volgende aankoop aanspreken */
$zoek_inkId2 = mysqli_query ($db,"
SELECT min(inkId) inkId
FROM tblInkoop
WHERE artId = '".mysqli_real_escape_string($db,$artId)."' and inkId > '".mysqli_real_escape_string($db,$fldinkId)."'
") or die (mysqli_error($db));
	while ($ink = mysqli_fetch_assoc($zoek_inkId2)) { $inkId2 = $ink['inkId']; }
	
$insert_tblHistorie = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$flddag)."', actId = 8 ";
/*echo $insert_tblHistorie.'<br>';*/		mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));

$zoek_hisId = mysqli_query ($db,"
SELECT max(hisId) hisId
FROM tblHistorie
WHERE actId = 8 and stalId = '".mysqli_real_escape_string($db,$stalId)."'
") or die (mysqli_error($db));
	while ($hi = mysqli_fetch_assoc($zoek_hisId)) { $hisId2 = $hi['hisId']; }
	
	$insertNuttig_new = "INSERT INTO tblNuttig SET hisId = '".mysqli_real_escape_string($db,$hisId2)."', inkId = '".mysqli_real_escape_string($db,$inkId2)."', nutat = '".mysqli_real_escape_string($db,$hoev2)."', stdat = '".mysqli_real_escape_string($db,$stdat)."', reduId = " . db_null_input($fldreden);	
/*echo $insertNuttig_new.'<br>';*/		mysqli_query($db,$insertNuttig_new) or die (mysqli_error($db));
/* Einde Eerst volgende aankoop aanspreken */

if($reader == 'Agrident')  {
    $updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' " ;
	 }
else { 
	$updateReader = "UPDATE impReader SET verwerkt = 1 WHERE readId = '".mysqli_real_escape_string($db,$recId)."' " ;
	 }
	 
} // Einde Als de voorraad van de inkoophoeveelheid niet toereikend is
else {

$insert_tblHistorie = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$flddag)."', actId = 8 ";
/*echo $insert_tblHistorie.'<br>';*/		mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));

$zoek_hisId = mysqli_query ($db,"
SELECT max(hisId) hisId
FROM tblHistorie
WHERE actId = 8 and stalId = '".mysqli_real_escape_string($db,$stalId)."'
") or die (mysqli_error($db));
	while ($hi = mysqli_fetch_assoc($zoek_hisId)) { $hisId = $hi['hisId']; }

$hoev = $hoeveel/$stdat; 
	$insert_tblNuttig = "INSERT INTO tblNuttig SET hisId = '".mysqli_real_escape_string($db,$hisId)."', inkId = '".mysqli_real_escape_string($db,$fldinkId)."', nutat = '".mysqli_real_escape_string($db,$hoev)."', stdat = '".mysqli_real_escape_string($db,$stdat)."', reduId = " . db_null_input($fldreden);
/*echo $insert_tblNuttig.'<br>';*/		mysqli_query($db,$insert_tblNuttig) or die (mysqli_error($db));	

		
if($reader == 'Agrident')  {
    $updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' " ;
	 }
else { 
	$updateReader = "UPDATE impReader SET verwerkt = 1 WHERE readId = '".mysqli_real_escape_string($db,$recId)."' " ;
	 }

	}	

	if(isset($updateReader)) { /*echo $updateReader.'<br>';*/	mysqli_query($db,$updateReader) or die (mysqli_error($db));  
}


} /* EINDE INVOEREN  EINDE */

} // CONTROLE op alle verplichten velden bij medicatie

} // Einde if ($fldKies == 1 && $fldDel == 0)

if ($fldKies == 0 && $fldDel == 1) {

  foreach($id as $key => $value) {	

  if($reader == 'Agrident')  {
    $updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' " ;
 		}
  else {   		
    $updateReader = "UPDATE impReader set verwerkt = 1 WHERE readId = '".mysqli_real_escape_string($db,$recId)."' " ;
		}
		/*echo $updateReader.'<br>';*/		mysqli_query($db,$updateReader) or die (mysqli_error($db));
	}

}



//echo '<br>'.'einde '.$recId.'<br>';
unset($dmafv);
	}

?>
					
	