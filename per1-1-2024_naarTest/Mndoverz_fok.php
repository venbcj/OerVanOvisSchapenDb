<?php /* 25-2-2014 Maandoverzicht wordt ovv Rina per jaar gekozen en getoond.
 11-10-2014 : Maanden gewijigd van cijfers naar omschrijving
11-3-2015 : Login toegevoegd */
$versie = '25-11-2016'; /*25-11-2016 : query m.b.t. totalen verwijderd en gebaseerd op de query met loop per maand */
$versie = "13-2-2017"; /* 18-1-2017 Query's aangepast n.a.v. nieuwe DoelId		22-1-2017 tblBezetting gewijzigd naar tblBezet 	13-2-2017  mdrId verwezen naar tblVolwas. Gem groei niet deelbaar door 0 */
$versie = "3-3-2017"; /* Rapport wordt getoond per ingangsjaar user. Dit geldt enkel voor de productie omgeving !!! */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '16-11-2019'; /* Hoeveelheid voer per maand opnieuw gebouwd i.v.m. andere manier van kg voer vastleggen */
$versie = '27-03-2022'; /* Detail uitval voor spenen toegevoegd en sql beveiligd met quotes */
$versie = '31-12-2023'; /* and h.skip = 0 aangevuld bij tblHistorie en ook sub-queries gespeenden en geboren herschreven */
session_start(); ?>
<html>
<head>
<title>Rapport</title>
</head>
<body>

<center>
<?php
$titel = 'Maandoverzicht fokkerij';
$subtitel = ''; 

Include "header.php"; ?>
		<TD width = 960 height = 400 valign = "top" >
<?php
$file = "Mndoverz_fok.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { if($modtech ==1) { 

if (isset($_GET['jaar'])) { $kzlJaar = $_GET['jaar']; }	elseif (isset($_POST['kzlJaar'])) { $kzlJaar = $_POST['kzlJaar']; }
if (isset($_GET['maand'])) { $keuze_mnd = $_GET['maand']; }

$label = "Kies een jaartal &nbsp " ;
if (isset($kzlJaar)) { unset($label); } ?>

