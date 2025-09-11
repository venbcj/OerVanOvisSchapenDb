<?php

require_once("autoload.php");

/* 8-8-2014 Aantal karakters werknr variabel gemaakt 
11-8-2014 : veld type gewijzigd in fase
11-3-2015 : Login toegevoegd */
$versie = '30-11-2016';  /* actId = 3 aan schaapId gekoppeld i.p.v. een stalId */
$versie = '2-4-2017';  /* ras niet verplicht gemaakt => left join tblRas */
$versie = '5-5-2017';  /* Aantal lammeren gebasseerd op eigen lidId */
$versie = '5-8-2017';  /* Gem groei bij spenen toegevoegd */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '12-12-2018'; /* Van het aantal lammeren worden alleen die met geboortedatum geteld. Aanvoer dieren van moeder dus niet */
$versie = '4-4-2020'; /* halsnrs in keuzelijst alleen van dieren op stallijst */
$versie = '31-12-2023'; /* and h.skip = 0 aangevuld aan tblHistorie en sql beveiligd met quotes */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = 'top' align = 'center'> gewijzigd naar <TD valign = 'top' align = 'center'> 31-12-24 include login voor include header gezet */

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Rapport</title>
</head>
<body>

<?php
$titel = 'Ooikaart per moederdier';
$file = "Ooikaart.php";
include "login.php"; ?>

		<TD valign = 'top' align = 'center'>
