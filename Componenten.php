<?php

require_once("autoload.php");

/* 23-10-2015 : Gemaakt.
19-10-2016 : Omgebouwd naar Release 2 (28-10 geïnstalleerd op productieomgeving)*/
$versie = '29-10-2016';/* : in tblElement veldnaam 'kenmerk' gewijzigd naar 'eenheid'*/
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '11-7-2020'; /* € gewijzigd in &euro; */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD align = "center" valign = "top"> 31-12-24 include login voor include header gezet */
$versie = '07-03-2025'; /* <input type= "hidden" name= <?php echo "txtId_$Id"; ?> size = 1 value = <?php echo "$Id";?> > verwijderd */
 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Financieel</title>
</head>
<body>

<?php
$titel = 'Componenten';
$file = "Componenten.php";
include "login.php"; ?>

		<TD align = "center" valign = "top">
<?php
if (Auth::is_logged_in()) { if($modfin == 1) {

if (isset ($_POST['knpSave_'])) { include "save_component.php"; }

//*****************************
//** COMPONENTEN IN GEBRUIK
//*****************************
?>
<form action="Componenten.php" method="post" > 
<table border= 0 > <tr><td width = 350 valign = 'top'> <!-- Overkoepelende tabel -->
 <table border= 0   align =  "left" > 
 <tr> 
 <td colspan =  3 > 
 <b>Componenten in gebruik :</b> 
 </td></tr> 


 <tr style =  "font-size:12px;" valign =  "bottom"> 
		 <th width = 180>Component</th>
		 <th></th>
		 <th>Waarde</th>
		 <th></th>
		 <th>Actief</th> 
		 <th>t.b.v.<br> Saldo-<br>&nbsp&nbspberekening</th> 
 </tr> 
<?php
// START LOOP Eenheid
$loopEenh = mysqli_query($db,"
select e.eenheid
from tblElement e
 join tblElementuser eu on (e.elemId = eu.elemId)
where eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and (actief = 1 or eu.sal = 1)
group by e.eenheid
order by e.eenheid
") or die (mysqli_error($db));

	while($rij = mysqli_fetch_assoc($loopEenh))
	{
		$eenh = "{$rij['eenheid']}"; 
	
	if($eenh == 'euro') 	{ $eenheid = 'Bedragen'; }
	if($eenh == 'getal') 	{ $eenheid = 'Getallen'; }
	if($eenh == 'procent') 	{ $eenheid = 'Percentages'; } ?>
	
<tr><th height = 52 align = left valign = bottom><?php echo $eenheid; ?><hr></th></tr>	
<?php		
// START LOOP Componenten
$loopCom = mysqli_query($db,"
select eu.elemuId, e.element, eu.waarde, e.eenheid, eu.actief, eu.sal
from tblElement e
 join tblElementuser eu on (e.elemId = eu.elemId)
where eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and e.eenheid = '".mysqli_real_escape_string($db,$eenh)."' and (actief = 1 or eu.sal = 1)
order by e.eenheid, e.element
") or die (mysqli_error($db));

	while($row = mysqli_fetch_assoc($loopCom))
	{
		$Id = "{$row['elemuId']}";
		$compo = "{$row['element']}";
		$waarde = "{$row['waarde']}";
		$eenh = "{$row['eenheid']}";
		$actief = "{$row['actief']}";
		$sal = "{$row['sal']}";
		
	$eenheid_voor = array(''=>'','euro'=>'&euro;','getal'=>'','procent'=>'');
	$eenheid_achter = array(''=>'','euro'=>'','getal'=>'','procent'=>'%');
?>
<tr style = "font-size:12px;">
<td width = 180 style = "font-size : 14px;">
<!-- Veld Componentnaam -->
	<?php echo $compo; ?>
<!-- EINDE  Veld Componentnaam  -->
</td>
<td width = 1><?php echo $eenheid_voor[$eenh]; ?></td>
<td><!--Waarde -->
<input type= "text" name= <?php echo "txtWaarde_$Id"; ?> size = 3 style = "font-size:12px; text-align : right" value = <?php echo $waarde ; ?>  >
</td>
<td width = 1 ><?php echo $eenheid_achter[$eenh]; ?></td>

<td align = center>
<input type = "hidden" name = <?php echo "chkActief_$Id"; ?> size = 1 value =0 >
<input type = "checkbox" name = <?php echo "chkActief_$Id"; ?> id="c1" value="1" <?php echo $row['actief'] == 1 ? 'checked' : ''; ?> 		title = "Is Component te gebruiken ja/nee ?"> </td>

<td align = center>
<input type = "hidden" name = <?php echo "chkSalber_$Id"; ?> size = 1 value =0 >
<input type = "checkbox" name = <?php echo "chkSalber_$Id"; ?> id="c1" value="1" <?php echo $row['sal'] == 1 ? 'checked' : ''; ?> 		title = "te gebruiken bij saldoberekening ja/nee ?"> </td>
</tr>

	</td>
<?php		
	}
	} ?>

<td></td></tr>
</table>
<!--
*************************************
** EINDE COMPONENTEN IN GEBRUIK
*************************************
-->

</td> <!-- Ruimte tussen de twee tabellen--> <td width = 200 align = center valign = 'top'> 
<input type = "submit" name="knpSave_" value = "Opslaan" >
</td>
	

<?php

//*****************************
//** COMPONENTEN NIET IN GEBRUIK
//***************************** 
// Aantal componenten niet in gebruik 
$Aantal_uit = mysqli_query($db,"
select count(elemuId) aant
from tblElementuser
where lidId = ".mysqli_real_escape_string($db,$lidId)." and actief = 0 and sal = 0
") or die (mysqli_error($db));
	while ($uit = mysqli_fetch_assoc($Aantal_uit))
	{	$niet_actief = $uit['aant'];	}
if ($niet_actief > 0) {
?>
<td width = 350 align = 'right' valign = 'top'> <!--betreft cel van overkoepelende tabel -->
<table border= 0 >
 <tr> 
 <td colspan =  4 valign = "bottom"> 
 <b>Componenten niet in gebruik:</b> 
 </td></tr> 


 <tr style =  "font-size:12px;" valign =  "bottom"> 
		 <th align = "left" >Component</th>
		 <th></th>
		 <th align = "left" >Waarde</th>
		 <th></th>
		 <th>Actief</th>
		 <th>t.b.v.<br>Saldo-<br>&nbsp&nbspberekening</th> 
 </tr> 
<?php		
// START LOOP Eenheid
$loopEenh = mysqli_query($db,"
select e.eenheid
from tblElement e
 join tblElementuser eu on (e.elemId = eu.elemId)
where eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and eu.actief = 0 and sal = 0
group by e.eenheid
order by e.eenheid
") or die (mysqli_error($db));

	while($rij = mysqli_fetch_assoc($loopEenh))
	{
		$eenh = "{$rij['eenheid']}"; 
	
	if($eenh == 'euro') 	{ $eenheid = 'Bedragen'; }
	if($eenh == 'getal') 	{ $eenheid = 'Getallen'; }
	if($eenh == 'procent') 	{ $eenheid = 'Percentages'; } ?>
	
<tr><th height = 52 align = left valign = bottom><?php echo $eenheid; ?><hr></th></tr>	
<?php		
// START LOOP Componenten
$loopCom = mysqli_query($db,"
select eu.elemuId, e.element, eu.waarde, eu.actief, eu.sal
from tblElement e
 join tblElementuser eu on (e.elemId = eu.elemId)
where eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and e.eenheid = '".mysqli_real_escape_string($db,$eenh)."' and actief = 0 and sal = 0
order by element
") or die (mysqli_error($db));


	while($row = mysqli_fetch_assoc($loopCom))
	{
		$Id = "{$row['elemuId']}";
		$compo = "{$row['element']}";
		$waarde = "{$row['waarde']}";
		$actief = "{$row['actief']}";
		$sal = "{$row['sal']}";
?>
		<tr style = "font-size:12px;">
		<td style = "font-size : 14px;">
<?php
// Veld Componentnaam
echo $compo; 
// EINDE  Veld Componentnaam
?></td>
<td width = 1></td>
<td><!--Registratienummer -->
 <?php echo $waarde ; ?>
 <input type= "hidden" name= <?php echo "txtWaarde_$Id"; ?> size = 3 style = "font-size:12px; text-align : right" value = <?php echo $waarde ; ?>  > <!-- hiddden -->
		<!-- txtWaarde nodig anders wordt de laatste waarde/variabele van de actieven vastgehouden en doorgegeven naar de niet actieven -->
</td>

<td width = 1 ></td>
<td align = center>
<input type = "hidden" name = <?php echo "chkActief_$Id"; ?> size = 1 value =0 > <!-- hiddden -->
<input type = "checkbox" name = <?php echo "chkActief_$Id"; ?> id="c1" value="1" <?php echo $row['actief'] == 1 ? 'checked' : ''; ?> > </td>

<td align = center>
<input type = "hidden" name = <?php echo "chkSalber_$Id"; ?> size = 1 value =0 >
<input type = "checkbox" name = <?php echo "chkSalber_$Id"; ?> id="c1" value="1" <?php echo $row['sal'] == 1 ? 'checked' : ''; ?> 		title = "te gebruiken bij saldoberekening ja/nee ?"> </td>
<?php		
	}
	}
?>

<td></td></tr>
</td></tr></table> <!-- Einde Deze tabel bevat twee tabellen -->
<?php    } ?> 
</td></tr>


<!--
*************************************
** EINDE COMPONENTEN NIET IN GEBRUIK
************************************* -->
<?php  // EINDE Aantal componenten niet in gebruik  ?>


</table>
</form> 


	</TD>
<?php } else { ?> <img src='componenten_php.jpg'  width='970' height='550'/> <?php }
include "menuFinance.php"; } ?>
</body>
</html>
