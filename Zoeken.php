<?php

/* 8-8-2014 Aantal karakters werknr variabel gemaakt 
11-8-2014 : veld type gewijzigd in fase 
20-2-2015 : login toegevoegd 
19-11-2015 geboorte datum kan ook aankoopdatum zijn 
23-11-2015 : Berekening breedte kzlWerknr verplaatst naar login.php */
$versie = '2-12-2016'; /* Dubbele records verwijderd als schaap opnieuw wordt aangevoerd */
$versie = '5-12-2016'; /* In historie alleen meldingen die niet zijn verwijderd.  and m.skip = 0 toegvoegd dus */
$versie = '14-1-2017'; /* In query geschiedenis levnr vervangen door schaapId. Bij Overplaatsing = aanwas is schaap t.t.v. overplaatsing lam en geen moeder zoals tot voor 14-1-2017 */
$versie = '15-1-2017'; /* In query geschiedenis hisId toegevoegd bij eerste en laatste worp */
$versie = "22-1-2017"; /* tblBezetting gewijzigd naar tblBezet */
$versie = '30-1-2017'; /* : Halsnummer toegevoegd  */
$versie = '16-2-2017'; /* hokken van volwassen dieren tonen (incl opnieuw lam ivm niet meer via tblPeriode)  LET OP : bij lam moet h1.actId = 2 worden uitgesloten en bij mdrs en vdrs h2.actId = 3 uitsluiten !!! */
$versie = '2-4-2017'; /* veld commentaar toegevoegd */
$versie = '5-8-2017';  /* Gem groei bij spenen toegevoegd */
$versie = '28-12-2017';  /* In uit verblijf halen van moeder- en vaderdieren in Historie opgenomen */
$versie = '20-07-2018';  /* Index kzlRam_ gewijzigd van werknr_ram naar schaapId */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '12-12-2018'; /* Eerste en laatste worp mag alleen eigen lammeren zijn => sl.lidId = ...lidId toegevoegd */
$versie = '15-2-2020'; /* tabelnaam gewijzigd van HIS naar his en van TOEL naar toel */
$versie = '23-5-2020'; /* unset gem groei spenen en afvoer en stamboeknummer. Geadopteerd aan historie toegevoegd */
$versie = '27-9-2020'; /* Handmatig omnummeren toegevoegd */
$versie = '27-2-2020'; /* SQL beveiligd met quotes en 'Transponder bekend' toegevoegd */
$versie = '11-4-2021'; /* Adoptie losgekoppeld van verblijf */
$versie = '11-4-2021'; /* Union SELECT uit.hist hisId, concat(ho.hoknr,' verlaten ') toel   aangepast. ht.actId = 7 toegevoegd en niet alleen volwassen dieren kunnen nu de status 'verlaten' hebben. */
$versie = '16-4-2023'; /* Bij omnummeren oud nummmer getoond incl. de melding van omnunummeren. Na omnummeren werden eerdere meldingen aan RVO niet meer getoond. Dit was nl. gekoppeld aan het oude levensnummer. Dit is hersteld door het oude levensnummer te koppelen. Zie veld 'wanneer wel omgenummerd' */
$versie = '14-5-2023'; /* Voorouders toegevoegd */
$versie = '23-6-2023'; /* schaapId werd te laat gezet. Na de link Wijzigen. schaapId wordt nu eerder gezet. */
$versie = '01-01-2024'; /* h.skip = 0 aangevuld bij tblHistorie */
$versie = "11-03-2024"; /* Bij geneste query uit 
join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId) gewijzgd naar
join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
I.v.m. historie van stalId 22623. Dit dier is eerst verkocht en met terugwerkende kracht geplaatst in verblijf Afmest 1 */
$versie = '30-11-2024'; /* In keuzelijst moeder- en vaderdieren  uitgeschaarde dieren wel tonen. zoek_afvoerstatus_mdr aangevuld met h.actId != 10 */
$versie = '14-12-2024'; /* 4 links t.b.v. jquery en ajax verplaatst naar header.php */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD align = "center" valign = "top"> 31-12-24 include login voor include header gezet */
$versie = '16-08-2025'; /* ubn van gebruiker toegevoegd. Per deze versie kan een gebruiker meerdere ubn's hebben */

 session_start();  ?>
<!DOCTYPE html>
<html>
<head>
<title>Raadplegen</title>
</head>
<body>

<?php
$titel = 'Schaap zoeken';
$file = "Zoeken.php";
include "login.php"; ?>

		<TD align = "center" valign = "top">
