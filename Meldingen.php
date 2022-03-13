<?php /* 28-11-2014 Chargenummer toegevoegd  
11-3-2015 : Login toegevoegd */
$versie = '25-11-2016';  /* actId = 3 uit on clause gehaald en als sub query genest */
$versie = "22-1-2017"; /* Foto toegevoegd voor gebruikers die module melden niet hebben */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '28-12-2018'; /* Controle toegevoegd. Definitieve melding mag niet als controlemelding terugkomen. Leeg maanden (indien alleen verwijderen bevat) niet tonen */
$versie = '26-9-2020'; /* Omnummeren toegevoegd */
$versie = '20-12-2020'; /* Menu gewijzigd */
$versie = '21-8-2021'; /* and rs.melding = '$code' toegevoegd aan subquery lresp. Wanneer een definitieve melding als controle melding terugkomt nu de tekst : Definitieve melding is teruggekomen als een controle melding ! Kijk op de portal van RVO wat te doen. */

 session_start(); ?>
<html>
<head>
<title>Rapport</title>
</head>
<body>

<center>
<?php
$titel = 'Overzicht meldingen';
$subtitel = '';
Include "header.php"; ?>
		<TD width = 960 height = 400 valign = "top" align = center>
<?php
$file = "Meldingen.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { if($modmeld == 1) {

Include "responscheck.php"; ?>

<table Border = 0 align = center>

<form action = "Meldingen.php" method = "post">
<tr> <td> </td>
<td style="font-size : 13px" >
<?php
if(isset($_POST['radDel']) && $_POST['radDel'] == 1) { $resDel = ' m.skip is not null'; } 
 else { $resDel = ' m.skip = 0 and h.skip = 0'; }

echo 'Jaar ' ;
//kzlJaar
$name = "kzlJaar"; ?>
<select name= <?php echo"$name";?> width = 60 >
 <option></option>
