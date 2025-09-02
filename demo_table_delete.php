<?php
/* Aangemaakt : 14-5-2016  Onderstaande statements moeten worden uitgevoerd om maandelijks demo gegevens te verwijderen.
	Wordt ook gebruikt om handmatig de (basis)gegevens te verwijderen in de test omgevng 
22-1-2017 : tblBezetting gewijzigd naar tblBezet
*/

// VERWIJDEREN RECORDS
/********************	Voorraadbeheer	*******************************************************************/

//tblNuttig
$zoek_NutId = mysqli_query($db,"
SELECT n.nutId
FROM ".mysqli_real_escape_string($db,$dtb).".tblNuttig n
 join ".mysqli_real_escape_string($db,$dtb).".tblHistorie h on (h.hisId = n.hisId)
 join ".mysqli_real_escape_string($db,$dtb).".tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)."
ORDER BY n.nutId
") or die (mysqli_error($db));
//mysqli_query($db,$upd_tblNuttig) or die (mysqli_error($db));

//mysqli_query($db,"DELETE FROM tblNuttig WHERE `delete` = 1 ") or die (mysqli_error($db));

$nutId = array();
while( $nut = mysqli_fetch_assoc($zoek_NutId)) { $nutId[] = $nut['nutId'];  
	
$nutIds = implode(',',$nutId);
	}
	if(isset($nutIds)) {
	$del_tblNuttig = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblNuttig` WHERE nutId IN (".mysqli_real_escape_string($db,$nutIds).") ; ";
	#echo $del_tblNuttig.'<br>';
	mysqli_query($db,$del_tblNuttig);
	}

//Einde tblNuttig


//tblInkoop
$zoek_inkId = mysqli_query($db,"
SELECT i.inkId
FROM ".mysqli_real_escape_string($db,$dtb).".tblInkoop i
 join ".mysqli_real_escape_string($db,$dtb).".tblArtikel a on (a.artId = i.artId)
 join ".mysqli_real_escape_string($db,$dtb).".tblEenheiduser eu on (eu.enhuId = a.enhuId)
WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)."
ORDER BY i.inkId
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblInkoop) or die (mysqli_error($db));

#mysqli_query($db,"DELETE FROM tblInkoop WHERE `delete` = 1 ") or die (mysqli_error($db));

$inkId = array();
while( $ink = mysqli_fetch_assoc($zoek_inkId)) { $inkId[] = $ink['inkId'];  
	
$inkIds = implode(',',$inkId);
	}
	if(isset($inkIds)) {
	$del_tblInkoop = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblInkoop` WHERE inkId IN (".mysqli_real_escape_string($db,$inkIds).") ; ";
	#echo $del_tblInkoop.'<br>';
	mysqli_query($db,$del_tblInkoop);
	}
//Einde tblInkoop
//tblArtikel
$zoek_artId = mysqli_query($db,"
SELECT a.artId
FROM ".mysqli_real_escape_string($db,$dtb).".tblArtikel a
 join ".mysqli_real_escape_string($db,$dtb).".tblEenheiduser eu on (eu.enhuId = a.enhuId)
WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)."
ORDER BY a.artId
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblArtikel) or die (mysqli_error($db));

#mysqli_query($db,"DELETE FROM tblArtikel WHERE `delete` = 1 ") or die (mysqli_error($db));

$artId = array();
while( $art = mysqli_fetch_assoc($zoek_artId)) { $artId[] = $art['artId'];  
	
$artIds = implode(',',$artId);
	}
	if(isset($artIds)) {
	$del_tblArtikel = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblArtikel` WHERE artId IN (".mysqli_real_escape_string($db,$artIds).") ; ";
	#echo $del_tblArtikel.'<br>';
	mysqli_query($db,$del_tblArtikel);
	}
//Einde tblArtikel
//tblEenheiduser
$zoek_enhuId = mysqli_query($db,"
SELECT eu.enhuId
FROM ".mysqli_real_escape_string($db,$dtb).".tblEenheiduser eu
WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)."
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblEenheiduser) or die (mysqli_error($db));

#mysqli_query($db,"DELETE FROM tblEenheiduser WHERE `delete` = 1 ") or die (mysqli_error($db));

$enhuId = array();
while( $enhu = mysqli_fetch_assoc($zoek_enhuId)) { $enhuId[] = $enhu['enhuId'];  
	
$enhuIds = implode(',',$enhuId);
	}
	if(isset($enhuIds)) {
	$del_tblEenheiduser = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblEenheiduser` WHERE enhuId IN (".mysqli_real_escape_string($db,$enhuIds).") ; ";
	#echo $del_tblEenheiduser.'<br>';
	mysqli_query($db,$del_tblEenheiduser);
	}
