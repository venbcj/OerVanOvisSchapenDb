<?php

/* 28-11-2014 Chargenummer toegevoegd  
11-3-2015 : Login toegevoegd */
$versie = '25-11-2016';  /* actId = 3 uit on clause gehaald en als sub query genest */
$versie = "22-1-2017"; /* Foto toegevoegd voor gebruikers die module melden niet hebben */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '28-12-2018'; /* Controle toegevoegd. Definitieve melding mag niet als controlemelding terugkomen. Leeg maanden (indien alleen verwijderen bevat) niet tonen */
$versie = '26-9-2020'; /* Omnummeren toegevoegd */
$versie = '20-12-2020'; /* Menu gewijzigd */
$versie = '21-8-2021'; /* and rs.melding = 'code' toegevoegd aan subquery lresp. Wanneer een definitieve melding als controle melding terugkomt nu de tekst : Definitieve melding is teruggekomen als een controle melding ! Kijk op de portal van RVO wat te doen. */
$versie = '31-12-2023'; /* and h.skip = 0 aangevuld aan tblHistorie en sql beveiligd met quotes */
$versie = '10-06-2024'; /* Sortering kzlJaar aangepast. Het recentste jaar staat nu bovenaan */
$versie = '12-12-2024'; /* kzlFouteMeld toegevoegd een aantallen getoond bij meldingen. Als een definitieve melding retour komt als controle melding kan binnen 30 dagen de melding weer worden 'open' gezet met kzlFouteMeld en knpOpenReq */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" align = center> gewijzigd naar <TD valign = 'top' align = 'center'> 31-12-24 include login voor include header gezet */
$versie = '16-08-2025'; /* ubn van gebruiker toegevoegd. Per deze versie kan een gebruiker meerdere ubn's hebben */

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Rapport</title>
</head>
<body>

<?php
$titel = 'Overzicht meldingen';
$file = "Meldingen.php";
include "login.php"; ?>

			<TD valign = 'top' align = 'center'>
