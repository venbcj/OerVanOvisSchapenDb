<?php /* 6-11-2014 gemaakt
20-2-2015 : login toegevoegd 
23-11-2015 : </form> toegvoegd */
$versie = "22-1-2017"; /* Foto toegevoegd voor gebruikers die module melden niet hebben */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '4-7-2020'; /* Omnummering toegevoegd */
$versie = '20-12-2020'; /* Menu gewijzigd */
$versie = '31-12-2023'; /* sql beveiligd met quotes */
$versie = '19-01-2024'; /* Functie aantal_melden() gedeclareerd in basifuncties.php en hernoemt naar aantal_te_melden() */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = 'top'> 31-12-24 Include "login.php"; voor Include "header.php" gezet */
$versie = '10-08-2025'; /* veld ubn uit tblLeden verwijderd */

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Registratie</title>
</head>
<body>

<?php
$titel = 'Melden RVO';
$subtitel = 'Maximaal 60 per melding';
$file = "Melden.php";
Include "login.php"; ?>

		<TD valign = 'top'>	
<?php
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { if($modmeld == 1) {

Include "responscheck.php"; 
// Controleren of inloggevens bestaan
$queryInlog = mysqli_query($db,"
SELECT relnr, urvo, prvo
FROM tblLeden
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die (mysqli_error($db));
	while ($inl = mysqli_fetch_assoc($queryInlog)) {
	$relnr = $inl['relnr'];
	$urvo = $inl['urvo'];
	$prvo = $inl['prvo']; }
if( !isset($relnr) || !isset($urvo) || !isset($prvo) ) { $onvolledig = 'variabele bestaat'; } ?>
<form action="Melden.php" method = "post">

<br><br>
<table border = 0 align="center">

<h2 align="center" style="color:blue";>Hier kun je meldingen bij RVO indienen.</h2>

<tr  height = 40><td></td></tr>
<?php
if(isset($onvolledig)) { ?> <tr><td> <?php echo "Melden is niet mogelijk. Inloggevens RVO zijn onvolledig. Zie systeemgegevens. " ;?> </td></tr> <?php } else {
?>


<?php $leeg = "<a href=' ".$url."Melden.php' style = 'color : blue'>"; $zestig = "&nbsp&nbsp&nbsp U ziet per melding max. 60 schapen. "; ?>
<tr><td> <?php 
$rows_geb = aantal_te_melden($db,$lidId,'GER');
if (!empty($rows_geb)){ ?> <a href='<?php echo $url; ?>MeldGeboortes.php' style = 'color : blue'> <?php } else {echo "$leeg"; } ?>
melden geboortes</a> </td><td style = "font-size : 12px;"><?php if (!empty($rows_geb)){	echo "&nbsp $rows_geb geboorte(s) te melden.";	}?></td></tr>

<tr><td><?php 
$rows_afl = aantal_te_melden($db,$lidId,'AFV');
if (!empty($rows_afl)){ ?><a href='<?php echo $url; ?>MeldAfvoer.php' style = 'color : blue'><?php } else { echo "$leeg"; } ?>
melden afvoer</a> </td><td style = "font-size : 12px;">
<?php if (!empty($rows_afl) && $rows_afl <= 60 ){	echo "&nbsp $rows_afl afvoer te melden.";	}
	  if (!empty($rows_afl) && $rows_afl > 60 ) {	echo "&nbsp $rows_afl afvoer te melden.".$zestig ; } ?></td></tr>

<tr><td> <?php 
$rows_uitv = aantal_te_melden($db,$lidId,'DOO');
if ($rows_uitv>0){ ?><a href='<?php echo $url; ?>MeldUitval.php' style = 'color : blue'> <?php } else {echo "$leeg"; } ?>
melden uitval</a> </td><td style = "font-size : 12px;"><?php if (!empty($rows_uitv)){	echo "&nbsp $rows_uitv uitval te melden."; }?></td></tr>

<tr><td> <?php 
$rows_aanw = aantal_te_melden($db,$lidId,'AAN');
if (!empty($rows_aanw)){ ?> <a href='<?php echo $url; ?>MeldAanvoer.php' style = 'color : blue'>  <?php } else {echo "$leeg"; } ?>
melden aanvoer</a> </td><td style = "font-size : 12px;"><?php if (!empty($rows_aanw)){	echo "&nbsp $rows_aanw aanwas te melden.";	}?></td></tr>

<tr><td> <?php 
$rows_omn = aantal_te_melden($db,$lidId,'VMD');
if (!empty($rows_omn)){ ?> <a href='<?php echo $url; ?>MeldOmnummer.php' style = 'color : blue'>  <?php } else {echo "$leeg"; } ?>
melden omnummeren</a> </td><td style = "font-size : 12px;"><?php if (!empty($rows_omn)){	echo "&nbsp $rows_omn omnummering te melden.";	}?></td></tr>



<?php } ?>
</table>
<br><br><br>

	</TD>
<?php } else { ?> <img src='Melden_php.jpg'  width='970' height='550'/> <?php }
Include "menuMelden.php"; } ?>
</tr>
</table>
</form>

</body>
</html>


 