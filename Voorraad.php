<!-- 8-3-2015 : Login toegevoegd 
12-12-2015 : kolom 'Aantal nog toe te dienen' aangevuld met eenheid 
29-8-2020 : Voorraadcorrectie toegevoegd -->
<?php
$versie = '12-12-2015';
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = 'top'> 31-12-24 include login voor include header gezet */

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Inkoop</title>
</head>
<body>

<?php
$titel = 'Voorraad';
$file = "Voorraad.php";
include "login.php"; ?>

		<TD valign = 'top'>
<?php
if (is_logged_in()) { if($modtech ==1) { ?>

<table border = 0>
<tr><td colspan = 5 style = "font-size : 18px;"><b> Voorraad Voer </b></td></tr>
<tr valign = "bottom">
 <td><i><sub>Omschrijving</sub></i><hr></td>
 <td colspan = 2 width = 100 align = "center"><i><sub>Aantal nog toe te dienen</sub></i><hr></td>
 <td colspan = 2 width = 80><i><sub>Totale hoeveelheid</sub></i><hr></td>
 <td width = 70><i><sub></sub></i><hr></td>
</tr><?php 
// 1-8-2016 : Er is geen rekening gehouden met de inkoopeenheden bij sommatie alle inkoophoeveelheden. Reden : te complex t.o.v. de kans dat eenheden veranderen. Mogelijk in de toekomst noodzakelijk
$queryvoer = mysqli_query($db,"
SELECT a.artId, a.naam, a.stdat, e.eenheid, i.inkat-coalesce(v.vbrat,0) vrdat, round((i.inkat-coalesce(v.vbrat,0))/a.stdat,2) toedat
FROM tblArtikel a
 join (
	SELECT i.artId, i.enhuId, sum(i.inkat) inkat
	FROM tblEenheiduser eu
	 join tblInkoop i on (i.enhuId = eu.enhuId)
	 join tblArtikel a on (a.artId = i.artId)
	WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and a.soort = 'voer'
	GROUP BY a.artId, i.enhuId
 ) i on (a.artId = i.artId)
 join tblEenheiduser eu on (eu.enhuId = i.enhuId)
 join tblEenheid e on (e.eenhId = eu.eenhId)
 left join (
	SELECT a.artId, sum(v.nutat*v.stdat) vbrat
	FROM tblEenheiduser eu
	 join tblArtikel a on (a.enhuId = eu.enhuId)
	 join tblInkoop i on (i.artId = a.artId)
	 join tblVoeding v on (i.inkId = v.inkId)
	WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and a.soort = 'voer'
	GROUP BY a.artId
 ) v on (i.artId = v.artId)
WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and a.soort = 'voer' and i.inkat-coalesce(v.vbrat,0) > 0
ORDER BY a.naam
") or die (mysqli_error($db));
while ($qryvr = mysqli_fetch_assoc($queryvoer))	{
	$artId = $qryvr ['artId'];
	$naam = $qryvr ['naam'];
	$vrdat = $qryvr ['vrdat'];
	$stdat = str_replace('.00', '', $qryvr['stdat']);
	$toedat = str_replace('.00', '', $qryvr ['toedat']);
	$eenheid = $qryvr ['eenheid'];	

?>
<tr>
 <td width = 300 ><table border = 0><tr><td><?php echo "$naam";?></td>
 <td><i style = "font-size : 13px;"><?php echo "&nbsp &nbsp per $stdat $eenheid"; ?></i></td></tr></table></td>
 <td align = "right" ><?php echo $toedat; ?></td>
 <td><i style = "font-size : 14px;" > <?php echo ' x '.$stdat.$eenheid; ?> </i></td>
 <td align = "right"><?php echo "$vrdat"; ?></td>
 <td><i style = "font-size : 13px;"><?php echo "$eenheid"; ?></i></td>
 <td></td>
 <td> <a href=' <?php echo $url; ?>Voorraadcorrectie.php?pst=<?php echo $artId; ?>' style = "color : blue; font-size : 13px;"> Corrigeren </a></td>


 <td><?php echo ""; ?></td>
</tr>											<?php } ?>
</table>
<hr><br/>
<table border = 0>
<tr><td colspan = 5 style = "font-size : 18px;"><b> Voorraad medicijn </b></td></tr>
<tr valign = "bottom">
 <td><i><sub>Omschrijving</sub></i><hr></td>
 <td colspan = 2 width = 100 align = "center"><i><sub>Aantal nog toe te dienen</sub></i><hr></td>
 <td colspan = 2 width = 80><i><sub>Totale hoeveelheid</sub></i><hr></td>
 <td width = 180><i><sub> &nbsp &nbsp Chargenummer</sub></i><hr></td>
</tr><?php
$querypil = mysqli_query($db,"
SELECT a.artId, a.naam, a.stdat, e.eenheid, i.charge, sum(i.inkat-coalesce(n.vbrat,0)) vrdat, round(sum((i.inkat-coalesce(n.vbrat,0))/a.stdat),2) toedat, artvrd.totvrd
FROM tblArtikel a
 join tblInkoop i on (a.artId = i.artId)
 join tblEenheiduser eu on (eu.enhuId = i.enhuId)
 join tblEenheid e on (e.eenhId = eu.eenhId)
 left join (
	SELECT n.inkId, sum(n.nutat*n.stdat) vbrat
	FROM tblEenheiduser eu
	 join tblArtikel a on (a.enhuId = eu.enhuId)
	 join tblInkoop i on (i.artId = a.artId)
	 join tblNuttig n on (i.inkId = n.inkId)
	WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and a.soort = 'pil'
	GROUP BY n.inkId
 ) n on (i.inkId = n.inkId)
 left join (
	SELECT artId, sum(totat) totvrd
	FROM (
		SELECT a.artId, round(i.inkat - sum(coalesce(n.nutat*n.stdat,0)),0) totat
		FROM tblEenheiduser eu
		 join tblArtikel a on (eu.enhuId = a.enhuId)
		 join tblInkoop i on (a.artId = i.artId)
		 left join tblNuttig n on (n.inkId = i.inkId) 
		WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and a.soort = 'pil'
		GROUP BY i.inkId
	 ) vrd
	GROUP BY artId
 ) artvrd on (artvrd.artId = a.artId)
WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and a.soort = 'pil' and i.inkat-coalesce(n.vbrat,0) > 0 
GROUP BY a.artId, a.naam, a.stdat, e.eenheid, i.charge, artvrd.totvrd
ORDER BY a.naam, i.inkId
 ") or die (mysqli_error($db));
while ($qryvr = mysqli_fetch_assoc($querypil))	{
	$artId = $qryvr ['artId'];
	$naam = $qryvr ['naam'];
	$charge = $qryvr ['charge'];
	$vrdat = $qryvr ['vrdat'];
	$stdat = str_replace('.00', '', $qryvr['stdat']);
	$toedat = str_replace('.00', '', $qryvr ['toedat']);
	$eenheid = $qryvr ['eenheid'];	

?>
<tr>
 <td width = 300 ><table border = 0><tr><td><?php echo "$naam";?></td>
 <td><i style = "font-size : 13px;"><?php echo "&nbsp &nbsp per $stdat $eenheid"; ?></i></td></tr></table></td>
 <td width = 40 align = "right" ><?php echo $toedat; ?> </td>
 <td><i style = "font-size : 14px;" > <?php echo ' x '.$stdat.$eenheid; ?> </i></td>
 <td align = "right"><?php echo "$vrdat"; ?></td>
 <td><i style = "font-size : 13px;"><?php echo "$eenheid"; ?></i></td>
 <td><?php echo "&nbsp &nbsp &nbsp".$charge; ?></td>
 <td> <a href=' <?php echo $url; ?>Voorraadcorrectie.php?pst=<?php echo $artId; ?>' style = "color : blue; font-size : 13px;"> Corrigeren </a></td>
</tr>											<?php } ?>
</table>
</TD>
<?php } else { ?> <img src='Voorraad_php.jpg'  width='970' height='550'/> <?php }
include "menuInkoop.php"; } ?>
</tr>
</table>

</body>
</html>
