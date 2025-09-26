<?php 
$versie = '10-6-2017'; /* Gemaakt */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '15-3-2020'; /* veld reader toegevoegd */
$versie = '20-6-2020'; /* knop bewerken toegevoegd als reader = Agrident en bepaalde redenen en Lambar bestaan niet of redenen niet actief */
$versie = '12-2-2021'; /* Redenen afvoer toegevoegd. Controle lambar verwijderd */
$versie = '11-8-2023'; /* Veld ingescand toegevoed. Dit is de laatste dag dat een stallijst kan worden ingelezen bij een nieuwe klant. functie db_null_input() gebruikt. Sql beveiligd met quotes */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = "top"> 31-12-24 Include "login.php"; voor Include "header.php" gezet */
 session_start(); 
 ?>
<!DOCTYPE html>
<html>
<head>
<title>Beheer</title>
</head>
<body>

<?php
$titel = 'Gebruiker';
$file = "Systeem.php";
Include "login.php"; ?>

			<TD valign = "top">
<?php
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {

	if(isset($_GET['pstId']))	{ $_SESSION["ID"] = $_GET['pstId']; } $ID = $_SESSION["ID"];
?>

<script>
function verplicht() {
var rnaam 	= document.getElementById("voornaam"); 		var rnaam_v = rnaam.value;
var anaam 	= document.getElementById("achternaam");	var anaam_v = anaam.value;
var telf  	= document.getElementById("telefoon");		var telf_v  = telf.value;
var relatnr = document.getElementById("relatienummer");	var relatnr_v  = +relatnr.value;

	 if(rnaam_v.length == 0) rnaam.focus() 	+ alert("Roepnaam is onbekend.");
else if(rnaam_v.length > 25) rnaam.focus() 	+ alert("Roepnaam mag maximaal 25 karakters zijn.");
else if(anaam_v.length == 0) anaam.focus() 	+ alert("Achternaam is onbekend.");
else if(anaam_v.length > 25) anaam.focus() 	+ alert("Achternaam mag maximaal 25 karakters zijn.");
else if(telf_v.length > 11)  telf.focus()  	+ alert("Telefoonnummer mag max 11 karakters zijn.");
else if(isNaN(relatnr_v))  relatnr.focus()  + alert("Relatienummer is niet numeriek.");

}
</script>
<?php

//echo '$ID = '.$ID.'<br>'; // $ID is de gebruiker die op de pagina is opgeroepen
//echo '$lidId = '.$lidId.'<br>'; // $LidId is de gebruiker die is ingelogd
if (isset ($_POST['knpSave']))
{		
	
$zoek_ingescand = mysqli_query($db,"
SELECT ingescand
FROM tblLeden 
WHERE lidId = '".mysqli_real_escape_string($db,$ID)."' ;
") or die (mysqli_error($db)); 

while ($zi = mysqli_fetch_assoc($zoek_ingescand)) 	{ $scanday = $zi['ingescand']; }

	$txtRoep = $_POST['txtRoep'];
	$txtVoeg = $_POST['txtVoeg'];
	$txtNaam = $_POST['txtNaam'];
	$txtTel = $_POST['txtTel'];
	$txtMail = $_POST['txtMail'];
	$txtRelnr = $_POST['txtRelnr'];
	$txtUrvo = $_POST['txtUrvo'];
	$txtPrvo = $_POST['txtPrvo'];
	$kzlReader = $_POST['kzlReader'];
	$radMeld = $_POST['radMeld'];
	$radTech = $_POST['radTech'];
	$radFin = $_POST['radFin'];
	$kzlAdm = $_POST['kzlAdm'];
	$txtLstScan = $_POST['txtIngescand'];  $dag = date_create($txtLstScan); $lstScanDay =  date_format($dag, 'Y-m-d');

	

if (empty($txtLstScan)) { $lstScanDay =  $scanday; }
	
$update_lid = "UPDATE tblLeden SET 
	
	roep = '".mysqli_real_escape_string($db,$txtRoep)."',
	voegsel = ". db_null_input($txtVoeg) . ",
	naam = '".mysqli_real_escape_string($db,$txtNaam)."',
	relnr = ". db_null_input($txtRelnr) . ",
	urvo = ". db_null_input($txtUrvo) . ",
	prvo = ". db_null_input($txtPrvo) . ",
	mail = ". db_null_input($txtMail) . ",
	tel = ". db_null_input($txtTel) . ",
	meld = '".mysqli_real_escape_string($db,$radMeld)."',
	tech = '".mysqli_real_escape_string($db,$radTech)."',
	fin = '".mysqli_real_escape_string($db,$radFin)."',
	beheer = '".mysqli_real_escape_string($db,$kzlAdm)."',
	ingescand = '".mysqli_real_escape_string($db,$lstScanDay)."',
	reader = ". db_null_input($kzlReader) . "


	WHERE lidId = '".mysqli_real_escape_string($db,$ID)."'
	;";
/*echo $update_lid;*/		mysqli_query($db,$update_lid) or die (mysqli_error($db));

}

if (isset ($_POST['knpUpdate']))
{

$lidid = $ID;
include "Newreader_keuzelijsten.php";

}

$result = mysqli_query($db,"
SELECT l.lidId, l.roep, l.voegsel, l.naam, l.relnr, u.ubn, l.urvo, l.prvo, l.mail, l.tel, date_format(l.ingescand,'%d-%m-%Y') ingescand, l.meld, l.tech, l.fin, l.beheer, l.tel, l.reader, l.readerkey 
FROM tblLeden l
 join tblUbn u on (l.lidId = u.lidId)
WHERE l.lidId = '".mysqli_real_escape_string($db,$ID)."' ;
") or die (mysqli_error($db)); 

while ($row = mysqli_fetch_assoc($result))
		{ 
		  $roep = $row['roep'];
		  $tvoeg = $row['voegsel'];
		  $naam = $row['naam'];
		  $relnr = $row['relnr'];
		  $ubn = $row['ubn'];
		  $urvo = $row['urvo'];
		  $prvo = $row['prvo'];
		  $mail = $row['mail'];
		  $tel = $row['tel'];
		  $ingescand = $row['ingescand'];
		  $meld = $row['meld'];
		  $tech = $row['tech'];
		  $fin = $row['fin'];
		  $admin = $row['beheer'];
		  $reader = $row['reader'];
		  $readerkey = $row['readerkey'];
		   } ?>

<form action = "Gebruiker.php" method = "post" >

<table border = 0 width = 900>
<tr height = 20><td></td></tr>
<tr><th colspan = 3><hr>Gebruiker gegevens<hr></th></tr>
</table>


<table border = 0 width = 900>


<tr>
 <td colspan = 15><u><i>Gebruiker :</i></u></td>
</tr>
<tr>
 <td colspan = 15>Roepnaam : <input type = "text" name = "txtRoep" id="voornaam" size = 10 value = <?php if(isset($roep)) { echo " \"$roep\"";} ?> >
 	Tussenvoegsel : <input type = "text" name = "txtVoeg" id="tussen" size = 3 value = <?php if(isset($tvoeg)) { echo " \"$tvoeg\"";} ?> >
 	&nbsp&nbsp Achternaam : <input type = "text" name = "txtNaam" id="achternaam" size = 27 value = <?php if(isset($naam)) { echo " \"$naam\"";} ?> ></td>
</tr>
<tr>
 <td colspan = 15 >Telefoonnr : <input type = "text" name = "txtTel" id="telefoon" size = 10 value = <?php if(isset($tel)) { echo " \"$tel\"";} ?> >
 	 &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspE-mail : <input type = "text" name = "txtMail" size = 50 value =  <?php if(isset($mail)) { echo $mail;} ?> ></td>
</tr>
<tr><td height = 15></td>
</table>
<table border = 0 width = 900>
<tr>
 <td colspan = 15><u><i>Bedrijfgegevens RVO :</i></u></td>
</tr>
<tr>
 <td width = 150 align = 'right'>Ubn :</td>
 <td width = 100> <?php echo $ubn; ?> </td>
 <td width = 100 align = "right" >Gebruikersnaam RVO :</td>
 <td colspan = 2 ><input type = "text" name = "txtUrvo" size = 10 value = <?php echo $urvo; ?> ></td>
</tr>
<tr>
 <td width = 150>Relatienummer RVO :</td>
 <td><input type = text name = "txtRelnr" id="relatienummer" size = 10 value = <?php echo $relnr; ?>></td>
 <td width = 160 align = "right">Wachtwoord RVO :</td>
 <td colspan = 2 ><input type = password name = "txtPrvo" size = 10 value = <?php echo $prvo; ?> ></td>

</tr>
<tr><td height = 20></td>
</tr>
<tr>
 <td colspan=>Reader :  	

 	<!-- kzlReader --> 
<select <?php echo "name=\"kzlReader\" "; ?> style = "width:80; font-size:13px;">
<option></option>
<?php
$opties = array('Agrident' => 'Agrident', 'Biocontrol' => 'Biocontrol');
foreach ( $opties as $key => $waarde)
{
   if((!isset($_POST['knpSave']) && $reader == $key) || (isset($_POST["kzlReader"]) && $_POST["kzlReader"] == $key) ) {
	echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else {
	echo '<option value="' . $key . '">' . $waarde . '</option>';
  }
} ?> 
</select> <!-- EINDE kzlReader -->

 </td>
 <?php // knpUpdate hoeft alleen te worden getoond als er iets valt bij te werken bij reader Agrident 
if($reader == 'Agrident') {
$zoek_redenen_uitval =  mysqli_query($db,"
SELECT count(redId) aant
FROM tblRedenuser
WHERE redId in (8, 13, 22, 42, 43, 44) and uitval = 1 and lidId = '".mysqli_real_escape_string($db,$ID)."'
") or die (mysqli_error($db));
	while ( $zr = mysqli_fetch_assoc($zoek_redenen_uitval)) { $rd_db = $zr['aant']; }

$zoek_redenen_afvoer =  mysqli_query($db,"
SELECT count(redId) aant
FROM tblRedenuser
WHERE redId in (15, 45, 46, 47, 48, 49, 50, 51) and afvoer = 1 and lidId = '".mysqli_real_escape_string($db,$ID)."'
") or die (mysqli_error($db));
	while ( $zr = mysqli_fetch_assoc($zoek_redenen_afvoer)) { $rd_db += $zr['aant']; }
/*
$zoek_Lambar = mysqli_query($db,"
SELECT hokId
FROM tblHok
WHERE hoknr = 'Lambar' and lidId = '".mysqli_real_escape_string($db,$ID)."'
") or die (mysqli_error($db));
while ($h = mysqli_fetch_assoc($zoek_Lambar)) {	$Lambar = $h['hokId'];	}*/

 ?>
 <td>
 	<?php if($rd_db < 14 /*|| !isset($Lambar)*/) { ?>
 	<input type = "submit" name ="knpUpdate" value="Bijwerken">
 </td>
 <?php }
 } ?>
</tr>
<tr>
 <td colspan="4">Reader wachtwoord : <?php echo $readerkey; ?> </td>
</tr>
</table>

<table border = 0 width = 900>
<tr height = 15><td></td></tr>
<tr><th><hr> Module<hr></th><th colspan = 3 align="left"><hr>&nbsp&nbsp<hr></th></tr>
<tr>
 <td width = 105 >Melden : </td>
 <td><input type = radio name = 'radMeld' value = 1 
 	<?php 	if(!isset($_POST['radMeld']) && $meld == 1 ) 			{ echo "checked"; } 
 		else if(isset($_POST['radMeld']) && $_POST['radMeld'] == 1) { echo "checked"; } ?> 
 	 > Ja 
 	 <input type = radio name = 'radMeld' value = 0
 	<?php 	if(!isset($_POST['radMeld']) && $meld == 0 ) 			{ echo "checked"; }
 		else if(isset($_POST['radMeld']) && $_POST['radMeld'] == 0) { echo "checked"; } ?>
 	 > Nee 
 </td>
</tr>
<tr>
 <td width = 105 >Technisch : </td>
 <td><input type = radio name = 'radTech' value = 1
 	<?php 	if(!isset($_POST['radTech']) && $tech == 1 ) 			{ echo "checked"; }
 		else if(isset($_POST['radTech']) && $_POST['radTech'] == 1) { echo "checked"; } ?> 
 	 > Ja 
 	 <input type = radio name = 'radTech' value = 0
 	 <?php 	if(!isset($_POST['radTech']) && $tech == 0 ) 			{ echo "checked"; }
 	 	else if(isset($_POST['radTech']) && $_POST['radTech'] == 0) { echo "checked"; } ?>
 	 > Nee 
 </td>
</tr>
<tr>
 <td width = 105 >Financieel : </td>
 <td><input type = radio name = 'radFin' value = 1
  <?php 	if(!isset($_POST['radFin']) && $fin == 1 ) 			{ echo "checked"; }
  		else if(isset($_POST['radFin']) && $_POST['radFin'] == 1)	{ echo "checked"; } ?> 
 	 > Ja 
 	 <input type = radio name = 'radFin' value = 0 
 	 <?php 	if(!isset($_POST['radFin']) && $fin == 0 ) 			{ echo "checked"; }
 	 	else if(isset($_POST['radFin']) && $_POST['radFin'] == 0) { echo "checked"; } ?>
 	 > Nee 
 </td>
</tr>
<tr>
 <td height="15">
 </td>
</tr>
<tr>
 <td width = 105 >Administrator : </td>
 <td>
 	<!-- kzlBeheer ja/nee --> 
<select <?php echo "name=\"kzlAdm\" "; ?> style = "width:60; font-size:13px;">
<?php  
$opties = array(1 => 'Ja', 0 => 'Nee');
foreach ( $opties as $key => $waarde)
{
   if((!isset($_POST['knpSave']) && $admin == $key) || (isset($_POST["kzlAdm"]) && $_POST["kzlAdm"] == $key) ) {
	echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else {
	echo '<option value="' . $key . '">' . $waarde . '</option>';
  }
} ?> 
</select> <!-- EINDE kzlBeheer ja/nee -->
 </td>
</tr>
<tr> <td colspan="4"> <hr></td></tr>

</table>

<table border = 0 width = 900>

<tr>
 <td width = 105 >
Laatste dag stallijst inlezen
 </td>
 
 <td >
 	<input type = "text" name = "txtIngescand" size = 8 value = <?php echo $ingescand; ?> >
 </td>
 <td> t.b.v. nieuwe klanten
 </td>
 <td width = 500 ></td>
</tr>
<tr height = 50 ></tr>
<tr>
 <td colspan = 4 align =right><input type = "submit" name = "knpSave" onfocus = "verplicht()" value = "Opslaan"></td>
</tr>
</table>
</form>

</TD>
<?php
Include "menuBeheer.php"; } ?>
</tr>

</table>

</body>
</html>
