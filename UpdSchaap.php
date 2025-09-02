<?php /* 5-8-2014 karakters werknr variabel gemaakt
11-8-2014 : veld type gewijzigd in fase 
13-11-2014 variabel $actie_afv toegevoegd bij foutmeldingen zodat melding verkoopdatum niet verschijnt bij uitgevallen dieren 
1-03-2015 : login toegevoegd 
17-09-2016 : modules gesplitst */
$versie = '12-11-2016'; /* Aanwas wordt niet getoond als aankoop en aanwas dezelfde datum heeft. Aanvoer moeder- en vaderdieren dus */
$versie = '7-12-2016'; /* Uitscharen lammeren niet mogelijk gemaakt. ActId = 3 uit on-clause gehaald en appart genest. Herstelopties gespecificeerd met varianele $optie1, $optie2 enz. */
$versie = '15-01-2017'; /* Sortering kzlooi aangepast. Eerst werknr dan aantal lammeren i.p.v. andersom. zodat dubbele werknrs worden gezien. */
$versie = '22-01-2017'; /* 20-1-2017 Query's aangepast n.a.v. nieuwe tblDoel	22-1-2017 tblBezetting gewijzigd naar tblBezet */
$versie = '01-02-2017'; /* : Halsnummer toegevoegd		11-2-2017 : bij aanwas verblijf invoer toegevoegd */
$versie = '03-04-2017'; /* : Fokkersnummer toegevoegd		4-4-2017 : kleuren halsnummer uitgebreid */
$versie = '05-05-2017'; /* : Wijzigen van levensnummers mogelijk gemaakt	 21-7 controle spndm leeg toegevoegd !empty($nietna) */
$versie = '28-12-2017'; /* : In en uit verblijf plaatsen van moeder- en vaderdieren mogelijk gemaakt */
$versie = '16-02-2018'; /* : Afvoeren lam mogelijk gemaakt voor gebruikers die alleen melden. 4-3-2018 : Als afvoerdatum voor laatste historiedatum lag verscheen melding maar werd ook opgeslagen. Dit is aangepast en oorspronkelijke datum wordt teruggezet. Zie bij variabele $zetdatumterug */
$versie = '03-04-2018'; /* : Tussenweging toegevoegd	 */
$versie = '28-09-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '26-01-2019'; /* Ras wijzigbaar gemaakt */
$versie = '31-08-2019'; /* In query $zoek_nietvoor_datum actId = 5 and actId = 6 uit WHERE gehaald. Nav mail Rina 23-8-19 isset($nietna) toegevoegd om niet met datum van vandaag te vergelijken */
$versie = '15-03-2020'; /* Geslacht kan worden gewijzigd als schaap nog niet voorkomt in tblVolwas en niet bij een ander op de stallijst heeft gestaan */
$versie = '07-06-2020'; /* datepicker2 aan txtHokOoiDm toegevoegd */
$versie = '17-2-2020'; /* SQL beveiligd met quotes en keuzelijst Reden uitval gebasseerd op type 'sterfte' */
$versie = '08-4-2023'; /* Optie vermist toegevoegd */
$versie = '02-09-2023'; /* functie zoek_hisId_stal en andere bisisfuncties toegepast */
$versie = '04-11-2023'; /* Toevoegen van geboortedatum mogelijk gemaakt */
$versie = '01-01-2024'; /* and h.skip = 0 aangevuld bij tblHistorie */
$versie = '19-01-2024'; /* $hoknr nergens gedeclareerd, daarom gewijzigd naar $hok */
$versie = "11-03-2024"; /* Bij geneste query uit 
join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId) gewijzgd naar
join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
I.v.m. historie van stalId 22623. Dit dier is eerst verkocht en met terugwerkende kracht geplaatst in verblijf Afmest 1 */
$versie = "21-04-2024"; /* Lam uit verblijf halen niet meer mogelijk gemaakt */
$versie = "08-06-2024"; /* Afleveren aanwezige lammeren mogelijk gemaakt */
$versie = "26-10-2024"; /* Het zoeken naar afgevoerde dieren en dier wel of niet in een verblijf 2 aparte querys van gemaakt */
$versie = "03-11-2024"; /* Terug van uitscharen mogelijk gemaakt incl. medling naar RVO */
$versie = "09-11-2024"; /* $invoerdate <= $dmOoiHokNa gewijzigd naar $invoerdate < $dmOoiHokNa */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = 'top'> 31-12-24 Include "login.php"; voor Include "header.php" gezet */
$versie = '18-02-2024'; /* De historie als 3e kolom getoond rechts naast de andere gegevens */

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>wijzigen schaap</title>
</head>
<body>

<?php
$titel = 'Schaap bijwerken';
$file = "Zoeken.php";
Include "login.php"; ?>

		<TD valign = 'top'>