//Einde tblEenheiduser
/********************	Einde Voorraadbeheer	*******************************************************************/
/********************	Melden	*******************************************************************/

//tblRequest
$zoek_reqId = mysqli_query($db,"
SELECT r.reqId
FROM ".mysqli_real_escape_string($db,$dtb).".tblRequest r
 join ".mysqli_real_escape_string($db,$dtb).".tblMelding m on (r.reqId = m.reqId)
 join ".mysqli_real_escape_string($db,$dtb).".tblHistorie h on (h.hisId = m.hisId)
 join ".mysqli_real_escape_string($db,$dtb).".tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)."
GROUP BY r.reqId
ORDER BY r.reqId
") or die (mysqli_error($db));
//GROUP BY r.reqId
#mysqli_query($db,$upd_tblRequest) or die (mysqli_error($db));

#mysqli_query($db,"DELETE FROM tblRequest WHERE `delete` = 1 ") or die (mysqli_error($db));

$reqId = array();
while( $req = mysqli_fetch_assoc($zoek_reqId)) { $reqId[] = $req['reqId'];  

$reqIds = implode(',',$reqId);
	}
	if(isset($reqIds)) {
	$del_tblRequest = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblRequest` WHERE reqId IN (".mysqli_real_escape_string($db,$reqIds).") ; ";
	#echo $del_tblRequest.'<br>'.'<br>';
	mysqli_query($db,$del_tblRequest);
	}
//Einde tblRequest

//tblMelding
$zoek_meldId = mysqli_query($db,"
SELECT m.meldId
FROM ".mysqli_real_escape_string($db,$dtb).".tblMelding m
 join ".mysqli_real_escape_string($db,$dtb).".tblHistorie h on (h.hisId = m.hisId)
 join ".mysqli_real_escape_string($db,$dtb).".tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)."
ORDER BY m.meldId
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblMelding) or die (mysqli_error($db));

#mysqli_query($db,"DELETE FROM tblMelding WHERE `delete` = 1 ") or die (mysqli_error($db));

$meldId = array();
while( $meld = mysqli_fetch_assoc($zoek_meldId)) { $meldId[] = $meld['meldId'];  

$meldIds = implode(',',$meldId);
	}
	if(isset($meldIds)) {
	$del_tblMelding = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblMelding` WHERE meldId IN (".mysqli_real_escape_string($db,$meldIds).") ; ";
	#echo $del_tblMelding.'<br>';
	mysqli_query($db,$del_tblMelding);
	}

//Einde tblMelding
/********************	Einde Melden	*******************************************************************/
/********************	Het schaap		*******************************************************************/

//tblVolwas 

$zoek_volwId = mysqli_query($db,"
SELECT v.volwId
FROM ".mysqli_real_escape_string($db,$dtb).".tblVolwas v
 join ".mysqli_real_escape_string($db,$dtb).".tblSchaap s on (v.volwId = s.volwId)
 join ".mysqli_real_escape_string($db,$dtb).".tblStal st on (s.schaapId = st.schaapId)
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)."
ORDER BY v.volwId
") or die (mysqli_error($db));

//echo $upd_tblVolwas;
#mysqli_query($db,$upd_tblVolwas) or die (mysqli_error($db));

#mysqli_query($db,"DELETE FROM tblVolwas WHERE `delete` = 1 ") or die (mysqli_error($db));

