<?php
$versie = '22-12-2019'; /* Kopie van Hoklijsten.php */
$versie = '31-12-2023'; /* and h.skip = 0 aangevuld aan tblHistorie en sql beveiligd */
$versie = "11-03-2024"; /* Bij geneste query uit 
join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId) gewijzgd naar
join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
I.v.m. historie van stalId 22623. Dit dier is eerst verkocht en met terugwerkende kracht geplaatst in verblijf Afmest 1 */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Actueel</title>
</head>
<body>

<?php
$titel = 'Schapen zonder verblijf';
$file = "Loslopers.php";
include "login.php"; ?>

		<TD align = "center" valign = "top">
<?php
if (is_logged_in()) { 

$zoek_aantal_doelgroep1 = mysqli_query($db,"
SELECT count(hin.schaapId) aantin
FROM (
	SELECT st.schaapId, max(hisId) hisId
	FROM tblStal st 
	 join tblHistorie h on (st.stalId = h.stalId)
	 join tblActie a on (a.actId = h.actId) 
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(st.rel_best) and a.aan = 1 and h.skip = 0
	GROUP BY st.schaapId
 ) hin
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
	SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
 left join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = hin.schaapId)
 left join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = hin.schaapId)
WHERE (isnull(b.hokId) or uit.hist is not null) and isnull(spn.schaapId) and isnull(prnt.schaapId)
") or die (mysqli_error($db));
		
	while($nu1 = mysqli_fetch_assoc($zoek_aantal_doelgroep1))
		{ $aanwezig1 = $nu1['aantin']; }

$zoek_aantal_doelgroep2 = mysqli_query($db,"
SELECT count(hin.schaapId) aantin
FROM (
	SELECT st.schaapId, max(hisId) hisId
	FROM tblStal st 
	 join tblHistorie h on (st.stalId = h.stalId)
	 join tblActie a on (a.actId = h.actId) 
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(st.rel_best) and a.aan = 1 and h.skip = 0
	GROUP BY st.schaapId
 ) hin
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
	SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
 join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = hin.schaapId)
 left join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = hin.schaapId)
WHERE (isnull(b.hokId) or uit.hist is not null) and isnull(prnt.schaapId)
") or die (mysqli_error($db));
		
	while($nu2 = mysqli_fetch_assoc($zoek_aantal_doelgroep2))
		{ $aanwezig2 = $nu2['aantin']; }

	$aanwezig = $aanwezig1 + $aanwezig2;

$zoek_aantal_doelgroep3 = mysqli_query($db,"
SELECT count(hin.schaapId) aantin
FROM (
	SELECT st.schaapId, max(hisId) hisId
	FROM tblStal st 
	 join tblHistorie h on (st.stalId = h.stalId)
	 join tblActie a on (a.actId = h.actId) 
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(st.rel_best) and a.aan = 1 and h.skip = 0
	GROUP BY st.schaapId
 ) hin
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
	SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
 join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = hin.schaapId)
WHERE (isnull(b.hokId) or uit.hist is not null)
") or die (mysqli_error($db));
		
	while($nu3 = mysqli_fetch_assoc($zoek_aantal_doelgroep3))
		{ $aanwezig3 = $nu3['aantin']; }

		$aanwezig_incl = $aanwezig + $aanwezig3; ?>

<table border = 0>
<tr>
 <td colspan = 6 style = "font-size : 15px;"> </td>
 <td><a href= '<?php echo $url;?>Loslopers_pdf.php?' style = 'color : blue'>print pagina </a></td>
 <td> </td>
 <td rowspan = 6 width = 100 align = "center">
 	<hr>

 <?php 
if ($aanwezig_incl > 0) { $_SESSION["DT1"] = NULL; $_SESSION["BST"] = NULL; ?>
 <a href='<?php echo $url; ?>LoslopersPlaatsen.php?' style = "color : blue">	
	In verblijf plaatsen
 </a> <?php } ?>
 <br>
 <br>
 <?php if(isset($aanwezig3) && $aanwezig3 > 0) { $_SESSION["DT1"] = NULL; $_SESSION["BST"] = NULL; ?>
 <a href='<?php echo $url; ?>LoslopersVerkopen.php?' style = "color : blue">   
	Verkopen	 
 </a> <?php } else { ?> <u style = "color : grey"> Verkopen </u> <?php } ?>
 <br>
 <br>

 </td>
</tr>

<?php
		
if($aanwezig1 > 0) { ?>

<tr height = 35 valign =bottom>
 <td colspan = 6><i style = "font-size : 15px;" >Aantal lammeren voor spenen :  &nbsp </i><b style = "font-size:15px;"><?php echo $aanwezig1;?> </b></td>
</tr>
<?php
$schapen_geb = mysqli_query ($db,"
SELECT s.schaapId, right(s.levensnummer,".mysqli_real_escape_string($db,$Karwerk).") werknr, r.ras, s.geslacht
FROM tblSchaap s
 left join tblRas r on (r.rasId = s.rasId)
 join (
	SELECT st.schaapId, max(hisId) hisId
	FROM tblStal st 
	 join tblHistorie h on (st.stalId = h.stalId)
	 join tblActie a on (a.actId = h.actId) 
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(st.rel_best) and a.aan = 1 and h.skip = 0
	GROUP BY st.schaapId
 ) hin on (hin.schaapId = s.schaapId)
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
	SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
 left join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = hin.schaapId)
 left join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = hin.schaapId)
