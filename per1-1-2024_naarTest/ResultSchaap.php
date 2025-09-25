<?php /* 8-8-2014 Aantal karakters werknr variabel gemaakt en html buiten php geprogrammeerd 
13-3-2015 : Login toegevoegd */
$versie = "22-1-2017"; /* 19-1-2017 Query's aangepast n.a.v. nieuwe tblDoel		22-1-2017 tblBezetting gewijzigd naar tblBezet */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '20-12-2019'; /* tabelnaam gewijzigd van UIT naar uit tabelnaam */
$versie = '31-12-2023'; /* and h.skip = 0 aangevuld aan tblHistorie en sql beveiligd met quotes */
 session_start(); ?>

<html>
<head>
<title>Resultaat schapen</title>
</head>
<body>

<center>
<?php
$titel = 'Resultaten per schaap uit 1 periode';
$subtitel = '';
Include "header.php"; ?>

		<TD width = 960 height = 400 valign = "top" >
<?php
$file = "ResultHok.php";
Include "login.php";
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { 

$periId = $_GET['pstId'];

$periode = mysqli_query($db,"
SELECT ho.hoknr, d.doel, date_format(min(h.datum),'%d-%m-%Y') van, date_format(max(ht.datum),'%d-%m-%Y') tot, date_format(p.dmafsluit,'%d-%m-%Y') afsluitdm
FROM tblHok ho
 join tblPeriode p on (p.hokId = ho.hokId)
 join tblDoel d on (p.doelId = d.doelId)
 join tblBezet b on (b.hokId = ho.hokId)
 join tblHistorie h on (h.hisId = b.hisId)
 join tblStal st on (st.stalId = h.stalId)
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
 ) uit on (uit.bezId = b.bezId)
 left join tblHistorie ht on (ht.hisId = uit.hist)
WHERE h.skip = 0 and p.periId = '".mysqli_real_escape_string($db,$periId)."'
GROUP BY ho.hoknr, d.doel, dmafsluit, p.dmafsluit
") or die (mysqli_error($db));
	while($rij = mysqli_fetch_assoc($periode))
	{$dag = date_create($rij['van']);
		$van = date_format($dag, 'd-m-Y');
	 $tot = $rij['tot'];
	 $hok = $rij['hoknr'];
	 $groep = $rij['doel'];
	 $afsldm = $rij['afsluitdm'];
	} ?>
<table border = 0 >


<tr>
<td> </td>

		<td>	
<tr>


<td colspan = 3 align = "right" style = "font-size:20px;"><b> <?php echo $hok; ?> </b></td> 
<td colspan = 3 ><i style = "font-size:12px;"> &nbsp &nbsp Doelgroep : </i><b style = "font-size:13px;"> <?php echo $groep; ?> </b></td> 
<td colspan = 7 ><i style = "font-size:12px"> &nbsp &nbsp Periode : </i><b style = "font-size:13px;"><?php Echo $van." - ".$afsldm;?></b></td>
 

</td> </tr>

<?php
$result = mysqli_query($db,"
SELECT right(s.levensnummer,$Karwerk) werknr, r.ras, s.geslacht, date_format(h.datum,'%d-%m-%Y') indm, date_format(ht.datum,'%d-%m-%Y') uitdm, datediff(ht.datum, h.datum) schpdgn, h.kg kgin, ht.kg kguit, round((ht.kg-h.kg)/datediff(ht.datum, h.datum)*1000,2) gemgroei, date_format(hdo.datum,'%d-%m-%Y') uitvdm, a.actie status
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
 ) uit on (uit.bezId = b.bezId)
 left join tblHistorie ht on (ht.hisId = uit.hist)
 left join tblHistorie hdo on (hdo.hisId = uit.hist and hdo.actId = 14)
 left join tblActie a on (a.actId = ht.actId)
WHERE h.skip = 0 and p.periId = '".mysqli_real_escape_string($db,$periId)."'
ORDER BY right(s.levensnummer,$Karwerk), h.datum
") or die (mysqli_error($db));
?>
 
<tr style = "font-size:12px;">
<th width = 0 height = 30></th>
<th style = "text-align:center;"valign="bottom";width= 80>Werknr<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 50>Ras<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 50>Geslacht<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 200>Datum erin<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 200>Datum eruit<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 60>Schaap-dagen<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Begin gewicht<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Eind gewicht<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 60>Gem groei<hr></th>
<th width = 60></th>
<th style = "text-align:center;"valign="bottom";width= 80>Reden uit verblijf<hr></th>
<!-- Echo "<th width = \"60\"></th> -->
<th style = "text-align:center;"valign="bottom";width= 80></th>
<th width = 600></th>
<!-- Echo "<th style = \"text-align:center;\"valign=\"bottom\";width= \"80\">Overleden<hr></th>";
Echo "<th width = \"1\"></th>";
Echo "<th style = \"text-align:center;\"valign=\"bottom\";width= \"80\">Hok verlaten<hr></th>";
Echo "<th width = \"1\"></th>";
Echo "<th style = \"text-align:center;\"valign=\"bottom\";width= \"80\">Moeder<hr></th>";
Echo "<th width = \"1\"></th>"; -->
	

<th width= 60 ></th>
 </tr>
<?php
		while($row = mysqli_fetch_array($result))
		{ 		if($groep == 'Geboren' && $row['status'] == 'Eruit') { $status = 'Gespeend'; } 
		   else if($groep == 'Gespeend' && $row['status'] == 'Eruit') { $status = 'Afgeleverd'; }
		   else { $status = $row['status']; }		   ?>
		
<tr align = "center">	
	   <td width = 0> </td>			
	   
	   <td width = 100 style = "font-size:15px;"> <?php echo "{$row['werknr']}"; ?> <br> </td>
	   <td width = 1> </td>	   	   
	   <td width = 100 style = "font-size:15px;"> <?php echo "{$row['ras']}"; ?> <br> </td>
	   <td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"> <?php echo "{$row['geslacht']}"; ?> <br> </td>
	   <td width = 1> </td>	
	   <td width = 200 style = "font-size:15px;"> <?php echo "{$row['indm']}"; ?> <br> </td>
	   <td width = 1> </td>
<?php	   If (empty($row['uitdm']))
	   { ?>
	   <td width = 200 style = "font-size:15px;"> <?php echo "{$row['uitvdm']}"; ?> <br> </td>
<?php	   }
	   else	
		{ ?>
	   <td width = 200 style = "font-size:15px;"> <?php echo "{$row['uitdm']}"; ?> <br> </td>
<?php		} ?>
	   <td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"> <?php echo "{$row['schpdgn']}"; ?> <br> </td>
	   <td width = 1> </td>
	   <td width = 80 style = "font-size:15px;"> <?php echo "{$row['kgin']}"; ?> <br> </td>
	   <td width = 1> </td>
	   <td width = 80 style = "font-size:15px;"> <?php echo "{$row['kguit']}"; ?> <br> </td>
	   <td width = 1> </td>
	   <td width = 60 style = "font-size:15px;"> <?php echo "{$row['gemgroei']}"; ?> <br> </td>
	   <td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"> <?php if(isset($status)) { echo $status; } else {echo "Onbekend"; } ?> <br> </td>

	   
</tr>				

		
<?php		} ?>
</tr>				
</table>


		</TD>
<?php
Include "menuRapport.php"; } ?>
</body>
</html>
