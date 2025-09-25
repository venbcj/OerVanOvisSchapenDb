<?php 
$versie = '25-10-2015'; /*Gemaakt*/
$versie = '21-12-2015'; /*hoofdrubrieken gesorteerd*/
$versie = '19-10-2016';
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '12-7-2020'; /* ë uit database gewijzigd in echo htmlentities($string, ENT_COMPAT,'ISO-8859-1', true); bron https://www.php.net/htmlspecialchars via https://www.phphulp.nl/php/forum/topic/speciale-tekens-in-code-omzetten/50786/ */
$versie = '31-12-2023'; /* sql beveiligd met quotes */
session_start(); ?>
<html>
<head>
<title>Financieel</title>
</head>
<body>

<center>
<?php
$titel = 'Rubrieken';
$subtitel = '';
Include "header.php"; ?>

		<TD width = 960 height = 400 valign = "top">
<?php
$file = "Rubrieken.php";
Include "login.php";
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { if($modfin == 1) {

	if (isset ($_POST['knpSave_']))
{
 include "save_rubriek.php";	
}

//*****************************
//** RUBRIEKEN IN GEBRUIK
//*****************************
?>
<form action="Rubrieken.php" method="post" > 
<table border = 0 > <tr><td width = 350> <!-- Overkoepelende tabel -->
 <table border= 0   align =  "left" > 
 <tr> 
 <td colspan =  3 > 
 <b>Rubrieken in gebruik :</b> 
 </td></tr> 


 <tr style =  "font-size:12px;" valign =  "bottom"> 
		 <th width = 180>Rubriek</th>
		 <th>Actief</th>
		 <th>t.b.v.<br> Saldo-<br>&nbsp&nbspberekening</th>
 </tr> 
<?php
// START LOOP Hoofdrubrieken
$loopHRub = mysqli_query($db,"
SELECT hr.rubhId, hr.rubriek 
FROM tblRubriekhfd hr 
 join tblRubriek r on (hr.rubhId = r.rubhId)
 join tblRubriekuser ru on (r.rubId = ru.rubId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and hr.actief = 1 and r.actief = 1 and (ru.actief = 1 or ru.sal = 1) 
GROUP BY hr.rubhId, hr.rubriek 
ORDER BY hr.sort 
") or die (mysqli_error($db));

	while($rij = mysqli_fetch_assoc($loopHRub))
	{
		$rubhId = $rij['rubhId'];
		$hrubr = $rij['rubriek']; ?>
<tr><th height = 50 valign = bottom align = 'left'> <?php echo htmlentities($hrubr, ENT_COMPAT,'ISO-8859-1', true); ?>	<hr></th></tr>
<?php
// START LOOP Rubrieken
$loopRub = mysqli_query($db,"
SELECT ru.rubuId, r.rubriek, ru.actief, ru.sal
FROM tblRubriek r
 join tblRubriekuser ru on (r.rubId = ru.rubId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and r.rubhId = '$rubhId' and r.actief = 1 and (ru.actief = 1 or ru.sal = 1)
ORDER BY r.rubriek
") or die (mysqli_error($db));

	while($row = mysqli_fetch_assoc($loopRub))
	{
		$Id = "{$row['rubuId']}";
		$rubr = "{$row['rubriek']}";
		$actief = "{$row['actief']}";
?>
<tr style = "font-size:12px;">
<td width = 180 style = "font-size : 14px;">
<!-- Veld Rubrieknaam -->
	<?php echo $rubr; ?>
<!-- EINDE  Veld Rubrieknaam  -->
</td>

<td align = center>
<input type = "hidden" name = <?php echo "chkActief_$Id"; ?> size = 1 value =0 > <!-- hiddden -->

<input type = "checkbox" name = <?php echo "chkActief_$Id"; ?> id="c1" value="1" <?php echo $row['actief'] == 1 ? 'checked' : ''; ?> 		title = "Is Rubriek te gebruiken ja/nee ?"> 



</td>

<td align = center>
<input type = "checkbox" name = <?php echo "chkSalber_$Id"; ?> id="c1" value="1" <?php echo $row['sal'] == 1 ? 'checked' : ''; ?> 		title = "te gebruiken bij saldoberekening ja/nee ?"> </td>
</tr>

	</td>
<?php
	}
	}
	?>

<td></td></tr>
</table>
<!--
*************************************
** EINDE RUBRIEKEN IN GEBRUIK
*************************************
-->

</td> <!-- Ruimte tussen de twee tabellen--> <td width = 200 align = center valign = 'top'> 
<input type = "submit" name="knpSave_" value = "Opslaan" >
</td>
	

<?php

//*****************************
//** RUBRIEKEN NIET IN GEBRUIK
//***************************** 
// Aantal rubrieken niet in gebruik 
$Aantal_uit = mysqli_query($db,"
SELECT count(rubuId) aant
FROM tblRubriekuser
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actief = 0 and sal = 0
") or die (mysqli_error($db));
	while ($uit = mysqli_fetch_assoc($Aantal_uit))
	{	$niet_actief = $uit['aant'];	}
if ($niet_actief > 0) {
?>
<td width = 350 align = 'right' valign = 'top'> <!--betreft cel van overkoepelende tabel -->
<table border = 0 >
<tr> 
 <td colspan =  4 valign = "bottom"> 
 <b>Rubrieken niet in gebruik:</b> 
 </td>
</tr> 


<tr style =  "font-size:12px;" valign =  "bottom"> 
		 <th align = "left" >Rubriek</th>
		 <th>Actief</th>
		 <th>t.b.v.<br> Saldo-<br>&nbsp&nbspberekening</th>
</tr> 
<?php
// START LOOP Hoofdrubrieken
$loopHRub = mysqli_query($db,"
SELECT hr.rubhId, hr.rubriek 
FROM tblRubriekhfd hr 
 join tblRubriek r on (hr.rubhId = r.rubhId)
 join tblRubriekuser ru on (r.rubId = ru.rubId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and (hr.actief = 0 or r.actief = 0 or (ru.actief = 0 and ru.sal = 0)) 
GROUP BY hr.rubhId, hr.rubriek 
ORDER BY hr.sort 
") or die (mysqli_error($db));

	while($rij = mysqli_fetch_assoc($loopHRub))
	{
		$rubhId = $rij['rubhId'];
		$hrubr = $rij['rubriek']; ?>
<tr><th height = 50 valign = bottom align = 'left'> <?php echo htmlentities($hrubr, ENT_COMPAT,'ISO-8859-1', true); ?>	<hr></th></tr>
<?php
// START LOOP Rubrieken
$loopRub = mysqli_query($db,"
SELECT ru.rubuId, r.rubriek, ru.actief, ru.sal
FROM tblRubriek r
 join tblRubriekuser ru on (r.rubId = ru.rubId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and r.rubhId = '".mysqli_real_escape_string($db,$rubhId)."' and r.actief = 1 and ru.actief = 0 and ru.sal = 0
ORDER BY r.rubriek
") or die (mysqli_error($db));


	while($row = mysqli_fetch_assoc($loopRub))
	{
		$Id = "{$row['rubuId']}";
		$rubr = "{$row['rubriek']}";
		$actief = "{$row['actief']}";
?>
		<tr style = "font-size:12px;">
		<td style = "font-size : 14px;">
<?php
// Veld Rubrieknaam
echo $rubr; 
// EINDE  Veld Rubrieknaam
?></td>
<td align = center>
	<input type = "hidden" name = <?php echo "chkActief_$Id"; ?> size = 1 value =0 > <!-- hiddden -->
<input type = "checkbox" name = <?php echo "chkActief_$Id"; ?> id="c1" value="1" <?php echo $row['actief'] == 1 ? 'checked' : ''; ?> >
 </td>

<td align = center>
<input type = "checkbox" name = <?php echo "chkSalber_$Id"; ?> id="c1" value="1" <?php echo $row['sal'] == 1 ? 'checked' : ''; ?> 		title = "te gebruiken bij saldoberekening ja/nee ?"> </td>


	</td>
<?php
	}
	}
?>

<td></td></tr>
</td></tr></table> <!-- Einde Overkoepelende tabel -->
<?php    } ?> 
</td></tr>


<!--
*************************************
** EINDE RUBRIEKEN NIET IN GEBRUIK
************************************* -->
<?php  // EINDE Aantal rubrieken niet in gebruik  ?>


</table>
</form> 


	</TD>
<?php } else { ?> <img src='rubrieken_php.jpg'  width='970' height='550'> <?php }
Include "menuFinance.php"; } ?>
</body>
</html>