<?php
if(isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {

include "kalender.php";
include "vw_kzlOoien.php"; 
	
	 if(empty($_GET['pstschaap'])) 	{	$schaapId = $_POST['txtSchaapId'];  }  else	{ 	$schaapId = $_GET['pstschaap'];  }
	
	 If(empty($_GET['pstwerknr']) && empty($_POST['txtwerknr'])) 				{	$pstwerknr = '';}  
else if(empty($_GET['pstwerknr']))  {$pstwerknr = $_POST['txtwerknr'];} else	{ 	$pstwerknr = $_GET['pstwerknr']; }


/* Declaratie Ras */
$qry_Ras = mysqli_query($db, "
SELECT r.rasId, r.ras
FROM tblRas r
 join tblRasuser ru on (r.rasId = ru.rasId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and r.actief = 1 and ru.actief = 1
ORDER BY r.ras
") or die (mysqli_error($db));

$index = 0; 
while ($rs = mysqli_fetch_array($qry_Ras)) 
{ 
   $rsId[$index] = $rs['rasId']; 
   $rsnum[$index] = $rs['ras'];
   $rsRaak[$index] = $rs['rasId'];
   $index++; 
} 
unset($index); 
/* Einde Declaratie Ras */

/* Declaratie Bestemming */
$qry_Bestemming = mysqli_query($db, "
SELECT r.relId, p.naam
FROM tblPartij p
 join tblRelatie r on (p.partId = r.partId)
WHERE p.lidId = '".mysqli_real_escape_string($db,$lidId)."' and r.relatie = 'deb' and p.actief = 1 and r.actief = 1
ORDER BY p.naam
") or die (mysqli_error($db));

$index = 0; 
while ($bst = mysqli_fetch_array($qry_Bestemming)) 
{ 
   $bstnId[$index] = $bst['relId']; 
   $bstnum[$index] = $bst['naam'];
   $bstRaak[$index] = $bst['relId'];
   $index++; 
} 
unset($index); 
/* Einde Declaratie Bestemming */

// Declaratie HOKNUMMER KEUZE
$qryHokkeuze = mysqli_query($db,"
SELECT hokId, hoknr
FROM tblHok h
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actief = 1
ORDER BY hoknr
") or die (mysqli_error($db));

$index = 0;
while ($hnr = mysqli_fetch_array($qryHokkeuze)) 
{ 
   $hoknId[$index] = $hnr['hokId']; 
   $hoknum[$index] = $hnr['hoknr'];
   $hokRaak[$index] = $hnr['hokId']; 
   $index++; 
} 
unset($index);
// EINDE Declaratie HOKNUMMER  KEUZE

/* Declaratie Reden */
$qryReden = mysqli_query($db, "
SELECT r.redId, r.reden
FROM tblReden r
 join tblRedenuser ru on (r.redId = ru.redId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ru.sterfte = 1
ORDER BY r.reden
") or die (mysqli_error($db));

$index = 0; 
while ($red = mysqli_fetch_array($qryReden)) 
{ 
   $rednId[$index] = $red['redId']; 
   $rednum[$index] = $red['reden'];
   $redRaak[$index] = $red['redId'];
   $index++; 
} 
unset($index); 
/* Einde Declaratie Reden */

		/*****************************
		          OPSLAAN
		*****************************/
if(isset ($_POST['knpSave']))
{
// Wijzigen Levensnummer
$zoek_bestaand_levensnummer = mysqli_query($db,"
SELECT levensnummer
FROM tblSchaap s
WHERE s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
") or die (mysqli_error($db));
	while ( $si = mysqli_fetch_assoc($zoek_bestaand_levensnummer)) { $levnr = $si['levensnummer']; }

if(empty($_POST['txtLevnr']) && !empty($levnr)) { $fout = "Levensnummer is onbekend."; }

else if(!empty($_POST['txtLevnr']) && $_POST['txtLevnr'] <> $levnr) { $txtLevnr = $_POST['txtLevnr']; 

$zoek_op_levensnummer = mysqli_query($db,"
SELECT schaapId
FROM tblSchaap s
WHERE s.levensnummer = '".mysqli_real_escape_string($db,$txtLevnr)."'
") or die (mysqli_error($db));
	while ( $sch = mysqli_fetch_assoc($zoek_op_levensnummer)) { $schaap = $sch['schaapId']; }

if(isset($schaap)) { $fout = "Dit levensnummer bestaat al."; }

else {

$update_tblSchaap = " UPDATE tblSchaap set levensnummer = '".mysqli_real_escape_string($db,$txtLevnr)."' WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."' ";

	mysqli_query($db,$update_tblSchaap) or die(mysqli_error($db));

$update_impRespons = " UPDATE impRespons set levensnummer = '".mysqli_real_escape_string($db,$txtLevnr)."' WHERE levensnummer = '".mysqli_real_escape_string($db,$levnr)."' ";

	mysqli_query($db,$update_impRespons) or die(mysqli_error($db));
}
}
// Einde Wijzigen Levensnummer

// Wijzigen Fokkersnummer
$zoek_fokkernr = mysqli_query($db,"
SELECT fokkernr
FROM tblSchaap
WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
") or die (mysqli_error($db));
	while( $fok = mysqli_fetch_assoc($zoek_fokkernr)) { $dbFokrnr = $fok['fokkernr']; }

If(isset($_POST['txtFokrnr']) && $_POST['txtFokrnr'] <> $dbFokrnr) { 

 if(!empty($_POST['txtFokrnr'])) { $newfokrnr = $_POST['txtFokrnr']; }

	$update_tblSchaap = "UPDATE tblSchaap set fokkernr = ".db_null_input($newfokrnr)." WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."' ";
	
		mysqli_query($db,$update_tblSchaap) or die (mysqli_error($db));
}
// Einde Wijzigen Fokkersnummer

// Wijzigen Halsnummer
if(isset($_POST['kzlKleur'])) { $kzlKleur = $_POST["kzlKleur"]; } 
if(isset($_POST['txtHnr'])) { $txtHnr = $_POST['txtHnr']; }

$dbKleur = ''; $dbHalsnr = '';
$zoek_halsnummer = mysqli_query($db,"
SELECT stalId, kleur, halsnr
FROM tblStal st
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and isnull(st.rel_best)
") or die (mysqli_error($db));
	while ( $hal = mysqli_fetch_assoc($zoek_halsnummer)) { $stalId = $hal['stalId']; $dbKleur = $hal['kleur']; $dbHalsnr = $hal['halsnr']; }
if($kzlKleur <> $dbKleur || $txtHnr <> $dbHalsnr) {

$update_tblStal = "UPDATE tblStal set kleur = ". db_null_input($kzlKleur) .", halsnr = ". db_null_input($txtHnr)." WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."' ";

/*echo $update_tblStal."<br>";*/	mysqli_query($db,$update_tblStal) or die (mysqli_error($db));
}
// Einde Wijzigen Halsnummer

// Wijzigen geslacht
$zoek_geslacht = mysqli_query($db,"
SELECT geslacht
FROM tblSchaap
WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
") or die (mysqli_error($db));
	while( $gs = mysqli_fetch_assoc($zoek_geslacht)) { $dbSekse = $gs['geslacht']; }

if(isset($_POST['kzlSekse']) && $_POST['kzlSekse'] <> $dbSekse) {

	$newsekse = $_POST['kzlSekse']; 

 	$update_Geslacht = "UPDATE tblSchaap set geslacht = '".mysqli_real_escape_string($db,$newsekse)."' WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."' ";

 	mysqli_query($db,$update_Geslacht) or die (mysqli_error($db));
}
// Einde Wijzigen geslacht

// Wijzigen Ras
$zoek_ras = mysqli_query($db,"
SELECT rasId
FROM tblSchaap
WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
") or die (mysqli_error($db));
	while( $ra = mysqli_fetch_assoc($zoek_ras)) { $dbRasId = $ra['rasId']; }

if(isset($_POST['kzlRas']) && $_POST['kzlRas'] <> $dbRasId) { 

$newrasId = $_POST['kzlRas']; 

 	$update_Ras = "UPDATE tblSchaap set rasId = ".db_null_input($newrasId)." WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."' ";


	
		mysqli_query($db,$update_Ras) or die (mysqli_error($db));
}
// Einde Wijzigen Ras

// Invoeren aanwasdatum
if(!empty($_POST['txtGebdm'])){

// Bij verversen pagina kan geboorte reeds bestaan. Daarom controleren op bestaan van geboortedatum
$zoek_geboorte = mysqli_query($db,"
SELECT datum
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE h.actId = 1 and h.skip = 0 and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
") or die (mysqli_error($db));
	while( $zg = mysqli_fetch_assoc($zoek_geboorte)) { $dbGebdm = $zg['datum']; }

if(!isset($dbGebdm)) { 

$zoek_eerste_datum = mysqli_query($db,"
SELECT min(datum) date1, date_format(datum,'%d-%m-%Y') datum1
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE h.skip = 0 and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
") or die (mysqli_error($db));
	while( $zed = mysqli_fetch_assoc($zoek_eerste_datum)) { $dbDate1 = $zed['date1']; $dbDatum1 = $zed['datum1']; }


	$date = date_create($_POST['txtGebdm']); $txtDate = date_format($date,'Y-m-d');

if($dbDate1 < $txtDate) { $fout = "De geboortedatum mag niet na ".$dbDatum1." liggen. Zie ook de historie."; }
else {
$stalId = zoek_stalId_in_stallijst($lidId,$schaapId);

$insert_tblHistorie = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$txtDate)."', actId = 1 ";

/*echo $insert_tblHistorie.'<br>';  ##*/mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));
}

}

} // einde if(!empty($_POST['txtGebdm']))
// Einde Invoeren aanwasdatum


// Wijzigen aanwasdatum
// Alleen tijdens het stalmoment waarneer de speendatum is aangemaakt kan een aanwasdatum worden gewijzigd. Na afvoeren bestaat txtaanw niet meer.
if(!empty($_POST['txtaanw'])){

$date = date_create($_POST['txtaanw']); $txtDate = date_format($date,'Y-m-d');

$zoek_aanwasdatum = mysqli_query($db,"
SELECT hisId, datum
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and h.actId = 3 and h.skip = 0
") or die (mysqli_error($db));
	while ( $oudr = mysqli_fetch_assoc($zoek_aanwasdatum)) { $hisaanw = $oudr['hisId']; $dmaanwas = $oudr['datum']; }

if($txtDate <> $dmaanwas) {

$zoek_nietvoor_datum = mysqli_query($db,"
SELECT max(datum) date
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and 
 actId = 4 and h.skip = 0
") or die (mysqli_error($db));
	while( $nv = mysqli_fetch_assoc($zoek_nietvoor_datum)) { $nietvoor = $nv['date']; } $day = date_create($nietvoor); $nietv = date_format($day,'d-m-Y');
	
$controle_nietna_datum = mysqli_query($db,"
SELECT min(datum) date
From (
	SELECT datum, actie
	FROM tblActie a
	 join tblHistorie h on (a.actId = h.actId)
	 join tblStal st on (st.stalId = h.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and a.af = 1 and h.skip = 0
	
	union
	
	SELECT  min(h.datum) datum, 'Eerste worp' actie
	FROM tblSchaap mdr
	 join tblVolwas v on (mdr.schaapId = v.mdrId)
	 join tblSchaap lam on (v.volwId = lam.volwId)
	 join tblStal st on (st.schaapId = lam.schaapId)
	 join tblHistorie h on (st.stalId = h.stalId and h.actId = 1 and h.skip = 0)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and mdr.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
) datum
") or die (mysqli_error($db));
	while( $nna = mysqli_fetch_assoc($controle_nietna_datum)) { $nietna = $nna['date']; } $day = date_create($nietna); $nietn = date_format($day,'d-m-Y');
	
	if($txtDate < $nietvoor) { $fout = "De aanwasdatum mag niet voor ".$nietv." liggen. "; }
	else if(isset($nietna) && $txtDate > $nietna) { $fout = "De aanwasdatum mag niet na ".$nietn." liggen. "; }
	else {
	$update_tblHistorie = "UPDATE tblHistorie set datum = '".mysqli_real_escape_string($db,$txtDate)."' WHERE hisId = '".mysqli_real_escape_string($db,$hisaanw)."' ";
		mysqli_query($db,$update_tblHistorie) or die (mysqli_error($db));
		}
}
}
// Einde Wijzigen aanwasdatum

// Wijzigen/invoeren moeder en/of vader
$zoek_mdrId = mysqli_query($db,"
SELECT v.volwId, v.mdrId
FROM tblVolwas v
 join tblSchaap s on (v.volwId = s.volwId)
WHERE s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
") or die (mysqli_error($db));
	while( $mdr = mysqli_fetch_assoc($zoek_mdrId)) { $volwId = $mdr['volwId']; $mdr_db = $mdr['mdrId']; }

$zoek_vdrId = mysqli_query($db,"
SELECT v.volwId, v.vdrId
FROM tblVolwas v
 join tblSchaap s on (v.volwId = s.volwId)
WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
") or die (mysqli_error($db));
	while( $vdr = mysqli_fetch_assoc($zoek_vdrId)) { $volwId = $vdr['volwId']; $vdr_db = $vdr['vdrId']; }

if(isset($volwId)) {
// wijzigen moeder
if(!empty($_POST['kzlOoi']) && ((isset($mdr_db) && $_POST['kzlOoi'] <> $mdr_db) || (!isset($mdr_db)) ) ) { $newmdrId = $_POST['kzlOoi'];
	$update_tblVolwas = "
	UPDATE tblVolwas v 
	 join tblSchaap s on (v.volwId = s.volwId)
	set v.mdrId = '".mysqli_real_escape_string($db,$newmdrId)."'
	WHERE s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	";
		mysqli_query($db,$update_tblVolwas) or die (mysqli_error($db));
//$goed = "Keuzelijst moeder van ".$mdr_db." naar ".$_POST['kzlOoi']." bij ".$schaapId;	
}
// Einde wijzigen moeder
// wijzigen vader
if(!empty($_POST['kzlRam']) && ((isset($vdr_db) && $_POST['kzlRam'] <> $vdr_db) || (!isset($vdr_db)) ) ) { $newvdrId = $_POST['kzlRam'];
	$update_tblVolwas = "
	UPDATE tblVolwas v
	 join tblSchaap s on (v.volwId = s.volwId)
	set v.vdrId = '".mysqli_real_escape_string($db,$newvdrId)."'
	WHERE s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	";
		mysqli_query($db,$update_tblVolwas) or die (mysqli_error($db));
//$goed = "Keuzelijst vader van ".$vdr_db." naar ".$_POST['kzlRam']." bij ".$schaapId;	
}
// Einde wijzigen vader

} // Einde if(isset($volwId))
else {
// invoer ouders 
	$newmdrId = $_POST['kzlOoi'];
	$newvdrId = $_POST['kzlRam'];
// invoer moeder en/of vader
	$insert_tblVolwas = "
	INSERT INTO tblVolwas set mdrId = ".db_null_input($newmdrId).", vdrId = ".db_null_input($newvdrId);
/*echo $insert_tblVolwas.'<br>';*/		mysqli_query($db,$insert_tblVolwas) or die (mysqli_error($db));

	$zoek_volwId = mysqli_query($db,"
		SELECT max(volwId) volwId
		FROM tblVolwas
		WHERE ".db_null_filter(mdrId, $newmdrId) . " and " . db_null_filter(vdrId, $newvdrId) . "
	") or die (mysqli_error($db));
		while ($vw = mysqli_fetch_assoc($zoek_volwId)) { $volwId = $vw['volwId']; }

// Einde invoer moeder en/of vader



if(isset($volwId)) { // $volwId hoeft niet te bestaan als dier geen ouders heeft en kzlOoi is leeg en kzlRam is leeg.
$invoer_volwId_tblSchaap = "UPDATE tblSchaap set volwId = '".mysqli_real_escape_string($db,$volwId)."' WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."' ";
/*echo $invoer_volwId_tblSchaap.'<br>';*/		mysqli_query($db,$invoer_volwId_tblSchaap) or die (mysqli_error($db));
}
// Einde invoer ouders 
}
// Einde Wijzigen/invoeren moeder en/of vader



// Wijzigen speendatum
if(!empty($_POST['txtSpndm'])){

$spday = date_create($_POST['txtSpndm']); $newdmspeen = date_format($spday,'Y-m-d');

$zoek_speendm = mysqli_query($db,"
SELECT hisId, datum
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and h.actId = 4 and h.skip = 0
") or die (mysqli_error($db));
	while( $spn = mysqli_fetch_assoc($zoek_speendm)) { $hisspeen = $spn['hisId']; $dmspeen = $spn['datum'];}

if($newdmspeen <> $dmspeen) {

$zoek_nietvoor_datum = mysqli_query($db,"
SELECT max(datum) date
From (
	SELECT datum
	FROM tblHistorie h
	 join tblStal st on (st.stalId = h.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and 
	 (actId = 1 or actId = 2) and h.skip = 0

	union

	SELECT datum
	FROM tblHistorie h
	 join tblBezet b on (h.hisId = b.hisId)
	 join tblPeriode p on (p.periId = b.periId)
	 join tblStal st on (st.stalId = h.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and p.doelId = 1 and (h.actId = 5 or h.actId = 6) and h.skip = 0
) datum
") or die (mysqli_error($db));
	while( $nv = mysqli_fetch_assoc($zoek_nietvoor_datum)) { $nietvoor = $nv['date']; } $day = date_create($nietvoor); $nietv = date_format($day,'d-m-Y');
	
$controle_nietna_datum = mysqli_query($db,"
SELECT min(datum) date
From (
	SELECT datum
	FROM tblHistorie h
	 join tblStal st on (st.stalId = h.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and (h.actId = 3 or h.actId = 10 or h.actId = 12 or h.actId = 14) and h.skip = 0

	union

	SELECT datum
	FROM tblHistorie h
	 join tblBezet b on (h.hisId = b.hisId)
	 join tblPeriode p on (p.periId = b.periId)
	 join tblStal st on (st.stalId = h.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and p.doelId = 2 and (h.actId = 5 or h.actId = 6) and h.skip = 0
) datum
") or die (mysqli_error($db));
	while( $nna = mysqli_fetch_assoc($controle_nietna_datum)) { $nietna = $nna['date']; } $day = date_create($nietna); $nietn = date_format($day,'d-m-Y');
	
	if($newdmspeen < $nietvoor) { $fout = "De speendatum mag niet voor ".$nietv." liggen. "; }
	else if(!empty($nietna) && $newdmspeen > $nietna) { $fout = "De speendatum mag niet na ".$nietn." liggen. "; }
	else {
	$update_tblHistorie = "
	UPDATE tblHistorie h set h.datum = '".mysqli_real_escape_string($db,$newdmspeen)."'	WHERE hisId = '".mysqli_real_escape_string($db,$hisspeen)."' ";
		mysqli_query($db,$update_tblHistorie) or die (mysqli_error($db));
		}
}
}
// Einde Wijzigen speendatum


// Wijzigen speengewicht
if(!empty($_POST['txtSpnkg'])) {

$zoek_speenkg = mysqli_query($db,"
SELECT hisId, kg speenkg
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and h.actId = 4 and h.skip = 0
") or die (mysqli_error($db));
	while( $spn = mysqli_fetch_assoc($zoek_speenkg)) { $hisspeen = $spn['hisId']; $speenkg = $spn['speenkg'];}

if($_POST['txtSpnkg'] <> $speenkg) { $newspeenkg = $_POST['txtSpnkg'];

	$update_tblHistorie = "
	UPDATE tblHistorie h set h.kg = '".mysqli_real_escape_string($db,$newspeenkg)."' WHERE hisId = '".mysqli_real_escape_string($db,$hisspeen)."' ";
		mysqli_query($db,$update_tblHistorie) or die (mysqli_error($db));
}
}
// Einde Wijzigen speengewicht

// Wijzigen bestemming
$zoek_relId = mysqli_query($db,"
SELECT st.stalId, st.rel_best
FROM (
	SELECT max(stalId) stalId
	FROM tblStal
	WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
 ) mst
 join tblStal st on (mst.stalId = st.stalId)
") or die (mysqli_error($db));
	while( $rel = mysqli_fetch_assoc($zoek_relId)) { $stalId = $rel['stalId']; $best_db = $rel['rel_best']; }

if(!empty($_POST['kzlBestupd']) && $_POST['kzlBestupd'] <> $best_db) { $newrel_best = $_POST['kzlBestupd'];

$update_tblStal = "
UPDATE tblStal set rel_best = '".mysqli_real_escape_string($db,$newrel_best)."' WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."'
";
	mysqli_query($db,$update_tblStal) or die (mysqli_error($db));
}
// Einde Wijzigen bestemming

// Wijzigen afvoerdatum
if(!empty($_POST['txtAfvdm'])) {

$afvday = date_create($_POST['txtAfvdm']); $newdmafvoer = date_format($afvday,'Y-m-d');

$zoek_afvoerdm = mysqli_query($db,"
SELECT hisId, datum
FROM tblActie a
 join tblHistorie h on (a.actId = h.actId)
 join tblStal st on (h.stalId = st.stalId)
WHERE st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and a.af = 1 and h.skip = 0
") or die (mysqli_error($db));
	while( $afv = mysqli_fetch_assoc($zoek_afvoerdm)) { $hisafv = $afv['hisId']; $dmafvoer = $afv['datum']; }

if($newdmafvoer <> $dmafvoer) {

$zoek_nietvoor_datum = mysqli_query($db,"
SELECT max(datum) date
From (
	SELECT h.datum, a.actie
	FROM tblActie a 
	 join tblHistorie h on (a.actId = h.actId)
	 join tblStal st on (st.stalId = h.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and 
	 a.af != 1 and h.skip = 0
	 
	union

	SELECT max(h.datum) datum, 'Laatste worp' actie
	FROM tblSchaap mdr
	 join tblVolwas v on (mdr.schaapId = v.mdrId)
	 join tblSchaap lam on (v.volwId = lam.volwId)
	 join tblStal st on (st.schaapId = lam.schaapId)
	 join tblHistorie h on (st.stalId = h.stalId and h.actId = 1 and h.skip = 0)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and mdr.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	GROUP BY mdr.schaapId, h.actId
	HAVING (max(h.datum) > min(h.datum))
) datum
") or die (mysqli_error($db));
	while( $nv = mysqli_fetch_assoc($zoek_nietvoor_datum)) { $nietvoor = $nv['date']; } $day = date_create($nietvoor); $nietv = date_format($day,'d-m-Y');

	if($newdmafvoer < $nietvoor) { $fout = "De afvoerdatum mag niet voor ".$nietv." liggen."; $zetdatumterug = 'zetdatumterug'; }
	else {
	$update_tblHistorie = "
	UPDATE tblHistorie h set h.datum = '".mysqli_real_escape_string($db,$newdmafvoer)."' WHERE hisId = '".mysqli_real_escape_string($db,$hisafv)."' ";
		mysqli_query($db,$update_tblHistorie) or die (mysqli_error($db));
	}
}
}
// Einde Wijzigen afvoerdatum

// Wijzigen afvoergewicht
$zoek_afvoerkg = mysqli_query($db,"
SELECT kg
FROM tblActie a
 join tblHistorie h on (a.actId = h.actId)
 join tblStal st on (h.stalId = st.stalId)
WHERE st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and a.af = 1 and h.skip = 0 ") or die (mysqli_error($db));
	while( $afv = mysqli_fetch_assoc($zoek_afvoerkg)) { $afvoerkg = $afv['kg']; }

if(!empty($_POST['txtAfvkg']) && $_POST['txtAfvkg'] <> $afvoerkg) { $newafvoerkg = $_POST['txtAfvkg'];

	$update_tblHistorie = "
	UPDATE tblActie a
	 join tblHistorie h on (a.actId = h.actId)
	 join tblStal st on (h.stalId = st.stalId)
	set h.kg = '".mysqli_real_escape_string($db,$newafvoerkg)."'
	WHERE st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and a.af = 1 and h.skip = 0 ";
		mysqli_query($db,$update_tblHistorie) or die (mysqli_error($db));
//$goed = "Afvoerkg van ".$afvoerkg." naar ".$_POST['txtAfvkg']." bij ".$schaapId;	
}
// Einde Wijzigen afvoergewicht

// Wijzigen afvoerreden
$zoek_reden = mysqli_query($db,"
SELECT redId
FROM tblSchaap
WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
") or die (mysqli_error($db));
	while( $red = mysqli_fetch_assoc($zoek_reden)) { $red_db = $red['redId']; }

If(isset($_POST['kzlReden']) && $_POST['kzlReden'] <> $red_db) {  $newreden = $_POST['kzlReden']; 

	$update_tblSchaap = "UPDATE tblSchaap set redId = ".db_null_input($newreden)." WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."' ";
		mysqli_query($db,$update_tblSchaap) or die (mysqli_error($db));
}
// Einde Wijzigen afvoerreden

/*****************************************/
// VELDEN m.b.t. AFVOER AANWEZIGE schapen. Naam radiobutton is bij aanwezige schapen radAfv i.p.v. radHerst.
/*****************************************/
if( !isset($_POST['radAfv']) && ((isset($_POST['txtEinddm']) && !empty($_POST['txtEinddm'])) || !empty($_POST['kzlBstm'])) ) { $fout = "Er is geen keuze afvoer gemaakt."; }
else if(isset($_POST['radAfv']) && empty($_POST['txtEinddm'])) { $fout = "De afvoerdatum is onbekend."; }
else if(isset($_POST['radAfv']) && ($_POST['radAfv'] == 10 || $_POST['radAfv'] == 12 || $_POST['radAfv'] == 13) && empty($_POST['kzlBstm'])) { $fout = "De bestemming is onbekend."; }
else if(isset($_POST['radAfv'])) { // Bij schapen zonder levensnummer bestaat dit niet.

$date = date_create($_POST['txtEinddm']); $dmafv = date_format($date, 'Y-m-d');

$stalId = zoek_stalId_in_stallijst($lidId,$schaapId);
	
// Is er een schaap aanwezig en eerder al afgevoerd met een definitieve melding ? In dat geval wordt geïnformeerd dat er geen melding aan de RVO wordt aangemaakt 
$zoek_definitieve_afvoermelding = mysqli_query($db,"
SELECT count(h.hisId) defat
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."' and h.skip = 1 and rq.def = 1 and m.skip = 0
") or die (mysqli_error($db));
	while ( $zda = mysqli_fetch_assoc($zoek_definitieve_afvoermelding)) { $def_aant = $zda['defat']; }
	
	
// registratie aanwas
  if(isset($_POST['radAfv']) && $_POST['radAfv'] == 3) {

$zoek_nietvoor_datum = mysqli_query($db,"
SELECT max(datum) date
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and 
 (actId = 4 or actId = 5 or actId = 6) and h.skip = 0
") or die (mysqli_error($db));
	while( $nv = mysqli_fetch_assoc($zoek_nietvoor_datum)) { $nietvoor = $nv['date']; } $day = date_create($nietvoor); $nietv = date_format($day,'d-m-Y');
	
$zoek_nietna_datum = mysqli_query($db,"
SELECT min(datum) date
From (
	SELECT datum, actie
	FROM tblActie a
	 join tblHistorie h on (a.actId = h.actId)
	 join tblStal st on (st.stalId = h.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and a.af = 1 and h.skip = 0
	
	union
	
	SELECT  min(h.datum) datum, 'Eerste worp' actie
	FROM tblSchaap mdr
	 join tblVolwas v on (mdr.schaapId = v.mdrId)
	 join tblSchaap lam on (v.volwId = lam.volwId)
	 join tblStal st on (st.schaapId = lam.schaapId)
	 join tblHistorie h on (st.stalId = h.stalId and h.actId = 1 and h.skip = 0)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and mdr.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
) datum
") or die (mysqli_error($db));
	while( $nna = mysqli_fetch_assoc($zoek_nietna_datum)) { $nietna = $nna['date']; } if(!empty($nietna)) { $day = date_create($nietna); $nietn = date_format($day,'d-m-Y'); } //LET OP : $nietna kan NULL zijn !! 

	   if($dmafv < $nietvoor) { $fout = "De aanwasdatum mag niet voor ".$nietv." liggen."; }
  else if(!empty($nietna) && $dmafv > $nietna) { $fout = "De aanwasdatum mag niet na ".$nietn." liggen."; }
  else {
  $insert_tblHistorie = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$dmafv)."', actId = 3 ";
		mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));
 
/* if(!empty($_POST['kzlHok'])) {
$hokId_aanw = $_POST['kzlHok'];
$zoek_hisId = mysqli_query($db," SELECT hisId FROM tblHistorie WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."' and actId = 3 ") or die (mysqli_error($db));
	while( $ha = mysqli_fetch_assoc($zoek_hisId)) { $hisId_a = $ha['hisId']; }
 
  $insert_tblBezet = "INSERT INTO tblBezet set hisId = '".mysqli_real_escape_string($db,$hisId_a)."', hokId = '".mysqli_real_escape_string($db,$hokId_aanw)."' ";
		mysqli_query($db,$insert_tblBezet) or die (mysqli_error($db));
 }*/
 
	$zoek_geslacht = mysqli_query($db,"SELECT geslacht FROM tblSchaap WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."'	") or die(mysqli_error($db));
	 while( $gsl = mysqli_fetch_assoc($zoek_geslacht)) { $mn_vr = $gsl['geslacht']; } if($mn_vr == 'ooi') { $parent = 'moederdier'; } else if($mn_vr == 'ram') { $parent = 'vaderdier'; }
	
	$goed = "Het lam is gewijzigd naar een ".$parent.". ";
  }
  }
// Einde registratie aanwas

// registratie uitscharen, afvoeren en verkopen  (afvoeren kan door gebruikers met alleen module melden)
  if(isset($_POST['radAfv']) && ($_POST['radAfv'] == 10 || $_POST['radAfv'] == 12 || $_POST['radAfv'] == 13)) {

$zoek_nietvoor_datum = mysqli_query($db,"
SELECT max(datum) date
From (
	SELECT h.datum, a.actie
	FROM tblActie a 
	 join tblHistorie h on (a.actId = h.actId)
	 join tblStal st on (st.stalId = h.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and 
	 a.af != 1 and h.skip = 0
	 
	union

	SELECT max(h.datum) datum, 'Laatste worp' actie
	FROM tblSchaap mdr
	 join tblVolwas v on (mdr.schaapId = v.mdrId)
	 join tblSchaap lam on (v.volwId = lam.volwId)
	 join tblStal st on (st.schaapId = lam.schaapId)
	 join tblHistorie h on (st.stalId = h.stalId and h.actId = 1 and h.skip = 0)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and mdr.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	GROUP BY mdr.schaapId, h.actId
	HAVING (max(h.datum) > min(h.datum))
) datum
") or die (mysqli_error($db));
	while( $nv = mysqli_fetch_assoc($zoek_nietvoor_datum)) { $nietvoor = $nv['date']; } $day = date_create($nietvoor); $nietv = date_format($day,'d-m-Y');
  
  if($dmafv < $nietvoor) { $fout = "De afvoerdatum mag niet voor ".$nietv." liggen."; }
  else {
  $insert_tblHistorie = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$dmafv)."', actId = '".mysqli_real_escape_string($db,$_POST['radAfv'])."' ";
		mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));
		
  $update_tblStal = "UPDATE tblStal set rel_best = '".mysqli_real_escape_string($db,$_POST['kzlBstm'])."' WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."' ";
		mysqli_query($db,$update_tblStal) or die (mysqli_error($db));

// Aanmaken melding bij alleen allereerste afvoer
if($modmeld == 1) { // if(isset($_POST['radAfv']) ... heeft er al voor gezocht dat het een aanwezig schaap betreft 

if(!isset($def_aant) || $def_aant == 0) { // Er mag niet eerder een definitieve afvoer zijn gemeld die later is hersteld

$hisId = zoek_hisId_stal($stalId,$_POST['radAfv']);

/*Per 2-9-2023 vervangen door een functie uit basisfuncties.php 
$zoek_hisId = mysqli_query($db,"
SELECT hisId
FROM tblHistorie
WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."' and actId = '".mysqli_real_escape_string($db,$_POST['radAfv'])."'
") or die (mysqli_error($db));
	while ( $hs = mysqli_fetch_assoc($zoek_hisId)) { $hisId = $hs['hisId']; }*/

//$reqst_file = 'UpdSchaap.php_afvoer';
$Melding = 'AFV';
include "maak_request_func.php";
include "maak_request.php";
}
}
// Einde Aanmaken melding bij alleen allereerste afvoer

	$goed = "Het schaap is afgevoerd."; 
	}
	}
// Einde registratie uitscharen en verkopen
	
// registratie overleden of vermist
  if(isset($_POST['radAfv']) && ($_POST['radAfv'] == 14 || $_POST['radAfv'] == 20)) {

$zoek_nietvoor_datum = mysqli_query($db,"
SELECT max(datum) date
From (
	SELECT h.datum, a.actie
	FROM tblActie a 
	 join tblHistorie h on (a.actId = h.actId)
	 join tblStal st on (st.stalId = h.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and 
	 a.af != 1 and h.skip = 0
	 
	union

	SELECT max(h.datum) datum, 'Laatste worp' actie
	FROM tblSchaap mdr
	 join tblVolwas v on (mdr.schaapId = v.mdrId)
	 join tblSchaap lam on (v.volwId = lam.volwId)
	 join tblStal st on (st.schaapId = lam.schaapId)
	 join tblHistorie h on (st.stalId = h.stalId and h.actId = 1 and h.skip = 0)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and mdr.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	GROUP BY mdr.schaapId, h.actId
	HAVING (max(h.datum) > min(h.datum))
) datum
") or die (mysqli_error($db));
	while( $nv = mysqli_fetch_assoc($zoek_nietvoor_datum)) { $nietvoor = $nv['date']; } $day = date_create($nietvoor); $nietv = date_format($day,'d-m-Y');
  
  if($dmafv < $nietvoor) { $fout = "De afvoerdatum mag niet voor ".$nietv." liggen."; }
  else {
  $insert_tblHistorie = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$dmafv)."', actId = '".mysqli_real_escape_string($db,$_POST['radAfv'])."' ";
		mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));

if($_POST['radAfv'] == 14) {	// Alleen bij overleden
  $update_tblStal = "UPDATE tblStal set rel_best = '".mysqli_real_escape_string($db,$rendac_Id)."' WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."' ";
		mysqli_query($db,$update_tblStal) or die (mysqli_error($db));

	if(!empty($_POST['kzlAfvred'])) {
  $update_tblSchaap = "UPDATE tblSchaap set redId = '".mysqli_real_escape_string($db,$_POST['kzlAfvred'])."' WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."' ";
		mysqli_query($db,$update_tblSchaap) or die (mysqli_error($db));	
	}
	
// Aanmaken melding bij eerste afvoer of een schaap teruggeplaatst op stal waarvan nog geen definitieve melding bestaat m.b.t. afvoer
if($modmeld == 1) { // if(isset($_POST['radAfv']) ... heeft er al voor gezocht dat het een aanwezig schaap betreft 

if(!isset($def_aant) || $def_aant == 0) { // Er mag niet eerder een definitieve afvoer zijn gemeld die later is hersteld

$hisId = zoek_hisId_stal($stalId,$_POST['radAfv']);

/*Per 2-9-2023 vervangen door een functie uit basisfuncties.php 
$zoek_hisId = mysqli_query($db,"
SELECT hisId
FROM tblHistorie
WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."' and actId = '".mysqli_real_escape_string($db,$_POST['radAfv'])."'
") or die (mysqli_error($db));
	while ( $hs = mysqli_fetch_assoc($zoek_hisId)) { $hisId = $hs['hisId']; }*/

//$reqst_file = 'UpdSchaap.php_afvoer';
$Melding = 'DOO';
include "maak_request_func.php";
include "maak_request.php";
}
}
// Einde Aanmaken melding bij alleen allereerste afvoer

}	// Einde Alleen bij overleden


if($_POST['radAfv'] == 20) {	// Alleen bij vermist

$zoek_crediteur_vermist = mysqli_query($db,"
SELECT relId
FROM tblPartij p
 join tblRelatie r on (r.partId = p.partId)
WHERE p.lidId = ".mysqli_real_escape_string($db,$lidId)." and p.naam = 'Vermist'
") or die (mysqli_error($db));

	while($zcv = mysqli_fetch_assoc($zoek_crediteur_vermist))
	{ $cred_vermist = $zcv['relId']; }

  $update_tblStal = "UPDATE tblStal set rel_best = '".mysqli_real_escape_string($db,$cred_vermist)."' WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."' ";
		mysqli_query($db,$update_tblStal) or die (mysqli_error($db));
		}

	$goed = "Het schaap is afgevoerd.";
	}
	}
// Einde registratie overleden of vermist	
}

/***********************************************/
// Einde VELDEN m.b.t. AFVOER AANWEZIGE schapen
/***********************************************/


// Moeder aan verblijf toekennen
if(isset($_POST['kzlHokOoi']) && !empty($_POST['kzlHokOoi'])) {

if(empty($_POST['txtHokOoiDm'])) { $fout = "De datum dat het moederdier in het verblijf is geplaatst ontbreekt. "; }
else {

$zoek_datum_na = mysqli_query($db,"
SELECT max(datum) date, date_format(max(datum),'%d-%m-%Y') datum
FROM (
 	SELECT h.datum
 	FROM tblHistorie h
 	 join tblStal st on (h.stalId = st.stalId)
 	WHERE st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and actId = 1 and skip = 0
  union
 	SELECT max(h.datum) dmaank
 	FROM tblHistorie h
 	 join tblStal st on (h.stalId = st.stalId)
 	WHERE st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and actId = 2 and skip = 0
  union
  	SELECT h.datum
 	FROM tblHistorie h
 	 join tblStal st on (h.stalId = st.stalId)
 	WHERE st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and actId = 4 and skip = 0
  
) dm_na	
") or die (mysqli_error($db));

	while ( $na = mysqli_fetch_assoc($zoek_datum_na)) { $dmOoiHokNa = $na['date']; $OoiHokNadm = $na['datum']; }

$zoek_datum_vanaf = mysqli_query($db,"
SELECT max(h.datum) date, date_format(max(h.datum),'%d-%m-%Y') datum
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and (actId = 3 or actId = 7) and skip = 0
") or die (mysqli_error($db));

	while ( $vaf = mysqli_fetch_assoc($zoek_datum_vanaf)) { $dmOoiHokVanaf = $vaf['date']; $OoiHokVanafdm = $vaf['datum']; }


$invoerdatum = date_create($_POST['txtHokOoiDm']); $invoerdate = date_format($invoerdatum,'Y-m-d');

if(isset($dmOoiHokNa) && $invoerdate < $dmOoiHokNa) { $fout = "De datum mag niet voor ".$OoiHokNadm." liggen." ; }
elseif (isset($dmOoiHokVanaf) && $invoerdate < $dmOoiHokVanaf) {  $fout = "De datum mag niet liggen voor ".$OoiHokVanafdm ; }
else {
	$hokOoi = $_POST['kzlHokOoi'];
	$hkOoiDay = date_create($_POST['txtHokOoiDm']); $invoerdate = date_format($hkOoiDay,'Y-m-d');

	$insert_tblHistorie = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$invoerdate)."', actId = 6";

	mysqli_query($db,$insert_tblHistorie) or die(mysqli_error($db));

$his_hokin = zoek_max_hisId_stal($stalId,6);

/*	$zoek_hisId = mysqli_query($db,"
	SELECT max(hisId) hisId FROM tblHistorie WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."' and actId = 6
	") or die(mysqli_error($db));
	while( $in = mysqli_fetch_assoc($zoek_hisId)) { $his_hokin = $in['hisId']; }*/

	$insert_tblBezet = "INSERT INTO tblBezet set hisId = '".mysqli_real_escape_string($db,$his_hokin)."', hokId = '".mysqli_real_escape_string($db,$hokOoi). "' " ;

	mysqli_query($db,$insert_tblBezet) or die(mysqli_error($db));

	}

}
}
// Einde Moeder aan verblijf toekennen

// Terug van uitscharen registreren

if(isset($_POST['txtTerugdm'])) {

$last_stalId = zoek_max_stalId($lidId,$schaapId);

$zoek_uitschaardatum = mysqli_query($db,"
SELECT datum date
FROM tblHistorie
WHERE stalId = '".mysqli_real_escape_string($db,$last_stalId)."' and actId = 10
") or die(mysqli_error($db));
	while( $zu = mysqli_fetch_assoc($zoek_uitschaardatum)) { $dmUitsch = $zu['date']; }

$terugdatum = date_create($_POST['txtTerugdm']); $terugdate = date_format($terugdatum,'Y-m-d');

	if($terugdate < $dmUitsch) { $fout = "De datum mag niet voor de datum van uitscharen liggen."; }
	else {

$zoek_bestemming = mysqli_query($db,"
SELECT r.partId
FROM tblStal st
 join tblRelatie r on (st.rel_best = r.relId)
WHERE st.stalId = '".mysqli_real_escape_string($db,$last_stalId)."'
") or die(mysqli_error($db));
	while( $zb = mysqli_fetch_assoc($zoek_bestemming)) { $partId = $zb['partId']; }

$zoek_herkomst = mysqli_query($db,"
SELECT relId
FROM tblRelatie
WHERE partId = '".mysqli_real_escape_string($db,$partId)."' and relatie = 'cred'
") or die(mysqli_error($db));
	while( $zh = mysqli_fetch_assoc($zoek_herkomst)) { $rel_herk = $zh['relId']; }

if(!isset($rel_herk)) { $fout = "Het bedrijf van herkomst bestaat nog niet als crediteur. Maak deze eerst aan op de pagina Relaties. "; }
else {
$insert_tblStal = "INSERT INTO tblStal set lidId = '".mysqli_real_escape_string($db,$lidId)."', schaapId = '".mysqli_real_escape_string($db,$schaapId)."', rel_herk = '".mysqli_real_escape_string($db,$rel_herk)."' ";

	mysqli_query($db,$insert_tblStal) or die(mysqli_error($db));

$new_stalId = zoek_max_stalId($lidId,$schaapId);

if($new_stalId > $last_stalId) { // extra controle if($new_stalId > $last_stalId)

$insert_tblHistorie = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$new_stalId)."', datum = '".mysqli_real_escape_string($db,$terugdate)."', actId = 11";

	mysqli_query($db,$insert_tblHistorie) or die(mysqli_error($db));
}		

// Aanmaken melding bij terug van uitscharen
if($modmeld == 1) {  

$hisId = zoek_hisId_stal($new_stalId,11);

$Melding = 'AAN';
include "maak_request_func.php";
include "maak_request.php";

}
// Einde Aanmaken melding bij terug van uitscharen

} // Einde else van if(!isset($rel_herk))

		} // einde else van if($terugdate < $dmUitsch)

} // Einde if(isset($_POST['txtTerugdm']))

// Einde Terug van uitscharen registreren

} // EINDE if(isset ($_POST['knpSave']))
		/*****************************
		      EINDE  OPSLAAN
		*****************************/

$show = "
SELECT stm.stalId, st.kleur, st.halsnr hnr, s.levensnummer, date_format(hg.datum,'%d-%m-%Y') gebdm, hg.kg gebkg, s.rasId, s.geslacht, date_format(hs.datum,'%d-%m-%Y') speendm, hs.kg speenkg, ouder.datum dmaanw, date_format(ouder.datum,'%d-%m-%Y') aanwdm, 
mdr.schaapId mdrId, right(mdr.levensnummer,$Karwerk) werknr_ooi, vdr.schaapId vdrId, right(vdr.levensnummer,$Karwerk) werknr_ram,
s.momId, s.redId,
b.bezId, ho.hoknr hoknr_lst, h_in.datum dmHokIn, date_format(h_in.datum,'%d-%m-%Y') hokInDm, p.periId, p.dmafsluit, 
st.rel_best

FROM tblSchaap s
 join (
	SELECT max(stalId) stalId, schaapId
	FROM tblStal st
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	GROUP BY schaapId
 ) stm on (stm.schaapId = s.schaapId)
 join tblStal st on (stm.stalId = st.stalId)

 left join (
	SELECT st.schaapId, datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) ouder on (ouder.schaapId = s.schaapId)

 left join tblVolwas vm on (vm.volwId = s.volwId)
 left join tblSchaap mdr on (mdr.schaapId = vm.mdrId)
 left join tblVolwas vv on (vv.volwId = s.volwId)
 left join tblSchaap vdr on (vdr.schaapId = vv.vdrId)

 left join (
 	SELECT st.schaapId, h.datum, h.kg
 	FROM tblHistorie h
 	 join tblStal st on (st.stalId = h.stalId)
 	WHERE actId = 1 and h.skip = 0
 ) hg on (s.schaapId = hg.schaapId)

 left join (
 	SELECT st.schaapId, h.datum, h.kg
 	FROM tblHistorie h
 	 join tblStal st on (st.stalId = h.stalId)
 	WHERE actId = 4 and h.skip = 0
 ) hs on (s.schaapId = hs.schaapId)

 left join (
	SELECT h.stalId, h.hisId, h.datum, h.kg, a.actie
	FROM tblHistorie h
	 join tblActie a on (a.actId = h.actId)
	 join tblStal st on (h.stalId = st.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.skip = 0 and a.af = 1 and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
 ) haf on (haf.stalId = stm.stalId)


 left join (
	SELECT max(bezId) bezId, st.stalId
	FROM tblBezet b
	 join tblHistorie h on (h.hisId = b.hisId)
	 join tblStal st on (st.stalId = h.stalId)
	WHERE st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and h.skip = 0
	GROUP BY stalId
 ) bm on (bm.stalId = stm.stalId)
 left join tblBezet b on (bm.bezId = b.bezId)
 left join tblHistorie h_in on (h_in.hisId = b.hisId)
 left join tblHok ho on (ho.hokId = b.hokId)
 left join tblPeriode p on (b.periId = p.periId)


 left join (
	SELECT b.bezId, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	GROUP BY b.bezId
 ) uit on (uit.bezId = bm.bezId)

WHERE st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'

ORDER BY right(s.levensnummer,$Karwerk)
";
//echo 'query $show = '.$show.'<br>';
$show = mysqli_query($db,$show) or die (mysqli_error($db));	

	while($record=mysqli_fetch_array($show))
	{	
			
	$stalId = $record['stalId']; // Laatste stalId om afvoer te herstellen. Ook tbv bepalen een na laatste hisId bij controlevelden. Er wordt dus geen rekening gehouden met eerdere historie op andere bedrijven.
	$levnr = $record['levensnummer'];
	$kleur = $record['kleur'];
	$hnr = $record['hnr'];
	$gebdm = $record['gebdm']; // Geboortedatum uit historie van laatste stalId
	$kg = $record['gebkg'];
	$rasId = $record['rasId'];
	$sekse = $record['geslacht'];
	$spndm = $record['speendm']; // Speendatum uit historie van laatste stalId
	$spnkg = $record['speenkg'];
	$dmaanw = $record['dmaanw']; if(isset($dmaanw)) { if($sekse == 'ooi') {$fase = 'moederdier'; } else if($sekse == 'ram') { $fase = 'vaderdier';} } 
					else { $fase = 'lam';}
	$aanwdm = $record['aanwdm']; // Aanwasdatum uit de hele historie van het schaap

	$mdr_db = $record['mdrId'];
	$mdr = $record['werknr_ooi'];
	$vdr_db = $record['vdrId'];
	$vdr = $record['werknr_ram'];
	
	$momId = $record['momId']; // tbv tonen betreffende uitvalmoment in keuzelijst
	$red_db = $record['redId']; // tbv tonen betreffende uitvalmoment in keuzelijst

	$bezId = $record['bezId']; // Het bezId van schaap nu in hok (t.b.v. zit moederdier al in een verblijf ja of nee)
	$hok = $record['hoknr_lst']; // Laatste verblijf. Kan huidig verblijf zijn of waar naar kan worden hersteld
	$dmHokIn = $record['dmHokIn']; 
	$hokInDm = $record['hokInDm']; 
	$periId = $record['periId']; // Om te achterhalen of het hok van deze periode nog actief is
	$dmafsl = $record['dmafsluit']; // Afsluitdatum van de periode waar het schaap voor het laatst in heeft gezeten
	
	$relId = $record['rel_best']; // tbv tonen betreffende afvoerbestemming in beuzelijst	
	

	}	


// Is het dier afgevoerd
$zoek_laatste_hisId  = mysqli_query($db,"
SELECT max(hisId) hisId
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
") or die (mysqli_error($db));	

	while($zlh = mysqli_fetch_array($zoek_laatste_hisId))
	{ $maxhis = $zlh['hisId']; }

$zoek_afgevoerd = mysqli_query($db,"
SELECT h.hisId afvhisId, date_format(h.datum,'%d-%m-%Y') afvoerdm, h.kg afvoerkg, h.actId, a.actie, lower(a.actie) status
FROM tblHistorie h
 join tblActie a on (h.actId = a.actId)
WHERE hisId = '".mysqli_real_escape_string($db,$maxhis)."' and a.af = 1

") or die (mysqli_error($db));	

	while($za = mysqli_fetch_array($zoek_afgevoerd))
	{ 
	$afvhis = $za['afvhisId']; // Nodig tijdens bijwerken van afvoer en bepalen van een na laatste hisId
	$afvdm = $za['afvoerdm'];
	$afvkg = $za['afvoerkg'];
	$actId_afv = $za['actId'];
	$Actie_afv = $za['actie'];
	$actie_afv = $za['status'];
	}
// Einde Is het dier afgevoerd

// Zit het dier in een verblijf
if(!isset($afvhis)) {

$zoek_laatste_verblijf = mysqli_query($db,"
SELECT max(h.hisId) hisId
FROM tblHistorie h
 join tblBezet b on (b.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
") or die (mysqli_error($db));	

	while($zlv = mysqli_fetch_array($zoek_laatste_verblijf))
	{
		$lst_bezet = $zlv['hisId'];
	}

// zoek of het dier uit het verblijf is gehaald na laatste verblijf
$zoek_dier_uit_verblijf = mysqli_query($db,"
SELECT h.actId
FROM tblHistorie h
 join tblActie a on (h.actId = a.actId)
 join tblStal st on (h.stalId = st.stalId)
WHERE hisId > '".mysqli_real_escape_string($db,$lst_bezet)."' and uit = 1 and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
") or die (mysqli_error($db));	

	while($zduv = mysqli_fetch_array($zoek_dier_uit_verblijf))
	{ 
		$actId_uit = $zduv['actId']; 
	}

} // einde if(!isset($afvhis))
// Einde Zit het dier in een verblijf

if(isset($_POST['knpUitHok'])) {

 if(empty($_POST['txtHokOoiDm'])) { $fout = "Datum dat het ".$fase." ".strtolower($hok)." verlaat is onbekend."; }
 else {

	$invoerdatum = date_create($_POST['txtHokOoiDm']); $invoerdate = date_format($invoerdatum,'Y-m-d');

	if($invoerdate < $dmHokIn) { $fout = "De datum mag niet voor ".$hokInDm." liggen."; }
	else {

	$insert_tblHistorie = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$invoerdate)."', actId = 7";

	mysqli_query($db,$insert_tblHistorie) or die(mysqli_error($db));
		}
  }
}

/* Declaratie Moment */
// voorwaarde om keuzelijst te beperken
	 if(!isset($levnr)) { $where_momId = " (m.momId = 1 or m.momId = 2 or m.momId = 3) "; }
else if(isset($levnr) && !isset($spndm)) { $where_momId = " m.momId = 4 "; }
else if(isset($spndm) && !isset($dmaanw)) { $where_momId = " m.momId = 5 "; }
else if(isset($dmaanw)) { $where_momId = " m.momId = 6 "; }
else { $where_momId = " m.momId > 3 "; }
// einde voorwaarde om keuzelijst te beperken

$qryMoment = mysqli_query($db, "
SELECT m.momId, m.moment
FROM tblMoment m
 join tblMomentuser mu on (m.momId = mu.momId)
WHERE '" .mysqli_real_escape_string($db,$where_momId). "' and mu.lidId = '".mysqli_real_escape_string($db,$lidId)."' and m.actief = 1 and mu.actief = 1

union

SELECT m.momId, m.moment
FROM tblMoment m
 join tblSchaap s on (m.momId = s.momId)
WHERE s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'

ORDER BY momId") or die (mysqli_error($db));

$index = 0; 
while ($mom = mysqli_fetch_array($qryMoment)) 
{ 
   $momnId[$index] = $mom['momId']; 
   $momnum[$index] = $mom['moment'];
   $momRaak[$index] = $mom['momId'];
   $index++; 
} 
unset($index);
/* Einde Declaratie Moment */

	 if(!isset($_POST['txtSpndm'])) { $txtSpndm = $spndm; } else { $txtSpndm = $_POST['txtSpndm']; }
	 if(!isset($_POST['txtSpnkg'])) { $txtSpnkg = $spnkg; } else { $txtSpnkg = $_POST['txtSpnkg']; }
	 if(!isset($_POST['txtEinddm'])) { $hersdm = $afvdm; } else { $hersdm = $_POST['txtEinddm']; }
	 if(!isset($_POST['txtAfvdm']) || isset($zetdatumterug)) { $txtAfvdm = $afvdm; unset($zetdatumterug); } else { $txtAfvdm = $_POST['txtAfvdm']; }
	 if(!isset($_POST['txtAfvkg'])) { $txtAfvkg = $afvkg; } else { $txtAfvkg = $_POST['txtAfvkg']; }
	
	 if(isset($_POST['txtTerugdm'])) { $txtTerugdm = $_POST['txtTerugdm']; }




/***  ---- UITVOEREN HERSTEL ----  ***/
if(isset ($_POST['knpHerstel']))
{

$hisId = zoek_hisId_stal_af($stalId);

/* Per 2-9-2023 vervangen door een functie uit basisfuncties.php 
$zoek_hisId = mysqli_query($db,"
SELECT hisId
FROM tblActie a
 join tblHistorie h on (a.actId = h.actId)
 join tblStal st on (st.stalId = h.stalId)
WHERE a.af = 1 and st.stalId = '".mysqli_real_escape_string($db,$stalId)."' and h.skip = 0
") or die (mysqli_error($db));
	while( $his = mysqli_fetch_assoc($zoek_hisId)) { $hisId = $his['hisId']; }*/

			$date = date_create($hersdm);	$newDate = date_format($date, 'Y-m-d'); $newDatum = date_format($date, 'd-m-Y');
	
	if(!isset($_POST['radHerst'])) { $fout = "Er is geen keuze m.b.t. herstellen gemaakt."; } 
//Terug plaatsen in verblijf of in stal(lijst)	
	else if($_POST['radHerst'] == 1 || $_POST['radHerst'] == 2) {

$update_tblHistorie = "UPDATE tblHistorie set skip = 1 WHERE hisId = '".mysqli_real_escape_string($db,$hisId)."' ";
	mysqli_query($db, $update_tblHistorie) or die (mysqli_error($db));
// eventuele meldingen worden niet verwijderd

$update_tblStal = "UPDATE tblStal st join tblHistorie h on (st.stalId = h.stalId) SET st.rel_best = NULL WHERE hisId = '".mysqli_real_escape_string($db,$hisId)."' ";
	mysqli_query($db, $update_tblStal) or die (mysqli_error($db));
	
if($actie_afv == 'overleden') {
$update_tblSchaap = "UPDATE tblSchaap SET momId = NULL, redId = NULL WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."' ";
	mysqli_query($db, $update_tblSchaap) or die (mysqli_error($db));
}

	if($_POST['radHerst'] == 1) { 
	 $zoek_hok = mysqli_query($db,"
	 SELECT hk.hoknr
	 FROM tblHok hk
	  join tblPeriode p on (hk.hokId = p.hokId)
	  join tblBezet b on (p.periId = b.periId)
	  join (
		SELECT max(bezId) bezId
		FROM tblBezet b
		 join tblHistorie h on (b.hisId = h.hisId)
		 join tblStal st on (st.stalId = h.stalId)
		WHERE h.skip = 0 and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	  ) mb on (mb.bezId = b.bezId)
	  ")or die (mysqli_error($db));
	while( $hk = mysqli_fetch_assoc($zoek_hok)) { $hok = $hk['hoknr']; }
								  $goed = "Het schaap is teruggeplaatst in ".$hok."."; }
	if($_POST['radHerst'] == 2) { $goed = "Het schaap is teruggeplaatst in de stal(lijst)."; }
	}
//Einde Terug plaatsen in verblijf of in stal(lijst)	
//Aanhouden als moeder- of vaderdier
	else if($_POST['radHerst'] == 3) { //Aanhouden als moeder- of vaderdier
	
$zoek_nietvoor_datum = mysqli_query($db,"
SELECT max(datum) date
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and 
 (actId = 4 or actId = 5 or actId = 6) and h.skip = 0
") or die (mysqli_error($db));
	while( $nv = mysqli_fetch_assoc($zoek_nietvoor_datum)) { $nietvoor = $nv['date']; } $day = date_create($nietvoor); $nietv = date_format($day,'d-m-Y');
	
$zoek_eerste_worp = mysqli_query($db,"
SELECT min(datum) date
From (
	SELECT  min(h.datum) datum, 'Eerste worp' actie
	FROM tblSchaap mdr
	 join tblVolwas v on (mdr.schaapId = v.mdrId)
	 join tblSchaap lam on (v.volwId = lam.volwId)
	 join tblStal st on (st.schaapId = lam.schaapId)
	 join tblHistorie h on (st.stalId = h.stalId and h.actId = 1 and h.skip = 0)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and mdr.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
) datum
") or die (mysqli_error($db));
	while( $nna = mysqli_fetch_assoc($zoek_eerste_worp)) { $dmworp1 = $nna['date']; } $day = date_create($dmworp1); $worpdm1 = date_format($day,'d-m-Y');
	
	
			 if(empty($_POST['txtEinddm'])) { $fout = "De hersteldatum is onbekend."; }
		else if($newDate < $nietvoor) { $fout = "De datum mag niet voor ".$nietv." liggen."; }
		else if(isset($dmworp1) && $newDate > $dmworp1) { $fout = "De datum mag niet na ".$worpdm1." liggen."; }
		else {
		$update_tblHistorie = "UPDATE tblHistorie SET skip = 1 WHERE hisId = '".mysqli_real_escape_string($db,$hisId)."' ";
			mysqli_query($db, $update_tblHistorie) or die (mysqli_error($db));		
		
		$insert_tblHistorie = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$newDate)."', actId = 3 ";
			mysqli_query($db, $insert_tblHistorie) or die (mysqli_error($db));
		
		// eventuele meldingen worden niet verwijderd
		
		$update_tblStal = "UPDATE tblStal SET rel_best = NULL WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."' ";
			mysqli_query($db, $update_tblStal) or die (mysqli_error($db));
			
		if($actie_afv == 'overleden') {	
		$update_tblSchaap = "UPDATE tblSchaap SET momId = NULL, redId = NULL WHERE schaapId = '".mysqli_real_escape_string($db,$_POST['txtSchaapId'])."' ";
			mysqli_query($db, $update_tblSchaap) or die (mysqli_error($db));
		}
		
			if($sekse == 'ooi') { $parent = 'moederdier'; } else if($sekse == 'ram') { $parent = 'vaderdier'; }
		$goed = "Het schaap is teruggeplaatst op de stallijst en is een ".$parent." per ".$newDatum.".";
		}
	}
//Einde Aanhouden als moeder- of vaderdier
// Uitscharen, afleveren of verkopen
	else if($_POST['radHerst'] == 10 or $_POST['radHerst'] == 12 or $_POST['radHerst'] == 13) {
	
$zoek_nietvoor_datum = mysqli_query($db,"
SELECT max(datum) date
From (
	SELECT h.datum, a.actie
	FROM tblActie a 
	 join tblHistorie h on (a.actId = h.actId)
	 join tblStal st on (st.stalId = h.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and 
	 a.af != 1 and h.skip = 0
	 
	union

	SELECT max(h.datum) datum, 'Laatste worp' actie
	FROM tblSchaap mdr
	 join tblVolwas v on (mdr.schaapId = v.mdrId)
	 join tblSchaap lam on (v.volwId = lam.volwId)
	 join tblStal st on (st.schaapId = lam.schaapId)
	 join tblHistorie h on (st.stalId = h.stalId and h.actId = 1 and h.skip = 0)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and mdr.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	GROUP BY mdr.schaapId, h.actId
	HAVING (max(h.datum) > min(h.datum))
) datum
") or die (mysqli_error($db));
	while( $nv = mysqli_fetch_assoc($zoek_nietvoor_datum)) { $nietvoor = $nv['date']; } $day = date_create($nietvoor); $nietv = date_format($day,'d-m-Y');
	
		if($newDate < $nietvoor) { $fout = "De datum mag niet voor ".$nietv." liggen."; }
		else if(empty($_POST['txtEinddm'])) { $fout = "De hersteldatum is onbekend."; }
		else if(empty($_POST['kzlBstm'])) { $fout = "De bestemming is onbekend."; }
		else if($_POST['radHerst'] == 12 && empty($_POST['txtHerkg'])) { $fout = "Aflevergewicht is onbekend."; }
		else {
		
		$update_tblHistorie = "UPDATE tblHistorie SET skip = 1 WHERE hisId = '".mysqli_real_escape_string($db,$hisId)."' ";
			mysqli_query($db,$update_tblHistorie) or die (mysqli_error($db));
	

			if(isset($_POST['txtHerkg'])) { $herkg = $_POST['txtHerkg']; }
			if(!empty($_POST['txtHerkg'])) { $txtAfvkg = $_POST['txtHerkg']; }
		$insert_tblHistorie = "INSERT INTO tblHistorie SET stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$newDate)."', kg = ".db_null_input($herkg).", actId = '".mysqli_real_escape_string($db,$_POST['radHerst'])."' ";
			mysqli_query($db, $insert_tblHistorie) or die (mysqli_error($db));
		
		$update_tblStal = "UPDATE tblStal SET rel_best = '".mysqli_real_escape_string($db,$_POST['kzlBstm'])."' WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."' ";
			mysqli_query($db, $update_tblStal) or die (mysqli_error($db));
			
		if($actie_afv == 'overleden') {	
		$update_tblSchaap = "UPDATE tblSchaap SET momId = NULL, redId = NULL WHERE schaapId = '".mysqli_real_escape_string($db,$_POST['txtSchaapId'])."' ";
			mysqli_query($db, $update_tblSchaap) or die (mysqli_error($db));
		}
			

		}
	}
// Einde Uitscharen, afleveren of verkopen
// Overleden
	else if($_POST['radHerst'] == 14) {

$zoek_nietvoor_datum = mysqli_query($db,"
SELECT max(datum) date
From (
	SELECT h.datum, a.actie
	FROM tblActie a 
	 join tblHistorie h on (a.actId = h.actId)
	 join tblStal st on (st.stalId = h.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and 
	 a.af != 1 and h.skip = 0
	 
	union

	SELECT max(h.datum) datum, 'Laatste worp' actie
	FROM tblSchaap mdr
	 join tblVolwas v on (mdr.schaapId = v.mdrId)
	 join tblSchaap lam on (v.volwId = lam.volwId)
	 join tblStal st on (st.schaapId = lam.schaapId)
	 join tblHistorie h on (st.stalId = h.stalId and h.actId = 1 and h.skip = 0)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and mdr.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	GROUP BY mdr.schaapId, h.actId
	HAVING (max(h.datum) > min(h.datum))
) datum
") or die (mysqli_error($db));
	while( $nv = mysqli_fetch_assoc($zoek_nietvoor_datum)) { $nietvoor = $nv['date']; } $day = date_create($nietvoor); $nietv = date_format($day,'d-m-Y');
	
		if($newDate < $nietvoor) { $fout = "De datum mag niet voor ".$nietv." liggen."; }
		else if(empty($_POST['txtEinddm'])) { $fout = "De hersteldatum is onbekend."; }
		else {
		$update_tblHistorie = "UPDATE tblHistorie SET skip = 1 WHERE hisId = '".mysqli_real_escape_string($db,$hisId)."' ";
			mysqli_query($db, $update_tblHistorie) or die (mysqli_error($db));
			
		$insert_tblHistorie = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$newDate)."', actId = '".mysqli_real_escape_string($db,$_POST['radHerst'])."' ";
			mysqli_query($db, $insert_tblHistorie) or die (mysqli_error($db));
		
		$update_tblStal = "UPDATE tblStal SET rel_best = '".mysqli_real_escape_string($db,$rendac_Id)."' WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."' ";
			mysqli_query($db, $update_tblStal) or die (mysqli_error($db));
	
		 if(isset($_POST['kzlAfvred'])) { $updRed = $_POST['kzlAfvred']; }	
		$update_tblSchaap = "UPDATE tblSchaap SET redId = ".db_null_input($updRed)." WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."' ";
			mysqli_query($db, $update_tblSchaap) or die (mysqli_error($db));
			
		$goed = "Het schaap is overleden per ".$newDatum.".";
			}
}
// Einde Overleden	
}
/***  EINDE ---- UITVOEREN HERSTEL ----  EINDE ***/
?>


<table border = 0 style="border-color : #9EB368"; valign = "top"> <!-- tabel 1 : voor velden zowel links als rechts -->
<tr>
<td valign = "top">	

 <!--   **********************************
	    ******	VELDEN LINKS	 ****** 
	    ********************************** --> 
<table border = 0 width = 450> <!-- tabel 2 : voor velden links -->

<form action= "UpdSchaap.php" method= "post"> 
<tr>
 <td> <input type= "hidden" name= "txtSchaapId" size = 5 value= <?php echo $schaapId; ?> > <!-- hiddden -->
	  <input type= "hidden" name= "txtStalId" 	size = 2 value= <?php echo $stalId; ?> > <!-- hiddden --> </td> 
 <td> <input type= "hidden" name= "txtwerknr"  value= <?php echo $pstwerknr; ?> > </td> <!-- hiddden -->
</tr>
<tr>
 <td>Levensnummer : </td>
 <td colspan = 3 align = left > <input type = text name = "txtLevnr" style = "text-align : right" size = 13 value = <?php echo $levnr; ?> > </td>
</tr>
<tr>
 <td>Fokkersnummer : </td>
 <td colspan = 3 align = left >
 	<input type = text name = "txtFokrnr" style = "text-align : right" size = 2 value = <?php if(isset($fokrnr)) { echo $fokrnr; } ?> >
 </td>
</tr>
<?php if(!isset($kleur) && isset($hnr)) { $halsnr = $hnr; } else if(isset($kleur) && !isset($hnr)) { $halsnr = $kleur; }  else if(isset($kleur) && isset($hnr)) { $halsnr = $kleur." ".$hnr; } ?>
<tr>
 <td>Halsnr : </td>
 <td colspan = 2 align = left width = 75><?php if(isset($halsnr)) { echo $halsnr; } ?> </td>
 <td>
 <select name= "kzlKleur" style= "width:62;" > 
<?php
$opties = array('' => '', 'blauw' => 'blauw', 'geel' => 'geel', 'oranje' => 'oranje', 'paars' => 'paars', 'rood'=>'rood', 'wit' => 'wit', 'zwart' => 'zwart');
foreach ( $opties as $key => $waarde)
{
   if((!isset($_POST['knpSave']) && $kleur == $key) || (isset($_POST["kzlKleur"]) && $_POST["kzlKleur"] == $key) ) {
	echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else {
	echo '<option value="' . $key . '">' . $waarde . '</option>';
  }
} ?>
</select>  
 <input type = text name = "txtHnr" style = "text-align : right" size = 1 value = <?php if(isset($hnr)) { echo $hnr; } ?> > </td>
</tr>
<?php /*Geslacht kan worden aangepast als het dier nog niet voorkomt in tblVolwas en
 niet bij een ander op de stallijst heeft gestaan */ 
$zoek_in_tblVolwas = mysqli_query($db, "
SELECT max(volwId) volwId
FROM tblVolwas
WHERE mdrId = '".mysqli_real_escape_string($db,$schaapId)."' or vdrId = '".mysqli_real_escape_string($db,$schaapId)."'
") or die (mysqli_error($db));
while ($vw = mysqli_fetch_assoc($zoek_in_tblVolwas)) { $volwas = $vw['volwId']; }

$zoek_aantal_stalljsten = mysqli_query($db, "
SELECT count(stalId) stalId
FROM tblStal
WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and lidId <> '".mysqli_real_escape_string($db,$lidId)."'
") or die (mysqli_error($db));
while ($as = mysqli_fetch_assoc($zoek_aantal_stalljsten)) { $ander_stallijst = $as['stalId']; }

?>
<!-- geslacht -->
<tr>
<td>Geslacht : </td>
<td> <?php if(isset($volwas) || $ander_stallijst > 0) { echo $sekse ; }
else{ ?>
<!-- keuzelijst Geslacht -->

 <select name= "kzlSekse" style= "width:62;" > 
<?php
$opties = array('ooi' => 'ooi', 'ram' => 'ram', 'kween' => 'kween');
foreach ( $opties as $key => $waarde)
{
   if((!isset($_POST['knpSave']) && $sekse == $key) || (isset($_POST["kzlSekse"]) && $_POST["kzlSekse"] == $key) ) {
	echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else {
	echo '<option value="' . $key . '">' . $waarde . '</option>';
  }
} ?>
</select>
<!-- Einde keuzelijst Geslacht -->
<?php } ?> </td> </tr>

<!-- fase -->
<tr>
 <td>Generatie : </td>
 <td colspan = 3 ><?php echo $fase;
 // Alleen tijdens het stalmoment waarneer de speendatum is aangemaakt kan een aanwasdatum worden gewijzigd. Na afvoeren dus niet meer
	if(isset($spndm) && isset($dmaanw)) { echo ' sinds '; ?> <input id="datepicker1" type= "text" name= "txtaanw" size = 8 value = <?php echo $aanwdm; ?> > <?php echo '(Aanwas)'; }
 ?>
 </td>

</tr>
<!-- ras --> 
<tr>
 <td> Ras : </td>
 <td colspan = 3> 
 <select name= "kzlRas" style= "width:145;" >
 <option> </option>
<?php $count = count($rsnum);
for ($i = 0; $i < $count; $i++){

	$opties = array($rsId[$i]=>$rsnum[$i]);		
			foreach ( $opties as $key => $waarde)
			{
						
		if((!isset($_POST['kzlRas']) && $rsRaak[$i] == $rasId) || (isset($_POST['kzlRas']) && $_POST['kzlRas'] == $key))
		{
			echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
		} else { 
			echo '<option value="' . $key . '" >' . $waarde . '</option>';
		}
			}
		
		} ?>
 </select> 
  </td>
</tr>
 <!-- moeder -->
<tr><td ></td><td></td><td colspan = 2 valign = "bottom"> <i><sub> Werknr &nbsp - &nbsp generatie &nbsp-&nbsp gelammerd </sub></i></td></tr>
<tr>
 <td>Werknr ooi (moeder): </td>
 <td width = 50 ><?php echo $mdr; ?> </td>
 <td colspan = 2 > <?php
$result = mysqli_query($db, "
SELECT ko.schaapId, right(ko.levensnummer,$Karwerk) Werknr, ko.lamrn
FROM (".$vw_kzlOoien.") ko
ORDER BY right(ko.levensnummer,$Karwerk), ko.lamrn
") or die (mysqli_error($db)); ?>
 <select name= "kzlOoi" style= "width:200;" >
 <option> </option>	
<?php		while($row = mysqli_fetch_array($result))
		{
		
			$opties= array($row['schaapId']=>$row['Werknr'].'&nbsp &nbsp &nbsp &nbsp moeder &nbsp &nbsp &nbsp &nbsp &nbsp '.$row['lamrn']);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if(isset($_POST['kzlOoi']) && $_POST['kzlOoi'] == $key)
		{
			$keuze = ' selected ';
		}
				
		echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
			}
		
		} ?>
 </select>
 </td>
</tr> 

 <!-- vader --> 
<tr><td></td><td></td><td colspan = 2 style = "font-size : 13px"> <i>Werknr - generatie - index </i></td></tr>
<tr> 
 <td>Werknr ram (vader) : </td>
 <td> <?php echo $vdr; ?> </td>
 <td colspan = 2 > <?php
$resultram = mysqli_query($db, "
SELECT st.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk) werknr, s.indx
FROM tblStal st 
 join tblSchaap s on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
WHERE s.geslacht = 'ram' and h.actId = 3 and h.skip = 0 and lidId = '".mysqli_real_escape_string($db,$lidId)."'
and not exists (
	SELECT st.schaapId
	FROM tblStal stal 
	 join tblHistorie h on (h.stalId = stal.stalId)
	 join tblActie  a on (a.actId = h.actId)
	WHERE stal.schaapId = s.schaapId and a.af = 1 and h.skip = 0 and lidId = '".mysqli_real_escape_string($db,$lidId)."')
GROUP BY st.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk)
ORDER BY right(s.levensnummer,$Karwerk)
") or die (mysqli_error($db)); ?>
 <select name= "kzlRam" style= "width:200;" >
 <option> </option>	
<?php		while($row = mysqli_fetch_array($resultram))
		{
		
			$opties= array($row['schaapId']=>$row['werknr'].'&nbsp &nbsp &nbsp &nbsp vader &nbsp &nbsp &nbsp &nbsp &nbsp '.$row['indx']);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if(isset($_POST['kzlRam']) && $_POST['kzlRam'] == $key)
		{
			$keuze = ' selected ';
		}
				
		echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
			}
		
		} ?>
 </select>
 </td>
</tr>
<tr><td height = 15 ></td></tr> 

 <!-- geboortedatum --> 
<tr> 
 <td> Geboortedatum : </td>
 <td colspan = 3> 
 	<?php if(isset($gebdm)) { echo $gebdm; }
 else { ?>
  <input type= "text" id="datepicker6" name= "txtGebdm" size = 8 value = <?php echo $txtGebdm; ?> >
  <?php } ?> 
  </td>
</tr>
 <!-- Aankoop --> 
<?php if(!isset($spndm) && isset($aanwdm)) { ?>
<tr> 
 <td> Aankoopdatum </td>
 <td colspan = 3 > <?php echo $aanwdm; ?>
  <input type= "hidden" name= "txtaanw" size = 8 value = <?php echo $aanwdm; ?> > <!-- hiddden bestaan onbekend --> </td>
</tr> 
<?php } ?>
 <!-- Einde Aankoop -->
 <!-- gewicht --> 
<tr> 
 <td> <?php if(isset($gebdm)) { ?> Geboorte gewicht :  </td>
 <td> <?php echo $kg; } ?> </td>
</tr>
<tr>
 <td height = 30></td>
</tr> 
 <!-- Speendatum --> 
 <?php // Alleen tijdens het stalmoment waarneer de speendatum is aangemaakt kan een speendatum worden gewijzigd. Na afvoeren dus niet meer
$zoek_speendm_alleStal = mysqli_query($db,"
SELECT datum date, date_format(datum,'%d-%m-%Y') datum, kg
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
WHERE st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and h.actId = 4 and h.skip = 0
") or die (mysqli_error($db));
	while( $alsp = mysqli_fetch_assoc($zoek_speendm_alleStal)) { $Speendate = $alsp['date']; $Speendatum = $alsp['datum']; $speengewicht = $alsp['kg']; }
 if(isset($Speendate)) { ?>
<tr>
 <td width = 135 > Speendatum : </td>
 <td colspan = 2 >
 <?php if(isset($spndm)) { // alleen te wijzigen als speendatum komt uit laatste stal ?>
 <input id="datepicker3" type= "text" name= "txtSpndm" size = 8 value= <?php if(isset($txtSpndm)) { echo $txtSpndm; } ?> >
 <?php } else { echo $Speendatum; } ?>
 </td>
</tr>
 <!-- speengewicht --> 
<tr>
 <td> Speengewicht : </td>
 <td colspan = 2>
 <?php  if(isset($spndm)) { ?>
 <input type= "text" name= "txtSpnkg" size = 2 value= <?php if(isset($txtSpnkg)) { echo $txtSpnkg; } ?> >
 <?php } else { echo $speengewicht; } ?>
 </td>
</tr>
 <?php } ?>
</table> <!-- Einde tabel 2 : voor velden links -->
</td>

<td width= 10 > </td>

<td width= 500 valign = "top">
 <!--  **********************************
	    ******	VELDEN MIDDEN	 ****** 
	    ********************************** --> 
<table border = 0 > <!-- tabel 3 : voor velden rechts -->
	

 <!-- Tussenwegingen --> 

 <table border = 0 > <!-- tabel 4 : voor velden t.b.v. tussenweging -->
<tr><td colspan = 2 width = 250 align = "center"> <?php	if(!empty($weegid)) { echo "Tussenmetingen"; } ?> </td>
<td align = "center" >
<i style = "font-size:14px;">
<a href='<?php echo $url; ?>Wegen.php?pstId=<?php echo $schaapId; ?>' style = "color : blue">
weging registreren
			</a>   </td></tr>
<?php	
if(!empty($weegid))
{ ?>
<tr style = "font-size:12px;">
 <th width= 120 style = "text-align:center;" valign="bottom" > Datum weging <hr></th>
 <th width= 120 style = "text-align:center;" valign="bottom" > Gewicht <hr></th>
</tr>
<?php
$weeg = mysqli_query($db, "
SELECT s.levensnummer, h.datum dmweeg, h.kg weegkg
FROM tblSchaap s
 join (
	SELECT max(stalId) stalId, schaapId
	FROM tblStal st
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	GROUP BY st.schaapId
 ) st_max on (s.schaapId = st_max.schaapId)
 join tblHistorie h on (h.stalId = st_max.stalId and h.actId = 9 and h.skip = 0)
ORDER BY h.datum desc
") or die (mysqli_error($db));

		while ($row = mysqli_fetch_array($weeg))
		{
		$pstlevnr =  $row['levensnummer'];
		$date = date_create($row['dmweeg']);
		$weegdm = date_format($date, 'd-m-Y');
		$weegkg = $row['weegkg'];
?>		
				
<tr align = "center">	
 <td width = 120 style = "font-size:15px;" > <?php echo $weegdm; ?> <br> </td>
 <td width = 120 style = "font-size:15px;"> <?php echo $weegkg; ?> <br> </td>
</tr>				
	<?php	} } ?>


<!-- <tr><td height = 20></td></tr> -->
</table> <!-- Einde tabel 4 : voor velden t.b.v. tussenweging -->

 <!-- Einde Tussenwegingen -->

<?php	/************************************
		**** 	 	AFVOEREN 	    **** Als schaap nog niet is afgevoerd wordt enkel hoofding getoond. Invoervelden worden gepresenteerd uit onderdeel HERSTEL !!
		************************************* Als schaap wel is afgevoerd wordt naast hoofding ook velden m.b.t. afvoer getoond */
if(!isset($afvhis)) { $hoofding = 'Afvoeren'; } //t.b.v. Aanwezige schapen
else if(isset($afvhis)) { $hoofding = $Actie_afv; }
	 if(isset($hoofding)) { // Toon hoofding ?>
<table border = 0 width = 450 > <!-- tabel 5 : t.b.v. velden bestaande afvoer -->
<tr>  <!-- Hoofding afvoer -->
 <td colspan = 3 align = "center"> <?php echo $hoofding; ?> 
 <hr></td>
</tr> <?php

// VELDEN T.B.V. AFGELEVERDEN EN VERKOCHTEN SCHAPEN
if( isset($afvhis) && ($actId_afv == 12 || $actId_afv == 13) ) { // Velden bij wijzigen afgeleverde en verkochte schapen	
/* BESTEMMING */	 ?>
<tr>
 <td> Bestemming<sup>*</sup> : </td>	
 <td> 
 <select name= "kzlBestupd" style= "width:145;" >
 <option> </option>
<?php $count = count($bstnum);
for ($i = 0; $i < $count; $i++){

	$opties = array($bstnId[$i]=>$bstnum[$i]);		
			foreach ( $opties as $key => $waarde)
			{
						
		if((!isset($_POST['kzlBestupd']) && $bstRaak[$i] == $relId) || (isset($_POST['kzlBestupd']) && $_POST['kzlBestupd'] == $key))
		{
			echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
		} else { 
			echo '<option value="' . $key . '" >' . $waarde . '</option>';
		}
			}
		
		} ?>
 </select> 
 </td>
 <td> </td>
</tr>
<?php  /* Einde BESTEMMING */ 


 if($fase == 'moederdier' || $fase == 'vaderdier' || isset($Actie_afv)) { ?>
<tr>
 <td> Afvoerdatum<sup>*</sup> : </td>
 <td> <input id="datepicker4" onchange="datumControle()" name= "txtAfvdm" type= "text" size = 8 value= <?php if(isset($txtAfvdm)) { echo $txtAfvdm; } ?> >
 	<p id="demo"></p>
 </td>
 <td> </td>
</tr> <?php }
if(isset($afvkg)) { ?>
<tr>
 <td> Afvoergewicht : </td>
 <td> <input name= "txtAfvkg" type= "text" size = 2 value= <?php if(isset($txtAfvkg)) {  echo $txtAfvkg; } ?> ></td>
 <td> </td>
</tr> <?php } 

if($actie_afv == 'overleden') { ?>
 <!-- reden uitval --> 
<tr>
 <td> Reden uitval : </td>
 <td>
 <select style= "width:170;" name= "kzlReden" >
 <option></option>
<?php $count = count($rednum);
for ($i = 0; $i < $count; $i++){

	$opties = array($rednId[$i]=>$rednum[$i]);		
			foreach ( $opties as $key => $waarde)
			{
						
		if((!isset($_POST['kzlReden']) && $red_db == $redRaak[$i]) || (isset($_POST['kzlReden']) && $_POST['kzlReden'] == $key))
		{
			echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
		} else { 
			echo '<option value="' . $key . '" >' . $waarde . '</option>';
		}
			}
		
		} ?>		
 </select>
 </td>
 <td></td>
</tr> <?php } // Einde if($actie_afv == 'overleden')
 
	 if($fase == 'moederdier' || $fase == 'vaderdier' || isset($Actie_afv)) { ?>
<tr style = "font-size:12px;">
 <td colspan = 3 align = right> 
 * Verplicht.
 </td> 
</tr> <?php } ?>
</table> <!-- Einde tabel 5 : t.b.v. velden bestaande afvoer -->

	  <script>

	function datumControle() {
	
	var x = document.getElementById("datepicker4").value;
    /*document.getElementById("demo").innerHTML = "You selected: " + x;*/
    $nietv = '12-01-2001';
    if(isset($nietv)) { 
     y = 'De datum mag niet voor ' + $nietv + ' liggen.';
		window.alert(y);
	}
	
}
</script>

<?php 	} // Einde if( isset($afvhis) && ($actId_afv == 12 || $actId_afv == 13) ) 
// EINDE VELDEN T.B.V. AFGELEVERDEN EN VERKOCHTEN SCHAPEN


// VELDEN T.B.V. UITGESCHAARDE SCHAPEN
if( isset($afvhis) && $actId_afv == 10) { ?>

<tr>
 <td> Datum terug van uitscharen : </td>	
 <td> 
 <input id="datepicker4" type = text name = "txtTerugdm" size = 8 style= "font-size : 11px" value = <?php if(isset($txtTerugdm)) { echo $txtTerugdm; } ?> >
 </td>
 <td> </td>
</tr>
<tr height = 75>
 <td colspan = 3 align = "center">  <hr>
 </td>
</tr>
<?php
}
// EINDE VELDEN T.B.V. UITGESCHAARDE SCHAPEN

} // Einde Toon hoofding 
/*	*******************************
	**** 	 EINDE AFVOEREN  **** Einde Als schaap nog niet is afgevoerd wordt enkel hoofding getoond. Invoervelden worden gepresenteerd uit onderdeel HERSTEL !!
	******************************* Einde Als schaap wel is afgevoerd wordt naast hoofding ook velden m.b.t. afvoer getoond */



/*	******************************
	**** 	   HERSTELLEN 	   **** Incl. velden m.b.t. afvoer bij een schaap dat nog niet is afgevoerd en dus niet kan worden hersteld.
	LET OP uitgeschaarde schapen kunnen niet worden hersteld.
	****************************** */

if(!isset($actId_afv) || (isset($actId_afv) && $actId_afv != 10) ) { /* Herstellen kan niet bij uitgeschaard */
	
/* Opties van herstellen */
// OPTIE 1 = Terug plaatsen in verblijf
if(!isset($dmafsl) && !isset($Nextstal) && !isset($dmaanw) && isset($afvhis) && $modtech == 1) { $optie1 = 'Terug plaatsen in '.$hok; }

// OPTIE 2 = Terug plaatsen in stal(lijst)
if(($modtech == 0 && !isset($Nextstal) && isset($afvhis)) || (!isset($Nextstal) && isset($dmaanw) && isset($afvhis))) { $optie2 = 'Terug plaatsen in stal(lijst)'; }
/* Volgende stal mag niet bestaan, aanwasdatum moet bestaan en het dier moet zijn afgevoerd (Nodig i.v.m. afvoeren moeder/vader niet zichtbaar)*/

// OPTIE 3 = Aanhouden als moer- of vaderdier
if(isset($spndm) /*Speendatum uit historie van laatste stalId*/ && !isset($dmaanw) && !isset($Nextstal)) { if($sekse == 'ooi') { $ouder = 'moederdier'; } else if($sekse == 'ram') { $ouder = 'vaderdier'; } $optie3 = 'Aanhouden als '.$ouder; }

// OPTIE 4 = Uitscharen volwassen dieren
if($actie_afv != 'uitgeschaard' && !isset($Nextstal) && isset($dmaanw) /*isset($dmaanw) zorgt ervoor dat uitscharen lammmeren niet kan */ ) { $optie4 = 'Uitscharen'; }

// OPTIE 5 = Afleveren, verkopen of afvoeren bij herstellen
if( (($actie_afv == 'overleden' || $actie_afv == 'uitgeschaard' || $actie_afv == 'vermist') && !isset($dmaanw) && isset($spndm))		)				  		  { $new_afvoer = 'Afleveren'; $value = 12;} 
else if( (($actie_afv == 'overleden' || $actie_afv == 'uitgeschaard' || $actie_afv == 'vermist') && isset($dmaanw)) || (isset($dmaanw) && !isset($afvhis)) ) { $new_afvoer = 'Verkopen'; $value = 13; }
else if(($actie_afv == 'overleden' || $actie_afv == 'uitgeschaard' || $actie_afv == 'vermist') && !isset($dmaanw) && !isset($spndm) && $modtech == 0)		  { $new_afvoer = 'Afvoeren'; $value = 12;}
 if(isset($value)) { $optie5 = $new_afvoer; }
 
// OPTIE 5 = Afleveren, verkopen bij aanwezige schapen
if( !isset($afvhis) && !isset($dmaanw) && isset($spndm) )	{ $new_afvoer = 'Afleveren'; $value = 12;} 
else if( !isset($afvhis) && isset($dmaanw) ) { $new_afvoer = 'Verkopen'; $value = 13; }

 if(isset($value)) { $optie5 = $new_afvoer; }

// OPTIE 6 = Overleden
if($actie_afv != 'overleden' || !isset($afvhis)) 		{ $optie6 = 'Overleden'; }

// OPTIE 7 = Vermissing
if($actie_afv != 'vermist' || !isset($afvhis)) 		{ $optie7 = 'Vermist'; }

/* Einde Opties van herstellen */

if(!isset($optie1) && !isset($optie2) && !isset($optie3) && !isset($optie4) && !isset($optie5) && !isset($optie6) && !isset($optie7) ) { echo 'Herstellen is niet (meer) mogelijk.'; } else { $Hersteloptie = ''; }
if(isset($Hersteloptie) && isset($levnr)) { // Bij herstel moet levenenummer bestaan ?>
<table border = 0 width = 500 > <!-- tabel 6 : velden t.b.v. 1e afvoer en herstel -->
<?php if(isset($afvhis)) { ?> 
<tr  height = 40 valign = bottom>
 <td colspan = 3 align = "center"> <hr></td>
</tr> <?php }
// Is er een afvoermelding die klaar staat om te melden aan de RVO ? In dat geval moet worden geïnformeerd dat deze komt te vervallen. Als tblMelding.skip = 1 dan wel verwijderen maar niet vermelden.
$zoek_actuele_melding = mysqli_query($db,"
SELECT m.meldId, m.skip
FROM tblSchaap s 
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
 join tblMelding m on (m.hisId = h.hisId)
 join tblRequest rq on (rq.reqId = m.reqId)
WHERE (rq.code = 'AFV' or rq.code = 'DOO') and isnull(rq.dmmeld) and h.skip = 0 and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
") or die (mysqli_error($db));

	while ($zam = mysqli_fetch_assoc($zoek_actuele_melding)) { $meldId = $zam['meldId']; $skip = $zam['skip']; }
	
	if(isset($meldId) && (!isset($skip) || $skip == 0)) { $msg_rvo = "Let op: De melding voor de RVO die klaar staat zal na herstellen zijn verwijderd."; } 
	
// Is er een schaap aanwezig en eerder al afgevoerd met een definitieve melding ? In dat geval wordt geïnformeerd dat er geen melding aan de RVO wordt aangemaakt 
$zoek_definitieve_afvoermelding = mysqli_query($db,"
SELECT count(h.hisId) defat
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."' and h.skip = 0 and rq.def = 1 and m.skip = 0
") or die (mysqli_error($db));
	while ( $zda = mysqli_fetch_assoc($zoek_definitieve_afvoermelding)) { $def_aant = $zda['defat']; }
	
	if(isset($def_aant) and $def_aant > 0)  { $msg_geen_rvo = "Er zal geen melding voor de RVO worden gemaakt. Er is immers eerder al afvoer gemeld."; }

?>

<tr>
 
 <td colspan = 2> <?php if(isset($afvhis)) { ?> Herstellen : <?php  $radio = "radHerst"; } else { $radio = "radAfv"; } ?>
	<i style= "font-size : 13px"> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp datum : </i><input id="datepicker5" type = text name = "txtEinddm" size = 8 style= "font-size : 11px" value = <?php echo $hersdm; ?> >

<?php if(isset($optie4) || isset($optie5) || isset($dmaanw) /* uitscharen lammeren niet mogelijk gemaakt */) { ?>
	<i style= "font-size : 13px"> &nbsp bestemming : </i><select name= "kzlBstm" style= "width:145; font-size : 11px" >
  <option> </option>
<?php $count = count($bstnum);
for ($i = 0; $i < $count; $i++){

	$opties = array($bstnId[$i]=>$bstnum[$i]);		
			foreach ( $opties as $key => $waarde)
			{
						
		if(isset($_POST['kzlBstm']) && $_POST['kzlBstm'] == $key)
		{
			echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
		} else { 
			echo '<option value="' . $key . '" >' . $waarde . '</option>';
		}
			}
		
		} ?>
 </select> <sup style= "font-size : 12px"> **</sup>
<?php } ?>
 </td>
</tr>
<?php if(isset($optie1)) { ?>
<tr>
 <td colspan = 2 >
	<input type = radio name = 'radHerst' value = 1 > <?php echo $optie1; /* Terugplaatsen in hok */ ?> 
	<!-- <input name= "txtHok" type= "hidden" value= <?php echo $hok; ?> > <!--hiddden Tbv melding na herstel --> 
 </td>
</tr>
<?php }
	 if(isset($optie2)) { ?>
<tr>
 <td colspan = 2 >
	<input type = radio name = 'radHerst' value = 2 > <?php echo $optie2; /* Terugplaatsen in Stallijst */ ?> </td>
</tr>
<?php }
if(isset($optie3)) { ?>
<tr>
 <td colspan = 2 >
	 <input type = radio name = <?php echo $radio; ?> value = 3 > <?php echo $optie3; /*Aanhouden als vader- of moederdier */ ?>
<!-- kzlVerblijf -->
 <!-- <select name= "kzlHok" style= "width:80; font-size : 12px" >
 <option></option> -->
<?php /*$count = count($hoknum);
for ($i = 0; $i < $count; $i++){

	$opties = array($hoknId[$i]=>$hoknum[$i]);		
			foreach ( $opties as $key => $waarde)
			{
						
		if((!isset($_POST['kzlHok']) && $hokId == $hokRaak[$i]) || (isset($_POST['kzlHok']) && $_POST['kzlHok'] == $key))
		{
			echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
		} else { 
			echo '<option value="' . $key . '" >' . $waarde . '</option>';
		}
			}
		
		}*/ ?>		
<!-- </select>	-->
<!-- Einde kzlVerblijf -->
 </td> 
</tr>  
<?php }
	if(isset($optie4)) { ?>
<tr>
 <td colspan = 2 >
	 <input type = radio name = <?php echo $radio; ?> value = 10 > <?php echo $optie4; /* Uitscharen */ ?>
 </td> 
</tr> <?php }
  
if(isset($optie5)) { ?>
<tr>
 
 <td colspan = 2 >
	 <input type = radio name = <?php echo $radio; ?> value = <?php echo $value; ?> > <?php echo $optie5; /* Afleveren, verkopen of afvoeren */
	 if($value == 12 && $modtech == 1) { ?>
	 <i style= "font-size : 13px"> &nbsp Kg : </i> <input type = text name = 'txtHerkg' size = 2 style= "font-size : 11px"> <?php } ?>
 </td>
</tr>
<?php }

if(isset($optie6)) { ?>
<tr>
 
 <td colspan = 2 >
	 <input type = radio name = <?php echo $radio; ?> value = 14 > <?php echo $optie6; /* Overleden */ ?>

<!-- Reden uitval bij hestel -->
 <i style= "font-size : 13px"> &nbsp reden : </i>
 <select name= "kzlAfvred" style= "width:135; font-size : 12px" >
 <option></option>
<?php $count = count($rednum);
for ($i = 0; $i < $count; $i++){

	$opties = array($rednId[$i]=>$rednum[$i]);		
			foreach ( $opties as $key => $waarde)
			{
						
		if((!isset($_POST['kzlAfvred']) && $red_db == $redRaak[$i]) || (isset($_POST['kzlAfvred']) && $_POST['kzlAfvred'] == $key))
		{
			echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
		} else { 
			echo '<option value="' . $key . '" >' . $waarde . '</option>';
		}
			}
		
		} ?>		
 </select>	
<!-- Einde Reden uitval bij hestel -->
 </td>
</tr>
 <?php } 

 	if(isset($optie7)) { ?>
<tr>
 <td colspan = 2 >
	 <input type = radio name = <?php echo $radio; ?> value = 20 > <?php echo $optie7; /* Vermist */ ?>
 </td> 
</tr> <?php } ?>
 
</tr> 
<tr>  <!-- Hoofding afvoer -->

 <td align = left valign = bottom>
<?php if(isset($afvhis)) { ?>
 <input type= "submit" name= "knpHerstel" value= "Herstellen" style = "font-size:11px;" >
<?php } ?>
 </td>
 <?php
 if(isset($optie4)) { $hint = '** Alleen bij uitscharen'; } 

if(isset($hint) && isset($optie5)) { $hint = $hint." en ".strtolower($new_afvoer); } 
else if(isset($optie5)) { $hint = '** Alleen bij '.strtolower($new_afvoer); } ?>
 <td colspan = 1 align = right style= "font-size : 13px"> <?php if(isset($hint)) { echo $hint; } ?> </td>
</tr>

<?php 
 if(isset($msg_rvo)) { ?>
<tr>
 <td colspan = 3 style= "font-size : 13px"><?php echo $msg_rvo; ?>
 <input type = hidden name = "txtmeldId" size = 1 value = <?php if(isset($meldId)) { echo $meldId; } ?> > <!-- hiddden -->
 <hr></td>
</tr>
<?php }
 else { ?>
<tr>
 <td colspan = 3 style= "font-size : 13px"><?php if(isset($msg_geen_rvo) && !isset($afvhis)) { echo $msg_geen_rvo; } ?> <hr> 
 </td>
 </tr> <?php } ?>
<!--	************************************
	**** 	 EINDE  HERSTELLEN 	   **** Einde Incl. velden m.b.t. afvoer bij een schaap dat nog niet is afgevoerd en dus niet kan worden hersteld.
	************************************-->
</table> <!-- Einde tabel 6 : velden t.b.v. 1e afvoer en herstel -->
<?php } // Einde Bij herstel moet levenenummer bestaan 
} // Einde if(!isset($actId_afv) || (isset($actId_afv) && $actId_afv != 10) ) Herstellen kan niet bij uitgeschaard ?>

<table border = 0 align="center"> <!-- tabel 7 : t.b.v. velden verblijf moederdier-->
<tr height = 50>
 <td></td>
</tr>
<tr>
 <td>
<?php if (!isset($afvhis) && (!isset($bezId) || isset($actId_uit)) ) { /* Tonen kzlVerblijf voor moederdier */ ?>
 	Verblijf ooi
 	 <!-- KZLVERBLIJF KEUZE-->
 <select style="width:<?php echo $w_hok; ?>;" name= 'kzlHokOoi' value = "" style = "font-size:12px;">
  <option></option>
<?php
$count = count($hoknum);
for ($i = 0; $i < $count; $i++){

	$opties = array($hoknId[$i]=>$hoknum[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((isset($_POST['kzlHokOoi']) && $_POST['kzlHokOoi'] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}
?> </select> &nbsp

 <!-- EINDE KZLVERBLIJF KEUZE -->

 <?php } 
 else if (!isset($afvhis) && !isset($actId_uit) && isset($bezId) && $fase != 'lam') {
 	echo ucfirst($fase)." uit ".strtolower($hok)." halen."; ?> 


	<input type="submit" name="knpUitHok" value = " Verlaat ">
 <?php }/* Einde Tonen kzlVerblijf voor moederdier */ ?>
  </td>
 <td> <?php if (!isset($afvhis)) { ?> per datum 
	<input type="text" id="datepicker2" name= "txtHokOoiDm" size = 8 >
<?php } ?>
</td>

</tr>
</table><!-- Einde tabel 7 : t.b.v. verbljf moederdier -->

<table border = 0> <!-- tabel 8 : t.b.v. Opslaan -->
<tr height = 50>
 <td></td>
</tr>
<tr> 
 <td colspan = 4 align = "center"> <input type= "submit" name= "knpSave" value= "Opslaan" > <br>
<i style = "font-size:14px;"> <br>  
<a href='<?php echo $url; ?>Zoeken.php?kzllevnr=<?php echo $pstlevnr; ?>&kzlwerknr=<?php echo $pstwerknr; ?>&knpzoek=zoeken' style = "color : blue">
terug
			</a> </i>  

 </td>	   
</tr>
</table> <!-- Einde tabel 8 : t.b.v. Opslaan -->
 </td> 
 <td valign = top> 

<table border = 0 > <!-- tabel 9 : t.b.v. velden historie -->
<tr>
 <td rowspan = 2 width = 100> </td>
 <td>Historie :</td>
</tr>
<tr>
 <td style = "font-size : 15px ;">
<?php 
$queryHistorie = mysqli_query($db,"
SELECT date_format(datum,'%d-%m-%Y') dag, h.actId, actie, datum
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
 join tblActie a on (a.actId = h.actId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and h.skip = 0
and not exists (
	SELECT datum 
	FROM tblHistorie geenAanwas 
	 join tblStal st on (geenAanwas.stalId = st.stalId)
	WHERE actId = 2 and h.datum = geenAanwas.datum and h.actId = geenAanwas.actId+1 and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)/* bij aankoop incl. aanwas wordt aanwas niet getoond */."')

union

SELECT date_format(datum,'%d-%m-%Y') dag, h.actId, actie, datum
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
 join tblActie a on (a.actId = h.actId)
WHERE h.actId = 1 and h.skip = 0 and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'

union

SELECT date_format(p.dmafsluit,'%d-%m-%Y') dag, h.actId, 'Gevoerd' actie, p.dmafsluit
FROM tblVoeding v	
 join tblPeriode p on (p.periId = v.periId)
 join tblBezet b on (p.periId = b.periId)
 join tblHistorie h on (h.hisId = b.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId =st.schaapId)
WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'

union

SELECT date_format(min(h.datum),'%d-%m-%Y') dag, h.actId, 'Eerste worp' actie, min(h.datum) datum
FROM tblSchaap s
 join tblVolwas v on (s.schaapId = v.mdrId)
 join tblSchaap lam on (v.volwId = lam.volwId)
 join tblStal st on (st.schaapId = lam.schaapId)
 join tblHistorie h on (st.stalId = h.stalId and h.actId = 1 and h.skip = 0)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
GROUP BY h.actId

union

SELECT date_format(max(h.datum),'%d-%m-%Y') dag, h.actId, 'Laatste worp' actie, max(h.datum) datum
FROM tblSchaap s
 join tblVolwas v on (s.schaapId = v.mdrId)
 join tblSchaap lam on (v.volwId = lam.volwId)
 join tblStal st on (st.schaapId = lam.schaapId)
 join tblHistorie h on (st.stalId = h.stalId and h.actId = 1 and h.skip = 0)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
GROUP BY h.actId
HAVING (max(h.datum) > min(h.datum))

union

SELECT date_format(rs.dmcreate,'%d-%m-%Y') dag, h.actId, 'Geboorte gemeld' actie, rs.dmcreate
FROM impRespons rs
 join tblMelding m on (rs.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId and s.levensnummer = rs.levensnummer)
WHERE rs.melding = 'GER' and rs.meldnr is not null and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'

union

SELECT date_format(rs.dmcreate,'%d-%m-%Y') dag, h.actId, 'Aanvoer gemeld' actie, rs.dmcreate
FROM impRespons rs
 join tblMelding m on (rs.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId and s.levensnummer = rs.levensnummer)
WHERE rs.melding = 'AAN' and rs.meldnr is not null and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'

union

SELECT date_format(rs.dmcreate,'%d-%m-%Y') dag, h.actId, 'Afvoer gemeld' actie, rs.dmcreate
FROM impRespons rs
 join tblMelding m on (rs.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId and s.levensnummer = rs.levensnummer)
WHERE rs.melding = 'AFV' and rs.meldnr is not null and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'

union

SELECT date_format(rs.dmcreate,'%d-%m-%Y') dag, h.actId, 'Uitval gemeld' actie, rs.dmcreate
FROM impRespons rs
 join tblMelding m on (rs.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId and s.levensnummer = rs.levensnummer)
WHERE rs.melding = 'DOO' and rs.meldnr is not null and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'

ORDER BY datum desc, actId desc
") or die (mysqli_error($db));

while ($h = mysqli_fetch_assoc($queryHistorie)) { $da = $h['dag']; $ac = $h['actie'];  echo $da." - ".$ac."<br>"; } ?>
 </td>
</tr>
</table><!-- Einde tabel 9 : t.b.v. velden historie -->

 </td>
</tr>

 <!-- </table> Einde Einde tabel 3 : voor velden rechts -->
</table> <!-- Einde tabel 1 : voor velden zowel links als rechts -->
<?php $toonControle = 0; 
if($toonControle  == 1) { 
echo '$actId_afv = '. $actId_afv.'<br>';
echo '$dmafsl = '.$dmafsl.'<br>';
echo '$Nextstal = '.$Nextstal.'<br>';
echo '$dmaanw = '.$dmaanw.'<br>';
echo '$afvhis = '.$afvhis.'<br>';
echo '$modtech = '.$modtech.'<br>';
echo '$hok = '.$hok.'<br>';
echo '$spndm = '.$spndm.'<br>';
echo '$sekse = '.$sekse.'<br>';
echo '$ouder = '.$ouder.'<br>';
echo '$actie_afv = '.$actie_afv.'<br>';

	
/* Opties van herstellen */
// OPTIE 1 = Terug plaatsen in verblijf



	?>
<table border = 0 > <!-- tabel 16 : voor controlevelden  -->
<tr>
<td colspan = 8 align = "center"> <hr> Controle velden </td>
</tr>
<tr>
 <td>
 Volgend stalId <input type = text name = "txtStalIdNext"  size = 1 title = "Indien deze bestaat moet afvoer blijven bestaan" value = <?php if(isset($Nextstal)) { echo $Nextstal; } ?> >
 </td></tr>
<tr>
 <td>
	Afsluitdatum laatste periode (<?php echo $periId; ?>)
	<input type = text name = "txtAfsldm" size = 8 title = "Indien deze bestaat kan het schaap niet terug in het verblijf" value = <?php if(isset($dmafsl)) { echo $dmafsl; } ?> >
	
 </td>
</tr>

<tr>
<td colspan = 8 align = "center"> Einde controle velden <hr> </td>


</tr> 
</form>

</table> <!-- Einde tabel 16 : voor controlevelden  -->
<?php } ?>
</TD>
<?php
Include "menu1.php"; } ?>
</body>
</html>
