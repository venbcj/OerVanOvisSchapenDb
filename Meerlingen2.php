<?php 
$versie = '1-2-2019'; /* gemaakt */
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
$titel = 'Meerlingen per jaar';
$file = "Meerlingen2.php";
include "login.php"; ?>

		<TD valign = 'top' align = 'center'>
<?php
if (is_logged_in()) { if($modtech ==1) {
$huidigjaar = date("Y"); $begin_datum = '1-01-'.$huidigjaar; $eind_datum = '1-03-'.$huidigjaar;

$var1dag = 60*60*24;
	$maak_datum = strtotime($eind_datum) - $var1dag; $eind_datum = date("d-m-Y", $maak_datum);
	/*if (isset($_GET['pstId'])) {$raak = $_GET['pstId']; }*/

$jaar1 = $huidigjaar;
$jaar2 = $huidigjaar-1;
$jaar3 = $huidigjaar-2;
$jaar4 = $huidigjaar-3; 

function meerlingen_perOoi_perJaar($datb,$Lidid,$Ooiid,$Jaar,$Maand) {

$zoek_meerlingen = "
SELECT count(lam.schaapId) aant, v.volwId
FROM tblSchaap mdr
 join tblVolwas v on (v.mdrId = mdr.schaapId)
 join tblSchaap lam on (v.volwId = lam.volwId)
 join tblStal st on (st.schaapId = lam.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
 
WHERE st.lidId = ".mysqli_real_escape_string($datb,$Lidid)." and mdr.schaapId = ".mysqli_real_escape_string($datb,$Ooiid)." and h.actId = 1 and date_format(h.datum,'%Y') = '".mysqli_real_escape_string($datb,$Jaar)."' and date_format(h.datum,'%m') = '".mysqli_real_escape_string($datb,$Maand)."' and h.skip = 0
GROUP BY v.volwId
ORDER BY date_format(h.datum,'%Y%m') desc
";
//echo $zoek_meerlingen;
$zoek_meerlingen = mysqli_query($datb,$zoek_meerlingen) or die (mysqli_error($datb));	
	while($mrl = mysqli_fetch_assoc($zoek_meerlingen))
			{
				return array($mrl['aant'], $mrl['volwId']); 
			} 

} 

function aantal_perGeslacht($datb,$Volwid,$Geslacht,$Jaar,$Maand) {
	$zoek_aantal_geslacht = mysqli_query($datb,"
SELECT count(s.schaapId) aant
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE s.volwId = ".mysqli_real_escape_string($datb,$Volwid)." and s.geslacht = '".mysqli_real_escape_string($datb,$Geslacht)."' and h.actId = 1 and date_format(h.datum,'%m') = ".mysqli_real_escape_string($datb,$Maand)." and date_format(h.datum,'%Y') = ".mysqli_real_escape_string($datb,$Jaar)." and h.skip = 0		
") or die(mysqli_error($datb));

while($a = mysqli_fetch_assoc($zoek_aantal_geslacht)) { return $a['aant']; }

} ?>

<form action= "Meerlingen2.php" method="post">
<table border = 0> 
<tr align = "center" valign = 'top' ><td colspan = 10>	

<table border = 0>
<tr>
 
<?php if (isset($raak)) { ?>
 <td> <a href = '<?php echo $url;?>Meerlingen2_pdf.php?Id=<?php echo $raak; ?>' style = 'color : blue' > print pagina </a> </td>
<?php } else if(isset($gekozen_ooi)) { ?>
 <td> <a href = '<?php echo $url;?>Meerlingen2_pdf.php?Id=<?php echo $gekozen_ooi; ?>' style = 'color : blue' > print pagina  </a> </td>
<?php } ?>
</tr>

</table>		</td></tr>	

<tr><td colspan = 10 align = "center"><h3>lammeren per moederdier </td></tr>
<tr><td colspan = 10 ><hr></td></tr>
<tr><td></td></tr>
<!--	Einde Gegevens tbv MOEDERDIER		-->
<tr><td colspan = 50><table border = 0>
<tr height = 30 valign = 'bottom'>
 <td> </td>
 <td >
 	<input type = "submit" name="ascTotat"  value = "A" style= "font-size:7px";>
	<input type = "submit" name="descTotat" value = "Z" style= "font-size:7px";>
 </td>
 <td align ="center" style = "font-size : 12px;" >
 	<input type = "submit" name="ascJaar1"  value = "A" style= "font-size:7px";>
	<input type = "submit" name="descJaar1" value = "Z" style= "font-size:7px";>
 </td>
 <td align ="center" style = "font-size : 12px;" >
 	<input type = "submit" name="ascJaar2"  value = "A" style= "font-size:7px";>
	<input type = "submit" name="descJaar2" value = "Z" style= "font-size:7px";>
 </td>
 <td align ="center" style = "font-size : 12px;" >
 	<input type = "submit" name="ascJaar3"  value = "A" style= "font-size:7px";>
	<input type = "submit" name="descJaar3" value = "Z" style= "font-size:7px";>
 </td>
 <td align ="center" style = "font-size : 12px;" >
 	<input type = "submit" name="ascJaar4"  value = "A" style= "font-size:7px";>
	<input type = "submit" name="descJaar4" value = "Z" style= "font-size:7px";>
 </td>
</tr>
<?php

if(isset($_POST['ascTotat'])) {	$order = "sum(perWorp.worp), ooi asc"; } 
elseif(isset($_POST['descTotat'])) { $order = "sum(perWorp.worp) desc, ooi asc"; }

elseif(isset($_POST['ascJaar1'])) { $order = "sum(perWorp_jr1.worp) asc, ooi asc"; }
elseif(isset($_POST['descJaar1'])) { $order = "sum(perWorp_jr1.worp) desc, ooi asc"; }

elseif(isset($_POST['ascJaar2'])) { $order = "sum(perWorp_jr2.worp) asc, ooi asc"; }
elseif(isset($_POST['descJaar2'])) { $order = "sum(perWorp_jr2.worp) desc, ooi asc"; }

elseif(isset($_POST['ascJaar3'])) { $order = "sum(perWorp_jr3.worp) asc, ooi asc"; }
elseif(isset($_POST['descJaar3'])) { $order = "sum(perWorp_jr3.worp) desc, ooi asc"; }

elseif(isset($_POST['ascJaar4'])) { $order = "sum(perWorp_jr4.worp) asc, ooi asc"; }
elseif(isset($_POST['descJaar4'])) { $order = "sum(perWorp_jr4.worp) desc, ooi asc"; }

else { $order = "ooi"; }

$ooien_met_meerlingworpen = mysqli_query($db,"
SELECT perWorp.schaapId, ooi, sum(perWorp.worp) totat, sum(perWorp_jr1.worp) jr1, sum(perWorp_jr2.worp) jr2, sum(perWorp_jr3.worp) jr3, sum(perWorp_jr4.worp) jr4
FROM (
	SELECT mdr.schaapId, right(mdr.levensnummer,$Karwerk) ooi, v.volwId, count(lam.schaapId) worp
	FROM tblSchaap mdr
	 join tblStal stm on (stm.schaapId = mdr.schaapId)
	 join tblVolwas v on (mdr.schaapId = v.mdrId)
	 join tblSchaap lam on (v.volwId = lam.volwId)
	 join tblStal st on (lam.schaapId = st.schaapId)
	 join tblHistorie h on (h.stalId = st.stalId)
	WHERE isnull(stm.rel_best) and stm.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and date_format(h.datum,'%Y') <= '".mysqli_real_escape_string($db,$jaar1)."' and date_format(h.datum,'%Y') >= '".mysqli_real_escape_string($db,$jaar4)."' and h.actId = 1 and h.skip = 0
	GROUP BY mdr.schaapId, right(mdr.levensnummer,$Karwerk), v.volwId
	HAVING count(v.volwId) > 0
 ) perWorp
left join (
	SELECT mdr.schaapId, v.volwId, count(lam.schaapId) worp
	FROM tblSchaap mdr
	 join tblStal stm on (stm.schaapId = mdr.schaapId)
	 join tblVolwas v on (mdr.schaapId = v.mdrId)
	 join tblSchaap lam on (v.volwId = lam.volwId)
	 join tblStal st on (lam.schaapId = st.schaapId)
	 join tblHistorie h on (h.stalId = st.stalId)
	WHERE isnull(stm.rel_best) and stm.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	 and h.actId = 1 and date_format(h.datum,'%Y') = '".mysqli_real_escape_string($db,$jaar1)."' and h.skip = 0
	GROUP BY mdr.schaapId, v.volwId
	HAVING count(v.volwId) > 0
) perWorp_jr1  on (perWorp.volwId = perWorp_jr1.volwId)
left join (
	SELECT mdr.schaapId, v.volwId, count(lam.schaapId) worp
	FROM tblSchaap mdr
	 join tblStal stm on (stm.schaapId = mdr.schaapId)
	 join tblVolwas v on (mdr.schaapId = v.mdrId)
	 join tblSchaap lam on (v.volwId = lam.volwId)
	 join tblStal st on (lam.schaapId = st.schaapId)
	 join tblHistorie h on (h.stalId = st.stalId)
	WHERE isnull(stm.rel_best) and stm.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	 and h.actId = 1 and date_format(h.datum,'%Y') = '".mysqli_real_escape_string($db,$jaar2)."' and h.skip = 0
	GROUP BY mdr.schaapId, v.volwId
	HAVING count(v.volwId) > 0
) perWorp_jr2 on (perWorp.volwId = perWorp_jr2.volwId)
left join (
	SELECT mdr.schaapId, v.volwId, count(lam.schaapId) worp
	FROM tblSchaap mdr
	 join tblStal stm on (stm.schaapId = mdr.schaapId)
	 join tblVolwas v on (mdr.schaapId = v.mdrId)
	 join tblSchaap lam on (v.volwId = lam.volwId)
	 join tblStal st on (lam.schaapId = st.schaapId)
	 join tblHistorie h on (h.stalId = st.stalId)
	WHERE isnull(stm.rel_best) and stm.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	 and h.actId = 1 and date_format(h.datum,'%Y') = '".mysqli_real_escape_string($db,$jaar3)."' and h.skip = 0
	GROUP BY mdr.schaapId, v.volwId
	HAVING count(v.volwId) > 0
) perWorp_jr3 on (perWorp.volwId = perWorp_jr3.volwId)
left join (
	SELECT mdr.schaapId, v.volwId, count(lam.schaapId) worp
	FROM tblSchaap mdr
	 join tblStal stm on (stm.schaapId = mdr.schaapId)
	 join tblVolwas v on (mdr.schaapId = v.mdrId)
	 join tblSchaap lam on (v.volwId = lam.volwId)
	 join tblStal st on (lam.schaapId = st.schaapId)
	 join tblHistorie h on (h.stalId = st.stalId)
	WHERE isnull(stm.rel_best) and stm.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	 and h.actId = 1 and date_format(h.datum,'%Y') = '".mysqli_real_escape_string($db,$jaar4)."' and h.skip = 0
	GROUP BY mdr.schaapId, v.volwId
	HAVING count(v.volwId) > 0
) perWorp_jr4 on (perWorp.volwId = perWorp_jr4.volwId)
GROUP BY schaapId, ooi

ORDER BY $order
") or die(mysqli_error($db));

while($jm = mysqli_fetch_assoc($ooien_met_meerlingworpen)) { 

	$ooiId = $jm['schaapId']; 
	$ooi = $jm['ooi'];
	$totat = $jm['totat'];
 ?>

<tr height = 30 valign = 'bottom'>
 <td style = "font-size : 18px;"> <b><?php echo $ooi; ?></b></td>
 <td style = "font-size : 12px;" ><?php echo 'Totaal : '.$totat.'&nbsp'; ?>
 </td>
</tr>

<tr align = "center" style = "font-size : 14px;"  >
 <td></td>
 <td width = 100><b> maand </b><hr></td>
 <td width = 50><b> <?php echo $jaar1; ?> </b><hr></td>
 <td width = 50><b> <?php echo $jaar2; ?> </b><hr></td>
 <td width = 50><b> <?php echo $jaar3; ?> </b><hr></td> 
 <td width = 50><b> <?php echo $jaar4; ?> </b><hr></td>

</tr>

<?php
$maand = array(1 => 'Jan','Feb','Mrt','Apr','Mei','Jun','Jul','Aug','Sep','Okt','Nov','Dec');

$zoek_maanden_per_ooi = "
SELECT date_format(h.datum,'%m') mndtxt, date_format(h.datum,'%m')*1 mndnr
FROM tblSchaap mdr
 join tblVolwas v on (v.mdrId = mdr.schaapId)
 join tblSchaap lam on (v.volwId = lam.volwId)
 join tblStal st on (st.schaapId = lam.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
 
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and mdr.schaapId = '".mysqli_real_escape_string($db,$ooiId)."' and h.actId = 1 and date_format(h.datum,'%Y') <= '".mysqli_real_escape_string($db,$jaar1)."' and date_format(h.datum,'%Y') >= '".mysqli_real_escape_string($db,$jaar4)."' and h.skip = 0
GROUP BY date_format(h.datum,'%m')
ORDER BY date_format(h.datum,'%m')
";
//echo $zoek_maanden_per_ooi; 
$zoek_maanden_per_ooi = mysqli_query($db,$zoek_maanden_per_ooi) or die (mysqli_error($db));	
	while($mrl = mysqli_fetch_assoc($zoek_maanden_per_ooi))
			{ $maandtxt = $mrl['mndtxt']; 
			  $maandnr = $mrl['mndnr']; #echo $maandnr.'<br>';
			
/* Gegevens jaar 1 opvragen : meerling, aantal ooitjes en/of rammetjes */
unset($ooi_st1);
unset($ram_st1);
$jaar_1 = meerlingen_perOoi_perJaar($db,$lidId,$ooiId,$jaar1,$maandtxt);

	$volwId = $jaar_1[1];

if(isset($volwId)) {

$ooi_st1 = aantal_perGeslacht($db,$volwId,'ooi',$jaar1,$maandtxt); if($ooi_st1 == 1) { $vrouw1 = 'ooitje'; } else { $vrouw1 = 'ooitjes'; }
$ram_st1 = aantal_perGeslacht($db,$volwId,'ram',$jaar1,$maandtxt); if($ram_st1 == 1) { $man1 = 'ram'; } else { $man1 = 'rammen'; }

unset($volwId);
}
/* Einde Gegevens jaar 1 opvragen : meerling, aantal ooitjes en/of rammetjes */

/* Gegevens jaar 2 opvragen : meerling, aantal ooitjes en/of rammetjes */
unset($ooi_st2);
unset($ram_st2);
$jaar_2 = meerlingen_perOoi_perJaar($db,$lidId,$ooiId,$jaar2,$maandtxt);

	$volwId = $jaar_2[1]; 

if(isset($volwId)) {

$ooi_st2 = aantal_perGeslacht($db,$volwId,'ooi',$jaar2,$maandtxt); if($ooi_st2 == 1) { $vrouw2 = 'ooitje'; } else { $vrouw2 ='ooitjes'; }
$ram_st2 = aantal_perGeslacht($db,$volwId,'ram',$jaar2,$maandtxt); if($ram_st2 == 1) { $man2 = 'ram'; } else { $man2 = 'rammen'; }

unset($volwId);
}
/* Einde Gegevens jaar 2 opvragen : meerling, aantal ooitjes en/of rammetjes */

/* Gegevens jaar 3 opvragen : meerling, aantal ooitjes en/of rammetjes */
unset($ooi_st3); 
unset($ram_st3);
$jaar_3 = meerlingen_perOoi_perJaar($db,$lidId,$ooiId,$jaar3,$maandtxt);

	$volwId = $jaar_3[1];

if(isset($volwId)) {

$ooi_st3 = aantal_perGeslacht($db,$volwId,'ooi',$jaar3,$maandtxt); if($ooi_st3 == 1) { $vrouw3 = 'ooitje'; } else { $vrouw3 = 'ooitjes'; }
$ram_st3 = aantal_perGeslacht($db,$volwId,'ram',$jaar3,$maandtxt); if($ram_st3 == 1) { $man3 = 'ram'; } else { $man3 = 'rammen'; }

unset($volwId);
}
/* Einde Gegevens jaar 3 opvragen : meerling, aantal ooitjes en/of rammetjes */

/* Gegevens jaar 4 opvragen : meerling, aantal ooitjes en/of rammetjes */
unset($ooi_st4);
unset($ram_st4);
$jaar_4 = meerlingen_perOoi_perJaar($db,$lidId,$ooiId,$jaar4,$maandtxt);

	$volwId = $jaar_4[1];

if(isset($volwId)) {

$ooi_st4 = aantal_perGeslacht($db,$volwId,'ooi',$jaar4,$maandtxt); if($ooi_st4 == 1) { $vrouw4 = 'ooitje'; } else { $vrouw4 = 'ooitjes'; }
$ram_st4 = aantal_perGeslacht($db,$volwId,'ram',$jaar4,$maandtxt); if($ram_st4 == 1) { $man4 = 'ram'; } else { $man4 = 'rammen'; }

unset($volwId);
}
/* Einde Gegevens jaar 4 opvragen : meerling, aantal ooitjes en/of rammetjes */

	?>	
<tr align = "center" style = "font-size : 15px";>
 <td>  </td>
 <td> <?php echo $maand[$maandnr]; ?> </td>

 <td align="left" style = "font-size : 13px";> <?php 
 	if(isset($ooi_st1) && $ooi_st1 > 0)										{ echo $ooi_st1.' '.$vrouw1; } 
 	if(isset($ooi_st1) && $ooi_st1 > 0 && isset($ram_st1) && $ram_st1 > 0) 	{ echo '<br>'; }
 	if(isset($ram_st1) && $ram_st1 > 0) 									{ echo $ram_st1.' '.$man1; } ?>
 </td>

 <td align="left" style="font-size: 13px";> <?php 
 	if(isset($ooi_st2) && $ooi_st2 > 0)										{ echo $ooi_st2.' '.$vrouw2; } 
 	if(isset($ooi_st2) && $ooi_st2 > 0 && isset($ram_st2) && $ram_st2 > 0) 	{ echo '<br>'; }
 	if(isset($ram_st2) && $ram_st2 > 0) 									{ echo $ram_st2.' '.$man2; } ?>
 </td>


 <td align="left" style = "font-size : 13px";> <?php 
 	if(isset($ooi_st3) && $ooi_st3 > 0)										{ echo $ooi_st3.' '.$vrouw3; } 
 	if(isset($ooi_st3) && $ooi_st3 > 0 && isset($ram_st3) && $ram_st3 > 0) 	{ echo '<br>'; }
 	if(isset($ram_st3) && $ram_st3 > 0) 									{ echo $ram_st3.' '.$man3; } ?>
 </td>

 
 
 <td align="left" style = "font-size : 13px";> <?php 
 	if(isset($ooi_st4) && $ooi_st4 > 0)										{ echo $ooi_st4.' '.$vrouw4; } 
 	if(isset($ooi_st4) && $ooi_st4 > 0 && isset($ram_st4) && $ram_st4 > 0) 	{ echo '<br>'; }
 	if(isset($ram_st4) && $ram_st4 > 0) 									{ echo $ram_st4.' '.$man4; } ?>
 </td>

 <td style = "font-size : 11px"; >
 </td>
 
 <td align="left" style = "font-size : 11px";>  </td>
 
 <td align = 'right'>  </td>

</tr> <tr>
<tr><td>

</td>
</tr>
<?php 

} // Einde $zoek_maanden_per_ooi

} 		//$ooien_met_meerlingworpen ?>
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