<?php		
$kzlJaar = mysqli_query($db,"
SELECT date_format(rq.dmmeld,'%Y') jaar 
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and date_format(rq.dmmeld,'%Y') is not null and ".$resDel."
GROUP BY date_format(rq.dmmeld,'%Y') 
ORDER BY date_format(rq.dmmeld,'%Y') 
") or die (mysqli_error($db));
	while($row = mysqli_fetch_array($kzlJaar))
		{
$kzlkey="$row[jaar]";
$kzlvalue="$row[jaar]";

include "kzl.php";
		}
// EINDE kzlJaar
?>
</select> 
 </td>
<td colspan = 4 style = "font-size : 13px;">
<?php
If (isset($_POST['knpToon']) && !empty($_POST['kzlJaar'])) { // Keuze maand of melding
// Controle meerdere maanden
$aantmaanden = mysqli_query($db,"
SELECT count(date_format(rq.dmmeld,'%m')) mnd
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and date_format(rq.dmmeld,'%Y') = $_POST[kzlJaar] and ".$resDel."
GROUP BY date_format(rq.dmmeld,'%m')
") or die (mysqli_error($db));
   $row = mysqli_fetch_assoc($aantmaanden);
		$rows_mnd = $row['mnd'];
		
	if ($rows_mnd >1) { ?>
 &nbsp Maand
 
<?php //kzlJaarMaand
$kzljrmnd = mysqli_query($db,"
SELECT date_format(rq.dmmeld,'%Y%m') jrmnd, month(rq.dmmeld) maand, date_format(rq.dmmeld,'%Y') jaar 
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and date_format(rq.dmmeld,'%Y') = $_POST[kzlJaar] and ".$resDel."
GROUP BY date_format(rq.dmmeld,'%Y%m') ") or die (mysqli_error($db)); 

$name = 'kzlmdjr'; ?>
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
//Einde Controle meerdere maanden
// Controle meerdere soorten meldingen
$aantmeldingen = mysqli_query($db,"
SELECT code
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and date_format(rq.dmmeld,'%Y') = $_POST[kzlJaar] and ".$resDel."
GROUP BY rq.code
ORDER BY rq.code
") or die (mysqli_error($db));
	$rows_meld = mysqli_num_rows($aantmeldingen);
		if($rows_meld > 1) { echo "&nbsp Melding ";
//kzlMelding
$kzlMeld = mysqli_query($db,"
SELECT code
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId) 
WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." and date_format(rq.dmmeld,'%Y') = $_POST[kzlJaar] and ".$resDel."
GROUP BY code

") or die (mysqli_error($db));
$name = 'kzlmeld';?>
<select name = <?php echo"$name";?> style = "font-size : 13px" width = 100 >
 <option></option>
<?php		while($row = mysqli_fetch_assoc($kzlMeld)) {
$meldname = array('GER'=>'Geboorte','AAN'=>'Aanwas', 'AFV'=>'Afvoer', 'DOO'=>'Uitval', 'VMD'=>'Omnummeren');
$kzlkey = "$row[code]";
$kzlvalue="$meldname[$kzlkey]";

include "kzl.php";
}
} // Einde Controle meerdere soorten meldingen
 
} /* EINDE Keuze maand of melding */

?>
</td>
 <td> <input type = "submit" name ="knpToon" value = "Toon"> </td>
 
 
 <td> <input type = radio name = "radDel" value = 0 
		<?php if(!isset($_POST['knpToon']) || (isset($_POST['radDel']) && $_POST['radDel'] == 0 )) { echo "checked"; } ?> > Excl.
	 <input type = radio name = "radDel" value = 1
		<?php if(isset($_POST['radDel']) && $_POST['radDel'] == 1 ) { echo "checked"; } ?> > Incl. verwijderden</td>
 
 </tr>	
 </table>
</form>

<table border = 0 >
<tr>
<td> </td>
<td>
<?php
if (isset($_POST['knpToon']) && !empty($_POST['kzlJaar']) ) {
	if ($rows_mnd <= 1 || empty($_POST['kzlmdjr'])) { $resJrmnd = "( date_format(rq.dmmeld,'%Y%m') is not null )"; }
	else if ($rows_mnd > 1 && !empty($_POST['kzlmdjr'])) { $resJrmnd = "( date_format(rq.dmmeld,'%Y%m') = $_POST[kzlmdjr] )"; }
	if ($rows_meld <= 1 || empty($_POST['kzlmeld'])) { $resMeld = "( code is not null )"; }
	else if ($rows_meld > 1 && !empty($_POST['kzlmeld'])) { $value = "$_POST[kzlmeld]"; $resMeld = "( code = '$value' )"; }

//$maandjaren toont de maand(en) binnen het gekozen jaar en eventueel gekozen melding. T.b.v. de loop maand jaar
$maandjaren = mysqli_query($db,"
SELECT month(rq.dmmeld) maand, date_format(rq.dmmeld,'%Y') jaar
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and date_format(rq.dmmeld,'%Y') = $_POST[kzlJaar] and ".$resDel." and ".$resJrmnd." and ".$resMeld."
GROUP BY month(rq.dmmeld), date_format(rq.dmmeld,'%Y')
ORDER BY jaar, month(rq.dmmeld) desc ") or die (mysqli_error($db));
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
$meldingen = mysqli_query($db,"
SELECT rq.code
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and ".$resDel." and date_format(rq.dmmeld,'%Y') = $_POST[kzlJaar] and month(rq.dmmeld) = $mndnr and ".$resMeld."
GROUP BY rq.code
") or die (mysqli_error($db));
  while ($mld = mysqli_fetch_assoc($meldingen))
		{  // START LOOP meldingen
		$code = $mld['code'];  
		$melding = array('GER'=>'Geboorte','AAN'=>'Aanwas', 'AFV'=>'Afvoer', 'DOO'=>'Uitval', 'VMD'=>'Omnummeren');?>
<tr><td colspan = 2><hr> </td></tr>
<tr><td><?php echo $melding[$code]; ?></td></tr>
<tr><td colspan = 25><hr></td></tr>
<tr ><td colspan = 25></td></tr>
<?php

if($code == 'VMD') {
$connect_tblSchaap = "join tblSchaap s on (s.levensnummer = rs.levensnummer_new)";
} else {
$connect_tblSchaap = "join tblSchaap s on (s.levensnummer = rs.levensnummer)";
}

$result = mysqli_query($db,"
SELECT date_format(rq.dmmeld,'%Y') jaar, month(rq.dmmeld) maand, rq.def req_def, rq.code, 
h.skip skip_h,
m.skip skip_m,
m.fout,
rq.dmmeld meldtime,
date_format(rq.dmmeld, '%d-%m-%Y %H:%i') meldtijd,

s.levensnummer, s.geslacht, date_format(h.datum,'%d-%m-%Y') schaapdm, h.datum dmschaap, if(isnull(m.fout),0,1) sortFout,
 lr.meldnr, lr.sucind, lr.foutmeld, lr.respId, lr.def resp_def, lr.dmcreate resptime,
 ouder.datum dmaanwas

FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (st.schaapId = s.schaapId)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3
 ) ouder on (s.schaapId = ouder.schaapId)
 left join (
	SELECT max(rs.respId) respId, rs.reqId, s.schaapId 
	FROM impRespons rs "
	 .$connect_tblSchaap.
	 "join tblMelding m on (m.reqId = rs.reqId)
	 join tblHistorie h on (m.hisId = h.hisId)
	 join tblStal st on (st.stalId = h.stalId)
	WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and rs.melding = '$code'
	GROUP BY rs.reqId, s.schaapId 
 ) lresp on (lresp.reqId = rq.reqId and lresp.schaapId = s.schaapId)
 left join impRespons lr on (lr.respId = lresp.respId)
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and rq.code = '$code' and date_format(rq.dmmeld,'%Y') = $_POST[kzlJaar] and month(rq.dmmeld) = $mndnr and  ".$resDel." and ".$resMeld." 

ORDER BY m.skip, if(isnull(m.fout),0,1), rq.dmmeld desc, right(s.levensnummer,$Karwerk) ") or die (mysqli_error($db));
?>

<th width = 0 height = 5></th>
<th style = "text-align:center;"valign="bottom";		 >Meldnr<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Levensnummer<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Datum<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";		 >Generatie <hr></th>
<th width = 1></th>
<th style = "text-align:left;"valign="bottom";width= 90>Gemeld<hr></th>
<th width = 1></th>
<th style = "text-align:left;"valign="bottom";width= 450>Boodschap<hr></th>
<th width = 1></th>

<th width=60></th>
 </tr>
<?php 		while($row = mysqli_fetch_array($result))
		{ $skip_h = $row['skip_h'];
		  $skip_m = $row['skip_m'];
		  $foutdb = $row['fout'];
		  $meldtime = $row['meldtime'];
		  $meldtijd = $row['meldtijd'];
		  if($skip_m == 1) { $gemeld = 'Verwijderd'; } else if (isset($foutdb)) { $gemeld = 'Foutief'; } else { $gemeld = $meldtijd; }
		  
		  $sucind = $row['sucind'];
		  $foutmeld = $row['foutmeld'];
		  $respId = $row['respId']; 
		  
		  $dmaanw = $row['dmaanwas'];
		  $geslacht = $row['geslacht'];
		  $dmschaap = $row['dmschaap']; if(isset($dmaanw) && $dmaanw <= $dmschaap) { if($geslacht == 'ooi') { $fase = 'moeder'; } else if($geslacht == 'ram') { $fase = 'vader'; } } else { $fase = 'lam'; }

		  $meldnr = $row['meldnr'];
		  $req_def = $row['req_def'];
		  $resp_def = $row['resp_def'];
		  $resptime = $row['resptime'];
		  
  if ($skip_h == 1)								{ $bericht = 'Let op : Afvoer is hersteld na melden RVO.'; }
  else if ($req_def == 'J' && $sucind == 'J' && $resp_def == 'N' && $resptime > $meldtime) { $bericht = 'Definitieve melding is teruggekomen als een controle melding ! Kijk op de portal van RVO wat te doen.'; }
  else if (isset($meldnr)) { $bericht = 'RVO meldt : Melding correct'; }
  else if ($sucind == 'J' && isset($foutmeld) && $gemeld == $meldtijd) { $bericht = 'RVO meldt : '. $foutmeld; }
  else if ($sucind == 'N' && isset($foutmeld))	{ $bericht = 'RVO meldt : '.$foutmeld; }
  else if ($gemeld == 'Foutief')				{ $bericht = 'Niet gemeld'; }
  else if (isset($respId))						{ $bericht = 'Resultaat van melding is onbekend'; }
 ?>		
<tr align = center>	
	   <td width = 0> </td>
	   <td 			   style = "font-size:15px;"> <?php echo $meldnr; ?> <br> </td>
	   <td width = 1> </td>	
	   <td width = 100 style = "font-size:15px;"> <?php echo "$row[levensnummer]"; ?> <br> </td>
	   <td width = 1> </td>		   
	   <td width = 80 style = "font-size:15px;"> <?php echo "$row[schaapdm]"; ?> <br> </td>	   
	   <td width = 1> </td>
	   <td			  style = "font-size:15px;"> <?php echo $fase; ?> <br> </td>
	   <td width = 1> </td>
	   <td width = 130 style = "font-size:15px;" align = "left"> <?php echo $gemeld; ?> <br> </td>
	   <td width = 1> </td>
	   <td width = 450 style = "font-size:15px;" align = "left"> <?php if(isset($bericht)) { echo $bericht; unset($bericht); } ?> <br> </td>
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
<?php } else { ?> <img src='Meldingen_php.jpg'  width='970' height='550'/> <?php }
Include "menuMelden.php"; } ?>
</body>
</html>
