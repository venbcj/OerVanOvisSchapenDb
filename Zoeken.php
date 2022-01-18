<?php /* 8-8-2014 Aantal karakters werknr variabel gemaakt 
11-8-2014 : veld type gewijzigd in fase 
20-2-2015 : login toegevoegd 
19-11-2015 geboorte datum kan ook aankoopdatum zijn 
23-11-2015 : Berekening breddte kzlWerknr verplaatst naar login.php */
$versie = '2-12-2016'; /* Dubbele records verwijderd als schaap opnieuw wordt aangevoerd */
$versie = '5-12-2016'; /* In historie alleen meldingen die niet zijn verwijderd.  and m.skip = 0 toegvoegd dus */
$versie = '14-1-2017'; /* In query $geschiedenis $levnr vervangen door $schaapId. Bij Overplaatsing = aanwas is schaap t.t.v. overplaatsing lam en geen moeder zoals tot voor 14-1-2017 */
$versie = '15-1-2017'; /* In query $geschiedenis hisId toegevoegd bij eerste en laatste worp */
$versie = "22-1-2017"; /* tblBezetting gewijzigd naar tblBezet */
$versie = '30-1-2017'; /* : Halsnummer toegevoegd  */
$versie = '16-2-2017'; /* hokken van volwassen dieren tonen (incl opnieuw lam ivm niet meer via tblPeriode)  LET OP : bij lam moet h1.actId = 2 worden uitgesloten en bij mdrs en vdrs h2.actId = 3 uitsluiten !!! */
$versie = '2-4-2017'; /* veld commentaar toegevoegd */
$versie = '5-8-2017';  /* Gem groei bij spenen toegevoegd */
$versie = '28-12-2017';  /* In uit verblijf halen van moeder- en vaderdieren in Historie opgenomen */
$versie = '20-07-2018';  /* Index kzlRam_ gewijzigd van werknr_ram naar schaapId */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '12-12-2018'; /* Eerste en laatste worp mag alleen eigen lammeren zijn => sl.lidId = ...$lidId toegevoegd */
$versie = '15-2-2020'; /* tabelnaam gewijzigd van HIS naar his en van TOEL naar toel */
$versie = '23-5-2020'; /* unset gem groei spenen en afvoer en stamboeknummer. Geadopteerd aan historie toegevoegd */
$versie = '27-9-2020'; /* Handmatig omnummeren toegevoegd */
$versie = '27-2-2020'; /* SQL beveiligd met quotes en 'Transponder bekend' toegevoegd */
$versie = '11-4-2021'; /* Adoptie losgekoppeld van verblijf */
$versie = '11-4-2021'; /* Union SELECT uit.hist hisId, concat(ho.hoknr,' verlaten ') toel   aangepast. ht.actId = 7 toegevoegd en niet alleen volwassen dieren kunnen nu de status 'verlaten' hebben. */

 session_start();  ?>
<html>
<head>
<title>Raadplegen</title>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
</head>
<body>

<center>
<?php
$titel = 'Schaap zoeken';
$subtitel = '';
Include "header.php"; ?>

		<TD width = 960 height = 400 valign = "top">
