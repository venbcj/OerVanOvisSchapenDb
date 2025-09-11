<?php

require_once("autoload.php");

/* 28-11-2014 Chargenummer toegevoegd  
11-3-2015 : Login toegevoegd
20-12-2015 : sortering Hoofdrubrieken */
$versie = '24-12-2016'; /* keuzelijst standaard huidig jaar */
$versie = '18-4-2017'; /* Goup by verwijderd in laagste (rubriek) nivo */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '11-7-2020'; /* € gewijzigd in &euro; */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" align = center> gewijzigd naar <TD valign = "top" align = center> 31-12-24 include login voor include header gezet */

 session_start();  ?>
<!DOCTYPE html>
<html>
<head>
<title>Financieel</title>
</head>
<body>

<?php
$titel = 'Betaalde posten';
$file = "Kostenoverzicht.php";
include "login.php"; ?>

			<TD valign = "top" align = "center">
<?php
if (Auth::is_logged_in()) { if($modfin == 1) { 

if(isset($_POST["knpSave_"])) { include "save_kostenoverzicht.php"; }

$nu_jaar = date('Y');

$zoek_maxjaar = mysqli_query($db,"
SELECT year(max(datum)) jaar
FROM tblOpgaaf o
 join tblRubriekuser ru on (o.rubuId = ru.rubuId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and o.his = 1
") or die (mysqli_error($db));
	while ( $maxj = mysqli_fetch_assoc($zoek_maxjaar)) { $maxjaar = $maxj['jaar']; }
	
	 if(!isset($_POST['kzlJaar_']) && isset($maxjaar)) 	{ $toon_jaar = $maxjaar; }
else if(isset($_POST['kzlJaar_'])) 						{ $toon_jaar = $_POST['kzlJaar_']; }
	
	
// Declaratie kzlJaar
$kzlJaar = mysqli_query($db,"
SELECT year(datum) jaar 
FROM tblOpgaaf o
 join tblRubriekuser ru on (o.rubuId = ru.rubuId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and o.his = 1
GROUP BY year(datum)
ORDER BY year(datum) desc
") or die (mysqli_error($db));

$index = 0;
$jaarnr = [];
$jaarRaak = [];
	while($kzljr = mysqli_fetch_array($kzlJaar))
		{
	   $jaarnr[$index] = $kzljr['jaar'];
	   $jaarRaak[$index] = $toon_jaar;
	   $index++; 
    }
// Einde Declaratie kzlJaar ?>
<form action = "Kostenoverzicht.php" method = "post">

<table Border = 0 align = "center">
<tr>
 <td width="210"> </td>
 <td style="font-size : 13px" >
<?php
echo 'Jaar ' ; ?>
 <!-- KZLJAAR -->
 <select style="width:60;" name= "kzlJaar_" >
<?php	$count = count($jaarnr);	
for ($i = 0; $i < $count; $i++){

	$opties = array($jaarnr[$i]=>$jaarnr[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((!isset($_POST['knpToon_']) && $jaarRaak[$i] == $key) || (isset($_POST["kzlJaar_"]) && $_POST["kzlJaar_"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}

 ?> </select>
	 <!-- EINDE KZLJAAR -->
 </td>
 <td colspan = 4 style = "font-size : 13px;">
<?php
// Na het klikken op de knop knpToon_ kunnen een aantal keuzelijsten zijn gevuld die invloed hebben op de inhoud van de andere keuzelijsten. Hierbij variabelen die de query's beïnvloeden.
/*if(!isset($resMnd) || $resMnd == "( (datum) is not null )") { $reskzlMnd = "( (datum) is not null )"; }
if(!isset($resHrub) || $resHrub == "( r.rubhId is not null )") { $reskzlHrub = "( r.rubhId is not null )"; } else { $reskzlHrub = $resHrub; }

echo $reskzlHrub.'<br>';
echo $rows_Hrub;*/
// EINDE Na het klikken op de knop knpToon_ kunnen een aantal keuzelijsten zijn gevuld die invloed hebben op de inhoud van de andere keuzelijsten. Hierbij variabelen die de query's beïnvloeden.


If ( (isset($_POST['knpToon_']) && !empty($_POST['kzlJaar_']) ) || (isset($toon_jaar)) ) { // Keuze maand of melding
if(!empty($_POST['kzlJaar_'])) { $jaartal = $_POST['kzlJaar_']; } else { $jaartal = $toon_jaar; }
// KZLMAANDEN indien meerdere maanden
$zoek_aantal_maanden = mysqli_query($db,"
SELECT count(date_format(datum,'%m')) mnd
FROM tblOpgaaf o
 join tblRubriekuser ru on (o.rubuId = ru.rubuId)
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and year(o.datum) = '".mysqli_real_escape_string($db,$jaartal)."' and o.his = 1
") or die (mysqli_error($db));

   $zam = mysqli_fetch_assoc($zoek_aantal_maanden);
		$aant_mnd = $zam['mnd'];
		
	if ($aant_mnd >1) {
 echo "&nbsp Maand " ;
 
 //kzlMaand
$kzljrmnd = mysqli_query($db,"
SELECT date_format(o.datum,'%m') jrmnd, month(datum) maand
FROM tblOpgaaf o
 join tblRubriekuser ru on (o.rubuId = ru.rubuId)
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and year(datum) = '".mysqli_real_escape_string($db,$jaartal)."' and o.his = 1
GROUP BY date_format(o.datum,'%m'), month(datum)
") or die (mysqli_error($db)); 

$name = 'kzlMnd_'; ?>
<select name= <?php echo"$name";?>  style="font-size : 13px" width= 108 >
 <option></option>	
<?php		while($row = mysqli_fetch_array($kzljrmnd))
		  { $maand = $row['maand']; 
				$mndname = array('','januari', 'februari', 'maart','april','mei','juni','juli','augustus','september','oktober','november','december');
$kzlkey = $row['jrmnd'];
$kzlvalue = $mndname[$maand].$row['jaar'];

include "kzl.php";
		}
?></select> <?php
}
//Einde KZLMAANDEN indien meerdere maanden
// KZLHOOFDRUBRIEKEN indien meerdere hoofdrubrieken 
$zoek_hoofdrubrieken = mysqli_query($db,"
SELECT hr.rubriek
FROM tblOpgaaf o
 join tblRubriekuser ru on (o.rubuId = ru.rubuId)
 join tblRubriek r on (ru.rubId = r.rubId)
 join tblRubriekhfd hr on (r.rubhId = hr.rubhId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and year(o.datum) = '".mysqli_real_escape_string($db,$jaartal)."'
GROUP BY hr.rubriek
ORDER BY hr.rubriek
") or die (mysqli_error($db));

	$rows_Hrub = mysqli_num_rows($zoek_hoofdrubrieken);
		if($rows_Hrub > 1) { echo "&nbsp Hoofdrubriek ";
//kzlHfdRubriek
$kzlHrub = mysqli_query($db,"
SELECT hr.rubhId, hr.rubriek
FROM tblOpgaaf o
 join tblRubriekuser ru on (o.rubuId = ru.rubuId)
 join tblRubriek r on (ru.rubId = r.rubId)
 join tblRubriekhfd hr on (r.rubhId = hr.rubhId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and year(o.datum) = '".mysqli_real_escape_string($db,$jaartal)."' 
GROUP BY hr.rubhId, hr.rubriek ORDER BY hr.sort
") or die (mysqli_error($db));

$name = 'kzlHrub_';?>
<select name = <?php echo"$name";?> style = "font-size : 13px" width = 100 >
 <option></option>
<?php		while($row = mysqli_fetch_assoc($kzlHrub)) {
$kzlkey = $row['rubhId'];
$kzlvalue = $row['rubriek'];

include "kzl.php";
} ?>
</select>

<?php } // Einde KZLHOOFDRUBRIEKEN indien meerdere hoofdrubrieken 

// KZLRUBRIEKEN indien meerdere rubrieken 
$zoek_rubrieken = mysqli_query($db,"
SELECT r.rubriek
FROM tblOpgaaf o
 join tblRubriekuser ru on (o.rubuId = ru.rubuId)
 join tblRubriek r on (ru.rubId = r.rubId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and year(o.datum) = '".mysqli_real_escape_string($db,$jaartal)."'
GROUP BY r.rubriek ORDER BY r.rubriek
") or die (mysqli_error($db));

	$rows_Rub = mysqli_num_rows($zoek_rubrieken);
		if($rows_Rub > 1) { echo "&nbsp Rubriek ";
//kzlHfdRubriek
$kzlRub = mysqli_query($db,"
SELECT r.rubId, r.rubriek
FROM tblOpgaaf o
 join tblRubriekuser ru on (o.rubuId = ru.rubuId)
 join tblRubriek r on (ru.rubId = r.rubId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and year(o.datum) = '".mysqli_real_escape_string($db,$jaartal)."'
GROUP BY r.rubId, r.rubriek
ORDER BY r.credeb desc, r.rubriek
") or die (mysqli_error($db));

$name = 'kzlRub_';?>
<select name = <?php echo"$name";?> style = "font-size : 13px" width = 100 >
 <option></option>
<?php		while($row = mysqli_fetch_assoc($kzlRub)) {
$kzlkey = $row['rubId'];
$kzlvalue = $row['rubriek'];

include "kzl.php";
} ?>
</select>
<?php } // Einde Controle op meerdere , en kzl hoofdrubrieken
 
} /* EINDE KZLRUBRIEKEN indien meerdere rubrieken */

?>
 </td>
 <td> <input type = "submit" name ="knpToon_" value = "Toon"> </td>
 <td width="210"></td>
 <td>
	<input type="submit" name= <?php echo "knpSave_"; ?> value="Opslaan" style = "font-size:12px;">
 </td>
</tr>	
</table>

<?php
If ( (isset($_POST['knpToon_']) && !empty($_POST['kzlJaar_'])) || (isset($toon_jaar)) ) {
if(!empty($_POST['kzlJaar_'])) { $jaartal = $_POST['kzlJaar_']; } else { $jaartal = $toon_jaar; }
	// Filter Maand
	if ($aant_mnd <= 1 || empty($_POST['kzlMnd_'])) { $resMnd = "( (datum) is not null )"; }
	else if ($aant_mnd > 1 && !empty($_POST['kzlMnd_'])) { $resMnd = "( date_format(datum,'%m') = ".$_POST['kzlMnd_']." )"; } // Einde Filter Maand
	// Filter Hoofdrubriek
	if ($rows_Hrub <= 1 || empty($_POST['kzlHrub_'])) { $resHrub = "( r.rubhId is not null )"; }
	else if ($rows_Hrub > 1 && !empty($_POST['kzlHrub_'])) { $value = $_POST['kzlHrub_']; $resHrub = "( r.rubhId = '$value' )"; } // Einde Filter Hoofdrubriek
	// Filter Rubriek
	if ($rows_Rub <= 1 || empty($_POST['kzlRub_'])) { $resRub = "( r.rubId is not null )"; }
	else if ($rows_Rub > 1 && !empty($_POST['kzlRub_'])) { $value = $_POST['kzlRub_']; $resRub = "( r.rubId = '$value' )"; } // Einde Filter Hoofdrubriek

//$maandjaren toont de maand(en) binnen het gekozen jaar en eventueel gekozen melding. T.b.v. de loop maand jaar
$maandjaren = mysqli_query($db,"
SELECT month(datum) maand, year(datum) jaar 
FROM tblOpgaaf o 
 join tblRubriekuser ru on (o.rubuId = ru.rubuId)
 join tblRubriek r on (ru.rubId = r.rubId)
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and year(datum) = '".mysqli_real_escape_string($db,$jaartal)."' and ".$resMnd." and ".$resHrub." and ".$resRub." and o.his = 1
GROUP BY month(datum), year(datum)
ORDER BY month(datum), year(datum) desc
") or die (mysqli_error($db));
  while ($rij = mysqli_fetch_assoc($maandjaren))
		{  // START LOOP maandnaam jaartal
		$mndnr = $rij['maand'];
		
$mndnaam = array('','januari', 'februari', 'maart','april','mei','juni','juli','augustus','september','oktober','november','december'); 
		
$tot = date("Ym"); 
	$maand = date("m");
	$jaarstart = date("Y")-2;
//$vanaf = "$jaarstart$maand";
?>

<table border = 0 >
<tr style = "font-size:18px;" >
 <td></td>
 <td colspan = 6><b><?php echo $mndnaam[$mndnr]. " &nbsp ". $rij['jaar']; ?></b></td>
</tr>

<tr style = "font-size:12px;">

<?php
$zoek_hoofdrubriek = mysqli_query($db,"
SELECT r.rubhId, hr.rubriek
FROM tblOpgaaf o
 join tblRubriekuser ru on (o.rubuId = ru.rubuId)
 join tblRubriek r on (ru.rubId = r.rubId)
 join tblRubriekhfd hr on (r.rubhId = hr.rubhId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and year(datum) = '".mysqli_real_escape_string($db,$jaartal)."' and month(datum) = $mndnr and o.his = 1 and ".$resHrub." and ".$resRub."
GROUP BY r.rubhId, hr.rubriek
ORDER BY hr.sort
") or die (mysqli_error($db));
  while ($opg = mysqli_fetch_assoc($zoek_hoofdrubriek))
		{  // START LOOP meldingen
		$rubhId = $opg['rubhId'];
		$hfdRubriek = $opg['rubriek'];  ?>
<tr><td colspan = 2><hr> </td></tr>
<tr><td width = 150 ><?php echo $hfdRubriek; ?></td></tr>
<tr><td colspan = 25><hr></td></tr>
<?php


$zoek_posten = mysqli_query($db,"
SELECT o.opgId, o.liq, date_format(o.datum,'%d-%m-%Y') datum, r.rubriek, o.bedrag, o.toel
FROM tblOpgaaf o
 join tblRubriekuser ru on (o.rubuId = ru.rubuId)
 join tblRubriek r on (ru.rubId = r.rubId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and year(datum) = '".mysqli_real_escape_string($db,$jaartal)."' and month(datum) = $mndnr and o.his = 1 and r.rubhId = $rubhId and ".$resRub."
ORDER BY r.rubriek, o.datum desc
") or die (mysqli_error($db));
?>

<tr>
 <th width = 0 height = 5></th>
 <th style = "text-align:center; font-size : 13px;" valign="bottom";width= 80>t.b.v. <br> Liquiditeit<hr></th>
 <th style = "text-align:center;" valign="bottom"; width= 80>Datum<hr></th>
 <th style = "text-align:center;" valign="bottom";		 >Rubriek<hr></th>
 <th style = "text-align:center;" valign="bottom"; width= 80>Bedrag<hr></th>
 <th width = 10>  </th>
 <th style = "text-align:left;" valign="bottom";width= 450>Toelichting<hr></th>
 <th style = "text-align:center; font-size : 12px;" width=60>Terug naar inboeken<hr></th>
</tr>

<?php 		while($zp = mysqli_fetch_array($zoek_posten))
		{ if($zp['liq'] == 1) {$liq = 'Ja'; } else { $liq = 'Nee'; } 

			$Id = $zp['opgId'];
			$datum = $zp['datum'];
			$rubriek = $zp['rubriek'];
			$bedrag = $zp['bedrag'];
			$toelichting = $zp['toel']; ?>

<tr>	
 <td width = 0 align="right"> <?php //echo $Id; ?></td>
 <td width = 70 align = "center" style = "font-size:15px;"> <?php echo $liq; ?> <br> </td>	   
 <td width = 180 align = "center" style = "font-size:15px;"> <?php echo $datum; ?> <br> </td>	   
 <td width = 250 style = "font-size:15px;"> <?php echo $rubriek; ?> <br> </td>
 <td width = 100 align = right style = "font-size:15px;"> <?php echo '&euro; '.$bedrag; ?> <br> </td>
 <td width = 10>  </td>
 <td width = 350 style = "font-size:15px;"> <?php echo $toelichting; ?> <br> </td>
 <td width = 50 align="center"> 
 	<input type="checkbox" name= <?php echo "chbTerug_$Id"; ?> value= 1 style = "font-size:9px;" title=" Terugzetten naar Inboeken.">
 </td>
</tr>

<?php } ?>
<tr height = 25><td></td></tr>	 
<?php } // EINDE LOOP meldingen ?>
			
<tr style = "height : 100px;"><td colspan = 25></td></tr>
</table>
<?php

}  // EINDE LOOP maandnaam jaartal
	
} //  Einde knop toon ?>			


</form>
		</TD>
<?php } else { ?> <img src='Kostenoverzicht_php.jpg'  width='970' height='550'/> <?php }
include "menuFinance.php"; } ?>
</body>
</html>
