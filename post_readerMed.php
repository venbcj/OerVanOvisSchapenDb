<!--  18-12-2014 : Gemaakt 
 12-03-2017 : Aangpast na.v. Release 2 of wel nieuwe databasestructuur, verwijderen mogelijk gemaakt en mysql verandert in mysqli 
Toegepast in : InsMedicijn.php 
15-11-2020 : onderscheid gemaakt tussen reader Agrident en Biocontrol 
24-1-2021 : Sql beveiligd met quotes. Transponder nummer opslaan in tblSchaap als deze nog niet bestaat Verschil tussen kiezen of verwijderen herschreven 31-1 $schaapId gewijzigd naar $schaapId_db 
8-5-2021 : isset($verwerkt) toegevoegd om dubbele invoer te voorkomen 
25-6-20221 Bij $zoek_totale_voorraad GROUP BY inkId toegevoegd 
22-9-2021 : Functie inlezen_pil toegevoegd -->

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
									/*echo $key.'='.$valuedatum.' ';*/ $fldDay = $valuedatum; }
	
	if ($key == 'kzlPil' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldArtId = $value; }
	if ($key == 'txtAantal' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldToedat = $value; }

	if ($key == 'kzlReden' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldReden = $value; }
	 //else if ($key == 'kzlReden' && empty($value)) { /*echo $key.'='.$value.' ';*/ $fldReden = ''; }
	 
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

// (extra) controle of readerregel reeds is verwerkt. Voor als de pagina 2x wordt verstuurd bij fouten op de pagina
unset($verwerkt);
if($reader == 'Agrident') {
$zoek_readerRegel_verwerkt = mysqli_query($db,"
SELECT verwerkt
FROM impAgrident
WHERE Id = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db)); 
}
else {
$zoek_readerRegel_verwerkt = mysqli_query($db,"
SELECT verwerkt
FROM impReader
WHERE readId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));
}
while($verw = mysqli_fetch_array($zoek_readerRegel_verwerkt))
{ $verwerkt = $verw['verwerkt']; }
// Einde (extra) controle of readerregel reeds is verwerkt.

if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt)) { // isset($verwerkt) is een extra controle om dubbele invoer te voorkomen

// CONTROLE op alle verplichten velden bij medicatie
if (isset($fldDay) && isset($fldToedat) && isset($fldArtId))
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
unset($dmafv);
$zoek_afvoerdatum = mysqli_query($db,"
SELECT h.datum date, date_format(h.datum,'%d-%m-%Y') datum
FROM tblHistorie h
 join tblActie a on (a.actId = h.actId)
WHERE h.stalId = '".mysqli_real_escape_string($db,$stalId)."' and a.af = 1
") or die (mysqli_error($db));
	while( $afv = mysqli_fetch_assoc($zoek_afvoerdatum)) { $dmafv = $afv['date']; $afvdm = $afv['datum']; }
// Einde Controle op afvoerdatum



if(isset($dmafv) && $dmafv <= $fldDay) {
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
$zoek_artikel_gegevens = mysqli_query($db,"
SELECT a.naam, a.stdat
FROM tblArtikel a
WHERE artId = '".mysqli_real_escape_string($db,$fldArtId)."'
") or die (mysqli_error($db));
	while( $std = mysqli_fetch_assoc($zoek_artikel_gegevens)) { $naam = $std['naam']; $stdat = $std['stdat']; }

$toedtotal = $fldToedat*$stdat;


$zoek_totale_voorraad = mysqli_query($db,"
SELECT sum(i.inkat) - sum(coalesce(n.nutat,0)) vrdat
FROM tblInkoop i
 left join (
 	SELECT inkId, sum(nutat*stdat) nutat
 	FROM tblNuttig 
 	GROUP BY inkId
 ) n on (i.inkId = n.inkId)
WHERE i.artId = '".mysqli_real_escape_string($db,$fldArtId)."'
") or die (mysqli_error($db));
	while ($check = mysqli_fetch_assoc($zoek_totale_voorraad)) { $tot_vrd = $check['vrdat']; }

if ($tot_vrd < $toedtotal)	{$fout = "De voorraad van ".$naam." is niet toereikend";}

else {

$insert_tblHistorie = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$fldDay)."', actId = 8 ";
/*echo $insert_tblHistorie.'<br>';*/		mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));

$zoek_hisId = mysqli_query ($db,"
SELECT max(hisId) hisId
FROM tblHistorie
WHERE actId = 8 and stalId = '".mysqli_real_escape_string($db,$stalId)."'
") or die (mysqli_error($db));
	while ($hi = mysqli_fetch_assoc($zoek_hisId)) { $hisId = $hi['hisId']; }

inlezen_pil($db, $hisId, $fldArtId, $fldToedat, $fldDay, $fldReden);

if($reader == 'Agrident')  {
    $updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' " ;
	 }
else { 
	$updateReader = "UPDATE impReader SET verwerkt = 1 WHERE readId = '".mysqli_real_escape_string($db,$recId)."' " ;
	 }

/*echo $updateReader.'<br>';*/	mysqli_query($db,$updateReader) or die (mysqli_error($db));

}


} /* EINDE INVOEREN  EINDE */

} // CONTROLE op alle verplichten velden bij medicatie

} // Einde if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt))

if ($fldKies == 0 && $fldDel == 1) {

  if($reader == 'Agrident')  {
    $updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' " ;
 		}
  else {   		
    $updateReader = "UPDATE impReader set verwerkt = 1 WHERE readId = '".mysqli_real_escape_string($db,$recId)."' " ;
		}
		/*echo $updateReader.'<br>';*/		mysqli_query($db,$updateReader) or die (mysqli_error($db));
}



//echo '<br>'.'einde '.$recId.'<br>';

	}

?>
					
	