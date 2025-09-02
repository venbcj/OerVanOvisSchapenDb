<?php 
$versie = '24-10-2015'; /*gemaakt */
$versie = '6-12-2015'; /*Totaal per hoofdrubriek verbeterd deze klopte niet*/
$versie = '19-12-2015'; /*Hoofdrubrieken gesorteerd */
$versie = '24-12-2016'; /* Eindsaldo toegevoegd */
$versie = '25-12-2016'; /* Rubriek 'aankoop vaderdier' toegevoegd en rubrieken gesorteerd */
$versie = '4-01-2017'; /* Totaal bedragen verdeeld over meerdere regels. Van 2 regels naar 4 */
$versie = '20-02-2017'; /* Bij nieuwe gebruikers zonder gegevens in tblLiquiditeit pagina juist opgebouwd. Zie script tussen  if(isset($last_jaar)) { 


UItleg Liquiditeit :

Bedragen Verplichtingen */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '11-7-2020'; /* € gewijzigd in &euro; 12-7 ë uit database gewijzigd in echo htmlentities($string, ENT_COMPAT,'ISO-8859-1', true); bron https://www.php.net/htmlspecialchars via https://www.phphulp.nl/php/forum/topic/speciale-tekens-in-code-omzetten/50786/ */
$versie = '26-12-2024'; /* <TD width = 1010 height = 400 valign = "top" > gewijzigd naar <TD valign = "top"> 31-12-24 Include "login.php"; voor Include "header.php" gezet */
$versie = '11-03-2025'; /* Het hidden veld type = 'hidden' name = <?php echo "txtM_$Id"."_$i"; ?> verwijderd. txtM_$Id"."_$i wordt alleen getoond als $tesktveld == 'tonen' */

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Financieel</title>
</head>
<body>

<?php
$titel = 'Liquiditeit';
$file = "Liquiditeit.php";
Include "login.php"; ?>

				<TD valign = "top">
