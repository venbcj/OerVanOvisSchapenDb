<?php /* 28-02-2015 html gesplitst van php en login toegevoegd 
19-1-2015 : Hok- gewijzigd naar verblijfoverzicht */
$versie = "18-1-2017"; /* Query's aangepast n.a.v. nieuwe tblDoel incl. $_GET['pstgroep'] */
$versie = "22-1-2017"; /* tblBezetting gewijzigd naar tblBezet */
$versie = "13-2-2017"; /* tekst hok gewijzigd naar verblijf */
$versie = "1-3-2017"; /* Ras niet verplicht gemaakt door left join te maken */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '20-12-2019'; /* tabelnaam gewijzigd van UIT naar uit tabelnaam */
$versie = '30-12-2023'; /* and h.skip = 0 toegevoegd aan tblHistorie en sql beveiligd met quotes */
$versie = "11-03-2024"; /* Bij geneste query uit 
join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId) gewijzgd naar
join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
I.v.m. historie van stalId 22623. Dit dier is eerst verkocht en met terugwerkende kracht geplaatst in verblijf Afmest 1 */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" align = "center"> gewijzigd naar <TD align = "center" valign = "top"> 31-12-24 include login voor include header gezet */

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Actueel</title>
</head>
<body>

<?php
$titel = 'Verblijfoverzicht';
$file = "hoklijst.php";
include "login.php"; ?>

		<TD align = "center" valign = "top">
<?php
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {

$pstId = $_GET['pstgroep'];

$zoek_doel = mysqli_query($db,"
SELECT doel
FROM tblDoel
WHERE doelId = '".mysqli_real_escape_string($db,$pstId)."' 
") or die (mysqli_error($db));
while($dl = mysqli_fetch_array($zoek_doel)){ $dgroep = $dl['doel']; } ?>

<table border = 0 >
<tr >
<td > </td>
<tr>
<td width="200"> </td>
<td colspan = 15 width = 300 align = "center" valign = "top"> <b style = "font-size : 19px;"><?php echo $dgroep."en";?> </b></td>
<td width="200" align="right"> 
 <a href= '<?php echo $url;?>HokLijst_pdf.php?Id=<?php echo $pstId; ?>' style = 'color : blue'> print pagina </a></td>
</tr>
</table>
<?php
if($pstId == 1) {
$zoek_hok_ingebruik_geb = mysqli_query($db,"
SELECT ho.hokId, ho.hoknr
FROM tblBezet b
 join tblHok ho on (b.hokId = ho.hokId)
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 left join tblRas r on (s.rasId = r.rasId)
 left join 
 (
	SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 left join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.skip = 0 and isnull(uit.bezId) and isnull(spn.schaapId) and isnull(prnt.schaapId)
GROUP BY ho.hokId, ho.hoknr
") or die (mysqli_error($db)); $zoek_hok_ingebruik = $zoek_hok_ingebruik_geb; }

if($pstId == 2) {
$zoek_hok_ingebruik_spn = mysqli_query($db,"
SELECT ho.hokId, ho.hoknr
FROM tblBezet b
 join tblHok ho on (b.hokId = ho.hokId)
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 left join tblRas r on (s.rasId = r.rasId)
 left join 
 (
	SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.skip = 0 and isnull(uit.bezId) and isnull(prnt.schaapId)
GROUP BY ho.hokId, ho.hoknr
") or die (mysqli_error($db)); $zoek_hok_ingebruik = $zoek_hok_ingebruik_spn; }
		
	while($hk = mysqli_fetch_assoc($zoek_hok_ingebruik))
		{ $hokId = $hk['hokId']; $hok = $hk['hoknr'];  ?>
				
				
<table border = 0 >
<tr>

<td colspan = 6  align ="center">	
<b style = "font-size : 15px;"><?php echo $hok; ?> </b> </td>

</tr>

<?php
if($pstId == 1) {
$zoek_nu_in_verblijf_geb = mysqli_query($db,"
SELECT ho.hoknr, count(b.bezId) nu, r.ras, s.geslacht
FROM tblBezet b
 join tblHok ho on (b.hokId = ho.hokId)
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 left join tblRas r on (s.rasId = r.rasId)
 left join 
 (
	SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 left join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and h.skip = 0 and isnull(uit.bezId) and isnull(spn.schaapId) and isnull(prnt.schaapId)
GROUP BY ho.hoknr, r.ras, s.geslacht
") or die (mysqli_error($db));  $zoek_nu_in_verblijf = $zoek_nu_in_verblijf_geb; }

if($pstId == 2) {
$zoek_nu_in_verblijf_spn = mysqli_query($db,"
SELECT ho.hoknr, count(b.bezId) nu, r.ras, s.geslacht
FROM tblBezet b
 join tblHok ho on (b.hokId = ho.hokId)
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 left join tblRas r on (s.rasId = r.rasId)
 left join 
 (
	SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and h.skip = 0 and isnull(uit.bezId) and isnull(prnt.schaapId)
GROUP BY ho.hoknr, r.ras, s.geslacht
") or die (mysqli_error($db));  $zoek_nu_in_verblijf =$zoek_nu_in_verblijf_spn; } ?>
		
 
<tr style = "font-size:12px;">
<th style = "text-align:center;"valign="bottom";width= 100>Ras<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 100>Geslacht<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 100>Nu in verblijf<hr></th>
<th width = 1></th>
 </tr>
<?php
		while($n = mysqli_fetch_assoc($zoek_nu_in_verblijf))
		{ $nu = $n['nu']; ?>
		
<tr align = "center" style = "font-size:15px;">	

	   <td width = 110 align = "left"> <?php echo $n['ras']; ?> <br> </td>	   
	   <td width = 1> </td>
	   <td width = 80 style = "font-size:15px;"> <?php echo $n['geslacht']; ?> <br> </td>
	   <td width = 1> </td>
	   <td width = 85 style = "font-size:15px;"> <?php echo $nu; ?> <br> </td>
	   <td width = 1> </td>
	   
</tr>				
<?php	} ?>
		
<td width = 1 height = 20> </td>
</tr>		
</table> <?php

} ?>



	</TD>

<?php
include "menu1.php"; } ?>


</tr>

</table>

</body>
</html>
