<?php /* 3-3-2015 : Login toegevoegd */
$versie = '12-12-2015'; /* : Ubn niet te wijzigen */
$versie = '29-10-2016'; /* : Optie Administrator toegevoegd */
$versie = '09-01-2017'; /* : Link naar teamviewer toegevoegd */
$versie = '23-01-2019'; /* aanmaken persoonlijke map toegevoegd */
$versie = '12-08-2023'; /* veld ingescand toegevoegd en functie db_null_input() gebruikt. Sql beveiligd met quotes */
$versie = '16-10-2023'; /* Aanmaken map /Readerversies toegevoegd */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = 'top'> 31-12-24 include login voor include header gezet */
$versie = '29-08-2025'; /* Ubn wordt vanaf nu opgeslagen in tblUbn i.p.v. tblLeden. Tevens de keuze Biocontrol in het veld Reader verwijderd */

 session_start(); 


function generatekey($length) {
	$options = 'abcdefghijklmnopqrstuvwxyz013456789';
	$code = '';
	for($i = 0; $i < $length; $i++) {
		$key = rand(0, strlen($options) - 1);
		$code .= $options[$key];
	}
	return $code;
}

function getApiKey($datb) {
		$apikey = generatekey(64);

		$result = mysqli_query($datb,"SELECT count(*) aant FROM tblLeden WHERE readerkey = '".mysqli_real_escape_string($datb,$apikey)."' ;") or die (mysqli_error($datb)); 

		while ($row = mysqli_fetch_assoc($result)) { $count = $row['aant']; }

		if ($count > 0) { $apikey = getApiKey($datb); }

	return $apikey;
}

function getAlias($datb,$username,$vlgnr) {
	if($vlgnr > 0) { $alias = $username.$vlgnr; } else { $alias = $username; }

	$result = mysqli_query($datb,"SELECT count(*) aant FROM tblLeden WHERE alias = '".mysqli_real_escape_string($datb,$alias)."' ;") or die (mysqli_error($datb)); 

	while ($row = mysqli_fetch_assoc($result))
		{ $count = $row['aant']; } 

		
	if($count > 0) {
		$vlgnr = ++$vlgnr;

			$alias = getAlias($datb,$username,$vlgnr);
		}
			
	return $alias;

}

 ?>
<!DOCTYPE html>
<html>
<head>
<title>Beheer</title>
</head>
<body>

<?php
$titel = 'Nieuwe gebruiker';
$file = "Systeem.php";
include "login.php"; ?>

			<TD valign = 'top'>
