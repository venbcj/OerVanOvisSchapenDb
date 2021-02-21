<?php /* 17-2-14 : $schaapgegevens aangepast. vw_Schapen is aangevuld met max bezetId (laatste hoknr). Op deze manier wordt maar 1 regel uit vw_Bezetting gekoppeld met vw_Schapen. Het hok wordt alleen getoond als het dier een lam is. 
		Bij keuze moederdier moet per schaap het selectieveld uit staan 
  18-2-2014 : $reslevnr aangepast. $levnr vervangen door $_POST['kzlLevnr'] 
  
  19-2-14 : Kolom 'ander medicijn' uitgezet omdat weergave niet juist is.
  19-2-14 : Uit $kzl type = 'lam' verwijderd. Gevolg is uit $reswerknr 'and isnull(tot) and isnull(afsluitdm)' verwijderd. Ook is kolom 'Generatie' toegevoegd
		Post levensnummer via link naar MedOverzSchaap.php
  8-8-2014 : Aantal karakters werknr variabel gemaakt, quotes bij variabelen weggehaald
  11-8-2014 : veld type gewijzigd in fase 
  12-10-2014 : Ovv Rina 1e en 2e inenting eruit gehaald 
  28-11-2014 Toediening aangepast op Chargenummer. Of te wel inkId 
  20-2-2015 : login toegevoegd 
  14-11-2015 : naamwijziging van Medicijn registratie naar Medicijn toediening en Keuze medicijn naar Keuze medicijnvoorraad
  8-12-2015 : laatste geboren lam bij moeders tonen hoeveelheid per schaap verplaatst en getotaliseerd 
  6-1-2016 : Hoknr gewijzigd aar Verblijf */
$versie = '25-11-2016'; /* actId = 3 uit on clause gehaald en als sub query genest */
$versie = '28-12-2016'; /* Bij keuze moederdieren wordt standaard chbKeuze aangevinkt */
$versie = "23-1-2017"; /* 18-1-2017 Query's aangepast n.a.v. nieuwe DoelId		22-1-2017 tblBezetting gewijzigd naar tblBezet	23-1-2017 kalender toegevoegd */
$versie = "7-2-2017"; /* de Extra opties bij hok leidde ook bij keuze schaap ook voor tonen van zowel lam als moederdier. Dit is aangepast.	9-2-2017 : foutmelding toegevoegd als schaap niet is geselecteerd.  */
$versie = "17-3-2017"; /* tblPeriode verwijderd 	26-3-2017 : vanaf - t/m geboortedatum zoeken toegevoegd */
$versie = "25-3-2018"; /* Keuze moederdier van lammeren in een verblijf is gewijzigd naar keuze volwassen dieren die in het verblijf zitten  */
$versie = "17-6-2018"; /* Registreren van afgevoerde schapen mogelijk gemaakt */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '6-1-2019'; /* javascript toegevoegd tbv stadat en eenheid wijzigen per medicijn */
$versie = '10-2-2019'; /* zoeken op Halsnr mogelijk gemaakt */
$versie = '20-12-2019'; /* tabelnaam gewijzigd van UIT naar uit tabelnaam */
 session_start();  ?>
  
<html>
<head>
<title>Registratie</title>
</head>
<body>

<center>
<?php
include "kalender.php";
$titel = 'Medicijn toediening';
$subtitel = '';
Include "header.php"; ?>

		<TD width = 960 height = 400 valign = top >
