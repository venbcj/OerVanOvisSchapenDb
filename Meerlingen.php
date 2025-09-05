<?php 
$versie = '26-10-2018'; /* gemaakt */
$versie = '28-12-2023'; /* and h.skip = 0 toegevoegd bij tblHistorie sql voorzien van enkele quotes */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = 'top' align = center > gewijzigd naar <TD valign = 'top' align = 'center'> 31-12-24 include login voor include header gezet */

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Rapport</title>
</head>
<body>

<?php
$titel = 'Meerling per geslacht';
$file = "Meerlingen.php";
include "login.php"; ?>

		<TD valign = 'top' align = 'center'>
<?php
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { if($modtech ==1) {
$huidigjaar = date("Y"); $begin_datum = '1-01-'.$huidigjaar; $eind_datum = '1-03-'.$huidigjaar;

$var1dag = 60*60*24;
	$maak_datum = strtotime($eind_datum) - $var1dag; $eind_datum = date("d-m-Y", $maak_datum);
	/*if (isset($_GET['pstId'])) {$raak = $_GET['pstId']; }*/ ?>

<form action= "Meerlingen.php" method="post">
<table border = 0> 
<tr align = "center" valign = 'top' ><td colspan = 10>	

<table border = 0>
<tr>
 
<?php if (isset($raak)) { ?>
 <td> <a href = '<?php echo $url;?>Meerlingen_pdf.php?Id=<?php echo $raak; ?>' style = 'color : blue' > print pagina </a> </td>
<?php } else if(isset($gekozen_ooi)) { ?>
 <td> <a href = '<?php echo $url;?>Meerlingen_pdf.php?Id=<?php echo $gekozen_ooi; ?>' style = 'color : blue' > print pagina  </a> </td>
<?php } ?>
</tr>

</table>		</td></tr>	

<tr><td colspan = 10 align = "center"><h3>lammeren per moederdier </td></tr>
<tr><td colspan = 10 ><hr></td></tr>
<tr><td></td></tr>
<!--	Einde Gegevens tbv MOEDERDIER		-->
<tr><td colspan = 50><table border = 0>

<?php

if(isset($_POST['ascTotat'])) {	$order = "sum(worp)"; } 
elseif(isset($_POST['descTotat'])) { $order = "sum(worp) desc"; }
else { $order = "ooi"; }

$ooien_met_meerlingworpen = mysqli_query($db,"
SELECT schaapId, ooi, sum(worp) totat
FROM (
	SELECT mdr.schaapId, right(mdr.levensnummer,$Karwerk) ooi, v.volwId, count(lam.schaapId) worp
	FROM tblSchaap mdr
	 join tblStal stm on (stm.schaapId = mdr.schaapId)
	 join tblVolwas v on (mdr.schaapId = v.mdrId)
	 join tblSchaap lam on (v.volwId = lam.volwId)
	 join tblStal st on (lam.schaapId = st.schaapId)
	WHERE isnull(stm.rel_best) and stm.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	GROUP BY mdr.schaapId, right(mdr.levensnummer,$Karwerk), v.volwId
	HAVING count(v.volwId) > 0
	 ) perWorp
GROUP BY schaapId, ooi

ORDER BY $order
") or die(mysqli_error($db));

while($jm = mysqli_fetch_assoc($ooien_met_meerlingworpen)) { 

	$ooiId = $jm['schaapId']; 
	$ooi = $jm['ooi'];
	$totat = $jm['totat'];
	

unset($geengeslacht); // geen geslacht
$zoek_aantal_geengeslacht_tbv_hoofding = mysqli_query($db,"
SELECT count(s.schaapId) aant
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblVolwas v on (s.volwId = v.volwId)
WHERE isnull(s.geslacht) and v.mdrId =  '".mysqli_real_escape_string($db,$ooiId)."'  and st.lidId = '".mysqli_real_escape_string($db,$ooiId)."'
") or die(mysqli_error($db));

while($ga = mysqli_fetch_assoc($zoek_aantal_geengeslacht_tbv_hoofding)) { $geengeslacht = $ga['aant']; }
 ?>

<tr height = 30 valign = 'bottom'>
 <td style = "font-size : 18px;"> <b><?php echo $ooi; ?></b></td>
 <td style = "font-size : 12px;" ><?php echo 'Totaal : '.$totat.'&nbsp'; ?>
 	<input type = "submit" name="ascTotat"  value = "A" style= "font-size:7px";>
	<input type = "submit" name="descTotat" value = "Z" style= "font-size:7px";></td>
</tr>

<tr align = "center" style = "font-size : 14px;"  >
 <td></td>
 <td width = 100><b> maand </b><hr></td>
 <td width = 100><b> Aantal </b><hr></td>
 <td colspan = 2 width = 120><b> ooitjes </b><hr></td>
 <td colspan = 2 width = 120><b> rammen </b><hr></td> 
<?php if(isset($geengeslacht) && $geengeslacht > 0) { ?>
 <td colspan = 2 width = 120> onbekend <hr></td>
<?php } ?> 
</tr>

<?php
$maand = array(1 => 'Jan','Feb','Mrt','Apr','Mei','Jun','Jul','Aug','Sep','Okt','Nov','Dec');

$zoek_meerlingen_ooi = mysqli_query($db,"
SELECT date_format(h.datum,'%m')*1 mnd, date_format(h.datum,'%Y') jaar, count(lam.schaapId) aant, v.volwId
FROM tblSchaap mdr
 join tblVolwas v on (v.mdrId = mdr.schaapId)
 join tblSchaap lam on (v.volwId = lam.volwId)
 join tblStal st on (st.schaapId = lam.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
 
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and mdr.schaapId =  '".mysqli_real_escape_string($db,$ooiId)."'  and h.actId = 1 and h.skip = 0
GROUP BY date_format(h.datum,'%Y%m'), date_format(h.datum,'%Y'), v.volwId
ORDER BY date_format(h.datum,'%Y%m') desc
") or die (mysqli_error($db));	
	while($mrl = mysqli_fetch_assoc($zoek_meerlingen_ooi))
			{
				$mnd = $mrl['mnd'];
				$jaar = $mrl['jaar']; $MaandJaar = $maand[$mnd].' '.$jaar;
				$aant = $mrl['aant'];
				$volwId = $mrl['volwId']; 


unset($ooi_st);
unset($werknr_ooi);
$zoek_aantal_ooitjes = mysqli_query($db,"
SELECT count(s.schaapId) aant
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE s.volwId = '".mysqli_real_escape_string($db,$volwId)."' and s.geslacht = 'ooi' and h.actId = 1 and date_format(h.datum,'%m')*1 = '".mysqli_real_escape_string($db,$mnd)."' and date_format(h.datum,'%Y') = '".mysqli_real_escape_string($db,$jaar)."' and h.skip = 0
		
") or die(mysqli_error($db));

while($oa = mysqli_fetch_assoc($zoek_aantal_ooitjes)) { $ooi_st = $oa['aant']; }

$zoek_werknr_ooitjes = mysqli_query($db,"
SELECT coalesce(right(s.levensnummer,$Karwerk),' ------- ') werknr, kg
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE s.volwId = '".mysqli_real_escape_string($db,$volwId)."' and s.geslacht = 'ooi' and h.actId = 1 and date_format(h.datum,'%m')*1 = '".mysqli_real_escape_string($db,$mnd)."' and date_format(h.datum,'%Y') = '".mysqli_real_escape_string($db,$jaar)."' and h.skip = 0
GROUP BY s.schaapId
		
") or die(mysqli_error($db));

while($ow = mysqli_fetch_assoc($zoek_werknr_ooitjes)) { 
	$wnr = $ow['werknr'];
	$kg = $ow['kg']; if(isset($kg)) { $kg = $kg.' kg'; }
	
	$werknr_ooi[] = array($wnr, $kg);
}


unset($ram_st);
unset($werknr_ram);
$zoek_aantal_ramtjes = mysqli_query($db,"
SELECT count(s.schaapId) aant
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE s.volwId = '".mysqli_real_escape_string($db,$volwId)."' and s.geslacht = 'ram' and h.actId = 1 and date_format(h.datum,'%m')*1 = '".mysqli_real_escape_string($db,$mnd)."' and date_format(h.datum,'%Y') = '".mysqli_real_escape_string($db,$jaar)."' and h.skip = 0

") or die(mysqli_error($db));

while($ra = mysqli_fetch_assoc($zoek_aantal_ramtjes)) { $ram_st = $ra['aant']; }

$zoek_werknr_ramtjes = mysqli_query($db,"
SELECT coalesce(right(s.levensnummer,$Karwerk),' ------- ') werknr, kg
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE s.volwId = '".mysqli_real_escape_string($db,$volwId)."' and s.geslacht = 'ram' and h.actId = 1 and date_format(h.datum,'%m')*1 = '".mysqli_real_escape_string($db,$mnd)."' and date_format(h.datum,'%Y') = '".mysqli_real_escape_string($db,$jaar)."' and h.skip = 0
GROUP BY s.schaapId
		
") or die(mysqli_error($db));

while($rw = mysqli_fetch_assoc($zoek_werknr_ramtjes)) { 
	$wnr = $rw['werknr'];
	$kg = $rw['kg']; if(isset($kg)) { $kg = $kg.' kg'; }
	
	$werknr_ram[] = array($wnr, $kg);
}


unset($gg_st); // geen geslacht
unset($werknr_gg); // geen geslacht
$zoek_aantal_geengeslacht = mysqli_query($db,"
SELECT count(s.schaapId) aant
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE s.volwId = '".mysqli_real_escape_string($db,$volwId)."' and isnull(s.geslacht) and h.actId = 1 and date_format(h.datum,'%m')*1 = '".mysqli_real_escape_string($db,$mnd)."' and date_format(h.datum,'%Y') = '".mysqli_real_escape_string($db,$jaar)."' and h.skip = 0

") or die(mysqli_error($db));

while($ga = mysqli_fetch_assoc($zoek_aantal_geengeslacht)) { $gg_st = $ga['aant']; }

$zoek_werknr_geengeslacht = mysqli_query($db,"
SELECT coalesce(right(s.levensnummer,$Karwerk),' ------- ') werknr, kg
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE s.volwId = '".mysqli_real_escape_string($db,$volwId)."' and isnull(s.geslacht) and h.actId = 1 and date_format(h.datum,'%m')*1 = '".mysqli_real_escape_string($db,$mnd)."' and date_format(h.datum,'%Y') = '".mysqli_real_escape_string($db,$jaar)."' and h.skip = 0
GROUP BY s.schaapId
		
") or die(mysqli_error($db));

while($gw = mysqli_fetch_assoc($zoek_werknr_geengeslacht)) { 
	$wnr = $gw['werknr'];
	$kg = $gw['kg']; if(isset($kg)) { $kg = $kg.' kg'; }
	
	$werknr_gg[] = array($wnr, $kg);
}

	?>	
<tr align = "center" style = "font-size : 15px";>
 <td>  </td>
 <td> <?php echo $MaandJaar ?> </td>

 <td align="center" style = "font-size : 13px";> <?php echo $aant; ?> </td>

 <td align="right" style="font-size: 13px";> <?php if($ooi_st > 0) { echo $ooi_st; } ?> </td>
 <td style = "font-size : 11px"; >
<?php
if(isset($werknr_ooi)) {
	foreach ($werknr_ooi as $array) {
		foreach ($array as $key => $value) {
			echo $value.'&nbsp&nbsp';
		}
		echo '<br>';
	}
}

 ?> </td>
 
 <td align="right" style = "font-size : 13px";> <?php if($ram_st > 0) { echo $ram_st; } ?> </td>

 <td style = "font-size : 11px"; >
<?php
if(isset($werknr_ram)) {
	foreach ($werknr_ram as $array) {
		foreach ($array as $key => $value) {
			echo $value.'&nbsp&nbsp';
		}
		echo '<br>';
	}
}

 ?> </td>
 
 <td align="right" style = "font-size : 13px";> <?php if($gg_st > 0) { echo $gg_st; } ?> </td>

 <td style = "font-size : 11px"; >
<?php
if(isset($werknr_gg)) {
	foreach ($werknr_gg as $array) {
		foreach ($array as $key => $value) {
			echo $value.'&nbsp&nbsp';
		}
		echo '<br>';
	}
}

 ?> </td>
 
 <td align="left" style = "font-size : 11px";>  </td>
 
 <td align = 'right'>  </td>

</tr> <tr>
<tr><td>

</td>
</tr>
<?php } // Einde while($mrl = mysqli_fetch_assoc($zoek_meerlingen_ooi)) 
} 		//$zoek_ooien_uit_periode ?>
</table>		

<!--	Einde Gegevens tbv LAM	-->	

</td></tr></table>
</form>

</TD>
<?php } else { ?> <img src='ooikaart_php.jpg'  width='970' height='550'/> <?php }
include "menuRapport1.php"; } ?>
</tr>
</table>

</body>
</html>
