<?php 
$versie = '11-11-2014'; /*header("Location: http://localhost:8080/schapendb/Hok.php");   toegevoegd. Dit ververst de pagina zodat een wijziging op het eerste record direct zichtbaar is*/
$versie = '8-3-2015'; /*Login toegevoegd */
$versie = '18-11-2015'; /* hok verandert in verblijf*/
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '30-5-2020'; /* Scannummer t.b.v. reader Agrident aangepast. Dubbele loop Hokken en  hidden velden scan en actief verwijderd */
$versie = '02-08-2020'; /* veld sort toegevoegd */
session_start(); ?>
<html>
<head>
<title>Beheer</title>
</head>
<body>

<center>
<?php
$titel = 'Verblijven';
$subtitel = ''; 
Include "header.php";
?>

		<TD width = '960' height = '400' valign = 'top'>
		 
<?php
$file = "Hok.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { if($modtech ==1) {

include "vw_HoknBeschikbaar.php"; // toegepast in save_hok.php

if (isset ($_POST['knpSave_'])) { include "save_hok.php"; }

if (isset ($_POST['knpInsert_']))
{
	$hok = $_POST['insHok_'];
// Zoek naar hok op duplicaten
$controle = mysqli_query($db,"
SELECT count(hoknr) aantal
FROM tblHok
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and hoknr = '".mysqli_real_escape_string($db,$hok)."'
") or die (mysqli_error($db));
				while ($row = mysqli_fetch_assoc($controle))
				{
					$dubbel = $row['aantal'];
				} // Einde Zoek naar hok op duplicaten
// Zoek naar scannr op duplicaten	
$zoek_scannr = mysqli_query($db,"
SELECT count(scan) scan
FROM tblHok
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and " . db_null_filter('scan',$_POST['insScan_']) . " and scan is not null 
") or die(mysqli_error($db));
	while ( $scannr = mysqli_fetch_assoc($zoek_scannr)) { $aantsc = $scannr['scan']; } // Einde Zoek naar scannr op duplicaten
	
	if (empty($_POST['insHok_'])) 				{ $fout = "U heeft geen verblijf ingevoerd."; }	
	else if (isset($dubbel) && $dubbel > 0)	 	{ $fout = "Deze omschrijving bestaat al.";	$hok = '';	}	
	else if(!empty($hok) && strlen("$hok")> 10)	{ $fout = "Het verblijf mag uit max. 10 karakters bestaan."; }	
	else if ($aantsc > 0) 						{ $fout = "De scancode bestaat al."; }	
	else 
	{
$query_hok_toevoegen= "
  INSERT INTO tblHok 
  SET lidId = '".mysqli_real_escape_string($db,$lidId)."', 
	  hoknr = '".mysqli_real_escape_string($db,$hok)."', 
	  scan = " . db_null_input($_POST['insScan_']) . ", 
	  sort = ". db_null_input($_POST['insSort_']);
		
				/*echo $query_hok_toevoegen; */ mysqli_query($db,$query_hok_toevoegen) or die (mysqli_error($db));
	}
}

$zoek_hok = mysqli_query($db,"
SELECT hokId
FROM tblHok
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
ORDER BY sort, hokId
") or die (mysqli_error($db));

	while($line = mysqli_fetch_assoc($zoek_hok))
	{
            $pdf = $line['hokId']; 
    } ?>

<form action="Hok.php" method="post">
<table border = 0>
<tr>
 <td width = 450 valign = 'top'>
<table border = 0>
<tr>
 <td>
	<?php if($reader == 'Agrident') { $kop = 'sortering reader'; } else { $kop = 'code tbv reader'; }  ?>
<b> Nieuw verblijf : </b> <td align = center width = 10 style ="font-size:12px;"> <b> <?php echo $kop; ?> </b>
 </td>
</tr>
<tr>
 <td> <input type= "text" name= "insHok_" value = <?php if(isset($hok)) { echo $hok; }; ?> > </td>
 <td> <?php if($reader == 'Agrident') { ?>
	<input type= "text" name= "insSort_" size = 1 title = "Leg hier het nummer vast om de volgorde in de reader te bepalen." > 
<?php } else { ?>
	<input type= "text" name= "insScan_" size = 1 title = "Leg hier de code vast die u tijdens het scannen met de reader gaat gebruiken." > 
<?php } ?>
 </td>
 <td> <input type = "submit" name= "knpInsert_" value = "Toevoegen" > </td>
</tr>
</table>

 </td>
 <td>		
<table border = 0 align = 'left' >
<tr>
 <td> <b> Verblijven</b> </td>
 <td align = center style ="font-size:12px;"> <?php echo $kop; ?> </td>
 <td align = center style ="font-size:12px;"> in gebruik </td>
 <td> <input type = "submit" name= "knpSave_" value = "Opslaan" style = "font-size:12px;"> </td>
 <td width= 200 align="right">
 	<a href= '<?php echo $url;?>Hok_pdf.php?Id=<?php echo $pdf; ?>' style = 'color : blue'>
	print pagina </a> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
 </td>	
</tr>
<tr>
 <td colspan = 5><hr> </td>
</tr>


<?php	
// START LOOP	
$query = mysqli_query($db,"
SELECT hokId, hoknr, scan, sort, actief
FROM tblHok
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
ORDER BY coalesce(sort,hokId + 500), hoknr
") or die (mysqli_error($db));

	while($row = mysqli_fetch_assoc($query))
	{
	  $Id = $row['hokId'];
	  $hoknr = $row['hoknr'];
	  $scan = $row['scan']; 
	  $sort = $row['sort']; 
	  $actief = $row['actief']
	  ?>


<tr>
 <td> <?php echo $hoknr ?> </td>			
 <td width = 100 align = "center">
<?php if ($reader == 'Agrident') { ?>
	<input type = text name = <?php echo "txtSort_$Id"; ?> size = 1 value = <?php echo $sort; ?>  >
<?php } else { ?>
	<input type = text name = <?php echo "txtScan_$Id"; ?> size = 1 value = <?php echo $scan; ?>  > <?php } ?>
 </td>
 <td> 
	<input type = hidden name = <?php echo "chkActief_$Id"; ?> value= 0 > <!-- hiddden -->
	<input type = "checkbox" name = <?php echo "chkActief_$Id"; ?> id="c1" value= 1 <?php echo $actief == 1 ? 'checked' : ''; ?> 		title = "Is verblijf te gebruiken ja/nee ?">
 </td>
</tr>
<?php	} ?>
 </td>
</tr>
</table>
</td></tr></table>

</form>



	</TD>
<?php } else { ?> <img src='hok_php.jpg'  width='970' height='550'/> <?php }
Include "menuBeheer.php"; } ?>
</body>
</html>
