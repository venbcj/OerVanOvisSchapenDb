<?php
$versie = '14-8-2014'; /*Menu (rechts) veranderd van menuInkoop naar menuBeheer en html buiten php geprogrammeerd */
$versie = '11-11-2014'; /*header("Location: http://localhost:8080/schapendb/.....php");   toegevoegd. Dit ververst de pagina zodat een wijziging op het eerste record direct zichtbaar is */
$versie = '8-3-2015'; /*Login toegevoegd*/
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '9-8-2018'; /* veld naamreader toegevoegd */
session_start(); ?>
<html>
<head>
<title>Beheer</title>
</head>
<body>

<center>
<?php
if (isset ($_POST['knpSave_'])) { /*header("Location: ".$url."Relaties.php");*/ }
$titel = 'Relaties';
$subtitel = '';
Include "header.php"; ?>

		<TD width = 960 height = 400 valign = "top">
<?php
$file = "Relaties.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {

if (isset($_POST['knpdebSave_'])) { include "save_debiteuren.php"; }
if (isset($_POST['knpcreSave_'])) { include "save_crediteuren.php"; }

if (isset($_POST['knpInsert_']))
{

	$controle = mysqli_query($db,"
	SELECT count(p.partId) aantal
	FROM tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	WHERE p.lidId = ".mysqli_real_escape_string($db,$lidId)." and p.naam = '$_POST[insPartij_]'
	") or die (mysqli_error($db));
				while ($rij = mysqli_fetch_assoc($controle))
				{
					$dubbel = ("{$rij['aantal']}");
				}

	
	if (empty($_POST['insPartij_']))
	{
		$fout = "U heeft geen naam ingevoerd.";
	}
	else if (empty($_POST['kzlRelatie_']))
	{ 
		$fout = "Maak een keuze uit debiteur of crediteur."; 
			if (!empty($_POST['insUbn_'])) { $txtUbn = $_POST['insUbn_']; }
			if (!empty($_POST['insPartij_'])) { $txtPartij = $_POST['insPartij_']; }
			if (!empty($_POST['insStraat_'])) { $txtStraat = $_POST['insStraat_']; }
			if (!empty($_POST['insNr_'])) { $txtNr = $_POST['insNr_']; }
			if (!empty($_POST['insPc_'])) { $txtPc = $_POST['insPc_']; }
			if (!empty($_POST['insPlaats_'])) { $txtPlaats = $_POST['insPlaats_']; }
			if (!empty($_POST['insTel_'])) { $txtTel = $_POST['insTel_']; }
	}
	else if (!empty($dubbel) && $dubbel >= 1 )
	{  
		$fout = "Deze naam bestaat al.";
			if (!empty($_POST['insUbn_'])) { $txtUbn = $_POST['insUbn_']; }
			if (!empty($_POST['insStraat_'])) { $txtStraat = $_POST['insStraat_']; }
			if (!empty($_POST['insNr_'])) { $txtNr = $_POST['insNr_']; }
			if (!empty($_POST['insPc_'])) { $txtPc = $_POST['insPc_']; }
			if (!empty($_POST['insPlaats_'])) { $txtPlaats = $_POST['insPlaats_']; }
			if (!empty($_POST['insTel_'])) { $txtTel = $_POST['insTel_']; }
	}
	else 
	{
if (empty($_POST['insUbn_']))	{	$insUbn = "NULL";	}
  else	{	$insUbn = "'$_POST[insUbn_]'";	}

if (empty($_POST['insPartij_']))	{	$insPartij = "NULL";	}
  else		{	$insPartij = " '$_POST[insPartij_]' ";	$Partij = $_POST[insPartij_]; }

if (!isset($_POST['insPres_']) || empty($_POST['insPres_']))	{	$insPres = $Partij;	}
  else		{	$insPres = $_POST[insPres_];	}

if (empty($_POST['insStraat_']))	{	$insStraat = "NULL";	}
  else		{	$insStraat = " '$_POST[insStraat_]' ";	}

if (empty($_POST['insNr_']))	{	$insNr = "NULL";	}
  else		{	$insNr = " '$_POST[insNr_]' ";	}

if (empty($_POST['insPc_']))	{	$insPc = "NULL";	}
  else	{	$insPc = "' $_POST[insPc_]'";	}
  
if (empty($_POST['insPlaats_']))	{	$insPlaats = "NULL";	}
  else	{	$insPlaats = "'$_POST[insPlaats_]'";	}  

if (empty($_POST['insTel_']))	{	$insTel = "NULL";	}
  else	{	$insTel = "'$_POST[insTel_]'";	}


// Functie : Maak readernamen uniek
function getReadername($datb, $lidid, $naam, $n) {
		$n++;
		$len = strlen($n); $string_len = 20 - $len;
		$readername = substr($naam, 0, $string_len) . $n;

		$result = mysqli_query($datb,"
			SELECT count(*) aant 
			FROM tblPartij p
			WHERE lidId = ".mysqli_real_escape_string($datb,$lidid)." and naamreader = '".mysqli_real_escape_string($datb,$readername)."' ;") or die (mysqli_error($datb)); 

		while ($row = mysqli_fetch_assoc($result)) { $count = $row['aant']; }

		if ($count > 0) { $readername = getReadername($datb, $lidid, $naam, $n); }

	return $readername;
}
// Einde Functie : Maak readernamen uniek

$readernaam = substr($insPres, 0, 20);
$zoek_readernaam = mysqli_query($db,"
			SELECT count(*) aant 
			FROM tblPartij p 
			WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." and naamreader = '".mysqli_real_escape_string($db,$readernaam)."' ;") or die (mysqli_error($db)); 

		while ($dup = mysqli_fetch_assoc($zoek_readernaam)) { $count = $dup['aant']; }

		if ($count > 0) { $d = 0;
$readernaam = getReadername($db, $lidId, $insPres, $d);
}

		$insert_tblPartij = "INSERT INTO tblPartij SET lidId = ".mysqli_real_escape_string($db,$lidId).", ubn = ".$insUbn.", naam = ".$insPartij.", naamreader = '".$readernaam."', tel=  ".$insTel." ";
/*echo $insert_tblPartij.'<br>';*/		mysqli_query($db,$insert_tblPartij) or die (mysqli_error($db));
		
$zoek_partId = mysqli_query($db,"
SELECT partId 
FROM tblPartij 
WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." and naam = ".$insPartij."
") or die(mysqli_error($db));
	while( $par = mysqli_fetch_assoc($zoek_partId)) { $insPartId = $par['partId']; }
	
		$insert_tblRelatie = "INSERT INTO tblRelatie SET partId = ".mysqli_real_escape_string($db,$insPartId).", relatie = '$_POST[kzlRelatie_]' ";

/*echo $insert_tblRelatie.'<br>';*/		mysqli_query($db,$insert_tblRelatie) or die (mysqli_error($db));

if (!empty($_POST['insStraat_']) || !empty($_POST['insNr_']) || !empty($_POST['insPc_']) || !empty($_POST['insPlaats_'])) {
$zoek_relId = mysqli_query($db,"
SELECT relId 
FROM tblPartij p
 join tblRelatie r on (p.partId = r.partId)
WHERE p.lidId = ".mysqli_real_escape_string($db,$lidId)." and p.naam = ".$insPartij."
") or die(mysqli_error($db));
	while( $rel = mysqli_fetch_assoc($zoek_relId)) { $insRelId = $rel['relId']; }

		$insert_tblAdres = "INSERT INTO tblAdres SET relId = ".mysqli_real_escape_string($db,$insRelId).", straat = ".$insStraat.", nr = ".$insNr.", pc = ".$insPc.", plaats = ".$insPlaats." ";

	mysqli_query($db,$insert_tblAdres) or die (mysqli_error($db));	
}	
	}
} ?>

<form action= "Relaties.php" method= "post" >
<table border= 0  align = "left" >


			<!--------------------------------
			-----	DEBITEUREN  ------
			-------------------------------->
<tr>
 <td colspan = 11> <b>Debiteuren :</b> </td> 
 <td colspan = 3 ><input type = "submit" name= <?php echo "knpdebSave_"; ?> style = "font-size:12px;" value = "Opslaan debiteuren" ></td> 
</tr>
<tr style = "font-size:12px;" valign = "bottom">
 <th>ubn</th>
 <th>Bedrijfsnaam</th>
 <?php if($reader == 'Agrident') { ?>
 <th>Presentatie reader</th>
<?php } ?>
 <th>Vestigingsadres</th>
 <th>Huisnr</th>
 <th>Postcode</th>
 <th>Woonplaats</th>
 <th>Telefoon</th>
 <th>Actief</th>
</tr>
<?php
// START LOOP debiteuren
$debiteuren = mysqli_query($db,"
SELECT relId, naam
FROM tblPartij p
 join tblRelatie r on (r.partId = p.partId)
WHERE p.lidId = ".mysqli_real_escape_string($db,$lidId)." and r.relatie = 'deb'
ORDER BY r.actief desc, p.naam
") or die (mysqli_error($db));

	while($record = mysqli_fetch_assoc($debiteuren))
	{
            $Id = ("{$record['relId']}");  

$query = mysqli_query($db,"
SELECT p.partId, r.relId, r.relatie, p.ubn, p.naam, p.naamreader, a.straat, a.nr, a.pc, a.plaats, p.tel, r.actief, adrId
FROM tblPartij p
 join tblRelatie r on (r.partId = p.partId)
 left join tblAdres a on (r.relId = a.relId)
WHERE r.relId = '$Id'
ORDER BY naam
") or die (mysqli_error($db));

	while($row = mysqli_fetch_assoc($query))
	{
		$partId = $row['partId'];
		$ubn = $row['ubn'];
		$naam = $row['naam'];
		$pres = $row['naamreader'];
		$straat = $row['straat']; $nr = $row['nr'];
		$pc = $row['pc'];
		$plaats = $row['plaats'];
		$tel = $row['tel'];

if(isset($_POST['knpCred_'.$Id])) {
// crediteur toevoegen		
$insert_relatie = "INSERT INTO tblRelatie set partId = ".mysqli_real_escape_string($db,$partId).", relatie = 'cred' ";
	mysqli_query($db,$insert_relatie) or die (mysqli_error($db));
//echo '<br>'.$insert_relatie.'<br>';	
// Einde crediteur toevoegen
// Als adres bestaat ook adres toevoegen aan crediteur-relatie. LET OP emtpy NIET veranderen in isset want variabelen bestaan altijd !!
if(!empty($straat) || !empty($nr) || !empty($pc) || !empty($plaats)) { //toevoegen adres
$zoek_relId = mysqli_query($db,"SELECT relId FROM tblRelatie WHERE partId = ".mysqli_real_escape_string($db,$partId)." and relatie = 'cred'") or die (mysqli_error($db));
 while( $rel = mysqli_fetch_assoc($zoek_relId)) { $rel_cred = $rel['relId']; }

if(!empty($straat)) { $newStraat = "'".$straat."'"; } else { $newStraat = "NULL"; }
if(!empty($nr)) { $newNr = $nr; } else { $newNr = "NULL"; }
if(!empty($pc)) { $newPc = "'".$pc."'"; } else { $newPc = "NULL"; }
if(!empty($plaats)) { $newPlaats = "'".$plaats."'"; } else { $newPlaats = "NULL"; }
$insert_adres = "INSERT INTO tblAdres set relId = ".mysqli_real_escape_string($db,$rel_cred).", straat = ".$newStraat.", nr = ".$newNr.", pc = ".$newPc.", plaats = ".$newPlaats." ";
	mysqli_query($db,$insert_adres) or die (mysqli_error($db));
//echo $insert_adres.'<br>';
	}
// Einde Als adres bestaat ook adres toevoegen aan crediteur-relatie. 

}
		
?>		 
<tr style = "font-size:12px;">
 <td><!--Id --><input type= "hidden" name= <?php echo "txtId_$Id"; ?> size = 1 style= "font-size : 11px" value = <?php echo $Id; ?> > <!-- hiddden -->
 <input type= "text" name= <?php echo "txtdebUbn_$Id"; ?> size = 5 style= "font-size : 11px" value = <?php echo $ubn; ?> ></td>
 <td><input type= "text" name= <?php echo "txtdebNaam_$Id"; ?> style= "font-size : 11px" value = <?php echo " \"$naam\" "; ?> ></td>
 <?php if($reader == 'Agrident') { ?>
 <td><input type= "text" name= <?php echo "txtdebPres_$Id"; ?> size = 17 style= "font-size : 11px" value = <?php echo " \"$pres\" "; ?> ></td>
 <?php } ?>

 <td><input type= "text" name= <?php echo "txtdebStraat_$Id"; ?> style= "font-size : 11px" value = <?php echo " \"$straat\" "; ?> ></td>
 <td><input type= "text" name= <?php echo "txtdebNr_$Id"; ?> size = 1 style= "font-size : 11px; text-align : right;" value = <?php echo " \"$nr\" "; ?> style= "width: 30px; padding: 2px"></td>

 <td><input type= "text" name= <?php echo "txtdebPc_$Id"; ?> size = 5 style= "font-size : 11px;" value = <?php echo " \"$pc\" "; ?> ></td>

 <td><input type= "text" name= <?php echo "txtdebPlaats_$Id"; ?> size = 17 style= "font-size : 11px" value = <?php echo " \"$plaats\" "; ?> ></td>
 <td><input type= "text" name= <?php echo "txtdebTel_$Id"; ?> size = 12 style= "font-size : 11px" value = <?php echo " \"$tel\" "; ?> style= "width: 80px;"></td>
 <td><input type = "checkbox" name = <?php echo "chkdebActief_$Id"; ?> id= "c1" style= "font-size : 11px" value= "1" <?php echo $row['actief'] == 1 ? 'checked' : ''; ?> 		title = "Is debiteur te gebruiken ja/nee ?"> </td>
		
 <td width = 80> <a href='<?php echo $url; ?>Relatie.php?pstid=<?php echo $partId; ?>' style = "color : blue"> meer gegevens </a> </td>
 <td> 			 <a href='<?php echo $url; ?>Contact.php?pstid=<?php echo $partId; ?>' style = "color : blue"> contacten </a> </td>
<?php
$zoek_cred = mysqli_query($db,"SELECT relId FROM tblRelatie WHERE partId = ".mysqli_real_escape_string($db,$partId)." and relatie = 'cred' ") or die(mysqli_error($db)); 
	while( $cr = mysqli_fetch_assoc($zoek_cred)) { $cred_exists = $cr['relId']; } 
if(!isset($cred_exists)) { ?>
 <td> <input type= "submit" name= <?php echo "knpCred_$Id"; ?> value= "maak ook crediteur" style = "font-size:9px;" > </td> <?php } unset($cred_exists); ?>

	</td>
		
<?php	}	?>

 <td> </td>
</tr>
	

<?php    } ?>
</td>
</tr>
			<!-------------------------------------------------
			-----	EINDE DEBITEUREN EINDE ------
			------------------------------------------------->
			<!--------------------------------
			-----	CREDITEUREN  ------
			-------------------------------->
<tr height = 50 valign = 'bottom' >
 <td colspan = 11> <b>Crediteuren :</b> </td>
 <td colspan = 3><input type = "submit" name= <?php echo "knpcreSave_"; ?> style = "font-size:12px;" value = "Opslaan crediteuren" ></td> 
</tr>
<tr style = "font-size:12px;" valign = "bottom">
 <th>ubn</th>
 <th>Bedrijfsnaam</th>
 <?php if($reader == 'Agrident') { ?>
 <th>Presentatie reader</th>
<?php } ?>
 <th>Vestigingsadres</th>
 <th>Huisnr</th>
 <th>Postcode</th>
 <th>Woonplaats</th>
 <th>Telefoon</th>
 <th>Actief</th>
</tr>
<?php		
// START LOOP crediteuren
$crediteuren = mysqli_query($db,"
SELECT relId, naam
FROM tblPartij p
 join tblRelatie r on (r.partId = p.partId)
WHERE p.lidId = ".mysqli_real_escape_string($db,$lidId)." and r.relatie = 'cred'
ORDER BY r.actief desc, p.naam
") or die (mysqli_error($db));

	while($record = mysqli_fetch_assoc($crediteuren))
	{
            $Id = ("{$record['relId']}");  

$query = mysqli_query($db,"
SELECT p.partId, r.relId, relatie, ubn, naam, naamreader, straat, nr, pc, plaats, tel, r.actief, uitval
FROM tblPartij p
 join tblRelatie r on (r.partId = p.partId)
 left join tblAdres a on (r.relId = a.relId)
WHERE r.relId = '$Id'
ORDER BY naam
") or die (mysqli_error($db));

	while($row = mysqli_fetch_assoc($query))
	{
		$partId = $row['partId'];
		$ubn = $row['ubn'];
		$naam = $row['naam'];
		$pres = $row['naamreader'];
		$straat = $row['straat']; $nr = $row['nr'];
		$pc = $row['pc'];
		$plaats = $row['plaats'];
		$tel = $row['tel'];
		$uitval = $row['uitval'];
		$actief = $row['actief'];
		
if(isset($_POST['knpDeb_'.$Id])) {
// debiteur toevoegen		
$insert_relatie = "INSERT INTO tblRelatie set partId = ".mysqli_real_escape_string($db,$partId).", relatie = 'deb' ";
	mysqli_query($db,$insert_relatie) or die (mysqli_error($db));
//echo '<br>'.$insert_relatie.'<br>';	
// Einde debiteur toevoegen
// Als adres bestaat ook adres toevoegen aan debiteur-relatie. LET OP emtpy NIET veranderen in isset want variabelen bestaan altijd !!
if(!empty($straat) || !empty($nr) || !empty($pc) || !empty($plaats)) { //toevoegen adres
$zoek_relId = mysqli_query($db,"SELECT relId FROM tblRelatie WHERE partId = ".mysqli_real_escape_string($db,$partId)." and relatie = 'deb'") or die (mysqli_error($db));
 while( $rel = mysqli_fetch_assoc($zoek_relId)) { $rel_deb = $rel['relId']; }

if(!empty($straat)) { $newStraat = "'".$straat."'"; } else { $newStraat = "NULL"; }
if(!empty($nr)) { $newNr = $nr; } else { $newNr = "NULL"; }
if(!empty($pc)) { $newPc = "'".$pc."'"; } else { $newPc = "NULL"; }
if(!empty($plaats)) { $newPlaats = "'".$plaats."'"; } else { $newPlaats = "NULL"; }
$insert_adres = "INSERT INTO tblAdres set relId = ".mysqli_real_escape_string($db,$rel_deb).", straat = ".$newStraat.", nr = ".$newNr.", pc = ".$newPc.", plaats = ".$newPlaats." ";
	mysqli_query($db,$insert_adres) or die (mysqli_error($db));
//echo $insert_adres.'<br>';
	}
// Einde Als adres bestaat ook adres toevoegen aan debiteur-relatie. 

}
?>		 
<tr style = "font-size:12px;">
 <td><!--Id --><input type= "hidden" name= <?php echo "txtcreId_$Id"; ?> size = 1 style= "font-size : 11px" value = <?php echo $Id; ?> > <!-- hiddden -->
<?php if($uitval <> 1) { ?> 
 <input type= "text" name= <?php echo "txtcreUbn_$Id"; ?> size = 5 style= "font-size : 11px" value = <?php echo $ubn; ?> > <?php } ?> </td>
 <td>
<?php if($uitval == 1) { echo $naam; } else { ?>
 <input type= "text" name= <?php echo "txtcreNaam_$Id"; ?> style= "font-size : 11px" value = <?php echo " \"$naam\" "; ?> > <?php } ?> 
 </td>
<?php if($reader == 'Agrident') { ?>
 <td>
	<?php if($uitval <> 1) { ?>
 <input type= "text" name= <?php echo "txtcrePres_$Id"; ?> size = 17 style= "font-size : 11px" value = <?php echo " \"$pres\" "; ?> >
 	<?php } ?>
 </td>
<?php } ?>
 
 <td><input type= "text" name= <?php echo "txtcreStraat_$Id"; ?> style= "font-size : 11px" value = <?php echo " \"$straat\" "; ?> ></td>
 <td><input type= "text" name= <?php echo "txtcreNr_$Id"; ?> size = 1 style= "font-size : 11px; text-align : right;" value = <?php echo " \"$nr\" "; ?> style= "width: 30px; padding: 2px"></td>
 
 <td><input type= "text" name= <?php echo "txtcrePc_$Id"; ?> size = 5 style= "font-size : 11px" value = <?php echo " \"$pc\" "; ?> ></td>
 
 <td><input type= "text" name= <?php echo "txtcrePlaats_$Id"; ?> size = 17 style= "font-size : 11px" value = <?php echo " \"$plaats\" "; ?> ></td>
 <td><input type= "text" name= <?php echo "txtcreTel_$Id"; ?> size = 12 style= "font-size : 11px" value = <?php echo " \"$tel\" "; ?> style= "width: 80px;"></td>
 <td>
 <input type = "checkbox" name = <?php echo "chkcreActief_$Id"; ?> id= "c1" style= "font-size : 11px" value= "1" <?php echo $actief == 1 ? 'checked' : ''; if($uitval == 1) { ?> disabled <?php } ?> > </td>
		
 <td width = 80> <a href='<?php echo $url; ?>Relatie.php?pstid=<?php echo $partId; ?>' style = "color : blue"> meer gegevens </a> </td>
 <td> 			 <a href='<?php echo $url; ?>Contact.php?pstid=<?php echo $partId; ?>' style = "color : blue"> contacten </a> </td>
<?php
$zoek_deb = mysqli_query($db,"
SELECT relId 
FROM tblRelatie r
WHERE partId = ".mysqli_real_escape_string($db,$partId)." and relatie = 'deb' or (
	exists( SELECT relId FROM tblRelatie rl WHERE rl.partId = r.partId and rl.partId = ".mysqli_real_escape_string($db,$partId)." and rl.uitval = 1)
)
") or die(mysqli_error($db)); 
	while( $cr = mysqli_fetch_assoc($zoek_deb)) { $deb_exists = $cr['relId']; } 
if(!isset($deb_exists)) { ?>
 <td> <input type= "submit" name= <?php echo "knpDeb_$Id"; ?> value= "maak ook debiteur" style = "font-size:9px;" > </td> <?php } unset($deb_exists); ?>

	</td>
		
<?php	}	?>

 <td> </td>
</tr>
	

<?php    } ?>
</td>
</tr>
			<!-------------------------------------------------
			-----	EINDE CREDITEUREN EINDE ------
			------------------------------------------------->
<tr><td colspan = 15><hr></td></tr>
<tr><td colspan = 2 style = "font-size:13px;"><i> Nieuwe relatie : </i></td></tr>
<tr><td><input type= "text" name= "insUbn_" size = 5 style= "font-size : 12px" <?php if (isset($txtUbn)) { ?> value = <?php echo "'".$txtUbn."'"; } ?> ></td>
<td><input type= "text" name= "insPartij_" style= "font-size : 11px" <?php if (isset($txtPartij)) { ?> value = <?php echo "'".$txtPartij."'"; } ?> ></td>

  <?php if($reader == 'Agrident') { ?>
 <td><input type= "text" name= "txtcredPres_" size = 17 style= "font-size : 11px" ></td>
 <?php } ?>

<td><input type= "text" name= "insStraat_" style= "font-size : 11px" <?php if (isset($txtStraat)) { ?> value = <?php echo "'".$txtStraat."'"; } ?> ></td>
<td><input type= "text" name= "insNr_" size = 1 style= "font-size : 11px; text-align : right;" <?php if (isset($txtNr)) { ?> value = <?php echo "'".$txtNr."'"; } ?>  style= "width: 30px; padding: 2px"></td>

<td><input type= "text" name= "insPc_" size = 5 style= "font-size : 11px" <?php if (isset($txtPc)) { ?> value = <?php echo "'".$txtPc."'"; } ?>  ></td>

<td><input type= "text" name= "insPlaats_" size = 17 style= "font-size : 11px" <?php if (isset($txtPlaats)) { ?> value = <?php echo "'".$txtPlaats."'"; } ?> ></td>
<td><input type= "text" name= "insTel_" size = 12 style= "font-size : 11px" <?php if (isset($txtTel)) { ?> value = <?php echo "'".$txtTel."'"; } ?>  style= "width: 80px;"></td>
<td><input type= "checkbox" name= "boxActief_" id= "c2" <?php if(true){ echo "checked"; } ?>  disabled ></td>
<td>
 <select name= "kzlRelatie_" style= "width:80; font-size : 11px;" > 
<?php
$opties = array('' => '', 'deb' => 'debiteur', 'cred' => 'crediteur');
foreach ( $opties as $key => $waarde)
{
   $keuze = '';
   if(isset($_POST['kzlRelatie_']) && $_POST['kzlRelatie_'] == $key)
   {
        $keuze = ' selected ';
   }
   echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
} ?>
</select> </td>
<td colspan = 2><input type = "submit" name= "knpInsert_" value = "Toevoegen" style = "font-size:10px;"></td></tr>
</table>
</form>
	

</TD>
<?php
Include "menuBeheer.php"; } ?>
</body>
</html>
