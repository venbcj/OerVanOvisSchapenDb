<?php /* 30-11-2014 : keuzelijst voer gewijigd zodat enkel voer in voorraad kan worden gekozen. Bovendie first in first out (via inkId )
28-2-2015 login toegevoegd 
27-11-2015 : insVoer.php vervangen door save_voer.php */
$versie = "18-1-2017"; /* Query's aangepast n.a.v. nieuwe tblDoel */
$versie = "23-1-2017"; /* 22-1-2017 tblBezetting gewijzigd naar tblBezet 23-1-2017 kalender toegevoegd */
$versie = "5-2-2017"; /* Aanpassing n.a.v. verblijven met verschillende doelgroepen */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '20-12-2019'; /* tabelnaam gewijzigd van UIT naar uit tabelnaam */
$versie = '29-2-2020'; /* Datum laatste schaap uit verblijf toegevoegd 7-3-2020 fouten uit code gehaald $dmstopgeb moest $dmstopspn of $dmstopvolw zijn. Er waren onterecht volwassendieren waarvan de periode kon worden afgesloten */
 session_start(); ?>
<html>
<head>
<title>Registratie</title>
</head>
<body>

<center>
<?php
include"kalender.php";
$titel = 'Afsluiten periode';
$subtitel = '';
Include "header.php"; ?>

	<TD width = 940 height = 400 valign =top>
