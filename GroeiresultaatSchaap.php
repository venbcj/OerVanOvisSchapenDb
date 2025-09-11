<?php


$versie = "16-12-2017"; /* Rapport gemaakt */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '30-12-2023'; /* sql beveiligd */
$versie = '10-03-2024'; /* Filter op worp periode toegevoegd en filter meenemen naar Excel gemaakt */
$versie = '29-09-2024'; /* Hernoemd van Groeiresultaat.php naar GroeiresultaatSchaap.php*/
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD align = "center" valign = "top"> 31-12-24 include login voor include header gezet */

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Groeiresultaat schapen</title>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

</head>
<body>

<?php
$titel = 'Groeiresultaten per schaap';
$file = "GroeiresultaatSchaap.php";
include "login.php"; ?>

			<TD align = "center" valign = "top">
<?php
if (is_logged_in()) { if($modtech ==1) {

include "kalender.php";

if(isset($_POST['knpZoek_'])) { 
	$kzlSchaap = $_POST['kzlLevnr_']; $kzlMoeder = $_POST['kzlOoi_']; 

	$worpvan = $_POST['txtWorpVan_']; $dmWorpvan = date_format(date_create($worpvan), 'Y-m-d');
	$worptot = $_POST['txtWorpTot_']; $dmWorptot = date_format(date_create($worptot), 'Y-m-d');
}


/* Declaratie keuzelijst Levensnummer */
$zoek_schapen = mysqli_query($db,"
SELECT s.schaapId,  s.levensnummer
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.levensnummer is not null
GROUP BY s.schaapId, s.levensnummer
ORDER BY s.levensnummer
") or die (mysqli_error($db));
/* Einde Declaratie keuzelijst Levensnummer */


/* Declaratie keuzelijst moeder */
$zoek_moeders = mysqli_query($db,"
SELECT mdr.schaapId, right(mdr.levensnummer,$Karwerk) werknr_ooi
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblVolwas v on (v.volwId = s.volwId)
 join tblSchaap mdr on (v.mdrId = mdr.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and mdr.levensnummer is not null
GROUP BY mdr.schaapId, right(mdr.levensnummer,$Karwerk)
ORDER BY right(mdr.levensnummer,$Karwerk)
") or die (mysqli_error($db));
/* Einde Declaratie keuzelijst moeder */

?> 

<form action="GroeiresultaatSchaap.php" method="post">
<table border = 0>
<tr>
 <td></td>
 <td align="center"><i>Moeder</i></td>
 <td></td>
 <td align="center"><i>Lam</i></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td colspan="4" align="right"><i>Worpen vanaf &nbsp</i></td>
 <td colspan="4" align="left"><i>&nbsp&nbsp&nbsp tot en met</i></td>
</tr>

<tr>
 <td></td>
  <td> 
	<select name= "kzlOoi_" style= "width:<?php echo $w_werknr;?> " >
 <option></option>
<?php		while($row = mysqli_fetch_array($zoek_moeders))
		{
		
			$opties= array($row['schaapId']=>$row['werknr_ooi']);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if(isset($_POST['kzlOoi_']) && $_POST['kzlOoi_'] == $key)
		{
			$keuze = ' selected ';
		}
				
		echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
			}
		
		} ?>
 	</select> 
  </td>
  <td> </td>
  <td> 
	<select name= "kzlLevnr_" style= "width:130; height: 20px" class="search-select">
 <option></option>
<?php		while($row = mysqli_fetch_array($zoek_schapen))
		{
		
			$opties= array($row['schaapId']=>$row['levensnummer']);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if(isset($_POST['kzlLevnr_']) && $_POST['kzlLevnr_'] == $key)
		{
			$keuze = ' selected ';
		}
				
		echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
			}
		
		}
?> </select>
 </td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td colspan="4" align="right"><input id = "datepicker1" type= text name = "txtWorpVan_" size = "8" value = <?php if(isset($worpvan)) { echo "$worpvan"; } ?> ></td>
 <td colspan="4" align="left"><input id = "datepicker2" type= text name = "txtWorpTot_" size = "8" value = <?php if(isset($worptot)) { echo "$worptot"; } ?> >
 </td>
 <td> <input type="submit" name="knpZoek_" value="Zoeken"> </td>
</tr>	

<tr height = 35 ></tr>

<?php
/*unset($where_moeder);
unset($where_schaap);*/

if(isset($kzlMoeder) && !empty($kzlMoeder)) { $where_moeder = " and mdr.schaapId = ". mysqli_real_escape_string($db,$kzlMoeder) . " "; }
if(isset($kzlSchaap) && !empty($kzlSchaap)) { $where_schaap = " and s.schaapId = ". mysqli_real_escape_string($db,$kzlSchaap) . " "; }
if(!empty($worpvan) && !empty($worptot)) { $where_worp = " and hg.datum >= '". mysqli_real_escape_string($db,$dmWorpvan) . "' and hg.datum <= '". mysqli_real_escape_string($db,$dmWorptot) . "' "; }

$where = '';

if(isset($where_moeder)) { $where .= $where_moeder; }
if(isset($where_schaap)) { $where .= $where_schaap; }
if(isset($where_worp))   { $where .= $where_worp; }

$result = "
SELECT right(mdr.levensnummer, $Karwerk) moeder, s.schaapId, s.levensnummer, right(s.levensnummer, $Karwerk) werknum, s.geslacht, prnt.datum aanw, h.kg, h.datum date, date_format(h.datum,'%d-%m-%Y') datum, h.actId, a.actie
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (st.stalId = h.stalId) 
 join tblActie a on (h.actId = a.actId)
 left join (
    SELECT st.schaapId, datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 1 and h.skip = 0
 ) hg on (hg.schaapId = s.schaapId)
 left join (
    SELECT st.schaapId, datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId) 
 left join tblVolwas v on (v.volwId = s.volwId)
 left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(st.rel_best) and h.kg is not null and h.skip = 0 ".$where. "
ORDER BY right(mdr.levensnummer, $Karwerk), right(s.levensnummer, $Karwerk), h.hisId
";

#echo $result;

$result = mysqli_query($db,$result) or die (mysqli_error($db));
?>
 
<tr style = "font-size:12px;">
<th width = 0 height = 30></th>
<th style = "text-align:center;"valign="bottom";width= 50>Moeder<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Levensnummer<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 50>Werknr<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 50>Geslacht<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 100>Generatie<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Gewicht<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 150>Datum<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 150>Actie<hr></th>

<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 700>Gem groei per dag<hr></th>

<th style = "text-align:center;"valign="bottom";width= 80></th>
<th width = 100> <a href="exportGroeiSchaap.php?pst=<?php echo $lidId; ?>&where=<?php echo $where; ?> "> Export-xlsx </a> </th>

	

<th width= 60 ></th>
 </tr>
<?php
		while($row = mysqli_fetch_array($result))
		{ 
unset($vorige_actie);
$levnr_record = $levnr;

    $moeder = $row['moeder'];
    $schaapId = $row['schaapId'];
    $levnr = $row['levensnummer']; if($levnr_record == $levnr) { $levnr_nu = ''; unset($moeder); } else { $levnr_nu = $levnr; unset($vorige_kg); unset($vorige_date);  }
    $werknr = $row['werknum'];
    $geslacht = $row['geslacht']; 
    $aanw = $row['aanw']; 
    $kg = $row['kg']; 			   //if(!isset($vorige_kg)) { $vorige_kg = $kg; }
    $date = $row['date']; 	   	  // if(!isset($vorige_date)) { $vorige_date = $date; }
    $datum = $row['datum']; 	   
    $actId = $row['actId']; 	   //if(!isset($vorige_actId)) { $vorige_actId = $actId; }
    $actie = $row['actie']; 	  // if(!isset($vorige_actie)) { $vorige_actie = $actie; }
    if(isset($aanw)) {if($geslacht == 'ooi') { $fase = 'moeder'; } else if($geslacht == 'ram') { $fase = 'vader'; } } else {$fase = 'lam'; } 


// Zoek vorige weging
unset($vorige_weging);
unset($berekening);

$zoek_vorige_weging = mysqli_query($db,"
SELECT max(hisId) vorige_weging
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
WHERE st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and h.datum < '".mysqli_real_escape_string($db,$date)."' and h.kg is not null
") or die (mysqli_error($db));

while($zvw = mysqli_fetch_array($zoek_vorige_weging))
		{ $vorige_weging = $zvw['vorige_weging']; }

if(isset($vorige_weging)) { 


$zoek_actie_vorige_weging = mysqli_query($db,"
SELECT h.actId, actie, h.datum, kg
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
 join tblActie a on (h.actId = a.actId)
WHERE h.hisId = '".mysqli_real_escape_string($db,$vorige_weging)."'
") or die (mysqli_error($db));

while($zavw = mysqli_fetch_array($zoek_actie_vorige_weging))
		{ $vorige_actId = $zavw['actId']; 
		  $vorige_actie = $zavw['actie']; if($vorige_actId == 9) { $vorige_actie = 'vorige tussenweging'; }
		  $vorige_date = $zavw['datum']; 
		  $vorige_kg = $zavw['kg']; }
}


$date_1 = strtotime($vorige_date); //time(); // or your date as well
$date_2 = strtotime($date);
$datediff = $date_2 - $date_1;

$dagen = round($datediff / (60 * 60 * 24));

if(isset($vorige_weging)) { $berekening = round((($kg - $vorige_kg) / $dagen),2).' kg in '.$dagen.' dagen vanaf '.strtolower($vorige_actie); }
	
// Einde Zoek vorige weging

if(isset($levnr_record) && $levnr_nu != '') { ?>
<tr>
 <td colspan="18"><hr></td>
</tr>	
<?php } ?>


<tr align = "center">	
	   <td width = 0> </td>			
	   
	   <td width = 100 style = "font-size:15px;"> <?php echo $moeder; ?> <br> </td>
	   <td width = 1> </td>	
	   <td width = 100 style = "font-size:15px;"> <?php echo $levnr_nu; ?> <br> </td>
	   <td width = 1> </td>	  	   
	   <td width = 100 style = "font-size:15px;"> <?php echo $werknr; ?> <br> </td>
	   <td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"> <?php echo $geslacht; ?> <br> </td>
	   <td width = 1> </td>	
	   <td width = 100 style = "font-size:15px;"> <?php echo $fase; ?> <br> </td>
	   <td width = 1> </td>

	   <td width = 80 style = "font-size:15px;"> <?php echo $kg; ?> <br> </td>

	   <td width = 1> </td>
	   <td width = 150 style = "font-size:15px;"> <?php echo $datum; ?> <br> </td>
	   <td width = 1> </td>
	   <td width = 150 style = "font-size:15px;"> <?php echo $actie; ?> <br> </td>

	   <td width = 1> </td>
	   <td width = 600 style = "font-size:15px;" align="left"> <?php echo $berekening; ?> <br> </td>

	   
<!-- '$vorige_kg = '.$vorige_kg.' en $kg = '.$kg; -->	   
</tr>				
<?php 

		} ?>
</tr>				
</table>
</form>


		</TD>
<script type="text/javascript">
	$(document).ready(function() {
	  $(".search-select").select2();
	});
</script>

<?php } else { ?> <img src='resultHok_php.jpg'  width='970' height='550'/> <?php }
include "menuRapport.php"; } ?>
</body>
</html>