WHERE (isnull(b.hokId) or uit.hist is not null) and isnull(spn.schaapId) and isnull(prnt.schaapId)
ORDER BY right(s.levensnummer,".mysqli_real_escape_string($db,$Karwerk).")
") or die (mysqli_error($db));

?> 
<tr style = "font-size:12px;">
 <th style = "text-align:center;" valign=bottom width= 80 > Werknr<hr></th>
 <th style = "text-align:center;" valign=bottom width= 80 > Ras<hr></th>
 <th style = "text-align:center;" valign=bottom width= 50 > Geslacht<hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> <hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> <hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> <hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> <hr></th>
</tr>
<?php
		while($row = mysqli_fetch_array($schapen_geb))
		{
		 $werknr = $row['werknr'];
		 $ras = $row['ras'];
		 $geslacht = $row['geslacht'];
?>		
<tr align = "center">	
 <td width = 80 style = "font-size:15px;"> <?php echo $werknr;?>  <br> </td>
 <td width = 80 style = "font-size:15px;"> <?php echo $ras;?> <br> </td>	   
 <td width = 50 style = "font-size:15px;"> <?php echo $geslacht;?> <br> </td>		   
 <td width = 100 style = "font-size:15px;">  <br> </td> 
 <td width = 100 style = "font-size:15px;">  <br> </td>	   
 <td width = 100 style = "font-size:15px;">  <br> </td>
 <td width = 80 style = "font-size:15px;"><br> </td>
 <td width = 120 style = "font-size:13px;" align = "left" >
	<a href='<?php echo $url; ?>UpdSchaap.php?pstschaap=<?php echo $row['schaapId']; ?>' style = "color : blue;" valign= "top"> Gegevens wijzigen </a> </td>
</tr>				

		
<?php	}	

 }

if($aanwezig2 > 0) {

	if($aanwezig1 >0) { $height_spn = 50; } else { $height_spn = 35; } /* alleen eerste blok is 35 hoog anders 50*/ ?>

<tr height = <?php echo $height_spn; ?> valign =bottom>
 <td colspan = 6><i style = "font-size : 15px;" >Aantal lammeren na spenen :  &nbsp </i><b style = "font-size:15px;"><?php echo $aanwezig2;?> </b></td>
</tr>
<?php
$schapen_spn = mysqli_query ($db,"
SELECT s.schaapId, right(s.levensnummer,".mysqli_real_escape_string($db,$Karwerk).") werknr, r.ras, s.geslacht
FROM tblSchaap s
 left join tblRas r on (r.rasId = s.rasId)
 join (
	SELECT st.schaapId, max(hisId) hisId
	FROM tblStal st 
	 join tblHistorie h on (st.stalId = h.stalId)
	 join tblActie a on (a.actId = h.actId) 
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(st.rel_best) and a.aan = 1 and h.skip = 0
	GROUP BY st.schaapId
 ) hin on (hin.schaapId = s.schaapId)
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
	SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
 join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = hin.schaapId)
 left join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = hin.schaapId)
WHERE (isnull(b.hokId) or uit.hist is not null) and isnull(prnt.schaapId)
ORDER BY right(s.levensnummer,".mysqli_real_escape_string($db,$Karwerk).")
") or die (mysqli_error($db));

?> 
<tr style = "font-size:12px;">
 <th style = "text-align:center;" valign=bottom width= 80 > Werknr<hr></th>
 <th style = "text-align:center;" valign=bottom width= 80 > Ras<hr></th>
 <th style = "text-align:center;" valign=bottom width= 50 > Geslacht<hr></th>
 <th style = "text-align:center;" valign=bottom width= 100 > <hr></th>
 <th style = "text-align:center;" valign=bottom width= 100 > <hr></th>
 <th style = "text-align:center;" valign=bottom width= 100 > <hr></th>
 <th style = "text-align:center;" valign=bottom width= 100 ></th>
 <th width=60></th>
