<?php

$versie = '12-10-2014'; /*Ovv Rina 1e en 2e inenting eruit gehaald */
$versie = '28-11-2014'; /* Chargenummer toegevoegd */
$versie = '1-3-2015'; /*login toegevoegd*/
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '9-8-2020'; /* tabel tblschaap gewijzigd in tblSchaap */
$versie = '31-12-2023'; /* and h.skip = 0 aangevuld aan tblHistorie en sql beveiligd met quotes */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = top > gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */

session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Registratie</title>
</head>
<body>

<?php
$titel = 'Medicijnoverzicht';
$file = "Med_registratie.php";
include "login.php"; ?>

		<TD valign = "top">
<?php
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"])) {

$pstId = '';
If (empty($_GET['pstId']))
	{	$Id = $_GET['txtSchaapId'];	}
	  else
	{ 	$Id = $_GET['pstId']; }
				
?>
<table border = 0>
<tr style = "font-size:12px;">
 <th width = 0 height = 30></th>
 <th style = "text-align:center;"valign= bottom ;width= 80>Levensnummer<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign= bottom ;width= 50>Toediendatum<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign= bottom ;width= 50>medicijn<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign= bottom ;width= 50>chargenummer<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign= bottom ;width= 50>Aantal<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign= bottom ;width= 50>Standaard aantal<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign= bottom ;width= 50>Totaal aantal<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign= bottom ;width= 50>Eenheid<hr></th>
 <th width = 60></th>
 <th style = "text-align:center;"valign= bottom ;width= 80></th>
 <th width = 600></th>
</tr>

<?php
$result = mysqli_query($db,"
SELECT s.schaapId, s.levensnummer, date_format(h.datum,'%d-%m-%Y') toedm, a.naam, i.charge, round(sum(n.nutat),2) nutat, n.stdat, round(sum(n.nutat*n.stdat),2) totat, e.eenheid, r.reden
FROM tblSchaap s 
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblNuttig n on (n.hisId = h.hisId)
 join tblInkoop i on (n.inkId = i.inkId)
 join tblArtikel a on (a.artId = i.artId)
 join tblRedenuser ru on (ru.reduId = n.reduId)
 join tblReden r on (r.redId = ru.redId)
 join tblEenheiduser eu on (eu.enhuId = a.enhuId)
 join tblEenheid e on (e.eenhId = eu.eenhId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$Id)."' and a.soort = 'pil' and h.skip = 0
GROUP BY s.schaapId, s.levensnummer, h.datum, a.naam, i.charge, n.stdat, e.eenheid, r.reden
ORDER BY h.datum desc, i.inkId
") or die (mysqli_error($db));

while($row = mysqli_fetch_assoc($result))
			{
				$Id = $row['schaapId'];
				$levnr = $row['levensnummer'];
				$toedm = $row['toedm'];
				$naam = $row['naam'];
				$charge = $row['charge'];
				$vrbat = $row['nutat'];
				$stdat = $row['stdat'];
				$totat = $row['totat'];
				$eenh = $row['eenheid'];
				$reden = $row['reden']; ?>

<form action="MedOverzSchaap.php" method="post">

<tr>	
 <td width = 0> </td>
 <td width = 100 align = "center" style = "font-size:15px;"> <?php echo $levnr; ?> <br> 
	<input type="hidden" name="txtSchaapId" value= <?php echo $Id; ?> >
 </td>
 <td width = 1> </td>	   	   
 <td width = 100 align = "center" style = "font-size:15px;"> <?php echo $toedm; ?> <br> </td>
 <td width = 1> </td>
 <td width = 250 style = "font-size:15px;"> <?php echo $naam; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $charge; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 align = "center" style = "font-size:15px;"> <?php echo $vrbat; ?> <br> </td>
 <td width = 1> </td>	
 <td width = 100 align = "center" style = "font-size:15px;"> <?php echo $stdat; ?> </td>
 <td width = 1> </td>
 <td width = 100 align = "center" style = "font-size:15px;"> <?php echo $totat; ?> </td>
 <td width = 1> </td>
 <td width = 160 align = "center" style = "font-size:15px;"> <?php echo $eenh; ?> </td>
 <td width = 1> </td>
	   
<?php } ?>

</form>
</table>


		</TD>
<?php
include "menu1.php"; } ?>
</tr>

</table>

</body>
</html>

<!-- 19-2-14 : in $result vrbat en totat afgreond tot 2 cijfers achter de komma ovv Rina (dd 18-2) 
		Per levensnummer resultaten tonen
-->
