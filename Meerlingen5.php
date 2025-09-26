<?php 
$versie = '4-8-2019'; /* gemaakt */
$versie = '11-11-2019'; /* kolomkop worp gewijzigd in worpgrootte */
$versie = '28-12-2023'; /* and h.skip = 0 toegevoegd bij tblHistorie */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = 'top' align = center > gewijzigd naar <TD valign = 'top' align = 'center'> 31-12-24 Include "login.php"; voor Include "header.php" gezet */

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Rapport</title>
</head>
<body>

<?php
$titel = 'Meerling in periode';
$file = "Meerlingen5.php";
Include "login.php"; ?>

		<TD valign = 'top' align = 'center'>
<?php
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { if($modtech ==1) {
include "kalender.php";

$huidigjaar = date("Y"); $begin_datum = '1-01-'.$huidigjaar; $eind_datum = '1-03-'.$huidigjaar;

$var1dag = 60*60*24;
	$maak_datum = strtotime($eind_datum) - $var1dag; $eind_datum = date("d-m-Y", $maak_datum);
	/*if (isset($_GET['pstId'])) {$raak = $_GET['pstId']; }*/ 

if(isset($_POST['knpZoek'])) {
$worp_van = $_POST['txtWorp_van']; $van = date_format(date_create($worp_van), 'Y-m-d');
$worp_tot = $_POST['txtWorp_tot']; $tot = date_format(date_create($worp_tot), 'Y-m-d');

$query = "
SELECT right(lam.levensnummer,$Karwerk) lam, lam.geslacht, count(wrp.volwId) worp, h.datum date, date_format(h.datum,'%d-%m-%Y') datum, right(mdr.levensnummer,$Karwerk) ooi, round(((lstkg.kg - h.kg)*1000)/datediff(mx.mdm,h.datum),2) gemgroei, date_format(mx.mdm,'%d-%m-%Y') kgdag, st.stalId
FROM tblSchaap lam
 join tblVolwas v on (lam.volwId = v.volwId)
 join tblSchaap mdr on (mdr.schaapId = v.mdrId)
 join tblSchaap wrp on (lam.volwId = wrp.volwId)
 join tblStal st on (st.schaapId = lam.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
 left join (
 	SELECT stalId, max(datum) mdm
 	FROM tblHistorie
	WHERE kg is not null and actId > 1 and skip = 0
	GROUP BY stalId
 ) mx on (mx.stalId = st.stalId)
 left join (
 	SELECT stalId, datum, max(kg) kg
 	FROM tblHistorie
	WHERE kg is not null and actId > 1 and skip = 0
	GROUP BY stalId, datum
 ) lstkg on (lstkg.stalId = st.stalId and lstkg.datum = mx.mdm)
WHERE lam.levensnummer is not null and isnull(st.rel_best) and h.actId = 1 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.datum >= '".mysqli_real_escape_string($db,$van)."' and h.datum <= '".mysqli_real_escape_string($db,$tot)."' and h.skip = 0
GROUP BY lam.levensnummer, lam.geslacht, h.datum, mdr.levensnummer, mx.mdm, st.stalId
ORDER BY right(lam.levensnummer,$Karwerk)
";

$zoek_meerlingen = mysqli_query($db,$query) or die (mysqli_error($db));	

while($mrl = mysqli_fetch_assoc($zoek_meerlingen))
{				$pdf = $mrl['stalId']; // t.b.v. pdf
			}



} ?>

<form action= "Meerlingen5.php" method="post">
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


<tr>
<td align="right"><i>Worpen vanaf &nbsp</i></td>
 <td align="left"><i>&nbsp&nbsp&nbsp tot en met</i></td>
</tr>
<tr>
<td align="right"><input id = "datepicker1" type= text name = "txtWorp_van" size = "8" value = <?php if(isset($worp_van)) { echo "$worp_van"; } ?> ></td>
 <td align="left"><input id = "datepicker2" type= text name = "txtWorp_tot" size = "8" value = <?php if(isset($worp_tot)) { echo "$worp_tot"; } ?> >
  <input type="submit" name="knpZoek" value="Zoek">
 </td>
 <td width= 100 align = "right">
 	<a href= '<?php echo $url;?>Meerlingen5_pdf.php?Id=<?php echo $pdf; ?>&d1=<?php echo $van; ?>&d2=<?php echo $tot; ?>' style = 'color : blue'>
	print pagina </a>
 </td>
</tr>
<tr><td colspan = 10 ><hr></td></tr>
<tr><td></td></tr>
<!--	Einde Gegevens tbv MOEDERDIER		-->
<tr><td colspan = 50><table border = 0>




<tr align = "center" style = "font-size : 14px;"  >
 
 <td width = 80 align="center"><b> lammeren </b><hr></td> 
 <td width = 80 align="center"><b> geslacht </b><hr></td> 
 <td ><b> worpgrootte </b><hr></td>
 <td width = 100><b> werpdatum </b><hr></td>
 <td width = 180 align="left"><b>  gem. groei/dag </b><hr></td> 
 <td width = 100><b> ooi </b><hr></td>
 
 


<?php
if(isset($_POST['knpZoek'])) {

$zoek_meerlingen = mysqli_query($db,$query) or die (mysqli_error($db));	
	while($mrl = mysqli_fetch_assoc($zoek_meerlingen))
			{
				$lam = $mrl['lam'];
				$sek = $mrl['geslacht'];
				$worp = $mrl['worp'];
				$datum = $mrl['datum'];
				$gemkg = $mrl['gemgroei'];
				$maxdm = $mrl['kgdag'];
				$ooi = $mrl['ooi'];
				





	if(!isset($gemkg)) { $gemkg = 'Onbekend'; }
	else { $gemkg .= ' gr ( tot '. $maxdm .')'; }



?>
<tr align = "center" style = "font-size : 14px;"  >
 <td> <?php echo $lam; ?> </td>
 <td> <?php echo $sek; ?> </td>

 <td> <?php echo $worp; ?> </td>
 <td width = 120> <?php echo $datum; ?> </td>
 <td width = 180 align = "left"> <?php echo $gemkg?>
 </td>
 <td width = 100> <?php echo $ooi; ?> </td>
</tr>
<tr> <td colspan = 8 ><hr></td>
</tr>









	

<?php } // Einde while($mrl = mysqli_fetch_assoc($zoek_meerlingen_ooi)) 
	} // Einde isset($_POST['knpZoek'])	 ?>
</table>		

<!--	Einde Gegevens tbv LAM	-->	

</td></tr></table>
</form>

</TD>
<?php } else { ?> <img src='ooikaart_php.jpg'  width='970' height='550'/> <?php }
Include "menuRapport1.php"; } ?>
</tr>
</table>

</body>
</html>
