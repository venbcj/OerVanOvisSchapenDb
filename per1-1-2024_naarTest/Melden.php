<?php /* 6-11-2014 gemaakt
20-2-2015 : login toegevoegd 
23-11-2015 : </form> toegvoegd */
$versie = "22-1-2017"; /* Foto toegevoegd voor gebruikers die module melden niet hebben */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '4-7-2020'; /* Omnummering toegevoegd */
$versie = '20-12-2020'; /* Menu gewijzigd */
$versie = '31-12-2023'; /* sql beveiligd met quotes */

 session_start(); ?>
<html>
<head>
<title>Registratie</title>
</head>
<body>

<center>
<?php
$titel = 'Melden RVO';
$subtitel = 'Maximaal 60 per melding';
Include "header.php";?>
	<TD width = 960 height = 400 valign = "top">	

<?php
$file = "Melden.php";
Include "login.php";
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { if($modmeld == 1) {

Include "responscheck.php"; 
// Controleren of inloggevens bestaan
$queryInlog = mysqli_query($db,"
SELECT relnr, ubn, urvo, prvo
FROM tblLeden
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die (mysqli_error($db));
	while ($inl = mysqli_fetch_assoc($queryInlog)) {
	$relnr = $inl['relnr'];
	$ubn = $inl['ubn'];
	$urvo = $inl['urvo'];
	$prvo = $inl['prvo']; }
if( !isset($relnr) || !isset($ubn) || !isset($urvo) || !isset($prvo) ) { $onvolledig = 'variabele bestaat'; }

// Functie aantal nog te melden
function aantal_melden($datb,$lidid,$fldCode) {
$aantalmelden = mysqli_query($datb,"
SELECT count(*) aant
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($datb,$lidid)."' and h.skip = 0 and isnull(r.dmmeld) and code = '".mysqli_real_escape_string($datb,$fldCode)."'
"); // Foutafhandeling zit in return FALSE
	if($aantalmelden)
	{	$row = mysqli_fetch_assoc($aantalmelden);
            return $row['aant'];
	}
	return FALSE; // Foutafhandeling
}
// Einde Functie aantal nog te melden
?>
<form action="Melden.php" method = "post">

<br><br>
<table border = 0>
<?php
if(isset($onvolledig)) { ?> <tr><td> <?php echo "Melden is niet mogelijk. Inloggevens RVO zijn onvolledig. Zie systeemgegevens. " ;?> </td></tr> <?php } else {
?>


<?php $leeg = "<a href=' ".$url."Melden.php' style = 'color : blue'>"; $zestig = "&nbsp&nbsp&nbsp U ziet per melding max. 60 schapen. "; ?>
<tr><td> <?php 
$rows_geb = aantal_melden($db,$lidId,'GER');
if (!empty($rows_geb)){ ?> <a href='<?php echo $url; ?>MeldGeboortes.php' style = 'color : blue'> <?php } else {echo "$leeg"; } ?>
melden geboortes</a> </td><td style = "font-size : 12px;"><?php if (!empty($rows_geb)){	echo "&nbsp $rows_geb geboorte(s) te melden.";	}?></td></tr>

<tr><td><?php 
$rows_afl = aantal_melden($db,$lidId,'AFV');
if (!empty($rows_afl)){ ?><a href='<?php echo $url; ?>MeldAfvoer.php' style = 'color : blue'><?php } else { echo "$leeg"; } ?>
melden afvoer</a> </td><td style = "font-size : 12px;">
<?php if (!empty($rows_afl) && $rows_afl <= 60 ){	echo "&nbsp $rows_afl afvoer te melden.";	}
	  if (!empty($rows_afl) && $rows_afl > 60 ) {	echo "&nbsp $rows_afl afvoer te melden.".$zestig ; } ?></td></tr>

<tr><td> <?php 
$rows_uitv = aantal_melden($db,$lidId,'DOO');
if ($rows_uitv>0){ ?><a href='<?php echo $url; ?>MeldUitval.php' style = 'color : blue'> <?php } else {echo "$leeg"; } ?>
melden uitval</a> </td><td style = "font-size : 12px;"><?php if (!empty($rows_uitv)){	echo "&nbsp $rows_uitv uitval te melden."; }?></td></tr>

<tr><td> <?php 
$rows_aanw = aantal_melden($db,$lidId,'AAN');
if (!empty($rows_aanw)){ ?> <a href='<?php echo $url; ?>MeldAanvoer.php' style = 'color : blue'>  <?php } else {echo "$leeg"; } ?>
melden aanvoer</a> </td><td style = "font-size : 12px;"><?php if (!empty($rows_aanw)){	echo "&nbsp $rows_aanw aanwas te melden.";	}?></td></tr>

<tr><td> <?php 
$rows_omn = aantal_melden($db,$lidId,'VMD');
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
</center>

</body>
</html>


 