<?php
if (is_logged_in()) { if($modmeld == 1) {

include "responscheck.php";

if (isset($_POST['knpOpenReq']) ) { 
// Foute meldingen heropenen
	$req_open = $_POST['kzlFouteMeld']; //echo '$req_open = '.$req_open.'<br>';

	$update_tblRequest = "UPDATE tblRequest set def = 'N', dmmeld = NULL WHERE reqId = '".mysqli_real_escape_string($db,$req_open)."' ";

 /* echo $update_tblRequest.'<br>';*/ mysqli_query($db,$update_tblRequest) or die (mysqli_error($db));
// Einde Foute meldingen heropenen
} 


$mndnaam = array('','januari','februari','maart','april','mei','juni','juli','augustus','september','oktober','november','december'); 
$meldingen = array('GER'=>'Geboorte','AAN'=>'Aanwas', 'AFV'=>'Afvoer', 'DOO'=>'Uitval', 'VMD'=>'Omnummeren');

if(isset($_POST['radDel']) && $_POST['radDel'] == 1) { $radioDel = ' m.skip is not null'; } 
 else { $radioDel = ' m.skip = 0 and h.skip = 0'; }

// Declaratie kzlJaar
$zoek_jaartallen = mysqli_query($db,"
SELECT date_format(rq.dmmeld,'%Y') jaar 
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and date_format(rq.dmmeld,'%Y') is not null and ".$radioDel."
GROUP BY date_format(rq.dmmeld,'%Y') 
ORDER BY date_format(rq.dmmeld,'%Y') desc
") or die (mysqli_error($db));

$index = 0;
$jaarArray = [];
	while($zj = mysqli_fetch_array($zoek_jaartallen))
		{

$jaarArray[$index] = $zj['jaar'];
$index++;
}
// Einde Declaratie kzlJaar

if (!empty($_POST['kzlJaar'])) { $kzlJaar = $_POST['kzlJaar'];

$zoek_aantal_maanden = mysqli_query($db,"
SELECT count(date_format(rq.dmmeld,'%m')) mnd
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and date_format(rq.dmmeld,'%Y') = '".mysqli_real_escape_string($db,$kzlJaar)."' and ".$radioDel."
GROUP BY date_format(rq.dmmeld,'%m')
") or die (mysqli_error($db));
   $zam = mysqli_fetch_assoc($zoek_aantal_maanden);
		$rows_mnd = $zam['mnd'];

if ($rows_mnd >1) {

// Declaratie kzlMdjr
$zoek_jaarmaanden = mysqli_query($db,"
SELECT date_format(rq.dmmeld,'%Y%m') jrmnd, month(rq.dmmeld) maand, date_format(rq.dmmeld,'%Y') jaar 
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and date_format(rq.dmmeld,'%Y') = '".mysqli_real_escape_string($db,$kzlJaar)."' and ".$radioDel."
GROUP BY date_format(rq.dmmeld,'%Y%m') ") or die (mysqli_error($db)); 

$index = 0;
	while($zjm = mysqli_fetch_array($zoek_jaarmaanden))
		{

$jrmndId[$index] = $zjm['jrmnd'];
$maandNr[$index] = $zjm['maand'];
$jaartalValue[$index] = $zjm['jaar'];
$index++;
}
// Einde Declaratie kzlMdjr

} // Einde if ($rows_mnd >1)


// Controle meerdere soorten meldingen
$aantmeldingen = mysqli_query($db,"
SELECT code
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and date_format(rq.dmmeld,'%Y') = '".mysqli_real_escape_string($db,$kzlJaar)."' and ".$radioDel."
GROUP BY rq.code
ORDER BY rq.code
") or die (mysqli_error($db));
	$rows_meld = mysqli_num_rows($aantmeldingen);
// Einde Controle meerdere soorten meldingen

if($rows_meld > 1) {

// Declaratie kzlMelding kzlMeld
$zoek_meldingen_kzl = mysqli_query($db,"
SELECT code
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId) 
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and date_format(rq.dmmeld,'%Y') = '".mysqli_real_escape_string($db,$kzlJaar)."' and ".$radioDel."
GROUP BY code
") or die (mysqli_error($db));

$index = 0;
	while($zmk = mysqli_fetch_array($zoek_meldingen_kzl))
		{

$meldingId[$index] = $zmk['code'];
$index++;
}

// Einde Declaratie kzlMelding kzlMeld
} // Einde if($rows_meld > 1)

// Declaratie keuzelijst foute meldingen
$zoek_foute_meldingen /*niet ouder dan 1 maand */ = mysqli_query($db,"
SELECT rq.reqId, rq.code, count(distinct meldId) aant
FROM tblRequest rq
 join impRespons rp on (rq.reqId = rp.reqId)
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and rq.def = 'J' and rp.def = 'N' and date_format(rq.dmmeld,'%Y-%m-%d') <= date_format(rp.dmcreate,'%Y-%m-%d') and date_format(rp.dmcreate,'%Y-%m-%d') >= date_add(curdate(),interval -30 day)
GROUP BY rq.reqId, rq.code
") or die (mysqli_error($db));
  /*while ($zfm = mysqli_fetch_assoc($zoek_foute_meldingen))	
  	{
  		$reqId_openen = $zfm['reqId'];
  		$code_openen = $zfm['code'];  } */
$rows_foute_meld = mysqli_num_rows($zoek_foute_meldingen);

  $index = 0; 
while ($zfm = mysqli_fetch_assoc($zoek_foute_meldingen)) 
{ 
   $requId[$index] = $zfm['reqId'];
   $reqCode[$index] = $zfm['code'];
   $reqAant[$index] = $zfm['aant'];
   $reqRaak[$index] = $zfm['reqId'];
   $index++; 
}
// Einde Declaratie keuzelijst foute meldingen

} // Einde if (!empty($_POST['kzlJaar'])) ?>



<form action = "Meldingen.php" method = "post">
<table Border = 0 align = "center">
<tr> <td> </td>
 <td style="font-size : 13px" > Jaar
<!-- kzlJaar -->	
	<select style="width:60;" name= 'kzlJaar' style = "font-size:12px;">
  <option></option>
<?php	$count = count($jaarArray);	
for ($i = 0; $i < $count; $i++){

	$opties = array($jaarArray[$i]=>$jaarArray[$i]);
			foreach($opties as $key => $waarde)
			{
  if (isset($_POST["kzlJaar"]) && $_POST["kzlJaar"] == $key) {
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}
// EINDE kzlJaar
?>
</select> 
 </td>
<td colspan = 4 style = "font-size : 13px;">
<?php
if (!empty($_POST['kzlJaar'])) { // Keuze maand of melding


if ($rows_mnd > 1) { echo "&nbsp Maand ";
// kzlJaarMaand ?>
<select name= 'kzlMdjr' style="font-size : 13px" width= 108 >
 <option></option>
<?php	$count = count($jrmndId);	
for ($i = 0; $i < $count; $i++){

$maandnaam = $mndnaam[$maandNr[$i]];

	$opties = array($jrmndId[$i]=>$maandnaam .' '. $jaartalValue[$i]);
			foreach($opties as $key => $waarde)
			{
  if (isset($_POST["kzlMdjr"]) && $_POST["kzlMdjr"] == $key) {
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}
// EInde kzlJaarMaand ?>
</select> 
<?php } // Einde if ($rows_mnd >1) ?>

</td>
<td colspan = 4 style = "font-size : 13px;">

<?php		if($rows_meld > 1) { echo "&nbsp Melding ";

// kzlMelding ?>
<select name = 'kzlMeld' style = "font-size : 13px" width = 100 >
 <option></option>
<?php	$count = count($meldingId);	
for ($i = 0; $i < $count; $i++){

$meldnaam = $meldingen[$meldingId[$i]];

	$opties = array($meldingId[$i]=>$meldnaam);
			foreach($opties as $key => $waarde)
			{
  if (isset($_POST["kzlMeld"]) && $_POST["kzlMeld"] == $key) {
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }	
			}
}
// EINDE kzlMelding 
?>
</select> 
<?php } // Einde if($rows_meld > 1) ?>
</td>
<?php } // Einde if (!empty($_POST['kzlJaar'])) ?>
 <td> <input type = "submit" name ="knpToon" value = "Toon"> </td>
 
 
 <td> <input type = radio name = "radDel" value = 0 
		<?php if(!isset($_POST['knpToon']) || (isset($_POST['radDel']) && $_POST['radDel'] == 0 )) { echo "checked"; } ?> > Excl.
	 <input type = radio name = "radDel" value = 1
		<?php if(isset($_POST['radDel']) && $_POST['radDel'] == 1 ) { echo "checked"; } ?> > Incl. verwijderden</td>
 
 </tr>	
 </table>

<table border = 0 >
<tr>
<td> </td>
<td>
<?php
if (!empty($_POST['kzlJaar'])) {
$kzlMdjr = $_POST['kzlMdjr'];
$kzlMeld = $_POST['kzlMeld']; 

	if ($rows_mnd <= 1 || empty($_POST['kzlMdjr'])) { $resJrmnd = "( date_format(rq.dmmeld,'%Y%m') is not null )"; }
	else if ($rows_mnd > 1 && !empty($_POST['kzlMdjr'])) { $resJrmnd = "( date_format(rq.dmmeld,'%Y%m') = '".mysqli_real_escape_string($db,$kzlMdjr)."' )"; }
	if ($rows_meld <= 1 || empty($_POST['kzlMeld'])) { $resMeld = "( code is not null )"; }
	else if ($rows_meld > 1 && !empty($_POST['kzlMeld'])) { $resMeld = "( code = '".mysqli_real_escape_string($db,$kzlMeld)."' )"; }

// Zoek eerste getoonde maand van gekozen jaar en andere filters. Alleen bij deze maand moet eventueel kzlFouteMeld worden getoond.
$zoek_eerste_getoonde_maand_van_jaar = mysqli_query($db,"
SELECT month(rq.dmmeld) maand
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and date_format(rq.dmmeld,'%Y') = '".mysqli_real_escape_string($db,$kzlJaar)."' and ".$radioDel." and ".$resJrmnd." and ".$resMeld."
GROUP BY month(rq.dmmeld)
ORDER BY month(rq.dmmeld) desc
LIMIT 1
") or die (mysqli_error($db));
  while ($zegmvj = mysqli_fetch_assoc($zoek_eerste_getoonde_maand_van_jaar))
		{  $eerste_mndnr = $zegmvj['maand']; }

//$maandjaren toont de maand(en) binnen het gekozen jaar en eventueel gekozen melding. T.b.v. de loop maand jaar
$maandjaren = mysqli_query($db,"
SELECT month(rq.dmmeld) maand, date_format(rq.dmmeld,'%Y') jaar
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and date_format(rq.dmmeld,'%Y') = '".mysqli_real_escape_string($db,$kzlJaar)."' and ".$radioDel." and ".$resJrmnd." and ".$resMeld."
GROUP BY month(rq.dmmeld), date_format(rq.dmmeld,'%Y')
ORDER BY jaar, month(rq.dmmeld) desc
") or die (mysqli_error($db));
  while ($rij = mysqli_fetch_assoc($maandjaren))
		{  // START LOOP maandnaam jaartal
		$mndnr = $rij['maand'];
		$jaar = $rij['jaar'];
		

		
$tot = date("Ym"); 
$maand = date("m");
$jaarstart = date("Y")-2;
//$vanaf = "$jaarstart$maand"; ?>

<tr style = "font-size:18px;" height = "50"><td></td><td colspan = 12><b><?php echo "$mndnaam[$mndnr] &nbsp $jaar"; ?></b></td>
 <?php if($rows_foute_meld > 0 && $mndnr == $eerste_mndnr) { /*$mndnr == $eerste_mndnr zorgt ervoor dat kzlFouteMeld maar 1x wordt getoond */
// kzlFouteMeld ?>
  <td valign="bottom">
 	<select style="width:165;" name="kzlFouteMeld" value = "" style = "font-size:12px;">
  <option></option>
<?php	$count = count($requId);	
for ($i = 0; $i < $count; $i++){

	$opties = array($requId[$i]=>$requId[$i].' - '.$meldingen[$reqCode[$i]].' - '.$reqAant[$i].' schapen');
			foreach($opties as $key => $waarde)
			{
  if (isset($_POST["kzlFouteMeld"]) && $_POST["kzlFouteMeld"] == $key) {
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}

// Einde kzlFouteMeld ?>
 	</select>
 <input type = "submit" name = "knpOpenReq" value = "Heropen"> </td>
<?php } // Einde if($rows_foute_meld > 0) ?>
</tr>


<?php
$zoek_meldingen = mysqli_query($db,"
SELECT rq.code
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ".$radioDel." and date_format(rq.dmmeld,'%Y') = '".mysqli_real_escape_string($db,$kzlJaar)."' and month(rq.dmmeld) = $mndnr and ".$resMeld."
GROUP BY rq.code
") or die (mysqli_error($db));
  while ($zm = mysqli_fetch_assoc($zoek_meldingen))
		{  // START LOOP meldingen
		$code = $zm['code'];  


$zoek_aantal_per_melding = mysqli_query($db,"
SELECT rq.reqId, count(meldId) aant
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and rq.code = '$code' and date_format(rq.dmmeld,'%Y') = '".mysqli_real_escape_string($db,$kzlJaar)."' and month(rq.dmmeld) = $mndnr and  ".$radioDel." and ".$resMeld."  
GROUP BY rq.reqId
ORDER BY m.skip, if(isnull(m.fout),0,1), rq.dmmeld desc
") or die (mysqli_error($db));

unset($reqId_aant);
$index = 0;
	while($zapm = mysqli_fetch_array($zoek_aantal_per_melding))
		{

$reqId_aant[$index] = $zapm['reqId'];
$meld_aant[$index] = $zapm['aant'];
$index++;
}		

$totaal_per_melding = mysqli_query($db,"
SELECT count(meldId) aant
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and rq.code = '$code' and date_format(rq.dmmeld,'%Y') = '".mysqli_real_escape_string($db,$kzlJaar)."' and month(rq.dmmeld) = $mndnr and  ".$radioDel." and ".$resMeld."  
ORDER BY m.skip, if(isnull(m.fout),0,1), rq.dmmeld desc
") or die (mysqli_error($db));

while($tpm = mysqli_fetch_array($totaal_per_melding))
		{ $totaal = $tpm['aant']; }

?>

<tr><td colspan = 2><hr> </td></tr>
<tr>
 <td><?php echo $meldingen[$code]; ?>
</td>
<td colspan="3" style = "font-size:12px;">

<?php
$count = count($reqId_aant);	
for ($i = 0; $i < $count; $i++){

$meldnr = $reqId_aant[$i];
$meldAant = $meld_aant[$i];

if($meldAant == 1) { $schaap = 'schaap'; } else { $schaap = 'schapen'; }


if($count > 1 || $meldAant > 1) {
echo '&nbsp&nbsp&nbsp Nr '.$meldnr.' telt '.$meldAant.' '.$schaap.'<br>';
if($count > 2 /*meer dan 2 meldnrs*/ && $totaal > 10 /* totaal aantal dieren groter dan 10*/ && $i == $count -2) { ?> <u> <?php } 
if($count > 2 /*meer dan 2 meldnrs*/ && $totaal > 10 /* totaal aantal dieren groter dan 10*/ && $i == $count -1) { ?> </u> <?php 

if($totaal < 100) {
	echo '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp Totaal '.$totaal; } else {
  echo '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspTotaal '.$totaal; }
}

}
}	

?></td>
</tr>
<tr><td colspan = 25><hr></td></tr>
<tr ><td colspan = 25></td></tr>
<?php

if($code == 'VMD') {
$join_tblSchaap = "join tblSchaap s on (s.levensnummer = rs.levensnummer_new)";
} else {
$join_tblSchaap = "join tblSchaap s on (s.levensnummer = rs.levensnummer)";
}

$zoek_inhoud_meldingen = mysqli_query($db,"
SELECT rq.reqId, rq.def req_def, h.skip skip_h, m.skip skip_m, m.fout, rq.dmmeld meldtime, date_format(rq.dmmeld, '%d-%m-%Y %H:%i') meldtijd, u.ubn, s.levensnummer, s.geslacht, date_format(h.datum,'%d-%m-%Y') schaapdm, h.datum dmschaap, lr.meldnr, lr.sucind, lr.foutmeld, lr.respId, lr.def resp_def, lr.dmcreate resptime, ouder.datum dmaanwas

FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u on (u.ubnId = st.ubnId)
 join tblSchaap s on (st.schaapId = s.schaapId)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) ouder on (s.schaapId = ouder.schaapId)
 left join (
	SELECT max(rs.respId) respId, rs.reqId, s.schaapId 
	FROM impRespons rs "
	 .$join_tblSchaap.
	 "join tblMelding m on (m.reqId = rs.reqId)
	 join tblHistorie h on (m.hisId = h.hisId)
	 join tblStal st on (st.stalId = h.stalId)
	WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and rs.melding = '$code'
	GROUP BY rs.reqId, s.schaapId 
 ) lresp on (lresp.reqId = rq.reqId and lresp.schaapId = s.schaapId)
 left join impRespons lr on (lr.respId = lresp.respId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and rq.code = '$code' and date_format(rq.dmmeld,'%Y') = '".mysqli_real_escape_string($db,$kzlJaar)."' and month(rq.dmmeld) = $mndnr and  ".$radioDel." and ".$resMeld." 

ORDER BY m.skip, if(isnull(m.fout),0,1), rq.dmmeld desc, u.ubn, right(s.levensnummer,$Karwerk) ") or die (mysqli_error($db));
?>

<th width = 0 height = 5></th>
<th style = "text-align:center;"valign="bottom";>Eigen meldingnr<hr></th>
<th width = 0 height = 5></th>
<th style = "text-align:center;"valign="bottom";>Meldnr lam RVO<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";>Mijn ubn<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Levensnummer<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Datum<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";>Generatie <hr></th>
<th width = 1></th>
<th style = "text-align:left;"valign="bottom";width= 90>Gemeld<hr></th>
<th width = 1></th>
<th style = "text-align:left;"valign="bottom";width= 450>Boodschap<hr></th>
<th width = 1></th>

<th width=60></th>
 </tr>
<?php 		while($zim = mysqli_fetch_array($zoek_inhoud_meldingen))
		{ 
			$meldingnr = $zim['reqId'];
			$skip_h = $zim['skip_h'];
		  $skip_m = $zim['skip_m'];
		  $foutdb = $zim['fout'];
		  $meldtime = $zim['meldtime'];
		  $meldtijd = $zim['meldtijd'];
		  if($skip_m == 1) { $gemeld = 'Verwijderd'; } else if (isset($foutdb)) { $gemeld = 'Foutief'; } else { $gemeld = $meldtijd; }
		  
		  $sucind = $zim['sucind'];
		  $foutmeld = $zim['foutmeld'];
		  $respId = $zim['respId']; 
		  
		  $ubn = $zim['ubn'];
		  $dmaanw = $zim['dmaanwas'];
		  $geslacht = $zim['geslacht'];
		  $dmschaap = $zim['dmschaap']; if(isset($dmaanw) && $dmaanw <= $dmschaap) { if($geslacht == 'ooi') { $fase = 'moeder'; } else if($geslacht == 'ram') { $fase = 'vader'; } } else { $fase = 'lam'; }

		  $meldnr = $zim['meldnr'];
		  $req_def = $zim['req_def'];
		  $resp_def = $zim['resp_def'];
		  $resptime = $zim['resptime'];
		  
  if ($skip_h == 1)								{ $bericht = 'Let op : Afvoer is hersteld na melden RVO.'; }
  else if ($req_def == 'J' && $sucind == 'J' && $resp_def == 'N' && $resptime > $meldtime) { $bericht = 'Definitieve melding is teruggekomen als een controle melding ! Kijk op de portal van RVO wat te doen.'; }
  else if (isset($meldnr)) { $bericht = 'RVO meldt : Melding correct'; }
  else if ($sucind == 'J' && isset($foutmeld) && $gemeld == $meldtijd) { $bericht = 'RVO meldt : '. $foutmeld; }
  else if ($sucind == 'N' && isset($foutmeld))	{ $bericht = 'RVO meldt : '.$foutmeld; }
  else if ($gemeld == 'Foutief')				{ $bericht = 'Niet gemeld'; }
  else if (isset($respId))						{ $bericht = 'Resultaat van melding is onbekend'; }
 ?>		
<tr align = "center">	
	   <td width = 0> </td>
	   <td 			   style = "font-size:15px;"> <?php echo $meldingnr; ?> <br> </td>
	   <td width = 0> </td>
	   <td 			   style = "font-size:15px;"> <?php echo $meldnr; ?> <br> </td>
	   <td width = 1> </td>
	   <td 			   style = "font-size:15px;"> <?php echo $ubn; ?> <br> </td>
	   <td width = 1> </td>
	   <td width = 100 style = "font-size:15px;"> <?php echo $zim['levensnummer']; ?> <br> </td>
	   <td width = 1> </td>		   
	   <td width = 80 style = "font-size:15px;"> <?php echo $zim['schaapdm']; ?> <br> </td>	   
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
</form>
		</TD>
<?php } else { ?> <img src='Meldingen_php.jpg'  width='970' height='550'/> <?php }
include "menuMelden.php"; } ?>
</body>
</html>
