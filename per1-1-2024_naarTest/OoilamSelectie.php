<?php 
$versie = '20-12-2020'; /* gemaakt */
$versie = '31-12-2023'; /* and h.skip = 0 aangevuld aan tblHistorie */
 session_start(); ?>
<html>
<head>
<title>Rapport</title>
</head>
<body>

<center>
<?php
include "kalender.php";
$titel = 'Ooitjes uit meerlingen';
$subtitel = '';
Include "header.php"; ?>
<TD width = 960 height = 400 valign = 'top' align = center >
<?php
$file = "OoilamSelectie.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { if($modtech ==1) {


if(isset($_POST['knpStuur_'])) { include "save_ooiselectie.php"; }


if(isset($_POST['knpZoek_']) || isset($_POST['knpStuur_'])) {
$worpvan = $_POST['txtWorpVan_']; $van = date_format(date_create($worpvan), 'Y-m-d');
$worptot = $_POST['txtWorpTot_']; $tot = date_format(date_create($worptot), 'Y-m-d');

$query = "
SELECT count(s.schaapId) aantal, aant worp
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 join (
	SELECT s.volwId, count(s.schaapId) aant
	FROM tblSchaap s
	 join tblStal st on (s.schaapId = st.schaapId)
	WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
	GROUP BY s.volwId
 ) w on (s.volwId = w.volwId)
WHERE s.geslacht = 'ooi' and isnull(st.rel_best) and h.actId = 1 and h.skip = 0 and h.datum >= '".mysqli_real_escape_string($db,$van)."' and h.datum <= '".mysqli_real_escape_string($db,$tot)."'
GROUP BY aant
ORDER BY aant desc
";

$toon_meerlingen = mysqli_query($db,$query) or die (mysqli_error($db));	

} ?>

<form action= "OoilamSelectie.php" method="post">
<table border = 0> 

<tr>
<td align="right"><i>Worpen vanaf &nbsp</i></td>
 <td align="left"><i>&nbsp&nbsp&nbsp tot en met</i></td>
</tr>
<tr>
<td align="right"><input id = "datepicker1" type= text name = "txtWorpVan_" size = "8" value = <?php if(isset($worpvan)) { echo "$worpvan"; } ?> ></td>
 <td align="left"><input id = "datepicker2" type= text name = "txtWorpTot_" size = "8" value = <?php if(isset($worptot)) { echo "$worptot"; } ?> >
  <input type="submit" name="knpZoek_" value="Zoek">
 </td>
</tr>
<tr><td colspan = 10 ><hr></td></tr>

<!--	Einde Gegevens tbv MOEDERDIER		-->
<tr><td colspan = 50><table border = 0>
<?php if(isset($_POST['knpZoek_']) || isset($_POST['knpStuur_'])) { ?>
<tr>
	<td colspan = 6>Kies de gewenste opties in 1 keer en klik daarna op de knop 'Verstuur'.<br> De lijst met schapen kan nl. maar 1 keer worden samengesteld per keer dat <br> op de knop 'Verstuur' wordt geklikt.</td>
</tr>
<?php } ?>
<tr height = 75 align = center style = "font-size : 14px;"  >
 <td></td>
 <td width = 80 align="center"><b> Naar reader </b><hr></td>
 <td width = 80 align="center"><b> lammeren </b><hr></td>
 <td ><b> worpgrootte </b><hr></td>
 <?php
if(isset($_POST['knpZoek_']) || isset($_POST['knpStuur_'])) { ?>
 <td valign="top"> <input type="submit" name="knpStuur_" value="Verstuur"> <br><br><hr></td>
 <td></td>
</tr>


	<?php

$toon_meerlingen = mysqli_query($db,$query) or die (mysqli_error($db));	
	while($mrl = mysqli_fetch_assoc($toon_meerlingen))
			{
				$aantal = $mrl['aantal'];
				$worp = $mrl['worp'];

?>
<tr align = center style = "font-size : 14px;"  >
 <td></td>
 <td>
 	<?php if($worp <= 6) { ?>  <input type="checkbox" name= <?php echo "check_$worp"; ?> value = 1 > <?php } ?>
 </td>
 <td> <?php echo $aantal; ?> </td>
 <td> <?php echo 'Uit een worpgrootte '.$worp; ?> </td>
 <td></td>
</tr>
<tr> <td colspan = 8 ><hr></td>
</tr>

<?php } // Einde while($mrl = mysqli_fetch_assoc($zoek_meerlingen_ooi)) 
	} // Einde isset($_POST['knpZoek_'])	 ?>
</table>		

<!--	Einde Gegevens tbv LAM	-->	

</td></tr></table>
</form>

</TD>
<?php } else { ?> <img src='ooikaart_php.jpg'  width='970' height='550'/> <?php }
Include "menuAlerts.php"; } ?>
</tr>
</table>
</center>

</body>
</html>
