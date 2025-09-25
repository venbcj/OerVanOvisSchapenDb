<?php /* 28-11-2014 Chargenummer toegevoegd  
11-3-2015 : Login toegevoegd */
$versie = '25-11-2016';  /* actId = 3 uit on clause gehaald en als sub query genest */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '5-7-2020'; /* wdgn gewijzigd naar wdgn_v en wdgn_m */
$versie = '29-4-2023'; /* sql beveiligd met quotes */
$versie = '31-12-2023'; /* and h.skip = 0 aangevuld aan tblHistorie */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = "top"> 31-12-24 Include "login.php"; voor Include "header.php" gezet */

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Rapport</title>
</head>
<body>

<?php
$titel = 'Rapportage per medicijn';
$file = "Med_rapportage.php";
Include "login.php"; ?>

				<TD valign = "top">
<?php
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { if($modtech ==1) {

function aantal_fase($datb,$lidid,$M,$J,$V,$Sekse,$Ouder) { // Functie die het aantal lammeren, moederdieren of vaders telt
$vw_totaalFase = mysqli_query($datb,"
SELECT count(distinct s.levensnummer) werknrs
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
 join tblNuttig n on (h.hisId = n.hisId)
 join tblInkoop i on (n.inkId = i.inkId)
 left join (
	SELECT st.schaapId, h.hisId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) oudr on (s.schaapId = oudr.schaapId)
WHERE h.skip = 0 and month(h.datum) = $M and date_format(h.datum,'%Y') = $J and i.artId = $V and ".$Sekse." and ".$Ouder."
	and st.lidId = '".mysqli_real_escape_string($datb,$lidid)."' and h.actId = 8
GROUP BY date_format(h.datum,'%Y%m')
");

if($vw_totaalFase)
		{	$row = mysqli_fetch_assoc($vw_totaalFase);
	            return $row['werknrs'];
		}
		return FALSE; // Foutafhandeling
}

function voer_fase($datb,$lidid,$M,$J,$V,$Sekse,$Ouder) { // Functie die de hoeveelheid voer berekend per lammeren, moederdieren of vaders
$vw_totaalFase = mysqli_query($datb,"
SELECT round(sum(n.nutat*n.stdat),2) totats
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
 join tblNuttig n on (h.hisId = n.hisId)
 join tblInkoop i on (n.inkId = i.inkId)
 left join (
	SELECT st.schaapId, h.hisId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) oudr on (s.schaapId = oudr.schaapId)
WHERE month(h.datum) = $M and date_format(h.datum,'%Y') = $J and i.artId = $V and ".$Sekse." and ".$Ouder."
 and st.lidId = '".mysqli_real_escape_string($datb,$lidid)."' and h.skip = 0
GROUP BY concat(date_format(h.datum,'%Y'),month(h.datum))
");

if($vw_totaalFase)
		{	$row = mysqli_fetch_assoc($vw_totaalFase);
	            return $row['totats'];
		}
		return FALSE; // Foutafhandeling
}

function eenheid_fase($datb,$lidid,$M,$J,$V,$Sekse,$Ouder) { // Functie die de eenheid ophaalt per lammeren, moederdieren of vaders
$vw_totaalFase = mysqli_query($datb,"
SELECT e.eenheid 
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (eu.enhuId = a.enhuId)
 join tblInkoop i on (a.artId = i.artId)
 join tblNuttig n on (n.inkId = i.inkId)
 join tblHistorie h on (h.hisId = n.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 left join (
	SELECT st.schaapId, h.hisId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) oudr on (s.schaapId = oudr.schaapId)
WHERE h.skip = 0 and eu.lidId = '".mysqli_real_escape_string($datb,$lidid)."' and month(h.datum) = $M and date_format(h.datum,'%Y') = $J and i.artId = $V and ".$Sekse." and ".$Ouder."
GROUP BY e.eenheid
");

if($vw_totaalFase)
		{	$row = mysqli_fetch_assoc($vw_totaalFase);
	            return $row['eenheid'];
		}
		return FALSE; // Foutafhandeling
}
$minjaar = date("Y")-8;
$maxjaar = date("Y");
if(isset($_POST['knpToon'])) {$kzlpil = $_POST['kzlpil'];}

if(isset($kzlpil)) {
$JrMndPil  =
("
SELECT date_format(h.datum,'%Y%m') jrmnd, month(h.datum) mnd, date_format(h.datum,'%Y') jaar
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (eu.enhuId = a.enhuId)
 join tblInkoop i on (a.artId = i.artId)
 join tblNuttig n on (n.inkId = i.inkId)
 join tblHistorie h on (h.hisId = n.hisId)
WHERE h.skip = 0 and eu.lidId = '".mysqli_real_escape_string($db,$lidId)."' and date_format(h.datum,'%Y') >= '".$minjaar."' and date_format(h.datum,'%Y') <= '".$maxjaar."' and a.artId = '".$kzlpil."'
GROUP BY date_format(h.datum,'%Y%m')
ORDER BY date_format(h.datum,'%Y%m') desc
");
} ?>

<table Border = 0 align = "center">

<?php
$kzl = mysqli_query($db,"
SELECT a.artId, a.naam
FROM tblEenheiduser eu
 join tblArtikel a on (eu.enhuId = a.enhuId)
 join tblInkoop i on (a.artId = i.artId)
 join tblNuttig n on (n.inkId = i.inkId)
WHERE eu.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a.soort = 'pil'
GROUP BY a.naam
ORDER BY a.naam
") or die (mysqli_error($db));
?>
<form action = "Med_rapportage.php" method = "post">
<tr> <td> </td>

<td colspan = 4 style = "font-size : 13px;">
<?php
/*$minjaar = date("Y")-2;
$maxjaar = date("Y");*/

if (isset($_POST['knpToon']) && !empty($_POST['kzlpil'])) {
$kzlpil = $_POST['kzlpil'];

$aantperiodes = mysqli_query($db,"SELECT tbl.jrmnd FROM (".$JrMndPil.") tbl	") or die (mysqli_error($db));
   $rows_per = mysqli_num_rows($aantperiodes);
		if ($rows_per >1) {
echo "Mogelijkheid filter periode " ;
 //kzlJaarMaand
// Verzameld alle jaarmaanden van een toegediend medicijn. 
$kzljrmnd = mysqli_query($db,$JrMndPil) or die (mysqli_error($db)); 

$name = "kzlmdjr";
$width= 108 ; ?>
<select name=<?php echo"$name";?> style="font-size : 13px width:<?php echo "$width";?>;\" >
 <option></option>
		
<?php		while($row = mysqli_fetch_array($kzljrmnd))
		  { $maand = $row['mnd']; 
				$mndname = array('','januari', 'februari', 'maart','april','mei','juni','juli','augustus','september','oktober','november','december');
			$jaar = $row['jaar'];
$kzlkey="$row[jrmnd]";
$kzlvalue="$mndname[$maand] $row[jaar]";


include "kzl.php";
		}
}
}
// EINDE kzlJaarMaand
?>
</select> 

 </td>

<td colspan = 3> 
<?php
$label = "Kies een medicijn &nbsp " ;
If (isset($_POST['knpToon']) && !empty($_POST['kzlpil'])) {	$label = ""; }
echo $label;

//kzlMedicijn
$name = "kzlpil";
$width= 200 ; ?>
<select name=<?php echo"$name";?> style="width:<?php echo "$width";?>;\" >
 <option></option>
<?php		while($row = mysqli_fetch_array($kzl))
		{
$kzlkey="$row[artId]";
$kzlvalue="$row[naam]";

include "kzl.php";
		}
// EINDE kzlMedicijn
?>
</select> 
 </td>
 
 
 <td colspan = 2> <input type = "submit" name ="knpToon" value = "Toon"> </td></tr>	
</form>


<tr>
<td> </td>
<td>
<?php
If (isset($_POST['knpToon']) && !empty($_POST['kzlpil']) ) {
	if ($rows_per <= 1 || empty($_POST['kzlmdjr'])) { $resJrmnd = "( date_format(h.datum,'%Y%m') is not null )"; }
	else if ($rows_per > 1 && !empty($_POST['kzlmdjr'])) { $resJrmnd = "( date_format(h.datum,'%Y%m') = $_POST[kzlmdjr] )"; }

//$maandjaren verzameld alle maandjaren die worden gevonden
$maandjaren = "
SELECT month(h.datum) mnd, date_format(h.datum,'%Y') jaar 
FROM tblEenheiduser eu
 join tblArtikel a on (eu.enhuId = a.enhuId)
 join tblInkoop i on (a.artId = i.artId)
 join tblNuttig n on (n.inkId = i.inkId)
 join tblHistorie h on (h.hisId = n.hisId)
WHERE h.skip = 0 and eu.lidId = '".mysqli_real_escape_string($db,$lidId)."' and date_format(h.datum,'%Y') >= '".$minjaar."' and date_format(h.datum,'%Y') <= '".$maxjaar."' and i.artId = '".$_POST[kzlpil]."' and ".$resJrmnd."
GROUP BY month(h.datum), date_format(h.datum,'%Y')
ORDER BY date_format(h.datum,'%Y') desc, month(h.datum) desc ";

$maandjaren = mysqli_query($db,$maandjaren) or die (mysqli_error($db));

  while ($rij = mysqli_fetch_assoc($maandjaren))
		{
		$mndnr = $rij['mnd'];
		$jr = $rij['jaar'];
		
$mndnaam = array('','januari', 'februari', 'maart','april','mei','juni','juli','augustus','september','oktober','november','december'); 
		
$tot = date("Ym"); 
	$maand = date("m");
	$jaarstart = date("Y")-8;
//$vanaf = "$jaarstart$maand";
?>
<tr style = "font-size:18px;" ><td></td><td colspan = 3><b><?php echo "$mndnaam[$mndnr] &nbsp $rij[jaar]"; ?></b></td></tr>
<tr style = "font-size:12px;">
<tr><td colspan = 9><hr></td></tr>
<?php

// TOTALEN
$sekse = 's.geslacht is not null';
$ouder = 'isnull(oudr.hisId)';
$werknrs = aantal_fase($db,$lidId,$mndnr,$jr,$kzlpil,$sekse,$ouder);
	if ($werknrs == 1) {$fasen = 'lam';} else if(isset($werknrs))	{$fasen = 'lammeren';}
$voer = voer_fase($db,$lidId,$mndnr,$jr,$kzlpil,$sekse,$ouder);
$eenheid = eenheid_fase($db,$lidId,$mndnr,$jr,$kzlpil,$sekse,$ouder);
?>
		
<tr align = "center">	
 <td width = 0> </td>	   
 <td width = 100 style = "font-size:15px;"><b></b><br> </td>	   
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"><b> <?php echo $werknrs; ?> </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"><b> <?php if(isset($fasen)) { echo $fasen; }; ?> </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <b> <?php if(isset($voer)) { echo $voer." ".$eenheid; }; ?> </b><br> </td>
 <td width = 1> </td>	
 <td width = 100 style = "font-size:15px;" align = "right"><b>  </b><br> </td>
</tr> <?php 



unset($fasen); 
$sekse = 's.geslacht = \'ooi\'';
$ouder = 'oudr.hisId is not null';
$werknrs = aantal_fase($db,$lidId,$mndnr,$jr,$kzlpil,$sekse,$ouder);
	if ($werknrs == 1) {$fasen = 'moederdier';} else if(isset($werknrs))	{$fasen = 'moederdieren';}
$voer = voer_fase($db,$lidId,$mndnr,$jr,$kzlpil,$sekse,$ouder);
$eenheid = eenheid_fase($db,$lidId,$mndnr,$jr,$kzlpil,$sekse,$ouder);
	?>
<tr align = "center">	
 <td width = 0> </td>	   
 <td width = 100 style = "font-size:15px;"> <b> </b><br> </td>	   
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <b> <?php echo $werknrs; ?> </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <b> <?php if(isset($fasen)) { echo $fasen; }; ?> </b><br> </td>
 <td width = 1> </td>	  
 <td width = 100 style = "font-size:15px;"> <b> <?php if(isset($voer)) { echo $voer." ".$eenheid; }; ?> </b><br> </td>
 <td width = 1> </td>	 
 <td width = 100 style = "font-size:15px;" align = "right"> <b>  </b><br> </td>
</tr> <?php  



unset($fasen); 
$sekse = 's.geslacht = \'ram\'';
$ouder = 'oudr.hisId is not null';
$werknrs = aantal_fase($db,$lidId,$mndnr,$jr,$kzlpil,$sekse,$ouder);
	if ($werknrs == 1) {$fasen = 'vaderdier';} else if(isset($werknrs))	{$fasen = 'vaderdieren';}
$voer = voer_fase($db,$lidId,$mndnr,$jr,$kzlpil,$sekse,$ouder);
$eenheid = eenheid_fase($db,$lidId,$mndnr,$jr,$kzlpil,$sekse,$ouder);
	?>
<tr align = "center">	
 <td width = 0> </td>	   
 <td width = 100 style = "font-size:15px;"> <b> </b><br> </td>	   
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <b> <?php echo $werknrs; ?> </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <b> <?php if(isset($fasen)) { echo $fasen; }; ?> </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <b> <?php if(isset($voer)) { echo $voer." ".$eenheid; }; ?> </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;" align = "right"> <b> </b><br> </td>
</tr> <?php  
// EINDE TOTALEN

 ?>
<tr><td colspan = 25><hr></td></tr>
<tr ><td colspan = 25></td></tr>
<?php


$result = "
SELECT date_format(h.datum,'%Y%m') jrmnd, date_format(h.datum,'%Y') jaar, month(h.datum) maand, 
 right(s.levensnummer,$Karwerk) werknr, s.geslacht, oudr.hisId ouder, 
 date_format(h.datum,'%d-%m-%Y') toedm, h.datum, DATEDIFF(CURRENT_DATE(),h.datum) rest, round(sum(n.nutat*n.stdat),2) totat, e.eenheid,
 i.charge, a.wdgn_v, a.wdgn_m
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
 join tblNuttig n on (h.hisId = n.hisId)
 join tblInkoop i on (n.inkId = i.inkId)
 join tblArtikel a on (i.artId = a.artId)
 join tblEenheiduser eu on (eu.enhuId = a.enhuId)
 join tblEenheid e on (e.eenhId = eu.eenhId)
 left join (
	SELECT st.schaapId, h.hisId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) oudr on (s.schaapId = oudr.schaapId)
WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and month(h.datum) = '".$mndnr."' and year(h.datum) = '".$rij[jaar]."' and a.artId = '".$_POST[kzlpil]."'
GROUP BY date_format(h.datum,'%Y%m'), date_format(h.datum,'%Y'), month(h.datum), right(s.levensnummer,$Karwerk), s.geslacht, oudr.hisId,
 date_format(h.datum,'%d-%m-%Y'), h.datum, DATEDIFF(CURRENT_DATE(),h.datum), e.eenheid, i.charge, a.wdgn_v, a.wdgn_m
ORDER BY h.datum desc, right(s.levensnummer,$Karwerk)
";

$result = mysqli_query($db,$result) or die (mysqli_error($db));  


?>

<th width = 0 height = 30></th>
<th style = "text-align:center;"valign="bottom";width= 100>Werknr<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Generatie <hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Datum<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Hoeveelheid<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Eenheid<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Chargenr<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Wachtdagen resterend <br> vlees &nbsp&nbsp&nbsp melk <hr></th>
<th width = 1></th>

<th width=60></th>
 </tr>

<?php
while($row = mysqli_fetch_array($result)) {
$rest = $row['rest'];
$wdgn_v = $row['wdgn_v']; if ($wdgn_v > $rest) {$restdgn_v = $wdgn_v-$rest; } else {$restdgn_v = "geen"; }

$wdgn_m = $row['wdgn_m']; if ($wdgn_m > $rest) {$restdgn_m = $wdgn_m-$rest; } else {$restdgn_m = "geen"; }

$geslacht = $row['geslacht'];
if(!empty($row['ouder'])) { if($geslacht == 'ooi') {$fase = 'moeder'; } else if($geslacht == 'ram') {$fase = 'vader'; } } else {$fase = 'lam'; } ?>
		
<tr align = "center">	
 <td width = 0> </td>	   
 <td width = 100 style = "font-size:15px;"> <?php echo $row['werknr']; ?> <br> </td>	   
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $fase; ?> <br> </td>
 <td width = 1> </td>

 <td width = 100 style = "font-size:15px;"> <?php echo $row['toedm']; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $row['totat']; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $row['eenheid']; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $row['charge']; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $restdgn_v.' &nbsp&nbsp&nbsp&nbsp '.$restdgn_m; ?> <br> </td>
 <td width = 1> </td>
 <td width = 50> </td>
</tr>				
<?php		} ?>
<tr style = "height : 100px;"><td colspan = 25></td></tr>
<?php
}
	
} //  Einde knop toon ?>			
</table>
		</TD>
<?php } else { ?> <img src='med_rapportage_php.jpg'  width='970' height='550'/> <?php }
Include "menuRapport.php"; } ?>
</body>
</html>
