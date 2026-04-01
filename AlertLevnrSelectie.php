<?php 
$versie = '28-03-2026'; /* gemaakt */

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Rapport</title>
</head>
<body>

<?php
$titel = 'Controle levensnummers uit reader';
$file = "AlertLevnrSelectie.php";
Include "login.php"; ?>

		<TD valign = 'top' align = 'center'>
<?php
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { if($modtech ==1) {

include "kalender.php";

if(isset($_POST['knpZoek_']) || isset($_POST['knpStuur_'])) {
$datumvan = $_POST['txtDatumVan_']; $van = date_format(date_create($datumvan), 'Y-m-d');
$datumtot = $_POST['txtDatumTot_']; $tot = date_format(date_create($datumtot), 'Y-m-d');

$query = "
SELECT 'Nummers die ook niet bestaan in tblHistorie als oud nummer' toel, a.transponder, a.levensnummer, date_format(a.dmcreate,'%d-%m-%Y') datum, ac.actie taak, a.dmcreate
FROM impAgrident a
 join tblActie ac on (ac.actId = a.actId)
 left join tblSchaap s on (a.levensnummer = s.levensnummer)
 left join tblHistorie h on (a.levensnummer = h.oud_nummer)
WHERE ((a.verwerkt = 1 and a.actId = 1) or (a.actId != 17)) and isnull(s.schaapId) and isnull(h.hisId) and a.levensnummer is not null and a.lidId = '".mysqli_real_escape_string($db,$lidId)."' and date_format(a.dmcreate,'%Y-%m-%d') >= '".mysqli_real_escape_string($db,$van)."' and date_format(a.dmcreate,'%Y-%m-%d') <= '".mysqli_real_escape_string($db,$tot)."'

UNION

SELECT 'Nieuwe nummers bij omnummeren', a.nieuw_transponder, a.nieuw_nummer, date_format(a.dmcreate,'%d-%m-%Y') datum, ac.actie taak, a.dmcreate
FROM impAgrident a
 join tblActie ac on (ac.actId = a.actId)
 left join tblSchaap s on (a.nieuw_nummer = s.levensnummer)
WHERE a.actId = 17 and isnull(s.schaapId) and a.levensnummer is not null and a.lidId = '".mysqli_real_escape_string($db,$lidId)."' and date_format(a.dmcreate,'%Y-%m-%d') >= '".mysqli_real_escape_string($db,$van)."' and date_format(a.dmcreate,'%Y-%m-%d') <= '".mysqli_real_escape_string($db,$tot)."'

UNION

SELECT 'Oude nummers bij omnummeren', a.transponder, a.levensnummer, date_format(a.dmcreate,'%d-%m-%Y') datum, ac.actie taak, a.dmcreate
FROM impAgrident a
 join tblActie ac on (ac.actId = a.actId)
 left join tblHistorie h on (a.levensnummer = h.oud_nummer)
WHERE a.actId = 17 and isnull(h.hisId) and a.levensnummer is not null and a.lidId = '".mysqli_real_escape_string($db,$lidId)."' and date_format(a.dmcreate,'%Y-%m-%d') >= '".mysqli_real_escape_string($db,$van)."' and date_format(a.dmcreate,'%Y-%m-%d') <= '".mysqli_real_escape_string($db,$tot)."'

ORDER BY dmcreate
";

if(isset($_POST['knpStuur_'])) { 

$zoek_laatste_selectie = mysqli_query($db,"
SELECT max(volgnr) volgnr
FROM tblAlertselectie
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die (mysqli_error($db));

while($zs = mysqli_fetch_assoc($zoek_laatste_selectie))
			{
				$old_volgnr = $zs['volgnr']; }

if(!isset($old_volgnr)) { $volgnr = 1; } else { $volgnr = $old_volgnr + 1; }

$zoek_transponder = mysqli_query($db,$query) or die (mysqli_error($db));	
	while($zt = mysqli_fetch_assoc($zoek_transponder))
			{
				$transponder = $zt['transponder'].$zt['levensnummer']; 

 $insert_tblAlertselectie  = "INSERT INTO tblAlertselectie set volgnr = '".mysqli_real_escape_string($db,$volgnr)."', lidId = '".mysqli_real_escape_string($db,$lidId)."', transponder = '".mysqli_real_escape_string($db,$transponder)."', alertId = 7 ";


/*echo $insert_tblAlertselectie.'<br>';*/ mysqli_query($db,$insert_tblAlertselectie) or die (mysqli_error($db));


			}

$goed = 'De levensnummers zijn verstuurd en staan klaar om naar de reader te worden vertuurd.';

} // Einde if(isset($_POST['knpStuur_']))
} // Einde if(isset($_POST['knpZoek_']) || isset($_POST['knpStuur_'])) ?>

<form action= "AlertLevnrSelectie.php" method="post">
<table border = 0> 

<tr>
<td align="right"><i>Reader geleegd vanaf &nbsp</i></td>
 <td align="left"><i>&nbsp&nbsp&nbsp tot en met</i></td>
</tr>
<tr>
<td align="right"><input id = "datepicker1" type= text name = "txtDatumVan_" size = "8" value = <?php if(isset($datumvan)) { echo "$datumvan"; } ?> ></td>
 <td align="left"><input id = "datepicker2" type= text name = "txtDatumTot_" size = "8" value = <?php if(isset($datumtot)) { echo "$datumtot"; } ?> >
  <input type="submit" name="knpZoek_" value="Zoek">
 </td>
</tr>
<tr><td colspan = 10 ><hr></td></tr>

<tr><td colspan = 50><table border = 0>
<?php if(isset($_POST['knpZoek_']) || isset($_POST['knpStuur_'])) { ?>
<tr>
	<td colspan = 5>Dit zijn de levensnummers binnen de gekozen<br> periode uit de reader die niet voorkomen<br> in het management programma. <br>Klik op de knop 'Verstuur' om deze <br>levensnummers klaar te zetten om <br>naar de reader te sturen ter controle.<br> </td>
</tr>
<?php } ?>
<tr height = 75 align = "center" style = "font-size : 14px;"  >
 <td></td>
 <td width = 80 align="center"><br> <b> Levensnummer </b><hr></td>
 <td width = 80 align="center"><b> Datum van <br> legen reader </b><hr></td>
 <td ><br> <b> Taak </b><hr></td>
 <?php
if(isset($_POST['knpZoek_']) || isset($_POST['knpStuur_'])) { ?>
 <td valign="top"> <input type="submit" name="knpStuur_" value="Verstuur"> <br><br></td>
 <td></td>
</tr>


	<?php

$toon_levensnummers = mysqli_query($db,$query) or die (mysqli_error($db));	
	while($mrl = mysqli_fetch_assoc($toon_levensnummers))
			{
				$transp = $mrl['transponder'];
				$levnr = $mrl['levensnummer'];
				$datum = $mrl['datum'];
				$taak = $mrl['taak'];

?>
<tr align = "center" style = "font-size : 14px;"  >
 <td></td>
 <td> <?php echo $levnr; ?> </td>
 <td> <?php echo $datum; ?> </td>
 <td> <?php echo $taak; ?> </td>
 <td><?php if(!isset($transp)) { echo 'Transponder is onbekend! '; } ?></td>
</tr>
<tr> <td colspan = 4 ><hr></td>
</tr>

<?php } // Einde while($mrl = mysqli_fetch_assoc($zoek_meerlingen_ooi)) 
	} // Einde isset($_POST['knpZoek_']) ?>
</table>		


</td></tr></table>
</form>

</TD>
<?php } else { ?> <img src='ooikaart_php.jpg'  width='970' height='550'/> <?php }
Include "menuAlerts.php"; } ?>
</tr>
</table>

</body>
</html>
