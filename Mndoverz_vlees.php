<?php /* 16-3-2014 Maandoverzicht wordt ovv Rina per jaar gekozen en getoond.
 11-10-2014 : Maanden gewijigd van cijfers naar omschrijving
11-3-2015 : Login toegevoegd */
$versie = "22-1-2017"; /* 18-1-2017 Query's aangepast n.a.v. nieuwe tblDoel		22-1-2017 tblBezetting gewijzigd naar tblBezet */
$versie = '25-2-2017'/* Maandoverzicht worden getoond vanaf begin van gebruik programma. 	3-3-2017 : Geldt enkel voor productieomgeving !!! */;
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '16-11-2019'; /* Hoeveelheid voer per maand opnieuw gebouwd i.v.m. andere manier van kg voer vastleggen */
session_start(); ?>
<html>
<head>
<title>Rapport</title>
</head>
<body>

<center>
<?php
$titel = 'Maandoverzicht vleeslammeren';
$subtitel = '';
$label = "Kies een jaartal &nbsp " ;
If (isset($_POST['knpToon']) && !empty($_POST['kzljaar'])) {	$label = ""; }
Include "header.php"; ?>
		<TD width = 960 height = 400 valign = "top" >
<?php
$file = "Mndoverz_vlees.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { if($modtech ==1) { ?>

