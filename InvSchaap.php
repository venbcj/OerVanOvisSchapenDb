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
 session_start();  ?>
<html>
<head>
<title>Registratie</title>
<style type= "text/css">
<?php
//if (isset ($_POST['knpSave'])) { header("Location: http://localhost:8080/schapendb/InvSchaap.php"); }
echo "body {background-image:url('schaap_Backgr.jpg');}";
?>
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

// array tbv javascript
$zoek_vader_dracht = mysqli_query($db,"
SELECT v.mdrId, right(s.levensnummer,$Karwerk) lev
FROM tblVolwas v
 join tblSchaap s on (s.schaapId = v.vdrId)
WHERE date_add(v.datum,interval 183 day) > CURRENT_DATE() and v.mdrId is not null and v.vdrId is not null
") or die (mysqli_error($db));

while ( $va = mysqli_fetch_assoc($zoek_vader_dracht)) { $array_dracht[$va['mdrId']] = $va['lev']; }
// Einde array tbv javascript

?>

<script>
function verplicht() {
var levnr = document.getElementById("levnr"); 		var levnr_v = levnr.value;
var fase  = document.getElementById("fase");		var fase_v = fase.value;
var sekse = document.getElementById("sekse");		var sekse_v = sekse.value;
var ras   = document.getElementById("ras");			var ras_v = ras.value;
var gebdm = document.getElementById("datepicker1");	var gebdm_v = gebdm.value;
var gewicht = document.getElementById("gewicht");	var gewicht_v = gewicht.value;
var verblijf = document.getElementById("verblijf");	var verblijf_v = verblijf.value;
var moment = document.getElementById("moment");		var moment_v = moment.value;
var uitvdm = document.getElementById("datepicker3"); var uitvdm_v = uitvdm.value;
var reden = document.getElementById("reden"); 		var reden_v = reden.value;
var aanvdm = document.getElementById("datepicker2"); var aanvdm_v = aanvdm.value;


	 if(levnr_v.length > 0 && levnr_v.length != 12) levnr.focus() 	+ alert("Het levensnummer moet uit 12 cijfers bestaan.");
else if(isNaN(levnr_v)) levnr.focus() 	+ alert("Het levensnummer bevat een letter.");
else if((fase_v == 'moeder' && sekse_v == 'ram') || (fase_v == 'vader' && sekse_v == 'ooi')) fase.focus() 	+ alert("Geslacht en generatie zijn tegenstrijdig !");
else if(gebdm_v.length == 0 && fase_v == 'lam') gebdm.focus() 	+ alert("De geboortedatum moet zijn ingevuld.");
else if(verblijf_v.length > 0 && (moment_v.length > 0 || uitvdm_v.length > 0 || reden_v.length > 0))  verblijf.focus() 	+ alert("U kunt geen dood schaap in een verblijf plaatsen !");
else if(fase_v == 'lam' && aanvdm_v.length > 0)  aanvdm.focus()  + alert("Alleen volwassen dieren kunnen worden aangekocht.");
else if(fase_v != 'lam' && gewicht_v.length > 0)  gewicht.focus()  + alert("Bij invoer van een volwassen dier mag geen gewicht worden ingevoerd.");

}


function vader_dracht() {

var moeder = document.getElementById("moeder");		var moeder_v = moeder.value;


 if(moeder_v.length > 0) toon_dracht(moeder_v);

}

 var jArray= <?php echo json_encode($array_dracht); ?>;

function toon_dracht(m) {
	//document.getElementById('result').innerHTML = jArray[m];

  if(jArray[m] == null){
	//document.getElementById('vader').style.display = "block";
	document.getElementById('vader').style.display= "inline-block";
	document.getElementById('result').innerHTML = "";
	
	 }
  else {
  	
  	document.getElementById('vader').style.display = "none";
  	document.getElementById('result').innerHTML = jArray[m];
  	 }
}

</script>
<?php
// Als pagina wordt geladen na submit moet bij bestaan van dracht kzlRam hidden zijn.
/*if (isset ($_POST['kzlOoi']) && !empty($_POST['kzlOoi']))
{
	$moe = $_POST['kzlOoi'];
$zoek_vader_binnen_dracht = mysqli_query($db,"
SELECT right(s.levensnummer,$Karwerk) vader
FROM tblVolwas v
 join tblSchaap s on (v.vdrId = s.schaapId)
WHERE date_add(v.datum,interval 183 day) > CURRENT_DATE() and v.mdrId = ".mysqli_real_escape_string($db,$moe)." and v.vdrId is not null
") or die (mysqli_error($db));

while ( $va = mysqli_fetch_assoc($zoek_vader_binnen_dracht)) { $drachtvader = $va['vader']; }

}*/
// Einde Als pagina wordt geladen na submit moet bij bestaan van dracht kzlRam hidden zijn.




include "vw_kzlOoien.php";

function numeriek($subject) {
	if (preg_match('/([[a-zA-Z])/', $subject, $matches)) {  /*var_dump($matches[1]); */ return 1; }
}

// Query : Zoek naar geboortedatum lam van dracht moeder. Per dracht nl. 1 geboortedatum van alle lammeren
function worpdatum($datb,$OOI) {
	$zoek_gebdatum_lam_van_dracht = mysqli_query($datb,"
SELECT s.datum dmgeb, date_format(s.datum,'%d-%m-%Y') gebdm
FROM tblVolwas v
 join (
 	SELECT s.volwId, min(h.datum) datum
 	FROM tblSchaap s
 	 join tblStal st on (s.schaapId = st.schaapId)
 	 join tblHistorie h on (st.stalId = h.stalId)
 	WHERE h.actId = 1
 	GROUP BY s.volwId
 ) s on (s.volwId = v.volwId)
WHERE date_add(v.datum,interval 183 day) > CURRENT_DATE() and mdrId = ".mysqli_real_escape_string($datb,$OOI)."
");

	if($zoek_gebdatum_lam_van_dracht)
		{	$rij = mysqli_fetch_assoc($zoek_gebdatum_lam_van_dracht);
				return array($rij['dmgeb'], $rij['gebdm']);

		}
		return FALSE;
}
// Einde Query : Zoek naar geboortedatum lam van dracht moeder. Per dracht nl. 1 geboortedatum van alle lammeren

 // ( 'vandaag ingevoerd' is ter controle van schapen zonder levensnummer)
$metlevnr = mysqli_query($db,"
select count(st.schaapId) aantal 
from tblStal st 
 join tblLeden l on (st.lidId = l.lidId)
 join tblSchaap s on (s.schaapId = st.schaapId)
where l.ubn = ".mysqli_real_escape_string($db,$ubn)." and s.levensnummer is not null and date_format(now(),'%d-%m-%Y') = date_format(st.dmcreatie,'%d-%m-%Y')
") or die (mysqli_error($db));
	
	while ($metnr = mysqli_fetch_assoc($metlevnr))
		{ $met = $metnr['aantal'];}

$zonderlevnr = mysqli_query($db,"
select count(st.schaapId) aantal 
from tblStal st 
 join tblLeden l on (st.lidId = l.lidId)
 join tblSchaap s on (s.schaapId = st.schaapId)
where l.ubn = ".mysqli_real_escape_string($db,$ubn)." and isnull(s.levensnummer) and date_format(now(),'%d-%m-%Y') = date_format(st.dmcreatie,'%d-%m-%Y')
") or die (mysqli_error($db));
	
	while ($zondernr = mysqli_fetch_assoc($zonderlevnr))
		{$zonder = $zondernr['aantal'];}



if (isset($_POST['txtlevnr']) && !empty($_POST['txtlevnr'])) { $levnr = $_POST['txtlevnr'];  }
if (isset($_POST['txtindex'])) { $index = $_POST['txtindex'];  }

if(isset($levnr)) {
$zoek_in_stallijst = mysqli_query($db, "
select s.schaapId 
from tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and levensnummer = '".mysqli_real_escape_string($db,$levnr)."' and isnull(st.rel_best)
") or die (mysqli_error($db));
	while($stl = mysqli_fetch_assoc($zoek_in_stallijst)) {	$aanwezig = $stl['schaapId']; }	
	
$zoek_dood = mysqli_query($db, "
select s.schaapId 
from tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
where levensnummer = '".mysqli_real_escape_string($db,$levnr)."' and h.actId = 14
") or die (mysqli_error($db));
	while($do = mysqli_fetch_assoc($zoek_dood)) {	$dood = $do['schaapId']; }
}

if(isset($_POST['txtgebdm']) && !empty($_POST['txtgebdm'])) { // Dus altijd bij lammeren en optioneel bij volwassen dieren
$date = date_create($_POST['txtgebdm']);
		$gebdag =  date_format($date, 'Y-m-d'); }


if (isset ($_POST['knpSave']))
{
	if(isset($levnr)) { // Zoek naar een bestaand levensnummer. Bijvoorbeeld die een andere gebruiker al eens heeft ingevoerd of opnieuw aanvoer.
$query_bestaand_levensnummer = "
select s.schaapId, s.geslacht, s.volwId, v.mdrId, hg.datum dmgeb, h1.datum dmeerste, date_format(h1.datum,'%d-%m-%Y') eerstedm, ha.datum dmaanw, haf.datum dmafv, date_format(haf.datum,'%d-%m-%Y') afvdm
from tblSchaap s
 left join tblVolwas v on (s.volwId = v.volwId)
 left join (
	select s.schaapId, h.datum
	from tblSchaap s
	 join tblStal st on (s.schaapId = st.schaapId)
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 1 and s.levensnummer = '".mysqli_real_escape_string($db,$levnr)."'
 ) hg on (s.schaapId = hg.schaapId)
 left join (
	select his1.schaapId, h.datum
	from tblHistorie h
	join (
		select st.schaapId, min(h.hisId) hisId
		from tblSchaap s
		 join tblStal st on (s.schaapId = st.schaapId)
		 join tblHistorie h on (st.stalId = h.stalId)
		where s.levensnummer = '".mysqli_real_escape_string($db,$levnr)."'
		group by st.schaapId
	) his1 on (his1.hisId = h.hisId)
 ) h1 on (s.schaapId = h1.schaapId)
 left join (
	select s.schaapId, h.datum
	from tblSchaap s
	 join tblStal st on (s.schaapId = st.schaapId)
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 3 and s.levensnummer = '".mysqli_real_escape_string($db,$levnr)."'
 ) ha on (s.schaapId = ha.schaapId)
 left join (
	select afv.schaapId, h.datum
	from tblHistorie h
	join (
		select st.schaapId, max(h.hisId) hisId
		from tblSchaap s
		 join tblStal st on (s.schaapId = st.schaapId)
		 join tblHistorie h on (st.stalId = h.stalId)
		 join tblActie a on (h.actId = a.actId)
		where a.af = 1 and s.levensnummer = '".mysqli_real_escape_string($db,$levnr)."'
		group by st.schaapId
	) afv on (afv.hisId = h.hisId)
 ) haf on (s.schaapId = haf.schaapId)
where levensnummer = '".mysqli_real_escape_string($db,$levnr)."'

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

if(!empty($_POST['kzlOoi'])) { $moeder = $_POST['kzlOoi']; } else if(isset($mdrId_db)) { $moeder = $mdrId_db; }
if(isset($moeder)) {
$query_startdm_moeder = mysqli_query($db,"
select h.datum
from (
	select stalId
	from tblStal
	where lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(rel_best) and schaapId = ".mysqli_real_escape_string($db,$moeder)."
 ) minst
 join tblHistorie h on (minst.stalId = h.stalId)
 join tblActie a on (h.actId = a.actId)
where a.op = 1 and h.skip = 0
") or die (mysqli_error($db)); 
		while($mdrdm = mysqli_fetch_array($query_startdm_moeder))
		{ $startmdr = $mdrdm['datum']; }

$zoek_eindm_mdr_indien_afgevoerd = mysqli_query($db,"
select h.datum
from (
	select max(stalId) stalId, schaapId
	from tblStal
	where lidId = ".mysqli_real_escape_string($db,$lidId)." and schaapId = ".mysqli_real_escape_string($db,$moeder)."
	group by schaapId
 ) maxst
 join tblStal st on (st.stalId = maxst.stalId)
 join tblHistorie h on (h.stalId = st.stalId)
where rel_best is not null
") or die (mysqli_error($db)); 
		while($mdrdm = mysqli_fetch_array($zoek_eindm_mdr_indien_afgevoerd))
		{ $endmdr = $mdrdm['datum']; }

$zoek_drachtdatum = mysqli_query($db,"
SELECT datum dmdracht, date_format(datum,'%d-%m-%Y') drachtdm
FROM tblVolwas v
WHERE date_add(v.datum,interval 183 day) > CURRENT_DATE() and mdrId = ".mysqli_real_escape_string($db,$moeder)."
") or die (mysqli_error($db));

while ($dr = mysqli_fetch_assoc($zoek_drachtdatum)) { $dmdracht = $dr['dmdracht']; $drachtdm = $dr['drachtdm']; }

// Zoek naar geboortedatum lam van dracht moeder. Per dracht nl. 1 geboortedatum van alle lammeren
$dmgeb_dr = worpdatum($db,$moeder)[0];
$gebdm_dr = worpdatum($db,$moeder)[1];
// Einde Zoek naar geboortedatum lam van dracht moeder. Per dracht nl. 1 geboortedatum van alle lammeren
}
	
$sekse = $_POST['kzlsekse'];

$dateuitv = date_create($_POST['txtuitvdm']);
		$dmuitv =  date_format($dateuitv, 'Y-m-d');
$dateaanw = date_create($_POST['txtAanv']);
		$dmaanw =  date_format($dateaanw, 'Y-m-d');

// Controle moederdier bij reeds geregistreerd levensnummer
		if(isset($levnr_db) && isset($mdrId_db) && !empty($_POST['kzlOoi'])) { $fout = "Dit dier heeft al een moeder. Ooi (en ram) wordt niet opgeslagen. "; }
// Einde Controle moederdier bij reeds geregistreerd levensnummer

// CONTROLE OP JUISTE INVOER
	/*If ( isset($levnr) && strlen("$levnr")<> 12 )
	{
		$fout = "Het levensnummer moet uit 12 cijfers bestaan.";#"<center style = \"color : red;\">"."Het levensnummer moet uit 12 cijfers bestaan."."</center>";
			if ($_POST['txtgebdm'] <> "0")			{   $gebdatum = $_POST['txtgebdm'];   }
			if ($modtech == 1 && $_POST['txtgebkg'] <> "0")			{   $gebkg = str_replace(',', '.', $_POST['txtgebkg']); 	 }
			if (!empty($_POST['txtAanv']) )		{   $aanwas = $_POST['txtAanv']; 	 }
			if (!empty($_POST['txtHnr']))			{ $hnr = $_POST['txtHnr']; }
	}
	else if ( isset($levnr) && numeriek($levnr) == 1 )
	{
		$fout = "Het levensnummer bevat een letter.";
			if ($_POST['txtgebdm'] <> "0")			{   $gebdatum = $_POST['txtgebdm'];   }
			if ($modtech == 1 && $_POST['txtgebkg'] <> "0")			{   $gebkg = str_replace(',', '.', $_POST['txtgebkg']); 	 }
			if (!empty($_POST['txtAanv']) )		{   $aanwas = $_POST['txtAanv']; 	 }
			if (!empty($_POST['txtHnr']))			{ $hnr = $_POST['txtHnr']; }
	}
	else if (isset($levnr) && empty($_POST['kzlfase']) && !isset($levnr_db) && empty($_POST['kzlMoment']) && empty($_POST['txtuitvdm']) && empty($_POST['kzlReden']) )
	{
		$fout = "De generatie moet zijn ingevuld.";
			if ($_POST['txtgebdm'] <> "0")			{   $gebdatum = $_POST['txtgebdm'];   }
			if ($modtech == 1 && $_POST['txtgebkg'] <> "0")			{   $gebkg = str_replace(',', '.', $_POST['txtgebkg']); 	 }
			if (!empty($_POST['txtAanv']) )		{   $aanwas = $_POST['txtAanv']; 	 }
			if (!empty($_POST['txtHnr']))			{ $hnr = $_POST['txtHnr']; }
	}
	
	else*/ if (isset($levnr) && empty($_POST['kzlsekse']) && !isset($levnr_db) && empty($_POST['kzlMoment']) && empty($_POST['txtuitvdm']) && empty($_POST['kzlReden']) )
	{
		$fout = "Het geslacht moet zijn ingevuld.";
			if ($_POST['txtgebdm'] <> "0")			{   $gebdatum = $_POST['txtgebdm'];   }
			if ($modtech == 1 && $_POST['txtgebkg'] <> "0")			{   $gebkg = str_replace(',', '.', $_POST['txtgebkg']); 	 }
			if (!empty($_POST['txtAanv']) )		{   $aanwas = $_POST['txtAanv']; 	 }
			if (!empty($_POST['txtHnr']))			{ $hnr = $_POST['txtHnr']; }
	}

	/*else if( ($_POST['kzlfase'] == 'moeder' && $_POST['kzlsekse'] =='ram') || ($_POST['kzlfase'] == 'vader' && $_POST['kzlsekse'] =='ooi')  )
	{
		$fout = "Geslacht en generatie zijn tegenstrijdig !";
			if ($_POST['txtgebdm'] <> "0")			{   $gebdatum = $_POST['txtgebdm'];	 }
			if ($modtech == 1 && $_POST['txtgebkg'] <> "0")			{   str_replace(',', '.', $gebkg = $_POST['txtgebkg']); 	 }
			if (!empty($_POST['txtAanv']) )		{   $aanwas = $_POST['txtAanv']; 	 }
			if (!empty($_POST['txtHnr']))			{ $hnr = $_POST['txtHnr']; }
	}*/
	else if (isset($levnr) && empty($_POST['kzlras']) && !isset($levnr_db) && empty($_POST['kzlMoment']) && empty($_POST['txtuitvdm']) && empty($_POST['kzlReden']) )
	{
		$fout = "Het ras moet zijn ingevuld.";
			if ($_POST['txtgebdm'] <> "0")			{   $gebdatum = $_POST['txtgebdm'];   }
			if ($modtech == 1 && $_POST['txtgebkg'] <> "0")			{   $gebkg = str_replace(',', '.', $_POST['txtgebkg']); 	 }
			if (!empty($_POST['txtAanv']) 	)		{   $aanwas = $_POST['txtAanv']; 	 }
			if (!empty($_POST['txtHnr']))			{ $hnr = $_POST['txtHnr']; }
	}	
	else if ($modtech == 1 && empty($_POST['kzlOoi']) && $_POST['kzlfase'] == 'lam' ) 
	{
		$fout = "Het moederdier moet zijn ingevuld.";
			if ($_POST['txtgebdm'] <> "0") 			{   $gebdatum = $_POST['txtgebdm'];   }
			if ($modtech == 1 && $_POST['txtgebkg'] <> "0")			{   $gebkg = str_replace(',', '.', $_POST['txtgebkg']); 	 }
			if (!empty($_POST['txtAanv']) )		{   $aanwas = $_POST['txtAanv']; 	 }
	}
	/*else if (empty($_POST['txtgebdm']) && $_POST['kzlfase'] == 'lam' )
	{
		$fout = "De geboortedatum moet zijn ingevuld.";
			if ($_POST['txtgebdm'] <> "0")			{   $gebdatum = $_POST['txtgebdm'];   }
			if ($modtech == 1 && $_POST['txtgebkg'] <> "0")			{   $gebkg = str_replace(',', '.', $_POST['txtgebkg']); 	 }
			if (!empty($_POST['txtAanv']) )		{   $aanwas = $_POST['txtAanv']; 	 }
	}*/
	else if ($modtech == 1 && isset($levnr) && empty($_POST['txtgebkg']) && $_POST['kzlfase'] == 'lam' && (empty($_POST['kzlMoment']) && (empty($_POST['txtuitvdm']) && empty($_POST['kzlReden']))) )
	{
		$fout = "Het gewicht moet zijn ingevuld.";
			if ($_POST['txtgebdm'] <> "0")			{   $gebdatum = $_POST['txtgebdm'];   }
			if ($modtech == 1 && $_POST['txtgebkg'] <> "0")			{   $gebkg = str_replace(',', '.', $_POST['txtgebkg']); 	 }
			if (!empty($_POST['txtAanv']) )		{   $aanwas = $_POST['txtAanv']; 	 }
	}
	else if ( $_POST['kzlfase'] == 'lam' && !empty($_POST['txtindex']) )
	{
		$fout = "De index kan alleen bij een volwassen dier worden ingevoerd.";
			if ($_POST['txtgebdm'] <> "0")			{   $gebdatum = $_POST['txtgebdm'];	 }
			if ($modtech == 1 && $_POST['txtgebkg'] <> "0")			{   $gebkg = str_replace(',', '.', $_POST['txtgebkg']); 	 }
			if (!empty($_POST['txtAanv']) )		{   $aanwas = $_POST['txtAanv']; 	 }
			if (!empty($_POST['txtHnr']))			{ $hnr = $_POST['txtHnr']; }
	}	
	else if ( (!empty($_POST['kzlMoment']) && (empty($_POST['txtuitvdm']) ))
	  || (!empty($_POST['kzlReden']) && (empty($_POST['txtuitvdm']) ))
	  || (!isset($levnr) && (empty($_POST['txtuitvdm']) || ($modtech == 1 && empty($_POST['kzlOoi'])) )) )
	{
		$fout = "Bij overlijden moet datum t.b.v. uitval zijn ingevuld.";
			if ($_POST['txtgebdm'] <> "0")			{   $gebdatum = $_POST['txtgebdm'];   }
			if ($modtech == 1 && $_POST['txtgebkg'] <> "0")			{   $gebkg = str_replace(',', '.', $_POST['txtgebkg']); 	 }
			if (!empty($_POST['txtAanv']) )		{   $aanwas = $_POST['txtAanv']; 	 }
			if ($_POST['txtuitvdm'] <> "0")			{   $uvaldm = $_POST['txtuitvdm']; 	 }
	}		
	else if ( !empty($_POST['txtuitvdm']) && !empty($_POST['txtgebdm']) && $dmuitv < $gebdag )
	{
		$fout = "Datum overlijden kan niet voor geboortedatum liggen !";
			if ($_POST['txtgebdm'] <> "0")			{   $gebdatum = $_POST['txtgebdm'];	 }
			if ($modtech == 1 && $_POST['txtgebkg'] <> "0")			{   $gebkg = str_replace(',', '.', $_POST['txtgebkg']); 	 }
			if (!empty($_POST['txtAanv']) )		{   $aanwas = $_POST['txtAanv']; 	 }
			if ($_POST['txtuitvdm'] <> "0")			{   $uvaldm = $_POST['txtuitvdm']; 	 }
	}
	else if ( !empty($_POST['txtuitvdm']) && !empty($_POST['txtAanv']) && $dmuitv < $dmaanw )
	{
		$fout = "Datum overlijden kan niet voor aanschafdatum liggen !";
			if ($_POST['txtgebdm'] <> "0")			{   $gebdatum = $_POST['txtgebdm'];	 }
			if ($modtech == 1 && $_POST['txtgebkg'] <> "0")			{   $gebkg = str_replace(',', '.', $_POST['txtgebkg']); 	 }
			if (!empty($_POST['txtAanv']) )		{   $aanwas = $_POST['txtAanv']; 	 }
			if ($_POST['txtuitvdm'] <> "0")			{   $uvaldm = $_POST['txtuitvdm']; 	 }
	}
	else if ( !empty($_POST['txtgebdm']) && !empty($_POST['txtAanv']) && $dmaanw < $gebdag )
	{
		$fout = "Datum aanschaf kan niet voor geboortedatum liggen !";
			if ($_POST['txtgebdm'] <> "0")			{   $gebdatum = $_POST['txtgebdm'];	 }
			if ($modtech == 1 && $_POST['txtgebkg'] <> "0")			{   $gebkg = str_replace(',', '.', $_POST['txtgebkg']); 	 }
			if (!empty($_POST['txtAanv']) )		{   $aanwas = $_POST['txtAanv']; 	 }
			if ($_POST['txtuitvdm'] <> "0")			{   $uvaldm = $_POST['txtuitvdm']; 	 }
			if (!empty($_POST['txtHnr']))			{ $hnr = $_POST['txtHnr']; }
	}
	
	/*else if(!empty($_POST['kzlhok']) && (!empty($_POST['kzlMoment']) || (!empty($_POST['txtuitvdm']) || !empty($_POST['kzlReden'])) )    )
	{
		$fout = "U kunt geen dood schaap in een verblijf plaatsen !";
			if ($_POST['txtgebdm'] <> "0")			{   $gebdatum = $_POST['txtgebdm'];	 }
			if ($_POST['txtgebkg'] <> "0")			{   $gebkg = str_replace(',', '.', $_POST['txtgebkg']); 	 }
			if (!empty($_POST['txtAanv']) )		{   $aanwas = $_POST['txtAanv']; 	 }
			if ($_POST['txtuitvdm'] <> "0")			{   $uvaldm = $_POST['txtuitvdm']; 	 }
	}*/
	/*else if ( $_POST['kzlfase'] != 'lam' && !empty($_POST['kzlhok']) )
	{
		$fout = "Een volwassen dier kan niet in een verblijf worden geplaatst.";
			if ($_POST['txtgebdm'] <> "0")			{   $gebdatum = $_POST['txtgebdm'];	 }
			if ($_POST['txtgebkg'] <> "0")			{   $gebkg = $_POST['txtgebkg']; 	 }
			if (!empty($_POST['txtAanv']) )		{   $aanwas = $_POST['txtAanv']; 	 }
	}*/	
	
	else if ($modtech == 1 && empty($_POST['kzlhok']) && $_POST['kzlfase'] == 'lam' && empty($_POST['kzlMoment']) && empty($_POST['txtuitvdm']) && empty($_POST['kzlReden']) )
	{
		$fout = "Plaats het lam ook nog in een verblijf.";
			if ($_POST['txtgebdm'] <> "0")			{   $gebdatum = $_POST['txtgebdm'];	 }
			if ($_POST['txtgebkg'] <> "0")			{   $gebkg = str_replace(',', '.', $_POST['txtgebkg']); 	 }
			if (!empty($_POST['txtAanv']) )		{   $aanwas = $_POST['txtAanv']; 	 }
	}
	
	else if ( !empty($aanwezig) && !empty($_POST['txtlevnr']) )
	{
		$fout = "Dit dier staat al op de stallijst.";
			if ($_POST['txtgebdm'] <> "0")			{   $gebdatum = $_POST['txtgebdm'];	 }
			if ($modtech == 1 && $_POST['txtgebkg'] <> "0")			{   $gebkg = str_replace(',', '.', $_POST['txtgebkg']); 	 }
			if (!empty($_POST['txtAanv']) )		{   $aanwas = $_POST['txtAanv']; 	 }
			if ($_POST['txtuitvdm'] <> "0")			{   $uvaldm = $_POST['txtuitvdm']; 	 }
			if (!empty($_POST['txtHnr']))			{ $hnr = $_POST['txtHnr']; }
	}
	
	else if ( empty($_POST['txtAanv']) && ($_POST['kzlfase'] == 'moeder'  || $_POST['kzlfase'] == 'vader' || (isset($levnr_db) && isset($aanwas_db))) )
	{
		$fout = "Bij invoer van een volwassen dier is de aanschafdatum verplicht.";
			if ($_POST['txtgebdm'] <> "0")					{ $gebdatum = $_POST['txtgebdm']; }
			if ($modtech == 1 && $_POST['txtgebkg'] <> "0")	{ $gebkg = str_replace(',', '.', $_POST['txtgebkg']); }
			if (!empty($_POST['txtHnr']))					{ $hnr = $_POST['txtHnr']; }
	}
	
	/*	else if ( $_POST['kzlfase'] == 'lam' && !empty($_POST['txtAanv']) )
	{
		$fout = "Alleen volwassen dieren kunnen worden aangekocht.";
			if ($_POST['txtgebdm'] <> "0")			{   $gebdatum = $_POST['txtgebdm'];	 }
			if ($modtech == 1 && $_POST['txtgebkg'] <> "0")			{   $gebkg = str_replace(',', '.', $_POST['txtgebkg']); 	 }
	}
	
	else if ( $_POST['kzlfase'] != 'lam' && !empty($_POST['txtgebkg']) )
	{
		$fout = "Bij invoer van een volwassen dier mag geen gewicht worden ingevoerd.";
			if ($_POST['txtgebdm'] <> "0")			{   $gebdatum = $_POST['txtgebdm'];	 }
			if (!empty($_POST['txtAanv']) )		{   $aanwas = $_POST['txtAanv']; 	 }
			if (!empty($_POST['txtHnr']))			{   $hnr = $_POST['txtHnr']; 	 }
	}*/
	else if ($_POST['kzlfase'] == 'lam' && $gebdag < $startmdr) 
	{
		$fout = "Geboortedatum kan niet voor aanvoerdatum van moederdier liggen.";
			if ($_POST['txtgebdm'] <> "0")			{   $gebdatum = $_POST['txtgebdm'];	 }
			if ($modtech == 1 && $_POST['txtgebkg'] <> "0")			{   $gebkg = str_replace(',', '.', $_POST['txtgebkg']); 	 }
			if (!empty($_POST['txtAanv']) )		{   $aanwas = $_POST['txtAanv']; 	 }
			if ($_POST['txtuitvdm'] <> "0")			{   $uvaldm = $_POST['txtuitvdm']; 	 }
			if (!empty($_POST['txtHnr']))			{   $hnr = $_POST['txtHnr']; 	 }
	}
	else if ($_POST['kzlfase'] == 'lam' && isset($endmdr) && $endmdr < $gebdag) 
	{
		$fout = "Geboortedatum kan niet na afvoerdatum van moederdier liggen.";
			if ($_POST['txtgebdm'] <> "0")			{   $gebdatum = $_POST['txtgebdm'];	 }
			if ($modtech == 1 && $_POST['txtgebkg'] <> "0")			{   $gebkg = str_replace(',', '.', $_POST['txtgebkg']); 	 }
			if (!empty($_POST['txtAanv']) )		{   $aanwas = $_POST['txtAanv']; 	 }
			if ($_POST['txtuitvdm'] <> "0")			{   $uvaldm = $_POST['txtuitvdm']; 	 }
			if (!empty($_POST['txtHnr']))			{   $hnr = $_POST['txtHnr']; 	 }
	}
	else if (!isset($dmgeb_db) && !empty($_POST['txtgebdm']) && isset($levnr_db) && $dmeerste_db < $gebdag) 
	{
		$fout = "Geboortedatum kan niet na ".$eerstedm_db." liggen.";
			if ($_POST['txtgebdm'] <> "0")			{   $gebdatum = $_POST['txtgebdm'];	 }
			if ($modtech == 1 && $_POST['txtgebkg'] <> "0")			{   $gebkg = str_replace(',', '.', $_POST['txtgebkg']); 	 }
			if (!empty($_POST['txtAanv']) )		{   $aanwas = $_POST['txtAanv']; 	 }
			if ($_POST['txtuitvdm'] <> "0")			{   $uvaldm = $_POST['txtuitvdm']; 	 }
			if (!empty($_POST['txtHnr']))			{   $hnr = $_POST['txtHnr']; 	 }
	}
	else if (($_POST['kzlfase'] == 'moeder' || $_POST['kzlfase'] == 'vader' || (isset($levnr_db) && isset($aanwas_db)) ) && !empty($_POST['txtAanv']) && isset($dmafvoer_db) && $dmaanw < $dmafvoer_db) 
	{
		$fout = "Aanvoerdatum kan niet voor ".$laatste_afvoerdm." liggen.";
			if ($_POST['txtgebdm'] <> "0")			{   $gebdatum = $_POST['txtgebdm'];	 }
			if ($modtech == 1 && $_POST['txtgebkg'] <> "0")			{   $gebkg = str_replace(',', '.', $_POST['txtgebkg']); 	 }
			if (!empty($_POST['txtAanv']) )		{   $aanwas = $_POST['txtAanv']; 	 }
			if ($_POST['txtuitvdm'] <> "0")			{   $uvaldm = $_POST['txtuitvdm']; 	 }
			if (!empty($_POST['txtHnr']))			{   $hnr = $_POST['txtHnr']; 	 }
	}
	else if (isset($dood)) // Bestaand levensnummer dat reeds is overleden. T.b.v. aankoop volwassen dieren.
	{
		$fout = "Dit is een overleden schaap.";
			if ($_POST['txtgebdm'] <> "0")			{   $gebdatum = $_POST['txtgebdm'];	 }
			if ($modtech == 1 && $_POST['txtgebkg'] <> "0")			{   $gebkg = str_replace(',', '.', $_POST['txtgebkg']); 	 }
			if (!empty($_POST['txtAanv']) )		{   $aanwas = $_POST['txtAanv']; 	 }
			if ($_POST['txtuitvdm'] <> "0")			{   $uvaldm = $_POST['txtuitvdm']; 	 }
	}
	else if (!empty($_POST['txtuitvdm']) && isset($levnr_db)) // Dood dier met levensnummer dat al voorkomt in tblSchaap. Controle t.b.v. doodgeboren lam
	{
		$fout = "Dit levensnummer bestaat al.";
			if ($_POST['txtgebdm'] <> "0")			{   $gebdatum = $_POST['txtgebdm'];	 }
			if ($modtech == 1 && $_POST['txtgebkg'] <> "0")			{   $gebkg = str_replace(',', '.', $_POST['txtgebkg']); 	 }
			if (!empty($_POST['txtAanv']) )		{   $aanwas = $_POST['txtAanv']; 	 }
			if ($_POST['txtuitvdm'] <> "0")			{   $uvaldm = $_POST['txtuitvdm']; 	 }
			if (!empty($_POST['txtHnr']))			{   $hnr = $_POST['txtHnr']; 	 }
	}
	else if ($modtech == 1 && isset($gebdag) && isset($dmgeb_dr) && $gebdag <> $dmgeb_dr) // Het moederdier kan van deze dracht al een lam hebben. Die geboortedatum moet gelijk liggen aan de geboortedatum van dit schaap. Mits deze een geboortedatum heeft.
	{
		$fout = "Deze moeder heeft reeds geworpen op ".$gebdm_dr.". Dat moet dus de geboortedatum zijn van dit schaap.";
			$gebdatum = $gebdm_dr;
			if ($modtech == 1 && $_POST['txtgebkg'] <> "0")			{   $gebkg = str_replace(',', '.', $_POST['txtgebkg']); 	 }
			if (!empty($_POST['txtAanv']) )		{   $aanwas = $_POST['txtAanv']; 	 }
			if ($_POST['txtuitvdm'] <> "0")			{   $uvaldm = $_POST['txtuitvdm']; 	 }
			if (!empty($_POST['txtHnr']))			{   $hnr = $_POST['txtHnr']; 	 }
	}
	else if (!isset($levnr_db) && $_POST['kzlfase'] == 'lam' && isset($dmdracht) && $gebdag < $dmdracht) 
	{
		$fout = 'De geboortedatum mag niet voor drachtdatum ('.$drachtdm.') liggen.';
			if ($_POST['txtgebdm'] <> "0")			{   $gebdatum = $_POST['txtgebdm'];	 }
			if ($modtech == 1 && $_POST['txtgebkg'] <> "0")			{   $gebkg = str_replace(',', '.', $_POST['txtgebkg']); 	 }
			if (!empty($_POST['txtAanv']) )		{   $aanwas = $_POST['txtAanv']; 	 }
			if ($_POST['txtuitvdm'] <> "0")			{   $uvaldm = $_POST['txtuitvdm']; 	 }
			if (!empty($_POST['txtHnr']))			{   $hnr = $_POST['txtHnr']; 	 }

	}
// EINDE  CONTROLE OP JUISTE INVOER
	else 
	{
// DATABASE BIJWERKEN
	if(isset($_POST['txtgebkg'])) { $gebkg = str_replace(',', '.', $_POST['txtgebkg']); }
	if($modtech == 0 || (isset($_POST['txtgebkg']) && empty($_POST['txtgebkg'])) ) { $txtGebkg = 'NULL'; } else { $txtGebkg = str_replace(',', '.', $_POST['txtgebkg']); }



// BEPAAL VOLWID

if($modtech == 1 && !isset($levnr_db) && $_POST['kzlfase'] == 'lam') { // Als levnr niet bestaat in database en het is geen aanvoer
	#$goed = 'Geboren. Als levnr niet bestaat in database en het is geen aanvoer';

// Zoek volwId o.b.v. dracht met eventueel bijbehorende worpdatum ter controle van invoer geboortedatum lam
if(!empty($_POST['kzlOoi'])) { 
	$keuze_ooi = $_POST['kzlOoi'];

$zoek_volwId_obv_dracht = mysqli_query($db,"
SELECT volwId
FROM tblVolwas v
WHERE date_add(v.datum,interval 183 day) > CURRENT_DATE() and mdrId = ".mysqli_real_escape_string($db,$keuze_ooi)."
") or die (mysqli_error($db));

while ($dr = mysqli_fetch_assoc($zoek_volwId_obv_dracht)) { $drachtId = $dr['volwId']; }

// Zoek geboortedatum lam van worp moerder
$dmgeb_dr = worpdatum($db,$keuze_ooi)[0];
$gebdm_dr = worpdatum($db,$keuze_ooi)[1];

/*echo $dmgeb_dr.'<br>';
echo $gebdm_dr.'<br>';*/
// Einde Zoek geboortedatum lam van worp moerder

if(isset($drachtId)) { $volwId = $drachtId; }

}
// Einde Zoek volwId o.b.v. dracht met eventueel bijbehorende worpdatum ter controle van invoer geboortedatum lam
} // Einde if($modtech == 1 && !isset($levnr_db) && $_POST['kzlfase'] == 'lam')


if($modtech == 1 && (
  (isset($levnr_db) && !isset($volwId_db)) || // levnr bestaat in db maar heeft geen ouders
  (!isset($levnr_db) && $_POST['kzlfase'] != 'lam') || // Levnr bestaat niet in db en het betreft aanvoer
  (!isset($levnr_db) && $_POST['kzlfase'] == 'lam' && !isset($drachtId)) // Levnr bestaat niet in db, is geen aanvoer en dracht bestaat niet binnen 183 dagen
) ) { // MAAK nieuw volwId in de genoemde 3 gevallen

/*
if(isset($levnr_db) && !isset($volwId_db)) {
$goed ='Levnr bestaat in db maar heeft geen ouders'; }
if(!isset($levnr_db) && $_POST['kzlfase'] != 'lam') {
$goed = 'Levnr bestaat niet in db en het betreft aanvoer '; }*/

if(empty($_POST['kzlOoi']) || $modtech == 0) { $mdrId = 'NULL'; } else { $mdrId = $_POST['kzlOoi']; }
if(empty($_POST['kzlRam']) || $modtech == 0) { $vdrId = 'NULL'; } else { $vdrId = $_POST['kzlRam']; }


if($mdrId <> 'NULL' || $vdrId <> 'NULL') { // Als of moeder of vader is gevuld
$insert_tblVolwas = " INSERT INTO tblVolwas SET mdrId = ".mysqli_real_escape_string($db,$mdrId).", vdrId = ".mysqli_real_escape_string($db,$vdrId)." ";
/*echo $insert_tblVolwas.'<br>';*/			mysqli_query($db,$insert_tblVolwas) or die (mysqli_error($db));

	if($mdrId <> 'NULL' && $vdrId <> 'NULL') {
	$zoek_volwId = mysqli_query($db,"SELECT max(volwId) volwId FROM tblVolwas WHERE mdrId = ".mysqli_real_escape_string($db,$mdrId)." and vdrId = ".mysqli_real_escape_string($db,$vdrId)." ") or die (mysqli_error($db)); }

	if($mdrId == 'NULL' && $vdrId <> 'NULL') {
	$zoek_volwId = mysqli_query($db,"SELECT max(volwId) volwId FROM tblVolwas WHERE isnull(mdrId) and vdrId = ".mysqli_real_escape_string($db,$vdrId)."
	") or die (mysqli_error($db)); }

	if($mdrId <> 'NULL' && $vdrId == 'NULL') {
	$zoek_volwId = mysqli_query($db,"SELECT max(volwId) volwId FROM tblVolwas WHERE mdrId = ".mysqli_real_escape_string($db,$mdrId)." and isnull(vdrId)
	") or die (mysqli_error($db)); }
	
	while ( $vw = mysqli_fetch_assoc ($zoek_volwId)) { $volwId = $vw['volwId']; }

if (!isset($levnr_db) && $_POST['kzlfase'] == 'lam' && !isset($drachtId)) { // als scenario 3 van toepssing is (ofwel geboren lam)
#$goed = 'Levnr bestaat niet in db, is geen aanvoer en dracht bestaat niet binnen 183 dagen';
	$var145dagen = 60*60*24*145;
	$datumdracht = strtotime($gebdag) - $var145dagen; $drachtday = date("Y-m-d", $datumdracht);

$update_tblVolwas = " UPDATE tblVolwas SET datum = '".mysqli_real_escape_string($db,$drachtday)."' WHERE volwId = ".mysqli_real_escape_string($db,$volwId)." ";
/*echo $update_tblVolwas.'<br>';*/			mysqli_query($db,$update_tblVolwas) or die (mysqli_error($db));
}

} // Einde Als of moeder of vader is gevuld

} // Einde MAAK nieuw volwId in de genoemde 3 gevallen

// EINDE BEPAAL VOLWID

// GEBOREN LAM toevoegen
if (isset($levnr) && $_POST['kzlfase'] == 'lam' && empty($_POST['txtuitvdm']) )
		{
if(!isset($levnr_db)) {
$insert_tblSchaap= "INSERT INTO tblSchaap SET levensnummer = '".mysqli_real_escape_string($db,$levnr)."', geslacht = '".mysqli_real_escape_string($db,$sekse)."', rasId = ".mysqli_real_escape_string($db,$_POST['kzlras'])." ";
	mysqli_query($db,$insert_tblSchaap) or die (mysqli_error($db)); $met1 = 1; // aantal ingevoerd geforceerd ophogen met 1
}
$zoek_schaapId = mysqli_query($db,"select schaapId from tblSchaap where levensnummer = '".mysqli_real_escape_string($db,$levnr)."' ") or die (mysqli_error($db));
	while ( $sId = mysqli_fetch_assoc ($zoek_schaapId)) { $schaapId = $sId['schaapId']; }

if(isset($volwId)) {
$update_tblSchaap = "UPDATE tblSchaap SET volwId = ".mysqli_real_escape_string($db,$volwId)." WHERE schaapId = ".mysqli_real_escape_string($db,$schaapId);

/*echo $update_tblSchaap.'<br>';*/ 	mysqli_query($db,$update_tblSchaap) or die (mysqli_error($db));
}			

$insert_tblStal= "insert into tblStal set lidId = ".mysqli_real_escape_string($db,$lidId).", schaapId = ".mysqli_real_escape_string($db,$schaapId)." ";
			mysqli_query($db,$insert_tblStal) or die (mysqli_error($db));
			
$zoek_stalId = mysqli_query($db,"select stalId from tblStal where lidId = ".mysqli_real_escape_string($db,$lidId)." and schaapId = ".mysqli_real_escape_string($db,$schaapId)." ") or die (mysqli_error($db));
	while ( $stId = mysqli_fetch_assoc ($zoek_stalId)) { $stalId = $stId['stalId']; }
			
$insert_tblHistorie_geb = "insert into tblHistorie set stalId = ".mysqli_real_escape_string($db,$stalId).", datum = '".mysqli_real_escape_string($db,$gebdag)."', kg = ".mysqli_real_escape_string($db,$txtGebkg).", actId = 1 ";
			mysqli_query($db,$insert_tblHistorie_geb) or die (mysqli_error($db));


		$zoek_hisId = mysqli_query($db,"select hisId from tblHistorie where stalId = ".mysqli_real_escape_string($db,$stalId)." ") or die (mysqli_error($db));
			while ( $hId = mysqli_fetch_assoc ($zoek_hisId)) { $hisId = $hId['hisId']; }
			
if($modtech == 1) {
		$insert_tblBezet = "insert into tblBezet set hokId = ".mysqli_real_escape_string($db,$_POST['kzlhok']).", hisId = ".$hisId." ";
			mysqli_query($db,$insert_tblBezet) or die (mysqli_error($db));
}
			
if ($modmeld == 1 ) {
	$reqst_file = 'InvSchaap.php_geboren';
	$Melding = 'GER';
include "maak_request_func.php";
include "maak_request.php";
}		
}// Einde GEBOREN LAM toevoegen


// aangeschafte volwassen dieren toevoegen (gewicht IsNull)
else if ( (!empty($_POST['kzlfase']) && $_POST['kzlfase'] != 'lam') || (isset($levnr_db) && isset($aanwas_db)) )
		{
		if (empty($_POST['txtgebkg'])) 	{ $insGebkg = "kg = NULL";	}
		else 							{ $insGebkg = "kg = 'str_replace(',', '.', $_POST[txtgebkg])' "; }
		if(!empty($_POST['kzlKleur']))	{ $kleur = $_POST['kzlKleur']; }
		if(!empty($_POST['txtHnr']))	{ $halsnr = "halsnr = $_POST[txtHnr] "; } else { $halsnr = "halsnr = NULL"; }

if(!isset($levnr_db)) {	
$insert_tblSchaap = "insert into tblSchaap set levensnummer = '".mysqli_real_escape_string($db,$levnr)."', geslacht = '".mysqli_real_escape_string($db,$sekse)."', rasId = '$_POST[kzlras]' ";
	mysqli_query($db,$insert_tblSchaap) or die (mysqli_error($db)); $met1 = 1; // aantal ingevoerd geforceerd ophogen met 1
}
$zoek_schaapId = mysqli_query($db,"select schaapId from tblSchaap where levensnummer = '".mysqli_real_escape_string($db,$levnr)."' ") or die (mysqli_error($db));
	while ( $sId = mysqli_fetch_assoc ($zoek_schaapId)) { $schaapId = $sId['schaapId']; }

if(isset($volwId)) {
$update_tblSchaap = "UPDATE tblSchaap SET volwId = ".mysqli_real_escape_string($db,$volwId)." WHERE schaapId = ".mysqli_real_escape_string($db,$schaapId);

/*echo $update_tblSchaap.'<br>';*/ 	mysqli_query($db,$update_tblSchaap) or die (mysqli_error($db));
}

// Invoeren in tblStal
if(isset($kleur)) {
$insert_tblStal= "insert into tblStal set lidId = ".mysqli_real_escape_string($db,$lidId).", schaapId = ".mysqli_real_escape_string($db,$schaapId).", kleur = '".mysqli_real_escape_string($db,$kleur)."', ".mysqli_real_escape_string($db,$halsnr)." ";	
}
else 
{
$insert_tblStal= "insert into tblStal set lidId = ".mysqli_real_escape_string($db,$lidId).", schaapId = ".mysqli_real_escape_string($db,$schaapId).", ".mysqli_real_escape_string($db,$halsnr)." ";	
}
	mysqli_query($db,$insert_tblStal) or die (mysqli_error($db));
// Einde Invoeren in tblStal	
$zoek_stalId = mysqli_query($db,"
select max(stalId) stalId
from tblStal
where lidId = ".mysqli_real_escape_string($db,$lidId)." and schaapId = ".mysqli_real_escape_string($db,$schaapId)."
") or die (mysqli_error($db));
	while ( $stId = mysqli_fetch_assoc ($zoek_stalId)) { $stalId = $stId['stalId']; }

if(isset($gebdag) && !isset($dmgeb_db)) { // Geboortedatum bij volwassendieren niet verplicht
$insert_tblHistorie_geb = "insert into tblHistorie set stalId = ".mysqli_real_escape_string($db,$stalId).", datum = '".$gebdag."', ".$insGebkg.", actId = 1 ";
	mysqli_query($db,$insert_tblHistorie_geb) or die (mysqli_error($db));
		}

// Aanvoer kan Aankoop zijn of terug van uitscharen. Indien niet terug van uitscharen dan aankoop.
$zoek_Uitscharen = mysqli_query($db,"
select actId
from tblHistorie h
 join (
	select max(hisId) hisId
	from tblHistorie h
	 join tblStal st on (h.stalId = st.stalId)
	where st.lidId = ".mysqli_real_escape_string($db,$lidId)/* Ik ga ervan uit dat max(hisId) afvoer is of te wel tblActie.af = 1 */."
 ) lh on (lh.hisId = h.hisId)
") or die (mysqli_error($db));
	while ( $uitsch = mysqli_fetch_assoc ($zoek_Uitscharen)) { $uitsch_db = $uitsch['actId']; }

	if($uitsch_db == 10) { $actId = 11; } else { $actId = 2; }
$insert_tblHistorie_aank = "insert into tblHistorie set stalId = ".mysqli_real_escape_string($db,$stalId).", datum = '".$dmaanw."', actId = ".mysqli_real_escape_string($db,$actId)." ";
	mysqli_query($db,$insert_tblHistorie_aank) or die (mysqli_error($db));

$zoek_hisId_tbv_tblBezet = mysqli_query($db,"
select hisId from tblHistorie where stalId = ".mysqli_real_escape_string($db,$stalId)." and actId > 1
") or die (mysqli_error($db));
	while ( $hiho = mysqli_fetch_assoc ($zoek_hisId_tbv_tblBezet)) { $hishok = $hiho['hisId']; }

if(!isset($aanwas_db)) {
$insert_tblHistorie_aanw = "insert into tblHistorie set stalId = ".mysqli_real_escape_string($db,$stalId).", datum = '".$dmaanw."', actId = 3 ";
	mysqli_query($db,$insert_tblHistorie_aanw) or die (mysqli_error($db));
}

if ($modtech == 1 && !empty($_POST['kzlhok'])) {

	$insert_tblBezet = "insert into tblBezet set hokId = ".mysqli_real_escape_string($db,$_POST['kzlhok']).", hisId = ".mysqli_real_escape_string($db,$hishok)." ";
			mysqli_query($db,$insert_tblBezet) or die (mysqli_error($db));		
}

if ($modmeld == 1 ) {
	$zoek_hisIdaanv = mysqli_query($db,"select hisId from tblHistorie where stalId = ".mysqli_real_escape_string($db,$stalId)." and actId = ".mysqli_real_escape_string($db,$actId)." ") or die (mysqli_error($db));
		while ( $hId = mysqli_fetch_assoc ($zoek_hisIdaanv)) { $hisId = $hId['hisId']; }
			
	$reqst_file = 'InvSchaap.php_aanwas';
	$Melding = 'AAN';
include "maak_request_func.php";
include "maak_request.php";
}
		
		}
// Einde aangeschafte volwassen dieren toevoegen (gewicht IsNull)
		
// Dood dier met levensnummer toevoegen
else if (!empty($_POST['txtuitvdm']) && !empty($_POST['txtlevnr']) && isset($levnr) && !isset($levnr_db))
		{
if(!isset($volwId)) { $volwId = 'NULL'; } 
$insert_tblSchaap= "insert into tblSchaap set levensnummer = '".mysqli_real_escape_string($db,$levnr)."', rasId='$_POST[kzlras]', geslacht = '$_POST[kzlsekse]', volwId = ".mysqli_real_escape_string($db,$volwId).", momId='$_POST[kzlMoment]', redId='$_POST[kzlReden]' ";
	mysqli_query($db,$insert_tblSchaap) or die (mysqli_error($db)); $met1 = 1; // aantal ingevoerd geforceerd ophogen met 1
			
$zoek_schaapId = mysqli_query($db,"select schaapId from tblSchaap where levensnummer = '".mysqli_real_escape_string($db,$levnr)."' ") or die (mysqli_error($db));
	while ( $sId = mysqli_fetch_assoc ($zoek_schaapId)) { $schaapId = $sId['schaapId']; }

$insert_tblStal= "insert into tblStal set lidId = ".mysqli_real_escape_string($db,$lidId).", schaapId = ".mysqli_real_escape_string($db,$schaapId).", rel_best = ".mysqli_real_escape_string($db,$rendac_Id)." ";
	mysqli_query($db,$insert_tblStal) or die (mysqli_error($db));
		
$zoek_stalId = mysqli_query($db,"
select stalId
from tblStal
where lidId = ".mysqli_real_escape_string($db,$lidId)." and schaapId = ".mysqli_real_escape_string($db,$schaapId)."
") or die (mysqli_error($db));
	while ( $stId = mysqli_fetch_assoc ($zoek_stalId)) { $stalId = $stId['stalId']; }
			
$insert_tblHistorie_geb = "insert into tblHistorie set stalId = ".mysqli_real_escape_string($db,$stalId).", datum = '".$gebdag."', kg = ".mysqli_real_escape_string($db,$txtGebkg).", actId = 1 ";
	mysqli_query($db,$insert_tblHistorie_geb) or die (mysqli_error($db));
		
$insert_tblHistorie_doo = "insert into tblHistorie set stalId = ".mysqli_real_escape_string($db,$stalId).", datum = '".$dmuitv."', actId = 14  ";
	mysqli_query($db,$insert_tblHistorie_doo) or die (mysqli_error($db));
			
$zoek_hisId = mysqli_query($db,"
select hisId
from tblHistorie
where stalId = ".mysqli_real_escape_string($db,$stalId)." and actId = 14
") or die (mysqli_error($db));
	while ( $hi = mysqli_fetch_assoc ($zoek_hisId)) { $hisId = $hi['hisId']; }

if ($modmeld == 1 ) {
	$reqst_file = 'InvSchaap.php_uitval';
	$Melding = 'DOO';
include "maak_request_func.php";
include "maak_request.php";
}
}// Einde Dood dier met levensnummer toevoegen	

// Dood dier zonder levensnummer toevoegen
		else if (!empty($_POST['txtuitvdm']) && empty($_POST['txtlevnr']))
		{			
			If (empty($_POST['kzlras']))	 {  $insRas = 'NULL';		}
			else 			{	$insRas = $_POST['kzlras'];		}
			If (empty($_POST['kzlsekse'])) {  $insSekse = "geslacht = NULL";	}
			else 			{	$insSekse = "geslacht = '$_POST[kzlsekse]' ";}
		
		
// $ras en $gebkg werden t/m mei 2012 opgeslagen als 0 ipv NULL. Waarschijnlijk ivm numeriek veld !!??
if(!isset($volwId)) { $volwId = 'NULL'; }
$insert_tblSchaap= "insert into tblSchaap set levensnummer= ".mysqli_real_escape_string($db,$ubn).", rasId = ".mysqli_real_escape_string($db,$insRas).", volwId = ".mysqli_real_escape_string($db,$volwId).", $insSekse, momId = '$_POST[kzlMoment]', redId = '$_POST[kzlReden]' ";
	mysqli_query($db,$insert_tblSchaap) or die (mysqli_error($db)); $zonder1 = 1; // aantal ingevoerd geforceerd ophogen met 1

$zoek_schaapId = mysqli_query($db,"select schaapId from tblSchaap where levensnummer = ".mysqli_real_escape_string($db,$ubn)." ") or die (mysqli_error($db));
	while ( $sId = mysqli_fetch_assoc ($zoek_schaapId)) { $schaapId = $sId['schaapId']; }
	
$insert_tblStal= "insert into tblStal set lidId = ".mysqli_real_escape_string($db,$lidId).", schaapId = ".mysqli_real_escape_string($db,$schaapId).", rel_best = ".mysqli_real_escape_string($db,$rendac_Id)." ";
	mysqli_query($db,$insert_tblStal) or die (mysqli_error($db));

$update_tblSchaap= "update tblSchaap set levensnummer = NULL where levensnummer = ".mysqli_real_escape_string($db,$ubn)." ";
	mysqli_query($db,$update_tblSchaap) or die (mysqli_error($db));

$zoek_stalId = mysqli_query($db,"select stalId from tblStal where lidId = ".mysqli_real_escape_string($db,$lidId)." and schaapId = ".mysqli_real_escape_string($db,$schaapId)." ") or die (mysqli_error($db));
	while ( $stId = mysqli_fetch_assoc ($zoek_stalId)) { $stalId = $stId['stalId']; }
	
$insert_tblHistorie_geb = "insert into tblHistorie set stalId = ".mysqli_real_escape_string($db,$stalId).", datum = '".$gebdag."', kg = ".mysqli_real_escape_string($db,$txtGebkg).", actId = 1 ";
	mysqli_query($db,$insert_tblHistorie_geb) or die (mysqli_error($db));

$insert_tblHistorie_doo = "insert into tblHistorie set stalId = ".mysqli_real_escape_string($db,$stalId).", datum = '".$dmuitv."', actId = 14  ";
	mysqli_query($db,$insert_tblHistorie_doo) or die (mysqli_error($db));
}// Einde Dood dier zonder levensnummer toevoegen	


		$gebdatum = $_POST['txtgebdm'];
		$levnr = $_POST['txtlevnr'];
		$aanwas = $_POST['txtAanv'];
	

// Index invoeren (bijwerken)
If (!empty($_POST['txtindex']) && isset($levnr)) // Er moet wel een levensnummer bestaan
{
$index_invoeren = "update tblSchaap set indx = '$index' where schaapId = ".mysqli_real_escape_string($db,$schaapId)." " or die (mysqli_error($db));
	mysqli_query($db,$index_invoeren) or die (mysqli_error($db));
}

if(isset($levnr)) { $levnr = substr($_POST['txtlevnr'], 0, 6); }
unset($aanwas);
unset($index);
// EINDE   DATABASE BIJWERKEN	
	}
}

 if(isset($met1)) { $met = $met+$met1; unset($met1); }
 if(isset($zonder1)) { $zonder = $zonder+$zonder1; unset($zonder1); }
if (!empty($met) || !empty($zonder))
{ 
?><table border = '0' style = "font-size : 10px" > <tr> <td><i> 
Vandaag ingevoerd :</td><td><?php echo $met; ?> met levensnummer</td></tr>
<tr><td></td><td><?php echo $zonder; ?> zonder levensnummer. </td></tr></table><?php
}

?>
<table border = 0> <tr> <td></td>

<td>	
<!-- ********************
 INVULVELDEN LINKS 
     ******************** -->
<table border = 0>

<form action="InvSchaap.php" method="post"> 
<tr>
<td> Levensnummer : </td>
<td><input type="text" name="txtlevnr" id="levnr" onfocus="vader_dracht()" value = <?php if(isset($levnr)) { echo $levnr ; } ?> ></td>
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
   if((!isset($_POST['knpSave']) && $kleur == $key) || (isset($_POST["kzlKleur"]) && $_POST["kzlKleur"] == $key) ) {
	echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else {
	echo '<option value="' . $key . '">' . $waarde . '</option>';
  }
} ?>
</select>  
 <input type = text name = "txtHnr" style = "text-align : right" size = 1 value = <?php if(isset($hnr)) { echo $hnr; } ?> > </td>
</tr>
<!-- KZLGENERATIE -->
<tr>
<td>Generatie : </td>
<td>
<?php
$optie_fase = array('' => '', 'lam' => 'lam', 'moeder' => 'moeder', 'vader' => 'vader');
?>
	<select name= "kzlfase" id ="fase" style= "width:76;" > <?php
foreach ( $optie_fase as $key =>$waarde)	
{
	$keuze = '';
	if(isset($_POST['kzlfase']) && $_POST['kzlfase'] == $key)
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

 <select name= "kzlsekse" id = "sekse" style= "width:59;" > 
<?php
$opties = array('' => '', 'ooi' => 'ooi', 'ram' => 'ram', 'kween' => 'kween');
foreach ( $opties as $key => $waarde)
{
   $keuze = '';
   if(isset($_POST['kzlsekse']) && $_POST['kzlsekse'] == $key)
   {
        $keuze = ' selected ';
   }
   echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
} ?>
 </select> 
<sup> *</sup></td>
</tr>

<!-- ras -->
<tr>
<td>Ras :</td>
<td>
<?php
$result = mysqli_query($db,"
select r.rasId, r.ras 
from tblRas r
 join tblRasuser ru on (r.rasId = ru.rasId)
where ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and r.actief = 1 and ru.actief = 1
order by r.ras
") or die (mysqli_error($db)); ?>
 <select name= "kzlras" id = "ras" style= "width:80;" >
 <option></option>
 <?php	while($row = mysqli_fetch_array($result))
		{
			$opties= array($row['rasId']=>$row['ras']);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if(isset($_POST['kzlras']) && $_POST['kzlras'] == $key)
		{
			$keuze = ' selected ';
		}
				
		echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
			}
		
		} ?>
 </select>

<sup> *</sup></td></tr>

<!-- moeder -->
<tr> <td> </td>	
<td><i><sub> Werknr - lammeren - halsnr </sub></i>
</td> </tr>

<tr> <td> Werknr ooi (moeder) : </td>
<td>
<?php
$result = mysqli_query($db,"(".$vw_kzlOoien.")  ") or die (mysqli_error($db)); ?>

 <select name="kzlOoi" id ="moeder" style="width:100;" onchange="vader_dracht()" >
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

<!-- vader -->
<tr>
<td style = "font-size : 13px"><i>Werknr - index </i>
</td> </tr>

<tr> <td> Werknr ram (vader) : </td>

<td> <?php
/*if(isset($drachtvader)) { echo $drachtvader; } 
else {*/
$resultvader = mysqli_query($db,"
select st.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk) werknr, s.indx
from tblStal st 
 join tblSchaap s on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
where s.geslacht = 'ram' and h.actId = 3 and h.skip = 0 and lidId = ".mysqli_real_escape_string($db,$lidId)."
and not exists (
	select st.schaapId
	from tblStal stal 
	 join tblHistorie h on (h.stalId = stal.stalId)
	 join tblActie  a on (a.actId = h.actId)
	where stal.schaapId = s.schaapId and a.af = 1 and h.datum < DATE_ADD(CURDATE(), interval -1 year) and h.skip = 0 and lidId = ".mysqli_real_escape_string($db,$lidId).")
order by right(s.levensnummer,$Karwerk)
") or die (mysqli_error($db)); ?>
 <select name= "kzlRam" style= "width:100; text-align:left;" id="vader" onfocus="vader_dracht()" >
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
 </select><p id="result"></p> 
<?php //} ?>
</td>  </tr>
</td> </tr>

<!--Geboortedatum -->
<tr> 
<td height = 40 valign = "bottom">Geboortedatum :</td>
<td valign = "bottom"><input id="datepicker1" name="txtgebdm" type="text" value = <?php if(isset($gebdatum)) { echo $gebdatum; } ?> ><sup> * / **</sup></td></tr>

<?php if($modtech == 1) { ?>
<tr> 
<td>Gewicht :</td>
<td><input type= "text" id = "gewicht" name= "txtgebkg"  value = <?php if(isset($gebkg)) { echo $gebkg; } ?> > <sup> *</sup> </td></tr>
<?php } ?>
<tr> 
<td>Aanvoerdatum :</td>
<td><input type= "text" id="datepicker2" name= "txtAanv" value = <?php if(isset($aanwas)) { echo $aanwas; } ?> ></td></tr>
<tr> 
<td>Index :</td>
<td><input type= "text"  name= "txtindex" value = <?php if(isset($index)) { echo $index; } ?> ></td></tr>




</table>

</td>
<td width = 160> </td>
<td valign = "top">

<!-- ********************* 
  INVULVELDEN RECHTS 
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
select m.momId, m.moment
from tblMoment m
 join tblMomentuser mu on (m.momId = mu.momId)
where mu.lidId = ".mysqli_real_escape_string($db,$lidId)." and m.actief = 1 and mu.actief = 1
order by m.momId
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
<td><input id="datepicker3" name="txtuitvdm" type="text" height= 50 value = <?php if(isset($uvaldm)) { echo $uvaldm; } ?> ><sup> **</sup></td>
</tr>


<!-- reden uitval -->
<tr>
<td> Reden uitval :</td>
<td> <?php
$result = mysqli_query($db, "
select r.reden, ru.redId
from tblReden r
 join tblRedenuser ru on (r.redId = ru.redId)
where r.actief = 1 and ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and ru.uitval = 1
order by r.reden
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
<!-- Hoknummer -->
<!-- <tr>
<td> </td>	
<td><i><sub> Verblijf - doelgroep - aanwezig </sub></i>
</td> </tr> -->

<tr>
<td>Verblijf :</td>
<td>
<?php
$result = mysqli_query($db," select hokId, hoknr from tblHok where lidId = ".mysqli_real_escape_string($db,$lidId)." and actief = 1 order by hoknr ") or die (mysqli_error($db)); ?>
 <select name="kzlhok" id="verblijf" style="width:100;" >
 <option></option>	
<?PHP	while($row = mysqli_fetch_array($result))
		{
		
			$opties= array($row['hokId']=>$row['hoknr']/*.'&nbsp &nbsp &nbsp &nbsp '.$row['doel'].'&nbsp &nbsp &nbsp &nbsp &nbsp '.$row['nu']*/);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if(isset($_POST['kzlhok']) && $_POST['kzlhok'] == $key)
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