<table border = 0 align = center>
<?php
$zoek_startjaar_user = mysqli_query($db,"
SELECT date_format(min(dmcreatie),'%Y') jaar 
FROM tblStal
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die (mysqli_error($db));
	while($jr1 = mysqli_fetch_array($zoek_startjaar_user)) { $jaar1 = $jr1['jaar']; }

$jaarstart = date("Y")-3; if($jaar1 > $jaarstart && $dtb == "bvdvSchapenDb") { $jaarstart = $jaar1; }  // Alleen in productieomg rapport tonen vanaf startjaar user
$kzl = mysqli_query($db,"
SELECT date_format(h.datum,'%Y') jaar 
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 join tblVolwas v on (v.volwId = s.volwId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and date_format(h.datum,'%Y') >= '$jaarstart' and h.actId = 1 and h.skip = 0 and v.mdrId is not null
GROUP BY date_format(h.datum,'%Y')
ORDER BY date_format(h.datum,'%Y') desc 
") or die (mysqli_error($db));
?>
<form action = "Mndoverz_fok.php" method = "post">
<tr>
 <td> </td>
 <td> <?php
if(isset($label)) { echo $label; }
//Jaar selecteren
$kzlId = $kzlJaar;
$name = "kzlJaar";
$width= 100 ; ?>
<select name=<?php echo"$name";?> style="width:<? echo "$width";?>;\" >
 <option></option>
<?php		while($row = mysqli_fetch_array($kzl))
		{
$kzlkey= $row['jaar'];
$kzlvalue= $row['jaar'];

include "kzl.php";
		}
// EINDE Jaar selecteren
?>
</select> 
 </td>
 <td> </td>
 
 <td> <input type = "submit" name ="knpToon" value = "Toon"> </td></tr>	
</form>
<tr>
 <td> </td>

<td>	
<?php
if (isset($kzlJaar)) {

	$mndnaam = array('','januari', 'februari', 'maart','april','mei','juni','juli','augustus','september','oktober','november','december');

$result = mysqli_query($db,"
SELECT aant.maand, aant.jaar, aant.worpat, aant.gebaant, aant.gemworp, aant.levnrat, aant.speenat, 
 dgeb.doodgeb, round((dgeb.doodgeb/aant.gebaant*100),2) perc_doodgeb, 
 odgeb.onvdoodgeb, round((odgeb.onvdoodgeb/aant.gebaant*100),2) perc_onvdood, 
 merk.vrmerk, round((merk.vrmerk/aant.gebaant*100),2) perc_vrmerk,
 do_spn.vrspeen d_vrspeen, round((do_spn.vrspeen/aant.gebaant*100),2) perc_vrspeen,
 groei.gemgroeidag daggroei,
 kgvoer.voer
FROM (
	SELECT date_format(h.datum,'%Y%m') jrmnd, Month(h.datum) maand, year(h.datum) jaar, count(distinct v.mdrId) worpat, count(h.hisId) gebaant, 
	 round((count(h.hisId) / count(distinct v.mdrId)),2) gemworp, count(distinct s.levensnummer) levnrat, count(spn.hisId) speenat
	FROM tblHistorie h
	 join tblStal st on (st.stalId = h.stalId)
	 join tblSchaap s on (s.schaapId = st.schaapId)
	 join tblVolwas v on (v.volwId = s.volwId)
	 left join (
		SELECT st.schaapId, h.hisId
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 4 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	 ) spn on (spn.schaapId = s.schaapId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = 1 and h.skip = 0 and year(h.datum) = '".mysqli_real_escape_string($db,$kzlJaar)."' and v.mdrId is not null
	GROUP BY Month(h.datum), year(h.datum)
 ) aant
 left join (
	SELECT date_format(h.datum,'%Y%m') jrmnd, Month(h.datum) maand, Year(h.datum) jaar, count(distinct s.schaapId) doodgeb
	FROM tblSchaap s
	 join tblVolwas v on (v.volwId = s.volwId)
	 join tblStal st on (s.schaapId = st.schaapId)
	 join tblHistorie h on (st.stalId = h.stalId)
	 join tblHistorie ho on (st.stalId = ho.stalId and ho.actId = 14)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = 1 and h.skip = 0 and h.datum = ho.datum and (isnull(s.momId) or s.momId = 1) 
	 and year(h.datum) = '".mysqli_real_escape_string($db,$kzlJaar)."' and v.mdrId is not null
	GROUP BY month(h.datum), Year(h.datum)
 ) dgeb on (aant.jrmnd = dgeb.jrmnd)
 left join (
	SELECT date_format(h.datum,'%Y%m') jrmnd, Month(h.datum) maand, Year(h.datum) jaar, count(distinct s.schaapId) onvdoodgeb
		FROM tblSchaap s
		 join tblVolwas v on (v.volwId = s.volwId)
		 join tblStal st on (s.schaapId = st.schaapId)
		 join tblHistorie h on (st.stalId = h.stalId)
		 join tblHistorie ho on (st.stalId = ho.stalId and ho.actId = 14)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = 1 and h.skip = 0 and h.datum = ho.datum and s.momId = 2 and year(h.datum) = '".mysqli_real_escape_string($db,$kzlJaar)."' and v.mdrId is not null
	GROUP BY month(h.datum), Year(h.datum)
 ) odgeb on (aant.jrmnd = odgeb.jrmnd)
 left join (
	SELECT date_format(h.datum,'%Y%m') jrmnd, Month(h.datum) maand, Year(h.datum) jaar, count(distinct s.schaapId) vrmerk
		FROM tblSchaap s
		 join tblVolwas v on (v.volwId = s.volwId)
		 join tblStal st on (s.schaapId = st.schaapId)
		 join tblHistorie h on (st.stalId = h.stalId)
		 join tblHistorie ho on (st.stalId = ho.stalId and ho.actId = 14)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = 1 and h.skip = 0 and h.datum < ho.datum and isnull(s.levensnummer) and year(h.datum) = '".mysqli_real_escape_string($db,$kzlJaar)."' and v.mdrId is not null
	GROUP BY month(h.datum), Year(h.datum)
 ) merk on (aant.jrmnd = merk.jrmnd)
 left join (
	SELECT date_format(h.datum,'%Y%m') jrmnd, Month(h.datum) maand, Year(h.datum) jaar, count(distinct s.schaapId) vrspeen
		FROM tblSchaap s
		 join tblVolwas v on (v.volwId = s.volwId)
		 join tblStal st on (s.schaapId = st.schaapId)
		 join tblHistorie h on (st.stalId = h.stalId)
		 join tblHistorie ho on (st.stalId = ho.stalId and ho.actId = 14)
		 left join (
			SELECT st.schaapId, h.hisId
			FROM tblStal st
			 join tblHistorie h on (st.stalId = h.stalId)
			WHERE h.actId = 4 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
		 ) spn on (spn.schaapId = s.schaapId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = 1 and h.skip = 0 and isnull(spn.hisId) and s.levensnummer is not null and year(h.datum) = '".mysqli_real_escape_string($db,$kzlJaar)."' and v.mdrId is not null
	GROUP BY month(h.datum), Year(h.datum)
 ) do_spn on (aant.jrmnd = do_spn.jrmnd)
 left join (
	SELECT date_format(h.datum,'%Y%m') jrmnd, sum((spn.kg -  h.kg)*1000/ DATEDIFF(spn.datum, h.datum)) groeidag, count(distinct st.schaapId), 
	round(sum((spn.kg -  h.kg)*1000/ DATEDIFF(spn.datum, h.datum)) / count(st.schaapId),2) gemgroeidag
	FROM tblSchaap s 
	 join tblVolwas v on (v.volwId = s.volwId)
	 join tblStal st on (st.schaapId = s.schaapId)
	 join tblHistorie h on (st.stalId = h.stalId and h.actId = 1)
	 join (
		SELECT st.schaapId, h.datum, h.kg
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 4 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	 ) spn on (spn.schaapId = s.schaapId)
	WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and year(h.datum) = '".mysqli_real_escape_string($db,$kzlJaar)."' and v.mdrId is not null
	GROUP BY Month(h.datum), Year(h.datum) 
 ) groei on (aant.jrmnd = groei.jrmnd)
 left join (
	SELECT geb_jrmnd, round(sum(nutat_peri_mnd),2) voer
	FROM (
		SELECT date_format(hgeb.datum,'%Y%m') geb_jrmnd, sum(datediff(tot.datum,van.datum))/dgperi.dgn_periId*v.nutat nutat_peri_mnd
		FROM (
			SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist, p.periId, h1.actId
			FROM tblBezet b
			 join tblHistorie h1 on (b.hisId = h1.hisId)
			 join tblActie a1 on (a1.actId = h1.actId)
			 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
			 join tblActie a2 on (a2.actId = h2.actId)
			 join tblStal st on (h1.stalId = st.stalId)
			 join tblPeriode p on (b.hokId = p.hokId)
			 
			WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
			 and p.doelId = 1 and year(h1.datum) = '".mysqli_real_escape_string($db,$kzlJaar)."'
			GROUP BY b.bezId, st.schaapId, h1.hisId, h1.actId
		) vantot
		 join (
			SELECT vantot.periId, sum(datediff(tot.datum,van.datum)) dgn_periId
			FROM (
				SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist, p.periId, h1.actId
				FROM tblBezet b
				 join tblHistorie h1 on (b.hisId = h1.hisId)
				 join tblActie a1 on (a1.actId = h1.actId)
				 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
				 join tblActie a2 on (a2.actId = h2.actId)
				 join tblStal st on (h1.stalId = st.stalId)
				 join tblPeriode p on (b.hokId = p.hokId)
				 
				WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
				 and p.doelId = 1
				GROUP BY b.bezId, st.schaapId, h1.hisId, h1.actId
			) vantot
			 join tblHistorie van on (van.hisId = vantot.hisv)
			 join tblHistorie tot on (tot.hisId = vantot.hist)
			GROUP BY vantot.periId
		 ) dgperi on (vantot.periId = dgperi.periId)
		 join tblHistorie van on (van.hisId = vantot.hisv)
		 join tblHistorie tot on (tot.hisId = vantot.hist)
		 join tblVoeding v on (v.periId = vantot.periId)
		 
		 join tblStal st on (st.schaapId = vantot.schaapId)
		 join (
			SELECT st.schaapId, h.datum
			FROM tblStal st
			 join tblHistorie h on (st.stalId = h.stalId)
			WHERE h.actId = 1 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
		 ) hgeb on (hgeb.schaapId = vantot.schaapId)
		GROUP BY date_format(hgeb.datum,'%Y%m'), vantot.periId, dgperi.dgn_periId, v.nutat
	) kgvr
	GROUP BY geb_jrmnd
 ) kgvoer on (aant.jrmnd = kgvoer.geb_jrmnd)
ORDER BY aant.maand desc
") or die (mysqli_error($db));
?>

</td>
<tr style = "font-size:12px;">
 <th width = 0 height = 30></th>
 <th style = "text-align:center;" valign= "bottom" width= 150><h3>Jaar</h3> <br/>  Maand<hr></th>
 <th width = 1></th>

 <th style = "text-align:center;"valign= "bottom";width= 80><h3><?php echo $kzlJaar; ?> </h3> Aantal worpen <hr></th>

 <th width = 1></th>
 <th style = "text-align:center;"valign= "bottom" width= 80>Aantal Geboren<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom"width= 80>Gem. worp<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom"width= 80>Volledig dood geboren<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom"width= 80>% Volledig dood geboren<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom"width= 60>Onvolledig dood geboren<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom"width= 80>% Onvol- ledig dood geboren<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom"width= 80>uitval voor merken<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom"width= 80>% uitval voor merken<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom"width= 80>Aantal levens- nummers<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom"width= 80>uitval voor spenen<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom"width= 80>% uitval voor spenen<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom"width= 80>Aantal gespeend<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;" valign="bottom"width= 80>Gem groei per dag<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;" valign="bottom"width= 80>Kg voer<hr></th>
 <th width = 1></th>
 <th width=60></th>
</tr>

<?php
		while($row = mysqli_fetch_array($result))/*	$row zorgt voor de waardes per maand 	*/
		{ $mndnr = $row['maand'];

// Kg voer per maand
$kg_per_maand = mysqli_query($db,"
SELECT dagen_per_geboortejaarmaand.jaarmaand, round(sum(dagen_per_geboortejaarmaand.dgn*Kg_per_dag_per_periode.kgDag),2) kgMnd
FROM (

	SELECT p.periId, nutat/sum(dgn) kgDag
	FROM (

		SELECT p.periId, p.hokId, date_format(p.dmcreate,'%Y-%m-01') pStart, min(p.dmafsluit) pEind
		FROM tblPeriode p
		 join tblHok ho on (p.hokId = ho.hokId)
		 join tblLeden l on (ho.lidId = l.lidId)
		WHERE doelId = 1 and l.lidId = '".mysqli_real_escape_string($db,$lidId)."'
		GROUP BY p.periId, p.hokId, date_format(p.dmcreate,'%Y-%m-01')
		union

		SELECT p2.periId, p2.hokId, max(p1.dmafsluit) pStart, p2.dmafsluit pEind
		FROM tblPeriode p1
		 join tblPeriode p2 on (p1.hokId = p2.hokId and p1.doelId = p2.doelId and p1.dmafsluit < p2.dmafsluit)
		 join tblHok ho on (p1.hokId = ho.hokId)
		WHERE p1.doelId = 1 and ho.lidId = '".mysqli_real_escape_string($db,$lidId)."'
		GROUP BY p2.periId, p2.hokId, p2.dmafsluit
	 ) p
	 left join (
	 	SELECT p.periId, sum(nutat) nutat
	 	FROM tblVoeding v
	 	 join tblPeriode p on (v.periId = p.periId)
	 	 join tblHok ho on (ho.hokId = p.hokId)
	 	WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
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
			WHERE a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
			GROUP BY b.bezId, st.schaapId, h1.hisId
		 ) uit on (uit.hisv = b.hisId)
		 left join tblHistorie ht on (uit.hist = ht.hisId)
		WHERE hv.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	 ) s on (p.hokId = s.hokId)
	 left join (
		SELECT st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 4 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	 ) spn on (spn.schaapId = s.schaapId)
	  left join (
		SELECT st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 3 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	 ) prn on (prn.schaapId = s.schaapId)

	WHERE schpIn < pEind and schpUit > pStart and ( schpIn < spn.datum or (isnull(spn.schaapId) and isnull(prn.schaapId)) )

	GROUP BY p.periId, v.nutat

 ) Kg_per_dag_per_periode

 join 

 (
	SELECT p.periId, date_format(geb.datum,'%Y%m') jaarmaand, sum(s.dgn) dgn
	FROM (

		SELECT p.periId, p.hokId, date_format(p.dmcreate,'%Y-%m-01') pStart, min(p.dmafsluit) pEind
		FROM tblPeriode p
		 join tblHok ho on (p.hokId = ho.hokId)
		 join tblLeden l on (ho.lidId = l.lidId)
		WHERE doelId = 1 and l.lidId = '".mysqli_real_escape_string($db,$lidId)."'
		GROUP BY p.periId, p.hokId
		union

		SELECT p2.periId, p2.hokId, max(p1.dmafsluit) pStart, p2.dmafsluit pEind
		FROM tblPeriode p1
		 join tblPeriode p2 on (p1.hokId = p2.hokId and p1.doelId = p2.doelId and p1.dmafsluit < p2.dmafsluit)
		 join tblHok ho on (p1.hokId = ho.hokId)
		WHERE p1.doelId = 1 and ho.lidId = '".mysqli_real_escape_string($db,$lidId)."'
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
			WHERE a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
			GROUP BY b.bezId, st.schaapId, h1.hisId
		 ) uit on (uit.hisv = b.hisId)
		 left join tblHistorie ht on (uit.hist = ht.hisId)
		WHERE hv.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	 ) s on (p.hokId = s.hokId)
	 left join (
		SELECT st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 4 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	 ) spn on (spn.schaapId = s.schaapId)
	 left join (
		SELECT st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 3 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	 ) prn on (prn.schaapId = s.schaapId)
	 join (
		SELECT st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 1 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and date_format(h.datum,'%Y') = '".mysqli_real_escape_string($db,$kzlJaar)."' and Month(h.datum) = '".mysqli_real_escape_string($db,$mndnr)."'
	 ) geb on (geb.schaapId = s.schaapId)

	WHERE schpIn < pEind and schpUit > pStart and ( schpIn < spn.datum or (isnull(spn.schaapId) and isnull(prn.schaapId)) )

	GROUP BY p.periId, date_format(geb.datum,'%Y%m')

 ) dagen_per_geboortejaarmaand on (Kg_per_dag_per_periode.periId = dagen_per_geboortejaarmaand.periId)

 GROUP BY dagen_per_geboortejaarmaand.jaarmaand
") or die (mysqli_error($db));

while($kgd = mysqli_fetch_array($kg_per_maand)) { $mndkg = $kgd['kgMnd']; }
// Einde Kg voer per Maand
?>		
<tr align = center>	
 <td width = 0> </td>	   
 <td width = 250 style = "font-size:15px;" align = "right"> <?php echo $mndnaam[$mndnr]; ?> <br> </td>	   
 <td width = 1> </td>
 <td width = 80 style = "font-size:15px;"> <?php echo $row['worpat']; ?> <br> </td>
<?php	if(isset($totWorp)) {$totWorp = $totWorp+$row['worpat']; } else { $totWorp = $row['worpat']; } ?>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $row['gebaant']; ?> <br> </td>
<?php	if(isset($totGeb)) {$totGeb = $totGeb+$row['gebaant']; } else { $totGeb = $row['gebaant']; } ?>
 <td width = 1> </td>
 <td width = 80 style = "font-size:15px;"> <?php echo $row['gemworp']; ?> <br> </td>
<?php	if(isset($gemWorp)) {$gemWorp = $gemWorp+$row['gemworp']; } else { $gemWorp = $row['gemworp']; } ?>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $row['doodgeb']; ?> <br> </td>
<?php	if(isset($totDood)) {$totDood = $totDood+$row['doodgeb']; } else { $totDood = $row['doodgeb']; } ?>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $row['perc_doodgeb']; ?> <br> </td>
 <td width = 1> </td>
 <td width = 60 style = "font-size:15px;"> <?php echo $row['onvdoodgeb']; ?> <br> </td>
<?php	if(isset($totOndood)) {$totOndood = $totOndood+$row['onvdoodgeb']; } else { $totOndood = $row['onvdoodgeb']; } ?>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $row['perc_onvdood']; ?> <br> </td>
 <td width = 1> </td>
 <td width = 60 style = "font-size:15px;"> <?php echo $row['vrmerk']; ?> <br> </td>
<?php	if(isset($totVrmerk)) {$totVrmerk = $totVrmerk+$row['vrmerk']; } else { $totVrmerk = $row['vrmerk']; } ?>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $row['perc_vrmerk']; ?> <br> </td>
 <td width = 1> </td>	   
 <td width = 80 style = "font-size:15px;"> <?php echo $row['levnrat']; ?> <br> </td>
<?php	if(isset($totLevnr)) {$totLevnr = $totLevnr+$row['levnrat']; } else { $totLevnr = $row['levnrat']; } ?>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> 

 	<a href='<?php echo $url.'Mndoverz_fok.php?jaar='.$kzlJaar.'&maand='.$mndnr; ?>' style = "color : blue ;">
<?php echo $row['d_vrspeen']; ?> </a>

 <br> </td>
<?php	if(isset($totVrspn)) {$totVrspn = $totVrspn+$row['d_vrspeen']; } else { $totVrspn = $row['d_vrspeen']; } ?>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $row['perc_vrspeen']; ?> <br> </td>
 <td width = 1> </td>	   
 <td width = 80 style = "font-size:15px;"> <?php echo $row['speenat']; ?> <br> </td>
<?php	if(isset($totSpnat)) {$totSpnat = $totSpnat+$row['speenat']; } else { $totSpnat = $row['speenat']; } ?>
 <td width = 1> </td>	   
 <td width = 80 style = "font-size:15px;"> <?php echo $row['daggroei']; ?> <br> </td>
<?php	if(isset($totGroei)) {$totGroei = $totGroei+$row['daggroei']; } else { $totGroei = $row['daggroei']; } ?>
 <td width = 1> </td>	   
 <td width = 80 style = "font-size:15px;"> <?php echo $mndkg; ?> <br> </td>
<?php	if(isset($totVoer)) {$totVoer = $totVoer+$mndkg; } else { $totVoer = $mndkg; } ?>
 <td width = 1> </td>
 <td width = 50> </td>
</tr>				
<?php	} 


// totalen ?>
<tr align = "center">	
 <td width = 0> </td>
 <td width = 250 style = "font-size:15px;"> <hr /><b> Totaal&nbsp <?php echo $kzlJaar; ?> </b> </td>
 <td width = 1> </td>
 <td width = 80 style = "font-size:15px;"> <hr /><b> <?php echo $totWorp; ?> </b> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <hr /><b> <?php echo $totGeb; ?> </b> </td>
 <td width = 1> </td>
 <td width = 80 style = "font-size:15px;"> <hr /><b> </b><br> </td>
 <td width = 1> </td>
<?php	
if (!isset($totDood))
{	$totDood = "<br>";	} ?>
 <td width = 100 style = "font-size:15px;"> <hr /><b> <?php echo $totDood; ?> </b> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <hr /><b>  </b><br> </td>
 <td width = 1> </td>
<?php if(!isset($totOndood)) {	$totOndood = "<br>";	} ?>
 <td width = 60 style = "font-size:15px;"> <hr /><b> <?php echo $totOndood; ?> </b> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <hr /><b>  </b><br> </td>
 <td width = 1> </td>
<?php if(!isset($totVrmerk)) {	$totVrmerk = "<br>";	} ?>
 <td width = 60 style = "font-size:15px;"> <hr /><b> <?php echo $totVrmerk; ?> </b> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <hr /><b>  </b><br> </td>
 <td width = 1> </td>	   
 <td width = 80 style = "font-size:15px;"> <hr /><b> <?php echo $totLevnr; ?> </b> </td>
 <td width = 1> </td>
<?php if(!isset($totVrspn)){	$totVrspn = "<br>";	} ?>
 <td width = 100 style = "font-size:15px;"> <hr /><b> <?php echo $totVrspn; ?> </b> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <hr /><b>  </b><br> </td>
 <td width = 1> </td>	   
 <td width = 80 style = "font-size:15px;"> <hr /><b> <?php echo $totSpnat; ?> </b> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <hr /><b> </b><br> </td>
 <td width = 1> </td> 
<?php if(!isset($totVoer)) {	$totVoer = "<br>";	} ?>
 <td width = 80 style = "font-size:15px;"> <hr/><b> <?php echo $totVoer; ?> </b> </td>
 <td width = 1> </td>
 <td width = 50> </td>
</tr>
<?php // EINDE totalen

// Gemiddelden
$zoek_aantal_maanden = mysqli_query($db,"
SELECT count(distinct(month(h.datum))) mndat
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 join tblVolwas v on (v.volwId = s.volwId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = 1 and h.skip = 0 and year(h.datum) = '".mysqli_real_escape_string($db,$kzlJaar)."' and v.mdrId is not null
") or die (mysqli_error($db));
	while($rij = mysqli_fetch_array($zoek_aantal_maanden)) { $mndat = $rij['mndat']; }
	
$zoek_aantal_maanden_groei = mysqli_query($db,"
SELECT count(distinct(month(h.datum))) mndat
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 join tblVolwas v on (v.volwId = s.volwId)
 join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = s.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = 1 and h.skip = 0 and year(h.datum) = '".mysqli_real_escape_string($db,$kzlJaar)."' and v.mdrId is not null
") or die (mysqli_error($db));
	while($rij = mysqli_fetch_array($zoek_aantal_maanden_groei)) { $mndat_gr = $rij['mndat']; }
// Gemiddelden ?>

<tr align = center>	
 <td width = 0> </td>	   
 <td width = 100 style = "font-size:13px;"> Gem <?php echo $mndat; ?> Mnd <br> </td>
 <td width = 1> </td>
 <td width = 80 style = "font-size:13px;"> <?php echo round($totWorp/$mndat,2); ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:13px;"> <?php echo round($totGeb/$mndat,2); ?> <br> </td>
 <td width = 1> </td>
 <td width = 80 style = "font-size:13px;"> <?php echo round($gemWorp/$mndat,2); ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:13px;"> <?php echo round($totDood/$mndat,2); ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:13px;"> <?php echo round($totDood/$totGeb*100,2); ?> <br> </td>
 <td width = 1> </td>
 <td width = 60 style = "font-size:13px;"> <?php echo round($totOndood/$mndat,2); ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:13px;"> <?php echo round($totOndood/$totGeb*100,2); ?> <br> </td>
 <td width = 1> </td>
 <td width = 60 style = "font-size:13px;"> <?php echo round($totVrmerk/$mndat,2); ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:13px;"> <?php echo round($totVrmerk/$totGeb*100,2); ?> <br> </td>
 <td width = 1> </td>	   
 <td width = 80 style = "font-size:13px;"> <?php echo round($totLevnr/$mndat,2); ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:13px;"> <?php echo round($totVrspn/$mndat,2); ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:13px;"> <?php echo round($totVrspn/$totGeb*100,2); ?> <br> </td>
 <td width = 1> </td>	   
 <td width = 80 style = "font-size:13px;"> <?php echo round($totSpnat/$mndat,2); ?> <br> </td>
 <td width = 1> </td>	   
 <td width = 80 style = "font-size:13px;"> <?php if(isset($mndat_gr) && $mndat_gr >0) { echo round($totGroei/$mndat_gr,2); } ?> <br> </td>
 <td width = 1> </td>
 <td width = 80 style = "font-size:13px;"> <?php if($totVoer>0) { echo round($totVoer/$mndat,2); } ?> <br> </td>
 <td width = 1> </td>
 <td width = 50> </td>
</tr> 


 <?php
// EINDE Gemiddelden
			

} //  Einde knop toon

/*****************************/
// DETAILS UITVAL VOOR SPENEN
/*****************************/

if(isset($keuze_mnd)) { ?>

<tr>
 <td colspan = 50 align="center">

<table>
<tr height = "50">
 <td></td>
</tr>
<tr style = "font-size:13px;" align="center">
 <td colspan="10"><h3>Detail uitval voor spenen</h3></td>
</tr>

<tr style = "font-size:12px;">
 <th width = 0 height = 30></th>
 <th style = "text-align:center;" valign= "bottom" width= 1>Werknr <hr></th>
 <th style = "text-align:center;"valign= "bottom";width= 80> Geboren <hr></th>
 <th style = "text-align:center;"valign="bottom"width= 80>Uitvaldatum<hr></th>
 <th style = "text-align:center;"valign="bottom"width= 80>Reden<hr></th>
<!-- <th style = "text-align:center;"valign= "bottom" width= 80>Gespeend<hr></th> -->
 <th style = "text-align:center;"valign= "bottom" width= 80>Meldnr RVO<hr></th>
</tr>

<?php 

$zoek_overleden_schapen = mysqli_query($db,"
SELECT right(s.levensnummer, $Karwerk) werknr, date_format(h.datum,'%d-%m-%Y') gebdm, date_format(dood.datum,'%d-%m-%Y') uitvdm, r.reden, date_format(spn.datum,'%d-%m-%Y') spndm, meld.meldnr

FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
 left join tblReden r on (r.redId = s.redId)
 join(
 	SELECT st.schaapId, datum
 	FROM tblStal st
 	 join tblHistorie h on (st.stalId = h.stalId)
 	WHERE h.actId = 14 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
 ) dood on (dood.schaapId = s.schaapId)
 left join(
 	SELECT st.schaapId, h.datum
 	FROM tblStal st
 	 join tblHistorie h on (st.stalId = h.stalId)
 	WHERE h.actId = 4 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
 ) spn on (spn.schaapId = s.schaapId)
 left join(
 	SELECT rs.levensnummer, rs.meldnr
 	FROM impRespons rs
 	WHERE rs.meldnr is not null and rs.melding = 'DOO'
 ) meld on (meld.levensnummer = s.levensnummer)
WHERE s.levensnummer is not null and h.actId = 1 and h.skip = 0 and (isnull(spn.schaapId) or spn.datum > dood.datum) and year(h.datum) = '".mysqli_real_escape_string($db,$kzlJaar)."' and month(h.datum) = '".mysqli_real_escape_string($db,$keuze_mnd)."' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
GROUP BY s.schaapId, st.stalId
") or die (mysqli_error($db));
	while($zos = mysqli_fetch_array($zoek_overleden_schapen)) {

	$werknr = $zos['werknr'];
	$gebdm = $zos['gebdm'];
	$uitvdm = $zos['uitvdm'];
	$reden = $zos['reden'];
	$spndm = $zos['spndm']; 
	$meldnr = $zos['meldnr']; ?>


<tr style = "font-size:12px;" align="center">
 <td></td>
 <td><?php echo $werknr; ?></td>
 <td><?php echo $gebdm; ?></td>
 <td><?php echo $uitvdm; ?></td>
 <td><?php echo $reden; ?></td>
 <!-- <td><?php if(isset($spndm)) {echo $spndm; } else { echo 'n.v.t.'; } ?></td> -->
 <td><?php echo $meldnr; ?></td>
 
</tr>


<?php
} ?>
</table>

 </td>
</tr>

<?php
}
/***********************************/
// Einde DETAILS UITVAL VOOR SPENEN
/***********************************/
?>



</table>
		</TD>
<?php } else { ?> <img src='mndoverz_fok_php.jpg'  width='970' height='550'/> <?php }
Include "menuRapport.php"; } ?>
</body>
</html>