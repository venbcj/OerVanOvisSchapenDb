<?php /* 8-8-2014 Aantal karakters werknr variabel gemaakt en quotes bij "$met" en "$zonder" weggehaald 
11-8-2014 : veld type gewijzigd in fase 
5-11-2014 : Bijwerken database aangevuld met inserten tblRequest en tblMeldingen 
20-2-2015 : login toegevoegd 
11-4-2015 : veld volgnr uit tblUitval gebruikt i.pv. veld uitvalId LET OP volgnr wordt ook gebruikt in 
			-	importReader.php 
			-	insGeboortes.php 
5-11-2015 : aanschafdatum gewijzigd in aankoopdatum 
15-11-2015 controle geboortedatum na einddatum moeder indien van toepassing 
17-11-2015 kzlMoeder en kzlVader aangepast fase in lijst verwijderd want fase is (bij moeder) altijd moeder en geen lam meer
18-11-2015 : hok gewijzigd naar verblijf 
25-9-2016 Bij uitval keuze reden en moment niet verplicht gemaakt
20-10-2016 : mdrId en vdrId gewijzigd in volwId 
28-10-2016 : Geboortedatum bij aanvoer vader- moederdieren niet verplicht gemaakt */
$versie = "18-11-2016"; /* Controle 'levnr bestaat al' gewijzigd. Geldt nl. alleen indien op stallijst. Controle op dood dier toegevoegd t.b.v. aanvoer */
$versie = "19-11-2016"; /* Variabele $levnr bestaat alleen als er een levensnummer is ingevuld    21771 */
$versie = "22-1-2017"; /* 18-1-2017 Query's aangepast n.a.v. nieuwe tblDoel en $hok_uitgez = 'Gespeend' gewijzigd in $hok_uitgez = 2		22-1-2017 tblBezetting gewijzigd naar tblBezet */
$versie = "12-2-2017"; /* Halsnummer toegvoegd en komma bij geboorte gewicht omgezet naar een punt 	19-2-2017 aantal handmatig ingevoerde schapen gebaseerd op tblStal zodat opnieuw aanvoer ook wordt geteld.		4-4-2017 : kleuren halsnummer uitgebreid */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '10-11-2018'; /* invoer vader- en moederdier aangepast. Worp kan 1 x per 183 dagen en gebeurt op 1 dag 
	Verder zijn er 3 scenario voor invoer vader- en/of moederdier bij invoer levensnummer schaap
  1. Levnr bestaat in db maar heeft geen ouders 	 => Geen drachtdatum en geen registratie 'drachtig'
  2. Levnr bestaat niet in db en het betreft aanvoer => Geen drachtdatum en geen registratie 'drachtig'
  3. Levnr bestaat niet in db, is geen aanvoer en dracht bestaat niet binnen 183 dagen => fictieve drachtdatum en geen registratie 'drachtig'. Geen registratie drachtig zodat pagina 'Dracht.php' alleen met veld drachtig kan filteren/tonen !! */
$versie = '9-1-2019'; /* javascript toegevoegd 13-1 : vaderdier obv dracht mbv javascript */
$versie = '6-2-2019'; /* Vaderdier is tot een jaar terug te kiezen */
$versie = '2-2-2020'; /* keuzelijst geslacht uitgebreid met kween */
$versie = '11-1-2022'; /* Script verbeterd/herschreven. SQL beveiligd d.m.v. quotes. Code aangepast n.a.v. registratie dekkingen en dracht */
$versie = '05-02-2022'; /* Drachtig (ja/nee) wordt niet meer vastgelegd in tblVolwas */
 session_start();  ?>
<html>
<head>
<title>Registratie</title>
<style type= "text/css">

</style>
<?php include"kalender.php"; ?>

</head>
<body>

<center>
<?php
$titel = 'Invoeren schaap';
$subtitel = '';
Include "header.php"; ?>
		<TD width = "960" height = "400" valign = "top" >
<?php
$file = "InvSchaap.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { 