<?php
if (is_logged_in()) {

if(isset($_POST['knpSave_'])) { include "save_commentzoeken.php"; }
//include vw_Bezetting
//include vw_Hoklijsten

If (empty($_POST['kzlLevnr_']) ) 	{	$levnr = '';	} else {	$levnr = $_POST['kzlLevnr_'];		}
If (empty($_POST['kzlWerknr_']))	{	$werknr = '';	} else {	$werknr = $_POST['kzlWerknr_'];	}
If (!empty($_POST['kzlHalsnr_'])) {	$halsnr = $_POST['kzlHalsnr_'];	};
// tbv het posten en terug posten met dezelfde zoekcriterium
If (empty($_POST['kzlLevnr_'])) {$pstlevnr = NULL;} else {$pstlevnr = $_POST['kzlLevnr_'];}
If (empty($_POST['kzlWerknr_'])) {$pstwerknr = NULL;} else {$pstwerknr = $_POST['kzlWerknr_'];}

?>
<form action="Zoeken.php" method="post"> 
<table border = 0> <!-- Zoekgedeelte -->

<tr>
 <td> </td>	
 <td> <i><sub> Levensnummer </sub></i> </td>
 <td> </td>	
 <td> <i><sub> Werknr </sub></i> </td>
 <td> <i><sub> Halsnr </sub></i> </td>
 <td> </td>
 <td> <i><sub> Moederdier </sub></i> </td>
 <td> </td>
 <td> <i><sub> Vaderdier </sub></i> </td>
</tr>
<tr>
 <td> </td>

<!-- kzlLevensnummer -->
<?php
$kzlLam = mysqli_query($db,"
SELECT s.schaapId,  s.levensnummer
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.levensnummer is not null
GROUP BY s.schaapId, s.levensnummer
ORDER BY s.levensnummer
") or die (mysqli_error($db));
?> 
 <td>
 <select name= "kzlLevnr_" style= "width:130; height: 20px" class="search-select">
 <option></option>
 <option>Geen</option>
<?php		while($row = mysqli_fetch_array($kzlLam))
		{
		
			$opties= array($row['schaapId']=>$row['levensnummer']);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if(isset($_POST['kzlLevnr_']) && $_POST['kzlLevnr_'] == $key)
		{
			$keuze = ' selected ';
		}
				
		echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
			}
		
		}
?> </select>
</td>

<td> </td>
<!-- kzlWerknr -->
<?php  
$kzlLam = mysqli_query($db,"
SELECT s.schaapId, right(s.levensnummer,$Karwerk) werknr
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.levensnummer is not null
GROUP BY s.schaapId, right(s.levensnummer,$Karwerk)
ORDER BY right(s.levensnummer,$Karwerk)
") or die (mysqli_error($db)); ?>

 <td>			
 <select name="kzlWerknr_" style= "width:<?php echo $w_werknr; ?>;" >
 <option></option>
 <option>Geen</option>
<?php		while($row = mysqli_fetch_array($kzlLam))
		{
		
			$opties= array($row['schaapId']=>$row['werknr']);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if(isset($_POST['kzlWerknr_']) && $_POST['kzlWerknr_'] == $key)
		{
			$keuze = ' selected ';
		}
		
		echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
			}
		
		} ?>
 </select>
</td>

<!-- kzlHalsnr -->
<?php
$zoek_halsnr = mysqli_query($db,"
SELECT s.schaapId, concat(st.kleur,' ',st.halsnr) halsnr
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.kleur is not null and st.halsnr is not null and isnull(st.rel_best)
GROUP BY s.schaapId, concat(st.kleur,' ',st.halsnr)
ORDER BY st.kleur, st.halsnr
") or die (mysqli_error($db)); ?>

 <td>			
 <select name="kzlHalsnr_" style= "width: 80;" >
 <option></option>
<?php		while($row = mysqli_fetch_array($zoek_halsnr))
		{
		
			$opties= array($row['schaapId']=>$row['halsnr']);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if(isset($_POST['kzlHalsnr_']) && $_POST['kzlHalsnr_'] == $key)
		{
			$keuze = ' selected ';
		}
		
		echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
			}
		
		} ?>
 </select>
 </td>
<!-- Einde kzlHalsnr -->

 <td> </td>
<!-- kzlMoeder -->
<?php
$kzl = mysqli_query($db,"
SELECT mdr.schaapId, right(mdr.levensnummer,$Karwerk) werknr_ooi
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblVolwas v on (v.volwId = s.volwId)
 join tblSchaap mdr on (v.mdrId = mdr.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and mdr.levensnummer is not null
GROUP BY mdr.schaapId, right(mdr.levensnummer,$Karwerk)
ORDER BY right(mdr.levensnummer,$Karwerk)
") or die (mysqli_error($db));?>

 <td>
 <select name= "kzlOoi_" style= "width:<?php echo $w_werknr;?> " >
 <option></option>
<?php		while($row = mysqli_fetch_array($kzl))
		{
		
			$opties= array($row['schaapId']=>$row['werknr_ooi']);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if(isset($_POST['kzlOoi_']) && $_POST['kzlOoi_'] == $key)
		{
			$keuze = ' selected ';
		}
				
		echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
			}
		
		} ?>
  </select> 
  </td>

<!-- kzlVader -->
 <?php
$kzl = mysqli_query($db,"
SELECT vdr.schaapId, right(vdr.levensnummer,$Karwerk) werknr_ram
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblVolwas v on (v.volwId = s.volwId)
 join tblSchaap vdr on (v.vdrId = vdr.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and vdr.levensnummer is not null
GROUP BY vdr.schaapId, right(vdr.levensnummer,$Karwerk)
ORDER BY right(vdr.levensnummer,$Karwerk)
") or die (mysqli_error($db)); ?>

 <td>
 <select name="kzlRam_" style= "width:<?php echo $w_werknr;?>;" >
 <option></option>	
<?php		while($row = mysqli_fetch_array($kzl))
		{
		
			$opties= array($row['schaapId']=>$row['werknr_ram']);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if(isset($_POST['kzlRam_']) && $_POST['kzlRam_'] == $key)
		{
			$keuze = ' selected ';
		}
				
		echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
			}
		
		} ?>
 </select> </td>

<?php $toon_historie = mysqli_query($db,"SELECT histo FROM tblLeden WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' ") or die (mysqli_error($db));
	while ( $hi = mysqli_fetch_assoc($toon_historie)) { $histo = $hi['histo'];} ?>
	
 <td width = 50></td>
 <td> Historie tonen : <input type = radio name = 'radHis_' value = 0 
		<?php if(!isset($_POST['knpZoek_']) && !isset($_POST['knpSave_']) && $histo == 0) { echo "checked"; } 
		 else if(isset($_POST['radHis_']) && $_POST['radHis_'] == 0 ) { echo "checked"; } ?> title = "Standaard tonen van historie te wijzigen in systeemgegevens"> Nee
	 <input type = radio name = "radHis_" value = 1
		<?php if(!isset($_POST['knpZoek_']) && !isset($_POST['knpSave_']) && $histo == 1) { echo "checked"; }
		 else if(isset($_POST['radHis_']) && $_POST['radHis_'] == 1 ) { echo "checked"; } ?> title = "Standaard tonen van historie te wijzigen in systeemgegevens"> Ja 
		
		<?php if(isset($_POST['knpZoek_']) || isset($_POST['knpSave_']) ) { $historie = $_POST['radHis_'];} else { $historie = 0; }  ?>	
 </td>
 <td width="15"></td>
 <td> Voorouders tonen : <input type = radio name = 'radOud_' value = 0 
		<?php if(isset($_POST['radOud_']) && $_POST['radOud_'] == 0) { echo "checked"; } ?> > Nee
	 <input type = radio name = "radOud_" value = 1
		<?php  if(!isset($_POST['radOud_']) || (isset($_POST['radOud_']) && $_POST['radOud_'] == 1 ) ) { echo "checked"; } ?> > Ja 
		
		<?php if(isset($_POST['knpZoek_']) || isset($_POST['knpSave_']) ) { $voorOud = $_POST['radOud_'];} else { $voorOud = 0; }  ?>	
 </td> 
</tr>

<tr>
<td colspan = 9 align = "center">
<input type = "submit" name= "knpZoek_" value = "zoeken">
</td>
</tr>


</table> <!-- Einde Zoekgedeelte -->
<table border = 0> <!-- Gegevens van het schaap -->
<?php
// Om alle resultaten uit tblSchapen te voorkomen moet minimaal 1 keuze zijn gemakt
if ((isset($_POST['knpZoek_']) || isset($_POST['knpSave_'])) && (!empty($levnr) || !empty($werknr) || !empty($halsnr) || !empty($_POST['kzlOoi_']) || !empty($_POST['kzlRam_'])) ) {

If ($levnr == 'Geen')
{	$reslevnr = "isnull(s.levensnummer)";	$where = $reslevnr; }	
else If (!empty($levnr))
{	$reslevnr = "s.schaapId = $levnr ";	$where = $reslevnr; }


If ($werknr == 'Geen')
{	$reswerknr = " isnull(s.levensnummer) "; }
else if (!empty($werknr))
{	$reswerknr = "s.schaapId = $_POST[kzlWerknr_] ";	}
	if(isset($where) && isset($reswerknr)) { $where = $where." and ".$reswerknr; } else if(isset($reswerknr)) { $where = $reswerknr; }

If (!empty($_POST['kzlHalsnr_']))
{	$reshalsnr = "s.schaapId = ".$halsnr;	}
	if(isset($where) && isset($reshalsnr)) { $where = $where." and ".$reshalsnr; } else if(isset($reshalsnr)) { $where = $reshalsnr; }


If (!empty($_POST['kzlOoi_']))
{	$resooi = "mdr.schaapId = $_POST[kzlOoi_] ";	}
	if(isset($where) && isset($resooi)) { $where = $where." and ".$resooi; } else if(isset($resooi)) { $where = $resooi; }

		
If (!empty($_POST['kzlRam_']))
{	$resram = "vdr.schaapId = $_POST[kzlRam_] ";	}
	if(isset($where) && isset($resram)) { $where = $where." and ".$resram; } else if(isset($resram)) { $where = $resram; }
 ##echo '$where = '.$where;	

// Zoeken naar eerste datum en een eventuele aankoopdatum
$aankoop = mysqli_query($db,"
SELECT date_format(hg.datum,'%d-%m-%Y') datum, koop.datum dmkoop
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 left join tblVolwas v on (v.volwId = s.volwId)
 left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
 left join tblSchaap vdr on (v.vdrId = vdr.schaapId)
 left join (
	SELECT st.schaapId, h.datum 
	FROM tblHistorie h
	 join tblStal st on (h.stalId = st.stalId)
	WHERE h.actId = 1 and h.skip = 0
 ) hg on (hg.schaapId = s.schaapId)
 left join (
	SELECT st.schaapId, h.datum 
	FROM tblHistorie h
	 join tblStal st on (h.stalId = st.stalId)
	WHERE h.actId = 2 and h.skip = 0
 ) koop on (koop.schaapId = s.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and $where
") or die (mysqli_error($db));
  
 while ($lijn = mysqli_fetch_assoc($aankoop))
			{	$gebdm = $lijn['datum'];  
				$dmkoop = $lijn['dmkoop']; }
// Einde Controleren op aankoop door zoeken in tblBezetting

// schapen met status onbekend
//where isnull(vb.bezetId) and s.fase = 'lam' and isnull(afleverdm) and isnull(uitvaldm)

$zoek_schaapId = mysqli_query($db,"
SELECT s.schaapId
FROM tblSchaap s
 left join tblVolwas v on (v.volwId = s.volwId)
 left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
 left join tblSchaap vdr on (v.vdrId = vdr.schaapId)
WHERE $where
") or die (mysqli_error($db));

while($zsI = mysqli_fetch_assoc($zoek_schaapId))
	{
		$schaapId = $zsI['schaapId']; }


$result = mysqli_query($db,"
SELECT s.transponder, concat(st.kleur,' ',st.halsnr) halsnr, s.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk) werknr, s.fokkernr, right(mdr.levensnummer,$Karwerk) werknr_ooi, right(vdr.levensnummer,$Karwerk) werknr_ram, r.ras, s.geslacht, ouder.datum dmaanw, coalesce(lower(haf.actie),'aanwezig') status, haf.af,
hs.datum dmspn, hs.kg spnkg, afl.datum dmafl, afl.kg aflkg, hg.datum dmgeb, date_format(hg.datum,'%d-%m-%Y') gebdm, hg.kg gebkg, date_format(aanv1.datum,'%d-%m-%Y') aanvdm, aanv1.datum dmaanv, aanv1.kg aankkg

FROM tblSchaap s
 left join tblVolwas v on (v.volwId = s.volwId)
 left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
 left join tblSchaap vdr on (v.vdrId = vdr.schaapId)
 join (
	SELECT min(stalId) stalId, schaapId
	FROM tblStal
	WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
	GROUP BY schaapId
 ) st1 on (s.schaapId = st1.schaapId)
 join (
	SELECT max(stalId) stalId, schaapId
	FROM tblStal
	WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
	GROUP BY schaapId
 ) stm on (s.schaapId = stm.schaapId)
 join tblStal st on (stm.stalId = st.stalId)
 left join (
	SELECT st.schaapId, h.datum, h.kg
	FROM tblStal st 
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 1 and h.skip = 0
 ) hg on (s.schaapId = hg.schaapId)
 left join (
	SELECT st.stalId, h.datum, h.kg
	FROM tblStal st 
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 2 and h.skip = 0
 ) aanv1 on (st1.stalId = aanv1.stalId)
 left join (
	SELECT st.stalId, h.datum, h.kg
	FROM tblStal st 
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4 and h.skip = 0
 ) hs on (st.stalId = hs.stalId)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st 
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) ouder on (s.schaapId = ouder.schaapId)
 left join (
	SELECT st.stalId, a.actie, a.af
	FROM tblActie a
	 join tblHistorie h on (a.actId = h.actId)
	 join tblStal st on (h.stalId = st.stalId)
	 join tblSchaap s on (st.schaapId = s.schaapId)
	 left join tblVolwas v on (v.volwId = s.volwId)
	 left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
	 left join tblSchaap vdr on (v.vdrId = vdr.schaapId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId) /* tblSchaap mdr en tblSchaap vdr is voor als er op moeder of vader wordt gezocht*/."' and $where and a.af = 1 and h.skip = 0
 ) haf on (haf.stalId = st.stalId)
 left join (
	SELECT st.schaapId, h.datum, h.kg
	FROM tblHistorie h 
	 join 
	 (
		SELECT s.levensnummer, min(h.hisId) hisId 
		FROM tblStal st
		 join tblSchaap s on (st.schaapId = s.schaapId)
		 join tblHistorie h on (st.stalId = h.stalId)
		 left join tblVolwas v on (v.volwId = s.volwId)
		 left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
		 left join tblSchaap vdr on (v.vdrId = vdr.schaapId)
		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId) /* tblSchaap mdr en tblSchaap vdr is voor als er op moeder of vader wordt gezocht*/."' and $where and h.actId = 12 and h.skip = 0
		GROUP BY s.levensnummer
	 ) afl on (afl.hisId = h.hisId)
	 join tblStal st on (h.stalId = st.stalId)
	WHERE h.skip = 0
 ) afl on (afl.schaapId = s.schaapId)
 left join tblRas r on(s.rasId = r.rasId)
WHERE $where

ORDER BY if(isnull(s.levensnummer),'Geen',''), dmgeb desc, status
") or die (mysqli_error($db)); ?>
			
				
				
<tr style = "font-size:12px;">
 <th width = 0 height = 30></th>
 <th width = 1 height = 30></th>
 <th width = 90 height = 30></th>
 <th style = "text-align:center;"valign="bottom";width= 100>Transponder<br>bekend<hr></th>

 <th style = "text-align:center;"valign="bottom";width= 100>Halsnr<hr></th>

 <th style = "text-align:center;"valign="bottom";width= 80>Werknr<hr></th>

 <th style = "text-align:center;"valign="bottom";width= 50> <?php if(isset($gebdm)) { echo 'Geboortedatum'; } else { echo 'Aanvoerdatum'; } ?><hr></th>

 <th style = "text-align:center;"valign="bottom";width= 50>Generatie<hr></th>

 <th style = "text-align:center;"valign="bottom";width= 50>Ras<hr></th>

 <th style = "text-align:center;"valign="bottom";width= 50>Geslacht<hr></th>

 <th style = "text-align:center;"valign="bottom";width= 200>Werknr ooi<hr></th>

 <th style = "text-align:center;"valign="bottom";width= 200>Werknr ram<hr></th>

 <th style = "text-align:center;"valign="bottom";width= 60>Status<hr></th>

 <th style = "text-align:center;"valign="bottom";width= 60>Gem Groei speen<hr></th>

 <th style = "text-align:center;"valign="bottom";width= 60>Gem Groei aflev<hr></th>

 <th style = "text-align:center;"valign="bottom";width= 60>Stamboeknr<hr></th>
 <td width = 60 style = "font-size:15px;" align="center" > <a href=' <?php echo $url; ?>UpdSchaap.php?pstschaap=<?php echo $schaapId; ?>' style = "color : blue">Wijzigen</a> </td>

</tr>

<?php
while($row = mysqli_fetch_assoc($result))
{
	$transponder = $row['transponder']; if(isset($transponder)) {$transp = 'Ja'; } else {$transp = 'Nee'; }
	//$schaapId = $row['schaapId'];
	$levnr = $row['levensnummer'];
	$werknr = $row['werknr'];
	$fokkernr = $row['fokkernr']; if(isset($fokkernr)) { $stamb = $fokkernr.' - '.$werknr; }
	$halsnr = $row['halsnr'];
	$gebdm = $row['gebdm'];
	$ras = $row['ras'];
	$sekse = $row['geslacht'];
	if(isset($row['dmaanw'])) { if($sekse == 'ooi' ) { $fase = 'moeder'; } else { $fase = 'vader'; } } else { $fase = 'lam';}
	$mdr = $row['werknr_ooi'];
	$vdr = $row['werknr_ram'];
	$status = $row['status'];
	$opstal = $row['af'];
	$dmspn = $row['dmspn'];
	$spnkg = $row['spnkg'];
	$dmafl = $row['dmafl'];
	$aflkg = $row['aflkg'];
	$dmgeb = $row['dmgeb'];
	$gebkg = $row['gebkg'];
	$dmaanv = $row['dmaanv'];
	$aanvdm = $row['aanvdm'];
	$aankkg = $row['aankkg'];
	if(isset($dmgeb)) { $dmstart = $dmgeb; } else { $dmstart = $dmaanv;}
	if(isset($gebkg)) { $startkg = $gebkg; } else { $startkg = $aankkg;}

	$dagen_spn = strtotime($dmspn)-strtotime($dmstart); $dgn_s = floor($dagen_spn/3600/24);
	$dagen_afl = strtotime($dmafl)-strtotime($dmstart); $dgn_a = floor($dagen_afl/3600/24);

	if($dgn_s >0 && $startkg > 0) { $gemgr_s = round((($spnkg-$startkg)/($dgn_s)*1000),2) ; } 


	if($dgn_a >0 && $startkg > 0) { $gemgr_a = round((($aflkg-$startkg)/($dgn_a)*1000),2) ; } ?>
				
<tr align = "center">	
 <td width = 0> </td>
<td width = 1> </td>
 <td width = 90> </td>	   
 <td width = 150 style = "font-size:15px;"> <?php echo $transp; ?> <br> </td>

 <td width = 150 style = "font-size:15px;"> <?php echo $halsnr; ?> <br> </td>

 <td width = 100 style = "font-size:15px;"> <?php echo $werknr; ?> <br> </td>
   
 <td width = 100 style = "font-size:15px;"> <?php if(isset($gebdm)) { echo $gebdm; } else { echo $aanvdm; } ?> <br> </td>

 <td width = 100 style = "font-size:15px;"> <?php echo $fase; ?> <br> </td>

 <td width = 100 style = "font-size:15px;"> <?php echo $ras; ?> <br> </td>

 <td width = 100 style = "font-size:15px;"> <?php echo $sekse; ?> </td>

 <td width = 100 style = "font-size:15px;"> <?php echo $mdr; ?> </td>

 <td width = 100 style = "font-size:15px;"> <?php echo $vdr; ?> </td>

<?php if($status == 'aanwezig' && $fase == 'moeder') { ?>	   
 <td><a href=' <?php echo $url; ?>Ooikaart.php?pstId=<?php echo $schaapId; ?>' style = "color : blue">
	   <?php echo $status; ?></a></td>
<?php } else { ?>	   
 <td width = 160 style = "font-size:15px;"> <?php echo $status; ?> </td>
<?php } ?>	   

 <td width = 100 style = "font-size:15px;"> <?php if(isset($gemgr_s) ) { echo $gemgr_s; unset($gemgr_s); } ?> </td>

 <td width = 100 style = "font-size:15px;"> <?php if(isset($gemgr_a) && $status == 'afgeleverd') { echo $gemgr_a; unset($gemgr_a); } ?> </td>

 <td width = 100 style = "font-size:12px;"> <?php if(isset($stamb) ) { echo $stamb; unset($stamb); } ?> </td>

	   <?php 
if ($status == 'aanwezig' || $status == 'uitgeschaard')
{	?>
 <td width="450">
	<a href=' <?php echo $url; ?>OmnSchaap.php?pstschaap=<?php echo $schaapId; ?>' style = "color : blue">Omnummeren</a>
   </td> <?php
   
   
} ?>
	   

<?php	} // Einde while($row = mysqli_fetch_assoc($result)) ?> 				
	   
<?php  
if (!isset($schaapId))
{ 
$fout = "Het zoek criterium heeft geen resultaten opgeleverd. Pas het zoekcriterum eventueel aan.";
} 
} ?>
	   
</tr>
</table>  <!-- Einde Gegevens van het schaap -->

<?php if ((isset($_POST['knpZoek_']) || isset($_POST['knpSave_'])) && $historie == 1 && (!empty($_POST['kzlLevnr_']) || !empty($_POST['kzlWerknr_']) || !empty($_POST['kzlHalsnr_'])) ) { ?>	
<table border = 0>  <!-- Historie van het schaap -->

<!-- Om een lege tabel te verbergen moet minimaal 1 keuze zijn gemaakt -->

<tr height = 50>
</tr>

<tr><td colspan = 7 ><hr></td></tr>
<tr><td colspan = 2 >Historie van het schaap : </td> </tr>
<tr style = "font-size : 13px;">
 <th>Ubn<hr></th>
 <th>Datum<hr></th>
 <th>Actie<hr></th>
 <th>Generatie<hr></th>
<!--<th>Id<hr></th>-->
 <th>Gewicht<hr></th>
 <th align = "left"> &nbsp &nbsp &nbsp Toelichting<hr></th>
 <th align = "left"> &nbsp &nbsp &nbsp Commentaar<hr></th>
 <th align = "left"> <input type="submit" name="knpSave_" style="font-size: 11px;" value="Opslaan"> </th>
 <th width = 1></th>
</tr>

<?php if(isset($schaapId)) { // Zoekcriterium moet bestaan
$geschiedenis = mysqli_query($db,"
SELECT his.hisId, his.ubn, his.levensnummer, his.geslacht, his.datum, his.date, his.actId, his.actie, his.actie_if, his.kg, date_format(his.dmaanw,'%Y-%m-%d 00:00:00') dmaanw, toel.toel, his.hisId hiscom, comment
FROM
(
	SELECT h.hisId, u.ubn, s.levensnummer, s.geslacht, date_format(h.datum, '%d-%m-%Y') datum, h.datum date, h.actId, a.actie, right(a.actie,4) actie_if, h.kg, ouder.datum dmaanw, h.comment
	FROM tblSchaap s
	 join tblStal st on (st.schaapId = s.schaapId)
	 join tblUbn u on (st.ubnId = u.ubnId)
	 join tblHistorie h on (st.stalId = h.stalId)
	 join tblActie a on (a.actId = h.actId)
	 left join (
		SELECT s.schaapId, h.datum 
		FROM tblSchaap s 
		 join tblStal st on (st.schaapId = s.schaapId)
		 join tblHistorie h on (h.stalId = st.stalId)
		WHERE h.actId = 3 and h.skip = 0 and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	 ) ouder on (ouder.schaapId = s.schaapId)
	WHERE s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.skip = 0
	 and not exists (
		SELECT datum 
		FROM tblHistorie ha 
		 join tblStal st on (ha.stalId = st.stalId)
		 join tblSchaap s on (st.schaapId = s.schaapId)
		WHERE actId = 2 and h.skip = 0 and h.datum = ha.datum and h.actId = ha.actId+1 and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."')

  union

	SELECT h.hisId, u.ubn, s.levensnummer, s.geslacht, date_format(h.datum, '%d-%m-%Y') datum, h.datum date, h.actId, a.actie, right(a.actie,4) actie_if, h.kg, ouder.datum, h.comment
	FROM tblHistorie h
	 join tblStal st on (st.stalId = h.stalId)
	 join tblUbn u on (st.ubnId = u.ubnId)
	 join tblSchaap s on (st.schaapId = s.schaapId)
	 join tblActie a on (a.actId = h.actId)
	 left join (
		SELECT s.schaapId, h.datum 
		FROM tblSchaap s 
		 join tblStal st on (st.schaapId = s.schaapId)
		 join tblHistorie h on (h.stalId = st.stalId)
		WHERE h.actId = 3 and h.skip = 0 and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	 ) ouder on (ouder.schaapId = s.schaapId)
	WHERE s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and h.actId = 1 and h.skip = 0
) his
left join 
(
	SELECT 'adoptie lammeren' qry, h.hisId, concat('Bij ooi ', right(mdr.levensnummer,$Karwerk)) toel
	FROM tblHistorie h
	 join impAgrident vp on (h.datum = vp.datum)
	 join tblStal st on (h.stalId = st.stalId)
	 join tblSchaap s on (st.schaapId = s.schaapId and vp.levensnummer = s.levensnummer)
	 left join (
	 	SELECT levensnummer 
	 	FROM tblSchaap mdr 
	 	 join tblStal st on (mdr.schaapId = st.schaapId)
	 	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	 ) mdr on (vp.moeder = mdr.levensnummer)
	WHERE h.actId = 15 and h.skip = 0 and vp.actId = 15 and vp.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'

Union

	SELECT 'lammeren in hok geplaatst excl. adoptie' qry, h.hisId, concat('Geplaatst in ', lower(ho.hoknr),' voor ',datediff(coalesce(ht.datum,curdate()), h.datum), If(datediff(coalesce(ht.datum,curdate()), h.datum) = 1, ' dag', ' dagen')) toel

	FROM tblHok ho
	 join tblBezet b on (b.hokId = ho.hokId)
	 join tblHistorie h on (h.hisId = b.hisId)
	 join tblActie a on (a.actId = h.actId)
	 join tblStal st on (st.stalId = h.stalId)
	 left join (
		SELECT h1.hisId hisv, min(h2.hisId) hist
		FROM tblHistorie h1
		 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
		 join tblStal st on (st.stalId = h1.stalId)
		 join tblSchaap s on (s.schaapId = st.schaapId)
		 join tblActie a1 on (a1.actId = h1.actId)
		 join tblActie a2 on (a2.actId = h2.actId)
		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
		and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h1.actId != 2
		GROUP BY h1.hisId
	 ) uit on (uit.hisv = b.hisId)
	 left join tblHistorie ht on (ht.hisId = uit.hist)
	  left join (
		SELECT st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 3 and h.skip = 0
	 ) prnt on (prnt.schaapId = st.schaapId)
	WHERE a.aan = 1 and h.skip = 0 and h.actId != 15 and ho.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	 and (isnull(prnt.schaapId) or (prnt.datum > h.datum))

Union

	SELECT 'Volwassenen in hok geplaatst' qry, h.hisId, concat('Geplaatst in ', lower(ho.hoknr),' voor ',datediff(coalesce(ht.datum,curdate()), h.datum), If(datediff(coalesce(ht.datum,curdate()), h.datum) = 1, ' dag', ' dagen')) toel

	FROM tblHok ho
	 join tblBezet b on (b.hokId = ho.hokId)
	 join tblHistorie h on (h.hisId = b.hisId)
	 join tblActie a on (a.actId = h.actId)
	 join tblStal st on (st.stalId = h.stalId)
	 left join (
		SELECT h1.hisId hisv, min(h2.hisId) hist
		FROM tblHistorie h1
		 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
		 join tblStal st on (st.stalId = h1.stalId)
		 join tblSchaap s on (s.schaapId = st.schaapId)
		 join tblActie a1 on (a1.actId = h1.actId)
		 join tblActie a2 on (a2.actId = h2.actId)
		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
		and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
		GROUP BY h1.hisId
	 ) uit on (uit.hisv = b.hisId)
	 left join tblHistorie ht on (ht.hisId = uit.hist)
	 join (
		SELECT st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 3 and h.skip = 0
	 ) prnt on (prnt.schaapId = st.schaapId)
	WHERE a.aan = 1 and h.skip = 0 and ho.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	 and prnt.datum <= h.datum

Union

	SELECT 'Volwassenen hok verlaten' qry, uit.hist hisId, concat(ho.hoknr,' verlaten ') toel

	FROM tblHok ho
	 join tblBezet b on (b.hokId = ho.hokId)
	 join tblHistorie h on (h.hisId = b.hisId)
	 join tblActie a on (a.actId = h.actId)
	 join tblStal st on (st.stalId = h.stalId)
	 join (
		SELECT h1.hisId hisv, min(h2.hisId) hist
		FROM tblHistorie h1
		 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
		 join tblStal st on (st.stalId = h1.stalId)
		 join tblSchaap s on (s.schaapId = st.schaapId)
		 join tblActie a1 on (a1.actId = h1.actId)
		 join tblActie a2 on (a2.actId = h2.actId)
		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
		and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
		GROUP BY h1.hisId
	 ) uit on (uit.hisv = b.hisId)
	 left join tblHistorie ht on (ht.hisId = uit.hist)
	WHERE a.aan = 1 and h.skip = 0 and ho.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	 and ht.actId = 7

Union

	SELECT 'toel_afvoer excl dood met een reden' qry, h.hisId, p.naam
	FROM tblActie a
	 join tblHistorie h on (a.actId = h.actId)
	 join tblStal st on (st.stalId = h.stalId)
	 join tblSchaap s on (s.schaapId = st.schaapId)
	 join tblRelatie r on (st.rel_best = r.relId)
	 join tblPartij p on (r.partId = p.partId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a.af = 1 and h.skip = 0
	 and (h.actId != 14 or (h.actId = 14 and isnull(s.redId)))

Union

	SELECT 'toel_afvoer dood met een reden' qry, h.hisId, re.reden
	FROM tblActie a
	 join tblHistorie h on (a.actId = h.actId)
	 join tblStal st on (st.stalId = h.stalId)
	 join tblSchaap s on (s.schaapId = st.schaapId)
	 join tblReden re on (s.redId = re.redId)
	 join tblRelatie r on (st.rel_best = r.relId)
	 join tblPartij p on (r.partId = p.partId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a.af = 1 and h.skip = 0
	 and h.actId = 14 and s.redId is not null

Union

	SELECT 'medicatie' qry, n.hisId, concat(round(sum(n.nutat*n.stdat),2),' ', e.eenheid,'  ', a.naam,'  ',coalesce(i.charge,'')) toel
	FROM tblNuttig n
	 join tblInkoop i on (n.inkId = i.inkId)
	 join tblArtikel a on (a.artId = i.artId)
	 join tblEenheiduser eu on (eu.enhuId = a.enhuId)
	 join tblEenheid e on (e.eenhId = eu.eenhId)
	WHERE eu.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	GROUP BY n.hisId, e.eenheid, a.naam, i.charge

Union

	SELECT 'omnummeren' qry,  h.hisId, concat('Oud nummer ', h.oud_nummer) toel
	From tblHistorie h
	 join tblStal st on (h.stalId = st.stalId)
	Where st.lidId = 13 and h.actId = 17 and h.skip = 0

) toel
on (his.hisId = toel.hisId)

UNION 

SELECT NULL hisId, u.ubn, s.levensnummer, s.geslacht, date_format(p.dmafsluit,'%d-%m-%Y') datum, p.dmafsluit date, NULL actId, 'Gevoerd' actie, NULL actie_if, NULL kg, NULL dmaanw, concat(coalesce(round(datediff(ht.datum,hv.datum) * vr.kg_st,2),0), ' kg ', lower(a.naam), ' t.b.v. ', lower(h.hoknr)) toel, NULL hiscom, NULL comment
FROM tblBezet b
 join tblPeriode p on (p.periId = b.periId)
 join tblHok h on (h.hokId = p.hokId)
 join tblHistorie hv on (hv.hisId = b.hisId)
 join tblStal st on (st.stalId = hv.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
 join tblSchaap s on (st.schaapId = s.schaapId)
 join
	 (
		SELECT b.bezId, min(his.hisId) hist
		FROM tblPeriode p
		 join tblBezet b on (p.periId = b.periId)
		 join tblHistorie h on (h.hisId = b.hisId)
		 join tblStal st on (st.stalId = h.stalId)
		 join tblHistorie his on (st.stalId = his.stalId)
		 join tblActie a on (a.actId = his.actId)
		 join tblSchaap s on (s.schaapId = st.schaapId)
		WHERE h.skip = 0 and his.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
		 and (a.aan = 1 or a.uit = 1)
		 and his.hisId > b.hisId
		GROUP BY b.bezId
	 ) uit on (uit.bezId = b.bezId)
 join tblHistorie ht on (ht.hisId = uit.hist)
 join 
(
	SELECT v.periId, v.inkId, v.nutat/sum(datediff(ht.datum,hv.datum)) kg_st
	FROM tblVoeding v
	 join tblPeriode p on (v.periId = p.periId)
	 join tblBezet b on (p.periId = b.periId)
	 join tblHistorie hv on (hv.hisId = b.hisId)
	 join
	 (
		SELECT b.bezId, min(his.hisId) hist
		FROM tblBezet b
		 join tblHistorie h on (h.hisId = b.hisId)
		 join tblStal st on (st.stalId = h.stalId)
		 join tblHistorie his on (st.stalId = his.stalId)
		 join tblActie a on (a.actId = his.actId)
		 join (
			SELECT b.periId
			FROM tblBezet b
			 join tblHistorie h on (b.hisId = h.hisId)
			 join tblStal st on (h.stalId = st.stalId)
			 join tblSchaap s on (s.schaapId = st.schaapId)
			WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
		 ) peri_obv_schaap on (peri_obv_schaap.periId = b.periId)
		WHERE (a.aan = 1 or a.uit = 1)
		 and his.hisId > b.hisId and h.skip = 0 and his.skip = 0
		GROUP BY b.bezId
	 ) uit on (uit.bezId = b.bezId)
	 join tblHistorie ht on (ht.hisId = uit.hist)
	GROUP BY v.periId, v.inkId
) vr on (vr.periId = b.periId)
 join tblInkoop i on (i.inkId = vr.inkId)
 join tblArtikel a on (a.artId = i.artId)

UNION 

SELECT m.hisId, u.ubn, rs.levensnummer, s.geslacht, date_format(r.dmmeld,'%d-%m-%Y') datum, r.dmmeld date, NULL actId, 'Geboorte gemeld' actie, NULL actie_if, NULL kg, ouder.datum dmaanw, case when isnull(rs.meldnr) then concat('RVO meldt : ',rs.foutmeld) else concat('meldnr : ',rs.meldnr) end toel, NULL hiscom, NULL comment
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 join (
		SELECT max(rsp.respId) respId, rsp.reqId, s.schaapId, 'wanneer niet omgenummerd'
		FROM impRespons rsp
		 join tblSchaap s on (rsp.levensnummer = s.levensnummer)
		GROUP BY rsp.reqId, rsp.levensnummer

		UNION

		SELECT max(rsp.respId) respId, rsp.reqId, st.schaapId, 'wanneer wel omgenummerd'
		FROM impRespons rsp
		 join tblHistorie h on (rsp.levensnummer = h.oud_nummer)
		 join tblStal st on (h.stalId = st.stalId)
		GROUP BY rsp.reqId, rsp.levensnummer
	) id on (id.schaapId = s.schaapId and id.reqId = r.reqId)
 join impRespons rs on (id.respId = rs.respId )
 left join (
		SELECT s.schaapId, h.datum 
		FROM tblSchaap s 
		 join tblStal st on (st.schaapId = s.schaapId)
		 join tblHistorie h on (h.stalId = st.stalId)
		WHERE h.actId = 3 and h.skip = 0 and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	 ) ouder on (ouder.schaapId = s.schaapId)
 
WHERE r.dmmeld is not null and r.code = 'GER' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and h.skip = 0 and m.skip = 0

UNION 

SELECT m.hisId, u.ubn, rs.levensnummer, s.geslacht, date_format(r.dmmeld,'%d-%m-%Y') datum, r.dmmeld date, NULL actId, 'Aanvoer gemeld' actie, NULL actie_if, NULL kg, ouder.datum dmaanw, case when isnull(rs.meldnr) then concat('RVO meldt : ',rs.foutmeld) else concat('meldnr : ',rs.meldnr) end toel, NULL hiscom, NULL comment
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 join (
		SELECT max(rsp.respId) respId, rsp.reqId, s.schaapId, 'wanneer niet omgenummerd'
		FROM impRespons rsp
		 join tblSchaap s on (rsp.levensnummer = s.levensnummer)
		GROUP BY rsp.reqId, rsp.levensnummer

		UNION

		SELECT max(rsp.respId) respId, rsp.reqId, st.schaapId, 'wanneer wel omgenummerd'
		FROM impRespons rsp
		 join tblHistorie h on (rsp.levensnummer = h.oud_nummer)
		 join tblStal st on (h.stalId = st.stalId)
		GROUP BY rsp.reqId, rsp.levensnummer
	) id on (id.schaapId = s.schaapId and id.reqId = r.reqId)
 join impRespons rs on (id.respId = rs.respId )
 left join (
		SELECT s.schaapId, h.datum 
		FROM tblSchaap s 
		 join tblStal st on (st.schaapId = s.schaapId)
		 join tblHistorie h on (h.stalId = st.stalId)
		WHERE h.actId = 3 and h.skip = 0 and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	 ) ouder on (ouder.schaapId = s.schaapId)
 
WHERE r.dmmeld is not null and r.code = 'AAN' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and h.skip = 0 and m.skip = 0

UNION 

SELECT m.hisId, u.ubn, rs.levensnummer, s.geslacht, date_format(r.dmmeld,'%d-%m-%Y') datum, r.dmmeld date, NULL actId, 'Afvoer gemeld' actie, NULL actie_if, NULL kg, ouder.datum dmaanw, case when isnull(rs.meldnr) then concat('RVO meldt : ',rs.foutmeld) else concat('meldnr : ',rs.meldnr) end toel, NULL hiscom, NULL comment
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 join (
		SELECT max(rsp.respId) respId, rsp.reqId, s.schaapId, 'wanneer niet omgenummerd'
		FROM impRespons rsp
		 join tblSchaap s on (rsp.levensnummer = s.levensnummer)
		GROUP BY rsp.reqId, rsp.levensnummer

		UNION

		SELECT max(rsp.respId) respId, rsp.reqId, st.schaapId, 'wanneer wel omgenummerd'
		FROM impRespons rsp
		 join tblHistorie h on (rsp.levensnummer = h.oud_nummer)
		 join tblStal st on (h.stalId = st.stalId)
		GROUP BY rsp.reqId, rsp.levensnummer
	) id on (id.schaapId = s.schaapId and id.reqId = r.reqId)
 join impRespons rs on (id.respId = rs.respId )
 left join (
		SELECT s.schaapId, h.datum 
		FROM tblSchaap s 
		 join tblStal st on (st.schaapId = s.schaapId)
		 join tblHistorie h on (h.stalId = st.stalId)
		WHERE h.actId = 3 and h.skip = 0 and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	 ) ouder on (ouder.schaapId = s.schaapId)
 
WHERE r.dmmeld is not null and r.code = 'AFV' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and h.skip = 0 and m.skip = 0

UNION 

SELECT m.hisId, u.ubn, s.levensnummer, s.geslacht, date_format(r.dmmeld,'%d-%m-%Y') datum, r.dmmeld date, NULL actId, 'Uitval gemeld' actie, NULL actie_if, NULL kg, ouder.datum dmaanw, case when isnull(rs.meldnr) then concat('RVO meldt : ',rs.foutmeld) else concat('meldnr : ',rs.meldnr) end toel, NULL hiscom, NULL comment
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 join (
		SELECT max(respId) respId, reqId, levensnummer
		FROM impRespons
		GROUP BY reqId, levensnummer
	) id on (id.levensnummer = s.levensnummer and id.reqId = r.reqId)
 join impRespons rs on (id.respId = rs.respId )
 left join (
		SELECT s.schaapId, h.datum 
		FROM tblSchaap s 
		 join tblStal st on (st.schaapId = s.schaapId)
		 join tblHistorie h on (h.stalId = st.stalId)
		WHERE h.actId = 3 and h.skip = 0 and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	 ) ouder on (ouder.schaapId = s.schaapId)
 
WHERE r.dmmeld is not null and r.code = 'DOO' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and h.skip = 0 and m.skip = 0

UNION

SELECT m.hisId, u.ubn, s.levensnummer, s.geslacht, date_format(r.dmmeld,'%d-%m-%Y') datum, r.dmmeld date, NULL actId, 'Omnummeren gemeld' actie, NULL actie_if, NULL kg, ouder.datum dmaanw, case when isnull(rs.meldnr) then concat('RVO meldt : ',rs.foutmeld) else concat('meldnr : ',rs.meldnr) end toel, NULL hiscom, NULL comment
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 join (
		SELECT max(respId) respId, reqId, levensnummer_new
		FROM impRespons
		GROUP BY reqId, levensnummer
	) id on (id.levensnummer_new = s.levensnummer and id.reqId = r.reqId)
 join impRespons rs on (id.respId = rs.respId )
 left join (
		SELECT s.schaapId, h.datum 
		FROM tblSchaap s 
		 join tblStal st on (st.schaapId = s.schaapId)
		 join tblHistorie h on (h.stalId = st.stalId)
		WHERE h.actId = 3 and h.skip = 0 and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	 ) ouder on (ouder.schaapId = s.schaapId)
 
WHERE r.dmmeld is not null and r.code = 'VMD' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and h.skip = 0 and m.skip = 0

UNION

SELECT hisId1 hisId, mdr.ubn, mdr.levensnummer, mdr.geslacht, date_format(mdr.worp1,'%d-%m-%Y') datum, mdr.worp1 date, NULL actId, 'Eerste worp' actie, 'worp' actie_if, NULL kg, mdr.dmaanw, concat(lam.lmrn) toel, NULL hiscom, NULL comment
FROM
 (
	SELECT u.ubn, s.levensnummer, s.geslacht, ouder.datum dmaanw, min(hl.datum) worp1, min(hl.hisId) hisId1
	FROM tblStal st
	 join tblUbn u on (st.ubnId = u.ubnId)
	 join tblSchaap s on (s.schaapId = st.schaapId)
	 join tblVolwas v on (v.mdrId = s.schaapId)
	 join tblSchaap lam on (lam.volwId = v.volwId)
	 join tblStal sl on (lam.schaapId = sl.schaapId)
	 join tblHistorie hl on (sl.stalId = hl.stalId)
	 left join (
		SELECT s.schaapId, h.datum 
		FROM tblSchaap s 
		 join tblStal st on (st.schaapId = s.schaapId)
		 join tblHistorie h on (h.stalId = st.stalId)
		WHERE h.actId = 3 and h.skip = 0 and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	 ) ouder on (ouder.schaapId = s.schaapId)

	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and sl.lidId = '".mysqli_real_escape_string($db,$lidId)."' and hl.actId = 1 and hl.skip = 0 and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	GROUP BY s.levensnummer, s.geslacht, ouder.datum
 ) mdr
 join
 (
	SELECT mdr.levensnummer moeder, h.datum, count(lam.schaapId) lmrn 
	FROM tblSchaap mdr
	 join tblVolwas v on (mdr.schaapId = v.mdrId)
	 join tblSchaap lam on (v.volwId = lam.volwId)
	 join tblStal st on (st.schaapId = lam.schaapId)
	 join tblHistorie h on (h.stalId = st.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = 1 and h.skip = 0 and mdr.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	GROUP BY mdr.levensnummer, h.datum
 ) lam on (mdr.levensnummer = lam.moeder and mdr.worp1 = lam.datum)
 
UNION

SELECT hisend hisId, mdr.ubn, mdr.levensnummer, mdr.geslacht, date_format(mdr.worpend,'%d-%m-%Y') datum, mdr.worpend date, NULL actId, 'Laatste worp' actie, 'worp' actie_if, NULL kg, mdr.dmaanw, concat(lam.lmrn) toel, NULL hiscom, NULL comment
FROM
 (
	SELECT u.ubn, s.levensnummer, s.geslacht, ouder.datum dmaanw, max(hl.datum) worpend, max(hl.hisId) hisend
	FROM tblStal st
	 join tblUbn u on (st.ubnId = u.ubnId)
	 join tblSchaap s on (s.schaapId = st.schaapId)
	 join tblVolwas v on (v.mdrId = s.schaapId)
	 join tblSchaap lam on (lam.volwId = v.volwId)
	 join tblStal sl on (lam.schaapId = sl.schaapId)
	 join tblHistorie hl on (sl.stalId = hl.stalId)
	 left join (
		SELECT s.schaapId, h.datum 
		FROM tblSchaap s 
		 join tblStal st on (st.schaapId = s.schaapId)
		 join tblHistorie h on (h.stalId = st.stalId)
		WHERE h.actId = 3 and h.skip = 0 and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	 ) ouder on (ouder.schaapId = s.schaapId)
	 
	 left join (
		SELECT moe.levensnummer, moe.geslacht, min(hl.datum) worp1
		FROM tblStal st
		 join tblSchaap moe on (moe.schaapId = st.schaapId)
		 join tblVolwas v on (v.mdrId = moe.schaapId)
		 join tblSchaap lam on (lam.volwId = v.volwId)
		 join tblStal sl on (lam.schaapId = sl.schaapId)
		 join tblHistorie hl on (sl.stalId = hl.stalId)

		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and hl.actId = 1 and hl.skip = 0 and moe.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
		GROUP BY moe.levensnummer, moe.geslacht
	 ) lam1 on (lam1.levensnummer = s.levensnummer and lam1.worp1 = hl.datum)
	
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and sl.lidId = '".mysqli_real_escape_string($db,$lidId)."' and hl.actId = 1 and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and isnull(lam1.worp1)
	GROUP BY s.levensnummer, s.geslacht, ouder.datum
 ) mdr
 join
 (
	SELECT mdr.levensnummer moeder, h.datum, count(lam.schaapId) lmrn 
	FROM tblSchaap mdr
	 join tblVolwas v on (mdr.schaapId = v.mdrId)
	 join tblSchaap lam on (v.volwId = lam.volwId)
	 join tblStal st on (st.schaapId = lam.schaapId)
	 join tblHistorie h on (h.stalId = st.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = 1 and h.skip = 0 and mdr.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	GROUP BY mdr.levensnummer, h.datum
 ) lam on (mdr.levensnummer = lam.moeder and mdr.worpend = lam.datum)

ORDER BY date_format(date, '%Y-%m-%d 00:00:00') desc, hisId desc
") or die (mysqli_error($db)); 

/*Toelichting Order by :
kg noodzakelijk eerst hok verlaten geboren en dan de(zelfde) datum van spenen
 Id noodzakelijk bij meerder overplaatsingen (recordes tblBezet) op dezelfde dag
  */


while ($his = mysqli_fetch_assoc($geschiedenis)) { 
	$hisId = $his['hisId'];
	$ubn = $his['ubn'];
	$datum = $his['datum'];
	$actId = $his['actId'];
	$actie = $his['actie'];
	$actie_if = $his['actie_if'];
	$sekse = $his['geslacht'];
	$date = $his['date'];
	$dmaanw = $his['dmaanw'];
	if( !isset($dmaanw) || $date < $dmaanw /*geen lam meer */ ||
	($date == $dmaanw && ($actId == 5 || $actId == 6) ) ) /*zeldedatum en nog wel lam */ { $fase = 'lam';} 
	else { if($sekse == 'ooi') { $fase = 'moeder';} else if($sekse == 'ram') { $fase = 'vader'; } }
	$kg = $his['kg'];
	$toel = $his['toel']; if ($actie_if == 'worp' && $toel== 1) { $toel = $toel." lam"; } else if ($actie_if == 'worp' && $toel > 1) { $toel = $toel." lammeren"; }
	$Id = $his['hiscom']; /* hisId t.b.v. commentaar*/
	$comm = $his['comment'];
	
	$lev = $his['levensnummer'];
	?>

<tr style = "font-size : 14px;">
 <td> <?php echo $ubn; ?> </td>
 <td> <?php echo $datum; ?> </td>
 <td> <?php echo $actie; ?> </td>
 <td align = "center"> <?php echo $fase; ?> </td>
 <td align = 'right'> <?php if(isset($kg)) { echo $kg." kg"; } ?> </td>
 <td> <?php echo "&nbsp &nbsp &nbsp". $toel."&nbsp &nbsp &nbsp"; ?> </td>
 <td> <?php if($Id > 0) { ?>
	<input type="text" name=<?php echo "txtComm_$Id"; ?> style="font-size: 11px"; size="50" value= <?php echo " \"$comm\" "; ?> > <?php } ?> 
 </td>
</tr>
<?php }


 } // Einde Zoekcriterium moet bestaan ?>
</table> <!-- Einde Historie van het schaap -->
<?php } // Einde if ((isset($_POST['knpZoek_']) || isset($_POST['knpSave_'])) && $histo .........

// VOOROUDERS
 if ((isset($_POST['knpZoek_']) || isset($_POST['knpSave_'])) && $voorOud == 1 && (!empty($_POST['kzlLevnr_']) || !empty($_POST['kzlWerknr_']) || !empty($_POST['kzlHalsnr_'])) ) { ?>
<table border = 0>
<tr><td height="50"></td> </tr>

<tr><td colspan = 11 ><hr></td></tr>
<tr><td colspan = 5 >Voorouders van het schaap : </td> </tr>



<?php //   '".mysqli_real_escape_string($db,$schaapId)."'


$ouders = mysqli_query($db,"
with recursive sheep (schaapId, levnr, geslacht, ras, volwId_s, mdrId, levnr_ma, ras_ma, vdrId, levnr_pa, ras_pa) as (
   SELECT s.schaapId, right(s.levensnummer,5) levnr, s.geslacht, r.ras, s.volwId, v.mdrId, right(ma.levensnummer,5) levnr_ma, rm.ras ras_ma, v.vdrId, right(pa.levensnummer,5) levnr_pa, rv.ras ras_pa
     FROM tblVolwas v
     left join tblSchaap s on s.volwId = v.volwId
     left join tblRas r on s.rasId = r.rasId
     left join tblSchaap ma on ma.schaapId = v.mdrId
     left join tblRas rm on ma.rasId = rm.rasId
     left join tblSchaap pa on pa.schaapId = v.vdrId
     left join tblRas rv on pa.rasId = rv.rasId
    WHERE s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
    union all
   SELECT sm.schaapId, right(sm.levensnummer,5) levnr, sm.geslacht, r.ras, sm.volwId, vm.mdrId, right(ma.levensnummer,5) levnr_ma, rm.ras ras_ma, vm.vdrId, right(pa.levensnummer,5) levnr_pa, rv.ras ras_pa
     FROM tblVolwas vm
     left join tblSchaap sm on sm.volwId = vm.volwId
     left join tblRas r on sm.rasId = r.rasId
     left join tblSchaap ma on ma.schaapId = vm.mdrId
     left join tblRas rm on ma.rasId = rm.rasId
     left join tblSchaap pa on pa.schaapId = vm.vdrId
     left join tblRas rv on pa.rasId = rv.rasId
     join sheep on sm.schaapId = sheep.mdrId
    union all
   SELECT sv.schaapId, right(sv.levensnummer,5) levnr, sv.geslacht, r.ras, sv.volwId, vv.mdrId, right(ma.levensnummer,5) levnr_ma, rm.ras ras_ma, vv.vdrId, right(pa.levensnummer,5) levnr_pa, rv.ras ras_pa
     FROM tblVolwas vv
     left join tblSchaap sv on sv.volwId = vv.volwId
     left join tblRas r on sv.rasId = r.rasId
     left join tblSchaap ma on ma.schaapId = vv.mdrId
     left join tblRas rm on ma.rasId = rm.rasId
     left join tblSchaap pa on pa.schaapId = vv.vdrId
     left join tblRas rv on pa.rasId = rv.rasId
     join sheep on sv.schaapId = sheep.vdrId
)


SELECT s.schaapId, levnr, s.geslacht, ras, volwId_s, levnr_ma, ras_ma, levnr_pa, ras_pa, count(worp.schaapId) grootte
  FROM sheep s
   join tblSchaap worp on (s.volwId_s = worp.volwId)
GROUP BY s.schaapId, levnr, geslacht, ras, volwId_s, levnr_ma, ras_ma, levnr_pa, ras_pa
ORDER BY s.schaapId
") or die (mysqli_error($db));

if(mysqli_num_rows($ouders) == 0)  { ?>
 <td style = "font-size:13px;"> Van dit dier zijn geen voorouders bekend. </td>


<?php }

else { ?>

<tr style = "font-size:13px;" >
<th> Werknr<hr></th>
<th> Geslacht <hr></th>
<th> Ras <hr></th>
<th width="10"></th>
<th> Moeder <hr></th>
<th> Ras moeder <hr></th>
<th width="10"></th>
<th> Vader <hr></th>
<th> Ras vader <hr></th>
<th width="10"></th>
<th> Worpgrootte <hr></th>
</tr>

	<?php
while($row = mysqli_fetch_assoc($ouders)) {

	$schaap = $row['levnr'];
	$geslacht = $row['geslacht'];
	$ras = $row['ras'];
	$moeder = $row['levnr_ma'];
	$ras_ma = $row['ras_ma'];
	$vader = $row['levnr_pa']; 
	$ras_pa = $row['ras_pa']; 
	$worp = $row['grootte']; 
	?>

<tr style = "font-size:13px;">
 <td align="center"> <?php echo $schaap; ?> </td>
 <td> <?php echo $geslacht; ?> </td>
 <td> <?php echo $ras; ?> </td>
 <td></td>
 <td align="center"> <?php echo $moeder; ?> </td>
 <td> <?php echo $ras_ma; ?> </td>
 <td></td>
 <td align="center"> <?php echo $vader; ?> </td>
 <td> <?php echo $ras_pa; ?> </td>
 <td></td>
 <td align="center"> <?php echo $worp; ?> </td>
</tr>

<?php	}


 } // Einde if(isset($ouders)) ?>

</table>

<?php
} // Einde   if ((isset($_POST['knpZoek_']) || isset($_POST['knpSa .......
 // EINDE VOOROUDERS ?>

 
</form>	

		</TD>
<script type="text/javascript">
	$(document).ready(function() {
	  $(".search-select").select2();
	});
</script>
<?php		
include "menu1.php"; }
?>

</body>
</html>
