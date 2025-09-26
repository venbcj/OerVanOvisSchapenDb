<?php 
$versie = '20-2-2015'; /* login toegevoegd */ 
$versie = '19-12-2015'; /* Uitval toegevoegd */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '30-12-2023'; /* h.skip = 0 toegevoegd bij tblHistorie en sql beveiligd  */

session_start(); ?>
<html>
<head>
<title>Zoekafleverdatum</title>
</head>
<body>

<center>
<?php 
$titel = 'Keuze afleverdatum t.b.v. VKI';
$subtitel = '';
Include "header.php"; ?>

		<TD width = 960 height = 400 valign = "top">
<?php
$file = "ZoekAfldm.php";
Include "login.php";
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { ?>

<table border = 0 width= 200 height = 200 align = "left" >
<tr> <td> </td> </tr> </table>

<br>

<form action="AfleverLijst.php" method="post"> 
<b> Kies een afleverdatum : </b><br/><br/>

<?php
$result = mysqli_query($db,"
SELECT min(h.hisId) hisId, count(h.hisId) aantal, date_format(h.datum,'%d-%m-%Y') datum, r.relId, p.naam 
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
 join tblActie a on (a.actId = h.actId)
 join tblRelatie r on (r.relId = st.rel_best)
 join tblPartij p on (r.partId = p.partId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a.af = 1 and h.skip = 0
GROUP BY h.datum, r.relId, p.naam
ORDER BY r.uitval, h.datum desc
") or die (mysqli_error($db)); ?>
 <select style="width:200;" name="kzlPost" >";
 <option></option>
<?php		while($row = mysqli_fetch_array($result))
		{
				$dag = $row['datum'];
				$bedrijf = $row['relId'];
			  $hisId = $row['hisId'];
			  $ant = $row['aantal'];
			  $bestm = $row['naam'];

			$opties= array($hisId=>$dag.'&nbsp &nbsp'.$bestm.'&nbsp &nbsp'.$ant);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if(isset($_GET['kzlPost']) && $_GET['kzlPost'] == $key)
		{
			$keuze = ' selected ';
		}
				
		echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
			}
		
		}
?> </select>

&nbsp &nbsp &nbsp <input type = "submit" name="knpToon" value = "Toon" >
</form>

	</TD>
<?php
Include "menuRapport.php"; }?>

	</body>
	</html>