// Array tbv javascript om fase automatisch te tonen bij bestaande dieren
$zoek_fase = mysqli_query($db,"
SELECT s.levensnummer, 'moeder' fase
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h ON (h.stalId = st.stalId)
 join (
     SELECT stalId, datum
     FROM tblHistorie
     WHERE actId = 1
 ) hg ON (hg.stalId = st.stalId)
WHERE h.actId = 3 and geslacht = 'ooi' and date_add(hg.datum,interval 10 year) > CURRENT_DATE()

UNION

SELECT s.levensnummer, 'moeder' fase
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h ON (h.stalId = st.stalId)
 left join (
     SELECT stalId, datum
     FROM tblHistorie
     WHERE actId = 1
 ) hg ON (hg.stalId = st.stalId)
WHERE h.actId = 3 and isnull(hg.stalId) and geslacht = 'ooi' and date_add(s.dmcreatie,interval 10 year) > CURRENT_DATE()

UNION

SELECT s.levensnummer, 'vader' fase
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h ON (h.stalId = st.stalId)
 join (
     SELECT stalId, datum
     FROM tblHistorie
     WHERE actId = 1
 ) hg ON (hg.stalId = st.stalId)
WHERE h.actId = 3 and geslacht = 'ram' and date_add(hg.datum,interval 10 year) > CURRENT_DATE()

UNION

SELECT s.levensnummer, 'vader' fase
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h ON (h.stalId = st.stalId)
 left join (
     SELECT stalId, datum
     FROM tblHistorie
     WHERE actId = 1
 ) hg ON (hg.stalId = st.stalId)
WHERE h.actId = 3 and isnull(hg.stalId) and geslacht = 'ram' and date_add(s.dmcreatie,interval 10 year) > CURRENT_DATE()

UNION

SELECT s.levensnummer, 'lam' fase
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 left join (
     SELECT stalId
     FROM tblHistorie
	 WHERE actId = 3
 ) h ON (h.stalId = st.stalId)
 join (
     SELECT stalId, datum
     FROM tblHistorie
     WHERE actId = 1
 ) hg ON (hg.stalId = st.stalId)
 WHERE isnull(h.stalId) and s.levensnummer is not null and date_add(hg.datum,interval 10 year) > CURRENT_DATE()

 UNION

SELECT s.levensnummer, 'lam' fase
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 left join (
     SELECT stalId
     FROM tblHistorie
     WHERE actId = 3
 ) h ON (h.stalId = st.stalId)
 left join (
     SELECT stalId
     FROM tblHistorie
     WHERE actId = 1
 ) hg ON (hg.stalId = st.stalId)
 WHERE isnull(h.stalId) and s.levensnummer is not null and isnull(hg.stalId) and date_add(s.dmcreatie,interval 10 year) > CURRENT_DATE()
") or die (mysqli_error($db));

while ( $zf = mysqli_fetch_assoc($zoek_fase)) { $array_fase_bij_dier[$zf['levensnummer']] = $zf['fase']; }
// Einde Array tbv javascript om fase automatisch te tonen bij bestaande dieren


// Array tbv javascript om vader automatisch te tonen
	// Zoek de laatste dekkingen. Deze laatste dekking moet een vader hebben geregistreerd
	// Als er een dracht bestaat in tblDracht moet deze niet zijn verwijderd (zie hd.skip = 0)
$zoek_laatste_dekkingen = mysqli_query($db,"
SELECT v.mdrId, right(vdr.levensnummer,$Karwerk) lev
FROM tblVolwas v
 join (
 	SELECT v.mdrId, max(v.volwId) volwId
	FROM tblVolwas v
	 left join tblHistorie hv on (hv.hisId = v.hisId)
	 left join tblDracht d on (v.volwId = d.volwId)
	 left join tblHistorie hd on (hd.hisId = d.hisId)
	 left join tblSchaap k on (k.volwId = v.volwId)
     left join (
        SELECT s.schaapId
        FROM tblSchaap s
         join tblStal st on (s.schaapId = st.schaapId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3
     ) ha on (k.schaapId = ha.schaapId)
	WHERE (isnull(hv.hisId) or hv.skip = 0) and (isnull(hd.hisId) or hd.skip = 0) and isnull(ha.schaapId)
	GROUP BY v.mdrId
 ) lv on (v.volwId = lv.volwId)
 join tblSchaap vdr on (vdr.schaapId = v.vdrId)
") or die (mysqli_error($db));

while ( $zld = mysqli_fetch_assoc($zoek_laatste_dekkingen)) { $array_vader_uit_koppel[$zld['mdrId']] = $zld['lev']; }

// Einde Array tbv javascript om vader automatisch te tonen

// Array tbv javascript om werpdatum automatisch te tonen
	// Zoek de laatste dekkingen. Vervolgens daarvan actuele worpen (binnen de laatste 30 dagen) zoeken en werpdatum tonen
$zoek_werpdatum_laatste_dekking = mysqli_query($db,"
SELECT v.mdrId, date_format(h.datum,'%d-%m-%Y') werpdm
FROM tblVolwas v
 join (
 	SELECT v.mdrId, max(v.volwId) volwId
	FROM tblVolwas v
	 left join tblHistorie hv on (hv.hisId = v.hisId)
	 left join tblDracht d on (v.volwId = d.volwId)
	 left join tblHistorie hd on (hd.hisId = d.hisId)
	 left join tblSchaap k on (k.volwId = v.volwId)
     left join (
        SELECT s.schaapId
        FROM tblSchaap s
         join tblStal st on (s.schaapId = st.schaapId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3
     ) ha on (k.schaapId = ha.schaapId)
	WHERE (isnull(hv.hisId) or hv.skip = 0) and (isnull(hd.hisId) or hd.skip = 0) and isnull(ha.schaapId)
	GROUP BY v.mdrId
 ) lv on (v.volwId = lv.volwId)
 join tblSchaap l on (l.volwId = v.volwId)
 join tblStal st on (st.schaapId = l.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE h.actId = 1 and date_add(h.datum,interval 30 day) > CURRENT_DATE()
GROUP BY v.mdrId, h.datum
") or die (mysqli_error($db));

while ( $zwld = mysqli_fetch_assoc($zoek_werpdatum_laatste_dekking)) { $array_worp[$zwld['mdrId']] = $zwld['werpdm']; }
// Einde Array tbv javascript om werpdatum automatisch te tonen

?>

<script>
function verplicht() {
var levnr = document.getElementById("levnr"); 		var levnr_v = levnr.value;
var fase  = document.getElementById("fase");		var fase_v = fase.value;
var sekse = document.getElementById("sekse");		var sekse_v = sekse.value;
var gebdm = document.getElementById("datepicker1");	var gebdm_v = gebdm.value;
var gewicht = document.getElementById("gewicht");	if(gewicht) { var gewicht_v = gewicht.value; } // bij modtech bestaat variable gewicht niet. Daardoor werkt deze functie niet.
var verblijf = document.getElementById("verblijf");	if(verblijf) { var verblijf_v = verblijf.value; } // bij modtech bestaat variable verblijf niet. Daardoor werkt deze functie niet.
var moment = document.getElementById("moment");		var moment_v = moment.value;
var uitvdm = document.getElementById("datepicker3"); var uitvdm_v = uitvdm.value;
var reden = document.getElementById("reden"); 		var reden_v = reden.value;
var aanvdm = document.getElementById("datepicker2"); var aanvdm_v = aanvdm.value;


	 if(levnr_v.length > 0 && levnr_v.length != 12) levnr.focus() 	+ alert("Het levensnummer moet uit 12 cijfers bestaan.");

else if(isNaN(levnr_v)) levnr.focus() 	+ alert("Het levensnummer bevat een letter.");

else if(fase_v.length == 0) fase.focus() 	+ alert("Generatie moet zijn ingevuld.");

else if((fase_v == 'moeder' && sekse_v == 'ram') || (fase_v == 'vader' && sekse_v == 'ooi')) fase.focus() 	+ alert("Geslacht en generatie zijn tegenstrijdig !");

else if(window.getComputedStyle(gebdm).display === "inline-block" && gebdm_v.length == 0 && fase_v == 'lam') gebdm.focus() 	+ alert("De geboortedatum moet zijn ingevuld.");

else if(levnr_v.length > 0 && gewicht_v.length == 0 && fase_v == 'lam' && moment_v.length == 0 && uitvdm_v.length == 0 && reden_v.length == 0) gewicht.focus() 	+ alert("Het gewicht moet zijn ingevuld.");

else if(verblijf_v.length > 0 && (moment_v.length > 0 || uitvdm_v.length > 0 || reden_v.length > 0))  verblijf.focus() 	+ alert("U kunt geen dood schaap in een verblijf plaatsen !");

else if(fase_v == 'lam' && aanvdm_v.length > 0)  aanvdm.focus()  + alert("Alleen volwassen dieren kunnen worden aangekocht.");

else if(fase_v != 'lam' && gewicht_v.length > 0)  gewicht.focus()  + alert("Bij invoer van een volwassen dier mag geen gewicht worden ingevoerd.");

}

function kies_generatie() {

var fase  = document.getElementById("fase");		var fase_v = fase.value;

if(fase_v.length == 0) fase.focus() 	+ alert("Kies eerst een generatie.");

}

function toon_dracht() {

var moeder = document.getElementById("moeder");		var moeder_v = moeder.value;


 if(moeder_v.length > 0) toon_vader_uit_koppel(moeder_v); toon_werpdatum(moeder_v);

}

 var jArray_vdr = <?php echo json_encode($array_vader_uit_koppel); ?>;

function toon_vader_uit_koppel(m) {
	//document.getElementById('result_vader').innerHTML = jArray_vdr[m];

	var fase = document.getElementById("fase");		var fase_v = fase.value;

 	if(jArray_vdr[m] != null && fase_v == 'lam')
 	{
	document.getElementById('vader').style.display = "none";
  	document.getElementById('vader').value = null; // veld leegmaken indien gevuld
  	document.getElementById('result_vader').innerHTML = jArray_vdr[m];
	}
  	else 
  	{
  	//document.getElementById('vader').style.display = "block";
	document.getElementById('vader').style.display= "inline-block";
	document.getElementById('result_vader').innerHTML = "";
  	}
}

var jArray_worp = <?php echo json_encode($array_worp); ?>;

function toon_werpdatum(m) {

  var fase = document.getElementById("fase");		var fase_v = fase.value;

  if(jArray_worp[m] != null && fase_v == 'lam')
  {
  document.getElementById('datepicker1').style.display = "none";
  document.getElementById('datepicker1').value = null; // veld leegmaken indien gevuld
  document.getElementById('result_werpdatum').innerHTML = jArray_worp[m];
  document.getElementById('bijschrift').innerHTML = "";
  }
  else
  {
  document.getElementById('datepicker1').style.display = "inline-block";
  document.getElementById('result_werpdatum').innerHTML = "";
  document.getElementById('bijschrift').innerHTML = "&nbsp* / **";
  }
}

</script>
<?php

include "vw_kzlOoien.php";

function numeriek($subject) {
	if (preg_match('/([[a-zA-Z])/', $subject, $matches)) {  /*var_dump($matches[1]); */ return 1; }
}


if (!empty($_POST['txtLevnr'])) { $levnr = $_POST['txtLevnr'];  }
if (isset($_POST['txtindex'])) { $index = $_POST['txtindex'];  }

if(isset($levnr)) {
$zoek_in_stallijst = mysqli_query($db, "
SELECT s.schaapId 
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and levensnummer = '".mysqli_real_escape_string($db,$levnr)."' and isnull(st.rel_best)
") or die (mysqli_error($db));
	while($stl = mysqli_fetch_assoc($zoek_in_stallijst)) {	$aanwezig = $stl['schaapId']; }	
	
$zoek_dood = mysqli_query($db, "
SELECT s.schaapId 
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE levensnummer = '".mysqli_real_escape_string($db,$levnr)."' and h.actId = 14
") or die (mysqli_error($db));
	while($do = mysqli_fetch_assoc($zoek_dood)) {	$dood = $do['schaapId']; }
}


/***********************
 ****	OPSLAAN		****
 ***********************/
if (isset($_POST['knpSave']))
{
	#echo '$levnr = '.$levnr.'<br>';
	if(isset($levnr)) { // Zoek naar een bestaand levensnummer. Bijvoorbeeld die een andere gebruiker al eens heeft ingevoerd of opnieuw aanvoer.
$query_bestaand_levensnummer = "
SELECT s.schaapId, s.geslacht, s.volwId, v.mdrId, hg.datum dmgeb, h1.datum dmeerste, date_format(h1.datum,'%d-%m-%Y') eerstedm, ha.datum dmaanw, haf.datum dmafv, date_format(haf.datum,'%d-%m-%Y') afvdm
FROM tblSchaap s
 left join tblVolwas v on (s.volwId = v.volwId)
 left join (
	SELECT s.schaapId, h.datum
	FROM tblSchaap s
	 join tblStal st on (s.schaapId = st.schaapId)
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 1 and s.levensnummer = '".mysqli_real_escape_string($db,$levnr)."'
 ) hg on (s.schaapId = hg.schaapId)
 left join (
	SELECT his1.schaapId, h.datum
	FROM tblHistorie h
	join (
		SELECT st.schaapId, min(h.hisId) hisId
		FROM tblSchaap s
		 join tblStal st on (s.schaapId = st.schaapId)
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE s.levensnummer = '".mysqli_real_escape_string($db,$levnr)."'
		GROUP BY st.schaapId
	) his1 on (his1.hisId = h.hisId)
 ) h1 on (s.schaapId = h1.schaapId)
 left join (
	SELECT s.schaapId, h.datum
	FROM tblSchaap s
	 join tblStal st on (s.schaapId = st.schaapId)
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and s.levensnummer = '".mysqli_real_escape_string($db,$levnr)."'
 ) ha on (s.schaapId = ha.schaapId)
 left join (
	SELECT afv.schaapId, h.datum
	FROM tblHistorie h
	join (
		SELECT st.schaapId, max(h.hisId) hisId
		FROM tblSchaap s
		 join tblStal st on (s.schaapId = st.schaapId)
		 join tblHistorie h on (st.stalId = h.stalId)
		 join tblActie a on (h.actId = a.actId)
		WHERE a.af = 1 and s.levensnummer = '".mysqli_real_escape_string($db,$levnr)."'
		GROUP BY st.schaapId
	) afv on (afv.hisId = h.hisId)
 ) haf on (s.schaapId = haf.schaapId)
WHERE levensnummer = '".mysqli_real_escape_string($db,$levnr)."'

";
/*echo $query_bestaand_levensnummer.'<br>';*/	$zoek_bestaand_levensnummer = mysqli_query($db,$query_bestaand_levensnummer) or die (mysqli_error($db));
	while($lvn = mysqli_fetch_assoc($zoek_bestaand_levensnummer)) {
		$levnr_db = $lvn['schaapId'];
		$mdrId_db = $lvn['mdrId'];
		$volwId_db = $lvn['volwId'];
		$dmgeb_db = $lvn['dmgeb'];
		$dmeerste_db = $lvn['dmeerste'];
		$eerstedm_db = $lvn['eerstedm'];
		$aanwas_db = $lvn['dmaanw'];
		$dmafvoer_db = $lvn['dmafv'];
		$laatste_afvoerdm = $lvn['afvdm']; 
		}
	} // Einde if(isset($levnr))

if(!empty($_POST['kzlKleur']))	{ $kzlKleur = $_POST['kzlKleur']; }
if(!empty($_POST['txtHalsnr']))	{ $txtHalsnr = $_POST['txtHalsnr']; }

if(!empty($_POST['kzlFase']))	{ $kzlFase = $_POST['kzlFase']; } if($kzlFase != 'lam') { $invoer = 'aanvoer'; }
if(!empty($_POST['kzlSekse']))	{ $kzlSekse = $_POST['kzlSekse']; }
if(!empty($_POST['kzlRas']))	{ $kzlRas = $_POST['kzlRas']; }

if(!empty($_POST['kzlOoi']))	{ $kzlOoi = $_POST['kzlOoi']; $moeder = $kzlOoi; } else if(isset($mdrId_db)) { $moeder = $mdrId_db; }
if(!empty($_POST['kzlRam']))	{ $kzlRam = $_POST['kzlRam']; }

if(!empty($_POST['txtGebkg']))	{ $txtGebkg = str_replace(',', '.', $_POST['txtGebkg']); }
if(!empty($_POST['txtGebdm'])) 	{ $gebdm = date_create($_POST['txtGebdm']); $txtGebdm = $_POST['txtGebdm']; }
	else { $gebdm = date_create($array_worp[$kzlOoi]); $txtGebdm = $array_worp[$kzlOoi]; }
		
	if(isset($txtGebdm)) { $txtDmgeb =  date_format($gebdm, 'Y-m-d'); } //echo 'gebdm is leeg dus '.$txtDmgeb.'<br>'; } #/#
	
if(!empty($_POST['txtAanv']))	{ $aanvdm = date_create($_POST['txtAanv']); $txtAanvdm = $_POST['txtAanv'];
		$txtDmaanv = date_format($aanvdm, 'Y-m-d');
}

//echo '$txtGebdm = '.$txtGebdm.' - '.$txtDmgeb.'<br>'; #/# kenmerk om tijdelijke echo's te traceren
//echo '$kzlRam = '.$kzlRam.'<br>'; #/# kenmerk om tijdelijke echo's te traceren



if(!empty($_POST['kzlMoment'])) { $kzlMoment = $_POST['kzlMoment']; }
if(!empty($_POST['txtUitvdm'])) { $uitvdm = date_create($_POST['txtUitvdm']);  $txtUitvdm = $_POST['txtUitvdm'];
	$txtDmuitv =  date_format($uitvdm, 'Y-m-d');
}
if(!empty($_POST['kzlReden'])) { $kzlReden = $_POST['kzlReden']; }

if(!empty($_POST['kzlHok'])) { $kzlHok = $_POST['kzlHok']; }



if(isset($moeder)) {
$query_startdm_moeder = mysqli_query($db,"
SELECT h.datum
FROM (
	SELECT stalId
	FROM tblStal
	WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(rel_best) and schaapId = '".mysqli_real_escape_string($db,$moeder)."'
 ) minst
 join tblHistorie h on (minst.stalId = h.stalId)
 join tblActie a on (h.actId = a.actId)
WHERE a.op = 1 and h.skip = 0
") or die (mysqli_error($db)); 
		while($mdrdm = mysqli_fetch_array($query_startdm_moeder))
		{ $startmdr = $mdrdm['datum']; }

$zoek_eindm_mdr_indien_afgevoerd = mysqli_query($db,"
SELECT h.datum
FROM (
	SELECT max(stalId) stalId, schaapId
	FROM tblStal
	WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and schaapId = '".mysqli_real_escape_string($db,$moeder)."'
	GROUP BY schaapId
 ) maxst
 join tblStal st on (st.stalId = maxst.stalId)
 join tblHistorie h on (h.stalId = st.stalId)
WHERE rel_best is not null
") or die (mysqli_error($db)); 
		while($mdrdm = mysqli_fetch_array($zoek_eindm_mdr_indien_afgevoerd))
		{ $endmdr = $mdrdm['datum']; }

// Zoek naar laatste worp ter controle dat deze minstens 183 dagen is geleden.
if(isset($txtDmgeb)) {

$zoek_laatste_worp = mysqli_query($db,"
SELECT max(l.volwId) volwId
FROM tblSchaap l
 join tblVolwas v on (l.volwId = v.volwId)
 join tblStal st on (l.schaapId = st.schaapId)
 join tblHistorie h on (h.stalId = st.stalId) 
 left join tblSchaap k on (k.volwId = v.volwId)
     left join (
        SELECT s.schaapId
        FROM tblSchaap s
         join tblStal st on (s.schaapId = st.schaapId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3
     ) ha on (k.schaapId = ha.schaapId)
WHERE v.mdrId = '".mysqli_real_escape_string($db,$kzlOoi)."' and h.actId = 1 and isnull(ha.schaapId)
") or die (mysqli_error($db));
while ( $zlw = mysqli_fetch_assoc($zoek_laatste_worp)) { $lst_volwId = $zlw['volwId']; }

$zoek_datum_laatste_worp = mysqli_query($db,"
SELECT h.datum, date_format(h.datum,'%d-%m-%Y') dag
FROM tblSchaap l
 join tblStal st on (l.schaapId = st.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
WHERE h.actId = 1 and l.volwId = '" . mysqli_real_escape_string($db,$lst_volwId) . "'
") or die (mysqli_error($db));
while ( $zdlw = mysqli_fetch_assoc($zoek_datum_laatste_worp)) { $lst_dmworp = $zdlw['datum']; $lst_worpdm = $zdlw['dag']; }

$datetime1 = date_create($lst_dmworp);
$verschil_worp = date_diff($datetime1, $gebdm);


/*echo '$kzlOoi = '.$kzlOoi.'<br>'; #/#
echo '$lst_volwId = '.$lst_volwId.' - '.$lst_worpdm.'<br>'; #/#
echo '$verschil_worp = '.$verschil_worp->days.'<br>';*/ #/#

} // Einde Zoek naar laatste worp ter controle dat deze minstens 183 dagen is geleden.

} // Einde if(isset($moeder))
	




// Controle moederdier bij reeds geregistreerd levensnummer
	if(isset($levnr_db) && isset($mdrId_db) && isset($kzlOoi) && $mdrId_db <> $kzlOoi) {
		 $fout = "Dit dier heeft al een moeder. Ooi (en ram) wordt niet opgeslagen. "; 
		}
// Einde Controle moederdier bij reeds geregistreerd levensnummer

// CONTROLE OP JUISTE INVOER
 if (isset($levnr) && !isset($kzlSekse) && !isset($levnr_db) && !isset($kzlMoment) && !isset($txtDmuitv) && !isset($kzlReden) )
	{
		$fout = "Het geslacht moet zijn ingevuld.";
	}

else if (isset($levnr) && !isset($kzlRas) && !isset($levnr_db) && !isset($kzlMoment) && !isset($txtDmuitv) && !isset($kzlReden) )
	{
		$fout = "Het ras moet zijn ingevuld.";
	}

else if ($modtech == 1 && !isset($kzlOoi) && $kzlFase == 'lam' ) 
	{
		$fout = "Het moederdier moet zijn ingevuld.";
	}

/* per 13-1-2022 met javascript gecontroleerd
else if ($modtech == 1 && isset($levnr) && !isset($txtGebkg) && $kzlFase == 'lam' && !isset($kzlMoment) && !isset($txtDmuitv) && !isset($kzlReden) )
	{
		$fout = "Het gewicht moet zijn ingevuld.";
	}*/

else if ( $kzlFase == 'lam' && !empty($_POST['txtindex']) )
	{
		$fout = "De index kan alleen bij een volwassen dier worden ingevoerd.";
	}

else if ( (isset($kzlMoment) && !isset($txtDmuitv) )
	  || (isset($kzlReden) && !isset($txtDmuitv) )
	  || (!isset($levnr) && !isset($txtDmuitv)  ) )
	{
		$fout = "Bij overlijden moet datum t.b.v. uitval zijn ingevuld.";
	}

else if ( isset($txtDmuitv) && isset($txtGebdm) && $txtDmuitv < $txtDmgeb )
	{
		$fout = "Datum overlijden kan niet voor geboortedatum liggen !";
	}

else if ( isset($txtDmuitv) && isset($txtAanvdm) && $txtDmuitv < $txtDmaanv )
	{
		$fout = "Datum overlijden kan niet voor aanschafdatum liggen !";
	}

else if ( isset($txtGebdm) && isset($txtAanvdm) && $txtDmaanv < $txtDmgeb )
	{
		$fout = "Datum aanschaf kan niet voor geboortedatum liggen !";
	}
	
else if ($modtech == 1 && !isset($kzlHok) && $kzlFase == 'lam' && !isset($kzlMoment) && !isset($txtDmuitv) && !isset($kzlReden) )
	{
		$fout = "Plaats het lam ook nog in een verblijf.";
	}
	
else if ( !empty($aanwezig) && isset($levnr) )
	{
		$fout = "Dit dier staat al op de stallijst.";
	}
	
else if ( !isset($txtAanvdm) && ($kzlFase == 'moeder' || $kzlFase == 'vader' || (isset($levnr_db) && isset($aanwas_db))) )
	{
		$fout = "Bij invoer van een volwassen dier is de aanschafdatum verplicht.";
	}
	
//else if ($kzlFase == 'lam' && isset($txtDmgeb) && $txtDmgeb < $startmdr) // $txtDmgeb bestaat niet als werpdatum wordt gepresenteerd d.m.v. javascript i.p.v. het veld geboortedatum

else if ($kzlFase == 'lam' && $txtDmgeb < $startmdr)
	{
		$fout = "Geboortedatum kan niet voor aanvoerdatum van moederdier liggen.";

	}

else if ($kzlFase == 'lam' && isset($endmdr) && $endmdr < $txtDmgeb)
	{
		$fout = "Geboortedatum kan niet na afvoerdatum van moederdier liggen.";
	}

else if (!isset($dmgeb_db) && isset($txtGebdm) && isset($levnr_db) && $dmeerste_db < $txtDmgeb)
	{
		$fout = "Geboortedatum kan niet na ".$eerstedm_db." liggen.";
	}

else if (($kzlFase == 'moeder' || $kzlFase == 'vader' || (isset($levnr_db) && isset($aanwas_db)) ) && isset($txtAanvdm) && isset($dmafvoer_db) && $txtDmaanv < $dmafvoer_db)
	{
		$fout = "Aanvoerdatum kan niet voor ".$laatste_afvoerdm." liggen.";
	}

else if (isset($dood)) // Bestaand levensnummer dat reeds is overleden. T.b.v. aankoop volwassen dieren.
	{
		$fout = "Dit is een overleden schaap.";
	}

else if (isset($txtDmuitv) && isset($levnr_db)) // Dood dier met levensnummer dat al voorkomt in tblSchaap. Controle t.b.v. doodgeboren lam
	{
		$fout = "Dit levensnummer bestaat al.";
	}

else if (isset($verschil_worp) && $verschil_worp->days < 183 && $verschil_worp->days <> 0) // Het moederdier kan van deze dracht al een lam hebben. Die geboortedatum moet gelijk liggen aan de geboortedatum van dit schaap. Mits deze een geboortedatum heeft.
	{
		if($verschil_worp->days < 10) {
		$fout = "Deze ooi heeft reeds geworpen op " . $lst_worpdm . ". Dat moet dus de geboortedatum zijn van dit schaap.";
			$txtGebdm = $lst_worpdm;
		}
		else{
			$fout = "Deze ooi heeft op " . $lst_worpdm . " nog geworpen. Een ooi kan 1 x per half jaar werpen.";
		}
	}

else if (!isset($levnr_db) && $kzlFase == 'lam' && isset($dmdracht) && $txtDmgeb < $dmdracht)
	{
		$fout = 'De geboortedatum mag niet voor drachtdatum ('.$drachtdm.') liggen.';
	}
// EINDE  CONTROLE OP JUISTE INVOER
else 
	{
// DATABASE BIJWERKEN

// ********************
// 	  BEPAAL VOLWID
// ********************

// Bepaal volwId bij geboren lam

if($modtech == 1 && !isset($levnr_db) && $kzlFase == 'lam') { // Als levnr niet bestaat in database en het is geen aanvoer
	#testmelding $goed = 'Geboren. Als levnr niet bestaat in database en het is geen aanvoer';

$zoek_actuele_worp = "
SELECT v.volwId
FROM tblVolwas v
 join tblSchaap l on (l.volwId = v.volwId)
 join tblStal stl on (stl.schaapId = l.schaapId)
 join tblHistorie h on (h.stalId = stl.stalId)
WHERE v.mdrId = '" . mysqli_real_escape_string($db,$kzlOoi) . "' and h.actId = 1 and h.datum = '" . mysqli_real_escape_string($db,$txtDmgeb) . "'
";

//echo '$zoek_actuele_worp = <br>'.$zoek_actuele_worp.'<br>'; #/#
$zoek_actuele_worp = mysqli_query($db,$zoek_actuele_worp) or die (mysqli_error($db));


while ($zaw = mysqli_fetch_assoc($zoek_actuele_worp)) { $volwId = $zaw['volwId']; }
//echo 'resultaat = '.$volwId.'<br><br>'; #/#

if(!isset($volwId)) {

$zoek_vorige_worp = "
SELECT max(l.volwId) volwId
FROM tblSchaap l
 join tblVolwas v on (l.volwId = v.volwId)
 join tblStal st on (l.schaapId = st.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 left join tblSchaap k on (k.volwId = v.volwId)
 left join (
    SELECT s.schaapId
    FROM tblSchaap s
     join tblStal st on (s.schaapId = st.schaapId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 ) ha on (k.schaapId = ha.schaapId)
WHERE v.mdrId = '".mysqli_real_escape_string($db,$kzlOoi)."' and h.actId = 1 and h.datum < '".mysqli_real_escape_string($db,$txtDmgeb)."' and isnull(ha.schaapId)
";

//echo '$zoek_vorige_worp = <br>'.$zoek_vorige_worp.'<br>'; #/#
$zoek_vorige_worp = mysqli_query($db,$zoek_vorige_worp) or die (mysqli_error($db));


while ( $zvw = mysqli_fetch_assoc($zoek_vorige_worp)) { $lst_volwId = $zvw['volwId']; }
//echo 'resultaat = '.$lst_volwId.'<br><br>'; #/#

$zoek_actuele_dracht = "
SELECT v.volwId
FROM tblVolwas v
 join tblDracht d on (d.volwId = v.volwId)
 join tblHistorie h on (h.hisId = d.hisId)
WHERE h.skip = 0 and v.mdrId = '" . mysqli_real_escape_string($db,$kzlOoi) . "' and v.volwId > '".mysqli_real_escape_string($db,$lst_volwId)."'
";

//echo '$zoek_actuele_dracht = <br>'.$zoek_actuele_dracht.'<br>'; #/#
$zoek_actuele_dracht = mysqli_query($db,$zoek_actuele_dracht) or die (mysqli_error($db));

while ($zadr = mysqli_fetch_assoc($zoek_actuele_dracht)) { $volwId = $zadr['volwId']; }
//echo 'resultaat = '.$volwId.'<br><br>'; #/#
}


if(!isset($volwId)) {

$zoek_actuele_dekking = "
SELECT max(v.volwId) volwId
FROM tblVolwas v
 join tblHistorie h on (h.hisId = v.hisId)
WHERE h.skip = 0 and v.mdrId = '" . mysqli_real_escape_string($db,$kzlOoi) . "' and v.volwId > '".mysqli_real_escape_string($db,$lst_volwId)."'
";

//echo '$zoek_actuele_dekking = <br>'.$zoek_actuele_dekking.'<br>'; #/#
$zoek_actuele_dekking = mysqli_query($db,$zoek_actuele_dekking) or die (mysqli_error($db));

while ($zade = mysqli_fetch_assoc($zoek_actuele_dekking)) { $volwId = $zade['volwId']; }
//echo 'resultaat = '.$volwId.'<br><br>'; #/#
}


if(isset($volwId) && isset($kzlRam)) {
// Als er een actuele volwId bestaat kan hier eventueel alsnog een vader worden toegevoegd aan het koppel
$zoek_vader_uit_koppel = mysqli_query($db,"
 SELECT vdrId
 FROM tblVolwas
 WHERE volwId = '".mysqli_real_escape_string($db,$volwId)."'
 ") or die (mysqli_error($db));
  while ( $zva = mysqli_fetch_assoc($zoek_vader_uit_koppel)) { $vdrId = $zva['vdrId']; }

if(!isset($vdrId)){

$updateKoppel = "UPDATE tblVolwas set vdrId = '".mysqli_real_escape_string($db,$kzlRam)."' WHERE volwId = '".mysqli_real_escape_string($db,$volwId)."' " ; 

/*echo "$updateKoppel".'<br>'.'<br>';  ##*/ mysqli_query($db,$updateKoppel) or die (mysqli_error($db));
}

// Einde Als er een actuele volwId bestaat kan hier eventueel alsnog een vader worden toegevoegd aan het koppel
}

if(!isset($volwId)) {

  // Koppel maken
 $insert_tblVolwas = "INSERT INTO tblVolwas set mdrId = '".mysqli_real_escape_string($db,$kzlOoi)."', vdrId = " . db_null_input($kzlRam);

/*echo $insert_tblVolwas.'<br>';  ##*/mysqli_query($db,$insert_tblVolwas) or die (mysqli_error($db));
  // Einde Koppel maken

 $zoek_volwId = mysqli_query($db,"
 SELECT max(volwId) volwId
 FROM tblVolwas
 WHERE mdrId = '".mysqli_real_escape_string($db,$kzlOoi)."'
 ") or die (mysqli_error($db));
  while ( $zv = mysqli_fetch_assoc($zoek_volwId)) { $volwId = $zv['volwId']; }
}


} // Einde if($modtech == 1 && !isset($levnr_db) && $kzlFase == 'lam')

// Einde Bepaal volwId bij geboren lam

// Bepaal volwId bij aanvoer
if( ($modtech == 1 && (isset($kzlOoi) || isset($kzlRam)) ) && (
  (isset($levnr_db) && !isset($volwId_db) ) || // levnr bestaat in db maar heeft geen ouders en nu wel
  (!isset($levnr_db) && ($kzlFase == 'moeder' || $kzlFase == 'vader') )  // Levnr bestaat niet in db en het betreft aanvoer met registratie ouders
) )
{


// Controle nieuwe worp. Deze moet 183 dagan van vorige worp of volgende worp liggen.
if(isset($txtDmgeb) && isset($kzlOoi)) {
$zoek_bestaande_worp = "
SELECT v.volwId
FROM tblVolwas v
 join tblSchaap l on (l.volwId = v.volwId)
 join tblStal stl on (stl.schaapId = l.schaapId)
 join tblHistorie h on (h.stalId = stl.stalId)
WHERE v.mdrId = '" . mysqli_real_escape_string($db,$kzlOoi) . "' and h.actId = 1 and h.datum = '" . mysqli_real_escape_string($db,$txtDmgeb) . "'
";

//echo '$zoek_bestaande_worp = <br>'.$zoek_bestaande_worp.'<br>'; #/#
$zoek_bestaande_worp = mysqli_query($db,$zoek_bestaande_worp) or die (mysqli_error($db));


while ($zbw = mysqli_fetch_assoc($zoek_bestaande_worp)) { $volwId = $zbw['volwId']; }

if(!isset($volwId)) {
// Zoek de vorige worp t.o.v. de geboorte datum. Dus ongeacht wanneer de volwId is geregistreerd, max(volwId) geldt dus niet!
$zoek_laatste_worp_voor_geboortedatum = "
SELECT max(h.datum) datum, date_format(max(h.datum),'%d-%m-%Y') dag
FROM tblSchaap l
 join tblVolwas v on (l.volwId = v.volwId)
 join tblStal st on (l.schaapId = st.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
WHERE v.mdrId = '".mysqli_real_escape_string($db,$kzlOoi)."' and h.actId = 1 and h.datum < '".mysqli_real_escape_string($db,$txtDmgeb)."'
";
//echo '$zoek_laatste_worp_voor_geboortedatum = <br>'.$zoek_laatste_worp_voor_geboortedatum.'<br>'; #/#
$zoek_laatste_worp_voor_geboortedatum = mysqli_query($db,$zoek_laatste_worp_voor_geboortedatum) or die (mysqli_error($db));

while ( $zlw = mysqli_fetch_assoc($zoek_laatste_worp_voor_geboortedatum)) { $vorige_dmworp = $zlw['datum']; $vorige_worpdm = $zlw['dag']; }

$date_vorige = date_create($vorige_dmworp);
$verschil_vorige_worp = date_diff($date_vorige, $gebdm);
$dagen_vorige_worp = $verschil_vorige_worp->days;


/*echo '$txtGebdm = '.$txtGebdm.'<br>';
echo '$vorige_worpdm = '.$vorige_worpdm.'<br>';
echo '$dagen_vorige_worp = '.$dagen_vorige_worp.'<br>';*/ #/#


// Zoek de volgende worp t.o.v. de geboorte datum. Dus ongeacht wanneer de volwId is geregistreerd, max(volwId) geldt dus niet!
$zoek_volgende_worp_na_geboortedatum = "
SELECT min(h.datum) datum, date_format(min(h.datum),'%d-%m-%Y') dag
FROM tblSchaap l
 join tblVolwas v on (l.volwId = v.volwId)
 join tblStal st on (l.schaapId = st.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
WHERE v.mdrId = '".mysqli_real_escape_string($db,$kzlOoi)."' and h.actId = 1 and h.datum > '".mysqli_real_escape_string($db,$txtDmgeb)."'
";
//echo '$zoek_volgende_worp_na_geboortedatum = <br>'.$zoek_volgende_worp_na_geboortedatum.'<br>'; #/#
$zoek_volgende_worp_na_geboortedatum = mysqli_query($db,$zoek_volgende_worp_na_geboortedatum) or die (mysqli_error($db));

while ( $zvw = mysqli_fetch_assoc($zoek_volgende_worp_na_geboortedatum)) { $volgend_dmworp = $zvw['datum']; $volgend_worpdm = $zvw['dag']; }

$date_volgende = date_create($volgend_dmworp);
$verschil_volgende_worp = date_diff($gebdm, $date_volgende);
$dagen_volgende_worp = $verschil_volgende_worp->days;

/*echo '<br>';
echo '$txtGebdm = '.$txtGebdm.'<br>';
echo '$volgend_worpdm = '.$volgend_worpdm.'<br>';
echo '$dagen_volgende_worp = '.$dagen_volgende_worp.'<br>';*/ #/#

}

}
// Einde Controle nieuwe worp. Deze moet 183 dagan van vorige worp of volgende worp liggen.

if(isset($verschil_vorige_worp) && $dagen_vorige_worp < 183) {
$fout = "De vorige worp van dit moederdier is ".$vorige_worpdm.". Een ooi kan 1x in het half jaar werpen.";

}
else if(isset($verschil_volgende_worp) && $dagen_volgende_worp < 183) {
$fout = "De volgende worp van dit moederdier is ".$volgend_worpdm.". Een ooi kan 1x in het half jaar werpen.";

}
else {
$insert_tblVolwas = " INSERT INTO tblVolwas SET mdrId = " . db_null_input($kzlOoi) . ", vdrId = " . db_null_input($kzlRam);
/*echo 'Aanvoer =>'. $insert_tblVolwas.'<br>';  ##*/mysqli_query($db,$insert_tblVolwas) or die (mysqli_error($db));

$zoek_volwId = mysqli_query($db,"
SELECT max(volwId) volwId
FROM tblVolwas
WHERE " . db_null_filter(mdrId,$kzlOoi) . " and " . db_null_filter(vdrId,$kzlRam) . "
") or die (mysqli_error($db));
	
	while ( $zv = mysqli_fetch_assoc($zoek_volwId)) { $volwId = $zv['volwId']; }

}

} 
// Einde Bepaal volwId bij aanvoer

// ********************
// EINDE BEPAAL VOLWID
// ********************


// ***************************
// 		GEGEVENS INLEZEN
// ***************************
if (isset($levnr) && !isset($levnr_db) && $kzlFase == 'lam' && !isset($txtDmuitv) )	{ $scenario = 'Geboren_lam'; }
	
else if ( (isset($kzlFase) && $kzlFase != 'lam') || (isset($levnr_db) && isset($aanwas_db)) ) { $scenario = 'Aanvoer_ooi_ram'; }

else if (isset($txtDmuitv) && isset($levnr) && !isset($levnr_db)) { $scenario = 'Dood_lam_met_levensnummer'; }

else if (isset($txtDmuitv) && !isset($levnr)) { $scenario = 'Dood_lam_zonder_levensnummer'; }
 
if(!isset($fout) && isset($scenario)) { //echo '$scenario = '.$scenario.'<br>'; #/#

if(isset($levnr_db)) {
$zoek_schaapId = mysqli_query($db,"
SELECT schaapId
FROM tblSchaap
WHERE levensnummer = '".mysqli_real_escape_string($db,$levnr)."'
") or die (mysqli_error($db));
	while ( $sId = mysqli_fetch_assoc ($zoek_schaapId)) { $schaapId = $sId['schaapId']; }

	//echo 'bestaande schaapId = '.$schaapId.'<br>'; #/#
}

else if($scenario == 'Dood_lam_zonder_levensnummer') {

$insert_tblSchaap = "INSERT INTO tblSchaap SET levensnummer = '".mysqli_real_escape_string($db,$ubn)."', rasId = " . db_null_input($kzlRas) . ", geslacht = " . db_null_input($kzlSekse) . ", volwId = " . db_null_input($volwId) .", momId = " . db_null_input($kzlMoment) . ", redId = " . db_null_input($kzlReden);
	
	/*echo $insert_tblSchaap.'<br>';  ##*/mysqli_query($db,$insert_tblSchaap) or die (mysqli_error($db)); 

$zoek_schaapId = mysqli_query($db,"
SELECT schaapId
FROM tblSchaap
WHERE levensnummer = '".mysqli_real_escape_string($db,$ubn)."'
") or die (mysqli_error($db));
	while ( $sId = mysqli_fetch_assoc ($zoek_schaapId)) { $schaapId = $sId['schaapId']; }

	//echo 'nieuw schaapId met ubn als levensnummer = '.$schaapId.'<br>'; #/#

$update_tblSchaap = "UPDATE tblSchaap SET levensnummer = NULL WHERE levensnummer = '".mysqli_real_escape_string($db,$ubn)."' ";
	mysqli_query($db,$update_tblSchaap) or die (mysqli_error($db));
}

else {


// $kzlRas is bij uitval zonder levensnummer niet verplicht
// $kzlSekse is bij uitval niet verplicht
// $kzlOoi en dus $volwId is alleen verplicht bij geboren lammeren i.c.m. module technisch
$insert_tblSchaap = "INSERT INTO tblSchaap SET levensnummer = '".mysqli_real_escape_string($db,$levnr)."', rasId = " . db_null_input($kzlRas) . ", geslacht = " . db_null_input($kzlSekse) . ", volwId = " . db_null_input($volwId) .", momId = " . db_null_input($kzlMoment) . ", redId = " . db_null_input($kzlReden);
	
	/*echo $insert_tblSchaap.'<br>';  ##*/mysqli_query($db,$insert_tblSchaap) or die (mysqli_error($db)); 

$zoek_schaapId = mysqli_query($db,"
SELECT schaapId
FROM tblSchaap
WHERE levensnummer = '".mysqli_real_escape_string($db,$levnr)."'
") or die (mysqli_error($db));
	while ( $sId = mysqli_fetch_assoc ($zoek_schaapId)) { $schaapId = $sId['schaapId']; }

	//echo 'nieuw schaapId = '.$schaapId.'<br>'; #/#
}

if($scenario == 'Dood_lam_met_levensnummer' || $scenario == 'Dood_lam_zonder_levensnummer') { $rel_best = $rendac_Id; }

$insert_tblStal = "INSERT INTO tblStal SET lidId = '".mysqli_real_escape_string($db,$lidId)."', schaapId = '".mysqli_real_escape_string($db,$schaapId)."', kleur = " . db_null_input($kzlKleur) . ", halsnr = " . db_null_input($txtHalsnr) . ", rel_best = " . db_null_input($rel_best) ;

/*echo $insert_tblStal.'<br>';	##*/mysqli_query($db,$insert_tblStal) or die (mysqli_error($db));
			
$zoek_stalId = mysqli_query($db,"
SELECT max(stalId) stalId
FROM tblStal
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
") or die (mysqli_error($db));
	while ( $stId = mysqli_fetch_assoc ($zoek_stalId)) { $stalId = $stId['stalId']; }

/*****  EINDE INLEZEN SCHAAP EN STAL  *****/	
/*****  INLEZEN HISTORIE  *****/

if(isset($txtDmgeb) && !isset($dmgeb_db)) { // Geboortedatum bij volwassendieren niet verplicht
$insert_tblHistorie_geb = "INSERT INTO tblHistorie SET stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$txtDmgeb)."', kg = " . db_null_input($txtGebkg) . ", actId = 1 ";
			
/*echo $insert_tblHistorie_geb.'<br>';	##*/mysqli_query($db,$insert_tblHistorie_geb) or die (mysqli_error($db));
}

if($scenario == 'Aanvoer_ooi_ram') {

// Aanvoer kan Aankoop zijn of terug van uitscharen. Indien niet terug van uitscharen dan aankoop.
	// max(hisId) moet afvoer zijn anders is de code niet door de controle gekomen
$zoek_Uitscharen = mysqli_query($db,"
SELECT actId
FROM tblHistorie h
 join (
	SELECT max(hisId) hisId
	FROM tblHistorie h
	 join tblStal st on (h.stalId = st.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
 ) lh on (lh.hisId = h.hisId)
") or die (mysqli_error($db));
	while ( $zu = mysqli_fetch_assoc ($zoek_Uitscharen)) { $actie_af_db = $zu['actId']; }

	if($actie_af_db == 10) { $actId_op = 11; } else { $actId_op = 2; }
$insert_tblHistorie_aanv = "INSERT INTO tblHistorie SET stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$txtDmaanv)."', actId = '".mysqli_real_escape_string($db,$actId_op)."' ";
/*echo $insert_tblHistorie_aanv.'<br>';	##*/mysqli_query($db,$insert_tblHistorie_aanv) or die (mysqli_error($db));

if(!isset($aanwas_db)) {
$insert_tblHistorie_aanw = "INSERT INTO tblHistorie SET stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$txtDmaanv)."', actId = 3 ";
/*echo $insert_tblHistorie_aanw.'<br>';	##*/mysqli_query($db,$insert_tblHistorie_aanw) or die (mysqli_error($db));
}

}


if($scenario == 'Dood_lam_met_levensnummer' || $scenario == 'Dood_lam_zonder_levensnummer') {

$insert_tblHistorie_doo = "INSERT INTO tblHistorie SET stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$txtDmuitv)."', actId = 14  ";
/*echo $insert_tblHistorie_doo.'<br>';	##*/mysqli_query($db,$insert_tblHistorie_doo) or die (mysqli_error($db));

}
/*****  EINDE INLEZEN HISTORIE  *****/

if (isset($kzlHok)) {

$zoek_hisId_tbv_tblBezet = mysqli_query($db,"
SELECT max(hisId) hisId
FROM tblHistorie h
 join tblActie a on (h.actId = a.actId)
WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."' and a.aan = 1
") or die (mysqli_error($db));
	while ( $zhb = mysqli_fetch_assoc ($zoek_hisId_tbv_tblBezet)) { $hisId = $zhb['hisId']; }

	$insert_tblBezet = "INSERT INTO tblBezet SET hokId = '".mysqli_real_escape_string($db,$kzlHok)."', hisId = '".mysqli_real_escape_string($db,$hisId)."' ";
/*echo $insert_tblBezet.'<br>';	##*/mysqli_query($db,$insert_tblBezet) or die (mysqli_error($db));		
}


if ($modmeld == 1 && $scenario == 'Geboren_lam') {

$reqst_file = 'InvSchaap.php_geboren';
$Melding = 'GER';
/*##*/include "maak_request_func.php";
/*##*/include "maak_request.php";
}

if ($modmeld == 1 && $scenario == 'Aanvoer_ooi_ram') {

$zoek_hisIdaanv = mysqli_query($db,"
SELECT hisId
FROM tblHistorie
WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."' and actId = '".mysqli_real_escape_string($db,$actId_op)."'
") or die (mysqli_error($db));
		while ( $hId = mysqli_fetch_assoc ($zoek_hisIdaanv)) { $hisId = $hId['hisId']; }
			
$reqst_file = 'InvSchaap.php_aanwas';
$Melding = 'AAN';
/*##*/include "maak_request_func.php";
/*##*/include "maak_request.php";
}

if ($modmeld == 1 && $scenario == 'Dood_lam_met_levensnummer') {
		
$zoek_hisId = mysqli_query($db,"
SELECT hisId
FROM tblHistorie
WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."' and actId = 14
") or die (mysqli_error($db));
	while ( $hi = mysqli_fetch_assoc ($zoek_hisId)) { $hisId = $hi['hisId']; }

$reqst_file = 'InvSchaap.php_uitval';
$Melding = 'DOO';
/*##*/include "maak_request_func.php";
/*##*/include "maak_request.php";
}

}// Einde if(!isset($fout) && isset($scenario))			

// ***************************
// 	 EINDE GEGEVENS INLEZEN
// ***************************


// Index invoeren (bijwerken)
If (!empty($_POST['txtindex']) && isset($levnr)) // Er moet wel een levensnummer bestaan
{
$index_invoeren = "UPDATE tblSchaap SET indx = '".mysqli_real_escape_string($db,$index)."' WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."' " or die (mysqli_error($db));
	mysqli_query($db,$index_invoeren) or die (mysqli_error($db));
}

if(isset($levnr)) { $levnr = substr($levnr, 0, 6); }
// EINDE   DATABASE BIJWERKEN	
	}

} // Einde if (isset($_POST['knpSave']))
/***********************
 **** EINDE OPSLAAN	****
 ***********************/

 // ( 'vandaag ingevoerd' is ter controle van schapen zonder levensnummer)
$zoek_vandaag_ingevoerd_met_levnr = mysqli_query($db,"
SELECT count(s.schaapId) aant
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
WHERE s.levensnummer is not null and date_format(s.dmcreatie,'%Y-%m-%d') = CURRENT_DATE() and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die (mysqli_error($db));
	while ( $zvml = mysqli_fetch_assoc ($zoek_vandaag_ingevoerd_met_levnr)) { $met = $zvml['aant']; }

$zoek_vandaag_ingevoerd_zonder_levnr = mysqli_query($db,"
SELECT count(s.schaapId) aant
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
WHERE isnull(s.levensnummer) and date_format(s.dmcreatie,'%Y-%m-%d') = CURRENT_DATE() and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die (mysqli_error($db));
	while ( $zvzl = mysqli_fetch_assoc ($zoek_vandaag_ingevoerd_zonder_levnr)) { $zonder = $zvzl['aant']; }

if ($met > 0 || $zonder > 0)
{
?><table border = '0' style = "font-size : 10px" > <tr> <td><i> 
Vandaag ingevoerd :</td><td><?php echo $met; ?> met levensnummer</td></tr>
<tr><td></td><td><?php echo $zonder; ?> zonder levensnummer. </td></tr></table><?php
}

?>
<table border = 0> <tr> <td></td>

<td>	
<!-- ********************
		 OPMAAK LINKS 
     ******************** -->
<table border = 0>

<form action="InvSchaap.php" method="post"> 
<tr>
<td> Levensnummer : </td>
<td><input type="text" name="txtLevnr" autofocus id="levnr" onfocus="toon_dracht()" onchange="toon_dracht()" value = <?php if(isset($levnr)) { echo $levnr ; } ?> ></td>
</tr>
<!-- HALSNUMMER -->
<tr>
 <td>Halsnr : </td>
 <td>
 <select name= "kzlKleur" style= "width:62;" > 
<?php
$opties = array('' => '', 'blauw' => 'blauw', 'geel' => 'geel', 'groen' => 'groen', 'oranje' => 'oranje', 'paars' => 'paars', 'rood'=>'rood', 'wit' => 'wit', 'zwart' => 'zwart');
foreach ( $opties as $key => $waarde)
{
   if((!isset($_POST['knpSave']) && $kzlKleur == $key) || (isset($_POST["kzlKleur"]) && $_POST["kzlKleur"] == $key) ) {
	echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else {
	echo '<option value="' . $key . '">' . $waarde . '</option>';
  }
} ?>
</select>  
 <input type = text name = "txtHalsnr" style = "text-align : right" size = 1 value = <?php if(isset($txtHalsnr)) { echo $txtHalsnr; } ?> > </td>
</tr>
<!-- KZLGENERATIE -->
<tr>
<td>Generatie : </td>
<td>
<?php
$optie_fase = array('' => '', 'lam' => 'lam', 'moeder' => 'moeder', 'vader' => 'vader');
?>
	<select name= "kzlFase" id ="fase" onchange="toon_dracht()" style= "width:76;" > <?php
foreach ( $optie_fase as $key =>$waarde)	
{
	$keuze = '';
	if(isset($_POST['kzlFase']) && $_POST['kzlFase'] == $key)
	{
		$keuze = ' selected ';
	}
	echo '<option value="' . $key.'"' .$keuze .'>' . $waarde.'</option>';
}
?>	
	</select>
<sup> *</sup></td> </tr>

<!-- KZLGESLACHT -->
<tr>
<td> Geslacht :</td>
<td>

 <select name= "kzlSekse" id = "sekse" style= "width:59;" > 
<?php
$opties = array('' => '', 'ooi' => 'ooi', 'ram' => 'ram', 'kween' => 'kween');
foreach ( $opties as $key => $waarde)
{
   $keuze = '';
   if(isset($_POST['kzlSekse']) && $_POST['kzlSekse'] == $key)
   {
        $keuze = ' selected ';
   }
   echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
} ?>
 </select> 
<sup> *</sup></td>
</tr>

<!-- KZLRAS -->
<tr>
<td>Ras :</td>
<td>
<?php
$result = mysqli_query($db,"
SELECT r.rasId, r.ras 
FROM tblRas r
 join tblRasuser ru on (r.rasId = ru.rasId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and r.actief = 1 and ru.actief = 1
ORDER BY r.ras
") or die (mysqli_error($db)); ?>
 <select name= "kzlRas" id = "ras" style= "width:80;" >
 <option></option>
 <?php	while($row = mysqli_fetch_array($result))
		{
			$opties= array($row['rasId']=>$row['ras']);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if(isset($_POST['kzlRas']) && $_POST['kzlRas'] == $key)
		{
			$keuze = ' selected ';
		}
				
		echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
			}
		
		} ?>
 </select>

<sup> *</sup></td></tr>

<!-- KZLOOI -->
<tr> <td> </td>	
<td><i><sub> Werknr - lammeren - halsnr </sub></i>
</td> </tr>

<tr> <td> Werknr ooi (moeder) : </td>
<td>
<?php
$result = mysqli_query($db,"(".$vw_kzlOoien.")  ") or die (mysqli_error($db)); ?>

 <select name="kzlOoi" id ="moeder" style="width:100;" onfocus="kies_generatie()" onchange="toon_dracht()" >
 <option></option>	
<?php	while($row = mysqli_fetch_array($result))
		{
			$opties= array($row['schaapId']=>$row['werknr'].'&nbsp &nbsp '.$row['lamrn'].'&nbsp &nbsp '.$row['halsnr']);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if(isset($_POST['kzlOoi']) && $_POST['kzlOoi'] == $key)
		{
			$keuze = ' selected ';
		}
				
		echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
			}
		
		} ?>
 </select> 
 <?php if($modtech == 1) { ?> <sup> * / **</sup> <?php } ?>
 <input type = "hidden" name = "txtMaxmdr" size = 8 value = <?php if(isset($endmdr)) { echo $endmdr; } ?> >
 <!--<input type = "submit" name = "knpDracht" value = "Zoek vader" > (via dracht) -->
</td> <!-- hiddden --> </tr>
</td> </tr>

<!-- KZLRAM -->
<tr>
<td style = "font-size : 13px"><i>Werknr - index </i>
</td> </tr>

<tr> <td> Werknr ram (vader) : </td>

<td> <?php
$resultvader = mysqli_query($db,"
SELECT st.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk) werknr, s.indx
FROM tblStal st 
 join tblSchaap s on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
WHERE s.geslacht = 'ram' and h.actId = 3 and h.skip = 0 and lidId = '".mysqli_real_escape_string($db,$lidId)."'
and not exists (
	SELECT st.schaapId
	FROM tblStal stal 
	 join tblHistorie h on (h.stalId = stal.stalId)
	 join tblActie  a on (a.actId = h.actId)
	WHERE stal.schaapId = s.schaapId and a.af = 1 and h.datum < DATE_ADD(CURDATE(), interval -1 year) and h.skip = 0 and lidId = '".mysqli_real_escape_string($db,$lidId)."')
ORDER BY right(s.levensnummer,$Karwerk)
") or die (mysqli_error($db)); ?>
 <select name= "kzlRam" style= "width:100; text-align:left;" id="vader" onfocus="toon_dracht()" >
 <option></option>	
<?php	while($row = mysqli_fetch_array($resultvader))
		{
		
			$opties= array($row['schaapId']=>$row['werknr'].'&nbsp &nbsp '.$row['indx']);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if((isset($vaderId) && $vaderId == $key) || (isset($_POST['kzlRam']) && $_POST['kzlRam'] == $key))
		{
			$keuze = ' selected ';
		}
				
		echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
			}
		
		} ?>
 </select><p id="result_vader"></p> 
</td>  </tr>
</td> </tr>

<!--Geboortedatum -->
<tr> 
<td height = 40 valign = "bottom">Geboortedatum :</td>
<td valign = "bottom"><input id="datepicker1" name="txtGebdm" type="text" value = <?php if(isset($txtGebdm)) { echo $txtGebdm; } ?> ><sup id="bijschrift">  </sup> <p id="result_werpdatum"></p>  </td></tr>

<?php if($modtech == 1) { ?>
<tr> 
<td>Gewicht :</td>
<td><input type= "text" id = "gewicht" name= "txtGebkg"  value = <?php if(isset($txtGebkg)) { echo $txtGebkg; } ?> > <sup> *</sup> </td></tr>
<?php } ?>
<tr> 
<td>Aanvoerdatum :</td>
<td><input type= "text" id="datepicker2" name= "txtAanv" value = <?php if(isset($txtAanvdm)) { echo $txtAanvdm; } ?> ></td></tr>
<tr> 
<td>Index :</td>
<td><input type= "text"  name= "txtindex" value = <?php if(isset($index)) { echo $index; } ?> ></td></tr>




</table>

</td>
<td width = 160> </td>
<td valign = "top">

<!-- ********************* 
  		OPMAAK RECHTS 
      *********************-->
<table border = 0>
<th colspan = 2><i><sub> UITVAL</sub></i>
</th>
<!-- moment uitval -->
<tr>
<td>Moment uitval :</td>
<td>
<?php
$result = mysqli_query($db,"
SELECT m.momId, m.moment
FROM tblMoment m
 join tblMomentuser mu on (m.momId = mu.momId)
WHERE mu.lidId = '".mysqli_real_escape_string($db,$lidId)."' and m.actief = 1 and mu.actief = 1
ORDER BY m.momId
") or die (mysqli_error($db)); ?>
 <select name="kzlMoment" id="moment" style="width:180;" >
 <option></option>	
<?php	while($row = mysqli_fetch_array($result))
		{
			$opties= array($row['momId']=>$row['moment']);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if(isset($_POST['kzlMoment']) && $_POST['kzlMoment'] == $key)
		{
			$keuze = ' selected ';
		}
				
		echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
			}
		
		} ?>
 </select>
<sup> **</sup></td></tr>

<!-- datum uitval -->
<tr>
<td>Datum uitval : </td>
<td><input id="datepicker3" name="txtUitvdm" type="text" height= 50 value = <?php if(isset($txtUitvdm)) { echo $txtUitvdm; } ?> ><sup> **</sup></td>
</tr>


<!-- KZLREDEN -->
<tr>
<td> Reden uitval :</td>
<td> <?php
$result = mysqli_query($db, "
SELECT r.reden, ru.redId
FROM tblReden r
 join tblRedenuser ru on (r.redId = ru.redId)
WHERE r.actief = 1 and ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ru.uitval = 1
ORDER BY r.reden
") or die (mysqli_error($db)); ?>
 <select name= "kzlReden" id= "reden" style= "width:145;" >
 <option></option>
<?php	while($row = mysqli_fetch_array($result))					
		{
		
			$opties= array($row['redId']=>$row['reden']);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if(isset($_POST['kzlReden']) && $_POST['kzlReden'] == $key)
		{
			$keuze = ' selected ';
		}
				
		echo '<option value="' . $key . '" >' . $waarde . '</option>';
		//echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';  Gekozen waarde onthouden
			}
		
		} ?>
 </select>
 </td></tr>


</td></tr>
<tr> <td height = 50> </td> </tr>

<?php if($modtech == 1) { ?>
<!-- KZLHOK -->
<tr>
<td>Verblijf :</td>
<td>
<?php
$result = mysqli_query($db," SELECT hokId, hoknr FROM tblHok WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actief = 1 ORDER BY hoknr ") or die (mysqli_error($db)); ?>
 <select name="kzlHok" id="verblijf" style="width:100;" >
 <option></option>	
<?PHP	while($row = mysqli_fetch_array($result))
		{
		
			$opties= array($row['hokId']=>$row['hoknr']/*.'&nbsp &nbsp &nbsp &nbsp '.$row['doel'].'&nbsp &nbsp &nbsp &nbsp &nbsp '.$row['nu']*/);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if(isset($_POST['kzlHok']) && $_POST['kzlHok'] == $key)
		{
			$keuze = ' selected ';
		}
				
		echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
			}
		
		} ?>
 </select> <sup> *</sup>
</td> </tr>
<?php } ?>

<tr height = 50><td></td></tr>

<tr >
<td colspan = 2>
 * Verplichte velden bij geboren lammeren.<br/>
 ** Verplicht bij overlijden.
</td> 
</tr>

</table>

<tr> 
<td colspan = 4 align = center><input type = "submit" name = "knpSave" onfocus = "verplicht()" value = "opslaan" ></td>
</tr> 
</form>

</table>


		</TD>	
	
<?php	
Include "menu1.php"; } # deze haak hoor bij Include "login.php";  ?>
</tr>

</table>
</center>

</body>
</html>