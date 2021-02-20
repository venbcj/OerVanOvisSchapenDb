<?php /* 3-3-2015 : Login toegevoegd */
$versie = '12-12-2015'; /* : Ubn niet te wijzigen */
$versie = '29-10-2016'; /* : Optie Administrator toegevoegd */
$versie = '9-1-2017'; /* : Link naar teamviewer toegevoegd */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '1-6-2020'; /* Reader keuze toegevoegd */
$versie = '12-02-2021'; /* Systeemgegevens gewijzigd naar Instellingen */
 session_start(); ?>
<html>
<head>
<title>Beheer</title>
</head>
<body>

<center>
<?php
$titel = 'Instellingen';
$subtitel = '';
Include "header.php";?>
<TD width = 960 height = 400 valign = "top" >
<?php
$file = "Systeem.php";
Include "login.php";
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { 

If (isset ($_POST['knpSave']))
{		
	$updKarwerk = $_POST['txtKarWerknr'];
	$updHisto = $_POST['kzlHis'];
	$updReader = $_POST['kzlReader'];
	$updlokatie = $_POST['txtLokatie'];
	$updrelnr = $_POST['txtRelnr'];
	$updurvo = $_POST['txtUrvo'];
	$updprvo = $_POST['txtPrvo'];

	
if($updKarwerk < 1 || $updKarwerk > 8) {
$fout = "Het aantal karakters van een werknr moet liggen tussen 1 en 8.";

} else {

if (empty($updrelnr)) 	{ $updrelnr = 'relnr = NULL';} else { $updrelnr = "relnr = "."'$updrelnr'" ; }
if (empty($updurvo)) 	{ $updurvo = 'urvo = NULL';} else { $updurvo = "urvo = "."'$updurvo'" ; }
if (empty($updprvo)) 	{ $updprvo = 'prvo = NULL';} else { $updprvo = "prvo = "."'$updprvo'" ; }
if (empty($updReader)) 	{ $updReader = 'reader = NULL';} else { $updReader = "reader = "."'$updReader'" ; }
if (empty($updlokatie)) { $updlokatie = 'root_reader = NULL';} else { $updlokatie = "root_reader = "."'$updlokatie'" ; }
	
$update_lid = "UPDATE tblLeden SET $updrelnr, $updurvo, $updprvo, $updReader, $updlokatie, kar_werknr = '$updKarwerk', histo = '$updHisto' 
					  WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." ;";
		mysqli_query($db,$update_lid) or die (mysqli_error($db));
}
}

$result = mysqli_query($db,"SELECT lidId, relnr, ubn, urvo, prvo, root_reader, kar_werknr FROM tblLeden WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." ;") or die (mysqli_error($db)); 

while ($row = mysqli_fetch_assoc($result))
		{ $relnr = $row['relnr'];
		  $ubn = $row['ubn'];
		  $urvo = $row['urvo'];
		  $prvo = $row['prvo'];
		  $lokatie = $row['root_reader'];
		  $karwerknr = $row['kar_werknr']; } ?>
<form action = "Systeem.php" method = "post" >

<table border = 0 width = 900>
	<tr><th colspan = 6 height="50"><hr></th></tr>
<tr>
 <td width = 150><u><i>Inloggegevens :</i></u></td>
 <td width = 150 align = 'right'>Gebruikersnaam :</td><td width = 100><?php echo $_SESSION["U1"]; ?></td>

 <td width = 100 align = "right">Wachtwoord :</td><td><?php echo "**************"; ?> </td>
 <td> <a href='<?php $url; ?>Wachtwoord.php' style = 'color : blue' > inloggegevens wijzigen </a> </td>

</tr>
<tr><td height = 15></td></tr>
<tr><td colspan = 8><hr></hr></td></tr>
</table>

<table border = 0 width = 900>
<tr>
 <td style = "font-size : 14;"  >Aantal cijfers t.b.v. werknr (max 8)</td>
 <td width = 600 ><input type = text name = "txtKarWerknr" size = 1 value = <?php echo $karwerknr; ?>></td><td ></td>
</tr>
<tr>
 <td style = "font-size : 14;"  >Historie schaap standaard tonen</td>
 <td width = 600 >

<?php $historie = mysqli_query($db,"SELECT histo FROM tblLeden WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." ") or die (mysqli_error($db));
	while ( $hi = mysqli_fetch_assoc($historie)) { $histo = $hi['histo'];} ?>
<!-- KZLja/nee --> 
<select <?php echo "name=\"kzlHis\" "; ?> style = "width:60; font-size:13px;">
<?php  
$opties = array(1 => 'Ja', 0 => 'Nee');
foreach ( $opties as $key => $waarde)
{
   if((!isset($_POST['knpSave']) && $histo == $key) || (isset($_POST["kzlHis"]) && $_POST["kzlHis"] == $key) ) {
	echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else {
	echo '<option value="' . $key . '">' . $waarde . '</option>';
  }
} ?> 
</select> <!-- EINDE KZLja/nee -->

 </td>
 <td></td>
</tr>
<tr><td height = 15></td></tr>
</table>

<table border = 0 width = 900>
<tr>
 <td style = "font-size : 14;"  >Reader</td>
 <td>
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
</tr>
<tr>
 <td width = 150 >Mijn reader lokatie : </td>
 <td> <input type = text name = 'txtLokatie' size = 100 value = <?php echo "'".$lokatie."'"; ?> > Wijzig \ in / !!</td>
</tr>
</table>

<table border = 0 width = 900>
<tr>
 <td></td>
</tr>
<tr><td colspan = 8><hr></hr></td></tr>
<tr><td colspan = 5><u><i>Bedrijfgegevens RVO :</i></u></td></tr>
<tr><td width = 210 align = 'right'>Ubn :</td><td width = 100><?php echo $ubn; ?></td>

 <td width = 160 align = "right" >Gebruikersnaam RVO :</td><td><input type = "text" name = "txtUrvo" size = 15 value = <?php echo $urvo; ?> ></td>

</tr>
<tr>
 <td width = 210 align = 'right'>Relatienummer RVO :</td><td width = 100><input type = text name = "txtRelnr" size = 15 value = <?php echo $relnr; ?>></td>

 <td width = 160 align = "right">Wachtwoord RVO :</td><td><input type = password name = "txtPrvo" size = 15 value = <?php echo $prvo; ?> ></td>

</tr>
<tr>
 <td height = 15></td>
</tr>
</table>
<table border = 0 width = 900>
<tr>
 <td colspan = 8><hr></hr></td></tr>
<tr height = 50 ></tr>
<tr>
 <td colspan = 4 align =left> Hulp op afstand ? Klik <a href='https://download.teamviewer.com/download/TeamViewerQS_nl.exe' target="_blank" style = 'color : blue' > hier </a>
 
 </td>
 <td colspan = 4 align =right><input type = "submit" name = "knpSave" value = "Opslaan"></td>
</tr>
</table>
</form>

</TD>
<?php
Include "menuBeheer.php"; } ?>
</tr>

</table>
</center>

</body>
</html>
