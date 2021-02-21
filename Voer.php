<!-- 8-3-2015 : Login toegevoegd
14-11-2015 naamwijziging van Voer naar Voerbestand -->
<?php $versie = '19-12-2015'; /* : Rubriek toegevoegd */
$versie = '1-8-2017'; /* save_artikel.php toegevoegd */ 
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '7-4-2019'; /* Btw gewijzigd van 6 naar 9% */
 session_start(); ?>
<html>
<head>
<title>Inkoop</title>
</head>
<body>

<center>
<?php
If (isset ($_POST['knpSave_']) || isset($_POST['knpActive_']) ) {
	Include "url.php";
	header("Location: ".$url."Voer.php");
}

$titel = 'Voer';
$subtitel = '';
Include "header.php"; ?>

		<TD width = 960 height = 400 valign = "top">
<?php
$file = "Voer.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { if($modtech ==1) {

if (isset($_POST['knpSave_'])) { include "save_artikel.php"; }

//*******************
// NIEUWE INVOER POSTEN
//*******************
if (isset ($_POST['knpInsert_']))
{	

$controle = mysqli_query($db,"
SELECT count(naam) aantal
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (eu.enhuId = a.enhuId)
WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and a.naam = '".$_POST['insNaam_0']."' and a.soort = 'voer'
GROUP BY a.naam
") or die (mysqli_error($db));
		while ($rij = mysqli_fetch_assoc($controle))
		{
			$dubbel = ($rij['aantal']);
		}

	if (empty($_POST["insNaam_0"]))
	{ ?>
		<center style = "color : red;"> U heeft geen omschrijving ingevoerd.
<?php }
	else if (empty($_POST["insNhd_0"]))
	{ ?>
		<center style = "color : red;"> Eenheid is niet ingevoerd.
<?php }
	else if (!empty($dubbel) && $dubbel >= 1 )
	{
		echo "Dit voer bestaat al.";
	}
	else 
	{
if (empty($_POST["insNaam_0"]))	{	$insNaam = "NULL";	}
  else		{	$insNaam = "'$_POST[insNaam_0]'";	}

if (empty($_POST['insStdat_0']))	{	$insStdat = "stdat = NULL";	}
  else		{	$insStdat = "stdat = '$_POST[insStdat_0]' ";	}

if (empty($_POST['insNhd_0']))	{	$insNhd = "NULL";	}
  else		{	$insNhd = "'$_POST[insNhd_0]'";	}

if (empty($_POST['insBtw_0']))	{	$insBtw = "NULL";	}
  else	{	$insBtw = "'$_POST[insBtw_0]'";	}
  
if (empty($_POST['insRelatie_0']))	{	$insRelatie = "NULL";	}
  else	{	$insRelatie = "'$_POST[insRelatie_0]'";	}

if($modfin == 1 ) {
if (empty($_POST['insRubriek_0']))	{	$insRubriek = "NULL";	}
  else	{	$insRubriek = "'$_POST[insRubriek_0]'";	}
}
else
{ $insRubriek = "NULL"; }

  
$insert_tblArtikel = "INSERT INTO tblArtikel SET soort = 'voer', naam = ".$insNaam.", enhuId = ".$insNhd.", ".$insStdat.", btw = ".$insBtw.", relId=  ".$insRelatie.", rubuId=  ".$insRubriek." ";
		
/*echo $insert_tblArtikel.'<br>';*/				mysqli_query($db,$insert_tblArtikel) or die (mysqli_error($db));
	}
}

//*****************************
//** ARTIKELEN IN GEBRUIK
//*****************************
?>
<form action="Voer.php" method="post" >
<table border= 0 align =  "left" > 
<tr> 
 <td colspan = 10 > <b>Voer in gebruik :</b> </td>
 <td colspan = 2 align=right><input type = "submit" name="knpSave_" value = "Opslaan"  style = "font-size:12px;"></td>
</tr> 


 <tr style =  "font-size:12px;" align = center valign =  "bottom"> 
		 <th width = 200 >Omschrijving *</th>
		 <th></th> 
		 <th>stand.<br>aantal</th> 
		 <th>Eenheid&nbsp*</th>
		 <th></th> 
		 <th>Btw</th> 
		 <th>Leverancier</th> 
<?php if($modfin == 1 ) { ?>
		 <th>Rubriek **</th>  <?php } ?>

		 <th>Actief</th> 
 </tr> 
<?php		
// START LOOP
$loop = mysqli_query($db,"
SELECT a.artId
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (a.enhuId = eu.enhuId)
WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and a.soort = 'voer' and a.actief = 1
ORDER BY a.actief desc, a.naam
") or die (mysqli_error($db));

	while($lus = mysqli_fetch_assoc($loop))
	{
            $Id = $lus['artId'];  


$qryArtikel = mysqli_query($db,"
SELECT a.soort, a.naam, a.stdat, a.enhuId, e.eenheid, a.btw, a.relId, a.rubuId, a.actief
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (a.enhuId = eu.enhuId)
WHERE a.artId = ".mysqli_real_escape_string($db,$Id)."
") or die (mysqli_error($db));

	while($row = mysqli_fetch_assoc($qryArtikel))
	{
		$soort = "{$row['soort']}";
		$voer = "{$row['naam']}";
		$stdat = "{$row['stdat']}";
		$enhuId = "{$row['enhuId']}";
		$eenhd = "{$row['eenheid']}";
		$btw = "{$row['btw']}";
		$rubuId = "{$row['rubuId']}";
		$relId = "{$row['relId']}";
		$actief = "{$row['actief']}";

// Bepalen of artikel al is ingekocht
$voer_ingekocht = mysqli_query($db,"
SELECT count(artId) aant
FROM tblInkoop
WHERE artId = ".mysqli_real_escape_string($db,$Id)." 
") or die (mysqli_error($db));
 $ing = mysqli_fetch_assoc($voer_ingekocht);  $rows_inkoop = $ing['aant'];
// EINDE Bepalen of artikel al is ingekocht

?>
<tr style = "font-size:12px;">
 <td style = "font-size : 14px;">
<?php
// Veld Omschrijving (al dan niet te wijzigen) 
If ($rows_inkoop > 0) { echo $voer; }
else 	{ ?>

	<input type= "text" name= <?php echo "txtNaam_$Id"; ?> size = 30 value = <?php echo "'".$voer."'"; ?> style = "font-size:13px"; > 
<?php	} 
// EINDE  Veld Omschrijving (al dan niet te wijzigen) 
?></td>
<td width = 1></td>
<td> 

<input type= "text" name= <?php echo "txtStdat_$Id"; ?> size = 4 style = "font-size:12px; text-align : right;" title = "Standaard verbruikshoeveelheid" value = <?php echo $stdat; ?> >

<!-- kzlVerbruikseenheid (al dan niet te wijzigen) -->
</td><td><?php
If ($rows_inkoop > 0) { echo "$eenhd"; }
else  {

$result = mysqli_query($db,"
SELECT e.eenheid, eu.enhuId
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and eu.actief = 1
ORDER BY e.eenheid
") or die (mysqli_error($db));?>
 <select style="width:50;" name= <?php echo "kzlNhd_$Id"; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php		while($lijn = mysqli_fetch_array($result))
		{
			$raak = $lijn['enhuId'];

			$opties= array($lijn['enhuId']=>$lijn['eenheid']);
			foreach ( $opties as $key => $waarde)
			{

  if ((!isset($_POST['knpSave_']) && $enhuId == $raak) || (isset($_POST["kzlNhd_$Id"]) && $_POST["kzlNhd_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }	
			}
			
		}
?></select>

<?php }	?>
</td>
<?php

// EINDE kzlVerbruikseenheid (al dan niet te wijzigen)

?>		<td width = 1 ></td><td>
<?php
// kzlBtw bij wijzigen		
$opties = array('' => '', '9' => '9%', '21' => '21%'); ?>

 <select name= <?php echo "kzlBtw_$Id"; ?> style= "width:50;" style = "font-size:12px;">
<?php
foreach ( $opties as $key => $waarde)
{
   if((!isset($_POST['knpSave_']) && $btw == $key) || (isset($_POST["kzlBtw_$Id"]) && $_POST["kzlBtw_$Id"] == $key) ) {
   echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
 } else {
   echo '<option value="' . $key . '">' . $waarde . '</option>';
   }

} ?>
	</select> </td>
<!-- EINDE kzlBtw bij wijzigen		
    kzlLeverancier bij wijzigen -->
 <td> <?php
$qryLevcier = mysqli_query($db,"
SELECT r.relId, p.naam
FROM tblPartij p
 join tblRelatie r on (p.partId = r.partId)
WHERE p.lidId = ".mysqli_real_escape_string($db,$lidId)." and relatie = 'cred' and p.actief = 1 and r.actief = 1
ORDER BY p.naam
") or die (mysqli_error($db)); ?>
 <select style= "width:110;" name= <?php echo "kzlRelatie_$Id"; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php		while($lijn = mysqli_fetch_array($qryLevcier))
		{
			$raak = $lijn['relId'];

			$opties= array($lijn['relId']=>$lijn['naam']);
			foreach ( $opties as $key => $waarde)
			{
  if ((!isset($_POST['knpSave_']) && $relId == $raak) || (isset($_POST["kzlRelatie_$Id"]) && $_POST["kzlRelatie_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }	
			}
			
		}
?>
</select></td>
<?php
// EINDE kzlLeverancier bij wijzigen		
if($modfin == 1 ) { ?>

<td>
<!-- KZLRUBRIEK bij wijzigen-->
<?php

$qryRubriek = mysqli_query($db,"
SELECT ru.rubuId, r.rubriek
FROM tblRubriekuser ru 
 join tblRubriek r on (ru.rubId = r.rubId)
 join tblRubriekhfd hr on (r.rubhId = hr.rubhId)
WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and r.rubhId = 6 and r.actief = 1 and hr.actief = 1
ORDER BY r.rubriek
 ") or die (mysqli_error($db));?>
 <select style="width:180;" name= <?php echo "kzlRubriek_$Id"; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php		while($rub = mysqli_fetch_array($qryRubriek))
		{
			$raak = $rub['rubuId'];


			$opties = array($rub['rubuId']=>$rub['rubriek']);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if( (!isset($_POST['knpSave_']) && $rubuId == $raak) || (isset($_POST["kzlRubriek_$Id"]) && $_POST["kzlRubriek_$Id"] == $key) )
		{
			echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
		}
		else
		{		
		echo '<option value="' . $key . '" >' . $waarde . '</option>';
		}
			
		}
}
?>
</select>
<!-- EINDE KZLRUBRIEK bij wijzigen -->
 </td> <?php } ?>
 <td> <input type = "checkbox" name = <?php echo "chkActief_$Id"; ?> id="c1" value="1" <?php echo $actief == 1 ? 'checked' : ''; ?> title = "Is voer te gebruiken ja/nee ?"> </td>
	
 <td></td>
 <td></td> 

<?php } ?>

<td></td></tr>

<?php } ?>
</td></tr>
<tr><td colspan = 6 style= "font-size : 13px";><sub>* Eenmaal ingekocht niet meer wijzigbaar !
 <?php if($modfin == 1 ) { ?> <br>** t.b.v. automatisch aanmaken factuur na inkopen ! <?php } ?>
 </sub></td></tr>

<tr><td colspan = 15><hr></td></tr>

<!--
*************************************
** EINDE ARTIKELEN IN GEBRUIK
*************************************
	
*********************************
 VELDEN TBV NIEUWE INVOER
********************************* -->


<tr><td style = "font-size:13px;"><i> Nieuw voer : </i></td></tr>
<tr><td><input type="text" name= "insNaam_0" size = 30 value = '' maxlength = 50></td>
<td></td>
<td><input type= "text" name= "insStdat_0" value = 1 size = 1 style = "text-align : right; font-size:13px;" title = "Standaard verbruikshoeveelheid"></td>
<td>
<?php
// kzlverbruikseenheid bij nieuwe invoer
$newvrb = mysqli_query($db,"
SELECT e.eenheid, eu.enhuId
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId) 
WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and eu.actief = 1
ORDER BY e.eenheid
") or die (mysqli_error($db)); ?>
 <select style= "width:50;" name= "insNhd_0" >
 <option></option> <?php
 		while($lijn = mysqli_fetch_array($newvrb))
		{
		
			$opties= array($lijn['enhuId']=>$lijn['eenheid']);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if(isset($_POST['insNhd_0']) && $_POST['insNhd_0'] == $key)
		{
			$keuze = ' selected ';
		}
				
		echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
			}
		
		} ?>
 </select>

</td>
<td width = 1 ></td>
<td>

<?php
// kzl btw bij nieuwe invoer
$opties = array('' => '', '9' => '9%', '21' => '21%'); ?>

 <select name= "insBtw_0" style= "width:50;">
<?php
foreach ( $opties as $key => $waarde)
{
   $keuze = '';
   if(isset($_POST['insBtw_0']) && $_POST['insBtw_0'] == $key)
   {
        $keuze = ' selected ';
   }
   echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
} ?>
</select>

</td>
<td>
<?php
// kzlLeverancier bij nieuwe invoer
$newcrediteur = mysqli_query($db,"
SELECT r.relId, p.naam
FROM tblPartij p
 join tblRelatie r on (p.partId = r.partId)
WHERE p.lidId = ".mysqli_real_escape_string($db,$lidId)." and relatie = 'cred' and p.actief = 1 and r.actief = 1
ORDER BY p.naam
") or die (mysqli_error($db));
?>
 <select name= "insRelatie_0" style= "width:110;" >
 <option> </option>	
<?php		while($regel = mysqli_fetch_array($newcrediteur))
		{
		
			$opties= array($regel['relId']=>$regel['naam']);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if(isset($_POST['insRelatie_0']) && $_POST['insRelatie_0'] == $key)
		{
			$keuze = ' selected ';
		}
				
		echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
			}
		
		} ?>
 </select>

</td>
<?php if($modfin == 1 ) { ?>
<td>
<!-- KZLRUBRIEK bij nieuwe invoer -->
<?php
$newRubriek = mysqli_query($db,"
SELECT ru.rubuId, r.rubriek
FROM tblRubriekuser ru 
 join tblRubriek r on (ru.rubId = r.rubId)
 join tblRubriekhfd hr on (r.rubhId = hr.rubhId)
WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and r.rubhId = 6 and r.actief = 1 and hr.actief = 1
ORDER BY r.rubriek
") or die (mysqli_error($db));?>
 <select style="width:180;" name= "insRubriek_0" value = "" style = "font-size:12px;">
  <option></option>
<?php		while($nwrub = mysqli_fetch_array($newRubriek))
		{

			$opties = array($nwrub['rubuId'] => $nwrub['rubriek']);
			foreach ($opties as $key => $waarde)
			{
						$keuze = '';
		
		if(isset($_POST['insRubriek_0']) && $_POST['insRubriek_0'] == $key)
		{
			echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
		}
		else
		{		
		echo '<option value="' . $key . '" >' . $waarde . '</option>';
		}
			}
			
		} ?>
</select>
<!-- EINDE KZLRUBRIEK bij nieuwe invoer -->
</td>
<?php } ?>
<td><input type="checkbox" name="boxActief_0" id="c2" <?php if(true){ echo "checked"; } ?>  disabled ></td><?php
?>
<td colspan = 2><input type = "submit" name="knpInsert_" value = "Toevoegen" style = "font-size:10px;"></td></tr>
<!--
*********************************
 EINDE  VELDEN TBV NIEUWE INVOER
*********************************	 -->

<tr><td colspan = 15><hr></td></tr>


<?php

//*****************************
//** ARTIKELEN NIET IN GEBRUIK
//***************************** 
// Aantal artikelen niet in gebruik 
$Niet_in_gebruik = mysqli_query($db,"
SELECT count(artId) aant 
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (a.enhuId = eu.enhuId)
WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and a.soort = 'voer' and a.actief = 0 ") or die (mysqli_error($db));
	while ($uit = mysqli_fetch_assoc($Niet_in_gebruik))
	{	$niet_actief = $uit['aant'];	}
if ($niet_actief > 0) {
?>
 <tr> 
 <td colspan =  4 height = 80 valign = "bottom"> 
 <b>Voer niet in gebruik:</b> 
 </td></tr> 


 <tr style =  "font-size:12px;" valign =  "bottom"> 
		 <th align = "left" >Omschrijving </th>
		 <th></th> 
		 <th>stand. aantal</th> 
		 <th>Verbruiks eenheid</th>
		 <th></th> 
		 <th>Btw</th> 
		 <th>Leverancier</th> 
<?php if($modfin == 1 ) { ?>
		 <th>Rubriek</th>  <?php } ?>
		 <th>Actief</th> 
 </tr> 
<?php		
// START LOOP
$loop = mysqli_query($db,"
SELECT artId, naam 
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (a.enhuId = eu.enhuId)
WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and a.soort = 'voer' and a.actief = 0
ORDER BY a.actief desc, a.naam  ") or die (mysqli_error($db));

	while($lus = mysqli_fetch_assoc($loop))
	{
            $Id = $lus['artId'];  


$qryArtikel = mysqli_query($db,"
SELECT a.soort, a.naam, a.stdat, a.enhuId, e.eenheid, a.btw, p.naam relatie, r.rubriek, a.actief
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (a.enhuId = eu.enhuId)
 left join tblRelatie rl on (rl.relId = a.relId)
 left join tblPartij p on (p.partId = rl.partId)
 left join tblRubriekuser ru on (a.rubuId = ru.rubuId)
 left join tblRubriek r on (r.rubId = ru.rubId)
WHERE a.artId = ".mysqli_real_escape_string($db,$Id)."
ORDER BY a.naam 
") or die (mysqli_error($db));

	while($row = mysqli_fetch_assoc($qryArtikel))
	{
		$soort = "{$row['soort']}";
		$voer = "{$row['naam']}";
		$stdat = "{$row['stdat']}";
		$enhuId = "{$row['enhuId']}";
		$eenhd = "{$row['eenheid']}";
		$btw = "{$row['btw']}";
		$relatie = $row['relatie'];
		$rubriek = "{$row['rubriek']}";
		$actief = "{$row['actief']}";
?>
		<tr style = "font-size:12px;">
		<td style = "font-size : 14px;">
<?php
// Veld Medicijnnaam
echo $voer; 
// EINDE  Veld Medicijnnaam
?></td>
<td width = 1></td>

<td align = center >
<!-- Standaard verbruiksaantal -->
<?php echo $stdat; ?>		

</td><td align = center >
<?php // Verbruikseenheid
echo $eenhd; ?>
</td>	
<?php
// EINDE Verbruikseenheid

?>		<td width = 1 ></td>
<td align = center >
<?php
// Btw
echo $btw;  ?>
 </td>
<!-- EINDE Btw
 Leverancier -->
 <td> 
 	<?php if(isset($relatie)) { echo $relatie; } ?>
 </td>
<!-- EINDE Leverancier -->

<!-- Rubriek -->
<td align = center>
	<?php echo $rubriek; ?>
</td>
<!--EINDE Rubriek -->	
 <td align = center>Nee</td>
 <td>
  <input type = "submit" name="knpActive_" value = "Activeer"  style = "font-size:12px;">
 </td>
<?php if (isset ($_POST['knpActive_'])) {

$activeer = "Update tblArtikel set actief = 1 WHERE artId = ".mysqli_real_escape_string($db,$Id)." 	";
echo $activeer.'<br>';	mysqli_query($db,$activeer) or die (mysqli_error($db));
		//header("Location:  " .  echo $url;  . "Medicijnen.php");
	} ?>
<td></td></tr>

<?php    } ?> 
</td></tr>


<!--
*************************************
** EINDE ARTIKELEN NIET IN GEBRUIK
************************************* -->

<?php } ?>

<tr><td colspan = 15><hr></td></tr>

<?php } // EINDE Aantal artikelen niet in gebruik  ?>
</table>
</form>


	</TD>
<?php } else { ?> <img src='voer_php.jpg'  width='970' height='550'/> <?php }
Include "menuInkoop.php"; } ?>

</body>
</html>