<?php
$file = "Med_registratie.php";
Include "login.php";
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { if($modtech ==1) { 

$min_inkId_met_vrd = " 
SELECT min(i.inkId) inkId, a.naam, a.stdat, e.eenheid, sum(i.inkat-coalesce(n.vbrat,0)) vrdat
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblInkoop i on (i.enhuId = eu.enhuId)
 join tblArtikel a on (i.artId = a.artId)
 left join (
	SELECT n.inkId, sum(n.nutat*n.stdat) vbrat
	FROM tblNuttig n
	GROUP BY n.inkId
 ) n on (i.inkId = n.inkId)
WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and i.inkat-coalesce(n.vbrat,0) > 0 and a.soort = 'pil'
GROUP BY a.naam, a.stdat, e.eenheid
ORDER BY a.naam
"; 

$min_inkId_met_vrd1 = mysqli_query($db,$min_inkId_met_vrd) or die (mysqli_error($db));
	while($lin = mysqli_fetch_array($min_inkId_met_vrd1))
		{
			$stdat = str_replace('.00', '', $lin['stdat']);
$array_eenheid[$lin['inkId']] = 'x '.$stdat. $lin['eenheid'].' per schaap';

//echo $array_eenheid[$lin['artId']].'<br>';
}
?>

<script>
function eenheid_artikel() {

var artikel	 = document.getElementById("artikel");		var artikel_v = artikel.value;


 if(artikel_v.length > 0) toon_eenheid(artikel_v);

}

 var jArray= <?php echo json_encode($array_eenheid); ?>;

function toon_eenheid(e) {
	document.getElementById('aantal').innerHTML = jArray[e];
}
</script>
<?php
$hok_uitgez = "Alles";
	
//If (empty($_POST['txtStdrd']))	{	$stdrd = 1;		} else {	$stdrd = $_POST['txtStdrd'];	}
If (empty($_POST['txtAantal']))	{	$nutat = 1;	} else {	$nutat = $_POST['txtAantal'];	}
If (empty($_POST['txtDatum']))	{	$Datum = '';	} else {	$Datum = $_POST['txtDatum'];	}
If (empty($_POST['kzlArtikel']))	{	$kzlArt = '';	} else {	$kzlArt = $_POST['kzlArtikel'];		}
if(isset($_POST['kzlkwaal'])) { $kwaal = $_POST['kzlkwaal']; }


If (isset($_POST['knpToon']))	{
	if (empty($_POST['kzlLevnr']) && empty($_POST['kzlWerknr']) && empty($_POST['kzlHalsnr']) && empty($_POST['chbOoi']) && empty($_POST['kzlHok']) && empty($_POST['txtGeb_van'])	)
		{	$fout = "Keuze uit schapen is niet gemaakt.";	}
	else if(empty($_POST['kzlArtikel']))	{	$fout = "Medicijn is niet geselecteerd.";	
if(!empty($_POST['txtGeb_van'])) { $Geb_van = $_POST['txtGeb_van']; }
if(!empty($_POST['txtGeb_tot'])) { $Geb_tot = $_POST['txtGeb_tot']; }
}
	
	else {	$knpInsert = "toonknpInsert";	}

								}

?>
<?php
If (isset($_POST['knpInsert'])) {

if(empty($_POST['chbKeuze'])) { $fout = "Er is geen schaap geselecteerd."; }
else {
// Gegevens van artikel ophalen 
$qryArtikel = mysqli_query($db,"
select a.artId, a.stdat, e.eenheid
from tblArtikel a
 join tblInkoop i on (a.artId = i.artId)
 join tblEenheiduser eu on (eu.enhuId = a.enhuId)
 join tblEenheid e on (e.eenhId = eu.eenhId)
where i.inkId = ".mysqli_real_escape_string($db,$kzlArt)."
") or die (mysqli_error($db));

while ($qryvrd = mysqli_fetch_assoc($qryArtikel))
{	$artId = "$qryvrd[artId]";
		$stdrd = str_replace('.00', '', $qryvrd['stdat']); // haalt .00 weg in de waarde
	$eenh = "$qryvrd[eenheid]";	
	$stdat = $qryvrd['stdat'];
}
// EINDE Gegevens van artikel ophalen
// Berekening Totaal hoeveelheid toe te dienen medicijnen
$tel_aantal_schapen = "
select count(s.schaapId) schpat
from tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and s.levensnummer IN (".implode(',', $_POST['chbKeuze']).")
";


/*echo $tel_aantal_schapen.'<br>';*/ $tel_aantal_schapen = mysqli_query($db,$tel_aantal_schapen) or die (mysqli_error($db));


	while ($tal = mysqli_fetch_assoc($tel_aantal_schapen))
 $rows_lev = $tal['schpat'];
 $totaal = $stdrd * $nutat * $rows_lev;
// EINDE Berekening Totaal hoeveelheid toe te dienen medicijnen

$artikelvoorraad = mysqli_query($db,"
select sum(i.inkat-coalesce(n.vbrat,0)) vrdat
from (
	select i.inkId, sum(i.inkat) inkat
	from tblInkoop i
	where i.artId = ".mysqli_real_escape_string($db,$artId)."
	group by i.inkId
 ) i 
 left join (
	select n.inkId, sum(n.nutat*n.stdat) vbrat
	from tblInkoop i
	 join tblNuttig n on (i.inkId = n.inkId)
	where i.artId = ".mysqli_real_escape_string($db,$artId)."
	group by n.inkId
 ) n on (i.inkId = n.inkId)
") or die (mysqli_error($db));

while ($instock = mysqli_fetch_assoc($artikelvoorraad))
{ $stock = "$instock[vrdat]";	}

if(empty($_POST['txtDatum']))	{	$fout = "Datum is niet bekend.";
							if(!empty($_POST['kzlArtikel']))	{	$knpInsert = "toonknpInsert";	}
								}
else if(empty($_POST['txtAantal']))	{	$fout = "Het aantal is niet bekend.";
							if(!empty($_POST['kzlArtikel']))	{	$knpInsert = "toonknpInsert";	}
									}
else if(empty($_POST['kzlkwaal']))	{	$fout = "De reden is niet geselecteerd.";
							if(!empty($_POST['kzlArtikel']))	{	$knpInsert = "toonknpInsert";	}
									}

else if (empty($_POST['chbKeuze'])) {	$fout = "Er is geen schaap geselecteerd.";	
							if(!empty($_POST['kzlArtikel']))	{	$knpInsert = "toonknpInsert";	}
									}

// Controle van het toedien aantal tov het voorraad aantal
else if ($totaal > $stock) {	$fout = "U kunt geen $totaal $eenh toedienen er is nl. nog maar $stock $eenh beschikbaar.";	}
// EINDE Controle van het toedien aantal tov het voorraad aantal
																
else // toevoegen medicijn
{

$kzlArt =  "$_POST[kzlArtikel]";
$date = date_create($_POST['txtDatum']);
		$dag=  date_format($date, 'Y-m-d');
		
// Doorlopen van geselecteerde schapen 
$zoek_schaapId = "
select s.schaapId, s.levensnummer
from tblSchaap s
where s.levensnummer IN (".implode(',', $_POST['chbKeuze']).")
";

/*echo $zoek_schaapId.'<br>';*/  $zoek_schaapId = mysqli_query($db,$zoek_schaapId) or die (mysqli_error($db));
while( $s = mysqli_fetch_assoc($zoek_schaapId)) { /* Doorlopen van geselecteerde schapen  */ 
	$schaapId = $s['schaapId'];
	$levnsr = $s['levensnummer'];

// Zoek laatste stalId
$zoek_stalId = "
select max(stalId) stalId
from tblStal st
where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and st.schaapId = ".mysqli_real_escape_string($db,$schaapId)."
";

/*echo $zoek_stalId.'<br>';*/  $zoek_stalId = mysqli_query($db,$zoek_stalId) or die (mysqli_error($db));
while( $s = mysqli_fetch_assoc($zoek_stalId)) { /* Doorlopen van geselecteerde schapen met bijbehorend stalId */ 
	$stalId = $s['stalId'];

// Zoek naar einddatum in geval schaap reeds is afgevoerd
unset($dmafv);
$zoek_einddatum = mysqli_query($db,"
SELECT datum day, date_format(datum,'%d-%m-%Y') datum
FROM tblHistorie h
 join tblActie a on (h.actId = a.actId)
WHERE a.af = 1 and h.stalId = ".mysqli_real_escape_string($db,$stalId)."
") or die (mysqli_error($db));
while ($zk_end = mysqli_fetch_assoc($zoek_einddatum))
{ $dmafv = $zk_end['day']; $afvdm = $zk_end['datum']; }
// Einde Zoek naar einddatum in geval schaap reeds is afgevoerd

if (isset($dmafv) && $dag > $dmafv) { $opm = $levnsr.' de datum mag niet na '.$afvdm.' liggen.\n'; 

	if(isset($melding)) { $melding = $melding.$opm; } else { $melding = $opm; }
}

else { // Vervolgen toevoegen medicijn


// Aanvullen tblHistorie
$insert_tblHistorie = " INSERT INTO tblHistorie SET stalId = ".mysqli_real_escape_string($db,$stalId).", datum = '".mysqli_real_escape_string($db,$dag)."', actId= 8 ";	
		mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));
// zoeken laatste hisId van ingelezen historie t.b.v. tblNuttig ($insert_tblHistorie)
$zoek_hisId = mysqli_query($db,"
select max(hisId) hisId
from tblHistorie
where stalId = ".mysqli_real_escape_string($db,$stalId)." and datum = '".mysqli_real_escape_string($db,$dag)."' and actId = 8
") or die (mysqli_error($db));
while ($zk_hi = mysqli_fetch_assoc($zoek_hisId))
{ $hisId = $zk_hi['hisId']; }

// Hoeveelheid voorraad ophalen van oudste inkId met voorraad
$zoek_inkIds_met_voorraad = mysqli_query($db,"
select i.inkat - sum(coalesce(n.nutat*n.stdat,0)) vrdat, a.stdat
from tblInkoop i
 join tblArtikel a on (a.artId = i.artId)
 left join tblNuttig n on (i.inkId = n.inkId)
where i.inkId = ".mysqli_real_escape_string($db,$kzlArt)."
group by i.inkat, a.stdat
") or die (mysqli_error($db));
	while ($check = mysqli_fetch_assoc($zoek_inkIds_met_voorraad)) {
		//$artId = $check['artId'];
		$ink_vrd = $check['vrdat']; } // voorraad van betreffende inkoophoeveelheid
$hoeveelh = $stdat*$nutat; // hoeveelheid per dier
 $end_nutat_1 = $ink_vrd/$stdat; // Nodig als inkoophoeveelheid ten einde komt bij toediening dier. De variable betreft het nog toe kunnen dienen van het aantal x de standaard aantal.
 $rest = $hoeveelh-$ink_vrd; //restant. Nodig als inkoophoeveelheid kleiner is als hoeveelheid per dier
 $start_nutat_2 = $rest/$stdat; // Resterent inkoop voorraad voor volgende inkoopmoment (inkId)

// Als inkoophoeveelheid kleiner is dan hoeveelheid per dier en inkoophoeveelheid > 0
If($ink_vrd < $hoeveelh && $ink_vrd > 0) {

// Restant voorraad ink1 toevoegen	
	$pil_toevoegen_end = " INSERT INTO tblNuttig SET hisId = ".mysqli_real_escape_string($db,$hisId).", inkId= ".mysqli_real_escape_string($db,$kzlArt).", nutat = ".mysqli_real_escape_string($db,$end_nutat_1).", stdat= ".mysqli_real_escape_string($db,$stdat).", reduId= ".mysqli_real_escape_string($db,$kwaal)." ";	
	 mysqli_query($db,$pil_toevoegen_end) or die (mysqli_error($db));	// Einde Restant toevoegen
// Aanspreken volgende inkoophoeveelheid
 // volgende inkId opzoeken
$zoek_inkId2 = mysqli_query($db,"
select min(i.inkId) inkId
from tblEenheiduser eu
 join tblArtikel a on (a.enhuId = eu.enhuId)
 join tblInkoop i on (a.artId = i.artId)
where eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and a.artId = ".mysqli_real_escape_string($db,$artId)." and i.inkId > ".mysqli_real_escape_string($db,$kzlArt)."
") or die (mysqli_error($db));
while ($ink = mysqli_fetch_assoc($zoek_inkId2)) {
		$kzlArt = $ink['inkId']; }
 // Eerste hoeveelheid voorraad ink2 toevoegen				
	$pil_toevoegen_new = "INSERT INTO tblNuttig SET hisId = ".mysqli_real_escape_string($db,$hisId).", inkId= ".mysqli_real_escape_string($db,$kzlArt).", nutat = ".mysqli_real_escape_string($db,$start_nutat_2).", stdat= ".mysqli_real_escape_string($db,$stdat).", reduId= ".mysqli_real_escape_string($db,$kwaal)." ";	
	 mysqli_query($db,$pil_toevoegen_new) or die (mysqli_error($db));
// EindeAanspreken volgende inkoophoeveelheid

	}
else 
	{
$insert_tblNuttig = "INSERT INTO tblNuttig SET hisId = ".mysqli_real_escape_string($db,$hisId).", inkId= ".mysqli_real_escape_string($db,$kzlArt).", nutat = ".mysqli_real_escape_string($db,$nutat).", stdat= ".mysqli_real_escape_string($db,$stdat).", reduId= ".mysqli_real_escape_string($db,$kwaal)." ";	
	 mysqli_query($db,$insert_tblNuttig) or die (mysqli_error($db));
	}
	
} // Einde Vervolgen toevoegen medicijn
} // Einde Doorlopen van geselecteerde schapen
} // Einde Zoek laatste stalId
} // Einde toevoegen medicijn
if(isset($melding)) { $fout = 'De volgende dieren hebben geen medicatie gekregen !!\n\n'.$melding; }
}
} ?>


<table border = 0>

<!--	**************************************
	**	 GEGEVENS TBV MEDICIJN	**
	************************************** -->
	<form action="Med_registratie.php" method="post"> 
<tr><td colspan = 5 style = "font-size : 18px;"><b> Keuze medicijnvoorraad </b></td></tr>
<tr>
<td><i><sub> Datum </sub></i></td>
<td><i><sub>medicijn &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp</sub></i><i style = "font-size:12px;"><sub>(voorraad)</sub></i> </td>
<td><i><sub> Reden </sub></i></td>
<td colspan = 2 ><i><sub> Aantal</sub></i></td>
</tr>
<tr>

<td><input id = "datepicker1" type= text name = "txtDatum" size = "8" value = <?php echo "$Datum";?> ></td>
<?php 
//$eenheid = ''; $stadrd = '';
// Actueel inkoopId ophalen van een gekozen artikel
$queryEenheid = mysqli_query($db,"
select i.inkId inkId, a.stdat, e.eenheid
from tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblInkoop i on (i.enhuId = eu.enhuId)
 join tblArtikel a on (i.artId = a.artId)
where eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and i.inkId = '$kzlArt'
 ") or die (mysqli_error($db));
while ($qryeenh = mysqli_fetch_assoc($queryEenheid))
{
	$inkId = $qryeenh['inkId'];
	$eenh = $qryeenh['eenheid'];
		$stadrd = str_replace('.00', '', $qryeenh['stdat']);
	$eenheid = $stadrd.$eenh;
} ?>

<td>
<?php
/* KZLMEDICIJN 
Medicijnen met inkId als key. deze inkId is de laagste inkId waarvan nog voorraad is */
$min_inkId_met_vrd2 = mysqli_query($db,$min_inkId_met_vrd) or die (mysqli_error($db));

$name = "kzlArtikel";
//$width=  ;
?>
<select id = "artikel" name="kzlArtikel" width=250 onchange = "eenheid_artikel()" >";
 <option></option>
<?php		while($row = mysqli_fetch_array($min_inkId_met_vrd2))
		{
$vrd = str_replace('.00', '', $row[vrdat]);
$stdrd = str_replace('.00', '', $row[stdat]);
		
$kzlkey="$row[inkId]";
$kzlvalue="$row[naam] &nbsp per $stdrd $row[eenheid] &nbsp ($vrd $row[eenheid])";

include "kzl.php";
		}
// EINDE KZLMEDICIJN
?>
</select></td>
<td>
<?php
// kzlkwaal
$kzl = mysqli_query($db,"
select reduId, reden 
from tblReden r
 join tblRedenuser ru on (r.redId = ru.redId)
where ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and r.actief = 1 and ru.pil = 1
order by reden
") or die (mysqli_error($db));
$name = "kzlkwaal";
$width= 200 ;?>
<select name=<?php echo"$name";?> style="width:<? echo "$width";?>;\" >";
 <option></option>
<?php		while($row = mysqli_fetch_array($kzl))
		{
$kzlkey="$row[reduId]";
$kzlvalue="$row[reden]";

include "kzl.php";
		}
// EINDE kzlkwaal
?>
</select></td> 

<!-- Keuze 2e inenting -->
 <td>
	<input type = "text" name = "txtAantal" size = "1" value = <?php echo "$nutat";?> > 
 </td>
 <td style = "font-size:13px;" ><p  id="aantal" > <?php if(isset($eenheid)) { echo 'x '.$eenheid.' per schaap'; } else { echo 'x'; } ?> </p>
 	
 </td>
<?php If(!empty($knpInsert) || isset($fout)) { ?>
 <td></td>
 <td align = center><input type = 'submit' name ='knpInsert' value ='Toedienen'></td>	<?php }	?>
</tr>
</table>
<hr>
<?php
/*******************************************
**	EINDE GEGEVENS TBV MEDICIJN	**
********************************************/

/*******************************************
**	GEGEVENS TBV SCHAAP ZOEKEN	**
********************************************/?>
<table border = 0>
<tr>
 <td colspan = 4 style = "font-size : 18px;"><b> Keuze uit schapen </b></td>
  <td width = 35 ></td>
 <td colspan="2" align="center"><i><sub></sub></i></td>
 <td></td>
 <td width = 35 ></td>
 <td><i><sub>Opties bij keuze verblijf</sub></i></td>
 <td width = 35 ></td>
 <td><i><sub>Incl. afgevoerde dieren</sub></i></td>
 
 
</tr>
<tr>
 <td align = center><i><sub> alle moeders </sub></i> </td>
 <td><i><sub> Levensnummer </sub></i> </td>
 <td><i><sub> Werknr </sub></i> </td>
 <td><i><sub> Halsnr </sub></i> </td>
 <td width = 35 ></td>
 <td align="center"><i><sub>Geboren vanaf</sub></i></td>
 <td align="center"><i><sub>tot en met</sub></i></td>
 <td width = 35 ></td>
 <td><i><sub> Verblijf</sub></i><i style = "font-size:12px;"><sub> (aantal in verblijf) </sub></i> </td>
 <td><sub><input type = radio name = 'radHok' value = 1 
		<?php if(!isset($_POST['knpToon']) || $_POST['radHok'] == 1) { echo "checked"; } ?> title = "Toont alleen lammeren uit gekozen verblijf"> Lammeren </sub></td>
 <td></td>
 <td><sub><input type = radio name = 'radAfv' value = 0
		<?php if(!isset($_POST['knpToon']) || $_POST['radAfv'] == 0) { echo "checked"; } ?> 
		title = "Alleen dieren van stallijst"> Nee </sub>
	 <sub><input type = radio name = 'radAfv' value = 1
		<?php if((isset($_POST['knpToon']) || isset($_POST['knpVervers'])) && $_POST['radAfv'] == 1) { echo "checked"; } ?> 
		title = "Alleen dieren van stallijst"> Ja </sub></td>
</tr>

<tr>
 <td align = center > <input type = checkbox name = "chbOoi" value = 1 > </td>
 <td>
<?php //kzlLevensnummer

if(isset($_POST['radAfv']) && $_POST['radAfv'] == 1) { 

	
	$histo = '(isnull(afv.datum) or (afv.datum > date_add(curdate(), interval -666 month) )) and '; 
} else { $histo = 'isnull(afv.stalId) and '; }

$zoek_levensnummer = mysqli_query($db,"
select s.schaapId, s.levensnummer 
from tblSchaap s
 join (
	select max(stalId) stalId, schaapId
	from tblStal
	where lidId = ".mysqli_real_escape_string($db,$lidId)."
	group by schaapId
 )st on (st.schaapId = s.schaapId)
 join (
	select max(hisId) hisId, stalId
	from tblHistorie
	where skip = 0
	group by stalId
 ) hm on (hm.stalId = st.stalId)
 join tblHistorie h on (hm.hisId = h.hisId)
 left join (
	select h.datum, h.stalId
	from tblHistorie h
	 join tblActie a on (h.actId = a.actId)
	where skip = 0 and a.af = 1
	group by stalId
 ) afv on (afv.stalId = st.stalId)
where ".mysqli_real_escape_string($db,$histo)." h.skip = 0 and s.levensnummer is not null
order by s.levensnummer"); ?>
 <select name="kzlLevnr"  width=110 >
 <option></option>	
<?php		while($row = mysqli_fetch_array($zoek_levensnummer))
		{
		
			$opties= array($row['schaapId']=>$row['levensnummer']);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if(isset($_POST['kzlLevnr']) && $_POST['kzlLevnr'] == $key)
		{
			$keuze = ' selected ';
		}
				
		echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
			}
		
		}
?> </select>
 </td>
 <td> 
<?php
// Einde kzlLevensnummer
// kzlWerknr

$zoek_werknummer = mysqli_query($db,"
select s.schaapId, right(s.levensnummer,$Karwerk) werknr 
from tblSchaap s
 join (
	select max(stalId) stalId, schaapId
	from tblStal
	where lidId = ".mysqli_real_escape_string($db,$lidId)."
	group by schaapId
 )st on (st.schaapId = s.schaapId)
 join (
	select max(hisId) hisId, stalId
	from tblHistorie
	where skip = 0
	group by stalId
 ) hm on (hm.stalId = st.stalId)
 join tblHistorie h on (hm.hisId = h.hisId)
 left join (
	select h.datum, h.stalId
	from tblHistorie h
	 join tblActie a on (h.actId = a.actId)
	where skip = 0 and a.af = 1
	group by stalId
 ) afv on (afv.stalId = st.stalId)
where ".mysqli_real_escape_string($db,$histo)." h.skip = 0 and s.levensnummer is not null
group by s.schaapId, right(s.levensnummer,$Karwerk)
order by right(s.levensnummer,$Karwerk)
") or die (mysqli_error($db)); 
$name = "kzlWerknr";
$width= 25+(8*$Karwerk) ;
?>
<select name=<?php echo"$name";?> style= "width:<? echo "$width";?>;\" >";
 <option></option>
<?php		while($row = mysqli_fetch_array($zoek_werknummer))
		{
$kzlkey="$row[schaapId]";
$kzlvalue="$row[werknr]";

include "kzl.php";
		}?>
</select> 
 </td>
 <td> 
<?php
// Einde kzlWerknr
// kzlHalsnr

$zoek_halsnr = mysqli_query($db,"
SELECT schaapId, concat(kleur,' ',halsnr) halsnr
FROM tblStal
WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(rel_best) and (kleur is not null or halsnr is not null)
ORDER BY concat(kleur,' ',halsnr)
") or die (mysqli_error($db)); 
$name = "kzlHalsnr";
$width= 25+(8*$Karwerk) ;
?>
<select name=<?php echo"$name";?> style= "width:<? echo "$width";?>;\" >";
 <option></option>
<?php		while($row = mysqli_fetch_array($zoek_halsnr))
		{
$kzlkey = "$row[schaapId]";
$kzlvalue = "$row[halsnr]";

include "kzl.php";
/* Einde kzlHalsnr*/ } ?>
</select> 
 </td>
 <td width = 35 ></td>
 <td><input id = "datepicker2" type= text name = "txtGeb_van" size = "8" value = <?php if(isset($Geb_van)) { echo "$Geb_van"; } ?> ></td>
 <td><input id = "datepicker3" type= text name = "txtGeb_tot" size = "8" value = <?php if(isset($Geb_tot)) { echo "$Geb_tot"; } ?> ></td>
 <td width = 35 ></td>
 <td>
<?php
//Verblijf zoeken
$kzl = mysqli_query($db,"
Select b.hokId, hk.hoknr, count(b.bezId) nu
From tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblHok hk on (hk.hokId = b.hokId)
 left join (
	select b.bezId, h1.hisId hisv, min(h2.hisId) hist
	from tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h1.actId != 2
	group by b.bezId, h1.hisId
 ) uit on (b.bezId = uit.bezId)
 left join (
	select st.schaapId, h.datum
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 3
 ) prnt on (prnt.schaapId = st.schaapId)
Where hk.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(uit.bezId)
Group by b.hokId, hk.hoknr
order by hk.hoknr
") or die (mysqli_error($db));
$name = "kzlHok";
$width= 100 ;
?>
<select name=<?php echo"$name";?> style="width:<? echo "$width";?>;\" >";
 <option></option>
<?php		while($row = mysqli_fetch_array($kzl))
		{
$kzlkey="$row[hokId]";
$kzlvalue="$row[hoknr] &nbsp ($row[nu])";

include "kzl.php";
		}
// EINDE Verblijf zoeken
?>
</select>
 </td>
 <td><sub><input type = radio name = 'radHok' value = 2
		<?php if(isset($_POST['knpToon']) && $_POST['radHok'] == 2) { echo "checked"; } ?> title = "Toont alleen moederdieren van de lammeren uit gekozen verblijf"> Volwassen dieren </sub></td>
 <td></td>
 <td><input type = "submit" name="knpVervers" style = "font-size : 10px;" value = "Ververs"></td>
</tr>
<tr>
 <td colspan = 9 align = center><input type = "submit" name="knpToon" value = "toon"></td>
 <td><sub><input type = radio name = 'radHok' value = 3
		<?php if(isset($_POST['knpToon']) && $_POST['radHok'] == 3) { echo "checked"; } ?> title = "Toont zowel lammeren als hun moederdieren uit gekozen verblijf"> Beiden </sub></td>
</tr>
</table>
<!--	**************************************************
	**	EINDE GEGEVENS TBV SCHAAP ZOEKEN	**
	************************************************** -->


<!--	********************************************
	**	MEDICIJNREGISTRATIE TONEN	**
	********************************************-->
<?php // Ophalen en tonen van dieren o.b.v. ingevulde keuzelijst(en)
if (!empty($_POST['kzlArtikel']) && (!empty($_POST['kzlLevnr']) || !empty($_POST['kzlWerknr']) || !empty($_POST['kzlHalsnr']) || !empty($_POST['chbOoi']) || !empty($_POST['kzlHok']) || !empty($_POST['txtGeb_van'])	)) {

if (!empty($_POST['kzlLevnr']))
{	$filter = "schaapId = '$_POST[kzlLevnr]' ";	}


if (!empty($_POST['kzlWerknr']) && !isset($filter))
{	$filter = "schaapId = '$_POST[kzlWerknr]' ";	}
else if (!empty($_POST['kzlWerknr']) && isset($filter))
{	$filter = $filter. " and schaapId = '$_POST[kzlWerknr]' ";	}

if (!empty($_POST['kzlHalsnr']) && !isset($filter))
{	$filter = "schaapId = '$_POST[kzlHalsnr]' ";	}
else if (!empty($_POST['kzlHalsnr']) && isset($filter))
{	$filter = $filter. " and schaapId = '$_POST[kzlHalsnr]' ";	}


if (!empty($_POST['chbOoi']) && !isset($filter))
{	$filter = "geslacht = 'ooi' and aanw is not null";	}
else if (!empty($_POST['chbOoi']) && isset($filter))
{	$filter = $filter. " and geslacht = 'ooi' and aanw is not null";	}


// Als hok is gekozen is ook een keuze lam, moeders of allebei gemaakt. Vandaar opslitsing in variable $filt_hok. 
	 if (!empty($_POST['kzlHok']) && $_POST['radHok'] == 1) { $filt_hok = "hokId = '$_POST[kzlHok]' and generatie = 'lam' "; } 
else if (!empty($_POST['kzlHok']) && $_POST['radHok'] == 2) { $filt_hok = "hokId = '$_POST[kzlHok]' and generatie = 'ouder' "; } 
else if (!empty($_POST['kzlHok']) && $_POST['radHok'] == 3) { $filt_hok = "hokId = '$_POST[kzlHok]' "; }
/*else { $filt_hok = "fase = 'lam' "; }*/
		
	 if (isset($filt_hok) && !isset($filter)) {	$filter = $filt_hok; }
else if (isset($filt_hok) &&  isset($filter)) {	$filter = $filt_hok. " and ".$filter;	$filt_mdr = $filter;	} //$filt_mdr alleen bij keuzes niet betrekking op verblijf 

if(!empty($_POST['txtGeb_van'])) { 
	$Geb_van = $_POST['txtGeb_van']; $vanGeb = date_format(date_create($Geb_van), 'Y-m-d');	
	 if(!empty($_POST['txtGeb_tot'])) { $Geb_tot = $_POST['txtGeb_tot']; } else { $Geb_tot = date('d-m-Y'); }
	 $totGeb = date_format(date_create($Geb_tot), 'Y-m-d');

	 if(isset($filter)) { $filter = $filter." and dmgeb >= '".$vanGeb."' and dmgeb <= '".$totGeb."'"; }
	 else { $filter = " dmgeb >= '".$vanGeb."' and dmgeb <= '".$totGeb."'"; }
}


$filter; /*echo '$filter = '.$filter.'<br>'*/;

if(isset($filt_mdr)) { /*$where_mdr = $filt_mdr;*/ }
if(isset($where_mdr)) { /*echo '$where_mdr = '.$where_mdr.'<br>';*/
// Geneste query t.b.v. het hok (aglias b) is nodig. Bij zoeken op hok moet $filter op betreffende lammeren filteren. Zonder $reshok worden alle schapen getoond en is $levnr_mdr dus niet leeg !!
$zoek_aanwezig_moeder = mysqli_query($db,"
select s.levensnummer
from tblSchaap s
 join (
	select max(h.hisId) hisId, st.schaapId
	from tblHistorie h
	 join tblStal st on (h.stalId = st.stalId)
	where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(st.rel_best) and h.skip = 0
	group by st.stalId
 ) hm on (hm.schaapId = s.schaapId)
 join tblHistorie h on (hm.hisId = h.hisId)
 join (
	select st.schaapId, h.datum
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId)
 left join tblBezet b on (h.hisId = b.hisId)
 
where s.geslacht = 'ooi' and $where_mdr
") or die (mysqli_error($db));
	while($kkop = mysqli_fetch_assoc($zoek_aanwezig_moeder))
	{
	$levnr_mdr = $kkop['levensnummer'];
	}
}
?>
<script type="text/javascript">
function toggle(source) {
  checkboxes = document.getElementsByName("chbKeuze[]");
  for(var i=0, n=checkboxes.length;i<n;i++) {
    checkboxes[i].checked = source.checked;
  }
}
</script>
<table border = 0>
<tr height = 30><td></td></tr>
<tr style = "font-size:12px;">
 <th width = 0 height = 30></th>
 <th style = "text-align:center;" width = 80 height = 30 ><input type="checkbox" onClick="toggle(this)" /> </th>

 <th style = "text-align:center;"valign= bottom width= 80>Levensnummer<hr></th>

 <th style = "text-align:center;"valign= bottom width= 80>Geboorte datum<hr></th>

 <th style = "text-align:center;"valign="bottom"; >Generatie<hr></th>
 <th width = 1></th>
<?php if(!empty($_POST['chbOoi']) || isset($levnr_mdr)) { $veld = 'Laatst geboren lam'; } else { $veld = 'Verblijf'; } ?>
 <th style = "text-align:center;"valign="bottom"; ><?php echo $veld; ?><hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom"; width= 140 > Historie <hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 200 ></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 60></th>
 <th width = 60></th>

 <th style = "text-align:center;"valign="bottom";width= 80></th>
 <th width = 600></th>

</tr> 

<?php 	
$zoek_schaapgegevens = mysqli_query($db,"
SELECT schaapId, levensnummer, werknr, dmgeb, gebdm, geslacht, aanw, hoknr, lstgeblam, generatie, af
FROM (
	select s.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk) werknr, hg.datum dmgeb, date_format(hg.datum,'%d-%m-%Y') gebdm, s.geslacht, prnt.schaapId aanw, b.hokId, b.hoknr, NULL lstgeblam, 'lam' generatie, a.af
	from tblSchaap s
	 join (
		select max(stalId) stalId, schaapId
		from tblStal
		where lidId = ".mysqli_real_escape_string($db,$lidId)."
		group by schaapId
	 ) stm on (stm.schaapId = s.schaapId)
	 join (
		select max(h.hisId) hisId, h.stalId
		from tblHistorie h
		 join tblStal st on (h.stalId = st.stalId)
		where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and h.skip = 0
		group by stalId
	 ) hm on (hm.stalId = stm.stalId)
	 join tblHistorie h on (hm.hisId = h.hisId)
	 join tblActie a on (h.actId = a.actId)
	 
	 left join (
		select st.schaapId, datum
		from tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		where h.actId = 1 and h.skip = 0
	 ) hg on (hg.schaapId = s.schaapId)

	 left join (
		select st.schaapId
		from tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		where h.actId = 3 and h.skip = 0
	 ) prnt on (prnt.schaapId = s.schaapId)

	 left join (
		select h.datum, h.stalId
		from tblHistorie h
		 join tblActie a on (h.actId = a.actId)
		where skip = 0 and a.af = 1
		group by stalId
	 ) afv on (afv.stalId = stm.stalId)
	 
	 left join (
		select st.schaapId, hk.hokId, hk.hoknr
		from tblBezet b
		 join tblHok hk on (hk.hokId = b.hokId)
		 join tblHistorie h on (b.hisId = h.hisId)
		 join tblStal st on (h.stalId = st.stalId)
		 left join (
			select h1.stalId, h1.hisId hisv, min(h2.hisId) hist
			from tblHistorie h1
			 join tblActie a1 on (a1.actId = h1.actId)
			 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
			 join tblActie a2 on (a2.actId = h2.actId)
			 join tblStal st on (h1.stalId = st.stalId)
			where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
			group by h1.stalId, h1.hisId
		 ) tot on (b.hisId = tot.hisv)
		where hk.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(tot.hist) and h.skip = 0
	 ) b on (s.schaapId = b.schaapId)
	 
	 left join (
		select mdr.schaapId, max(h.datum) lstgeblam
		from tblSchaap mdr
		 join tblVolwas v on (mdr.schaapId = v.mdrId)
		 join tblSchaap lam on (v.volwId = lam.volwId)
		 join tblStal st on (lam.schaapId = st.schaapId)
		 join tblHistorie h on (st.stalId = h.stalId and h.actId = 1)
		where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and h.skip = 0
		group by mdr.schaapId
	 ) lstlam on (lstlam.schaapId = s.schaapId)
	 
	where ".mysqli_real_escape_string($db,$histo)." h.skip = 0 and isnull(prnt.schaapId)

	Union

	SELECT s.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk) werknr, hg.datum dmgeb, date_format(hg.datum,'%d-%m-%Y') gebdm, s.geslacht, prnt.schaapId aanw, b.hokId, b.hoknr, date_format(lstlam.lstgeblam,'%d-%m-%Y') lstgeblam, 'ouder' generatie, a.af
	FROM tblSchaap s
	 join (
		select max(stalId) stalId, schaapId
		from tblStal
		where lidId = ".mysqli_real_escape_string($db,$lidId)."
		group by schaapId
	 )stm on (stm.schaapId = s.schaapId)
	 join (
		select max(h.hisId) hisId, h.stalId
		from tblHistorie h
		 join tblStal st on (h.stalId = st.stalId)
		where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and h.skip = 0
		group by stalId
	 ) hm on (hm.stalId = stm.stalId)
	 join tblHistorie h on (hm.hisId = h.hisId)
	 join tblActie a on (h.actId = a.actId)
	 
	 left join (
		select st.schaapId, datum
		from tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		where h.actId = 1 and h.skip = 0
	 ) hg on (hg.schaapId = s.schaapId)

	 join (
		select st.schaapId
		from tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		where h.actId = 3 and h.skip = 0
	 ) prnt on (prnt.schaapId = s.schaapId)
	 
	 left join (
		select h.datum, h.stalId
		from tblHistorie h
		 join tblActie a on (h.actId = a.actId)
		where skip = 0 and a.af = 1
		group by stalId
	 ) afv on (afv.stalId = stm.stalId)

	 left join (
		select st.schaapId, hk.hokId, hk.hoknr
		from tblBezet b
		 join tblHok hk on (hk.hokId = b.hokId)
		 join tblHistorie h on (b.hisId = h.hisId)
		 join tblStal st on (h.stalId = st.stalId)
		 left join (
			select h1.stalId, h1.hisId hisv, min(h2.hisId) hist
			from tblHistorie h1
			 join tblActie a1 on (a1.actId = h1.actId)
			 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
			 join tblActie a2 on (a2.actId = h2.actId)
			 join tblStal st on (h1.stalId = st.stalId)
			where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
			group by h1.stalId, h1.hisId
		 ) tot on (b.hisId = tot.hisv)
		where hk.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(tot.hist) and h.skip = 0
	 ) b on (s.schaapId = b.schaapId)
	 
	 left join (
		select mdr.schaapId, max(h.datum) lstgeblam
		from tblSchaap mdr
		 join tblVolwas v on (mdr.schaapId = v.mdrId)
		 join tblSchaap lam on (v.volwId = lam.volwId)
		 join tblStal st on (lam.schaapId = st.schaapId)
		 join tblHistorie h on (st.stalId = h.stalId and h.actId = 1)
		where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and h.skip = 0
		group by mdr.schaapId
	 ) lstlam on (lstlam.schaapId = s.schaapId)
	 
	where ".mysqli_real_escape_string($db,$histo)." h.skip = 0
) geg

WHERE $filter
ORDER BY generatie, werknr, lstgeblam desc
	") or die (mysqli_error($db));	
	while($row = mysqli_fetch_assoc($zoek_schaapgegevens))
	{
	$schaapId = $row['schaapId']; // nodig bij doorklikken naar historie medicatie
	$levnr = $row['levensnummer'];
	$werknr = $row['werknr'];
	$gebdm = $row['gebdm'];
	$geslacht = $row['geslacht'];
	$aanw = $row['aanw']; if(isset($aanw)) { if($geslacht == 'ooi') {$fase = 'moederdier'; } else if($geslacht == 'ram') { $fase = 'vaderdier';} } 
					else { $fase = 'lam';}
	$hoknr = $row['hoknr'];
	$lstdm = $row['lstgeblam'];
	$afvoer = $row['af'];
	//if ($row['ent1'] <> 1 && $row['ent2'] <> 1 && !empty($levnrv)) {$mediic = "Ja";} else {$mediic = "Nee";}; 
if(!isset($schaapId)) { $fout = "Er zijn geen resultaten gevonden"; } 
else { ?>
<tr align = center>
 <td width = 0> </td>
 <td width = 90> <input type = checkbox name = "chbKeuze[]" value = <?php echo $levnr; ?> >
 </td>	   
 <td width = 100 style = "font-size:15px;"> <?php echo $levnr; ?> <br> </td>
 <td width = 100 style = "font-size:15px;"> <?php if(isset($gebdm)) { echo $gebdm; } ?> <br> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $fase; ?> <br> </td>
 <td width = 1> </td>	
 <td width = 100 style = "font-size:15px;"> <?php if(isset($lstdm) && $afvoer == 0) { echo $lstdm; } else { echo $hoknr; } ?> <br> </td>
 <td width = 1> </td>	 
 <td width = 100 style = "font-size:12px;"> <?php 

 // Zoeken naar historie medicijnen per schaap 
$zoek_medicijn = mysqli_query($db,"
select Count(s.levensnummer) aant
from tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
 join tblNuttig n on (h.hisId = n.hisId)
 join tblInkoop i on (n.inkId = i.inkId)
 join tblArtikel a on (i.artId = a.artId)
where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and h.skip = 0 and s.schaapId = ".mysqli_real_escape_string($db,$schaapId)." and a.soort = 'pil'
group by s.levensnummer
") or die (mysqli_error($db));
	while($med = mysqli_fetch_assoc($zoek_medicijn))
			{ $medic = $med['aant'];

	   If( !empty($medic) ) { ?>
	<a href='<?php echo $url; ?>MedOverzSchaap.php?pstId=<?php echo $schaapId; ?>' style = "color : blue">
			historie
			</a>
<?php	 }   
	   }// Zoeken naar medicijnen per schaap 
	   
	   ?> <br> </td>
</tr> 
<?php }

	} ?>
</table>
<?php
		
	 

} // EINDE Ophalen en tonen van dieren o.b.v. ingevulde keuzelijst(en)  ?>
<!--	**************************************************
	**	EINDE MEDICIJNREGISTRATIE TONEN	**
	***************************************************-->
	
		
		
</form>	
	</TD>

<?php } else { ?> <img src='med_registratie_php.jpg'  width='970' height='550'/> <?php }
Include "menu1.php"; }
?>
</body>
</html>


 