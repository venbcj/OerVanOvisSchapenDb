<?php

/* 20-2-2015 : login toegevoegd 
14-11-2015 Eerste en tweede inenting verwijderd
18-11-2015 Hok gewijzigd naar verblijf 
23-11-2015 Spenen afleveren mogelijk gemaakt en link 'periode afsluiten verplaatst naar achteren'
19-12-2015 : link 'hok overpl' gewijzigd naar overpl */
$versie = "18-1-2017"; /* Query's aangepast n.a.v. nieuwe tblDoel Aantal nu in hok gewijzigd van count(distinct st.schaapId)-count(distinct uit.schaapId) naar count(b.bezId)-count(uit.bezId) zodat terugplaatsen ook zichtbaar is. */
$versie = "22-1-2017"; /* tblBezetting gewijzigd naar tblBezet */
$versie = "5-2-2017"; /* Aanpassing n.a.v. verblijven met verschillende doelgroepen */
$versie = "12-2-2017"; /* Bij historie lammeren H1.ACTID != 2 toegevegd. Bij aankoop moederdieren bestaat act 2 en act 3 waardoor dit dier in het hok heeft gezeten van aankoop t/m aanwas als dier 'zonder' aanwas datum. Wordt ooit een lam aangekocht maak dan een nieuwe actie hiervoor aan in tblActie !!!!!!!!!!!!!!!! */
$versie = "29-12-2017"; /* Aantal aanwezige volwassen dieren toegevoegd */
$versie = "13-05-2018"; /* $_SESSION["DT1"] = NULL; $_SESSION["BST"] = NULL;  toegevoegd */
$versie = '28-09-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '18-05-2019'; /* Afleveren, spenen en Overplaatsen mogelijk gemaakt via Hoklijsten.php */
$versie = '20-12-2019'; /* tabelnaam gewijzigd van UIT naar uit tabelnaam */
$versie = '28-6-2020'; /* datum in verblijf van volwassen dieren toegevoegd zodat link 'periode sluiten' zichtbaar wordt bij verblijven met enkel volwassen dieren */
$versie = '8-2-2021'; /* zoek_nu_in_verblijf_prnt herschreven i.v.m. dubbele records. Sql beveiligd met quotes */
$versie = '4-6-2021'; /* Verblijf ook zichtbaar als enkel volwassen dieren in het verblijf hebben gezeten */
$versie = '9-7-2021'; /* Schapen uit verblijf herzien. Join gewijzigd van h.hisId = uit.hisv naar b.bezId = uit.bezId */
$versie = '4-8-2021'; /* Schapen die 0 dagen in verblijf zitten ook meegeteld. Zie bijv (h.datum = spn.datum && h.hisId >= spn.hisId) */
$versie = '23-12-2023'; /* In query zoek_nu_in_verblijf_prnt skip = 0 toegevoegd. Vandaag is bij Folkert een herstel actie uitgevoerd n.a.v. toevoegen speendatum op 17-12 jl. Alle 116 overplaatsingen zijn verwijderd (skip = 1) 27-12-2023 and skip = 0 toegevoegd bij tblHistorie */
$versie = '05-01-2024'; /* Schapen die in het verblijf spenen de status aanwas kregen werden niet getoond. Dit is aangepast 
7-1-2024 : Aanwas werd onterecht aan een verblijf gekoppeld waardoor volwassendieren dubbel werden geteld in de kolom Volwassen aanwezig. 
Dit is voor de toekomst aangepast in save_aanwas.php. Met distinct in zoek_nu_in_verblijf_prnt is dit ook met bestaande registraties hersteld 
14-01-2024 Doelgroep verlaten telden ook volwassen dieren die niet in het verblijf hadden gezeten. Dit is aangepast door bij zoek_verlaten_spn_excl_overpl_en_uitval or (isnull(uit.bezId) and prnt.schaapId is not null)) uit te breiden naar or (isnull(uit.bezId) and prnt.schaapId is not null and h.datum < spn.datum)) */
$versie = '19-01-2024'; /* in nestquery 'uit' is 'and a1.aan = 1' uit WHERE gehaald. De hisId die voorkomt in tblBezet volstaat. Bovendien is bij Pieter hisId met actId 3 gekoppeld aan tblBezet en heeft het veld 'aan' in tblActie de waarde 0. De WHERE incl. 'and a1.aan = 1' geeft dus een fout resultaat. */
$versie = "10-03-2024"; /* De aantallen in kolom aanwezigen blauw gemaakt */
$versie = "11-03-2024"; /* Bij geneste query uit 
join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId) gewijzgd naar
join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
I.v.m. historie van stalId 22623. Dit dier is eerst verkocht en met terugwerkende kracht geplaatst in verblijf Afmest 1 */
$versie = '31-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */

 session_start(); ?>
 <!DOCTYPE html>
