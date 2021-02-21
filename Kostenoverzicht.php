<?php /* 28-11-2014 Chargenummer toegevoegd  
11-3-2015 : Login toegevoegd
20-12-2015 : sortering Hoofdrubrieken */
$versie = '24-12-2016'; /* keuzelijst standaard huidig jaar */
$versie = '18-4-2017'; /* Goup by verwijderd in laagste (rubriek) nivo */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '11-7-2020'; /* € gewijzigd in &euro; */
 session_start();  ?>
<html>
<head>
<title>Financieel</title>
</head>
<body>

<center>
<?php
$titel = 'Betaalde posten';
$subtitel = '';
Include "header.php"; ?>
		<TD width = 960 height = 400 valign = "top" align = center>
<?php
$file = "Kostenoverzicht.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { if($modfin == 1) { 

$nu_jaar = date('Y');

$zoek_maxjaar = mysqli_query($db,"
select year(max(datum)) jaar
from tblOpgaaf o
 join tblRubriekuser ru on (o.rubuId = ru.rubuId)
where ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and o.his = 1
") or die (mysqli_error($db));
	while ( $maxj = mysqli_fetch_assoc($zoek_maxjaar)) { $maxjaar = $maxj['jaar']; }
	
	 if(!isset($_POST['kzlJaar']) && isset($maxjaar)) 	{ $toon_jaar = $maxjaar; if($maxjaar > $nu_jaar) { $toon_jaar = $nu_jaar; } }
else if(isset($_POST['kzlJaar'])) 						{ $toon_jaar = $_POST['kzlJaar']; }
	
	
// Declaratie kzlJaar
$kzlJaar = mysqli_query($db,"
select year(datum) jaar 
from tblOpgaaf o
 join tblRubriekuser ru on (o.rubuId = ru.rubuId)
where ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and o.his = 1
group by year(datum)
order by year(datum)
") or die (mysqli_error($db));

$index = 0;
	while($kzljr = mysqli_fetch_array($kzlJaar))
		{
	   $jaarnr[$index] = $kzljr['jaar'];
	   $jaarRaak[$index] = $toon_jaar;
	   $index++; 
    }
// Einde Declaratie kzlJaar ?>

<table Border = 0 align = center>

<form action = "Kostenoverzicht.php" method = "post">
<tr> <td> </td>
<td style="font-size : 13px" >
<?php
echo 'Jaar ' ; ?>
 <!-- KZLJAAR -->
 <select style="width:60;" name= "kzlJaar" >
<?php	$count = count($jaarnr);	
for ($i = 0; $i < $count; $i++){

	$opties = array($jaarnr[$i]=>$jaarnr[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((!isset($_POST['knpToon']) && $jaarRaak[$i] == $key) || (isset($_POST["kzlJaar"]) && $_POST["kzlJaar"] == $key)){
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


If ( (isset($_POST['knpToon']) && !empty($_POST['kzlJaar']) ) || (isset($toon_jaar)) ) { // Keuze maand of melding
if(!empty($_POST['kzlJaar'])) { $jaartal = $_POST['kzlJaar']; } else { $jaartal = $toon_jaar; }
// KZLMAANDEN indien meerdere maanden
$aantmaanden = mysqli_query($db,"SELECT count(date_format(datum,'%m')) mnd FROM tblOpgaaf o join tblRubriekuser ru on (o.rubuId = ru.rubuId) WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." and year(o.datum) = $jaartal and o.his = 1 ") or die (mysqli_error($db));
   $row = mysqli_fetch_assoc($aantmaanden);
		$rows_mnd = $row['mnd'];
		
	if ($rows_mnd >1) {
 echo "&nbsp Maand " ;
 
 //kzlMaand
$kzljrmnd = mysqli_query($db,"SELECT date_format(o.datum,'%m') jrmnd, month(datum) maand FROM tblOpgaaf o join tblRubriekuser ru on (o.rubuId = ru.rubuId)
	WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." and year(datum) = $jaartal and o.his = 1
	GROUP BY date_format(o.datum,'%m'), month(datum) ") or die (mysqli_error($db)); 

$name = 'kzlMnd'; ?>
<select name= <?php echo"$name";?>  style="font-size : 13px" width= 108 >
 <option></option>	
<?php		while($row = mysqli_fetch_array($kzljrmnd))
		  { $maand = $row['maand']; 
				$mndname = array('','januari', 'februari', 'maart','april','mei','juni','juli','augustus','september','oktober','november','december');
$kzlkey="$row[jrmnd]";
$kzlvalue="$mndname[$maand] $row[jaar]";

include "kzl.php";
		}
?></select> <?php
}
//Einde KZLMAANDEN indien meerdere maanden
// KZLHOOFDRUBRIEKEN indien meerdere hoofdrubrieken 
$aantHfdRub = mysqli_query($db,"SELECT hr.rubriek FROM tblOpgaaf o join tblRubriekuser ru on (o.rubuId = ru.rubuId) join tblRubriek r on (ru.rubId = r.rubId) join tblRubriekhfd hr on (r.rubhId = hr.rubhId) WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and year(o.datum) = $jaartal GROUP BY hr.rubriek ORDER BY hr.rubriek") or die (mysqli_error($db));
	$rows_Hrub = mysqli_num_rows($aantHfdRub);
		if($rows_Hrub > 1) { echo "&nbsp Hoofdrubriek ";
//kzlHfdRubriek
$kzlHrub = mysqli_query($db,"SELECT hr.rubhId, hr.rubriek FROM tblOpgaaf o join tblRubriekuser ru on (o.rubuId = ru.rubuId) join tblRubriek r on (ru.rubId = r.rubId) join tblRubriekhfd hr on (r.rubhId = hr.rubhId) WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and year(o.datum) = $jaartal 
							 GROUP BY hr.rubhId, hr.rubriek ORDER BY hr.sort ") or die (mysqli_error($db));
$name = 'kzlHrub';?>
<select name = <?php echo"$name";?> style = "font-size : 13px" width = 100 >
 <option></option>
<?php		while($row = mysqli_fetch_assoc($kzlHrub)) {
$kzlkey = "$row[rubhId]";
$kzlvalue = "$row[rubriek]";

include "kzl.php";
} ?>
</select>
<?php if(isset($_POST['kzlHrub'])) { $valHrub = $_POST['kzlHrub']; } else { $valHrub = 'NULL'; } ?>
<input type = hidden name = "txtHfdRub" size = 1 value = <?php echo $valHrub; ?> > <!-- hiddden -->
<?php } // Einde KZLHOOFDRUBRIEKEN indien meerdere hoofdrubrieken 

// KZLRUBRIEKEN indien meerdere rubrieken 
$aantRub = mysqli_query($db,"SELECT r.rubriek FROM tblOpgaaf o join tblRubriekuser ru on (o.rubuId = ru.rubuId) join tblRubriek r on (ru.rubId = r.rubId) WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and year(o.datum) = $jaartal GROUP BY r.rubriek ORDER BY r.rubriek") or die (mysqli_error($db));
	$rows_Rub = mysqli_num_rows($aantRub);
		if($rows_Rub > 1) { echo "&nbsp Rubriek ";
//kzlHfdRubriek
$kzlRub = mysqli_query($db,"SELECT r.rubId, r.rubriek FROM tblOpgaaf o join tblRubriekuser ru on (o.rubuId = ru.rubuId) join tblRubriek r on (ru.rubId = r.rubId) WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and year(o.datum) = $jaartal
							 GROUP BY r.rubId, r.rubriek ORDER BY r.credeb desc, r.rubriek ") or die (mysqli_error($db));
$name = 'kzlRub';?>
<select name = <?php echo"$name";?> style = "font-size : 13px" width = 100 >
 <option></option>
<?php		while($row = mysqli_fetch_assoc($kzlRub)) {
$kzlkey = "$row[rubId]";
$kzlvalue = "$row[rubriek]";

include "kzl.php";
} ?>
</select>
<?php } // Einde Controle op meerdere , en kzl hoofdrubrieken
 
} /* EINDE KZLRUBRIEKEN indien meerdere rubrieken */

?>
</td>
 <td> <input type = "submit" name ="knpToon" value = "Toon"> </td>
 
 
 <td> </td>
 
 </tr>	
 </table>
</form>

<table border = 0 >
<tr>
<td> </td>
<td>
<?php
If ( (isset($_POST['knpToon']) && !empty($_POST['kzlJaar'])) || (isset($toon_jaar)) ) {
if(!empty($_POST['kzlJaar'])) { $jaartal = $_POST['kzlJaar']; } else { $jaartal = $toon_jaar; }
	// Filter Maand
	if ($rows_mnd <= 1 || empty($_POST['kzlMnd'])) { $resMnd = "( (datum) is not null )"; }
	else if ($rows_mnd > 1 && !empty($_POST['kzlMnd'])) { $resMnd = "( date_format(datum,'%m') = '$_POST[kzlMnd]' )"; } // Einde Filter Maand
	// Filter Hoofdrubriek
	if ($rows_Hrub <= 1 || empty($_POST['kzlHrub'])) { $resHrub = "( r.rubhId is not null )"; }
	else if ($rows_Hrub > 1 && !empty($_POST['kzlHrub'])) { $value = "$_POST[kzlHrub]"; $resHrub = "( r.rubhId = '$value' )"; } // Einde Filter Hoofdrubriek
	// Filter Rubriek
	if ($rows_Rub <= 1 || empty($_POST['kzlRub'])) { $resRub = "( r.rubId is not null )"; }
	else if ($rows_Rub > 1 && !empty($_POST['kzlRub'])) { $value = "$_POST[kzlRub]"; $resRub = "( r.rubId = '$value' )"; } // Einde Filter Hoofdrubriek

//$maandjaren toont de maand(en) binnen het gekozen jaar en eventueel gekozen melding. T.b.v. de loop maand jaar
$maandjaren = mysqli_query($db,"
select month(datum) maand, year(datum) jaar 
from tblOpgaaf o 
 join tblRubriekuser ru on (o.rubuId = ru.rubuId)
 join tblRubriek r on (ru.rubId = r.rubId)
where lidId = ".mysqli_real_escape_string($db,$lidId)." and year(datum) = $jaartal and ".$resMnd." and ".$resHrub." and ".$resRub." and o.his = 1
group by month(datum), year(datum)
order by month(datum), year(datum) desc
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
<tr style = "font-size:18px;" ><td></td><td colspan = 3><b><?php echo "$mndnaam[$mndnr] &nbsp $rij[jaar]"; ?></b></td></tr>
<tr style = "font-size:12px;">

<?php
$hoofdrubriek = mysqli_query($db,"
select r.rubhId, hr.rubriek
from tblOpgaaf o
 join tblRubriekuser ru on (o.rubuId = ru.rubuId)
 join tblRubriek r on (ru.rubId = r.rubId)
 join tblRubriekhfd hr on (r.rubhId = hr.rubhId)
WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and year(datum) = $jaartal and month(datum) = $mndnr and o.his = 1 and ".$resHrub." and ".$resRub."
group by r.rubhId, hr.rubriek
order by hr.sort
") or die (mysqli_error($db));
  while ($opg = mysqli_fetch_assoc($hoofdrubriek))
		{  // START LOOP meldingen
		$rubhId = $opg['rubhId'];
		$rubriek = $opg['rubriek'];  ?>
<tr><td colspan = 2><hr> </td></tr>
<tr><td width = 150 ><?php echo $rubriek; ?></td></tr>
<tr><td colspan = 25><hr></td></tr>
<tr ><td colspan = 25></td></tr>
<?php


$result = mysqli_query($db,"
select o.liq, date_format(o.datum,'%d-%m-%Y') datum, r.rubriek, o.bedrag, o.toel
from tblOpgaaf o join tblRubriekuser ru on (o.rubuId = ru.rubuId) join tblRubriek r on (ru.rubId = r.rubId)
where ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and year(datum) = $jaartal and month(datum) = $mndnr and o.his = 1 and r.rubhId = $rubhId and ".$resRub."
order by r.rubriek
") or die (mysqli_error($db));
?>

<th width = 0 height = 5></th>
<th style = "text-align:center; font-size : 13px;" valign="bottom";width= 80>t.b.v. <br> Liquiditeit<hr></th>
<th width = 1></th>
<th style = "text-align:center;" valign="bottom";width= 80>Datum<hr></th>
<th width = 1></th>
<th style = "text-align:center;" valign="bottom";		 >Rubriek<hr></th>
<th width = 1></th>
<th style = "text-align:center;" valign="bottom";width= 80>Bedrag<hr></th>
<th width = 1></th>
<th style = "text-align:left;" valign="bottom";width= 450>Toelichting<hr></th>
<th width = 1></th>


<th width=60></th>
 </tr>
<?php 		while($row = mysqli_fetch_array($result))
		{ if($row['liq'] == 1) {$liq = 'Ja'; } else { $liq = 'Nee'; } ?>		
<tr>	
	   <td width = 0> </td>
	   <td width = 70 align = center style = "font-size:15px;"> <?php echo $liq; ?> <br> </td>	   
	   <td width = 1> </td>
	   <td width = 180 align = center style = "font-size:15px;"> <?php echo "$row[datum]"; ?> <br> </td>	   
	   <td width = 1> </td>
	   <td width = 250 style = "font-size:15px;"> <?php echo "$row[rubriek]"; ?> <br> </td>
	   <td width = 1> </td>	
	   <td width = 100 align = right style = "font-size:15px;"> <?php echo '&euro; '."$row[bedrag]"; ?> <br> </td>
	   <td width = 1> </td>		   

	   <td width = 700 style = "font-size:15px;"> <?php echo "$row[toel]"; ?> <br> </td>
	   <td width = 1> </td>


	   <td width = 50> </td>
</tr>

<?php } ?>
<tr height = 25><td></td></tr>	 
<?php } // EINDE LOOP meldingen ?>
			
<tr style = "height : 100px;"><td colspan = 25></td></tr>
<?php

}  // EINDE LOOP maandnaam jaartal
	
} //  Einde knop toon ?>			
</table>
		</TD>
<?php } else { ?> <img src='Kostenoverzicht_php.jpg'  width='970' height='550'/> <?php }
Include "menuFinance.php"; } ?>
</body>
</html>
