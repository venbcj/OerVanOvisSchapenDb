<?php 
$versie = "03-09-2017"; /* 16-7-2017 gemaakt 	3-9-2017 kg voer te wijzigen*/
$versie = '28-09-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '16-11-2019'; /* Hoeveelheid opnieuw gebouwd i.v.m. andere manier van kg voer vastleggen. Incl. toevoegen van optie Volwassen dieren */
$versie = '20-12-2019'; /* tabelnaam gewijzigd van UIT naar uit tabelnaam */
$versie = '06-03-2020'; /* Rapport ook zichtbaar gemaakt als voer niet wordt gebruikt */
$versie = '01-01-2024'; /* and h.skip = 0 aangevuld bij tblHistorie en sql verder beveiligd */
$versie = "11-03-2024"; /* Bij geneste query uit 
join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId) gewijzgd naar
join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
I.v.m. historie van stalId 22623. Dit dier is eerst verkocht en met terugwerkende kracht geplaatst in verblijf Afmest 1 */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" align = center> gewijzigd naar <TD valign = 'top' align = 'center'> 31-12-24 include login voor include header gezet */
$versie = '19-02-2025'; /* Gegevens werden niet getoond omdat geneste query's als variabele in mysqli_real_escape_string($db,... stonden. 23-02-2025 Titel gewijzigd van Overzicht voertoediening naar Voer rapportage*/

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Rapport</title>		
</head>
<body>

<?php
$titel = 'Voer rapportage';
$file = "Voer_rapportage.php";
 include "login.php"; ?>

				<TD valign = 'top' align = 'center'>