<?php
if (Auth::is_logged_in()) { if($modtech ==1) {

if (isset($_GET['pstId'])) {$raak = $_GET['pstId']; } ?>

<table border = 0> <?php					
If (empty($_POST['kzllevnr']) ) {	$kzlLevnr = '';	} else {	$kzlLevnr = $_POST['kzllevnr'];		}
If (empty($_POST['kzlwerknr']))	{	if(isset($raak)) {$kzlWerknr = $raak;} else {$kzlWerknr = '';} } else {	$kzlWerknr = $_POST['kzlwerknr'];	}
If (empty($_POST['kzlHalsnr']) ) {	$kzlHalsnr = '';	} else {	$kzlHalsnr = $_POST['kzlHalsnr'];		}

/* Keuze ooi kan op basis van levensnummer, werknr en/of halsnr 
Onderstaande werkt het volgende uit: 
Alle keuzes worden bij elkaar opgeteld en gedeeld door het aantal ingevulde keuze velden 
Elk van de gevulde keuze velden moet gelijk zijn aan het resultaat van de deling.
Dat is   $gekozen_ooi  !!! */
if (!empty($kzlLevnr))
{	$zoek_moeder = mysqli_query($db,"
SELECT schaapId
FROM tblSchaap
WHERE schaapId = '".mysqli_real_escape_string($db,$kzlLevnr)."'
") or die (mysqli_error($db)); 

while ($zm = mysqli_fetch_assoc($zoek_moeder)) { $mdrId_obv_levnr = $zm['schaapId']; }
$deel = 1; }

else { $mdrId_obv_levnr = 0; $deel = 0; }

if (!empty($kzlWerknr))
{	$zoek_moeder = mysqli_query($db,"
SELECT schaapId
FROM tblSchaap
WHERE schaapId = '".mysqli_real_escape_string($db,$kzlWerknr)."'
") or die (mysqli_error($db)); 

while ($zm = mysqli_fetch_assoc($zoek_moeder)) { $mdrId_obv_werknr = $zm['schaapId']; }
$deel++ ; }

else { $mdrId_obv_werknr = 0; }

if (!empty($kzlHalsnr))
{	$zoek_moeder = mysqli_query($db,"
SELECT schaapId
FROM tblSchaap
WHERE schaapId = '".mysqli_real_escape_string($db,$kzlHalsnr)."'
") or die (mysqli_error($db)); 

while ($zm = mysqli_fetch_assoc($zoek_moeder)) { $mdrId_obv_halsnr = $zm['schaapId']; }
$deel++ ; }

else { $mdrId_obv_halsnr = 0; }
	
if ($deel > 0) { $mdrId_obv_keuze = ($mdrId_obv_levnr + $mdrId_obv_werknr + $mdrId_obv_halsnr) / $deel; }
if (isset($mdrId_obv_keuze) && ($mdrId_obv_keuze == $mdrId_obv_levnr || $mdrId_obv_keuze == $mdrId_obv_werknr || $mdrId_obv_keuze == $mdrId_obv_halsnr) )
{ $gekozen_ooi = $mdrId_obv_keuze; } 
/* Einde  Keuze ooi kan op basis van levensnummer, werknr en/of halsnr */

	?>
<tr align = "center" valign = 'top' ><td colspan = 35>	<table border = 0>

<tr>
<td width="150"> </td>	
<td colspan = 3><i><sub> Levensnummer </sub></i> </td>
<td> </td>	
<td colspan = 3><i><sub> Werknr </sub></i> </td>
<td> </td>
<td colspan = 3><i><sub> Halsnr </sub></i> </td>
<td width="150"> </td>
<?php if (isset($raak)) { ?>
<td> <a href = '<?php echo $url;?>Ooikaart_pdf.php?Id=<?php echo $raak; ?>' style = 'color : blue' > print pagina </a> </td>
<?php } else if(isset($gekozen_ooi)) { ?>
<td> <a href = '<?php echo $url;?>Ooikaart_pdf.php?Id=<?php echo $gekozen_ooi; ?>' style = 'color : blue' > print pagina  </a> </td>
<?php } ?>
</tr>



<tr>
<td> </td>
<form action= "Ooikaart.php" method= "post"> 

<td colspan = 3>
<?php	//Keuzelijst levensnummer
$kzl = mysqli_query($db,"
SELECT mdr.schaapId, mdr.levensnummer
FROM tblSchaap mdr
 left join tblVolwas v on (mdr.schaapId = v.mdrId)
 left join tblSchaap lam on (v.volwId = lam.volwId) 
 join tblStal st on (mdr.schaapId = st.schaapId)
 join (
	SELECT schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) h on (st.schaapId = h.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(st.rel_best) and mdr.geslacht = 'ooi'
GROUP BY mdr.schaapId, mdr.levensnummer
ORDER BY mdr.levensnummer
") or die (mysqli_error($db)); ?>
 <select name= "kzllevnr" style= "width:120;" >
 <option> </option> 	
<?php		while($row = mysqli_fetch_array($kzl))
		{
		
			$opties= array($row['schaapId']=>$row['levensnummer']);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if(isset($_POST['kzllevnr']) && $_POST['kzllevnr'] == $key)
		{
			$keuze = ' selected ';
		}
				
		echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
			}
		
		} ?>
	</select>
	</td>

	<td> </td>

<td colspan = 3>
<?php //Keuzelijst werknr
$width = 25+(8*$Karwerk) ; 
$kzl = mysqli_query($db,"
SELECT mdr.schaapId, right(mdr.levensnummer,$Karwerk) werknr
FROM tblSchaap mdr 
 left join tblVolwas v on (mdr.schaapId = v.mdrId)
 left join tblSchaap lam on (v.volwId = lam.volwId) 
 join tblStal st on (mdr.schaapId = st.schaapId)
 join (
	SELECT schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) h on (st.schaapId = h.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(st.rel_best) and mdr.geslacht = 'ooi'
GROUP BY mdr.schaapId, right(mdr.levensnummer,$Karwerk)
ORDER BY right(mdr.levensnummer,$Karwerk)
") or die (mysqli_error($db)); ?>
<select name= "kzlwerknr" style= "width:<?php echo $width; ?>;" >
<option> </option>
<?php		while($row = mysqli_fetch_array($kzl))
		{
		
			$opties= array($row['schaapId']=>$row['werknr']);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if((isset($_POST['kzlwerknr']) && $_POST['kzlwerknr'] == $key) ||(isset($raak) && $raak == $key))
		{
			$keuze = ' selected ';
		}
		
		echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
			}
		
		} ?>
	</select>
	</td>


<td> </td>
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
			
 <select name="kzlHalsnr" style= "width: 80;" >
 <option></option>
<?php		while($row = mysqli_fetch_array($zoek_halsnr))
		{
		
			$opties= array($row['schaapId']=>$row['halsnr']);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if(isset($_POST['kzlHalsnr']) && $_POST['kzlHalsnr'] == $key)
		{
			$keuze = ' selected ';
		}
		
		echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
			}
		
		} ?>
 </select>
</td>
<!-- Einde kzlHalsnr -->
</tr>

<tr>
<td colspan = 14 align = "center">
<input type = "submit" name="knpToon" value = "toon">
</td>
</tr>
</form>	

</table>		</td></tr>

<?php

if (isset($gekozen_ooi))
{				

$result_mdr = mysqli_query($db,"
SELECT mdr.levensnummer, right(mdr.levensnummer,$Karwerk) werknr, r.ras, date_format(hg.datum,'%d-%m-%Y') geb_datum, date_format(hop.datum,'%d-%m-%Y') aanvoerdm, count(lam.schaapId) lammeren, datediff(current_date(),ouder.datum) dagen, count(ooi.schaapId) aantooi, count(ram.schaapId) aantram,
 count(lam.levensnummer) levend, round(((count(lam.levensnummer) / count(lam.schaapId)) * 100),2) percleven, round(avg(hg_lm.kg),2) gemgewicht,
 count(hs_lm.datum) aantspn, ((count(hs_lm.datum)/count(lam.schaapId))*100) percspn, min(hs_lm.kg) minspnkg, max(hs_lm.kg) maxspnkg, round(avg(hs_lm.kg),2) gemspnkg,
 count(haf_lm.datum) aantafv, round(avg(haf_lm.kg),2) gemafvkg
FROM tblSchaap mdr 
 left join tblVolwas v on (mdr.schaapId = v.mdrId)
 left join (
 	SELECT s.schaapId, s.levensnummer, s.volwId
 	FROM tblSchaap s
 	 join tblStal st on (s.schaapId = st.schaapId)
 	 join tblHistorie h on (st.stalId = h.stalId)
 	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = 1 and h.skip = 0
 ) lam on (v.volwId = lam.volwId)
 join (
	SELECT max(stalId) stalId, mdr.schaapId
	FROM tblStal st
	 join tblSchaap mdr on (st.schaapId = mdr.schaapId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and mdr.schaapId = '".mysqli_real_escape_string($db,$gekozen_ooi)."'
	GROUP BY mdr.schaapId
 ) maxst on (maxst.schaapId = mdr.schaapId)
 join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) ouder on (mdr.schaapId = ouder.schaapId)
 left join tblHistorie hg on (maxst.stalId = hg.stalId and hg.actId = 1 and hg.skip = 0)
 left join tblHistorie hop on (maxst.stalId = hop.stalId and (hop.actId = 2 or hop.actId = 11) and hop.skip = 0 )
 left join tblRas r on (r.rasId = mdr.rasId)
 left join tblSchaap ooi on (lam.schaapId = ooi.schaapId and ooi.geslacht = 'ooi')
 left join tblSchaap ram on (lam.schaapId = ram.schaapId and ram.geslacht = 'ram')
 left join tblStal st_lm on (lam.schaapId = st_lm.schaapId)
 left join tblHistorie hg_lm on (st_lm.stalId = hg_lm.stalId and hg_lm.actId = 1 and hg_lm.skip = 0)
 left join tblHistorie hs_lm on (st_lm.stalId = hs_lm.stalId and hs_lm.actId = 4 and hg_lm.skip = 0)
 left join tblHistorie haf_lm on (st_lm.stalId = haf_lm.stalId and haf_lm.actId = 12 and haf_lm.skip = 0)
 
GROUP BY mdr.levensnummer, mdr.geslacht, r.ras, date_format(hg.datum,'%d-%m-%Y'), date_format(hop.datum,'%d-%m-%Y')
ORDER BY right(mdr.levensnummer,$Karwerk) desc
") or die (mysqli_error($db));	

{	
while($row = mysqli_fetch_assoc($result_mdr))
			{
				$levnr = $row['levensnummer'];
				$werknr = $row['werknr'];
				$ras = $row['ras'];
				$gebdm = $row['geb_datum'];
				$aanvdm = $row['aanvoerdm']; if(isset($gebdm)) { $opdm = $gebdm; } else { $opdm = $aanvdm; }
				$dagen = $row['dagen'];
				$lammeren = $row['lammeren'];
				$levend = $row['levend'];
				$percleven = $row['percleven'];
				$aantooi = $row['aantooi'];
				$aantram = $row['aantram'];
				$gemkg = $row['gemgewicht'];
				$aantspn = $row['aantspn'];
				$gemspn = $row['gemspnkg'];
				$aantafl = $row['aantafv'];
				$gemafl = $row['gemafvkg'];			
/*	Gegevens tbv MOEDERDIER		*/


?>
<tr><td colspan = 6 align = "center"><h3>moederdier</td></tr>
							
<tr style = "font-size:12px;">
 <th width = 0 height = 30></th>
 <th width = 1 height = 30></th>
 <th style = "text-align:center;"valign="bottom";width= 80>Levensnummer<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 80>Werknr<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 50>Ras<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 50><?php if(isset($gebdm)) { echo 'Geboortedatum'; } else { echo 'Aanvoerdatum'; } ?><hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 200>Aantal dagen moeder<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 60>Aantal lammeren<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 60>Aantal levend geboren<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 60>% levend geboren<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 60>Aantal ooien<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 60>Aantal rammen<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 60>Gem geboorte gewicht<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 50>Gespeend<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 140>Gem speen gewicht<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 50>Afgeleverd<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 140>Gem aflever gewicht<hr></th>
 <th width = 60></th>
 <th style = "text-align:center;"valign="bottom";width= 80></th>
 <th width = 600></th>
</tr>

<tr align = "center">	
 <td width = 0> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:14px;"> <?php echo $levnr; ?> <br> </td>
 <td width = 1> </td>   
 <td width = 100 style = "font-size:14px;"> <?php echo $werknr; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:14px;"> <?php echo $ras; ?> <br> </td>
 <td width = 1> </td>	   	   
 <td width = 100 style = "font-size:12px;"> <?php echo $opdm; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:14px;"> <?php echo $dagen; ?> <br> </td>
 <td width = 1> </td>	   	   
 <td width = 100 style = "font-size:14px;"> <?php echo $lammeren; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:14px;"> <?php echo $levend; ?> <br> </td>
 <td width = 1> </td>	
 <td width = 100 style = "font-size:14px;"> <?php echo $percleven; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:14px;"> <?php echo $aantooi; ?> <br> </td>
 <td width = 1> </td>	
 <td width = 100 style = "font-size:14px;"> <?php echo $aantram; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:14px;"> <?php echo $gemkg; ?> <br> </td>	
 <td width = 1> </td>
 <td width = 100 style = "font-size:12px;"> <?php echo $aantspn; ?> <br> </td>
 <td width = 1> </td>	
 <td width = 100 style = "font-size:12px;"> <?php echo $gemspn; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:12px;"> <?php echo $aantafl; ?> <br> </td>
 <td width = 1> </td>	
 <td width = 100 style = "font-size:12px;"> <?php echo $gemafl; ?> <br> </td>
 <td width = 1> </td>
 <td width = 80 style = "font-size:13px;" >

<?php	}   ?>
	   </td>
<?php } ?>
	   
</tr> 
<tr><td colspan = 35 ><hr></td></tr>
<tr><td height = 25 ></td></tr>
<tr><td colspan = 10 align = "center"><h3>lammeren van moederdier </td></tr>
<tr><td></td></tr>
<!--	Einde Gegevens tbv MOEDERDIER		-->
<tr><td colspan = 50>
	<table border = 0>

	<tr align = "center" style = "font-size : 12px;" height = 30 valign = 'bottom' >
	 <td> <b>Levensnummer</b><hr></td>
	 <td></td> <td><b> werknr </b><hr></td>
	 <td></td> <td><b> Generatie </b><hr></td>
	 <td></td> <td><b> Geslacht </b><hr></td>
	 <td></td> <td><b> Ras </b><hr></td> 
	 <td></td> <td><b> Geboren </b><hr></td>
	 <td></td> <td><b> Gewicht </b><hr></td> 
	 <td></td> <td><b> Speendatum </b><hr></td>
	 <td></td> <td><b> Speen gewicht </b><hr></td>
	 <td></td> <td><b> Gem<br>groei<br>spenen </b><hr></td>
	 <td></td> <td><b> Afvoerdatum </b><hr></td>
	 <td></td> <td><b> Aflever gewicht </b><hr></td>
	 <td></td> <td><b> Reden </b><hr></td>

	 <td></td> <td><b> Gem<br>groei<br>afleveren </b><hr></td>
	</tr><?php
$lammeren = mysqli_query($db,"
SELECT s.levensnummer, right(s.levensnummer,$Karwerk) werknr, r.ras, s.geslacht, ouder.datum dmaanw, date_format(hg.datum,'%d-%m-%Y') gebrndm, date_format(hg.datum,'%Y-%m-%d') dmgebrn, hg.kg gebrnkg, date_format(hs.datum,'%d-%m-%Y') speendm, hs.kg speenkg, 

case when hs.kg-hg.kg > 0 and datediff(hs.datum,hg.datum) > 0 then round(((hs.kg-hg.kg)/datediff(hs.datum,hg.datum)*1000),2) end gemgr_s,

date_format(haf.datum,'%d-%m-%Y') afvdm, haf.kg afvkg, date_format(hdo.datum,'%d-%m-%Y') uitvaldm, re.reden, 

case when haf.kg-hg.kg > 0 and datediff(haf.datum,hg.datum) > 0 then round(((haf.kg-hg.kg)/datediff(haf.datum,hg.datum)*1000),2) end gemgr_a
FROM tblSchaap s
 join tblVolwas v on (v.volwId = s.volwId)
 join tblSchaap mdr on (mdr.schaapId = v.mdrId) 
 join tblStal st on (s.schaapId = st.schaapId)
 left join tblRas r on (s.rasId = r.rasId)
 left join tblReden re on (s.redId = re.redId)
 join tblHistorie hg on (st.stalId = hg.stalId and hg.actId = 1 and hg.skip = 0)
 left join tblHistorie hs on (st.stalId = hs.stalId and hs.actId = 4 and hs.skip = 0)
 left join tblHistorie haf on (st.stalId = haf.stalId and haf.actId = 12 and haf.skip = 0)
 left join tblHistorie hdo on (st.stalId = hdo.stalId and hdo.actId = 14 and hdo.skip = 0)
 join tblStal st_all on (st_all.schaapId = s.schaapId)
 left join tblHistorie ouder on (st_all.stalId = ouder.stalId and ouder.actId = 3 and ouder.skip = 0)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and v.mdrId = '".mysqli_real_escape_string($db,$gekozen_ooi)."'
ORDER BY hg.datum		") or die (mysqli_error($db));	
	while($lam = mysqli_fetch_assoc($lammeren))
			{
				if (empty($lam['levensnummer'])) {$Llevnr = 'Geen';} else {$Llevnr = $lam['levensnummer'];}
				$Lwerknr = $lam['werknr'];
				$Lsekse = $lam['geslacht'];
				$Ldmaanw = $lam['dmaanw'];	if(isset($Ldmaanw))	{ if($Lsekse == 'ooi') { $Lfase = 'moeder'; } if($Lsekse == 'ram') { $Lfase = 'vader'; }  } else { $Lfase = 'lam'; }
				$Lras = $lam['ras'];
				$Ldatum = $lam['gebrndm'];
				$Lkg = $lam['gebrnkg'];
				$Lspndm = $lam['speendm'];
				$Lspnkg = $lam['speenkg'];
				$gemgr_s = $lam['gemgr_s'];
				$Lafldm = $lam['afvdm'];
				$Laflkg = $lam['afvkg'];
				$Luitvdm = $lam['uitvaldm'];
				$Lreden= $lam['reden'];
				$gemgr_a = $lam['gemgr_a'];

	?>	
	<tr align = "center" style = "font-size : 14px";>
	 <td align = "center" > <?php echo $Llevnr; ?>  </td>
	 <td></td> <td> <?php echo $Lwerknr; ?> </td>
	 <td></td> <td> <?php echo $Lfase; ?> </td>
	 <td></td> <td> <?php echo $Lsekse; ?> </td>
	 <td></td> <td> <?php echo $Lras; ?> </td>
	 <td></td> <td width = 70> <?php echo $Ldatum; ?> </td>
	 <td></td> <td> <?php echo $Lkg; ?> </td>
	 <td></td> <td> <?php echo $Lspndm; ?> </td>
	 <td></td> <td> <?php echo $Lspnkg; ?> </td>
	 <td></td> <td width = 50 align = 'right'> <?php echo $gemgr_s."&nbsp&nbsp"; ?> </td>
	 <td></td> <td> <?php echo $Lafldm.$Luitvdm; ?> </td>
	 <td></td> <td> <?php echo $Laflkg; ?> </td>
	 <td></td> <td> <?php if(isset($Luitvdm) && !isset($Lreden)) { echo 'Overleden'; } else { echo $Lreden; } ?> </td>
	 <td></td> <td align = 'right'> <?php echo $gemgr_a."&nbsp"; ?> </td>
	</tr>
	<tr>
	 <td></td>
	</tr>
<?php } ?>
</table>		

<!--	Einde Gegevens tbv LAM	-->	

<?php } /* Einde if (isset($gekozen_ooi))  */ 

	elseif (isset($_POST['knpToon']) && $deel == 0)  { $fout = "Er is geen keuze gemaakt."; } 
	else if(isset($_POST['knpToon'])) { $fout = "Het zoek criterium heeft geen resultaten opgeleverd. Pas het criterum eventueel aan. "; } ?>
 </td>
</tr>
</table>

</TD>

<?php } else { ?> <img src='ooikaart_php.jpg'  width='970' height='550'/> <?php }
include "menuRapport1.php"; } ?>
</tr>
</table>

</body>
</html>