<html>
<head>
<title>Actueel</title>
</head>
<body>

<?php
$titel = 'Verblijven in gebruik';
$file = "Bezet.php";
include "login.php"; ?>

		<TD valign = "top">
<?php
if (is_logged_in()) { ?>

<form action = "Bezet.php" method = "post">
<table BORDER = 0 width = 960 align = "center">
<tr >
 <td colspan = 5> 

	<i style = "font-size : 13px;" > Verblijflijsten per doelgroep : &nbsp  
<?php

$zoek_verblijven_ingebruik_zonder_speendm = mysqli_query($db,"
SELECT count(distinct hokId) aant
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join 
 (
	SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	 left join (
		SELECT st.schaapId, h.datum dmspn
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 4
	 ) spn on (spn.schaapId = st.schaapId)
	 left join (
		SELECT st.schaapId, h.datum dmprnt
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 3
	 ) prnt on (prnt.schaapId = st.schaapId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	 and h1.datum <= coalesce(dmspn, coalesce(dmprnt,'2200-01-01'))
	GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (b.hisId = uit.hisv)
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
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(uit.bezId) and isnull(spn.schaapId) and isnull(prnt.schaapId) and h.skip = 0
") or die (mysqli_error($db));

$zoek_verblijven_ingebruik_met_speendm = mysqli_query($db,"
SELECT count(distinct hokId) aant
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join 
 (
	SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (b.hisId = uit.hisv)
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
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(uit.bezId) and isnull(prnt.schaapId) and h.skip = 0
") or die (mysqli_error($db));

	$row = mysqli_fetch_array($zoek_verblijven_ingebruik_zonder_speendm); if( $row['aant'] > 0 )
		{ ?>
			<a href=' <?php echo $url; ?>Hoklijst.php?pstgroep=1' style = "color : blue"> Geboren </a>	 
<?php
		}
	$row = mysqli_fetch_array($zoek_verblijven_ingebruik_met_speendm); if($row['aant'])
		{ 
			echo "&nbsp &nbsp" ; ?>
			<a href=' <?php echo $url; ?>Hoklijst.php?pstgroep=2' style = "color : blue"> Gespeend </a>	 
			</i>
<?php	} ?>
</td>
<?php
$zoek_schapen_zonder_verblijf = mysqli_query($db,"
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
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
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
WHERE (isnull(b.hokId) or uit.hist is not null)
") or die (mysqli_error($db));

while($row = mysqli_fetch_assoc($zoek_schapen_zonder_verblijf))
		{ $zVerb = $row['aantin']; } ?>

<td colspan = 8 align = "right">

<?php	if( $zVerb > 0 )
		{ ?>
	<a href=' <?php echo $url; ?>Loslopers.php?' style = "color : blue"> Schapen zonder verblijf </a>	 
<?php } ?>

</td>
<!--<td colspan = 2> <a href= '< ?php echo $url;?>Bezet_pdf.php? ?>' style = 'color : blue'> print verblijven </a></td> -->
</tr>
<tr style = "font-size:12px;">
<th colspan = 4 ></th>
<th colspan = 2 align =center valign=bottom style = "text-align:center;" >Totaal</th>
<th colspan = 6 ></th>
</tr>
<tr style = "font-size:12px;">
 <th width = 0 height = 30></th>
 <th style = "text-align:center;"valign="bottom"width= 150>Verblijf<hr></th>
 <th style = "text-align:center;"valign="bottom"width= 110>Eerste in<hr></th>
 <th style = "text-align:center;"valign="bottom"width= 110>Meest recente eruit<hr></th>
 <th style = "text-align:center;"valign="bottom"width= 60>voor spenen<hr></th>
 <th style = "text-align:center;"valign="bottom"width= 60>na spenen<hr></th>
 <th style = "text-align:center;"valign="bottom"width= 80>Lam aanwezig<hr></th>
 <th style = "text-align:center;"valign="bottom"width= 60>Doelgroep verlaten<hr></th>
 <th style = "text-align:center;"valign="bottom"width= 60>Overge- plaatst<hr></th>
 <th style = "text-align:center;"valign="bottom"width= 50>Uitval<hr></th>
 <th style = "text-align:center;"valign="bottom"width= 60>Moeders van lammeren<hr></th>
 <th style = "text-align:center;"valign="bottom"width= 60>Volwassen aanwezig<hr></th>
 <th style = "text-align:center;"valign="bottom"width= 60>Volwassen<br> totaal geteld<hr></th>
 <th style = "text-align:center;"valign="bottom"><hr></th>
 <th width=60></th>
</tr>
<?php // Zoek alle verblijven die in gebruik zijn 
/* Toelichting per union :
	schaap (doelgroep 1) zat in hok voor afsluitdm of zit na afsluitdam erin en zit in beide gevallen er nog steeds in
	schaap (doelgroep 1) is uit het hok gegaan na afsluitdatum doelgroep 1
	schaap (doelgroep 2) zat in hok voor afsluitdm of zit na afsluitdam erin en zit in beide gevallen er nog steeds in
	schaap (doelgroep 2) is uit het hok gegaan na afsluitdatum doelgroep 2	
	schaap met aanwasdatum zit nu in hok 
	schaap met aanwasdatum is uit het hok gegaan na afsluitdatum doelgroep 3 */
$zoek_verblijven_in_gebruik = mysqli_query($db,"
SELECT h.hokId, h.hoknr, count(distinct schaap_geb) maxgeb, count(distinct schaap_spn) maxspn, count(distinct schaap_prnt) maxprnt, min(dmin) eerste_in, max(dmuit) laatste_uit
FROM (
	SELECT b.hokId, st.schaapId schaap_geb, NULL schaap_spn, NULL schaap_prnt, h.datum dmin, NULL dmuit
	FROM tblBezet b
	 join tblHistorie h on (b.hisId = h.hisId)
	 join tblStal st on (st.stalId = h.stalId)
	 left join 
	 (
		SELECT b.bezId, min(h2.hisId) hist
		FROM tblBezet b
		 join tblHistorie h1 on (b.hisId = h1.hisId)
		 join tblActie a1 on (a1.actId = h1.actId)
		 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
		 join tblActie a2 on (a2.actId = h2.actId)
		 join tblStal st on (h1.stalId = st.stalId)
		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
		GROUP BY b.bezId
	 ) uit on (uit.bezId = b.bezId)
	 left join (
		SELECT st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 4 and h.skip = 0
	 ) spn on (spn.schaapId = st.schaapId)
	 left join (
		SELECT st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 3 and h.skip = 0
	 ) prnt on (prnt.schaapId = st.schaapId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(uit.bezId)
	and isnull(spn.schaapId)
	and isnull(prnt.schaapId)
 	and h.skip = 0

	UNION

	SELECT b.hokId, st.schaapId schaap_geb, NULL schaap_spn, NULL schaap_prnt, h.datum dmin, ht.datum dmuit
	FROM tblBezet b
	 join tblHistorie h on (h.hisId = b.hisId)
	 join 
	 (
		SELECT b.bezId, min(h2.hisId) hist
		FROM tblBezet b
		 join tblHistorie h1 on (b.hisId = h1.hisId)
		 join tblActie a1 on (a1.actId = h1.actId)
		 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
		 join tblActie a2 on (a2.actId = h2.actId)
		 join tblStal st on (h1.stalId = st.stalId)
		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h1.actId != 2
		GROUP BY b.bezId
	 ) uit on (uit.bezId = b.bezId)
	 join tblHistorie ht on (ht.hisId = uit.hist)
	 join tblStal st on (st.stalId = h.stalId)
	 left join (
		SELECT h.hisId, st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 4 and h.skip = 0
	 ) spn on (spn.schaapId = st.schaapId)
	 left join (
		SELECT st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 3 and h.skip = 0
	 ) prnt on (prnt.schaapId = st.schaapId)
	 left join (
		SELECT p.hokId, max(p.dmafsluit) dmstop
		FROM tblPeriode p
		 join tblHok h on (h.hokId = p.hokId)
		WHERE h.lidId = '".mysqli_real_escape_string($db,$lidId)."' and p.doelId = 1 and dmafsluit is not null
		GROUP BY p.hokId
	 ) endgeb on (endgeb.hokId = b.hokId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ht.datum > coalesce(dmstop,'1973-09-11') 
	 and ( isnull(spn.schaapId)  or (spn.datum  > coalesce(dmstop,'1973-09-11') and 
	 		( h.datum < spn.datum || (h.datum = spn.datum && h.hisId < spn.hisId) ) )
	 	 )
	 and ( isnull(prnt.schaapId) or (prnt.datum > coalesce(dmstop,'1973-09-11') and h.datum < prnt.datum) )
	 and h.skip = 0

	UNION

	SELECT b.hokId, NULL schaap_geb, st.schaapId schaap_spn, NULL schaap_prnt, h.datum dmin, NULL dmuit
	FROM tblBezet b
	 join tblHistorie h on (b.hisId = h.hisId)
	 join tblStal st on (st.stalId = h.stalId)
	 left join 
	 (
		SELECT b.bezId, min(h2.hisId) hist
		FROM tblBezet b
		 join tblHistorie h1 on (b.hisId = h1.hisId)
		 join tblActie a1 on (a1.actId = h1.actId)
		 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
		 join tblActie a2 on (a2.actId = h2.actId)
		 join tblStal st on (h1.stalId = st.stalId)
		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h1.actId != 2
		GROUP BY b.bezId
	 ) uit on (uit.bezId = b.bezId)
	 join (
		SELECT st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 4 and h.skip = 0
	 ) spn on (spn.schaapId = st.schaapId)
	 left join (
		SELECT st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 3 and h.skip = 0
	 ) prnt on (prnt.schaapId = st.schaapId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(uit.bezId)
	and (isnull(prnt.schaapId) or h.datum < prnt.datum)
	and h.skip = 0

	UNION

	SELECT b.hokId, NULL schaap_geb, st.schaapId schaap_spn, NULL schaap_prnt, h.datum dmin, ht.datum dmuit
	FROM tblBezet b
	 join tblHistorie h on (h.hisId = b.hisId)
	 join 
	 (
		SELECT b.bezId, min(h2.hisId) hist
		FROM tblBezet b
		 join tblHistorie h1 on (b.hisId = h1.hisId)
		 join tblActie a1 on (a1.actId = h1.actId)
		 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
		 join tblActie a2 on (a2.actId = h2.actId)
		 join tblStal st on (h1.stalId = st.stalId)
		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
		GROUP BY b.bezId
	 ) uit on (uit.bezId = b.bezId)
	 join tblHistorie ht on (ht.hisId = uit.hist)
	 join tblStal st on (st.stalId = h.stalId)
	 join (
		SELECT h.hisId, st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 4 and h.skip = 0
	 ) spn on (spn.schaapId = st.schaapId)
	 left join (
		SELECT st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 3 and h.skip = 0
	 ) prnt on (prnt.schaapId = st.schaapId)
	 left join (
		SELECT p.hokId, max(p.dmafsluit) dmstop
		FROM tblPeriode p
		 join tblHok h on (h.hokId = p.hokId)
		WHERE h.lidId = '".mysqli_real_escape_string($db,$lidId)."' and p.doelId = 2 and dmafsluit is not null
		GROUP BY p.hokId
	 ) endspn on (endspn.hokId = b.hokId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId /*9-1-2019 weggehaald and (isnull(prnt.schaapId) or prnt.datum > coalesce(dmstop,'1973-09-11')) */)."' and ht.datum > coalesce(dmstop,'1973-09-11') 
	 and (h.datum > spn.datum || (h.datum = spn.datum && h.hisId >= spn.hisId) )
	 and (isnull(prnt.schaapId) or h.datum < prnt.datum)
	 and h.skip = 0

	UNION

	SELECT b.hokId, NULL schaap_geb, NULL schaap_spn, st.schaapId schaap_prnt, h.datum dmin, NULL dmuit
	FROM tblBezet b
	 join tblHistorie h on (b.hisId = h.hisId)
	 join tblStal st on (st.stalId = h.stalId)
	 left join 
	 (
		SELECT b.bezId, min(h2.hisId) hist
		FROM tblBezet b
		 join tblHistorie h1 on (b.hisId = h1.hisId)
		 join tblActie a1 on (a1.actId = h1.actId)
		 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
		 join tblActie a2 on (a2.actId = h2.actId)
		 join tblStal st on (h1.stalId = st.stalId)
		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
		GROUP BY b.bezId
	 ) uit on (uit.bezId = b.bezId)
	 join (
		SELECT st.schaapId
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 3 and h.skip = 0
	 ) prnt on (prnt.schaapId = st.schaapId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(uit.bezId) and h.skip = 0

	UNION

	SELECT b.hokId, NULL schaap_geb, NULL schaap_spn, st.schaapId schaap_prnt, h.datum dmin, ht.datum dmuit
	FROM tblBezet b
	 join tblHistorie h on (h.hisId = b.hisId)
	 join 
	 (
		SELECT b.bezId, min(h2.hisId) hist
		FROM tblBezet b
		 join tblHistorie h1 on (b.hisId = h1.hisId)
		 join tblActie a1 on (a1.actId = h1.actId)
		 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
		 join tblActie a2 on (a2.actId = h2.actId)
		 join tblStal st on (h1.stalId = st.stalId)
		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
		GROUP BY b.bezId, st.schaapId, h1.hisId
	 ) uit on (uit.bezId = b.bezId)
	 join tblHistorie ht on (ht.hisId = uit.hist)
	 join tblStal st on (st.stalId = h.stalId)
	 join (
		SELECT h.hisId, st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 3 and h.skip = 0
	 ) prnt on (prnt.schaapId = st.schaapId)
	 left join (
		SELECT p.hokId, max(p.dmafsluit) dmstop
		FROM tblPeriode p
		 join tblHok h on (h.hokId = p.hokId)
		WHERE h.lidId = '".mysqli_real_escape_string($db,$lidId)."' and p.doelId = 3 and dmafsluit is not null
		GROUP BY p.hokId
	 ) endspn on (endspn.hokId = b.hokId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ht.datum > coalesce(dmstop,'1973-09-11') 
	 and (h.datum > prnt.datum || (h.datum = prnt.datum && h.hisId >= prnt.hisId) ) and h.skip = 0

 ) ingebr
 join tblHok h on (ingebr.hokId = h.hokId)
GROUP BY h.hokId, h.hoknr
ORDER BY hoknr
") or die (mysqli_error($db));
 
		while($row = mysqli_fetch_assoc($zoek_verblijven_in_gebruik))
		{  // Loop alle verblijven in gebruik
		/*$periId = $row['periId'];*/
		$hokId = $row['hokId'];
		$hoknr = $row['hoknr'];
		$maxgeb = $row['maxgeb'];
		$maxspn = $row['maxspn'];
		$maxprnt = $row['maxprnt'];
		$dmeerst = $row['eerste_in'];
		$dmlaatst = $row['laatste_uit'];
		
$zoek_laatste_afsluitdm_geb = mysqli_query($db,"
SELECT max(dmafsluit) dmstop
FROM tblPeriode
WHERE hokId = '".mysqli_real_escape_string($db,$hokId)."' and doelId = 1 and dmafsluit is not null
") or die (mysqli_error($db));
 
	while($stp_g = mysqli_fetch_assoc($zoek_laatste_afsluitdm_geb))	{ $dmstopgeb = $stp_g['dmstop']; } if(!isset($dmstopgeb)) { $dmstopgeb = '1973-09-11'; }

$zoek_laatste_afsluitdm_spn = mysqli_query($db,"
SELECT max(dmafsluit) dmstop
FROM tblPeriode
WHERE hokId = '".mysqli_real_escape_string($db,$hokId)."' and doelId = 2 and dmafsluit is not null
") or die (mysqli_error($db));
 
	while($stp_s = mysqli_fetch_assoc($zoek_laatste_afsluitdm_spn))	{ $dmstopspn = $stp_s['dmstop']; } if(!isset($dmstopspn)) { $dmstopspn = '1973-09-11'; }
		
$zoek_nu_in_verblijf_geb = mysqli_query($db,"
SELECT count(b.bezId) aantin
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join 
 (
	SELECT b.bezId, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and isnull(uit.bezId)
and isnull(spn.schaapId)
and isnull(prnt.schaapId)
and h.skip = 0
") or die (mysqli_error($db));
		
	while($nu1 = mysqli_fetch_assoc($zoek_nu_in_verblijf_geb))
		{ $aanwezig1 = $nu1['aantin']; }

$zoek_nu_in_verblijf_spn = mysqli_query($db,"
SELECT count(b.bezId) aantin
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join 
 (
	SELECT b.bezId, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and isnull(uit.bezId)
and isnull(prnt.schaapId)
and h.skip = 0
") or die (mysqli_error($db));
		
	while($nu2 = mysqli_fetch_assoc($zoek_nu_in_verblijf_spn))
		{ $aanwezig2 = $nu2['aantin']; }

	$aanwezig = $aanwezig1 + $aanwezig2;

$zoek_nu_in_verblijf_prnt = mysqli_query($db,"
SELECT count(distinct(st.schaapId)) aantin
FROM tblStal st
 join tblHistorie h on (h.stalId = st.stalId)
 join tblBezet b on (b.hisId = h.hisId)
 left join (
	SELECT b.bezId, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId, h1.hisId
 ) uit on (b.bezId = uit.bezId)
 join (
	SELECT schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)

WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and isnull(uit.bezId) and h.skip = 0
") or die (mysqli_error($db));
		
	while($nu3 = mysqli_fetch_assoc($zoek_nu_in_verblijf_prnt))
		{ $aanwezig3 = $nu3['aantin']; }

		$aanwezig_incl = $aanwezig + $aanwezig3;
		
		
$zoek_verlaten_geb_excl_overpl_en_uitval = mysqli_query($db,"
SELECT count(uit.bezId) aantuit
FROM tblBezet b
 join tblHistorie h on (h.hisId = b.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join 
 (
	SELECT b.bezId, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.bezId = b.bezId)
 join tblHistorie ht on (ht.hisId = uit.hist)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and ht.datum > '".mysqli_real_escape_string($db,$dmstopgeb)."' and ht.actId != 5 and ht.actId != 14
and (isnull(spn.schaapId) or ht.datum = spn.datum)
and (isnull(prnt.schaapId) or ht.datum < prnt.datum)
and h.skip = 0
") or die (mysqli_error($db));
		
	while($uit1 = mysqli_fetch_assoc($zoek_verlaten_geb_excl_overpl_en_uitval))
		{ $uit_geb = $uit1['aantuit']; }


$zoek_verlaten_spn_excl_overpl_en_uitval = mysqli_query($db,"
SELECT count(b.bezId) aantuit
FROM tblBezet b
 join tblHistorie h on (h.hisId = b.hisId)
 join tblStal st on (h.stalId = st.stalId)
 left join 
 (
	SELECT b.bezId, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 left join tblHistorie ht on (ht.hisId = uit.hist)
 join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and ((isnull(ht.datum) and prnt.schaapId is not null) or ht.datum > '".mysqli_real_escape_string($db,$dmstopspn)."')
and (isnull(ht.actId) or (ht.actId != 4 and ht.actId != 5 and ht.actId != 14))
and (ht.datum >= spn.datum or (isnull(uit.bezId) and prnt.schaapId is not null and h.datum < spn.datum))

and h.skip = 0
") or die (mysqli_error($db));
		
	while($uit2 = mysqli_fetch_assoc($zoek_verlaten_spn_excl_overpl_en_uitval))
		{ $uit_spn = $uit2['aantuit']; }
		
	$uit = $uit_geb + $uit_spn;


$zoek_overplaatsing_geb = mysqli_query($db,"
SELECT count(uit.bezId) aant
FROM tblBezet b
 join tblHistorie h on (h.hisId = b.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join 
 (
	SELECT b.bezId, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 join tblHistorie ht on (ht.hisId = uit.hist)
 left join (
	SELECT st.schaapId, h.hisId his_spn, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and ht.actId = 5
 and (ht.datum > '".mysqli_real_escape_string($db,$dmstopgeb)."' or (ht.datum = '".mysqli_real_escape_string($db,$dmstopgeb)."' and h.datum = '".mysqli_real_escape_string($db,$dmstopgeb)."' and h.hisId < ht.hisId))
and (isnull(spn.schaapId) or ht.datum < spn.datum or (ht.datum = spn.datum and his_spn > hist))
and (isnull(prnt.schaapId) or ht.datum < prnt.datum)
and h.skip = 0
") or die (mysqli_error($db));
		
	while($ovp1 = mysqli_fetch_assoc($zoek_overplaatsing_geb))
		{ $overpl_geb = $ovp1['aant']; }


$zoek_overplaatsing_spn = mysqli_query($db,"
SELECT count(uit.bezId) aant
FROM tblBezet b
 join tblHistorie h on (h.hisId = b.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join 
 (
	SELECT b.bezId, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 join tblHistorie ht on (ht.hisId = uit.hist)
 join (
	SELECT st.schaapId, h.hisId his_spn, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and ht.actId = 5
 and (ht.datum > '".mysqli_real_escape_string($db,$dmstopspn)."' or (ht.datum = '".mysqli_real_escape_string($db,$dmstopspn)."' and h.datum = '".mysqli_real_escape_string($db,$dmstopspn /* or (ht.datum = spn.datum and his_spn < hist) is voor als speendatum == overplaatsing en overplaatsing heeft eerder plaatsgevonden */)."' and h.hisId < ht.hisId))
and (ht.datum > spn.datum or (ht.datum = spn.datum and his_spn < hist))
and (isnull(prnt.schaapId) or h.datum < prnt.datum)
and h.skip = 0
") or die (mysqli_error($db));
		
	while($ovp2 = mysqli_fetch_assoc($zoek_overplaatsing_spn))
		{ $overpl_spn = $ovp2['aant']; }

	$overpl = $overpl_geb + $overpl_spn;

$zoek_overleden_geb = mysqli_query($db,"
SELECT count(uit.bezId) aantuit
FROM tblBezet b
 join 
 (
	SELECT b.bezId, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 join tblHistorie ht on (ht.hisId = uit.hist)
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and ht.actId = 14
 and ht.datum > '".mysqli_real_escape_string($db,$dmstopgeb)."'
 and isnull(spn.schaapId)
 and isnull(prnt.schaapId)
 and h.skip = 0
") or die (mysqli_error($db));
		
	while($doo1 = mysqli_fetch_assoc($zoek_overleden_geb))
		{ $uitval1 = $doo1['aantuit']; }

$zoek_overleden_spn = mysqli_query($db,"
SELECT count(uit.bezId) aantuit
FROM tblBezet b
 join 
 (
	SELECT b.bezId, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 join tblHistorie ht on (ht.hisId = uit.hist)
 join tblHistorie h on (h.hisId = b.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and ht.actId = 14
 and ht.datum > '".mysqli_real_escape_string($db,$dmstopspn)."'
 and isnull(prnt.schaapId)
 and h.skip = 0
") or die (mysqli_error($db));
		
	while($doo2 = mysqli_fetch_assoc($zoek_overleden_spn))
		{ $uitval2 = $doo2['aantuit']; }
		
	$uitval = $uitval1 + $uitval2;
	
$zoek_moeders_van_lam = mysqli_query($db,"
SELECT count(distinct v.mdrId) aantmdr
FROM tblBezet b
 left join 
 (
	SELECT b.bezId, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
 join tblSchaap s on (st.schaapId = s.schaapId)
 join tblVolwas v on (s.volwId = v.volwId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and isnull(uit.bezId)
 and isnull(spn.schaapId)
 and isnull(prnt.schaapId)
 and h.skip = 0
") or die (mysqli_error($db));
		
	while($mdr = mysqli_fetch_assoc($zoek_moeders_van_lam))
		{ $mdrs = $mdr['aantmdr']; }



		


		
if (isset($dmeerst)) {
$datum = date_create($dmeerst);
$van = date_format($datum,'d-m-Y');
$dmvan = date_format($datum,'Y-m-d');
$today = date('Y-m-d');
unset($dmeerst);
}

If (isset($dmlaatst))
{
$datum = date_create($dmlaatst);
$tot = date_format($datum,'d-m-Y');
} ?>

<tr align = "center">	
	   <td width = 0> </td>			
	   
<td width = 150 style = "font-size:15px;">	 
 <a href=' <?php echo $url; ?>Hoklijsten.php?pst=<?php echo $hokId; ?>' style = "color : blue">
<?php
 echo $hoknr; ?>	 
</a> <br/>  
</td>	   
	   
	   
	   <td width = 110 style = "font-size:13px;"> <?php if(isset($van)) { echo $van; unset($van); } ?> </td>	   
	   
	   <td width = 110 style = "font-size:13px;"> <?php if(isset($tot)) { echo $tot; } ?> </td>
	   
	   <td width = 60 style = "font-size:15px; color:grey; "> <?php if(isset($maxgeb) && $maxgeb > 0) { echo $maxgeb; } ?> </td>
	   
	   <td width = 60 style = "font-size:15px; color:grey; "> <?php if(isset($maxspn) && $maxspn > 0) { echo $maxspn; } ?> </td>
	   
	   <td width = 60 style = "font-size:15px; color:blue; "> <?php echo $aanwezig; ?> </td>
	   
	   <td width = 60 style = "font-size:15px; color:grey; "> <?php echo $uit; ?> </td>
	   
	   <td width = 60 style = "font-size:15px; color:grey; "> <?php echo $overpl; ?> </td>
	   
	   <td width = 50 style = "font-size:15px; color:grey; "> <?php echo $uitval; ?> </td>
	   
	   <td width = 60 style = "font-size:15px; color:grey; "> <?php echo $mdrs; ?> </td>

	   <td width = 60 style = "font-size:15px; color:blue; "> <?php if($aanwezig3 >0) { echo $aanwezig3; } ?> </td>

	   <td width = 60 style = "font-size:15px; color:grey; "> <?php if(isset($maxprnt) && $maxprnt >0) { echo $maxprnt; } ?> </td>

	 

</td>


<td width = 200 style = "font-size:13px;">
<?php if(isset($dmvan) && $dmvan < $today) { ?>
 <a href='<?php echo $url; ?>HokAfsluiten.php?pstId=<?php echo $hokId; ?>' style = "color : blue">   
	Periode sluiten  </a>	
<?php } unset($dmvan); ?>
 </td>
</tr>
	   
<?php	unset($tot);	} // Einde Loop alle verblijven in gebruik ?>
</tr>			
</table>
</form>
</TD>
<?php
include "menu1.php"; }
?>

</tr>

</table>

</body>
</html>
