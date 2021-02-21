<?php 
$versie = '1-2-2019'; /* gemaakt */
 session_start(); ?>
<html>
<head>
<title>Rapport</title>
</head>
<body>

<center>
<?php
$titel = 'Meerling oplopend';
$subtitel = '';
Include "header.php"; ?>
<TD width = 960 height = 400 valign = 'top' align = center >
<?php
$file = "Meerlingen3.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { if($modtech ==1) {


function aantal_meerlingen_perOoi($datb,$Lidid,$Ooiid,$Nr) {

$zoek_meerlingen = "
SELECT v.volwId
FROM tblSchaap mdr
 join tblStal stm on (stm.schaapId = mdr.schaapId)
 join tblVolwas v on (v.mdrId = mdr.schaapId)
 join tblSchaap lam on (v.volwId = lam.volwId)
 join tblStal st on (st.schaapId = lam.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
 
WHERE isnull(stm.rel_best) and st.lidId = ".mysqli_real_escape_string($datb,$Lidid)." and h.actId = 1 and mdr.schaapId = ".mysqli_real_escape_string($datb,$Ooiid)."
GROUP BY v.volwId
HAVING count(st.schaapId) in (".mysqli_real_escape_string($datb,$Nr).")
ORDER BY date_format(h.datum,'%Y') desc, date_format(h.datum,'%m') desc
";
//echo $zoek_meerlingen;

return $zoek_meerlingen;

} 

function periode($datb,$Volwid) {

	$zoek_periode = mysqli_query($datb,"
SELECT date_format(h.datum,'%Y') jaar, date_format(h.datum,'%m')*1 mndnr
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE s.volwId = ".mysqli_real_escape_string($datb,$Volwid)." and h.actId = 1
GROUP BY date_format(h.datum,'%Y'), date_format(h.datum,'%m')
") or die(mysqli_error($datb));

while($a = mysqli_fetch_assoc($zoek_periode)) { 
	
	return array(1=>$a['mndnr'], $a['jaar']); 
	}
}


function de_lammeren($datb,$Volwid,$KarWerk) {
	$zoek_lammeren = mysqli_query($datb,"
SELECT coalesce(geslacht,'---') geslacht, coalesce(right(s.levensnummer,$KarWerk),'-------') werknr
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE s.volwId = ".mysqli_real_escape_string($datb,$Volwid)." and h.actId = 1
ORDER BY coalesce(geslacht,'zzz')
") or die(mysqli_error($datb));

while($a = mysqli_fetch_assoc($zoek_lammeren)) { $rr[] = array($a['geslacht'], $a['werknr']); 
 }
return $rr;
} ?>

<form action= "Meerlingen3.php" method="post">
<table border = 0> 
<tr align = center valign = 'top' ><td colspan = 10>	

<table border = 0>
<tr>
 
<?php if (isset($raak)) { ?>
 <td> <a href = '<?php echo $url;?>Meerlingen3_pdf.php?Id=<?php echo $raak; ?>' style = 'color : blue' > print pagina </a> </td>
<?php } else if(isset($gekozen_ooi)) { ?>
 <td> <a href = '<?php echo $url;?>Meerlingen3_pdf.php?Id=<?php echo $gekozen_ooi; ?>' style = 'color : blue' > print pagina  </a> </td>
<?php } ?>
</tr>

</table>		</td></tr>	

<tr><td colspan = 10 align = center><h3>lammeren per moederdier </td></tr>
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
	WHERE isnull(stm.rel_best) and stm.lidId = ".mysqli_real_escape_string($db,$lidId)." and st.lidId = ".mysqli_real_escape_string($db,$lidId)."
	GROUP BY mdr.schaapId, right(mdr.levensnummer,$Karwerk), v.volwId
	HAVING count(v.volwId) > 1
	 ) perWorp
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
 	<input type = "submit" name="ascTotat"  value = "A" style= "font-size:7px";>
	<input type = "submit" name="descTotat" value = "Z" style= "font-size:7px";></td>
</tr>

<tr align = center style = "font-size : 14px;"  >
 <td></td>
 <td><b> 2-ling </b><hr></td>
 <td><b> 3-ling </b><hr></td>
 <td><b> 4-ling </b><hr></td> 
 <td><b> 5-ling </b><hr></td>
 <td><b> > 5-ling </b><hr></td>

</tr>

<?php $maand = array(1 => 'Jan','Feb','Mrt','Apr','Mei','Jun','Jul','Aug','Sep','Okt','Nov','Dec'); ?>	
<tr>
 <td>  </td>


 <td>
<!-- Cel waar tweelingen worden getoond -->

<table border = 0>

<?php
$mling2 = aantal_meerlingen_perOoi($db,$lidId,$ooiId,2); // Deze functie geeft een querystring
$mling2 = mysqli_query($db,$mling2) or die (mysqli_error($db));	
	while($mrl = mysqli_fetch_assoc($mling2))
			{
				$vw = $mrl['volwId']; 
			
 
?>
<tr>
 <td width = 60 align="left" style = "font-size : 13px";> <?php
 
$p_mrl2 = periode($db,$vw);

echo $maand[$p_mrl2[1]].' '.$p_mrl2[2];

 ?>
 </td>
 <td width = 60 align="right" style="font-size: 11px"; >
 <?php
 
$lam_mrl2 = de_lammeren($db,$vw,$Karwerk);
 
 foreach ($lam_mrl2 as $key => $value) {
 	echo $value[0].' '.$value[1].'<br>';
 }
//echo $ooi_st.' '.$geslacht.'<br>';

?>

</td>
</tr>
<?php } ?>
</table>

<!-- Einde Cel waar tweelingen worden getoond -->
 </td>


 <td>
<!-- Cel waar drielingen worden getoond -->

<table border = 0>

<?php
$mling3 = aantal_meerlingen_perOoi($db,$lidId,$ooiId,3); // Deze functie geeft een querystring
$mling3 = mysqli_query($db,$mling3) or die (mysqli_error($db));	
	while($mrl = mysqli_fetch_assoc($mling3))
			{
				$vw = $mrl['volwId']; 
			
 
?>
<tr>
 <td width = 60 align="left" style = "font-size : 13px";> <?php
 
$p_mrl3 = periode($db,$vw);

echo $maand[$p_mrl3[1]].' '.$p_mrl3[2];

 ?>
 </td>
 <td width = 60 align="right" style = "font-size : 11px";>
 <?php
 
$lam_mrl2 = de_lammeren($db,$vw,$Karwerk);
 
 foreach ($lam_mrl2 as $key => $value) {
 	echo $value[0].' '.$value[1].'<br>';
 }
//echo $ooi_st.' '.$geslacht.'<br>';

?>

</td>
</tr>
<?php } ?>
</table>

<!-- Einde Cel waar drielingen worden getoond -->
 </td>
 
 <td>
<!-- Cel waar vierlingen worden getoond -->

<table border = 0>

<?php
$mling4 = aantal_meerlingen_perOoi($db,$lidId,$ooiId,4); // Deze functie geeft een querystring
$mling4 = mysqli_query($db,$mling4) or die (mysqli_error($db));	
	while($mrl = mysqli_fetch_assoc($mling4))
			{
				$vw = $mrl['volwId']; 
			
 
?>
<tr>
 <td width = 60 align="left" style = "font-size : 13px";> <?php
 
$p_mrl4 = periode($db,$vw);

echo $maand[$p_mrl4[1]].' '.$p_mrl4[2];

 ?>
 </td>
 <td width = 60 align="right" style = "font-size : 11px";>
 <?php
 
$lam_mrl2 = de_lammeren($db,$vw,$Karwerk);
 
 foreach ($lam_mrl2 as $key => $value) {
 	echo $value[0].' '.$value[1].'<br>';
 }
//echo $ooi_st.' '.$geslacht.'<br>';

?>

</td>
</tr>
<?php } ?>
</table>

<!-- Einde Cel waar vierlingen worden getoond -->
 </td>

  <td>
<!-- Cel waar vijflingen worden getoond -->

<table border = 0>

<?php
$mling5 = aantal_meerlingen_perOoi($db,$lidId,$ooiId,5); // Deze functie geeft een querystring
$mling5 = mysqli_query($db,$mling5) or die (mysqli_error($db));	
	while($mrl = mysqli_fetch_assoc($mling5))
			{
				$vw = $mrl['volwId']; 
			
 
?>
<tr>
 <td width = 60 align="left" style = "font-size : 13px";> <?php
 
$p_mrl5 = periode($db,$vw);

echo $maand[$p_mrl5[1]].' '.$p_mrl5[2];

 ?>
 </td>
 <td width = 60 align="right" style = "font-size : 11px";>
 <?php
 
$lam_mrl2 = de_lammeren($db,$vw,$Karwerk);
 
 foreach ($lam_mrl2 as $key => $value) {
 	echo $value[0].' '.$value[1].'<br>';
 }
//echo $ooi_st.' '.$geslacht.'<br>';

?>

</td>
</tr>
<?php } ?>
</table>

<!-- Einde Cel waar vijflingen worden getoond -->
 </td>
   <td>
<!-- Cel waar meer dan vijflingen worden getoond -->

<table border = 0>

<?php
$mling5 = aantal_meerlingen_perOoi($db,$lidId,$ooiId,'6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30'); // Deze functie geeft een querystring
$mling5 = mysqli_query($db,$mling5) or die (mysqli_error($db));	
	while($mrl = mysqli_fetch_assoc($mling5))
			{
				$vw = $mrl['volwId']; 
			
 
?>
<tr>
 <td width = 60 align="left" style = "font-size : 13px";> <?php
 
$p_mrl5 = periode($db,$vw);

echo $maand[$p_mrl5[1]].' '.$p_mrl5[2];

 ?>
 </td>
 <td width = 60 align="right" style = "font-size : 11px";>
 <?php
 
$lam_mrl2 = de_lammeren($db,$vw,$Karwerk);
 
 foreach ($lam_mrl2 as $key => $value) {
 	echo $value[0].' '.$value[1].'<br>';
 }
//echo $ooi_st.' '.$geslacht.'<br>';

?>

</td>
</tr>
<?php } ?>
</table>

<!-- Einde Cel waar meer dan vijflingen worden getoond -->
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

} // Einde $zoek_ooien_uit_periode
?>
</table>		

<!--	Einde Gegevens tbv LAM	-->	

</td></tr></table>
</form>

</TD>
<?php } else { ?> <img src='ooikaart_php.jpg'  width='970' height='550'/> <?php }
Include "menuRapport1.php"; } ?>
</tr>
</table>
</center>

</body>
</html>
