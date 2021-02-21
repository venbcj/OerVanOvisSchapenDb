
<?php 
$versie = '11-11-2014'; /*header("Location: http://localhost:8080/schapendb/Hok.php");   toegevoegd. Dit ververst de pagina zodat een wijziging op het eerste record direct zichtbaar is*/
$versie = '8-3-2015'; /*Login toegevoegd */
$versie = '18-11-2015'; /* hok verandert in verblijf*/
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
session_start(); ?>
<html>
<head>
<title>Beheer</title>
</head>
<body>

<center>
<?php
$titel = 'Dekrammen';
$subtitel = ''; 
 
Include "header.php";
?>

		<TD width = '960' height = '400' valign = 'top'>
		 
<?php
$file = "Vader.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { if($modtech ==1) {

	if (isset ($_POST['knpSave_']))
{	
include "save_vader.php";
}

$zoek_stalId = mysqli_query($db,"
SELECT st.stalId
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join (
 	SELECT stalId
 	FROM tblHistorie
 	WHERE actId = 3
 ) ouder on (ouder.stalId = st.stalId)
WHERE s.geslacht = 'ram' and isnull(st.rel_best) and lidId = ".mysqli_real_escape_string($db,$lidId)." 
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
 <td align = center> <b> Halsnr</b> </td>
 <td align = center> <b> Dekram</b> </td>
 <td align = center style ="font-size:12px;"> code tbv Reader </td>
 <td></td>
 <td><input type = "submit" name= <?php echo "knpSave_"; ?> value = "Opslaan" ></td>
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
SELECT st.stalId, right(levensnummer, $Karwerk) werknr, concat(kleur,' ',halsnr) halsnr, scan 
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join (
 	SELECT stalId
 	FROM tblHistorie
 	WHERE actId = 3
 ) ouder on (ouder.stalId = st.stalId)
WHERE s.geslacht = 'ram' and isnull(st.rel_best) and lidId = ".mysqli_real_escape_string($db,$lidId)." 
GROUP BY st.stalId, levensnummer, scan 
ORDER BY right(levensnummer, $Karwerk)  
") or die (mysqli_error($db));

	while($record = mysqli_fetch_assoc($loop))
	{
            $Id = $record['stalId'];
            $werknr = $record['werknr'];
            $halsnr = $record['halsnr'];
            $scan = $record['scan'];
?>
			<!-- <input type = \"text\" name = \"txtId\" value = \"$id\">". "rowid : $rowid"; -->
<tr>
 <td> </td>
 <td align="right"> <?php echo $halsnr; ?> </td>
 <td align="center"> <?php echo $werknr; ?> </td>			
 <td width = 100 align = "center">
	<input type = text name = <?php echo "txtScan_$Id"; ?> size = 1 value = <?php echo $scan; ?>  > </td>
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
Include "menuBeheer.php"; } ?>
</body>
</html>