$volwId = array();
while( $volwas = mysqli_fetch_assoc($zoek_volwId)) { $volwId[] = $volwas['volwId'];  

$volwIds = implode(',',$volwId);
	}
	if(isset($volwIds)) {
	$del_tblVolwas = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblVolwas` WHERE volwId IN (".mysqli_real_escape_string($db,$volwIds).") ; ";
	#echo $del_tblVolwas.'<br>';
	mysqli_query($db,$del_tblVolwas);
	}
//Einde tblVolwas


//tblSchaap
$zoek_schaapId = mysqli_query($db,"
SELECT s.schaapId
FROM ".mysqli_real_escape_string($db,$dtb).".tblSchaap s
 join ".mysqli_real_escape_string($db,$dtb).".tblStal st on (s.schaapId = st.schaapId)
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)."
ORDER BY s.schaapId
") or die (mysqli_error($db));
#mysqli_query($db,$zoek_tblSchaap) or die (mysqli_error($db));

#mysqli_query($db,"DELETE FROM tblSchaap WHERE `delete` = 1 ") or die (mysqli_error($db));

$schaapId = array();
while( $schaap = mysqli_fetch_assoc($zoek_schaapId)) { $schaapId[] = $schaap['schaapId'];  
	
$schaapIds = implode(',',$schaapId);
	}
	if(isset($schaapIds)) {
	$del_tblSchaap = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblSchaap` WHERE schaapId IN (".mysqli_real_escape_string($db,$schaapIds).") ; ";
	#echo $del_tblSchaap.'<br>';
	mysqli_query($db,$del_tblSchaap);
	}
//Einde tblSchaap

//tblHistorie
$zoek_hisId = mysqli_query($db,"
SELECT h.hisId
FROM ".mysqli_real_escape_string($db,$dtb).".tblHistorie h
 join ".mysqli_real_escape_string($db,$dtb).".tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)."
ORDER BY h.hisId
") or die (mysqli_error($db));

#mysqli_query($db,$upd_tblHistorie) or die (mysqli_error($db));

#mysqli_query($db,"DELETE FROM tblHistorie WHERE `delete` = 1 ") or die (mysqli_error($db));

$hisId = array();
while( $his = mysqli_fetch_assoc($zoek_hisId)) { $hisId[] = $his['hisId'];  
	
$hisIds = implode(',',$hisId);
	}
	if(isset($hisIds)) {
	$del_tblHistorie = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblHistorie` WHERE hisId IN (".mysqli_real_escape_string($db,$hisIds).") ; ";
	#echo $del_tblHistorie.'<br>';
	mysqli_query($db,$del_tblHistorie);
	}
//Einde tblHistorie

//tblStal
$zoek_stalId = mysqli_query($db,"
SELECT st.stalId
FROM ".mysqli_real_escape_string($db,$dtb).".tblStal st
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)."
ORDER BY st.stalId
") or die (mysqli_error($db));

#mysqli_query($db,$upd_tblStal) or die (mysqli_error($db));

$stalId = array();
while( $stal = mysqli_fetch_assoc($zoek_stalId)) { $stalId[] = $stal['stalId'];  
	
$stalIds = implode(',',$stalId);
	}
	if(isset($stalIds)) {
	$del_tblStal = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblStal` WHERE stalId IN (".mysqli_real_escape_string($db,$stalIds).") ; ";
	#echo $del_tblStal.'<br>';
	mysqli_query($db,$del_tblStal);
	}
//Einde tblStal