<?php
$file = "Zoeken.php";
Include "login.php";
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {


if(isset($_POST['knpSave_'])) { include"save_commentzoeken.php"; }
//include "vw_Bezetting.php";
//include "vw_Hoklijsten.php";

If (empty($_POST['kzlLevnr_']) ) 	{	$levnr = '';	} else {	$levnr = $_POST['kzlLevnr_'];		}
If (empty($_POST['kzlWerknr_']))	{	$werknr = '';	} else {	$werknr = $_POST['kzlWerknr_'];	}
If (!empty($_POST['kzlHalsnr_'])) {	$halsnr = $_POST['kzlHalsnr_'];	};
// tbv het posten en terug posten met dezelfde zoekcriterium
If (empty($_POST['kzlLevnr_'])) {$pstlevnr = NULL;} else {$pstlevnr = $_POST['kzlLevnr_'];}
If (empty($_POST['kzlWerknr_'])) {$pstwerknr = NULL;} else {$pstwerknr = $_POST['kzlWerknr_'];}

?>
<table border = 0>

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
<form action="Zoeken.php" method="post"> 
<!-- kzlLevensnummer -->
<td>
<?php
$kzlLam = mysqli_query($db,"
SELECT s.schaapId,  s.levensnummer
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.levensnummer is not null
GROUP BY s.schaapId, s.levensnummer
ORDER BY s.levensnummer
") or die (mysqli_error($db));
?> <select name= "kzlLevnr_" style= "width:130; height: 20px" class="search-select">
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
<td>
<?php  
$kzlLam = mysqli_query($db,"
SELECT s.schaapId, right(s.levensnummer,$Karwerk) werknr
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.levensnummer is not null
GROUP BY s.schaapId, right(s.levensnummer,$Karwerk)
ORDER BY right(s.levensnummer,$Karwerk)
") or die (mysqli_error($db)); ?>
			
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
<td>
<?php
$zoek_halsnr = mysqli_query($db,"
SELECT s.schaapId, concat(st.kleur,' ',st.halsnr) halsnr
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.kleur is not null and st.halsnr is not null and isnull(st.rel_best)
GROUP BY s.schaapId, concat(st.kleur,' ',st.halsnr)
ORDER BY st.kleur, st.halsnr
") or die (mysqli_error($db)); ?>
			
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
<td> <?php
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
 
 <td> </td>

<!-- kzlVader -->
<td> <?php
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
	
<td width = 50></td><td> Historie tonen : <input type = radio name = 'radHis_' value = 0 
		<?php if(!isset($_POST['knpZoek_']) && !isset($_POST['knpSave_']) && $histo == 0) { echo "checked"; } 
		 else if(isset($_POST['radHis_']) && $_POST['radHis_'] == 0 ) { echo "checked"; } ?> title = "Standaard tonen van historie te wijzigen in systeemgegevens"> Nee
	 <input type = radio name = "radHis_" value = 1
		<?php if(!isset($_POST['knpZoek_']) && !isset($_POST['knpSave_']) && $histo == 1) { echo "checked"; }
		 else if(isset($_POST['radHis_']) && $_POST['radHis_'] == 1 ) { echo "checked"; } ?> title = "Standaard tonen van historie te wijzigen in systeemgegevens"> Ja 
		
		<?php if(isset($_POST['knpZoek_']) || isset($_POST['knpSave_']) ) { $historie = $_POST['radHis_'];} else { $historie = 0; }  ?>	
		</td> 
</tr>

<tr>
<td colspan = 9 align = "center">
<input type = "submit" name= "knpZoek_" value = "zoeken">
</td>
</tr>


</table>
<table border = 0>
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
 //echo '$where = '.$where;	

// Zoeken naar eerste datum en een eventuele aankoopdatum
$aankoop = mysqli_query($db,"
SELECT date_format(min(h.datum),'%d-%m-%Y') datum, min(h.actId) actId, koop.datum dmkoop
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
 left join tblVolwas v on (v.volwId = s.volwId)
 left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
 left join tblSchaap vdr on (v.vdrId = vdr.schaapId)
 left join (
		SELECT s.schaapId, h.datum 
		FROM tblSchaap s 
		 join tblStal st on (st.schaapId = s.schaapId)
		 join tblHistorie h on (h.stalId = st.stalId)
		WHERE h.actId = 2 and h.skip = 0
	 ) koop on (koop.schaapId = s.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.skip = 0 and $where
") or die (mysqli_error($db));
  
 while ($lijn = mysqli_fetch_assoc($aankoop))
			{	$datum1 = $lijn['datum'];  
				$dmkoop = $lijn['dmkoop']; }
// Einde Controleren op aankoop door zoeken in tblBezetting

// schapen met status onbekend
//where isnull(vb.bezetId) and s.fase = 'lam' and isnull(afleverdm) and isnull(uitvaldm)
$result = mysqli_query($db,"

SELECT s.transponder, concat(st.kleur,' ',st.halsnr) halsnr, s.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk) werknr, s.fokkernr, right(mdr.levensnummer,$Karwerk) werknr_ooi, right(vdr.levensnummer,$Karwerk) werknr_ram, r.ras, s.geslacht, ouder.datum dmaanw, coalesce(lower(act.actie),'aanwezig') status, act.af,
hs.datum dmspn, hs.kg spnkg, afl.datum dmafl, afl.kg aflkg, hg.datum dmgeb, date_format(hg.datum,'%d-%m-%Y') gebdm, hg.kg gebkg, date_format(aanv.datum,'%d-%m-%Y') aanvdm, aanv.datum dmaanv, aanv.kg aankkg

FROM tblSchaap s
 left join tblVolwas v on (v.volwId = s.volwId)
 left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
 left join tblSchaap vdr on (v.vdrId = vdr.schaapId)
 join (
	SELECT max(stalId) stalId, schaapId
	FROM tblStal
	WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
	GROUP BY schaapId
 ) stm on (s.schaapId = stm.schaapId)
 join tblStal st on (stm.stalId = st.stalId)
 left join (
	SELECT st.stalId, h.datum, h.kg
	FROM tblStal st 
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 1 and h.skip = 0
 ) hg on (st.stalId = hg.stalId)
 left join (
	SELECT st.stalId, h.datum, h.kg
	FROM tblStal st 
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE (h.actId = 2 or h.actId = 11) and h.skip = 0
 ) aanv on (st.stalId = aanv.stalId)
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
	Select st.stalId, a.actie, a.af
	From tblActie a
	 join tblHistorie h on (a.actId = h.actId)
	 join
	 (
		SELECT st.stalId, max(h.hisId) hisId 
		FROM tblStal st
		 join tblSchaap s on (st.schaapId = s.schaapId)
		 left join tblVolwas v on (v.volwId = s.volwId)
		 left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
		 left join tblSchaap vdr on (v.vdrId = vdr.schaapId)
		 join tblHistorie h on (st.stalId = h.stalId) 
		 join tblActie a on (a.actId = h.actId) 
		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and $where and a.af = 1 and h.skip = 0
		GROUP BY st.stalId
	 ) maxh on (h.hisId = maxh.hisId)
	 join tblStal st on (h.stalId = st.stalId)
	WHERE h.skip = 0
 ) act on (act.stalId = st.stalId)
 left join (
	Select st.schaapId, h.datum, h.kg
	From tblHistorie h 
	 join 
	 (
		SELECT s.levensnummer, min(h.hisId) hisId 
		FROM tblStal st
		 join tblSchaap s on (st.schaapId = s.schaapId)
		 left join tblVolwas v on (v.volwId = s.volwId)
		 left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
		 left join tblSchaap vdr on (v.vdrId = vdr.schaapId)
		 join tblHistorie h on (st.stalId = h.stalId) 
		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and $where and h.actId = 12 and h.skip = 0
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

<th style = "text-align:center;"valign="bottom";width= 50> <?php if(!isset($dmkoop)) { echo 'Geboortedatum'; } else { echo 'Aanvoerdatum'; } ?><hr></th>

<th style = "text-align:center;"valign="bottom";width= 50>Generatie<hr></th>

<th style = "text-align:center;"valign="bottom";width= 50>Ras<hr></th>

<th style = "text-align:center;"valign="bottom";width= 50>Geslacht<hr></th>

<th style = "text-align:center;"valign="bottom";width= 200>Werknr ooi<hr></th>

<th style = "text-align:center;"valign="bottom";width= 200>Werknr ram<hr></th>

<th style = "text-align:center;"valign="bottom";width= 60>Status<hr></th>

<th style = "text-align:center;"valign="bottom";width= 60>Gem Groei speen<hr></th>

<th style = "text-align:center;"valign="bottom";width= 60>Gem Groei aflev<hr></th>

<th style = "text-align:center;"valign="bottom";width= 60>Stamboeknr<hr></th>
<th width = 60></th>

<th style = "text-align:center;"valign="bottom";width= 80></th>
<th width = 600></th>

 </tr>

<?php

while($row = mysqli_fetch_assoc($result))
			{
				$transponder = $row['transponder']; if(isset($transponder)) {$transp = 'Ja'; } else {$transp = 'Nee'; }
				$schaapId = $row['schaapId'];
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
				$opstal = $row{'af'};
				$dmspn = $row{'dmspn'};
				$spnkg = $row{'spnkg'};
				$dmafl = $row{'dmafl'};
				$aflkg = $row{'aflkg'};
				$dmgeb = $row{'dmgeb'};
				$gebkg = $row{'gebkg'};
				$dmaanv = $row{'dmaanv'};
				$aanvdm = $row{'aanvdm'};
				$aankkg = $row{'aankkg'};
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
  	   
	   <td width = 100 style = "font-size:15px;"> <?php if(isset($dmaanv)) { echo $aanvdm; } else { echo $gebdm; } ?> <br> </td>

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
if ($status == 'aanwezig')
{	?> <td width="450">
	<a href=' <?php echo $url; ?>UpdSchaap.php?pstschaap=<?php echo $schaapId; ?>' style = "color : blue">Wijzigen</a>
	&nbsp&nbsp&nbsp
	<a href=' <?php echo $url; ?>OmnSchaap.php?pstschaap=<?php echo $schaapId; ?>' style = "color : blue">Omnummeren</a>
   </td> <?php
   
   
}
else
{ ?>
<td><a href='<?php echo $url; ?>UpdSchaap.php?pstschaap=<?php echo $schaapId; ?>' style = "color : blue">
	   Wijzigen</a></td>


	   
	   <?php
} ?>
	   <td width = 80 style = "font-size:13px;" >

<?php	}  ?> 				
	   </td>
<?php  
if (!isset($schaapId))
{ 
$fout = "Het zoek criterium heeft geen resultaten opgeleverd. Pas het zoekcriterum eventueel aan.";
} 
} ?>
	   
</tr>
</table>
<?php if ((isset($_POST['knpZoek_']) || isset($_POST['knpSave_'])) && $historie == 1 && (!empty($_POST['kzlLevnr_']) || !empty($_POST['kzlWerknr_']) || !empty($_POST['kzlHalsnr_'])) ) { ?>	
<table border = 0> 

<?php
// Om een lege tabel te verbergen moet minimaal 1 keuze zijn gemakt
?>
<tr height = 50>
</tr>

<tr><td colspan = 7 ><hr></td></tr>
<tr><td colspan = 2 >Historie van het schaap : </td>
<td> </td></tr>
<tr style = "font-size : 13px;">

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
SELECT his.hisId, his.levensnummer, his.geslacht, his.datum, his.date, his.actId, his.actie, his.actie_if, his.kg, date_format(his.dmaanw,'%Y-%m-%d 00:00:00') dmaanw, toel.toel, his.hisId hiscom, comment
FROM
(
	SELECT h.hisId, s.levensnummer, s.geslacht, date_format(h.datum, '%d-%m-%Y') datum, h.datum date, h.actId, a.actie, right(a.actie,4) actie_if, h.kg, ouder.datum dmaanw, h.comment
	FROM tblSchaap s
	 join tblStal st on (st.schaapId = s.schaapId)
	 join tblHistorie h on (st.stalId = h.stalId)
	 join tblActie a on (a.actId = h.actId)
	 left join (
		SELECT s.schaapId, h.datum 
		FROM tblSchaap s 
		 join tblStal st on (st.schaapId = s.schaapId)
		 join tblHistorie h on (h.stalId = st.stalId)
		WHERE h.actId = 3 and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	 ) ouder on (ouder.schaapId = s.schaapId)
	WHERE s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.skip = 0
	 and not exists (
		SELECT datum 
		FROM tblHistorie ha 
		 join tblStal st on (ha.stalId = st.stalId)
		 join tblSchaap s on (st.schaapId = s.schaapId)
		WHERE actId = 2 and h.datum = ha.datum and h.actId = ha.actId+1 and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."')

  union

	SELECT h.hisId, s.levensnummer, s.geslacht, date_format(h.datum, '%d-%m-%Y') datum, h.datum date, h.actId, a.actie, right(a.actie,4) actie_if, h.kg, ouder.datum, h.comment
	FROM tblHistorie h
	 join tblStal st on (st.stalId = h.stalId)
	 join tblSchaap s on (st.schaapId = s.schaapId)
	 join tblActie a on (a.actId = h.actId)
	 left join (
		SELECT s.schaapId, h.datum 
		FROM tblSchaap s 
		 join tblStal st on (st.schaapId = s.schaapId)
		 join tblHistorie h on (h.stalId = st.stalId)
		WHERE h.actId = 3 and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	 ) ouder on (ouder.schaapId = s.schaapId)
	WHERE s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and h.actId = 1
) his
left join 
(
	SELECT h.hisId, concat('Bij ooi ', right(mdr.levensnummer,$Karwerk)) toel
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
	WHERE h.actId = 15 and vp.actId = 15 and vp.lidId = '".mysqli_real_escape_string($db,$lidId)/*adoptie lammeren*/."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'

Union

	SELECT h.hisId, concat('Geplaatst in ', lower(ho.hoknr),' voor ',datediff(coalesce(ht.datum,curdate()), h.datum), If(datediff(coalesce(ht.datum,curdate()), h.datum) = 1, ' dag', ' dagen')) toel

	FROM tblHok ho
	 join tblBezet b on (b.hokId = ho.hokId)
	 join tblHistorie h on (h.hisId = b.hisId)
	 join tblActie a on (a.actId = h.actId)
	 join tblStal st on (st.stalId = h.stalId)
	 left join (
		SELECT h1.hisId hisv, min(h2.hisId) hist
		FROM tblHistorie h1
		 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
		 join tblStal st on (st.stalId = h1.stalId)
		 join tblSchaap s on (s.schaapId = st.schaapId)
		 join tblActie a1 on (a1.actId = h1.actId)
		 join tblActie a2 on (a2.actId = h2.actId)
		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)/*lammeren in hok geplaatst excl. adoptie*/."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
		and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h1.actId != 2
		GROUP BY h1.hisId
	 ) uit on (uit.hisv = b.hisId)
	 left join tblHistorie ht on (ht.hisId = uit.hist)
	  left join (
		SELECT st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 3
	 ) prnt on (prnt.schaapId = st.schaapId)
	WHERE a.aan = 1 and h.actId != 15 and ho.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	 and (isnull(prnt.schaapId) or (prnt.datum > h.datum))

Union

	SELECT h.hisId, concat('Geplaatst in ', lower(ho.hoknr),' voor ',datediff(coalesce(ht.datum,curdate()), h.datum), If(datediff(coalesce(ht.datum,curdate()), h.datum) = 1, ' dag', ' dagen')) toel

	FROM tblHok ho
	 join tblBezet b on (b.hokId = ho.hokId)
	 join tblHistorie h on (h.hisId = b.hisId)
	 join tblActie a on (a.actId = h.actId)
	 join tblStal st on (st.stalId = h.stalId)
	 left join (
		SELECT h1.hisId hisv, min(h2.hisId) hist
		FROM tblHistorie h1
		 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
		 join tblStal st on (st.stalId = h1.stalId)
		 join tblSchaap s on (s.schaapId = st.schaapId)
		 join tblActie a1 on (a1.actId = h1.actId)
		 join tblActie a2 on (a2.actId = h2.actId)
		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)/*Volwassenen in hok geplaatst */."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
		and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
		GROUP BY h1.hisId
	 ) uit on (uit.hisv = b.hisId)
	 left join tblHistorie ht on (ht.hisId = uit.hist)
	 join (
		SELECT st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 3
	 ) prnt on (prnt.schaapId = st.schaapId)
	WHERE a.aan = 1 and ho.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	 and prnt.datum <= h.datum

Union

	SELECT uit.hist hisId, concat(ho.hoknr,' verlaten ') toel

	FROM tblHok ho
	 join tblBezet b on (b.hokId = ho.hokId)
	 join tblHistorie h on (h.hisId = b.hisId)
	 join tblActie a on (a.actId = h.actId)
	 join tblStal st on (st.stalId = h.stalId)
	 join (
		SELECT h1.hisId hisv, min(h2.hisId) hist
		FROM tblHistorie h1
		 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
		 join tblStal st on (st.stalId = h1.stalId)
		 join tblSchaap s on (s.schaapId = st.schaapId)
		 join tblActie a1 on (a1.actId = h1.actId)
		 join tblActie a2 on (a2.actId = h2.actId)
		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)/*Volwassenen hok verlaten */."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
		and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
		GROUP BY h1.hisId
	 ) uit on (uit.hisv = b.hisId)
	 left join tblHistorie ht on (ht.hisId = uit.hist)
	WHERE a.aan = 1 and ho.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	 and ht.actId = 7

Union

	Select h.hisId, p.naam
	From tblActie a
	 join tblHistorie h on (a.actId = h.actId)
	 join tblStal st on (st.stalId = h.stalId)
	 join tblSchaap s on (s.schaapId = st.schaapId)
	 join tblRelatie r on (st.rel_best = r.relId)
	 join tblPartij p on (r.partId = p.partId)
	Where st.lidId = '".mysqli_real_escape_string($db,$lidId) /* deze query betreft toel_afvoer excl dood met een reden */."' and a.af = 1 and h.skip = 0
	 and (h.actId != 14 or (h.actId = 14 and isnull(s.redId)))

Union

	Select h.hisId, re.reden
	From tblActie a
	 join tblHistorie h on (a.actId = h.actId)
	 join tblStal st on (st.stalId = h.stalId)
	 join tblSchaap s on (s.schaapId = st.schaapId)
	 join tblReden re on (s.redId = re.redId)
	 join tblRelatie r on (st.rel_best = r.relId)
	 join tblPartij p on (r.partId = p.partId)
	Where st.lidId = '".mysqli_real_escape_string($db,$lidId) /* deze query betreft toel_afvoer dood met een reden */."' and a.af = 1 and h.skip = 0
	 and h.actId = 14 and s.redId is not null

Union

	Select n.hisId, concat(round(sum(n.nutat*n.stdat),2),' ', e.eenheid,'  ', a.naam,'  ',coalesce(i.charge,'')) toel
	From tblNuttig n
	 join tblInkoop i on (n.inkId = i.inkId)
	 join tblArtikel a on (a.artId = i.artId)
	 join tblEenheiduser eu on (eu.enhuId = a.enhuId)
	 join tblEenheid e on (e.eenhId = eu.eenhId)
	Where eu.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	GROUP BY n.hisId, e.eenheid, a.naam, i.charge
	
) toel
on (his.hisId = toel.hisId)

UNION 

Select NULL hisId, s.levensnummer, s.geslacht, date_format(p.dmafsluit,'%d-%m-%Y') datum, p.dmafsluit date, NULL actId, 'Gevoerd' actie, NULL actie_if, NULL kg, NULL dmaanw, concat(coalesce(round(datediff(ht.datum,hv.datum) * vr.kg_st,2),0), ' kg ', lower(a.naam), ' t.b.v. ', lower(h.hoknr)) toel, NULL hiscom, NULL comment
From tblBezet b
 join tblPeriode p on (p.periId = b.periId)
 join tblHok h on (h.hokId = p.hokId)
 join tblHistorie hv on (hv.hisId = b.hisId)
 join tblStal st on (st.stalId = hv.stalId)
 join tblSchaap s on (st.schaapId = s.schaapId)
 join
	 (
		Select b.bezId, min(his.hisId) hist
		From tblPeriode p
		 join tblBezet b on (p.periId = b.periId)
		 join tblHistorie h on (h.hisId = b.hisId)
		 join tblStal st on (st.stalId = h.stalId)
		 join tblHistorie his on (st.stalId = his.stalId)
		 join tblActie a on (a.actId = his.actId)
		 join tblSchaap s on (s.schaapId = st.schaapId)
		Where st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
		 and (a.aan = 1 or a.uit = 1)
		 and his.hisId > b.hisId
		Group by b.bezId
	 ) uit on (uit.bezId = b.bezId)
 join tblHistorie ht on (ht.hisId = uit.hist)
 join 
(
	Select v.periId, v.inkId, v.nutat/sum(datediff(ht.datum,hv.datum)) kg_st
	From tblVoeding v
	 join tblPeriode p on (v.periId = p.periId)
	 join tblBezet b on (p.periId = b.periId)
	 join tblHistorie hv on (hv.hisId = b.hisId)
	 join
	 (
		Select b.bezId, min(his.hisId) hist
		From tblBezet b
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
			WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
		 ) peri_obv_schaap on (peri_obv_schaap.periId = b.periId)
		WHERE (a.aan = 1 or a.uit = 1)
		 and his.hisId > b.hisId
		Group by b.bezId
	 ) uit on (uit.bezId = b.bezId)
	 join tblHistorie ht on (ht.hisId = uit.hist)
	Group by v.periId, v.inkId
) vr on (vr.periId = b.periId)
 join tblInkoop i on (i.inkId = vr.inkId)
 join tblArtikel a on (a.artId = i.artId)

UNION 

Select m.hisId, s.levensnummer, s.geslacht, date_format(r.dmmeld,'%d-%m-%Y') datum, r.dmmeld date, NULL actId, 'Geboorte gemeld' actie, NULL actie_if, NULL kg, ouder.datum dmaanw, case when isnull(rs.meldnr) then concat('RVO meldt : ',rs.foutmeld) else concat('meldnr : ',rs.meldnr) end toel, NULL hiscom, NULL comment
From tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
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
		WHERE h.actId = 3 and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	 ) ouder on (ouder.schaapId = s.schaapId)
 
WHERE r.dmmeld is not null and r.code = 'GER' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and h.skip = 0 and m.skip = 0

UNION 

Select m.hisId, s.levensnummer, s.geslacht, date_format(r.dmmeld,'%d-%m-%Y') datum, r.dmmeld date, NULL actId, 'Aanvoer gemeld' actie, NULL actie_if, NULL kg, ouder.datum dmaanw, case when isnull(rs.meldnr) then concat('RVO meldt : ',rs.foutmeld) else concat('meldnr : ',rs.meldnr) end toel, NULL hiscom, NULL comment
From tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
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
		WHERE h.actId = 3 and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	 ) ouder on (ouder.schaapId = s.schaapId)
 
WHERE r.dmmeld is not null and r.code = 'AAN' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and h.skip = 0 and m.skip = 0

UNION 

Select m.hisId, s.levensnummer, s.geslacht, date_format(r.dmmeld,'%d-%m-%Y') datum, r.dmmeld date, NULL actId, 'Afvoer gemeld' actie, NULL actie_if, NULL kg, ouder.datum dmaanw, case when isnull(rs.meldnr) then concat('RVO meldt : ',rs.foutmeld) else concat('meldnr : ',rs.meldnr) end toel, NULL hiscom, NULL comment
From tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
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
		WHERE h.actId = 3 and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	 ) ouder on (ouder.schaapId = s.schaapId)
 
WHERE r.dmmeld is not null and r.code = 'AFV' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and h.skip = 0 and m.skip = 0

UNION 

Select m.hisId, s.levensnummer, s.geslacht, date_format(r.dmmeld,'%d-%m-%Y') datum, r.dmmeld date, NULL actId, 'Uitval gemeld' actie, NULL actie_if, NULL kg, ouder.datum dmaanw, case when isnull(rs.meldnr) then concat('RVO meldt : ',rs.foutmeld) else concat('meldnr : ',rs.meldnr) end toel, NULL hiscom, NULL comment
From tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
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
		WHERE h.actId = 3 and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	 ) ouder on (ouder.schaapId = s.schaapId)
 