<?php
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { if($modfin == 1) {

include "func_euro.php";

// Zoeken naar laatste jaar in tblLiquiditeit
$laatste_jaar = mysqli_query($db,"
SELECT max(year(datum)) jaar
FROM tblLiquiditeit l
 join tblRubriekuser ru on (l.rubuId = ru.rubuId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die (mysqli_error($db));
	while ($lst = mysqli_fetch_assoc($laatste_jaar)) { $last_jaar = $lst['jaar'];}
	if(isset($last_jaar)) { $new_jaar = $last_jaar+1; $knptype = 'submit'; } else { $new_jaar = date('Y'); $knptype = 'hidden'; }

If(isset($_POST['kzlJaar___'])) {	$toon_jaar = $_POST['kzlJaar___']; } else if(isset($last_jaar) && $last_jaar<date('Y')) { $toon_jaar = $last_jaar; } else if(isset($last_jaar)) { $toon_jaar = date('Y'); }
if(isset($_POST['knpCreate___'])) {
	
include "create_liquiditeit.php"; } //$new_jaar wordt hier gebruikt.
if(isset($_POST['knpSave___'])) { include "save_liquiditeit.php"; }  //toon_jaar wordt hier gebruikt
 ?>

<form action = "Liquiditeit.php" method = "post">
<table border = 0>
<tr>
 <td>
	<input type = submit name = 'knpCreate___' value = <?php echo $new_jaar."_aanmaken"; ?> >
 </td>
 <td width = 350> </td> <?php
// Declaratie JAAR
$qryJaar = mysqli_query($db,"
SELECT year(datum) jaar
FROM tblLiquiditeit l
 join tblRubriekuser ru on (l.rubuId = ru.rubuId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."'
GROUP BY year(datum)
ORDER BY year(datum)
") or die (mysqli_error($db));  

$index = 0; 
while ($jr = mysqli_fetch_assoc($qryJaar)) 
{ 
   $jaar[$index] = $jr['jaar'];
   $jaarRaak[$index] = $toon_jaar;
   $index++; 
}
unset($index); 
// EINDE Declaratie JAAR ?>


 <td style = "text-align:center;"valign= 'bottom'; width= 80 ><h3>Jaar</h3> </td>
 <td valign = "top" style = "font-size : 11px;">
<!-- KZLJAAR -->
<?php $width = 65 ; ?>
 <select style= "width:<?php echo $width; ?>;" name = "kzlJaar___" style = "font-size:12px;">
<?php	$count = count($jaar);
for ($i = 0; $i < $count; $i++){

	$opties = array($jaar[$i]=>$jaar[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((!isset($_POST['knpToon___']) && $jaarRaak[$i] == $key) || (isset($_POST['kzlJaar___']) && $_POST['kzlJaar___'] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}
?> </select>
	<!-- EINDE KZLJAAR --> </td>
 <td valign = "top" ><input type = submit name = "knpToon___" value = 'Toon' ></td>
 <td width = 400 valign = "top" align = 'right'><input type = <?php echo $knptype; ?> name = "knpSave___" value = 'Opslaan' ></td>
</tr>
</table>
<?php if(isset($last_jaar)) { ?>
<!-- RIJKOPPEN --> 

 <table border = 0>

 <?php 
$qryHoofdRubriek = mysqli_query($db,"
SELECT hr.rubhId, hr.rubriek hrub
FROM tblRubriekhfd hr
 join tblRubriek r on (hr.rubhId = r.rubhId)
 join tblRubriekuser ru on (r.rubId = ru.rubId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ru.actief = 1
GROUP BY hr.rubhId, hr.rubriek
ORDER BY hr.sort, hr.rubhId
") or die (mysqli_error($db)); 
	while ($rb = mysqli_fetch_assoc($qryHoofdRubriek)) { $hrub = $rb['hrub']; $rubhId = $rb['rubhId'];

	
	$mndnr = array('','Jan.', 'Feb.', 'Mrt.','Apr.','Mei','Jun.','Jul.','Aug.','Sep.','Okt.','Nov.','Dec.');
	$mndstr = array('','01', '02', '03','04','05','06','07','08','09','10','11','12'); ?>

<tr height = 70 valign = 'bottom'><td><b><?php echo htmlentities($hrub, ENT_COMPAT,'ISO-8859-1', true); ?> <b></td>
 <td></td>
<?php for ($i = 1; $i<=12; $i++)
{ ?>
 <td align = "center" style = "color : <?php if(date('Yn') == $toon_jaar.$i) { echo 'blue'; } ?> " > <i> <?php echo $mndnr[$i]; ?> <hr></i></td>

<?php } ?>
 <td align = "center" width = 80><i>Totaal<hr></i></td></tr>
<?php // Rubriek ophalen
$qryRubriek = mysqli_query($db,"
SELECT ru.rubuId, r.rubId, r.rubriek
FROM tblRubriek r
 join tblRubriekuser ru on (r.rubId = ru.rubId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ru.actief = 1 and r.rubhId = '$rubhId'
ORDER BY r.rubriek
") or die (mysqli_error($db)); 
	while ($rub = mysqli_fetch_assoc($qryRubriek)) { $rubuId = $rub['rubuId'];  $rubId = $rub['rubId']; $rubriek = $rub['rubriek']; 

// Per rubriek alle maanden ophalen

//include "vw_Liquiditeit.php"; ?>





<tr style = "font-size : 14px">
 <td><?php echo $rubriek; ?> </td>
 <td style = "color : grey"><?php if($rubId != 39) { ?> &euro; <?php } ?></td>
 
<?php 
for ($i = 1; $i<=12; $i++)
{ 
 $zoek_realiteit = mysqli_query($db,"
SELECT li.rubuId, o.bedrag, 'realisatie' status
FROM tblLiquiditeit li
join (
	SELECT rubuId, date_format(datum,'%Y%m') jrmnd, sum(bedrag) bedrag 
	FROM tblOpgaaf
	WHERE month(datum) = '".mysqli_real_escape_string($db,$i)."' and year(datum) = '".mysqli_real_escape_string($db,$toon_jaar)."' and liq = 1
	GROUP BY rubuId, date_format(datum,'%Y%m') 
) o on (li.rubuId = o.rubuId and date_format(li.datum,'%Y%m') = o.jrmnd)
WHERE li.rubuId = '".mysqli_real_escape_string($db,$rubuId)."' and month(li.datum) = '".mysqli_real_escape_string($db,$i)."' and year(li.datum) = '".mysqli_real_escape_string($db,$toon_jaar)."'
") or die (mysqli_error($db));

	while ($zr = mysqli_fetch_assoc($zoek_realiteit)) {
		$Id = $zr['rubuId'];
		$mndprijs = $zr['bedrag'];
		$status = $zr['status'];
	}


$zoek_begroting = mysqli_query($db,"
SELECT li.rubuId, li.bedrag, 'begroot' status
FROM tblLiquiditeit li
left join (
	SELECT rubuId, date_format(datum,'%Y%m') jrmnd, sum(bedrag) bedrag 
	FROM tblOpgaaf
	WHERE month(datum) = '".mysqli_real_escape_string($db,$i)."' and year(datum) = '".mysqli_real_escape_string($db,$toon_jaar)."' and liq = 1 and rubuId = '".mysqli_real_escape_string($db,$rubuId)."'
	GROUP BY rubuId, date_format(datum,'%Y%m') 
) o on (li.rubuId = o.rubuId and date_format(li.datum,'%Y%m') = o.jrmnd)
WHERE li.rubuId = '".mysqli_real_escape_string($db,$rubuId)."' and month(li.datum) = '".mysqli_real_escape_string($db,$i)."' and year(li.datum) = '".mysqli_real_escape_string($db,$toon_jaar)."' and isnull(o.bedrag)
") or die (mysqli_error($db)); 


	while ($zb = mysqli_fetch_assoc($zoek_begroting)) {
		$Id = $zb['rubuId'];
		$mndprijs = $zb['bedrag'];
		$status = $zb['status'];
	}
# *********************************
 	
	if($status == 'realisatie') {$tesktveld = 'verbergen'; $align = 'right'; $color = 'green'; $value = euro_format($mndprijs);} // Als er posten zijn
	else if(empty($mndprijs) && (date('Ym')>$toon_jaar.$mndstr[$i] || $rubId == 39)) {$tesktveld = 'verbergen'; $align = 'center'; $color = 'black'; $value = '-';} // Als liquiditeit leeg is
	else if($rubId == 39 && !empty($mndprijs) && date('Ym')>$toon_jaar.$mndstr[$i]) {$tesktveld = 'verbergen'; $align = 'right'; $color = 'grey'; $value = euro_format($mndprijs);} // Als rubriek = Verkoop lammeren en verleden
	else if($rubId == 39 && !empty($mndprijs)) {$tesktveld = 'verbergen'; $align = 'right'; $color = 'black'; $value = euro_format($mndprijs);} // Als rubriek = Verkoop lammeren niet verleden
	else if($status == 'begroot' && date('Ym')>$toon_jaar.$mndstr[$i]) {$tesktveld = 'tonen'; $align = 'right'; $color = 'grey'; unset($value);} // Als er liquiditeit is
	else {$tesktveld = 'tonen'; $align = 'right'; $color = 'black'; unset($value);} // Anders ?>
 
 
 
 
 <td width = 62 align = <?php echo $align; ?> style = "color : <?php echo $color; ?>" ><!-- MAANDEN --> 
 <?php if($tesktveld == 'tonen') { ?> 
 <input type = 'text' name = <?php echo "txtM_$Id"."_$i"; ?> size = 6 style = "font-size : 12px; text-align : right; color : <?php echo $color; ?>  " value = <?php echo $mndprijs; ?> >
 <?php }

 if(isset($value)) { echo $value; } ?>
 </td>
<?php 
	
if(isset($tota)) {$tota = $tota+$mndprijs; } else { $tota = $mndprijs; }
	}	 ?> 	


<!-- SUBTOTALEN -->
 <td width = 80 align = 'right'><!-- Totaal--> <?php if(isset($tota) && $tota > 0) { echo euro_format($tota); } ?> </td>
 <!-- Einde SUBTOTALEN -->
<?php if(isset($totaal)) {$totaal = $totaal+$tota; } else {$totaal = $tota; } unset($tota); 
	} // Einde rubrieken ?> </tr>
<tr>
 <td colspan = 15><hr></td>
</tr>
<!-- TOTALEN -->
<tr>
 <td colspan = 15 align = 'right' style= "font-size : 14px;"><b> <?php echo 'Totale '.htmlentities($hrub, ENT_COMPAT,'ISO-8859-1', true).'&nbsp&nbsp '.euro_format($totaal); unset($totaal); ?> </b></td>
</tr>
 <!-- Einde TOTALEN -->
 <?php } // Einde hoofdrubrieken ?>
 
<!-- EINDE RIJKOPPEN -->
<?php
$qryTotaalMaandBedragen = mysqli_query($db,"
SELECT jaarmnd, sum(bedrag) bedrag
FROM (
	SELECT date_format(l.datum,'%Y%m') jaarmnd, sum(l.bedrag) bedrag
	FROM tblLiquiditeit l
	 join tblRubriekuser ru on (l.rubuId = ru.rubuId)
	 join tblRubriek r on (r.rubId = ru.rubId)
	 left join tblOpgaaf o on (l.rubuId = o.rubuId and date_format(l.datum,'%Y%m') = date_format(o.datum,'%Y%m') )
	WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ru.actief = 1 and isnull(o.opgId) and year(l.datum) = '".mysqli_real_escape_string($db,$toon_jaar)."' and r.rubhId = 5
	GROUP BY date_format(l.datum,'%Y%m')

	union

	SELECT date_format(l.datum,'%Y%m') jaarmnd, -sum(l.bedrag) bedrag
	FROM tblLiquiditeit l
	 join tblRubriekuser ru on (l.rubuId = ru.rubuId)
	 join tblRubriek r on (r.rubId = ru.rubId)
	 left join tblOpgaaf o on (l.rubuId = o.rubuId and date_format(l.datum,'%Y%m') = date_format(o.datum,'%Y%m') )
	WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ru.actief = 1 and isnull(o.opgId) and year(l.datum) = '".mysqli_real_escape_string($db,$toon_jaar)."' and r.rubhId <> 5
	GROUP BY date_format(l.datum,'%Y%m')

	union

	SELECT date_format(o.datum,'%Y%m') jaarmnd, sum(bedrag) bedrag
	FROM tblOpgaaf o
	 join tblRubriekuser ru on (o.rubuId = ru.rubuId)
	 join tblRubriek r on (r.rubId = ru.rubId)
	WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ru.actief = 1 and year(o.datum) = '".mysqli_real_escape_string($db,$toon_jaar)."'  and r.rubhId = 5
	GROUP BY date_format(o.datum,'%Y%m')

	union

	SELECT date_format(o.datum,'%Y%m') jaarmnd, -sum(bedrag) bedrag
	FROM tblOpgaaf o
	 join tblRubriekuser ru on (o.rubuId = ru.rubuId)
	 join tblRubriek r on (r.rubId = ru.rubId)
	WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ru.actief = 1 and year(o.datum) = '".mysqli_real_escape_string($db,$toon_jaar)."'  and r.rubhId <> 5
	GROUP BY date_format(o.datum,'%Y%m')
) a
GROUP BY jaarmnd

") or die (mysqli_error($db)); 
	while ($mndprs = mysqli_fetch_assoc($qryTotaalMaandBedragen)) {
		$mndtot[] = $mndprs['bedrag']; } 

 

for ($i=1; $i<=12; $i++)
{
$qryCummMaandBedragen = mysqli_query($db,"
SELECT sum(bedrag) bedrag
FROM (
	SELECT date_format(l.datum,'%Y%m') jaarmnd, sum(l.bedrag) bedrag
	FROM tblLiquiditeit l
	 join tblRubriekuser ru on (l.rubuId = ru.rubuId)
	 join tblRubriek r on (r.rubId = ru.rubId)
	 left join tblOpgaaf o on (l.rubuId = o.rubuId and date_format(l.datum,'%Y%m') = date_format(o.datum,'%Y%m') )
	WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ru.actief = 1 and isnull(o.opgId) and year(l.datum) = '".mysqli_real_escape_string($db,$toon_jaar)."' and month(l.datum) <= '".mysqli_real_escape_string($db,$i)."' and r.rubhId = 5
	GROUP BY date_format(l.datum,'%Y%m')

	union

	SELECT date_format(l.datum,'%Y%m') jaarmnd, -sum(l.bedrag) bedrag
	FROM tblLiquiditeit l
	 join tblRubriekuser ru on (l.rubuId = ru.rubuId)
	 join tblRubriek r on (r.rubId = ru.rubId)
	 left join tblOpgaaf o on (l.rubuId = o.rubuId and date_format(l.datum,'%Y%m') = date_format(o.datum,'%Y%m') )
	WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ru.actief = 1 and isnull(o.opgId) and year(l.datum) = '".mysqli_real_escape_string($db,$toon_jaar)."' and month(l.datum) <= '".mysqli_real_escape_string($db,$i)."' and r.rubhId <> 5
	GROUP BY date_format(l.datum,'%Y%m')

	union

	SELECT date_format(o.datum,'%Y%m') jaarmnd, sum(bedrag) bedrag
	FROM tblOpgaaf o
	 join tblRubriekuser ru on (o.rubuId = ru.rubuId)
	 join tblRubriek r on (r.rubId = ru.rubId)
	WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ru.actief = 1 and year(o.datum) = '".mysqli_real_escape_string($db,$toon_jaar)."' and month(o.datum) <= '".mysqli_real_escape_string($db,$i)."' and r.rubhId = 5
	GROUP BY date_format(o.datum,'%Y%m')

	union

	SELECT date_format(o.datum,'%Y%m') jaarmnd, -sum(bedrag) bedrag
	FROM tblOpgaaf o
	 join tblRubriekuser ru on (o.rubuId = ru.rubuId)
	 join tblRubriek r on (r.rubId = ru.rubId)
	WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ru.actief = 1 and year(o.datum) = '".mysqli_real_escape_string($db,$toon_jaar)."' and month(o.datum) <= '".mysqli_real_escape_string($db,$i)."' and r.rubhId <> 5
	GROUP BY date_format(o.datum,'%Y%m')
) a

") or die (mysqli_error($db)); 
	//$cumtot = array();
	while ($cumprs = mysqli_fetch_assoc($qryCummMaandBedragen)) {
		$cumtot[] = $cumprs['bedrag']; } 
// $inkId = array();		
//while( $ink = mysqli_fetch_assoc($zoek_inkId)) { $inkId[] = $ink['inkId']; 
 } ?>


<tr style = "font-size : 14px;">
 <td rowspan = 2>mutatie liquide middelen</td>
<?php for ($i=0; $i<12; $i++)
{ if ($i % 2 == 0) { ?> <td colspan = 2 align=right> <?php echo euro_format($mndtot[$i]); ?> </td> <?php } } ?>
</tr>

<tr style = "font-size : 14px;">
 <td colspan = 3 align=right> <?php echo euro_format($mndtot[1]); ?> </td>
<?php for ($i=0; $i<12; $i++)
{ if ($i > 1 && $i % 2 == 1) { ?> <td colspan = 2 align=right> <?php echo euro_format($mndtot[$i]); ?> </td> <?php } } ?>
</tr>

<tr>
 <td colspan = 15><hr></td>
</tr>

<tr style = "font-size : 14px;">
 <td rowspan = 2><b>Eindsaldo liquide middelen</b></td>
<?php for ($i=0; $i<12; $i++)
{ if ($i % 2 == 0) { ?> <td colspan = 2 align=right><b> <?php echo euro_format($cumtot[$i]); ?> </b></td> <?php } } ?>
</tr>

<tr style = "font-size : 14px;"> 
 <td colspan = 3 align=right><b> <?php echo euro_format($cumtot[1]); ?> </b></td>
<?php for ($i=2; $i<12; $i++)
{ if ($i > 1 && $i % 2 == 1) { ?> <td colspan = 2 align=right><b> <?php echo euro_format($cumtot[$i]); ?> </b></td> <?php } } ?>
</tr>
<tr>
 <td colspan = 15><hr></td>
</tr>
</table>
 <?php } ?>
</form> 
 


		</TD>
<?php } else { ?> <img src='liquiditeit_php.jpg'  width='970' height='550'> <?php }
Include "menuFinance.php"; } ?>
</body>
</html>