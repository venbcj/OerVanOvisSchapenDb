<?php /* 2-3-2015 : Login toegevoegd 
6-1-2016 : Hoknr gewijzigd aar Verblijf */
$versie = "22-1-2017"; /* 19-1-2017 Query's aangepast n.a.v. nieuwe tblDoel		22-1-2017 tblBezetting gewijzigd naar tblBezet*/
/*Wat als voer wordt ingekocht zonder rubriek aan het voer !!?? */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '20-12-2019'; /* tabelnaam gewijzigd van UIT naar uit tabelnaam */
$versie = '31-12-2023'; /* and h.skip = 0 aangevuld aan tblHistorie en sql beveiligd met quotes */
 session_start(); ?>
<html>
<head>
<title>Rapport</title>
</head>
<body>

<center>
<?php
$titel = 'Verblijfresultaten';
$subtitel = '';
Include "header.php"; ?>
		<TD width = 960 height = 400 valign = "top" >
<?php
$file = "ResultHok.php";
Include "login.php";
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { if($modtech ==1) { ?>

	<table Border = 0 align = "center">
<tr>
<td> </td>

		<td  >	<?php
$result = mysqli_query($db,"
SELECT p.periId, hoknr, date_format(p.dmafsluit,'%d-%m-%Y') afsldm, r.ras, s.geslacht, count(distinct s.schaapId) max_bezetting, count(distinct ovp.schaapId) overpl, count(ht_do.hisId) overleden, count(ht_weg.hisId) hok_verlaten, sum(datediff(ht.datum,h.datum)) schaapdagen, round(gem.groei,2) groei, p.doelId
FROM tblHok ho
 join tblPeriode p on (p.hokId = ho.hokId)
 join tblBezet b on (b.hokId = ho.hokId)
 join tblHistorie h on (h.hisId = b.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 join tblRas r on (r.rasId = s.rasId)
 left join 
 (
	SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit
 on (uit.bezId = b.bezId)
 left join tblHistorie ht on (ht.hisId = uit.hist)
 left join (
	SELECT st.schaapId, h.hisId
	FROM tblStal st
	 join tblHistorie h on (h.stalId = st.stalId)
	WHERE (actId = 5 or actId = 6) and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
 ) ovp on (ovp.hisId = uit.hist)
 left join tblHistorie ht_do on (ht_do.hisId = uit.hist and ht_do.actId = 14 and ht_do.skip = 0)
 left join tblHistorie ht_weg on (ht_weg.hisId = uit.hist and (ht_weg.actId = 3 or ht_weg.actId = 4 or ht_weg.actId = 10 or ht_weg.actId = 12))
 left join (
	SELECT uit.periId, uit.rasId, uit.geslacht, avg(ht.kg-uit.kg) groei
	FROM (
		SELECT b.bezId, b.periId, st.schaapId, s.rasId, s.geslacht, h1.hisId hisv, min(h2.hisId) hist, h1.kg
		FROM tblBezet b
		 join tblHistorie h1 on (b.hisId = h1.hisId)
		 join tblActie a1 on (a1.actId = h1.actId)
		 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
		 join tblActie a2 on (a2.actId = h2.actId)
		 join tblStal st on (h1.stalId = st.stalId)
		 join tblSchaap s on (s.schaapId = st.schaapId)
		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
		GROUP BY b.bezId, st.schaapId, s.rasId, s.geslacht, h1.hisId, h1.kg
	) uit
	join tblHistorie ht on (ht.hisId = uit.hist)
	GROUP BY uit.periId, uit.rasId, uit.geslacht
 ) gem on (gem.periId = p.periId and gem.rasId = s.rasId and gem.geslacht = s.geslacht)
WHERE h.skip = 0 and ho.lidId = '".mysqli_real_escape_string($db,$lidId)."' and p.dmafsluit is not null
GROUP BY ho.hoknr, p.dmafsluit, r.ras, s.geslacht, p.doelId
ORDER BY p.dmafsluit desc, ho.hoknr, r.ras, s.geslacht
") or die (mysqli_error($db)); ?>
 


<tr style = "font-size:12px;">
<th width = 0 height = 30></th>
<th style = "text-align:center;"valign="bottom";width= 100>Verblijf<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 100>Afsluitdatum<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 100>Ras<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Geslacht<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Max. Bezetting<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Schaapdagen<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Verblijf verlaten<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Over- geplaatst<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Uitval<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Gem. groei<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Doelgroep<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80> </th>
<th width = 1></th>


	

<th width=60></th>
 </tr>


<?php		while($row = mysqli_fetch_array($result))
		{ $doelId = $row['doelId']; if($doelId == 1) { $doelgr = 'Geboren'; } else if($doelId == 2) { $doelgr = 'Gespeend'; } ?>
		
<tr align = "center">
	   <td width = 0> </td>			
	   
<td width = 100 style = "font-size:15px;">
  
<a href='<?php echo $url; ?>ResultSchaap.php?pstId=<?php echo $row['periId']; ?>' style = "color : blue">	   
<?php
echo $row['hoknr']. "<br>";	?> 
</a>
</td>
	   
	   <td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"><?php echo $row['afsldm']; ?> <br> </td>	   
	   <td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"><?php echo $row['ras']; ?> <br> </td>
	   <td width = 1> </td>
	   <td width = 80 style = "font-size:15px;"><?php echo $row['geslacht']; ?> <br> </td>
	   <td width = 1> </td>
	   <td width = 80 style = "font-size:15px;"><?php echo $row['max_bezetting']; ?> <br> </td>
	   <td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"><?php echo $row['schaapdagen']; ?> <br> </td>
	   <td width = 1> </td>
	   <td width = 80 style = "font-size:15px;"><?php echo $row['hok_verlaten']; ?> <br> </td>
	   <td width = 1> </td>	   
	   <td width = 80 style = "font-size:15px;"><?php echo $row['overpl']; ?> <br> </td>
	   <td width = 1> </td>	   
	   <td width = 80 style = "font-size:15px;"><?php echo $row['overleden']; ?> <br> </td>
	   <td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"><?php echo $row['groei']; ?> <br> </td>
	    <td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"><?php echo $doelgr; ?> <br> </td>
	    <td width = 1> </td>

	   <td width = 50> </td>

</tr>			
		
<?php		} ?>
</tr>		
</table>
		</TD>
<?php } else { ?> <img src='resultHok_php.jpg'  width='970' height='550'/> <?php }
Include "menuRapport.php"; } ?>

</body>
</html>