<?php 

$versie = '11-11-2014'; /*header("Location: http://localhost:8080/schapendb/Hok.php");   toegevoegd. Dit ververst de pagina zodat een wijziging op het eerste record direct zichtbaar is*/
$versie = '8-3-2015'; /*Login toegevoegd */
$versie = '18-11-2015'; /* hok verandert in verblijf*/
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '30-12-2023'; /* Veld scan (tblStal) weggehaald en daarmee ook de knop Opslaan en het bestand save_vader.php. Ook sql beveiligd met quotes */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = 'top'> 31-12-24 include login voor include header gezet */

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Beheer</title>
</head>
<body>

<?php
$titel = 'Dekrammen';
$file = "Vader.php";
include "login.php"; ?>

		<TD valign = 'top'>
<?php
if (is_logged_in()) { if($modtech ==1) {

$zoek_stalId = mysqli_query($db,"
SELECT st.stalId
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join (
 	SELECT stalId
 	FROM tblHistorie
 	WHERE actId = 3
 ) ouder on (ouder.stalId = st.stalId)
WHERE s.geslacht = 'ram' and isnull(st.rel_best) and lidId = '".mysqli_real_escape_string($db,$lidId)."' 
GROUP BY st.stalId  
") or die (mysqli_error($db));

	while($record = mysqli_fetch_assoc($zoek_stalId))
	{
            $pdf = $record['stalId']; }
?>
<form action="Vader.php" method="post">

<table border = 0>
<tr>
 <td width = 300> </td>
 <td align = "center"> <b> Halsnr</b> </td>
 <td align = "center"> <b> Dekram</b> </td>
 <td></td>
 <td></td>
 <td width = 200 align="right">
	<a href= '<?php echo $url;?>Vader_pdf.php?Id=<?php echo $pdf; ?>' style = 'color : blue'>
	print pagina </a>
 </td>
</tr>
<tr>
 <td> </td>
 <td colspan = 4><hr></td>
</tr>
<tr>
 <td>

<?php
	
// START LOOP
$loop = mysqli_query($db,"
SELECT st.stalId, right(levensnummer, $Karwerk) werknr, concat(kleur,' ',halsnr) halsnr
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join (
 	SELECT stalId
 	FROM tblHistorie
 	WHERE actId = 3
 ) ouder on (ouder.stalId = st.stalId)
WHERE s.geslacht = 'ram' and isnull(st.rel_best) and lidId = '".mysqli_real_escape_string($db,$lidId)."' 
GROUP BY st.stalId, levensnummer
ORDER BY right(levensnummer, $Karwerk)  
") or die (mysqli_error($db));

	while($record = mysqli_fetch_assoc($loop))
	{
            $Id = $record['stalId'];
            $werknr = $record['werknr'];
            $halsnr = $record['halsnr'];
?>
			<!-- <input type = \"text\" name = \"txtId\" value = \"$id\">". "rowid : $rowid"; -->
<tr>
 <td> </td>
 <td align="right"> <?php echo $halsnr; ?> </td>
 <td align="center"> <?php echo $werknr; ?> </td>			
 <td> </td>
</tr>

<?php } // EINDE LOOP

?>
</td>
<td>
</td>
</tr>
</table>


</form>


</td>

	</TD>
<?php } 
include "menuBeheer.php"; } ?>
</body>
</html>