</tr>
<?php
		while($row = mysqli_fetch_array($schapen_spn))
		{
		 $werknr = $row['werknr'];
		 $ras = $row['ras'];
		 $geslacht = $row['geslacht'];
?>		
<tr align = "center">
 <td width = 80  style = "font-size:15px;"> <?php echo $werknr;?>  <br> </td>
 <td width = 80  style = "font-size:15px;"> <?php echo $ras;?> <br> </td>	   
 <td width = 50  style = "font-size:15px;"> <?php echo $geslacht;?> <br> </td>	   
 <td width = 100 style = "font-size:15px;">  <br> </td>	   	   
 <td width = 100 style = "font-size:15px;">  <br> </td>	   
 <td width = 100 style = "font-size:15px;">  <br> </td>
 <td width = 80  style = "font-size:15px;"> <br> </td>	

	   <td width = 180 style = "font-size:13px;" align = "left" >

	   		<a href='<?php echo $url; ?>UpdSchaap.php?pstschaap=<?php echo $row['schaapId']; ?>' style = "color : blue;" valign= "top">
			Gegevens wijzigen
			</a>

	   </td>
</tr>				

		
<?php	}	} ?>
</tr>				
<!-- Einde gespeende lammeren -->

<?php

if($aanwezig3 > 0) { 

	if($aanwezig1 >0 || $aanwezig2 >0 ) { $height_prnt = 50; } else { $height_prnt = 35; } /* alleen eerste blok is 35 hoog anders 50*/ ?>
<tr height = <?php echo $height_prnt; ?> valign =bottom>
 <td colspan = 6><i style = "font-size : 15px;" >Aantal volwassen schapen :  &nbsp </i><b style = "font-size:15px;"><?php echo $aanwezig3;?> </b></td>
</tr>
<?php
$schapen_vanaf_aanwas = mysqli_query ($db,"
SELECT s.schaapId, right(s.levensnummer,".mysqli_real_escape_string($db,$Karwerk).") werknr, r.ras, s.geslacht
FROM tblSchaap s
 left join tblRas r on (r.rasId = s.rasId)
 join (
	SELECT st.schaapId, max(hisId) hisId
	FROM tblStal st 
	 join tblHistorie h on (st.stalId = h.stalId)
	 join tblActie a on (a.actId = h.actId) 
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(st.rel_best) and a.aan = 1 and h.skip = 0
	GROUP BY st.schaapId
 ) hin on (hin.schaapId = s.schaapId)
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
	SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
 join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = hin.schaapId)
WHERE (isnull(b.hokId) or uit.hist is not null)
ORDER BY right(s.levensnummer,".mysqli_real_escape_string($db,$Karwerk).")
") or die (mysqli_error($db));

?> 
<tr style = "font-size:12px;" height = 48>
 <th style = "text-align:center;" valign=bottom width= 80 > Werknr<hr></th>
 <th style = "text-align:center;" valign=bottom width= 80 > Ras<hr></th>
 <th style = "text-align:center;" valign=bottom width= 50 > Geslacht<hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> <hr></th>  
 <th style = "text-align:center;" valign=bottom width= 100 ><hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> </th>
 <th style = "text-align:center;" valign=bottom width= 80> </th>
 <th width=120></th>
</tr>
<?php
		while($row = mysqli_fetch_array($schapen_vanaf_aanwas))
		{
		 $werknr = $row['werknr'];
		 $ras = $row['ras'];
		 $geslacht = $row['geslacht'];
?>		
<tr align = "center">  
 <td width = 80 style = "font-size:15px;"> <?php echo $werknr; ?>  <br> </td>
 <td width = 80 style = "font-size:15px;"> <?php echo $ras; ?> <br> </td>	   
 <td width = 50 style = "font-size:15px;"> <?php echo $geslacht;?> <br> </td>		   
 <td width = 100 style = "font-size:15px;">  <br> </td>	      
 <td width = 100 style = "font-size:15px;">  <br> </td>	   
 <td width = 100 style = "font-size:15px;">  <br> </td>
 <td width = 80 style = "font-size:15px;"> <br> </td>
 <td width = 180 style = "font-size:13px;" align = "left" >

	   		<a href='<?php echo $url; ?>UpdSchaap.php?pstschaap=<?php echo $row['schaapId']; ?>' style = "color : blue;" valign= "top">
			Gegevens wijzigen
			</a>

	   </td>
</tr>				

		
<?php	}	}

if($aanwezig1 == 0 && $aanwezig2 == 0 && $aanwezig3 == 0) { ?>

 <tr height = 35 valign =bottom>
 <td colspan = 6><i style = "font-size : 15px;" >Aantal schapen :  &nbsp </i><b style = "font-size:15px;"><?php echo $aanwezig1;?> </b></td>
</tr>

	<tr style = "font-size:12px;">
 <th style = "text-align:center;" valign=bottom width= 80 > Werknr<hr></th>
 <th style = "text-align:center;" valign=bottom width= 80 > Ras<hr></th>
 <th style = "text-align:center;" valign=bottom width= 50 > Geslacht<hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> <hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> <hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> <hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> <hr></th>
 <th style = "text-align:center;" valign=bottom width= 120> <hr></th>
</tr>
<tr> <td height = 25></td>
</tr>
<tr> <td height = 25></td>
</tr>
<tr> <td height = 25></td>
</tr>

<?php } ?>
</tr>				
</table>

	</TD>
<?php include "menu1.php"; } ?>
</tr>

</body>
</html>