<table Border = 0 align = "center">
<?php
$zoek_startjaar_user = mysqli_query($db,"
select date_format(min(dmcreatie),'%Y') jaar 
from tblStal
where lidId = ".mysqli_real_escape_string($db,$lidId)."
") or die (mysqli_error($db));
	while($jr1 = mysqli_fetch_array($zoek_startjaar_user)) { $jaar1 = $jr1['jaar']; }
	
$jaarstart = date("Y")-3; if($jaarstart < $jaar1 && $dtb == "bvdvSchapenDb") { $jaarstart = $jaar1; }// Alleen in productieomg rapport tonen vanaf startjaar user
$kzl = mysqli_query($db,"
select date_format(datum,'%Y') jaar 
from tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and date_format(datum,'%Y') >= '$jaarstart' and h.actId = 4
group by date_format(datum,'%Y')
order by date_format(datum,'%Y') desc 
") or die (mysqli_error($db));
?>
<form action = "Mndoverz_vlees.php" method = "post">
<tr> <td> </td>
<td> <?php
echo $label ;
//Jaar selecteren
$name = "kzljaar";
$width= 100 ; ?>
<select name=<?php echo"$name";?> style="width:<? echo "$width";?>;\" >
 <option></option>
<?php		while($row = mysqli_fetch_array($kzl))
		{
$kzlkey="$row[jaar]";
$kzlvalue="$row[jaar]";

include "kzl.php";
		}
// EINDE Jaar selecteren
?>
</select> 
 </td>
 <td> </td>
 
 <td> <input type = "submit" name ="knpToon" value = "Toon"> </td></tr>	
</form>
<tr> <td> </td>

<td>
<?php		
If (isset($_POST['knpToon']) && !empty($_POST['kzljaar'])) {
$jaar = $_POST['kzljaar'];
	

	$maand = date("m");
	$mndnaam = array('','januari', 'februari', 'maart','april','mei','juni','juli','augustus','september','oktober','november','december'); 
//$vanaf = "$jaarstart$maand";

$result = mysqli_query($db,"
select jrmnd jaarmnd, jaar, maand, speenat, afvat, doodat, Perc_naopleg, round(daggroei,2) gemgroei, round(voer,2) voer
from (
	Select aant.jrmnd, aant.maand, aant.jaar, aant.speenat, aant.afvat, 
	 naopleg.doodat, round((naopleg.doodat/aant.speenat*100),2) perc_naopleg, 
	 groei.gemgroeidag daggroei,
	 kgvoer.nutat_mnd voer
	From (
		select date_format(h.datum,'%Y%m') jrmnd, Month(h.datum) maand, year(h.datum) jaar, count(h.hisId) speenat, count(haf.hisId) afvat
		from tblHistorie h
		 join tblStal st on (st.stalId = h.stalId)
		 join tblSchaap s on (s.schaapId = st.schaapId)
		 left join tblHistorie haf on (st.stalId = haf.stalId and haf.actId = 12)
		where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and h.actId = 4 and year(h.datum) = '".mysqli_real_escape_string($db,$jaar)."'
		group by Month(h.datum), year(h.datum)
	 ) aant
	left join (
		select date_format(h.datum,'%Y%m') jrmnd, Month(h.datum) maand, Year(h.datum) jaar, count(s.schaapId) doodat
		from tblSchaap s
		 join tblStal st on (s.schaapId = st.schaapId)
		 join tblHistorie h on (st.stalId = h.stalId)
		 join tblHistorie ho on (st.stalId = ho.stalId and ho.actId = 14)
		 join tblHistorie hs on (st.stalId = hs.stalId and hs.actId = 4)
		 left join tblHistorie ha on (st.stalId = ha.stalId and ha.actId = 3)
		where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and h.actId = 4 and isnull(ha.actId) and year(h.datum) = '$jaar'
		group by month(h.datum), Year(h.datum)	
	 ) naopleg on (aant.jrmnd = naopleg.jrmnd)
	left join (
		select date_format(h.datum,'%Y%m') jrmnd, sum((haf.kg -  h.kg)*1000/ DATEDIFF(haf.datum, h.datum)) groeidag, count(st.schaapId), 
		sum((haf.kg -  h.kg)*1000/ DATEDIFF(haf.datum, h.datum)) / count(st.schaapId) gemgroeidag
		from tblSchaap s 
		 join tblStal st on (st.schaapId = s.schaapId)
		 join tblHistorie h on (st.stalId = h.stalId and h.actId = 4)
		 join tblHistorie haf on (st.stalId = haf.stalId and haf.actId = 12)
		where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and year(h.datum) = '".mysqli_real_escape_string($db,$jaar)."'
		group by Month(h.datum), Year(h.datum)
	 ) groei on (aant.jrmnd = groei.jrmnd)
	 left join (
		select gesp_jrmnd, sum(nutat_peri_mnd) nutat_mnd
		from (
			select date_format(hges.datum,'%Y%m') gesp_jrmnd, vantot.periId, dgperi.dgn_periId,
			 sum(datediff(tot.datum,van.datum)) dgn,
			 sum(datediff(tot.datum,van.datum))/dgperi.dgn_periId*100 perc_dgn,
			 v.nutat,
			 sum(datediff(tot.datum,van.datum))/dgperi.dgn_periId*v.nutat nutat_peri_mnd
			from (
				select b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist, b.periId, h1.actId
				from tblBezet b
				 join tblHistorie h1 on (b.hisId = h1.hisId)
				 join tblActie a1 on (a1.actId = h1.actId)
				 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
				 join tblActie a2 on (a2.actId = h2.actId)
				 join tblStal st on (h1.stalId = st.stalId)
				 join tblPeriode p on (b.periId = p.periId)
				 
				where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
				 and p.doelId = 2 and year(h1.datum) = '".mysqli_real_escape_string($db,$jaar)."'
				group by b.bezId, st.schaapId, h1.hisId, h1.actId
			) vantot
			 join (
				select vantot.periId, sum(datediff(tot.datum,van.datum)) dgn_periId
				from (
					select b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist, b.periId, h1.actId
					from tblBezet b
					 join tblHistorie h1 on (b.hisId = h1.hisId)
					 join tblActie a1 on (a1.actId = h1.actId)
					 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
					 join tblActie a2 on (a2.actId = h2.actId)
					 join tblStal st on (h1.stalId = st.stalId)
					 join tblPeriode p on (b.periId = p.periId)
					 
					where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
					 and p.doelId = 2
					group by b.bezId, st.schaapId, h1.hisId, h1.actId
				) vantot
				 join tblHistorie van on (van.hisId = vantot.hisv)
				 join tblHistorie tot on (tot.hisId = vantot.hist)
				group by vantot.periId
			 ) dgperi on (vantot.periId = dgperi.periId)
			 join tblHistorie van on (van.hisId = vantot.hisv)
			 join tblHistorie tot on (tot.hisId = vantot.hist)
			 join tblVoeding v on (v.periId = vantot.periId)
			 
			 join tblStal st on (st.schaapId = vantot.schaapId)
			 join tblHistorie hges on (hges.stalId = st.stalId and hges.actId = 4)
			group by date_format(hges.datum,'%Y%m'), vantot.periId, dgperi.dgn_periId, v.nutat
		) vr_mnd
		group by gesp_jrmnd
	 ) kgvoer on (aant.jrmnd = kgvoer.gesp_jrmnd)
) mv
order by jaarmnd desc
") or die (mysqli_error($db));
 ?>

<tr style = "font-size:18px;" align = "center"><td colspan = 1></td><td><b>Jaar <?php echo $jaar; ?> </b></td></tr>
<tr style = "font-size:12px;">
<th width = 0 height = 30></th>
<!--<th style = \"text-align:center;\"valign=\"bottom\";width= \"60\"></th>
<th width = \"1\"></th>-->
<th style = "text-align:center;"valign="bottom";width= 100>Speenmaand<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Aantal na opleg<hr></th>
<th width = 1></th>

<th style = "text-align:center;"valign="bottom";width= 80>uitval na opleg<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>% uitval na opleg<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Afgeleverd<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Gem Groei <hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Voer in kg <hr></th>
<th width = 1></th>
<th width=60></th>
 </tr>

<?php
		while($row = mysqli_fetch_array($result))/*	$row zorgt voor de waardes per maand 	*/
		{ $mndnr = $row['maand'];

// Kg voer per maand
$kg_per_maand = "
SELECT dagen_per_speenjaarmaand.jaarmaand, round(sum(dagen_per_speenjaarmaand.dgn*Kg_per_dag_per_periode.kgDag),2) kgMnd
FROM (

	SELECT p.periId, nutat/sum(dgn) kgDag
	FROM (

		SELECT p.periId, p.hokId, date_format(dmcreate,'%Y-%m-01') pStart, min(p.dmafsluit) pEind
		FROM tblPeriode p
		 join tblHok ho on (p.hokId = ho.hokId)
		 join tblLeden l on (ho.lidId = l.lidId)
		WHERE doelId = 2 and l.lidId = ".mysqli_real_escape_string($db,$lidId)."
		GROUP BY p.periId, p.hokId, date_format(dmcreate,'%Y-%m-01')
		union

		SELECT p2.periId, p2.hokId, max(p1.dmafsluit) pStart, p2.dmafsluit pEind
		FROM tblPeriode p1
		 join tblPeriode p2 on (p1.hokId = p2.hokId and p1.doelId = p2.doelId and p1.dmafsluit < p2.dmafsluit)
		 join tblHok ho on (p1.hokId = ho.hokId)
		WHERE p1.doelId = 2 and ho.lidId = ".mysqli_real_escape_string($db,$lidId)."
		GROUP BY p2.periId, p2.hokId, p2.dmafsluit
	 ) p
	 left join (
	 	SELECT p.periId, sum(nutat) nutat
	 	FROM tblVoeding v
	 	 join tblPeriode p on (v.periId = p.periId)
	 	 join tblHok ho on (ho.hokId = p.hokId)
	 	WHERE lidId = ".mysqli_real_escape_string($db,$lidId)."
	 	GROUP BY p.periId
	 ) v on (p.periId = v.periId)
	 join (
		SELECT b.hokId, st.schaapId, hv.datum schpIn, ht.datum schpUit, datediff(coalesce(ht.datum,CURDATE()),hv.datum) dgn
		FROM tblBezet b
		 join tblHistorie hv on (b.hisId = hv.hisId)
		 join tblStal st on (hv.stalId = st.stalId)
		 left join (
			SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
			FROM tblBezet b
			 join tblHistorie h1 on (b.hisId = h1.hisId)
			 join tblActie a1 on (a1.actId = h1.actId)
			 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
			 join tblActie a2 on (a2.actId = h2.actId)
			 join tblStal st on (h1.stalId = st.stalId)
			WHERE a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and st.lidId = ".mysqli_real_escape_string($db,$lidId)."
			GROUP BY b.bezId, st.schaapId, h1.hisId
		 ) uit on (uit.hisv = b.hisId)
		 left join tblHistorie ht on (uit.hist = ht.hisId)
		WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)."
	 ) s on (p.hokId = s.hokId)
	 join (
		SELECT st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 4 and st.lidId = ".mysqli_real_escape_string($db,$lidId)."
	 ) spn on (spn.schaapId = s.schaapId)
	  left join (
		SELECT st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 3 and st.lidId = ".mysqli_real_escape_string($db,$lidId)."
	 ) prn on (prn.schaapId = s.schaapId)

	WHERE schpIn < pEind and schpUit > pStart and schpIn >= spn.datum and (schpIn < prn.datum or isnull(prn.schaapId))

	GROUP BY p.periId, v.nutat

 ) Kg_per_dag_per_periode

 join 

 (
	SELECT p.periId, date_format(spn.datum,'%Y%m') jaarmaand, sum(s.dgn) dgn
	FROM (

		SELECT p.periId, p.hokId, date_format(dmcreate,'%Y-%m-01') pStart, min(p.dmafsluit) pEind
		FROM tblPeriode p
		 join tblHok ho on (p.hokId = ho.hokId)
		 join tblLeden l on (ho.lidId = l.lidId)
		WHERE doelId = 2 and l.lidId = ".mysqli_real_escape_string($db,$lidId)."
		GROUP BY p.periId, p.hokId
		union

		SELECT p2.periId, p2.hokId, max(p1.dmafsluit) pStart, p2.dmafsluit pEind
		FROM tblPeriode p1
		 join tblPeriode p2 on (p1.hokId = p2.hokId and p1.doelId = p2.doelId and p1.dmafsluit < p2.dmafsluit)
		 join tblHok ho on (p1.hokId = ho.hokId)
		WHERE p1.doelId = 2 and ho.lidId = ".mysqli_real_escape_string($db,$lidId)."
		GROUP BY p2.periId, p2.hokId, p2.dmafsluit
	 ) p
	 join (
		SELECT b.hokId, st.schaapId, hv.datum schpIn, ht.datum schpUit, datediff(coalesce(ht.datum,CURDATE()),hv.datum) dgn
		FROM tblBezet b
		 join tblHistorie hv on (b.hisId = hv.hisId)
		 join tblStal st on (hv.stalId = st.stalId)
		 left join (
			SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
			FROM tblBezet b
			 join tblHistorie h1 on (b.hisId = h1.hisId)
			 join tblActie a1 on (a1.actId = h1.actId)
			 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
			 join tblActie a2 on (a2.actId = h2.actId)
			 join tblStal st on (h1.stalId = st.stalId)
			WHERE a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and st.lidId = ".mysqli_real_escape_string($db,$lidId)."
			GROUP BY b.bezId, st.schaapId, h1.hisId
		 ) uit on (uit.hisv = b.hisId)
		 left join tblHistorie ht on (uit.hist = ht.hisId)
		WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)."
	 ) s on (p.hokId = s.hokId)
	 join (
		SELECT st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 4 and st.lidId = ".mysqli_real_escape_string($db,$lidId)." and date_format(h.datum,'%Y') = ".mysqli_real_escape_string($db,$jaar)." and Month(h.datum) = ".mysqli_real_escape_string($db,$mndnr)."
	 ) spn on (spn.schaapId = s.schaapId)
	 left join (
		SELECT st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 3 and st.lidId = ".mysqli_real_escape_string($db,$lidId)."
	 ) prn on (prn.schaapId = s.schaapId)

	WHERE schpIn < pEind and schpUit > pStart and schpIn >= spn.datum and (schpIn < prn.datum or isnull(prn.schaapId))

	GROUP BY p.periId, date_format(spn.datum,'%Y%m')

 ) dagen_per_speenjaarmaand on (Kg_per_dag_per_periode.periId = dagen_per_speenjaarmaand.periId)

 GROUP BY dagen_per_speenjaarmaand.jaarmaand
";

#echo $kg_per_maand.'<br><br><br>';

$kg_per_maand = mysqli_query($db,$kg_per_maand) or die (mysqli_error($db));

while($kgd = mysqli_fetch_array($kg_per_maand)) { $mndkg = $kgd['kgMnd']; }
// Einde Kg voer per Maand
?>		
<tr align = center>
 <td width = 0> </td>	   
 <td width = 100 style = "font-size:15px;" align = "right"> <?php echo $mndnaam[$mndnr]; ?> <br> </td>	

 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $row['speenat']; ?> <br> </td>
<?php	if(isset($totSpeen)) {$totSpeen = $totSpeen+$row['speenat']; } else { $totSpeen = $row['speenat']; } ?>

 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $row['doodat']; ?> <br> </td>
<?php	if(isset($totDood)) {$totDood = $totDood+$row['doodat']; } else { $totDood = $row['doodat']; } ?>

 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $row['Perc_naopleg']; ?> <br> </td>
<?php	if(isset($totOpleg)) {$totOpleg = $totOpleg+$row['Perc_naopleg']; } else { $totOpleg = $row['Perc_naopleg']; } ?>

 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $row['afvat']; ?> <br> </td>
<?php	if(isset($totAfv)) {$totAfv = $totAfv+$row['afvat']; } else { $totAfv = $row['afvat']; } ?>

 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $row['gemgroei']; ?> <br> </td>
<?php	if(isset($totGroei)) {$totGroei = $totGroei+$row['gemgroei']; } else { $totGroei = $row['gemgroei']; } ?>

 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $mndkg; ?> <br> </td>
<?php	if(isset($totKg)) {$totKg = $totKg+$mndkg; } else { $totKg = $mndkg; } unset($mndkg); ?>

 <td width = 1> </td>
 <td width = 50> </td>
</tr>				
<?php		} 
		

// totalen ?>
<tr align = "center">
 <td width = 0> </td>
 <td width = 100 style = "font-size:15px;"> <hr /><b> Totaal <?php echo $jaar; ?> </b><br> </td>	   
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <hr /><b> <?php echo $totSpeen; ?> </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <hr /><b> <?php echo $totDood; ?>  </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <hr /><b>  </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <hr /><b> <?php echo $totAfv; ?>  </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <hr /><b>  </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <hr /><b> <?php echo $totKg; ?>  </b><br> </td>
 <td width = 1> </td>
 <td width = 50> </td>
</tr> <?php
// EINDE totalen

// Gemiddelden 
$zoek_aantal_maanden = mysqli_query($db,"
SELECT count(distinct(month(h.datum))) mndat
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and h.actId = 4 and year(h.datum) = '".mysqli_real_escape_string($db,$jaar)."'
") or die (mysqli_error($db));
	while($rij = mysqli_fetch_array($zoek_aantal_maanden)) { $mndat = $rij['mndat']; }

if($mndat > 0)	{ ?>
<tr align = "center"  style = "font-size:13px;">
 <td width = 0> </td>
 <td width = 100>  Gem <?php echo $mndat; ?>Mnd </td>	   
 <td width = 1> </td>
 <td width = 100> <?php $gemSpeen = round($totSpeen/$mndat,2); if($gemSpeen>0) { echo $gemSpeen; } ?> <br> </td>
 <td width = 1> </td>
 <td width = 100> <?php $gemDood = round($totDood/$mndat,2); if($gemDood>0) { echo $gemDood; } ?> <br> </td>
 <td width = 1> </td>
 <td width = 100> <?php $gemOpleg = round($totOpleg/$mndat,2); if($gemOpleg>0) { echo $gemOpleg; } ?> </td>
 <td width = 1> </td>
 <td width = 100> <?php $gemAfv = round($totAfv/$mndat,2); if($gemAfv>0) { echo $gemAfv; } ?> <br> </td>
 <td width = 1> </td>
 <td width = 100> <?php $gemGroei = round($totGroei/$mndat,2); if($gemGroei>0) { echo $gemGroei; } ?> </td>
 <td width = 1> </td>
 <td width = 100> <?php $gemKg = round($totKg/$mndat,2); if($gemKg>0) { echo $gemKg; } ?> <br> </td>
 <td width = 1> </td>
 <td width = 50> </td>
</tr> <?php }
// EINDE Gemiddelden ?>


<tr><td></td><td></td><td align = "center">
<br/>
</td></tr>
<?php 

} //  Einde knop toon ?>
</tr>				
</table>
		</TD>
<?php } else { ?> <img src='mndoverz_vlees_php.jpg'  width='970' height='550'/> <?php }
Include "menuRapport.php"; } ?>
</body>
</html>