WHERE r.dmmeld is not null and r.code = 'DOO' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and h.skip = 0 and m.skip = 0

UNION

Select hisId1 hisId, mdr.levensnummer, mdr.geslacht, date_format(mdr.worp1,'%d-%m-%Y') datum, mdr.worp1 date, NULL actId, 'Eerste worp' actie, 'worp' actie_if, NULL kg, mdr.dmaanw, concat(lam.lmrn) toel, NULL hiscom, NULL comment
From
 (
	SELECT s.levensnummer, s.geslacht, ouder.datum dmaanw, min(hl.datum) worp1, min(hl.hisId) hisId1
	From tblStal st
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
		WHERE h.actId = 3 and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	 ) ouder on (ouder.schaapId = s.schaapId)

	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and sl.lidId = '".mysqli_real_escape_string($db,$lidId)."' and hl.actId = 1 and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
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
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = 1 and mdr.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	GROUP BY mdr.levensnummer, h.datum
 ) lam on (mdr.levensnummer = lam.moeder and mdr.worp1 = lam.datum)
 
UNION

Select hisend hisId, mdr.levensnummer, mdr.geslacht, date_format(mdr.worpend,'%d-%m-%Y') datum, mdr.worpend date, NULL actId, 'Laatste worp' actie, 'worp' actie_if, NULL kg, mdr.dmaanw, concat(lam.lmrn) toel, NULL hiscom, NULL comment
From
 (
	SELECT s.levensnummer, s.geslacht, ouder.datum dmaanw, max(hl.datum) worpend, max(hl.hisId) hisend
	From tblStal st
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
		WHERE h.actId = 3 and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
	 ) ouder on (ouder.schaapId = s.schaapId)
	 
	 left join (
		SELECT moe.levensnummer, moe.geslacht, min(hl.datum) worp1
		From tblStal st
		 join tblSchaap moe on (moe.schaapId = st.schaapId)
		 join tblVolwas v on (v.mdrId = moe.schaapId)
		 join tblSchaap lam on (lam.volwId = v.volwId)
		 join tblStal sl on (lam.schaapId = sl.schaapId)
		 join tblHistorie hl on (sl.stalId = hl.stalId)

		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and hl.actId = 1 and moe.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
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
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = 1 and mdr.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
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
	<td> <?php echo $datum; ?> </td>
	<td> <?php echo $actie; ?> </td>
	<td align = center> <?php echo $fase; ?> </td>
	<td align = 'right'> <?php if(isset($kg)) { echo $kg." kg"; } ?> </td>
	<td> <?php echo "&nbsp &nbsp &nbsp". $toel."&nbsp &nbsp &nbsp"; ?> </td>
	<td> <?php if($Id > 0) { ?>
	<input type="text" name=<?php echo "txtComm_$Id"; ?> style="font-size: 11px"; size="50" value= <?php echo " \"$comm\" "; ?> > <?php } ?> 
	</td>
	<?php } ?>
	
	
</tr> <?php } // Einde Zoekcriterium moet bestaan ?>
</table>
<?php } ?>
</form>	

		</TD>
<script type="text/javascript">
	$(document).ready(function() {
	  $(".search-select").select2();
	});
</script>
<?php		
Include "menu1.php"; }
?>

</body>
</html>
