<?php /* 11-10-2014 : Maanden gewijigd van cijfers naar omschrijving
11-3-2015 : Login toegevoegd */
$versie = '25-2-2017'/* Maandtotalen worden getoond vanaf begin van gebruik programma		3-3-2017 : Geldt enkel voor productieomgeving !!! */;
$versie = '15-9-2017'/* Som van aanwasdatum gescheiden van aanvoerdatum. Kolomkop Aanwas moeder en Aanwas gewijzigd */;
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '16-5-2021'; /* s.geslacht = 'ooi' toegevoegd in query $result_permaand, subquery aanw. Sql beveiligd met quotes */
 session_start(); ?>
<html>
<head>
<title>Rapport</title>
</head>
<body>

<center>
<?php
$titel = 'Maandtotalen';
$subtitel = '';
Include "header.php"; ?>
		<TD width = 960 height = 400 valign = "top" >
<?php
$file = "MaandTotalen.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { if($modtech ==1) { ?>

<table Border = 0 align = center>
<tr><td> </td>

<td>
<?php
// Omdat jaartal en maanden aflopend zijn moet de cumulatieven aantal ooien aflopend zijn i.p.v. oplopend. Het aantal cumulatief begint dus niet bij 0 maar bij het maximale aantal.
// query maximaal aantal ooien cumulatief
$result = mysqli_query($db,"

	SELECT sum(coalesce(aanv_m.mdrs,0) - coalesce(afv_m.mdrs,0) - coalesce(doo_m.mdrs,0)) saldo_ooi_end
	FROM (
		SELECT date_format(datum,'%Y%m') jrmnd
		FROM tblHistorie
		WHERE skip = 0
		GROUP BY date_format(datum,'%Y%m')
		) nr	
	left join (
		SELECT date_format(h.datum,'%Y%m') jrmnd, count(s.schaapId) mdrs
		FROM tblHistorie h
		 join tblStal st on (h.stalId = st.stalId)
		 join tblSchaap s on (s.schaapId = st.schaapId)
		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = 3 and s.geslacht = 'ooi' and skip = 0
		GROUP BY date_format(h.datum,'%Y%m')
	) aanv_m on (nr.jrmnd = aanv_m.jrmnd)
	left join (
		SELECT date_format(h.datum,'%Y%m') jrmnd, count(s.schaapId) mdrs
		FROM tblHistorie h
		 join tblStal st on (h.stalId = st.stalId)
		 join tblSchaap s on (s.schaapId = st.schaapId)
		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = 13 and s.geslacht = 'ooi' and skip = 0
		GROUP BY date_format(h.datum,'%Y%m')
	) afv_m on (nr.jrmnd = afv_m.jrmnd)
	left join (
		SELECT date_format(h.datum,'%Y%m') jrmnd, count(st.schaapId) mdrs
		FROM tblHistorie h
		 join tblStal st on (h.stalId = st.stalId)
		 join tblSchaap s on (s.schaapId = st.schaapId)
		 join (
			SELECT schaapId
			FROM tblStal st
			 join tblHistorie h on (st.stalId = h.stalId)
			WHERE h.actId = 3 and skip = 0
		 ) ouder on (ouder.schaapId = st.schaapId)
		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = 14
		 and s.geslacht = 'ooi' and skip = 0
		GROUP BY date_format(h.datum,'%Y%m')
	) doo_m on (nr.jrmnd = doo_m.jrmnd)

") or die (mysqli_error($db));

		while($cu = mysqli_fetch_array($result))/*	$row zorgt voor de waardes per maand 	*/
		{ $cumm = $cu['saldo_ooi_end'];  }

// Einde query maximaal aantal ooien cumulatief
$zoek_startjaar_user = mysqli_query($db,"
SELECT date_format(min(dmcreatie),'%Y') jaar 
FROM tblStal
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die (mysqli_error($db));
	while($jr1 = mysqli_fetch_array($zoek_startjaar_user)) { $jaar1 = $jr1['jaar']; }
	
	
$qry_eerstejaar_tbv_testen = mysqli_query($db,"
SELECT min(year(h.datum)) minjaar
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.datum > 0 and h.actId = 3 and s.geslacht = 'ooi' and skip = 0
") or die (mysqli_error($db));
while ($jr1 = mysqli_fetch_assoc($qry_eerstejaar_tbv_testen)) { $startjaar = $jr1['minjaar']; }
$startjaar = date("Y")-2; if($jaar1 > $startjaar && $dtb == $db_p) { $startjaar = $jaar1; } // Alleen in productieomg rapport tonen vanaf startjaar user
$endjaar = date("Y");
$endjrmnd = date("Ym");

for($j = $endjaar; $j>=$startjaar; $j--) { ?>
<tr style = "font-size:18px;" align = center><td colspan = 1></td><td><b>Jaar <?php echo $j; ?> </b></td></tr>
<tr style = "font-size:12px;">
<th width = 0 height = 30></th>
<th style = "text-align:center;"valign="bottom";width= 100>Maand<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Saldo nieuwe moeders<hr></th>
<th width = 1></th>

<th style = "text-align:center;"valign="bottom";width= 80>Totaal moeders<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Aanvoer moeders<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Moeders afgevoerd<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Moeders uitval <hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Eigen aanwas <hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Lammeren geboren<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Lammeren afgevoerd<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Lammeren uitval<hr></th>
<th width = 1></th>
<th width=60></th>
 </tr>
 <?php
$i = 1;
for($i=1;$i<13;$i++) { $m=13-$i; $jm = ($j*100)+($m);
		
 
	$mndnaam = array('','januari', 'februari', 'maart','april','mei','juni','juli','augustus','september','oktober','november','december');

$result_permaand = mysqli_query($db,"

	SELECT nr.jrmnd jm, nr.jaar, aanv_m.jrmnd, aanv_m.mdrs mdrs_aanv, afv_m.mdrs mdrs_afv, doo_m.mdrs mdrs_doo,
	 coalesce(aanw.oudrs,0) + coalesce(aanv_m.mdrs,0) - coalesce(afv_m.mdrs,0) - coalesce(doo_m.mdrs,0) saldo_ooi,
	 gebrn.aant gebrn, aanw.oudrs, afv_lam.afv afv_lam, doo_lam.lam doo_lam
	FROM (
		SELECT '".mysqli_real_escape_string($db,$jm)."' jrmnd, '".mysqli_real_escape_string($db,$j)."' jaar
		FROM dual
		WHERE '".mysqli_real_escape_string($db,$jm)."' <= '".mysqli_real_escape_string($db,$endjrmnd)."'
	) nr	
	left join (
		SELECT date_format(h.datum,'%Y%m') jrmnd, count(distinct s.schaapId) mdrs
		FROM tblHistorie h
		 join tblStal st on (h.stalId = st.stalId)
		 join tblSchaap s on (s.schaapId = st.schaapId)
		 join (
			SELECT st.schaapId, h.datum
			FROM tblStal st
			 join tblHistorie h on (st.stalId = h.stalId)
			WHERE h.actId = 3 and skip = 0
		 ) ouder on (ouder.schaapId = s.schaapId)
		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and date_format(h.datum,'%Y%m') = '".mysqli_real_escape_string($db,$jm)."' and (h.actId = 2 or h.actId = 11) and skip = 0 and s.geslacht = 'ooi' and ouder.datum <= h.datum
		GROUP BY date_format(h.datum,'%Y%m')
	) aanv_m on (nr.jrmnd = aanv_m.jrmnd)
	left join (
		SELECT date_format(h.datum,'%Y%m') jrmnd, count(distinct s.schaapId) mdrs
		FROM tblHistorie h
		 join tblStal st on (h.stalId = st.stalId)
		 join tblSchaap s on (s.schaapId = st.schaapId)
		 join (
			SELECT st.schaapId, h.datum
			FROM tblStal st
			 join tblHistorie h on (st.stalId = h.stalId)
			WHERE h.actId = 3 and skip = 0
		 ) ouder on (ouder.schaapId = s.schaapId)
		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and date_format(h.datum,'%Y%m') = '".mysqli_real_escape_string($db,$jm)."' and (h.actId = 10 or h.actId = 13) and skip = 0 and s.geslacht = 'ooi' and ouder.datum <= h.datum
		GROUP BY date_format(h.datum,'%Y%m')
	) afv_m on (nr.jrmnd = afv_m.jrmnd)
	left join (
		SELECT date_format(h.datum,'%Y%m') jrmnd, count(st.schaapId) mdrs
		FROM tblHistorie h
		 join tblStal st on (h.stalId = st.stalId)
		 join tblSchaap s on (s.schaapId = st.schaapId)
		 join (
			SELECT schaapId
			FROM tblStal st
			 join tblHistorie h on (st.stalId = h.stalId)
			WHERE h.actId = 3 and skip = 0
		 ) ouder on (ouder.schaapId = st.schaapId)
		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and date_format(h.datum,'%Y%m') = '".mysqli_real_escape_string($db,$jm)."' and h.actId = 14 and skip = 0
		 and s.geslacht = 'ooi'
		GROUP BY date_format(h.datum,'%Y%m')
	) doo_m on (nr.jrmnd = doo_m.jrmnd)
	left join (
		SELECT date_format(h.datum,'%Y%m') jrmnd, count(st.schaapId) aant
		FROM tblHistorie h
		 join tblStal st on (h.stalId = st.stalId)
		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and date_format(h.datum,'%Y%m') = '".mysqli_real_escape_string($db,$jm)."' and h.actId = 1 and skip = 0
		GROUP BY date_format(h.datum,'%Y%m')
	) gebrn on (nr.jrmnd = gebrn.jrmnd)
	left join (
		SELECT date_format(h.datum,'%Y%m') jrmnd, count(st.schaapId) oudrs
		FROM tblHistorie h
		 join tblStal st on (h.stalId = st.stalId)
		 join tblSchaap s on (s.schaapId = st.schaapId)
		 left join (
			SELECT h.stalId, datum
			FROM tblHistorie h
			 join tblStal st on (st.stalId = h.stalId)
			WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = 2 and skip = 0
		 ) aanv on (aanv.stalId = h.stalId)
		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.geslacht = 'ooi' and date_format(h.datum,'%Y%m') = '".mysqli_real_escape_string($db,$jm)."' and h.actId = 3 and skip = 0 and coalesce(aanv.datum, date_add(h.datum, INTERVAL 10 DAY)) <> h.datum
		GROUP BY date_format(h.datum,'%Y%m')
	) aanw on (nr.jrmnd = aanw.jrmnd)
	left join (
		SELECT date_format(h.datum,'%Y%m') jrmnd, count(st.schaapId) afv
		FROM tblHistorie h
		 join tblStal st on (h.stalId = st.stalId)
		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and date_format(h.datum,'%Y%m') = '".mysqli_real_escape_string($db,$jm)."' and h.actId = 12 and skip = 0
		GROUP BY date_format(h.datum,'%Y%m')
	) afv_lam on (nr.jrmnd = afv_lam.jrmnd)
	left join (
		SELECT date_format(h.datum,'%Y%m') jrmnd, count(st.schaapId) lam
		FROM tblHistorie h
		 join tblStal st on (h.stalId = st.stalId)
		 left join (
			SELECT schaapId
			FROM tblStal st
			 join tblHistorie h on (st.stalId = h.stalId)
			WHERE h.actId = 3 and skip = 0
		 ) ouder on (ouder.schaapId = st.schaapId)
		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and date_format(h.datum,'%Y%m') = '".mysqli_real_escape_string($db,$jm)."' and h.actId = 14 and skip = 0 and isnull(ouder.schaapId)
		GROUP BY date_format(h.datum,'%Y%m')
	) doo_lam on (nr.jrmnd = doo_lam.jrmnd)

	WHERE jaar = '".mysqli_real_escape_string($db,$j)."'
	ORDER BY jrmnd desc
") or die (mysqli_error($db));

	while($row = mysqli_fetch_array($result_permaand))/*	$row zorgt voor de waardes per maand 	*/
	{  
$saldo = $row['saldo_ooi'];	if(isset($cumm)) 				   { $cumm = $cumm-$saldo; } 			else { $cumm = $saldo; } 
	  						if(isset($totSaldo)   && $m != 12) { $totSaldo = $totSaldo+$saldo; }	else { $totSaldo = $saldo; }
$aanv_m = $row['mdrs_aanv']; if(isset($totAanv_m) && $m != 12) { $totAanv_m = $totAanv_m+$aanv_m; } else { $totAanv_m = $aanv_m; }
$afv_m = $row['mdrs_afv']; 	if(isset($totAfv_m)   && $m != 12) { $totAfv_m = $totAfv_m+$afv_m; } 	 else { $totAfv_m = $afv_m; }
$doo_m = $row['mdrs_doo'];	if(isset($totDoo_m)   && $m != 12) { $totDoo_m = $totDoo_m+$doo_m; } 	 else { $totDoo_m = $doo_m; }
$aanw = $row['oudrs']; 		if(isset($totAanw_m)  && $m != 12) { $totAanw_m = $totAanw_m+$aanw; } 	 else { $totAanw_m = $aanw; }
$gebrn = $row['gebrn']; 	if(isset($totGbrn)    && $m != 12) { $totGbrn = $totGbrn+$gebrn; } 	 else { $totGbrn = $gebrn; }
$afv_lam = $row['afv_lam']; if(isset($totAfv_lam) && $m != 12) { $totAfv_lam = $totAfv_lam+$afv_lam; } else { $totAfv_lam = $afv_lam; }
$doo_lam = $row['doo_lam']; if(isset($totDoo_lam) && $m != 12) { $totDoo_lam = $totDoo_lam+$doo_lam; } else { $totDoo_lam = $doo_lam; }

		?>
<tr align = center>	
	   <td width = 0> </td>	   
	   <td width = 100 style = "font-size:15px;" align = "right"> <?php echo $mndnaam[$m]; ?> <br> </td>	   
	   <td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"> <?php echo $saldo; ?> <br> </td>
	    <td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"> <?php echo $cumm; ?> <br> </td>
	    <td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"> <?php echo $aanv_m; ?> <br> </td>
	    <td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"> <?php echo $afv_m; ?> <br> </td>
	   	<td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"> <?php echo $doo_m; ?> <br> </td>
	    <td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"> <?php echo $aanw; ?> <br> </td>
	    <td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"> <?php echo $gebrn; ?> <br> </td>
	    <td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"> <?php echo $afv_lam; ?> <br> </td>
	    <td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"> <?php echo $doo_lam; ?> <br> </td>
	    <td width = 1> </td>
	   <td width = 50> </td>
</tr>				
<?php	}
	}		 

// Totalen ?>
		<tr align = center>	
	   <td width = 0> </td>	   
	   <td width = 100 style = "font-size:15px;"> <hr><b> Totaal <?php echo $j; ?> </b><br> </td>	   
	   <td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"> <hr><b> <?php echo $totSaldo; ?> </b><br> </td>
	   <td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"> <hr><b> </b><br> </td>
	    <td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"> <hr><b> <?php echo $totAanv_m; ?> </b><br> </td>
	    <td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"> <hr><b> <?php echo $totAfv_m; ?> </b><br> </td>
	    <td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"> <hr><b> <?php echo $totDoo_m; ?> </b><br> </td>
	   <td width = 1> </td>	   
	   <td width = 100 style = "font-size:15px;"> <hr><b> <?php echo $totAanw_m; ?> </b><br> </td>
	    <td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"> <hr><b> <?php echo $totGbrn; ?> </b><br> </td>
	    <td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"> <hr><b> <?php echo $totAfv_lam; ?> </b><br> </td>
	    <td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"> <hr><b> <?php echo $totDoo_lam; ?> </b><br> </td>
	    <td width = 1> </td>
	   <td width = 50> </td>
</tr>
<tr style = "height : 25px;"><td colspan = 25></td></tr><?php
// EINDE totalen




}	?>			
</table>
		</TD>
<?php } else { ?> <img src='maandTotalen_php.jpg'  width='970' height='550'/> <?php }
Include "menuRapport.php"; } ?>
</body>
</html>