// Dieren die niet zijn gekoppeld aan een stalId verwijderen. Dit kan bij inlezen dracht vaderdieren zijn. Wordt niet verwijderd als dit dier bij anderen ook voorkomt in impReader => dracht. Zie not exists 
//tblSchaap
$zoek_schaapId_dracht = mysqli_query($db,"
SELECT s.schaapId
FROM ".mysqli_real_escape_string($db,$dtb).".tblSchaap s
 join ".mysqli_real_escape_string($db,$dtb).".impReader r on (r.levnr_ovpl = s.levensnummer)
 left join ".mysqli_real_escape_string($db,$dtb).".tblStal st on (s.schaapId = st.schaapId)
WHERE r.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(st.stalId) and isnull(teller_ovpl)
 and not exists (
	SELECT rd.levnr_ovpl
	FROM ".mysqli_real_escape_string($db,$dtb).".impReader rd
	WHERE s.levensnummer = rd.levnr_ovpl and isnull(rd.teller_ovpl) and rd.lidId <> ".mysqli_real_escape_string($db,$lidId)."
	)
GROUP BY s.schaapId
ORDER BY s.schaapId
") or die (mysqli_error($db));
#mysqli_query($db,$zoek_tblSchaap) or die (mysqli_error($db));

#mysqli_query($db,"DELETE FROM tblSchaap WHERE `delete` = 1 ") or die (mysqli_error($db));

$schaapId_dracht = array();
while( $sch = mysqli_fetch_assoc($zoek_schaapId_dracht)) { $schaapId_dracht[] = $sch['schaapId'];

$schaapIds_dracht = implode(',',$schaapId_dracht);
	}
	if(isset($schaapIds_dracht)) {
	$del_tblSchaap = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblSchaap` WHERE schaapId IN (".mysqli_real_escape_string($db,$schaapIds_dracht).") ; ";
	#echo $del_tblSchaap.'<br>';
	mysqli_query($db,$del_tblSchaap);
	}
//Einde tblSchaap
/********************	Einde Het schaap	*******************************************************************/
/********************	Reader		*******************************************************************/
// Dracht uit tabel tblVolwas halen. Dit is het restant uit tabel tblVolwas nadat de schapen zijn verwijderd hierboven.
//tblVolwas
$zoek_volwId = mysqli_query($db,"
SELECT v.volwId
FROM ".mysqli_real_escape_string($db,$dtb).".tblVolwas v
 join impReader r on (v.readId = r.readId)
WHERE r.lidId = ".mysqli_real_escape_string($db,$lidId)."
ORDER BY v.volwId
") or die (mysqli_error($db));

#mysqli_query($db,$upd_tblVolwas) or die (mysqli_error($db));

$volwId = array();
while( $volw = mysqli_fetch_assoc($zoek_volwId)) { $volwId[] = $volw['volwId'];  
	
$volwIds = implode(',',$volwId);
	}
	if(isset($volwIds)) {
	$del_tblVolwas = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblVolwas` WHERE volwId IN (".mysqli_real_escape_string($db,$volwIds).") ; ";
	#echo $del_tblVolwas.'<br>';
	mysqli_query($db,$del_tblVolwas);
	}
//Einde tblVolwas

//impReader
$del_impReader = "DELETE FROM ".$dtb.".`impReader` WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." ; ";
	mysqli_query($db,$del_impReader);
/********************	Einde Reader	*******************************************************************/
/********************	Relaties		*******************************************************************/
//tblPersoon
$zoek_persId = mysqli_query($db,"
SELECT ps.persId
FROM ".mysqli_real_escape_string($db,$dtb).".tblPersoon ps
 join ".mysqli_real_escape_string($db,$dtb).".tblPartij p on (p.partId = ps.partId)
WHERE p.lidId = ".mysqli_real_escape_string($db,$lidId)."
ORDER BY ps.persId 
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblPersoon) or die (mysqli_error($db));

#mysqli_query($db,"DELETE FROM tblPersoon WHERE `delete` = 1 ") or die (mysqli_error($db));

$persId = array();
while( $pers = mysqli_fetch_assoc($zoek_persId)) { $persId[] = $pers['persId'];  
	
$persIds = implode(',',$persId);
	}
	if(isset($persIds)) {
	$del_tblPersoon = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblPersoon` WHERE persId IN (".mysqli_real_escape_string($db,$persIds).") ; ";
	#echo $del_tblPersoon.'<br>';
	mysqli_query($db,$del_tblPersoon);
	}
//Einde tblPersoon
//tblVervoer
$zoek_vervId = mysqli_query($db,"
SELECT v.vervId
FROM ".mysqli_real_escape_string($db,$dtb).".tblVervoer v
 join ".mysqli_real_escape_string($db,$dtb).".tblPartij p on (p.partId = v.partId)
WHERE p.lidId = ".mysqli_real_escape_string($db,$lidId)."
ORDER BY v.vervId
") or die (mysqli_error($db));

#mysqli_query($db,$upd_tblVervoer) or die (mysqli_error($db));

#mysqli_query($db,"DELETE FROM tblVervoer WHERE `delete` = 1 ") or die (mysqli_error($db));

$vervId = array();
while( $verv = mysqli_fetch_assoc($zoek_vervId)) { $vervId[] = $verv['vervId'];  
	
$vervIds = implode(',',$vervId);
	}
	if(isset($vervIds)) {
	$del_tblVervoer = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblVervoer` WHERE vervId IN (".mysqli_real_escape_string($db,$vervIds).") ; ";
	#echo $del_tblVervoer.'<br>';
	mysqli_query($db,$del_tblVervoer);
	}
//Einde tblVervoer
//tblAdres
$zoek_adrId = mysqli_query($db,"
SELECT a.adrId
FROM ".mysqli_real_escape_string($db,$dtb).".tblAdres a
 join ".mysqli_real_escape_string($db,$dtb).".tblRelatie r on (a.relId = r.relId)
 join ".mysqli_real_escape_string($db,$dtb).".tblPartij p on (p.partId = r.partId)
WHERE p.lidId = ".mysqli_real_escape_string($db,$lidId)."
ORDER BY a.adrId
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblAdres) or die (mysqli_error($db));

#mysqli_query($db,"DELETE FROM tblAdres WHERE `delete` = 1 ") or die (mysqli_error($db));

$adrId = array();
while( $adr = mysqli_fetch_assoc($zoek_adrId)) { $adrId[] = $adr['adrId'];  

$adrIds = implode(',',$adrId);
	}
	if(isset($adrIds)) {
	$del_tblAdres = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblAdres` WHERE adrId IN (".mysqli_real_escape_string($db,$adrIds).") ; ";
	#echo $del_tblAdres.'<br>';
	mysqli_query($db,$del_tblAdres);
	}
//Einde tblAdres
//tblRelatie
$zoek_relId = mysqli_query($db,"
SELECT r.relId
FROM ".mysqli_real_escape_string($db,$dtb).".tblRelatie r
 join ".mysqli_real_escape_string($db,$dtb).".tblPartij p on (p.partId = r.partId)
WHERE p.lidId = ".mysqli_real_escape_string($db,$lidId)."
ORDER BY r.relId
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblRelatie) or die (mysqli_error($db));

#mysqli_query($db,"DELETE FROM tblRelatie WHERE `delete` = 1 ") or die (mysqli_error($db));

$relId = array();
while( $rel = mysqli_fetch_assoc($zoek_relId)) { $relId[] = $rel['relId'];
	
$relIds = implode(',',$relId);
	}
	if(isset($relIds)) {
	$del_tblRelatie = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblRelatie` WHERE relId IN (".mysqli_real_escape_string($db,$relIds).") ; ";
	#echo $del_tblRelatie.'<br>';
	mysqli_query($db,$del_tblRelatie);
	}
//Einde tblRelatie
//tblPartij
$zoek_partId = mysqli_query($db,"
SELECT p.partId
FROM ".mysqli_real_escape_string($db,$dtb).".tblPartij p
WHERE p.lidId = ".mysqli_real_escape_string($db,$lidId)."
ORDER BY p.partId
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblPartij) or die (mysqli_error($db));

#mysqli_query($db,"DELETE FROM tblPartij WHERE `delete` = 1 ") or die (mysqli_error($db));

$partId = array();
while( $part = mysqli_fetch_assoc($zoek_partId)) { $partId[] = $part['partId'];  
	
$partIds = implode(',',$partId);
	}
	if(isset($partIds)) {
	$del_tblPartij = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblPartij` WHERE partId IN (".mysqli_real_escape_string($db,$partIds).") ; ";
	#echo $del_tblPartij.'<br>';
	mysqli_query($db,$del_tblPartij);
	}
//Einde tblPartij
/********************	Einde Relaties	*******************************************************************/
/********************	Hokken		*******************************************************************/


//tblBezet
$zoek_bezId = mysqli_query($db,"
SELECT b.bezId
FROM ".mysqli_real_escape_string($db,$dtb).".tblBezet b
 join ".mysqli_real_escape_string($db,$dtb).".tblPeriode p on (b.periId = p.periId)
 join ".mysqli_real_escape_string($db,$dtb).".tblHok h on (p.hokId = h.hokId)
WHERE h.lidId = ".mysqli_real_escape_string($db,$lidId)."
ORDER BY b.bezId
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblBezet) or die (mysqli_error($db));

#mysqli_query($db,"DELETE FROM tblBezet WHERE `delete` = 1 ") or die (mysqli_error($db));

$bezId = array();
while( $bez = mysqli_fetch_assoc($zoek_bezId)) { $bezId[] = $bez['bezId'];  
	
$bezIds = implode(',',$bezId);
	}
	if(isset($bezIds)) {
	$del_tblBezet = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblBezet` WHERE bezId IN (".mysqli_real_escape_string($db,$bezIds).") ; ";
	#echo $del_tblBezet.'<br>';
	mysqli_query($db,$del_tblBezet);
	}
//Einde tblBezet
//tblVoeding
$zoek_voedId = mysqli_query($db,"
SELECT v.voedId
FROM ".mysqli_real_escape_string($db,$dtb).".tblVoeding v
 join ".mysqli_real_escape_string($db,$dtb).".tblPeriode p on (v.periId = p.periId)
 join ".mysqli_real_escape_string($db,$dtb).".tblHok h on (p.hokId = h.hokId)
WHERE h.lidId = ".mysqli_real_escape_string($db,$lidId)."
ORDER BY v.voedId
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblVoeding) or die (mysqli_error($db));

#mysqli_query($db,"DELETE FROM tblVoeding WHERE `delete` = 1 ") or die (mysqli_error($db));

$voedId = array();
while( $voed = mysqli_fetch_assoc($zoek_voedId)) { $voedId[] = $voed['voedId'];  
	
$voedIds = implode(',',$voedId);
	}
	if(isset($voedIds)) {
	$del_tblVoeding = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblVoeding` WHERE voedId IN (".mysqli_real_escape_string($db,$voedIds).") ; ";
	#echo $del_tblVoeding.'<br>'.'<br>';
	mysqli_query($db,$del_tblVoeding);
	}
//Einde tblVoeding
//tblPeriode
$zoek_periId = mysqli_query($db,"
SELECT p.periId
FROM ".mysqli_real_escape_string($db,$dtb).".tblPeriode p
 join ".mysqli_real_escape_string($db,$dtb).".tblHok h on (p.hokId = h.hokId)
WHERE h.lidId = ".mysqli_real_escape_string($db,$lidId)."
ORDER BY p.periId
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblPeriode) or die (mysqli_error($db));

#mysqli_query($db,"DELETE FROM tblPeriode WHERE `delete` = 1 ") or die (mysqli_error($db));

$periId = array();
while( $peri = mysqli_fetch_assoc($zoek_periId)) { $periId[] = $peri['periId'];  
	
$periIds = implode(',',$periId);
	}
	if(isset($periIds)) {
	$del_tblPeriode = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblPeriode` WHERE periId IN (".mysqli_real_escape_string($db,$periIds).") ; ";
	#echo $del_tblPeriode.'<br>';
	mysqli_query($db,$del_tblPeriode);
	}
//Einde tblPeriode
//tblHok
$zoek_hokId = mysqli_query($db,"
SELECT h.hokId
FROM ".mysqli_real_escape_string($db,$dtb).".tblHok h
WHERE h.lidId = ".mysqli_real_escape_string($db,$lidId)."
ORDER BY h.hokId
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblHok) or die (mysqli_error($db));

#mysqli_query($db,"DELETE FROM tblHok WHERE `delete` = 1 ") or die (mysqli_error($db));

$hokId = array();
while( $hok = mysqli_fetch_assoc($zoek_hokId)) { $hokId[] = $hok['hokId'];  
	
$hokIds = implode(',',$hokId);
	}
	if(isset($hokIds)) {
	$del_tblHok = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblHok` WHERE hokId IN (".mysqli_real_escape_string($db,$hokIds).") ; ";
	#echo $del_tblHok.'<br>';
	mysqli_query($db,$del_tblHok);
	}
//Einde tblHok
/********************	Einde Hokken	*******************************************************************/
/********************	Financieel		*******************************************************************/

//tblLiquiditeit
$zoek_liqId = mysqli_query($db,"
SELECT l.liqId
FROM ".mysqli_real_escape_string($db,$dtb).".tblLiquiditeit l
 join ".mysqli_real_escape_string($db,$dtb).".tblRubriekuser ru on (ru.rubuId = l.rubuId)
WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)."
ORDER BY l.liqId
") or die (mysqli_error($db));
//ORDER BY liqId
#mysqli_query($db,$upd_tblLiquiditeit) or die (mysqli_error($db));

#mysqli_query($db,"DELETE FROM tblLiquiditeit WHERE `delete` = 1 ") or die (mysqli_error($db));

$liqId = array();
while( $liq = mysqli_fetch_assoc($zoek_liqId)) { $liqId[] = $liq['liqId'];  
	
$liqIds = implode(',',$liqId);
	}
	if(isset($liqIds)) {
	$del_tblLiquiditeit = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblLiquiditeit` WHERE liqId IN (".mysqli_real_escape_string($db,$liqIds).") ; ";
	#echo $del_tblLiquiditeit.'<br>';
	mysqli_query($db,$del_tblLiquiditeit);//Einde tblLiquiditeit
	}
//tblOpgaaf
$zoek_opgId = mysqli_query($db,"
SELECT o.opgId
FROM ".mysqli_real_escape_string($db,$dtb).".tblOpgaaf o
 join ".mysqli_real_escape_string($db,$dtb).".tblRubriekuser ru on (ru.rubuId = o.rubuId)
WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)."
ORDER BY o.opgId
") or die (mysqli_error($db));
//ORDER BY opgId
#mysqli_query($db,$upd_tblOpgaaf) or die (mysqli_error($db));

#mysqli_query($db,"DELETE FROM tblOpgaaf WHERE `delete` = 1 ") or die (mysqli_error($db));

$opgId = array();
while( $opg = mysqli_fetch_assoc($zoek_opgId)) { $opgId[] = $opg['opgId'];  

$opgIds = implode(',',$opgId);
	}
	if(isset($opgIds)) {
	$del_tblOpgaaf = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblOpgaaf` WHERE opgId IN (".mysqli_real_escape_string($db,$opgIds).") ; ";
	#echo $del_tblOpgaaf.'<br>';
	mysqli_query($db,$del_tblOpgaaf);
	}
//Einde tblOpgaaf
//tblSalber
$zoek_salbId = mysqli_query($db,"
SELECT sb.salbId
FROM ".mysqli_real_escape_string($db,$dtb).".tblSalber sb
 join ".mysqli_real_escape_string($db,$dtb).".tblRubriekuser ru on (ru.rubuId = sb.tblId)
WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and tbl = 'ru'

Union

SELECT sb.salbId
FROM ".mysqli_real_escape_string($db,$dtb).".tblSalber sb
 join ".mysqli_real_escape_string($db,$dtb).".tblElementuser eu on (eu.elemuId = sb.tblId)
WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and tbl = 'eu'
ORDER BY salbId
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblSalber) or die (mysqli_error($db));

#mysqli_query($db,"DELETE FROM tblSalber WHERE `delete` = 1 ") or die (mysqli_error($db));

$salbId = array();
while( $opg = mysqli_fetch_assoc($zoek_salbId)) { $salbId[] = $opg['salbId'];  

$salbIds = implode(',',$salbId);
	}
	if(isset($salbIds)) {
	$del_tblSalber = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblSalber` WHERE salbId IN (".mysqli_real_escape_string($db,$salbIds).") ; ";
	#echo $del_tblSalber.'<br>';
	mysqli_query($db,$del_tblSalber);
	}
//Einde tblSalber

//tblDeklijst
$zoek_dekId = mysqli_query($db,"
SELECT d.dekId
FROM ".mysqli_real_escape_string($db,$dtb).".tblDeklijst d
WHERE d.lidId = ".mysqli_real_escape_string($db,$lidId)."
ORDER BY d.dekId
") or die (mysqli_error($db));

#mysqli_query($db,$upd_tblDeklijst) or die (mysqli_error($db));
#mysqli_query($db,"DELETE FROM tblDeklijst WHERE `delete` = 1 ") or die (mysqli_error($db));

$dekId = array();
while( $dek = mysqli_fetch_assoc($zoek_dekId)) { $dekId[] = $dek['dekId'];  

$dekIds = implode(',', $dekId);
	}
	if(isset($dekIds)) {
	$del_tblDeklijst = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblDeklijst` WHERE dekId IN (".mysqli_real_escape_string($db,$dekIds).") ; ";
	#echo $del_tblDeklijst.'<br>'.'<br>';
	mysqli_query($db,$del_tblDeklijst);
	}
//echo '<br>';
//Einde tblDeklijst

//tblRubriekuser
$zoek_rubuId = mysqli_query($db,"
SELECT ru.rubuId
FROM ".mysqli_real_escape_string($db,$dtb).".tblRubriekuser ru
WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)."
ORDER BY ru.rubuId
") or die (mysqli_error($db));

#mysqli_query($db,$upd_tblRubriekuser) or die (mysqli_error($db));
#mysqli_query($db,"DELETE FROM tblRubriekuser WHERE `delete` = 1 ") or die (mysqli_error($db));

$rubuId = array();
while( $rubu = mysqli_fetch_assoc($zoek_rubuId)) { $rubuId[] = $rubu['rubuId'];  

$rubuIds = implode(',', $rubuId);
	}
	if(isset($rubuIds)) {
	$del_tblRubriekuser = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblRubriekuser` WHERE rubuId IN (".mysqli_real_escape_string($db,$rubuIds).") ; ";
	#echo $del_tblRubriekuser.'<br>'.'<br>';
	mysqli_query($db,$del_tblRubriekuser);
	}
//echo '<br>';
//Einde tblRubriekuser

//tblElementuser
$zoek_elemuId = mysqli_query($db,"
SELECT eu.elemuId
FROM ".mysqli_real_escape_string($db,$dtb).".tblElementuser eu
WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)."
ORDER BY eu.elemuId
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblElementuser) or die (mysqli_error($db));

#mysqli_query($db,"DELETE FROM tblElementuser WHERE `delete` = 1 ") or die (mysqli_error($db));

$elemuId = array();
while( $elemu = mysqli_fetch_assoc($zoek_elemuId)) { $elemuId[] = $elemu['elemuId'];  
	
$elemuIds = implode(',',$elemuId);
	}
	if(isset($elemuIds)) {
	$del_tblElementuser = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblElementuser` WHERE elemuId IN (".mysqli_real_escape_string($db,$elemuIds).") ; ";
	#echo $del_tblElementuser.'<br>'.'<br>';
	mysqli_query($db,$del_tblElementuser);
	}
//Einde tblElementuser
/********************	Einde Financieel	*******************************************************************/
/********************	Stamtabellen	*******************************************************************/

//tblMomentuser
$zoek_momuId = mysqli_query($db,"
SELECT mu.momuId
FROM ".mysqli_real_escape_string($db,$dtb).".tblMomentuser mu
WHERE mu.lidId = ".mysqli_real_escape_string($db,$lidId)."
ORDER BY mu.momuId
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblMomentuser) or die (mysqli_error($db));
#mysqli_query($db,"DELETE FROM tblMomentuser WHERE `delete` = 1 ") or die (mysqli_error($db));

$momuId = array();
while( $momu = mysqli_fetch_assoc($zoek_momuId)) { $momuId[] = $momu['momuId']; 

$momuIds = implode(',', $momuId); 
	}
	if(isset($momuIds)) {
	$del_tblMomentuser = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblMomentuser` WHERE momuId IN (".mysqli_real_escape_string($db,$momuIds).") ; ";
	#echo $del_tblMomentuser.'<br>'.'<br>';
	mysqli_query($db,$del_tblMomentuser);
	}
//Einde tblMomentuser



//tblRasuser
$zoek_rasuId = mysqli_query($db,"
SELECT ru.rasuId
FROM ".mysqli_real_escape_string($db,$dtb).".tblRasuser ru
WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)."
ORDER BY ru.rasuId
") or die (mysqli_error($db));

#mysqli_query($db,$upd_tblRasuser) or die (mysqli_error($db));
#mysqli_query($db,"DELETE FROM tblRasuser WHERE `delete` = 1 ") or die (mysqli_error($db));

$rasuId = array();
while( $rasu = mysqli_fetch_assoc($zoek_rasuId)) { $rasuId[] = $rasu['rasuId'];  

$rasuIds = implode(',', $rasuId);
	
	}
	if(isset($rasuIds)) {
	$del_tblRasuser = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblRasuser` WHERE rasuId IN (".mysqli_real_escape_string($db,$rasuIds).") ; ";
	#echo $del_tblRasuser.'<br>'.'<br>';
	mysqli_query($db,$del_tblRasuser);
	}
//Einde tblRasuser



//tblRedenuser
$zoek_reduId = mysqli_query($db,"
SELECT ru.reduId
FROM ".mysqli_real_escape_string($db,$dtb).".tblRedenuser ru
WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)."
ORDER BY ru.reduId
") or die (mysqli_error($db));

#mysqli_query($db,$upd_tblRedenuser) or die (mysqli_error($db));
#mysqli_query($db,"DELETE FROM tblRedenuser WHERE `delete` = 1 ") or die (mysqli_error($db));

$reduId = array();
while( $redu = mysqli_fetch_assoc($zoek_reduId)) { $reduId[] = $redu['reduId']; /*$reuId = $redu['reduId'];*/

$reduIds = implode(',', $reduId);
	
	}
	if(isset($reduIds)) { 
	$del_tblRedenuser = "DELETE FROM ".mysqli_real_escape_string($db,$dtb).".`tblRedenuser` WHERE reduId IN (".mysqli_real_escape_string($db,$reduIds).") ; ";
	#echo $del_tblRedenuser.'<br>'.'<br>';
	mysqli_query($db,$del_tblRedenuser);
	}
	

//Einde tblRedenuser	
/********************	Einde Stamtabellen	*******************************************************************/



?>