<?php
if (is_logged_in()) { ?>

<script>
function verplicht() {
var rnaam	 = document.getElementById("voornaam"); 		var rnaam_v = rnaam.value;
var anaam	 = document.getElementById("achternaam");		var anaam_v = anaam.value;
var telf	 = document.getElementById("telefoon");			var telf_v  = telf.value;
var ubn 	 = document.getElementById("ubn");				var ubn_v 	= +ubn.value; ubn_w = ubn.value;
var relatnr  = document.getElementById("relatienummer");	var relatnr_v  = +relatnr.value;

	 if(rnaam_v.length == 0) rnaam.focus() + alert("Roepnaam is onbekend.");
else if(rnaam_v.length > 25) rnaam.focus() + alert("Roepnaam mag maximaal 25 karakters zijn.");
else if(anaam_v.length == 0) anaam.focus() + alert("Achternaam is onbekend.");
else if(anaam_v.length > 25) anaam.focus() + alert("Achternaam mag maximaal 25 karakters zijn.");
else if(telf_v.length > 11)  telf.focus()  + alert("Telefoonnummer mag max 11 karakters zijn.");
else if(ubn_w.length == 0) 	 ubn.focus()   + alert("Ubn is onbekend.");
else if(isNaN(ubn_v))  		 ubn.focus()   + alert("Ubn is niet numeriek.");
else if(isNaN(relatnr_v))  	 relatnr.focus()  + alert("Relatienummer is niet numeriek.");

}
</script>

<?php

$ingescand = date_add_months($today,1); //zie basisfuncties.php

If (isset ($_POST['knpSave']))
{		
	$txtRoep = $_POST['txtRoep'];
	$txtVoeg = $_POST['txtVoeg'];
	$txtNaam = $_POST['txtNaam'];
	$txtTel = $_POST['txtTel'];
	$txtMail = $_POST['txtMail'];
	$txtUbn = $_POST['txtUbn'];
	$txtRelnr = $_POST['txtRelnr'];
	$txtUrvo = $_POST['txtUrvo'];
	$txtPrvo = $_POST['txtPrvo'];
	$kzlReader = $_POST['kzlReader'];
	$radMeld = $_POST['radMeld'];
	$radTech = $_POST['radTech'];
	$radFin = $_POST['radFin'];

	$ww = md5($txtUbn.'zfO3puW?Wod/UT<-|=)1VT]+{hgABEK(Yh^!Wv;5{ja{P~wX4t');

	$login = substr($txtNaam, 0, 4) . substr($txtRoep, 0, 1);
	$alias = getAlias($db,$login,0);

	$key = getApiKey($db);


$zoek_ubn = mysqli_query($db,"SELECT ubn FROM tblUbn WHERE ubn = '".mysqli_real_escape_string($db,$txtUbn)."' ;") or die (mysqli_error($db)); 

		while ($zu = mysqli_fetch_assoc($zoek_ubn)) { $gevonden_ubn = $zu['ubn']; }

if(isset($gevonden_ubn)) { $fout = "Dit ubn bestaat al."; }

else {

$insert_lid = "INSERT INTO tblLeden SET 
	alias = '".mysqli_real_escape_string($db,$alias)."',
	login = '".mysqli_real_escape_string($db,$txtUbn)."',
	passw = '".mysqli_real_escape_string($db,$ww)."',
	roep = '".mysqli_real_escape_string($db,$txtRoep)."',
	voegsel = ". db_null_input($txtVoeg) . ",
	naam = '".mysqli_real_escape_string($db,$txtNaam)."',
	relnr = ". db_null_input($txtRelnr) . ",
	urvo = ". db_null_input($txtUrvo) . ",
	prvo = ". db_null_input($txtPrvo) . ",
	mail = ". db_null_input($txtMail) . ",
	tel = ". db_null_input($txtTel) . ",
	kar_werknr = '5',
	actief = 1,
	ingescand = '".mysqli_real_escape_string($db,$ingescand)."',
	beheer = 0,
	histo = 1,
	meld = '".mysqli_real_escape_string($db,$radMeld)."',
	tech = '".mysqli_real_escape_string($db,$radTech)."',
	fin = '".mysqli_real_escape_string($db,$radFin)."',
	
	reader = ". db_null_input($kzlReader) . ",
	readerkey = '".mysqli_real_escape_string($db,$key)."'
	;";
		mysqli_query($db,$insert_lid) or die (mysqli_error($db));


$zoek_gebruiker = mysqli_query($db,"
	SELECT lidId FROM tblLeden WHERE alias = '".mysqli_real_escape_string($db,$alias)."' ;") or die (mysqli_error($db)); 

while ($zg = mysqli_fetch_assoc($zoek_gebruiker))
		{ $newId = $zg['lidId'];  }

$insert_tblUbn = "INSERT INTO tblUbn SET lidId = '".mysqli_real_escape_string($db,$newId)."', ubn = '".mysqli_real_escape_string($db,$txtUbn)."' ";
		mysqli_query($db,$insert_tblUbn) or die (mysqli_error($db));
	
include"Newuser_data.php";


	$lidid = $newId;
include "Newreader_keuzelijsten.php";


$map = 'user_'.$newId;
	mkdir("$map"); // Persoonlijk map voor user maken

$map = 'user_'.$newId.'/Readerbestanden';
	mkdir("$map"); // Persoonlijk map voor user maken t.b.v. readerbestanden die uit de reader komen als de reader wordt uitgelezen

$map = 'user_'.$newId.'/Readerversies';
	mkdir("$map"); // Persoonlijk map voor user maken t.b.v. readerversies



$persoonlijke_map = $dir.'/user_'.$lidId;








$goed = "De gebruiker is ingevoerd.";

} // Einde als $gevonden_ubn niet bestaat

} // Einde If (isset ($_POST['knpSave'])) ?>

<form action = "Newuser.php" method = "post" >

<table border = 0 width = 900>
<tr height = 20><td></td></tr>
<tr><th colspan = 3><hr>Gebruiker gegevens<hr></th></tr>
</table>


<table border = 0 width = 900>


<tr>
 <td colspan = 15><u><i>Gebruiker :</i></u></td>
</tr>
<tr>
 <td colspan = 15>Roepnaam* : <input type = "text" name = "txtRoep" id = "voornaam" size = 10  >
 	Tussenvoegsel : <input type = "text" name = "txtVoeg" size = 3 >
 	&nbsp&nbsp Achternaam* : <input type = "text" name = "txtNaam" id = "achternaam" size = 27 ></td>
</tr>
<tr>
 <td colspan = 15 >Telefoonnr &nbsp&nbsp: <input type = "text" name = "txtTel" id = "telefoon" size = 10 >
 	 &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspE-mail : <input type = "text" name = "txtMail" size = 50 ></td>
</tr>
<tr><td height = 15></td>
</table>
<table border = 0 width = 900>
<tr>
 <td colspan = 15><u><i>Bedrijfgegevens RVO :</i></u></td>
</tr>
<tr>
 <td width = 150 align = 'right'>Ubn* :</td>
 <td width = 100><input type = "text" name = "txtUbn" id = "ubn" size = 10 > </td>
 <td width = 100 align = "right" >Gebruikersnaam RVO :</td>
 <td colspan = 2 ><input type = "text" name = "txtUrvo" size = 10 > </td>
</tr>
<tr>
 <td width = 150>Relatienummer RVO :</td>
 <td><input type = text name = "txtRelnr" id = "relatienummer" size = 10 > </td>
 <td width = 160 align = "right">Wachtwoord RVO :</td>
 <td colspan = 2 ><input type = password name = "txtPrvo" size = 10 > </td>

</tr>
<tr><td height = 25></td></tr>
</table>

<table border = 0 width = 900>
<tr>
 <td width = 105> Reader
 </td>
 <td>
 	 	 	<!-- kzlReader --> 
<select <?php echo "name=\"kzlReader\" "; ?> style = "width:80; font-size:13px;">
<option></option>
<?php
$opties = array('Agrident' => 'Agrident');
foreach ( $opties as $key => $waarde)
{
   if((isset($_POST["kzlReader"]) && $_POST["kzlReader"] == $key) ) {
	echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else {
	echo '<option value="' . $key . '">' . $waarde . '</option>';
  }
} ?> 
</select> <!-- EINDE kzlReader -->
 </td>
</tr>
</table>

<table border = 0 width = 900>
<tr height = 20><td></td></tr>
<tr><th><hr>&nbspModule<hr></th><th colspan = 3 align="left"><hr>&nbsp&nbsp<hr></th></tr>
<tr>
 <td width = 105 >Melden : </td>
 <td><input type = radio name = 'radMeld' value = 1 <?php if(isset($_POST['radMeld']) && $_POST['radMeld'] == 1) { echo "checked"; } ?> 
 	 > Ja 
 	 <input type = radio name = 'radMeld' value = 0 <?php if(isset($_POST['radMeld']) && $_POST['radMeld'] == 0) { echo "checked"; } else if(!isset($_POST['knpSave']) ) { echo "checked"; } ?>
 	 > Nee 
 </td>
</tr>
<tr>
 <td width = 105 >Technisch : </td>
 <td><input type = radio name = 'radTech' value = 1 <?php if(isset($_POST['radTech']) && $_POST['radTech'] == 1) { echo "checked"; } ?> 
 	 > Ja 
 	 <input type = radio name = 'radTech' value = 0 <?php if(isset($_POST['radTech']) && $_POST['radTech'] == 0) { echo "checked"; } else if(!isset($_POST['knpSave']) ) { echo "checked"; } ?>
 	 > Nee 
 </td>
</tr>
<tr>
 <td width = 105 >Financieel : </td>
 <td><input type = radio name = 'radFin' value = 1 <?php if(isset($_POST['radFin']) && $_POST['radFin'] == 1) { echo "checked"; } ?> 
 	 > Ja 
 	 <input type = radio name = 'radFin' value = 0 <?php if(isset($_POST['radFin']) && $_POST['radFin'] == 0) { echo "checked"; } else if(!isset($_POST['knpSave']) ) { echo "checked"; } ?>
 	 > Nee 
 </td>
</tr>

</table>

<table border = 0 width = 900>
<tr><td colspan = 8><hr></hr></td></tr>
<tr height = 50 ></tr>
<tr>
 <td colspan = 4 align =right><input type = "submit" name = "knpSave" onfocus = "verplicht()" value = "Opslaan"></td>
</tr>
</table>
</form>

</TD>
<?php
include "menuBeheer.php"; } ?>
</tr>

</table>

</body>
</html>