<?php
$file = "Bezet.php";
Include "login.php";
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {
//include "vw_Voorraad.php"; // incl. $vw_Voorraden t.b.v. save_voer.php

if (empty ($_GET['pstId'])) { $Id = $_POST['txtId']; } // Id = hokId
else { $Id = $_GET['pstId']; }


if (isset($_POST['knpSave1'])) { 
	if(!empty($_POST['txtSluitdm1']) )  { $sluitdm1 = $_POST['txtSluitdm1']; $date = date_create("$sluitdm1");	$dmsluit = date_format($date,'Y-m-d'); 
		$sluitdm = $_POST['txtSluitdm1']; /*t.b.v. save_afsluiten.php*/ } 
	else { $fout = "Afsluitdatum is niet bekend"; }
	if(!empty($_POST['txtKg1'])) 		{ $txtKg = $_POST['txtKg1']; }
	if(!empty($_POST['kzlArtikel1']))	{ $fldInk = $_POST['kzlArtikel1']; }
	$doelId = 1;

	Include "save_afsluiten.php"; }

if (isset($_POST['knpSave2'])) { 
	if(!empty($_POST['txtSluitdm2']) )  { $sluitdm2 = $_POST['txtSluitdm2']; $date = date_create("$sluitdm2");	$dmsluit = date_format($date,'Y-m-d'); 
		$sluitdm = $_POST['txtSluitdm2']; /*t.b.v. save_afsluiten.php*/ } 
	else { $fout = "Afsluitdatum is niet bekend"; }
	if(!empty($_POST['txtKg2'])) 		{ $txtKg = $_POST['txtKg2']; }
	if(!empty($_POST['kzlArtikel2']))	{ $fldInk = $_POST['kzlArtikel2']; }
	$doelId = 2;

	Include "save_afsluiten.php"; }

if (isset($_POST['knpSave3'])) { 
	if(!empty($_POST['txtSluitdm3']) )  { $sluitdm3 = $_POST['txtSluitdm3']; $date = date_create("$sluitdm3");	$dmsluit = date_format($date,'Y-m-d'); 
		$sluitdm = $_POST['txtSluitdm3']; /*t.b.v. save_afsluiten.php*/ }
	else { $fout = "Afsluitdatum is niet bekend"; }
	if(!empty($_POST['txtKg3'])) 		{ $txtKg = $_POST['txtKg3']; }
	if(!empty($_POST['kzlArtikel3']))	{ $fldInk = $_POST['kzlArtikel3']; }
	$doelId = 3;

	Include "save_afsluiten.php"; } ?>

<form action= <?php echo "HokAfsluiten.php"; ?> method="post">
	<table border = 0> <!-- table1 -->
	<tr> <!-- table1 rij1 -->
	 <td> <input type ="hidden" name = "txtId" <?php  echo "value = \"$Id\" "; ?> >

<?php
$zoek_laatste_afsluitdm_geb = mysqli_query($db,"
SELECT max(dmafsluit) dmstop
FROM tblPeriode
WHERE hokId = ".mysqli_real_escape_string($db,$Id)." and doelId = 1 and dmafsluit is not null
") or die (mysqli_error($db));
 
	while($stp_g = mysqli_fetch_assoc($zoek_laatste_afsluitdm_geb))	{ $dmstopgeb = $stp_g['dmstop']; } if(!isset($dmstopgeb)) { $dmstopgeb = '1973-09-11'; }

	
$zoek_startdatum_geb = mysqli_query($db,"
SELECT hisv.datum date, date_format(hisv.datum,'%d-%m-%Y') datum, hisv.hoknr
FROM (
	SELECT min(h.datum) datum, b.hokId, hk.hoknr
	FROM tblHistorie h
	 join tblStal st on (h.stalId = st.stalId)
	 join tblBezet b on (h.hisId = b.hisId)
	 join tblHok hk on (hk.hokId = b.hokId)
	 left join (
		SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
		FROM tblBezet b
		 join tblHistorie h1 on (b.hisId = h1.hisId)
		 join tblActie a1 on (a1.actId = h1.actId)
		 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
		 join tblActie a2 on (a2.actId = h2.actId)
		 join tblStal st on (h1.stalId = st.stalId)
		WHERE b.hokId = ".mysqli_real_escape_string($db,$Id)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
		GROUP BY b.bezId, st.schaapId, h1.hisId
	 ) uit on (uit.hisv = b.hisId)
	 left join tblHistorie ht on (ht.hisId = uit.hist)
	 left join (
		SELECT st.schaapId, h.hisId, datum
		FROM tblStal st
		 join tblHistorie h on (h.stalId = st.stalId)
		WHERE h.actId = 4 and h.skip = 0
	 ) spn on (st.schaapId = spn.schaapId)
	 left join (
		SELECT st.schaapId, h.hisId, datum
		FROM tblStal st
		 join tblHistorie h on (h.stalId = st.stalId)
		WHERE h.actId = 3 and h.skip = 0
	 ) prnt on (st.schaapId = prnt.schaapId)
	WHERE b.hokId = ".mysqli_real_escape_string($db,$Id)."
	 and (isnull(ht.hisId) or ht.datum > '".mysqli_real_escape_string($db,$dmstopgeb)."')
	 and (isnull(spn.schaapId) or h.datum < spn.datum)
	 and (isnull(prnt.schaapId) or h.datum < prnt.datum)
	GROUP BY b.hokId, hk.hoknr
) hisv
") or die (mysqli_error($db));
	while ($hk = mysqli_fetch_assoc($zoek_startdatum_geb)) { 
		$hoknr_geb = $hk['hoknr'];
		$date1_geb = $hk['date']; }
		
		if(isset($date1_geb)) { if ($date1_geb < $dmstopgeb) { $dmstart_geb = $dmstopgeb; } else { $dmstart_geb = $date1_geb; } 
			$todate = date_create($dmstart_geb); $dag1_geb = date_format($todate,'d-m-Y'); }

$zoek_laatste_uitdatum_geb = mysqli_query($db,"
SELECT date_format(max(ht.datum),'%d-%m-%Y') lastuit
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join (
    SELECT st.schaapId, datum
    FROM tblStal st
     join tblHistorie h on (h.stalId = st.stalId)
    WHERE h.actId = 4 and h.skip = 0
 ) spn on (st.schaapId = spn.schaapId)
 left join (
    SELECT st.schaapId, datum
    FROM tblStal st
     join tblHistorie h on (h.stalId = st.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (st.schaapId = prnt.schaapId)
 join (
    SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
     join tblActie a2 on (a2.actId = h2.actId)
    WHERE b.hokId = ".mysqli_real_escape_string($db,$Id)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, h1.hisId
 ) uit on (b.hisId = uit.hisv)
 left join tblHistorie ht on (ht.hisId = uit.hist)
WHERE b.hokId = ".mysqli_real_escape_string($db,$Id)."
 and ht.datum > '".mysqli_real_escape_string($db,$dmstopgeb)."'
and (isnull(spn.schaapId) or h.datum < spn.datum)
and (isnull(prnt.schaapId) or h.datum < prnt.datum)
") or die (mysqli_error($db));
	while ($ht = mysqli_fetch_assoc($zoek_laatste_uitdatum_geb)) { 
		$laatste_uit_geb = $ht['lastuit']; }


// Als er schapen zonder speendatum in het verblijf zitten
if(isset($date1_geb)) {		
$zoek_totaalaantal_geb = mysqli_query($db,"
SELECT count(distinct st.schaapId) aant
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join (
	SELECT st.schaapId, datum
	FROM tblStal st
	 join tblHistorie h on (h.stalId = st.stalId)
	WHERE h.actId = 4 and h.skip = 0
 ) spn on (st.schaapId = spn.schaapId)
 left join (
	SELECT st.schaapId, datum
	FROM tblStal st
	 join tblHistorie h on (h.stalId = st.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (st.schaapId = prnt.schaapId)
 left join (
	SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
	 join tblActie a2 on (a2.actId = h2.actId)
	WHERE b.hokId = ".mysqli_real_escape_string($db,$Id)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId, h1.hisId
 ) uit on (b.hisId = uit.hisv)
 left join tblHistorie ht on (ht.hisId = uit.hist)
WHERE b.hokId = ".mysqli_real_escape_string($db,$Id)."
 and (isnull(ht.hisId) or ht.datum > '".mysqli_real_escape_string($db,$dmstopgeb)."')
and (isnull(spn.schaapId) or h.datum < spn.datum)
and (isnull(prnt.schaapId) or h.datum < prnt.datum)
") or die (mysqli_error($db));
	while ($hk = mysqli_fetch_assoc($zoek_totaalaantal_geb)) { $totat_geb = $hk['aant']; } 

$zoek_nu_in_hok = mysqli_query($db,"
SELECT count(b.bezId) aant
FROM tblBezet b
 left join (
	SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
	 join tblActie a2 on (a2.actId = h2.actId)
	WHERE b.hokId = ".mysqli_real_escape_string($db,$Id)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId, h1.hisId
 ) uit on (b.hisId = uit.hisv)
 join tblHistorie h on (h.hisId = b.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join (
		SELECT st.schaapId, h.hisId, datum
		FROM tblStal st
		 join tblHistorie h on (h.stalId = st.stalId)
		WHERE h.actId = 4 and h.skip = 0
	 ) spn on (st.schaapId = spn.schaapId)
WHERE b.hokId = ".mysqli_real_escape_string($db,$Id)." and isnull(uit.hist) and isnull(spn.hisId)
") or die (mysqli_error($db));
	while ($hk = mysqli_fetch_assoc($zoek_nu_in_hok)) { $nu_geb = $hk['aant']; }

?>
<!-- 		HTML LINKER BOVEN GEDEELTE -->
<table border = 0>
<tr> <td colspan = 2 align = center> <h3> Afsluiten Foklammeren </h3></td> </tr>
<tr> <td colspan = 2> <?php echo ucfirst($hoknr_geb); ?> heeft <?php echo $totat_geb; ?> schapen voor het spenen geteld waarvan er nu nog <?php echo $nu_geb; ?> in zitten.</td> </tr>
<tr> <td> Startdatum </td><td><?php echo $dag1_geb; ?></td> </tr>
<tr> <td> Laatste uit verblijf </td><td><?php  if(isset($laatste_uit_geb)) { echo $laatste_uit_geb; } ?></td> </tr>
<tr> <td> Afsluitdatum </td><td><input type =text id = "datepicker1" name = "txtSluitdm1" size = 6 <?php if(isset($sluitdm1)) { echo 'value = '.$sluitdm1; } ?>  > </td> </tr>
<tr>
 <td>
Hoeveelheid voer </td><td valign = 'top'><input type ="text" name = "txtKg1" size = 6 value = <?php if(isset($txtKg1)) { echo $txtKg1; } ?>>
 </td>
</tr>
<tr>

 <td >Voer</td>
 <td>
<?php

//kzl voer
$queryStock = mysqli_query($db,"
SELECT min(i.inkId) inkId, a.naam, a.stdat, e.eenheid, sum(i.inkat-coalesce(v.vbrat,0)) vrdat
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblInkoop i on (i.enhuId = eu.enhuId)
 join tblArtikel a on (i.artId = a.artId)
 left join (
	SELECT v.inkId, sum(v.nutat*v.stdat) vbrat
	FROM tblVoeding v
	 join tblPeriode p on (p.periId = v.periId)
	 join tblHok ho on (ho.hokId = p.hokId)
	WHERE ho.lidId = ".mysqli_real_escape_string($db,$lidId)."
	GROUP BY v.inkId
 ) v on (i.inkId = v.inkId)
WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and i.inkat-coalesce(v.vbrat,0) > 0 and a.soort = 'voer'
GROUP BY a.naam, a.stdat, e.eenheid
ORDER BY a.naam
") or die (mysqli_error($db));
$name = 'kzlArtikel1';
$width= 250 ;
?>
<select name=<?php echo"$name";?> style="width:<? echo "$width";?>;\" >";
 <option></option>
<?php		while($row = mysqli_fetch_array($queryStock))
		{
$vrd = str_replace('.00', '', $row[vrdat]);
$stdrd = str_replace('.00', '', $row[stdat]);
		
$kzlkey="$row[inkId]";
$kzlvalue="$row[naam] &nbsp per $stdrd $row[eenheid] &nbsp ($vrd $row[eenheid])";

include "kzl.php";
		}
// EINDE kzl voer

?>
 </td>
</tr>
<tr>
 <td colspan =2 align = center> <input type =submit name = "knpSave1" value = "Opslaan" ></td> </tr>
</table>
<!-- 		EINDE	HTML LINKER BOVEN GEDEELTE 	EINDE	-->
<?php } // Einde Als er schapen zonder speendatum in het verblijf zitten ?>
	</td>
<?php

$zoek_laatste_afsluitdm_spn = mysqli_query($db,"
SELECT max(dmafsluit) dmstop
FROM tblPeriode
WHERE hokId = ".mysqli_real_escape_string($db,$Id)." and doelId = 2 and dmafsluit is not null
") or die (mysqli_error($db));
 
	while($stp_s = mysqli_fetch_assoc($zoek_laatste_afsluitdm_spn))	{ $dmstopspn = $stp_s['dmstop']; } if(!isset($dmstopspn)) { $dmstopspn = '1973-09-11'; } 

$zoek_startdatum_spn = mysqli_query($db,"
SELECT hisv.datum date, date_format(hisv.datum,'%d-%m-%Y') datum, hisv.hoknr
FROM (
	SELECT min(h.datum) datum, b.hokId, hk.hoknr
	FROM tblHistorie h
	 join tblStal st on (h.stalId = st.stalId)
	 join tblBezet b on (h.hisId = b.hisId)
	 join tblHok hk on (hk.hokId = b.hokId)
	 left join (
		SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
		FROM tblBezet b
		 join tblHistorie h1 on (b.hisId = h1.hisId)
		 join tblActie a1 on (a1.actId = h1.actId)
		 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
		 join tblActie a2 on (a2.actId = h2.actId)
		 join tblStal st on (h1.stalId = st.stalId)
		WHERE b.hokId = ".mysqli_real_escape_string($db,$Id)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
		GROUP BY b.bezId, st.schaapId, h1.hisId
	 ) uit on (uit.hisv = b.hisId)
	 left join tblHistorie ht on (ht.hisId = uit.hist)
	 join (
		SELECT st.schaapId, h.hisId, datum
		FROM tblStal st
		 join tblHistorie h on (h.stalId = st.stalId)
		WHERE h.actId = 4 and h.skip = 0
	 ) spn on (st.schaapId = spn.schaapId)
	 left join (
		SELECT st.schaapId, h.hisId, datum
		FROM tblStal st
		 join tblHistorie h on (h.stalId = st.stalId)
		WHERE h.actId = 3 and h.skip = 0
	 ) prnt on (st.schaapId = prnt.schaapId)
	WHERE b.hokId = ".mysqli_real_escape_string($db,$Id)."
	 and (isnull(ht.hisId) or ht.datum > '".mysqli_real_escape_string($db,$dmstopspn)."')
	 and h.datum >= spn.datum
	 and (isnull(prnt.schaapId) or h.datum < prnt.datum)
	GROUP BY b.hokId, hk.hoknr
) hisv
") or die (mysqli_error($db));
	while ($hk = mysqli_fetch_assoc($zoek_startdatum_spn)) { 
		$hoknr_spn = $hk['hoknr'];
		$date1_spn = $hk['date'];
		$totat_spn = $hk['aant']; } 
		
		if(isset($date1_spn)) { if ($date1_spn < $dmstopspn) { $dmstart_spn = $dmstopspn; } else { $dmstart_spn = $date1_spn; } 
			$todate = date_create($dmstart_spn); $dag1_spn = date_format($todate,'d-m-Y'); }

$zoek_laatste_uitdatum_spn = mysqli_query($db,"
SELECT date_format(max(ht.datum),'%d-%m-%Y') lastuit
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join (
    SELECT st.schaapId, datum
    FROM tblStal st
     join tblHistorie h on (h.stalId = st.stalId)
    WHERE h.actId = 4 and h.skip = 0
 ) spn on (st.schaapId = spn.schaapId)
 left join (
    SELECT st.schaapId, datum
    FROM tblStal st
     join tblHistorie h on (h.stalId = st.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (st.schaapId = prnt.schaapId)
 join (
    SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
     join tblActie a2 on (a2.actId = h2.actId)
    WHERE b.hokId = ".mysqli_real_escape_string($db,$Id)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, h1.hisId
 ) uit on (b.hisId = uit.hisv)
 left join tblHistorie ht on (ht.hisId = uit.hist)
WHERE b.hokId = ".mysqli_real_escape_string($db,$Id)."
 and ht.datum > '".mysqli_real_escape_string($db,$dmstopspn)."'
and (h.datum >= spn.datum)
and (isnull(prnt.schaapId) or h.datum < prnt.datum)
") or die (mysqli_error($db));
	while ($ht = mysqli_fetch_assoc($zoek_laatste_uitdatum_spn)) { 
		$laatste_uit_spn = $ht['lastuit']; }

// Als er schapen met speendatum in het verblijf zitten
if(isset($date1_spn)) {
$zoek_totaalaantal_spn = mysqli_query($db,"
SELECT count(distinct st.schaapId) aant
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join (
	SELECT st.schaapId, datum
	FROM tblStal st
	 join tblHistorie h on (h.stalId = st.stalId)
	WHERE h.actId = 4 and h.skip = 0
 ) spn on (st.schaapId = spn.schaapId)
 left join (
	SELECT st.schaapId, datum
	FROM tblStal st
	 join tblHistorie h on (h.stalId = st.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (st.schaapId = prnt.schaapId)
 left join (
	SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
	 join tblActie a2 on (a2.actId = h2.actId)
	WHERE b.hokId = ".mysqli_real_escape_string($db,$Id)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId, h1.hisId
 ) uit on (b.hisId = uit.hisv)
 left join tblHistorie ht on (ht.hisId = uit.hist)
WHERE b.hokId = ".mysqli_real_escape_string($db,$Id)."
 and (isnull(ht.hisId) or ht.datum > '".mysqli_real_escape_string($db,$dmstopspn)."')
and  h.datum >= spn.datum 
and (isnull(prnt.schaapId) or h.datum < prnt.datum)
") or die (mysqli_error($db));
	while ($hk = mysqli_fetch_assoc($zoek_totaalaantal_spn)) { $totat_spn = $hk['aant']; } 

$zoek_nu_in_hok = mysqli_query($db,"
SELECT count(b.bezId) aant
FROM tblBezet b
 left join (
	SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
	 join tblActie a2 on (a2.actId = h2.actId)
	WHERE b.hokId = ".mysqli_real_escape_string($db,$Id)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId, h1.hisId
 ) uit on (b.hisId = uit.hisv)
 join tblHistorie h on (h.hisId = b.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join (
	SELECT st.schaapId, datum
	FROM tblStal st
	 join tblHistorie h on (h.stalId = st.stalId)
	WHERE h.actId = 4 and h.skip = 0
 ) spn on (st.schaapId = spn.schaapId)
 left join (
	SELECT st.schaapId, datum
	FROM tblStal st
	 join tblHistorie h on (h.stalId = st.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (st.schaapId = prnt.schaapId)
WHERE b.hokId = ".mysqli_real_escape_string($db,$Id)." and isnull(uit.bezId)
 and (isnull(prnt.schaapId) or h.datum < prnt.datum)
") or die (mysqli_error($db));
	while ($hk = mysqli_fetch_assoc($zoek_nu_in_hok)) { $nu_spn = $hk['aant']; }

?>
	 <td>
<!-- 		HTML RECHTER BOVEN GEDEELTE -->
<table border = 0>
<tr> <td colspan = 2 align = center> <h3> Afsluiten Vleeslammeren </h3> </td> </tr>
<tr> <td colspan = 2> <?php echo ucfirst($hoknr_spn); ?> heeft <?php echo $totat_spn; ?> schapen na het spenen geteld waarvan er nu nog <?php echo $nu_spn; ?> in zitten.</td> </tr>
<tr> <td> Startdatum </td><td><?php if(isset($dag1_spn)) { echo $dag1_spn; } ?></td> </tr>
<tr> <td> Laatste uit verblijf </td><td><?php if(isset($laatste_uit_spn)) { echo $laatste_uit_spn; } ?></td> </tr>
<tr> <td> Afsluitdatum </td><td><input type =text id = "datepicker2" name = "txtSluitdm2" size = 6 value = <?php if(isset($sluitdm2)) { echo $sluitdm2; } ?> > </td> </tr>
<tr>
 <td>
Hoeveelheid voer </td><td valign = 'top'><input type ="text" name = "txtKg2" size = 6 value = <?php if(isset($txtKg2)) { echo $txtKg2; } ?> >
 </td>
</tr>
<tr>

 <td>Voer</td>
 <td>
<?php

//kzl voer
$queryStock = mysqli_query($db,"
SELECT min(i.inkId) inkId, a.naam, a.stdat, e.eenheid, sum(i.inkat-coalesce(v.vbrat,0)) vrdat
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblInkoop i on (i.enhuId = eu.enhuId)
 join tblArtikel a on (i.artId = a.artId)
 left join (
	SELECT v.inkId, sum(v.nutat*v.stdat) vbrat
	FROM tblVoeding v
	 join tblPeriode p on (p.periId = v.periId)
	 join tblHok ho on (ho.hokId = p.hokId)
	WHERE ho.lidId = ".mysqli_real_escape_string($db,$lidId)."
	GROUP BY v.inkId
 ) v on (i.inkId = v.inkId)
WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and i.inkat-coalesce(v.vbrat,0) > 0 and a.soort = 'voer'
GROUP BY a.naam, a.stdat, e.eenheid
ORDER BY a.naam
") or die (mysqli_error($db));
$name = 'kzlArtikel2';
$width= 250 ;
?>
<select name=<?php echo"$name";?> style="width:<? echo "$width";?>;\" >";
 <option></option>
<?php		while($row = mysqli_fetch_array($queryStock))
		{
$vrd = str_replace('.00', '', $row[vrdat]);
$stdrd = str_replace('.00', '', $row[stdat]);
		
$kzlkey="$row[inkId]";
$kzlvalue="$row[naam] &nbsp per $stdrd $row[eenheid] &nbsp ($vrd $row[eenheid])";

include "kzl.php";
		}
// EINDE kzl voer

?>
 </td></tr>

<tr> <td colspan =2 align = center> <input type =submit name = "knpSave2" value = "Opslaan" ></td> </tr>
</table>
<!-- 		EINDE	HTML RECHTER BOVEN GEDEELTE 	EINDE	-->
<?php } // Einde Als er schapen met speendatum in het verblijf zitten ?>
	</td>
	</tr> <!-- table1 Einde rij1 -->
	<tr>  <!-- table1 rij2 -->
	 <td height="25"></td>
	</tr>  <!-- table1 Einde rij2 -->
	<tr>  <!-- table1 rij3 -->
	<td>
<?php
$zoek_laatste_afsluitdm_volw = mysqli_query($db,"
SELECT max(dmafsluit) dmstop
FROM tblPeriode
WHERE hokId = ".mysqli_real_escape_string($db,$Id)." and doelId = 3 and dmafsluit is not null
") or die (mysqli_error($db));
 
	while($stp_v = mysqli_fetch_assoc($zoek_laatste_afsluitdm_volw))	{ $dmstopvolw = $stp_v['dmstop']; } if(!isset($dmstopvolw)) { $dmstopvolw = '1973-09-11'; }

	
$zoek_startdatum_volw = mysqli_query($db,"
SELECT hisv.datum date, date_format(min(hisv.datum),'%d-%m-%Y') datum, hisv.hoknr
FROM (
	SELECT min(h.datum) datum, b.hokId, hk.hoknr
	FROM tblHistorie h
	 join tblStal st on (h.stalId = st.stalId)
	 join tblBezet b on (h.hisId = b.hisId)
	 join tblHok hk on (hk.hokId = b.hokId)
	 left join (
		SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
		FROM tblBezet b
		 join tblHistorie h1 on (b.hisId = h1.hisId)
		 join tblActie a1 on (a1.actId = h1.actId)
		 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
		 join tblActie a2 on (a2.actId = h2.actId)
		 join tblStal st on (h1.stalId = st.stalId)
		WHERE b.hokId = ".mysqli_real_escape_string($db,$Id)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
		GROUP BY b.bezId, st.schaapId, h1.hisId
	 ) uit on (uit.hisv = b.hisId)
	 left join tblHistorie ht on (ht.hisId = uit.hist)
	 join (
		SELECT st.schaapId, h.hisId, datum
		FROM tblStal st
		 join tblHistorie h on (h.stalId = st.stalId)
		WHERE h.actId = 3 and h.skip = 0
	 ) prnt on (st.schaapId = prnt.schaapId)
	WHERE b.hokId = ".mysqli_real_escape_string($db,$Id)." and h.datum >= prnt.datum
	 and (isnull(ht.hisId) or ht.datum > '".mysqli_real_escape_string($db,$dmstopvolw)."')
	GROUP BY b.hokId, hk.hoknr
) hisv
") or die (mysqli_error($db));
	while ($hk = mysqli_fetch_assoc($zoek_startdatum_volw)) { 
		$hoknr_volw = $hk['hoknr'];
		$date1_volw = $hk['date']; }
		
		if(isset($date1_volw)) { if ($date1_volw < $dmstopvolw) { $dmstart_volw = $dmstopvolw; } else { $dmstart_volw = $date1_volw; } 
			$todate = date_create($dmstart_volw); $dag1_volw = date_format($todate,'d-m-Y'); }

$zoek_laatste_uitdatum_prnt = mysqli_query($db,"
SELECT date_format(max(ht.datum),'%d-%m-%Y') lastuit
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join (
    SELECT st.schaapId, datum
    FROM tblStal st
     join tblHistorie h on (h.stalId = st.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (st.schaapId = prnt.schaapId)
 join (
    SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
     join tblActie a2 on (a2.actId = h2.actId)
    WHERE b.hokId = ".mysqli_real_escape_string($db,$Id)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, h1.hisId
 ) uit on (b.hisId = uit.hisv)
 left join tblHistorie ht on (ht.hisId = uit.hist)
WHERE b.hokId = ".mysqli_real_escape_string($db,$Id)."
 and ht.datum > '".mysqli_real_escape_string($db,$dmstopvolw)."'
and h.datum >= prnt.datum
") or die (mysqli_error($db));
	while ($ht = mysqli_fetch_assoc($zoek_laatste_uitdatum_prnt)) { 
		$laatste_uit_prnt = $ht['lastuit']; }


// Als er volwassen schapen in het verblijf zitten
if(isset($date1_volw)) {		
$zoek_totaalaantal = mysqli_query($db,"
SELECT count(distinct st.schaapId) aant
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join (
	SELECT st.schaapId, datum
	FROM tblStal st
	 join tblHistorie h on (h.stalId = st.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (st.schaapId = prnt.schaapId)
 left join (
	SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
	 join tblActie a2 on (a2.actId = h2.actId)
	WHERE b.hokId = ".mysqli_real_escape_string($db,$Id)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
	GROUP BY b.bezId, h1.hisId
 ) uit on (b.hisId = uit.hisv)
 left join tblHistorie ht on (ht.hisId = uit.hist)
WHERE b.hokId = ".mysqli_real_escape_string($db,$Id)." and h.datum >= prnt.datum
 and (isnull(ht.hisId) or ht.datum > '".mysqli_real_escape_string($db,$dmstopvolw)."')
") or die (mysqli_error($db));
	while ($hk = mysqli_fetch_assoc($zoek_totaalaantal)) { $totat_volw = $hk['aant']; } 

$zoek_nu_in_hok = mysqli_query($db,"
SELECT count(b.bezId) aant
FROM tblBezet b
 left join (
	SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
	 join tblActie a2 on (a2.actId = h2.actId)
	WHERE b.hokId = ".mysqli_real_escape_string($db,$Id)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
	GROUP BY b.bezId, h1.hisId
 ) uit on (b.hisId = uit.hisv)
 left join tblHistorie ht on (ht.hisId = uit.hist)
 join tblHistorie h on (h.hisId = b.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join (
	SELECT st.schaapId, h.hisId, datum
	FROM tblStal st
	 join tblHistorie h on (h.stalId = st.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (st.schaapId = prnt.schaapId)
WHERE b.hokId = ".mysqli_real_escape_string($db,$Id)." and h.datum >= prnt.datum and (isnull(ht.hisId) or ht.datum > '".mysqli_real_escape_string($db,$dmstopvolw)."')
") or die (mysqli_error($db));
	while ($hk = mysqli_fetch_assoc($zoek_nu_in_hok)) { $nu_volw = $hk['aant']; }

?>
<!-- 		HTML LINKER ONDER GEDEELTE -->
<table border = 0>
<tr> <td colspan = 2 align = center> <h3> Afsluiten Moeder- en vaderdieren </h3></td> </tr>
<tr> <td colspan = 2> <?php echo ucfirst($hoknr_volw); ?> heeft <?php echo $totat_volw; ?> moeder- en vaderdieren geteld waarvan er nu nog <?php echo $nu_volw; ?> in zitten.</td> </tr>
<tr> <td> Startdatum </td><td><?php echo $dag1_volw; ?></td> </tr>
<tr> <td> Laatste uit verblijf </td><td><?php  if(isset($laatste_uit_prnt)) { echo $laatste_uit_prnt; } ?></td> </tr>
<tr> <td> Afsluitdatum </td><td><input type =text id = "datepicker3" name = "txtSluitdm3" size = 6 <?php if(isset($sluitdm1)) { echo 'value = '.$sluitdm1; } ?>  > </td> </tr>
<tr>
 <td>
Hoeveelheid voer </td><td valign = 'top'><input type ="text" name = "txtKg3" size = 6 value = <?php if(isset($txtKg3)) { echo $txtKg3; } ?>>
 </td>
</tr>
<tr>

 <td >Voer</td>
 <td>
<?php

//kzl voer
$queryStock = mysqli_query($db,"
SELECT min(i.inkId) inkId, a.naam, a.stdat, e.eenheid, sum(i.inkat-coalesce(v.vbrat,0)) vrdat
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblInkoop i on (i.enhuId = eu.enhuId)
 join tblArtikel a on (i.artId = a.artId)
 left join (
	SELECT v.inkId, sum(v.nutat*v.stdat) vbrat
	FROM tblVoeding v
	 join tblPeriode p on (p.periId = v.periId)
	 join tblHok ho on (ho.hokId = p.hokId)
	WHERE ho.lidId = ".mysqli_real_escape_string($db,$lidId)."
	GROUP BY v.inkId
 ) v on (i.inkId = v.inkId)
WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and i.inkat-coalesce(v.vbrat,0) > 0 and a.soort = 'voer'
GROUP BY a.naam, a.stdat, e.eenheid
ORDER BY a.naam
") or die (mysqli_error($db));
$name = 'kzlArtikel3';
$width= 250 ;
?>
<select name=<?php echo"$name";?> style="width:<? echo "$width";?>;\" >";
 <option></option>
<?php		while($row = mysqli_fetch_array($queryStock))
		{
$vrd = str_replace('.00', '', $row[vrdat]);
$stdrd = str_replace('.00', '', $row[stdat]);
		
$kzlkey="$row[inkId]";
$kzlvalue="$row[naam] &nbsp per $stdrd $row[eenheid] &nbsp ($vrd $row[eenheid])";

include "kzl.php";
		}
// EINDE kzl voer

?>
 </td>
</tr>
<tr>
 <td colspan =2 align = center> <input type =submit name = "knpSave3" value = "Opslaan" ></td> </tr>
</table>
<!-- 		EINDE	HTML LINKER ONDER GEDEELTE 	EINDE	-->
<?php } // Einde Als er volwassen schapen in het verblijf zitten ?>



	</td>
	</tr> <!-- table1 Einde rij3 -->
<?php //if (isset ($_POST['knpJa']))	{ Include "save_voer.php"; } ?>

	</table> <!-- Einde table1 -->

</TD>

<?php

Include "menu1.php"; } ?>


	</body>
	</html>