<?php
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { if($modtech == 1) { 
 
if(isset($_POST['knpSave_'])) { include "save_voerrapport.php";  
		//header("Location: ".$url."Voer_rapportage.php"); 
	}
	?>

<table border = 0 align = "center">

<form action = "Voer_rapportage.php" method = "post">
<tr>
<td width= 100 >
 <select name= "kzlDoel_" style= "font-size : 11px; width:110;" > 
<?php
$opties = array(1 => 'Foklammeren', 2 => 'Vleeslammeren', 3 => 'Volwassen dieren');
foreach ( $opties as $key => $waarde)
{
   $keuze = '';
   if(isset($_POST['kzlDoel_']) && $_POST['kzlDoel_'] == $key)
   {
        $keuze = ' selected ';
   }
   echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
} ?>
</select> 

</td>

<td width = 250 style="font-size : 13px; text-align: right" >
Voer
<?php
//kzlVoer
$name = "kzlVoer_"; ?>
<select name= <?php echo"$name";?> width = 60 >
 <option></option>
<?php		
$zoek_voer = mysqli_query($db,"
SELECT a.artId, a.naam
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (a.enhuId = eu.enhuId)
 join tblInkoop i on (a.artId = i.artId)
WHERE a.soort = 'voer' and eu.lidId = '".mysqli_real_escape_string($db,$lidId)."'
GROUP BY a.artId, a.naam 
ORDER BY a.naam
") or die (mysqli_error($db));
	while($row = mysqli_fetch_array($zoek_voer))
		{
$kzlkey="$row[artId]";
$kzlvalue="$row[naam]";

			$opties= array($kzlkey=>$kzlvalue);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if(isset($_POST[$name]) && $_POST[$name] == $key)
		{
			$keuze = ' selected ';
		}
				
		echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
			}

		}
// EINDE kzlVoer
?>
</select> 
 </td>
<td width = 300 style = "font-size : 13px;">
<?php
If ((isset($_POST['knpToon_']) || isset($_POST['knpSave_'])) ) { 

/*** Opbouw keuzelijst maand of verblijf als er meerdere maanden of verblijven zijn ***/


if(isset($_POST['radVoer_']) && $_POST['radVoer_']==1 ) { $metVoer = 'ja'; } else { $metVoer = 'nee';  }

$fldVoer = $_POST['kzlVoer_'];


// Controle meerdere maanden om keuzelijst kzlJaarMaand te laten zien bij meer dan 1 maand.
// $aantjaarmaanden zoekt het aantal jaarmaanden in tblPeriode o.b.v. lidId, al dan niet het voer en de doelgroep
$aantjaarmaanden = " 
SELECT count(date_format(p.dmafsluit,'%Y%m')) jrmnd
FROM tblPeriode p
 join tblHok h on (p.hokId = h.hokId)
 left join tblVoeding v on (p.periId = v.periId)
 left join tblInkoop i on (i.inkId = v.inkId) 
WHERE h.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ".db_null_filter('i.artId', $fldVoer)." and p.doelId = $_POST[kzlDoel_]
";

$aantjaarmaanden = mysqli_query($db,$aantjaarmaanden) or die (mysqli_error($db));
   $row = mysqli_fetch_assoc($aantjaarmaanden);
		$rows_jrmnd = $row['jrmnd'];
		
	if ($rows_jrmnd >1) { ?>
 Maand
 
<?php //kzlJaarMaand
$kzljrmnd = mysqli_query($db,"
SELECT date_format(p.dmafsluit,'%Y%m') jrmnd, month(p.dmafsluit) maand, date_format(p.dmafsluit,'%Y') jaar 
FROM tblPeriode p 
 left join tblVoeding v  on (p.periId = v.periId)
 left join tblInkoop i on (i.inkId = v.inkId)
 left join tblArtikel a on (a.artId = i.artId)
 left join tblEenheiduser eu on (a.enhuId = eu.enhuId)
 left join tblEenheid e on (e.eenhId = eu.eenhId)
WHERE eu.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ".db_null_filter('i.artId', $fldVoer)."
GROUP BY date_format(p.dmafsluit,'%Y%m')
") or die (mysqli_error($db)); 

$name = 'kzlMdjr_'; ?>
<select name= <?php echo"$name";?>  style="font-size : 13px" width= 108 >
 <option></option>	
<?php		while($row = mysqli_fetch_array($kzljrmnd))
		  { $maand = $row['maand']; 
				$mndname = array('','januari', 'februari', 'maart','april','mei','juni','juli','augustus','september','oktober','november','december');
$kzlkey="$row[jrmnd]";
$kzlvalue="$mndname[$maand] $row[jaar]";

include "kzl.php";
		}
?></select> <?php //Einde kzlJaarMaand
}
//Einde Controle meerdere maanden om keuzelijst kzlJaarMaand te laten zien bij meer dan 1 maand.
// Controle meerdere verblijven om keuzelijst kzlHok_ te laten zien bij meer dan 1 verblijf.
$aantverblijven = mysqli_query($db,"
SELECT count(p.periId) aant
FROM tblHok h
 join tblPeriode p on (p.hokId = h.hokId)
 left join tblVoeding v on (p.periId = v.periId)
 left join tblInkoop i on (i.inkId = v.inkId)
WHERE h.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ".db_null_filter('i.artId', $fldVoer)." and p.doelId = $_POST[kzlDoel_]
") or die (mysqli_error($db));

	$row = mysqli_fetch_assoc($aantverblijven);
		$rows_hok = $row['aant'];

		if($rows_hok > 1) { echo "&nbsp Verblijf ";
//kzlHok
$kzlHok = mysqli_query($db,"
SELECT h.hokId, h.hoknr
FROM tblHok h
 join tblPeriode p on (p.hokId = h.hokId)
 left join tblVoeding v on (p.periId = v.periId)
 left join tblInkoop i on (i.inkId = v.inkId)
WHERE h.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ".db_null_filter('i.artId', $fldVoer)."
GROUP BY h.hoknr
") or die (mysqli_error($db));

$name = 'kzlHok_';?>
<select name = <?php echo"$name";?> style = "font-size : 13px" width = 100 >
 <option></option>
<?php		while($row = mysqli_fetch_assoc($kzlHok)) {
$kzlkey = "$row[hokId]";
$kzlvalue="$row[hoknr]";

include "kzl.php";
}
} // Einde Controle meerdere verblijven om keuzelijst kzlHok_ te laten zien bij meer dan 1 verblijf.
 
} /*** EINDE Opbouw keuzelijst maand of verblijf als er meerdere maanden of verblijven zijn ***/

?>
</td>
 <td> <input type = "submit" name ="knpToon_" value = "Toon"> </td>

 <td width = 160 style = "font-size:12px;" >
Alleen met voer 
  <input type = radio name = 'radVoer_' value = 1 
<?php if(!isset($_POST['radVoer_']) || (isset($_POST['radVoer_']) && $_POST['radVoer_'] == 1 )) { echo "checked"; } ?> > Ja
  <input type = radio name = 'radVoer_' value = 0
<?php if(isset($_POST['radVoer_']) && $_POST['radVoer_'] == 0 ) { echo "checked"; } ?> > Nee

</td>
 
 <td> </td>
<td width="1" align="right">
 <input type = submit name = 'knpSave_' value="opslaan" style = "font-size:11px;" > </td>
 </tr>	
 </table>

<table border = 0 >
<tr>
<td> </td>
<td>
<?php
If (isset($_POST['knpToon_']) || isset($_POST['knpSave_']) ) {
	if ($rows_jrmnd <= 1 || empty($_POST['kzlMdjr_'])) { $resJrmnd = "( date_format(p.dmafsluit,'%Y%m') is not null )"; }
	else if ($rows_jrmnd > 1 && !empty($_POST['kzlMdjr_'])) { $resJrmnd = "( date_format(p.dmafsluit,'%Y%m') = $_POST[kzlMdjr_] )"; }
	if ($rows_hok <= 1 || empty($_POST['kzlHok_'])) { $resHok = "( ho.hokId is not null )"; }
	else if ($rows_hok > 1 && !empty($_POST['kzlHok_'])) { $value = "$_POST[kzlHok_]"; $resHok = "( ho.hokId = $value )"; }

//$maandjaren toont de maand(en) uit tblPeriode binnen het gekozen voer en eventueel gekozen hok. T.b.v. de loop maand jaar
$maandjaren = "
SELECT month(p.dmafsluit) maand, date_format(p.dmafsluit,'%Y') jaar, date_format(p.dmafsluit,'%Y%m') jrmnd
FROM tblHok ho
 join tblPeriode p on (p.hokId = ho.hokId)
 left join tblVoeding v on (v.periId = p.periId)
 left join tblInkoop i on (i.inkId = v.inkId)
 left join tblArtikel a on (a.artId = i.artId) 
WHERE ho.lidId = '".mysqli_real_escape_string($db,$lidId)."' and p.doelId = $_POST[kzlDoel_] and ".db_null_filter('i.artId', $fldVoer)." and ".$resJrmnd." and ".$resHok."
GROUP BY month(p.dmafsluit), date_format(p.dmafsluit,'%Y')
ORDER BY jaar desc, month(p.dmafsluit) desc
";

//echo '$maandjaren = '.$maandjaren.'<br>';
$maandjaren = mysqli_query($db,$maandjaren) or die (mysqli_error($db));
  while ($rij = mysqli_fetch_assoc($maandjaren))
		{  // START LOOP maandnaam jaartal
		$mndnr = $rij['maand'];
		$jaar = $rij['jaar'];
		$jrmnd = $rij['jrmnd'];

$mndnaam = array('','januari', 'februari', 'maart','april','mei','juni','juli','augustus','september','oktober','november','december'); 
		
//$tot = 		 date("Ym");
//$maand = 	 date("m");
//$jaarstart = date("Y")-2;

	
//$vanaf = "$jaarstart$maand";
?>
<tr height = 30><td></td></tr>
<tr style = "font-size:18px;" ><td colspan = 3><b><?php echo "$mndnaam[$mndnr] &nbsp $jaar"; ?></b></td></tr>
<tr style = "font-size:12px;">

<tr style = "font-size:15px;" valign = top>
<th style = "text-align:center;"valign="bottom";width = 70>Verblijf<hr></th>
<th style = "text-align:center;"valign="bottom";width= 90>start<hr></th>
<th style = "text-align:center;"valign="bottom";width= 80>einddatum<hr></th>
<th style = "text-align:center;"valign="bottom";width= 80><?php if($metVoer == 'ja') { ?> voerdatum <?php } else { ?> afsluit- / voerdatum <?php } ?> <hr></th>
<th style = "text-align:center;"valign="bottom";		 >Aantal schapen<hr></th>
<th style = "text-align:center;"valign="bottom";width= 100	 >Gem dagen<br>per schaap <hr></th>
<th style = "text-align:center;"valign="bottom";>Kg voer<hr></th>
<th style = "text-align:left;"valign="bottom";><hr></th>
<td align = "center" valign = "bottom"> <input type = "submit" name = <?php echo "knpToon_"; ?> value = "Toon" style = "font-size:11px;" > <hr> </td>
<td colspan = 2 align="center"; valign="bottom";> Verwijder<br>&nbsp&nbspvoer&nbsp&nbsp periode<hr></td>
</tr>

<?php // $zoek_startdatum zoekt eerste van de maand en het jaar dat een gebruiker is begonnen met het programma
$zoek_startdatum = mysqli_query($db," 
SELECT date_format(dmcreate,'%Y-%m-01') dmstart
FROM tblLeden
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die(mysql_error($db));
	while ($st = mysqli_fetch_assoc($zoek_startdatum)) { $dmstart = $st['dmstart']; }


// subquery v binnen $allePeriodes_met_BeginEnEindDatum_inlc_Voer = Artikel met voeraantal per periode
$allePeriodes_met_BeginEnEindDatum_inlc_Voer = "
SELECT p2.periId, p2.hokId, p1.doelId, max(p1.dmafsluit) dmbegin, p2.dmafsluit dmeind, v.artId, v.nutat
FROM tblPeriode p1
 join tblPeriode p2 on (p1.hokId = p2.hokId and p1.doelId = p2.doelId and p1.dmafsluit < p2.dmafsluit)
 join tblHok ho on (ho.hokId = p1.hokId) 
 left join ( 
 		SELECT v.periId, i.artId, sum(v.nutat) nutat
		FROM tblVoeding v
		 join tblPeriode p on (v.periId = p.periId)
		 join tblHok ho on (ho.hokId = p.hokId)
		 join tblInkoop i on (v.inkId = i.inkId)
		WHERE ho.lidId = '".mysqli_real_escape_string($db,$lidId)."' and p.doelId = $_POST[kzlDoel_]
		GROUP BY v.periId, i.artId
 ) v on (p2.periId = v.periId)
WHERE ho.lidId = '".mysqli_real_escape_string($db,$lidId)."' and p1.doelId = $_POST[kzlDoel_]
GROUP BY p2.periId, p2.hokId, p1.doelId, p2.dmafsluit, v.nutat
";

// subquery p1 binnen $begin_eind_periode = Eerste periode met_fictieve startdatum
// subquery v binnen $begin_eind_periode = Artikel met voeraantal per periode
$begin_eind_periode = "
SELECT p.periId, ho.hokId, ho.hoknr, date_format(p.dmeind,'%Y%m') jrmnd, p.dmbegin, p.dmeind, p.artId, p.nutat
FROM (
	SELECT p.periId, p1.hokId, p1.doelId, p1.dmbegin, p1.dmeind, v.artId, v.nutat 
	FROM (
	 	SELECT p.hokId, p.doelId, l.dmcreate dmbegin, min(p.dmafsluit) dmeind
		FROM tblPeriode p
		 join tblHok ho on (p.hokId = ho.hokId)
		 join tblLeden l on (ho.lidId = l.lidId)
		WHERE ho.lidId = '".mysqli_real_escape_string($db,$lidId)."' and p.doelId = $_POST[kzlDoel_]
		GROUP BY p.hokId, p.doelId
	 ) p1
	 join tblPeriode p on (p1.hokId = p.hokId and p1.doelId = p.doelId and p1.dmeind = p.dmafsluit)
	 left join (
	 	SELECT v.periId, i.artId, sum(v.nutat) nutat
		FROM tblVoeding v
		 join tblPeriode p on (v.periId = p.periId)
		 join tblHok ho on (ho.hokId = p.hokId)
		 join tblInkoop i on (v.inkId = i.inkId)
		WHERE ho.lidId = '".mysqli_real_escape_string($db,$lidId)."' and p.doelId = $_POST[kzlDoel_]
		GROUP BY v.periId, i.artId
	 ) v on (p.periId = v.periId)

	union

	$allePeriodes_met_BeginEnEindDatum_inlc_Voer
 ) p
 join tblHok ho on (ho.hokId = p.hokId)
 left join tblArtikel i on (i.artId = p.artId)
WHERE ho.lidId = '".mysqli_real_escape_string($db,$lidId)."' and p.doelId = $_POST[kzlDoel_] and ".db_null_filter('i.artId', $fldVoer)." and date_format(p.dmeind,'%Y%m') = '".mysqli_real_escape_string($db,$jrmnd)."'
";

# if( $mndnr == 6) { echo $begin_eind_periode.'<br>'; }
$begin_eind_periode = mysqli_query($db,$begin_eind_periode) or die (mysqli_error($db));
  while ($mld = mysqli_fetch_assoc($begin_eind_periode))
		{  // START LOOP $begin_eind_periode
			$hokId = $mld['hokId'];
			$hoknr = $mld['hoknr'];
			$jaarmnd = $mld['jrmnd'];

			$periId = $mld['periId']; 
			$dmbegin = $mld['dmbegin']; 
			$dmeind = $mld['dmeind']; 

if($_POST['kzlDoel_'] == 1) { $filterDoel = ' and (his_in.datum < spn.datum or (isnull(spn.schaapId) and isnull(prn.schaapId)) )'; }
if($_POST['kzlDoel_'] == 2) { $filterDoel = ' and (his_in.datum >= spn.datum and (his_in.datum < prn.datum or isnull(prn.schaapId)) )'; }
if($_POST['kzlDoel_'] == 3) { $filterDoel = ' and (his_in.datum >= spn.datum)'; }

$periode_totalen = "
SELECT p.periId, ho.hokId, ho.hoknr, p.dmbegin, date_format(p.dmbegin,'%d-%m-%Y') begindm, min(his_in.datum) dmschaap1, date_format(min(his_in.datum),'%d-%m-%Y') schaap1dm,
 p.dmeind, date_format(p.dmeind,'%d-%m-%Y') einddm, max(coalesce(his_uit.datum,p.dmeind)) dmschaapend, 
 date_format(max(coalesce(his_uit.datum,p.dmeind)),'%d-%m-%Y') schaapenddm,
 p.nutat, count(distinct st.schaapId) schpn, 
 sum(datediff(coalesce(his_uit.datum,p.dmeind),his_in.datum)) dagen,
 round(sum(datediff(coalesce(his_uit.datum,p.dmeind),his_in.datum))/count(st.schaapId),2) gemdgn,
 count(v.voedId) voedId
FROM tblHok ho
 join (
 	SELECT p.periId, p1.hokId, p1.doelId, p1.dmbegin, p1.dmeind, v.nutat 
 	FROM (
	 	SELECT p.hokId, p.doelId, '".mysqli_real_escape_string($db,$dmstart)."' dmbegin, min(p.dmafsluit) dmeind
		FROM tblPeriode p
		 join tblHok ho on (p.hokId = ho.hokId)
		WHERE ho.lidId = '".mysqli_real_escape_string($db,$lidId)."' and p.doelId = $_POST[kzlDoel_]
		GROUP BY p.hokId, p.doelId
	 ) p1
	 join tblPeriode p on (p1.hokId = p.hokId and p1.doelId = p.doelId and p1.dmeind = p.dmafsluit)
	 left join (
	 	SELECT v.periId, i.artId, sum(v.nutat) nutat
	 	FROM tblVoeding v
	 	 join tblPeriode p on (v.periId = p.periId)
	 	 join tblHok ho on (ho.hokId = p.hokId)
	 	 join tblInkoop i on (v.inkId = i.inkId)
	 	WHERE ho.lidId = '".mysqli_real_escape_string($db,$lidId)."' and p.doelId = $_POST[kzlDoel_]
	 	GROUP BY v.periId, i.artId
	 ) v on (p.periId = v.periId)

	union

	SELECT p2.periId, p2.hokId, p1.doelId, max(p1.dmafsluit) dmbegin, p2.dmafsluit dmeind, v.nutat 
	FROM tblPeriode p1
	 join tblPeriode p2 on (p1.hokId = p2.hokId and p1.doelId = p2.doelId and p1.dmafsluit < p2.dmafsluit)
	 join tblHok ho on (ho.hokId = p1.hokId)
	 left join (
	 	SELECT v.periId, i.artId, sum(v.nutat) nutat
	 	FROM tblVoeding v
	 	 join tblPeriode p on (v.periId = p.periId)
	 	 join tblHok ho on (ho.hokId = p.hokId)
	 	 join tblInkoop i on (v.inkId = i.inkId)
	 	WHERE ho.lidId = '".mysqli_real_escape_string($db,$lidId)."' and p.doelId = $_POST[kzlDoel_]
	 	GROUP BY v.periId, i.artId
	 ) v on (p2.periId = v.periId)
	WHERE ho.lidId = '".mysqli_real_escape_string($db,$lidId)."' and p1.doelId = $_POST[kzlDoel_]
	GROUP BY p2.periId, p2.hokId, p1.doelId, p2.dmafsluit, v.nutat
 ) p  on (p.hokId = ho.hokId)
 left join tblVoeding v on (p.periId = v.periId)
 left join tblInkoop i on (i.inkId = v.inkId)

 left join tblBezet b on (b.hokId = p.hokId)
 left join (
	SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and b.hokId = '".mysqli_real_escape_string($db,$hokId)."'
	GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 left join (
 	SELECT hisId, '".mysqli_real_escape_string($db,$dmbegin)."' datum, h.stalId
 	FROM tblHistorie h
 	 join tblStal st on (h.stalId = st.stalId)
 	WHERE h.skip = 0 and datum <= '".mysqli_real_escape_string($db,$dmbegin)."' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
 
 union

 	SELECT hisId, datum, h.stalId
 	FROM tblHistorie h
 	 join tblStal st on (h.stalId = st.stalId)
 	WHERE h.skip = 0 and datum > '".mysqli_real_escape_string($db,$dmbegin)."' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'

 ) his_in on (b.hisId = his_in.hisId)
 left join (
	SELECT hisId, '".mysqli_real_escape_string($db,$dmeind)."' datum
 	FROM tblHistorie h
 	 join tblStal st on (h.stalId = st.stalId)
 	WHERE h.skip = 0 and datum >= '".mysqli_real_escape_string($db,$dmeind)."' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
 
 union

 	SELECT hisId, datum
 	FROM tblHistorie h
 	 join tblStal st on (h.stalId = st.stalId)
 	WHERE h.skip = 0 and datum < '".mysqli_real_escape_string($db,$dmeind)."' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
 ) his_uit on (uit.hist = his_uit.hisId)
 left join tblStal st on (st.stalId = his_in.stalId)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
 ) spn on (spn.schaapId = st.schaapId)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
 ) prn on (prn.schaapId = st.schaapId)
WHERE ho.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ho.hokId = '".mysqli_real_escape_string($db,$hokId)."' and p.doelId = $_POST[kzlDoel_] and ".db_null_filter('i.artId', $fldVoer)." 
 and date_format(p.dmeind,'%Y%m') = $jrmnd and his_in.datum < p.dmeind and coalesce(his_uit.datum,CURDATE()) > p.dmbegin ".mysqli_real_escape_string($db,$filterDoel)."
 and ".mysqli_real_escape_string($db,$resHok)."
 
GROUP BY p.periId, ho.hokId, ho.hoknr, p.dmbegin, p.dmeind, p.nutat
ORDER BY ho.hokId, p.dmeind
";

//if($mndnr == 6) { echo $periode_totalen.'<br>'.'<br>'; }
$periode_totalen = mysqli_query($db,$periode_totalen) or die (mysqli_error($db));


if (mysqli_num_rows($periode_totalen) == 0) { 

$periode_totalen_met_voer_zonder_schapen = "
SELECT p.periId, ho.hokId, ho.hoknr, p.dmbegin, date_format(p.dmbegin,'%d-%m-%Y') begindm, NULL dmschaap1, NULL schaap1dm,
 p.dmeind, date_format(p.dmeind,'%d-%m-%Y') einddm, NULL dmschaapend, NULL schaapenddm,
 p.nutat, NULL schpn, NULL dagen, NULL gemdgn, count(v.voedId) voedId
FROM tblHok ho
 join (
 	SELECT p.periId, p1.hokId, p1.doelId, p1.dmbegin, p1.dmeind, v.nutat 
 	FROM (
	 	SELECT p.hokId, p.doelId, '".mysqli_real_escape_string($db,$dmstart)."' dmbegin, min(p.dmafsluit) dmeind
		FROM tblPeriode p
		 join tblHok ho on (p.hokId = ho.hokId)
		WHERE ho.lidId = '".mysqli_real_escape_string($db,$lidId)."' and p.doelId = $_POST[kzlDoel_]
		GROUP BY p.hokId, p.doelId
	 ) p1
	 join tblPeriode p on (p1.hokId = p.hokId and p1.doelId = p.doelId and p1.dmeind = p.dmafsluit)
	 left join (
	 	SELECT v.periId, i.artId, sum(v.nutat) nutat
	 	FROM tblVoeding v
	 	 join tblPeriode p on (v.periId = p.periId)
	 	 join tblHok ho on (ho.hokId = p.hokId)
	 	 join tblInkoop i on (v.inkId = i.inkId)
	 	WHERE ho.lidId = '".mysqli_real_escape_string($db,$lidId)."' and p.doelId = $_POST[kzlDoel_]
	 	GROUP BY v.periId, i.artId
	 ) v on (p.periId = v.periId)

	union

	SELECT p2.periId, p2.hokId, p1.doelId, max(p1.dmafsluit) dmbegin, p2.dmafsluit dmeind, v.nutat 
	FROM tblPeriode p1
	 join tblPeriode p2 on (p1.hokId = p2.hokId and p1.doelId = p2.doelId and p1.dmafsluit < p2.dmafsluit)
	 join tblHok ho on (ho.hokId = p1.hokId)
	 left join (
	 	SELECT v.periId, i.artId, sum(v.nutat) nutat
	 	FROM tblVoeding v
	 	 join tblPeriode p on (v.periId = p.periId)
	 	 join tblHok ho on (ho.hokId = p.hokId)
	 	 join tblInkoop i on (v.inkId = i.inkId)
	 	WHERE ho.lidId = '".mysqli_real_escape_string($db,$lidId)."' and p.doelId = $_POST[kzlDoel_]
	 	GROUP BY v.periId, i.artId
	 ) v on (p2.periId = v.periId)
	WHERE ho.lidId = '".mysqli_real_escape_string($db,$lidId)."' and p1.doelId = $_POST[kzlDoel_]
	GROUP BY p2.periId, p2.hokId, p1.doelId, p2.dmafsluit, v.nutat
 ) p  on (p.hokId = ho.hokId)
 left join tblVoeding v on (p.periId = v.periId)
 left join tblInkoop i on (i.inkId = v.inkId)
 
WHERE ho.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ho.hokId = '".mysqli_real_escape_string($db,$hokId)."' and p.doelId = $_POST[kzlDoel_] and ".db_null_filter('i.artId', $fldVoer)." 
 and date_format(p.dmeind,'%Y%m') = $jrmnd
 and '".mysqli_real_escape_string($db,$resHok)."'
 
GROUP BY p.periId, ho.hokId, ho.hoknr, p.dmbegin, p.dmeind, p.nutat
ORDER BY ho.hokId, p.dmeind
";

# echo $periode_totalen_met_voer_zonder_schapen.'<br>';

$periode_totalen = $periode_totalen_met_voer_zonder_schapen;
//echo '<br>'.'<br>'.'<br>'.'<br>'.$periode_totalen.'<br>'.'<br>'.'<br>'.'<br>';


$periode_totalen = mysqli_query($db,$periode_totalen) or die (mysqli_error($db));

}


  while ($mld = mysqli_fetch_assoc($periode_totalen))
		{
		$Id = $mld['periId'];
		$hokId = $mld['hokId'];  
		$hoknr = $mld['hoknr'];
		$dmbegin = $mld['dmbegin'];  // Begindatum periode (= dmafsluit uit tblPeriode)
		$begindm = $mld['begindm'];  	// Begindatum periode (= dmafsluit uit tblPeriode)
		$dmschaap1 = $mld['dmschaap1'];
		$schaap1dm = $mld['schaap1dm']; if($dmschaap1 > $dmbegin) { $begindm = $schaap1dm; }
		$dmeind = $mld['dmeind'];	// Einddatum periode
		$einddm = $mld['einddm'];		// Einddatum periode
		$voerdm = $mld['einddm'];		// Einddatum periode
		$dmschaapend = $mld['dmschaapend'];
		$schaapenddm = $mld['schaapenddm']; if(!empty($dmschaapend) && $dmschaapend < $dmeind) { $einddm = $schaapenddm; }
		$kilo = $mld['nutat']; /*if(!isset($kilo)) { $kilo = 70; }*/
		$schpn = $mld['schpn']; if(empty($schpn)) { $schpn = 0; }
		$dgn = $mld['dagen'];
		$gemdgn = $mld['gemdgn'];
		$voedId = $mld['voedId'];
		 
 ?>
<tr ><td colspan = 25></td></tr>
<?php

if($_POST['kzlDoel_'] == 1) { $filterDoel = ' and (his_in.datum < spn.datum or (isnull(spn.schaapId) and isnull(prn.schaapId)) )'; }
if($_POST['kzlDoel_'] == 2) { $filterDoel = ' and (his_in.datum >= spn.datum or (isnull(spn.schaapId) and prn.schaapId is not null))'; }



/* and  ".$resDel." and ".$resHok." */ ?>


		
<tr align = "center" style = "font-size:15px;">	
 <td><?php echo $hoknr; ?></td>
 <td width = 90 > <?php echo $begindm; ?>  </td>
 <td width = 80 > <?php echo $einddm; ?>  </td>
 <td width = 80 > <input type="text" name = <?php echo "txtDatum_$Id"; ?> style="font-size: 11px;" size =8 value = <?php echo $voerdm; ?> > <?php unset($voerdm); ?> 
 </td>
 <td width = 80 > <?php echo $schpn; ?> </td>	   
 <td width= 100 > <?php echo $gemdgn; ?> </td>
 <td width= 50 > 
<?php if($voedId>0) { ?>
  <input type="text" name = <?php echo "txtKilo_$Id"; ?> style="font-size: 11px; text-align: right; " size =3 value = <?php echo $kilo; ?> > <?php } ?>
 </td>
 <td width = 280 > </td>
 <td width = 200 style = "font-size:12px;" >
<?php if($schpn > 0) { ?>
  <input type = radio name = <?php echo "radSchaap_$Id"; ?> value = 0 
<?php if(!isset($_POST["radSchaap_$Id"]) || (isset($_POST["radSchaap_$Id"]) && $_POST["radSchaap_$Id"] == 0 )) { echo "checked"; } ?> > Excl.
  <input type = radio name = <?php echo "radSchaap_$Id"; ?> value = 1
<?php if(isset($_POST["radSchaap_$Id"]) && $_POST["radSchaap_$Id"] == 1 ) { echo "checked"; } ?> > Incl. schapen
<?php } ?>
</td>
 <td width= 50 >
<?php if($voedId>0) { ?>
  <input type = checkbox name = <?php echo "chbDelVoer_$Id"; ?> value= 1 style = "font-size:11px;" > <?php } ?> 
 </td>
 <td width= 50 > <input type = checkbox name = <?php echo "chbDelPeri_$Id"; ?> value= 1 style = "font-size:11px;" > </td>
	   
</tr>

<?php


// CODE M.B.T. DETAIL SCHAAPGEGEVENS
if(isset($_POST["radSchaap_$Id"]) && $_POST["radSchaap_$Id"]==1 ) { 

	if($gemdgn == 0) { $dagkg = 0; } else { $dagkg = $kilo/$dgn; }  unset($kilo); 

$schaap_gegevens = "
SELECT s.levensnummer, his_in.datum dmin, date_format(his_in.datum,'%d-%m-%Y') indm, coalesce(his_uit.datum,'".mysqli_real_escape_string($db,$dmeind)."') dmuit, date_format(coalesce(his_uit.datum,'".mysqli_real_escape_string($db,$dmeind)."'),'%d-%m-%Y') uitdm, datediff(coalesce(his_uit.datum,'".mysqli_real_escape_string($db,$dmeind)."'),his_in.datum) dgn, round(datediff(coalesce(his_uit.datum,'".mysqli_real_escape_string($db,$dmeind)."'),his_in.datum)*".mysqli_real_escape_string($db,$dagkg).",2) kg
FROM tblBezet b
 join (
 	SELECT h.hisId, h.stalId, '".mysqli_real_escape_string($db,$dmbegin)."' datum
 	FROM tblHistorie h
 	 join tblStal st on (st.stalId = h.stalId)
 	 join tblBezet alleen_his_uit_bez on (alleen_his_uit_bez.hisId = h.hisId)
 	WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.datum < '".mysqli_real_escape_string($db,$dmbegin)."'
 	union 
 	SELECT h.hisId, h.stalId, h.datum
 	FROM tblHistorie h
 	 join tblStal st on (st.stalId = h.stalId)
 	 join tblBezet alleen_his_uit_bez on (alleen_his_uit_bez.hisId = h.hisId)
 	WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.datum >= '".mysqli_real_escape_string($db,$dmbegin)."'
 ) his_in on (his_in.hisId = b.hisId)

 join tblStal st on (st.stalId = his_in.stalId)
 join tblSchaap s on (st.schaapId = s.schaapId)
 left join 
	 (
		SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
		FROM tblBezet b
		 join tblHistorie h1 on (b.hisId = h1.hisId)
		 join tblActie a1 on (a1.actId = h1.actId)
		 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
		 join tblActie a2 on (a2.actId = h2.actId)
		 join tblStal st on (h1.stalId = st.stalId)
		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
		GROUP BY b.bezId, st.schaapId, h1.hisId
	 ) uit on (uit.hisv = b.hisId)
 left join (
 	SELECT h.hisId, h.stalId, '".mysqli_real_escape_string($db,$dmeind)."' datum
 	FROM tblHistorie h
 	 join tblStal st on (st.stalId = h.stalId)
 	WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.datum > '".mysqli_real_escape_string($db,$dmeind)."'
 	union 
 	SELECT h.hisId, h.stalId, h.datum
 	FROM tblHistorie h
 	 join tblStal st on (st.stalId = h.stalId)
 	WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.datum <= '".mysqli_real_escape_string($db,$dmeind)."'
 ) his_uit on (his_uit.hisId = uit.hist)

 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
 ) spn on (spn.schaapId = st.schaapId)
  left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
 ) prn on (prn.schaapId = st.schaapId)
 join tblPeriode p on (p.hokId = b.hokId and p.dmafsluit = '".mysqli_real_escape_string($db,$dmeind)."')
WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."'
 and his_in.datum < '".mysqli_real_escape_string($db,$dmeind)."' and (isnull(uit.bezId) or his_uit.datum > '".mysqli_real_escape_string($db,$dmbegin)."') and p.doelId = $_POST[kzlDoel_] ".mysqli_real_escape_string($db,$filterDoel)."
ORDER BY dmin, dmuit
"; ?>

<tr height = 10><td>	</td></tr>

<?php #echo $schaap_gegevens.'<br>'; 

$schaap_gegevens = mysqli_query($db,$schaap_gegevens) or die (mysqli_error($db));

	while($sch = mysqli_fetch_array($schaap_gegevens)) { 
		$levnr = $sch['levensnummer']; 
		$dmin = $sch['dmin']; 
		$indm = $sch['indm']; 	if($dmin < $dmbegin) { $indm = $begindm; }
		$dmuit = $sch['dmuit']; 
		$uitdm = $sch['uitdm']; if($dmuit > $dmeind) { $uitdm = $einddm; }
		$dgn = $sch['dgn']; 
		$kg = $sch['kg'];







		?> 
<tr align = "center" style = "font-size:15px;"> 
<td></td>
<td width = 80 >  <?php echo $indm; ?> </td>
<td width = 80 >  <?php echo $uitdm; ?> </td>
<td width = 80 >   </td>
<td width = 80 align = right>  <?php echo $levnr; ?> </td>

<td width = 80 >  <?php echo $dgn; ?> </td>
<td> <?php echo $kg; ?> </td>
<td>  </td>
<td> </td>

</tr>
 <?php 
  } unset($aant); unset($dagen);  unset($begindm); unset($einddm);
 // EINDE CODE M.B.T. DETAIL SCHAAPGEGEVENS ?>

<?php  } ?>

<tr><td colspan = 25><hr></td></tr>

<?php
} unset($periode_totalen); // Einde $periode_totalen ?>




<?php
unset($begindm); unset($einddm); } // Einde $begin_eind_periode
#echo /*$_POST["radSchaap_$Id"]*/$hoknr.' - '. $Id; ?>
			







<?php 





}  // EINDE LOOP maandnaam jaartal
	
} //  Einde knop toon ?>			
</table>
</form>
		</TD>
<?php } else { ?> <img src='Voer_rapportage_php.jpg'  width='970' height='550'/> <?php }
include "menuRapport.php"; } ?>
</body>
</html>
