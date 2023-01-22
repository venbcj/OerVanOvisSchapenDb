<?php /* 8-8-2014 Aantal karakters werknr variabel gemaakt en quotes bij "$kg" weggehaald 
23-11-2014 : functie header() toegevoegd. In de header wordt het vervevrsen van de pagina verstuurd (request =. response) naar de server
8-3-2015 : Login toegevoegd 
18-11-2015 Aanwas gewijzigd naar Aanvoer
21-12-2015 : maak_request_func.php ge-include i.p.v. in maak_request.php */
$versie = '28-10-2016'; /* : release 2 */
$versie = '9-11-2016'; /* : Controle moederdier aangepast */
$versie = '11-11-2016'; /* : Controle of dier elders nog op stal staat verwijderd. Dit werkt ave rechts op het programma. Alleen i.v.m. andere gebruikers heeft dit een blokkerende werking. */
$versie = '20-1-2017'; /* : $hok_uitgez = 'Gespeend' gewijzigd in $hok_uitgez = 2. */
$versie = '1-2-2017'; /* : Halsnummer toegevoegd  */
$versie = '28-2-2017'; /* Ras en gewicht niet veplicht gemaakt		4-4-2017 : kleuren halsnummer uitgebreid */
$versie = '17-2-2018'; /* moederdier verwijderd */
$versie = '20-3-2018';  /* Meerdere pagina's gemaakt 12-5-2018 : if(isset($data)) toegevoegd. Als alle records zijn verwerkt bestaat $data nl. niet meer !! */
$versie = '22-6-2018';  /* Velden in impReader aangepast */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '20-1-2019'; /* alles aan- en uitzetten met javascript */
$versie = '7-3-2019'; /* gewicht gedeeld door 100 ipv 10 */
$versie = '24-4-2020'; /* url Javascript libary aangepast */
$versie = '24-6-2020'; /* onderscheid gemaakt tussen reader Agrident en Biocontrol */
$versie = '4-7-2020'; /* 1 tabel impAgrident gemaakt */
$versie = '28-2-2020'; /* $fase gebaseerd om omschrijving geslacht */
$versie = '26-11-2022'; /* geboortedatum toegevoegd en sql beveiligd met enkele quotes */

 session_start(); ?>
<html>
<head>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Registratie</title>
</head>
<body>

<center>
<?php
$titel = 'Inlezen Aanvoer';
$subtitel = '';
Include "header.php"; ?>
	<TD width = 960 height = 400 valign = "top">
<?php 
$file = "InsAanvoer.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {

 
include "vw_kzlOoien.php";
if ($modmeld == 1 ) { include "maak_request_func.php"; }

If (isset($_POST['knpInsert_']))  {
	//Include "url.php";
	Include "post_readerAanv.php"; #Deze include moet voor de vervversing in de functie header()
	//header("Location: ".$url."InsAanvoer.php"); 
	}

function numeriek($subject) {
	if (preg_match('/([[a-zA-Z])/', $subject, $matches)) {  /*var_dump($matches[1]); */ return 1; }
}

// Aantal nog in te lezen AANVOER
/*$aanvoer = mysqli_query($db,"SELECT count(*) aant 
							FROM impReader 
							WHERE lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and teller_aanv is not NULL and isnull(verwerkt) ") or die (mysqli_error($db));
	$row = mysqli_fetch_assoc($aanvoer);
		$aantaanw = $row['aant'];*/
// EINDE Aantal nog in te lezen AANVOER

if($reader == 'Agrident') {
$velden = "rd.actId, rd.Id readId, rd.datum, rd.levensnummer levnr_rd, rd.ubn ubn_aanv, rd.rasId ras_rd, rd.geslacht, NULL moeder, rd.hokId hok_rd, rd.gewicht, rd.datumdier geb_datum, 
s.levensnummer levnr_db, p.ubn ubn_db, r.rasId ras_db, m.schaapId mdrId, m.levensnummer mdr_db, ho.hokId hok_db, dup.dubbelen ";

$tabel = "
impAgrident rd
 left join (
	SELECT max(h.hisId) hisId, s.schaapId, s.levensnummer, s.geslacht
	FROM tblSchaap s
	 join tblStal st on (st.schaapId = s.schaapId)
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE st.lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and h.skip = 0
	GROUP BY s.schaapId, s.levensnummer, s.geslacht
 ) s on (rd.levensnummer = s.levensnummer)
 left join tblPartij p on (rd.ubn = p.ubn and p.lidId = '" . mysqli_real_escape_string($db,$lidId) . "')
 left join (
	SELECT ru.lidId, r.rasId
	FROM tblRas r
	 join tblRasuser ru on (r.rasId = ru.rasId)
	WHERE r.actief = 1 and ru.actief = 1
 ) r on (rd.rasId = r.rasId and r.lidId = rd.lidId)
 left join (".$vw_kzlOoien.") m on (rd.ubn = m.levensnummer)
 left join (
	SELECT ho.hokId
	FROM tblHok ho
	WHERE ho.lidId = '" . mysqli_real_escape_string($db,$lidId) . "'
 ) ho on (rd.hokId = ho.hokId)
 left join (
 	SELECT rd.Id, count(dup.Id) dubbelen
	FROM impAgrident rd
	 join impAgrident dup on (rd.lidId = dup.lidId and rd.levensnummer = dup.levensnummer and rd.Id <> dup.Id and rd.actId = dup.actId and isnull(dup.verwerkt))
	WHERE rd.actId = 2 or rd.actId = 3
	GROUP BY rd.Id
 ) dup on (rd.Id = dup.Id)
";

$WHERE = "WHERE rd.lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and (rd.actId = 2 or rd.actId = 3) and isnull(rd.verwerkt)";

include "paginas.php";

$data = $page_nums->fetch_data($velden, "ORDER BY rd.datum, rd.Id");
}
else {
$velden = "rd.readId, rd.datum, rd.levnr_aanv levnr_rd, rd.ubn_aanv, lower(rd.rascode) ras_rd, lower(rd.geslacht) geslacht, rd.moeder, lower(rd.hokcode) hok_rd, rd.gewicht/100 gewicht,
s.levensnummer levnr_db, l.ubn ubn_db, r.scan ras_db, m.schaapId mdrId, m.levensnummer mdr_db, ho.scan hok_db ";

$tabel = "
impReader rd
 left join (
	SELECT max(h.hisId) hisId, s.schaapId, s.levensnummer, s.geslacht
	FROM tblSchaap s
	 join tblStal st on (st.schaapId = s.schaapId)
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE st.lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and h.skip = 0
	GROUP BY s.schaapId, s.levensnummer, s.geslacht
 ) s on (rd.levnr_aanv = s.levensnummer)
 left join tblLeden l on (rd.ubn_aanv = l.ubn and l.lidId = '" . mysqli_real_escape_string($db,$lidId) . "')
 left join (
	SELECT ru.lidId, ru.scan
	FROM tblRas r
	 join tblRasuser ru on (r.rasId = ru.rasId)
	WHERE r.actief = 1 and ru.actief = 1
 ) r on (lower(rd.rascode) = r.scan and r.lidId = rd.lidId)
 left join (".$vw_kzlOoien.") m on (rd.moeder = m.levensnummer)
 left join (
	SELECT ho.scan FROM tblHok ho WHERE ho.lidId = '" . mysqli_real_escape_string($db,$lidId) . "'
 ) ho on (rd.hokcode = ho.scan)
";

$WHERE = "WHERE rd.lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and rd.teller_aanv is not null and isnull(rd.verwerkt)";

include "paginas.php";

$data = $page_nums->fetch_data($velden, "ORDER BY str_to_date(rd.datum,'%d/%m/%Y'), rd.readId");
} ?>

<table border = 0>
<tr> <form action="InsAanvoer.php" method = "post">
 <td colspan = 2 style = "font-size : 13px;"> 
  <input type = "submit" name = "knpVervers_" value = "Verversen"></td>
 <td colspan = 2 align = center style = "font-size : 14px;"><?php 
echo $page_numbers; ?></td>
 <td colspan = 3 align = left style = "font-size : 13px;"> Regels Per Pagina: <?php echo $kzlRpp; ?> </td>
 <td colspan = 3 align = 'right'> <input type = "submit" name = "knpInsert_" value = "Inlezen">&nbsp &nbsp </td>
 <td colspan = 3 style = "font-size : 12px;"><b style = "color : red;">!</b> = waarde uit reader niet herkend. <br> 
<?php if($modtech == 1) { ?>* Alleen verplicht bij lammeren. <?php } ?> </td></tr>
<tr valign = bottom style = "font-size : 12px;">
 <th>Inlezen<br><b style = "font-size : 10px;">Ja/Nee</b><br> <input type="checkbox" id="selectall" checked /> <hr></th>
 <th>Verwij-<br>deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
 <th>Aanvoer<br>datum<hr></th>
 <th>Levensnummer<hr></th>
 <th colspan = 2>Halsnummer<hr></th>
 <th>Ras<hr></th>
 <th>Geslacht<hr></th>
 <th>Generatie<hr></th>
<?php if($modtech == 1) { ?>
 <th>Gewicht<hr></th>
 <th>Geboren<hr></th>
	<?php if(isset($verwijderd_sinds_17_2_2018)) { ?>
 <th>Moederdier<hr></th> <?php } ?>
 <th>Verblijf*<hr></th>
<?php } ?>
 <th>Herkomst<hr></th>
 <th><hr></th>

</tr>

<?php
// Declaratie ras
$qryRassen = ("
SELECT r.rasId, r.ras, lower(coalesce(isnull(ru.scan),'6karakters')) scan
FROM tblRas r
 join tblRasuser ru on (r.rasId = ru.rasId)
WHERE ru.lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and r.actief = 1 and ru.actief = 1
ORDER BY ras
"); 
$RAS = mysqli_query($db,$qryRassen) or die (mysqli_error($db)); 

$index = 0; 
while ($ras = mysqli_fetch_array($RAS)) 
{
   $rasId[$index] = $ras['rasId']; 
   $rasnm[$index] = $ras['ras'];
   $rasRaak[$index] = $ras['scan'];   if($reader == 'Agrident') { $rasRaak[$index] = $ras['rasId']; }
   $index++; 
}
unset($index);

//dan het volgende:
$count = count($rasId); 
/*
echo "<select name=\"kzlras_Id\">"; 
for ($i = 0; $i <= $count; $i++) 
{ 
    echo "<option value=\"$rasId[$i]\">$rasnm[$i]</option>"; 
} 
echo "</select>";*/
// EINDE Declaratie ras
if($modtech == 1) {
// Declaratie MOEDERDIER
$qryMoeder = ("SELECT ko.schaapId, right(ko.levensnummer,$Karwerk) Werknr, ko.lamrn
			FROM (".$vw_kzlOoien.") ko ORDER BY right(ko.levensnummer,$Karwerk) "); 
$moederdier = mysqli_query($db,$qryMoeder) or die (mysqli_error($db));

$index = 0; 
while ($mdr = mysqli_fetch_assoc($moederdier)) 
{ 
   $mdrId[$index] = $mdr['schaapId'];
   $wnrOoi[$index] = $mdr['Werknr'];
   $index++; 
} 
unset($index); 
// EINDE Declaratie MOEDERDIER

// Declaratie HOKNUMMER			// lower(if(isnull(scan),'6karakters',scan)) zorgt ervoor dat $raak nooit leeg is. Anders worden legen velden gevonden in legen velden binnen impReader.
$qryHoknummer = mysqli_query($db,"
SELECT hokId, hoknr, lower(if(isnull(scan),'6karakters',scan)) scan
FROM tblHok
WHERE lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and actief = 1
ORDER BY hoknr
") or die (mysqli_error($db));

$index = 0; 
while ($hknr = mysqli_fetch_assoc($qryHoknummer)) 
{ 
   $hoknId[$index] = $hknr['hokId']; 
   $hoknum[$index] = $hknr['hoknr'];
   $hokRaak[$index] = $hknr['scan'];   if($reader == 'Agrident') { $hokRaak[$index] = $hknr['hokId']; }
   $index++; 
} 
unset($index);
// EINDE Declaratie HOKNUMMER
}

// Declaratie HERKOMST			// lower(if(isnull(ubn),'6karakters',ubn)) zorgt ervoor dat $raak nooit leeg is. Anders worden legen velden gevonden in legen velden binnen impReader.
$qryRelatie = ("SELECT r.relId, '6karakters' ubn, p.naam
			FROM tblPartij p join tblRelatie r on (p.partId = r.partId)	
			WHERE p.lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and relatie = 'cred' and isnull(r.uitval) and p.actief = 1 and r.actief = 1
				  and isnull(p.ubn)
			union
			
			SELECT r.relId, p.ubn, p.naam
			FROM tblPartij p join tblRelatie r on (p.partId = r.partId)	
			WHERE p.lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and relatie = 'cred' and isnull(r.uitval) and p.actief = 1 and r.actief = 1 
				  and ubn is not null
			ORDER BY naam"); 
$relatienr = mysqli_query($db,$qryRelatie) or die (mysqli_error($db)); 

$index = 0; 
while ($rnr = mysqli_fetch_array($relatienr)) 
{ 
   $relnId[$index] = $rnr['relId']; 
   $relnum[$index] = $rnr['naam'];
   $relRaak[$index] = $rnr['ubn'];   
   $index++; 
} 
unset($index);
// EINDE Declaratie HERKOMST

if(isset($data))  {	

//echo count($data);

	foreach($data as $key => $array)
	{
		$var = $array['datum'];
$date = str_replace('/', '-', $var);
//$gebdatum = date('d-m-Y', strtotime($date)-365*60*60*24);
$datum = date('d-m-Y', strtotime($date));
if (!empty($array['uit_vmdm'])) {
		$varuitv = $array['uit_vmdm'];
$date2 = str_replace('/', '-', $varuitv);
$uitvdm = date('d-m-Y', strtotime($date2));
		} else { $uitvdm = '' ; } 
	
	$Id = $array['readId'];
	$levnr_rd = $array['levnr_rd']; //if (strlen($levnr_rd)== 11) {$levnr_rd = '0'.$array['levnr'];}
	$levnr_dupl = $array['dubbelen']; // twee keer in reader bestand
	$levnr_db = $array['levnr_db'];
	$ras_rd = $array['ras_rd'];
	$ras_db = $array['ras_db'];
	$sekse = $array['geslacht'];
	$gewicht = $array['gewicht'];
	$geb_datum = $array['geb_datum']; if(isset($geb_datum)) { $gebdm = date('d-m-Y', strtotime($geb_datum)); }
	$moeder = $array['moeder'];
	$mderId = $array['mdrId'];
	$mdr_db = $array['mdr_db'];
	$hok_rd = $array['hok_rd'];
	$hok_db = $array['hok_db'];
if($reader == 'Agrident') {
	$actId = $array['actId'];
	if($actId == 2) {
		$fase = 'lam';
	}
	else { 
	if($sekse == 'ram') { $fase = 'vader'; }
	else { $fase = 'moeder'; }
	}
}
if($reader == 'Biocontrol') {
		if (empty($hok_rd)) { $sekse = 'ooi'; }
		if (!empty($hok_rd)) { $fase = 'lam'; } else {$fase = 'moeder';}
	}
	$ubn_rd = $array['ubn_aanv'];
	$ubn_db = $array['ubn_db'];

	


// Controleren of ingelezen waardes worden gevonden .
$kzlRas = $ras_db; /*$kzlOoi = $mdr_db;*/ $kzlHok = $hok_db;  
if (isset($_POST['knpVervers_'])) {
$datum = $_POST["txtaanwdm_$Id"]; $hnr = $_POST["txtHnr_$Id"]; $kzlRas = $_POST["kzlras_$Id"]; $sekse = $_POST["kzlsekse_$Id"]; $fase = $_POST["kzlFase_$Id"];
if($modtech == 1) { $gewicht = $_POST["txtkg_$Id"]; /*$kzlOoi = $_POST["kzlooi_$Id"];*/ $kzlHok = $_POST["kzlhok_$Id"]; }
	 }
	 If	 
	 ( /*Aanvoer moeders*/   ( ($fase == 'moeder' || $fase == 'vader') && #generatie moet moeder of vader zijn
						(	($levnr_db > 0)			|| # of levensnummer bestaat al
							isset($levnr_dupl)    	|| # of levensnummer bestaat al in reader bestand
							strlen($levnr_rd)<> 12	|| # of levensnummer is geen 12 karakters lang of dus leeg
							numeriek($levnr_rd) == 1	|| # of levensnummer bevat een letter 
							empty($datum)				|| # of aanvoerdatum is leeg
						    //empty($kzlRas) 		|| # of ras is onbekend of leeg
					($fase == 'moeder' && $sekse =='ram') || #generatie en geslacht is tegenstrijdig
					($fase == 'vader' && $sekse =='ooi') ) #generatie en geslacht is tegenstrijdig
							
					  ) 
	||	
	/*Aanvoer lammeren*/  (  $fase == 'lam' && #generatie moet lam zijn
						(	($levnr_db > 0)		|| # of levensnummer bestaat al
							strlen($levnr_rd)<> 12		|| # of levensnummer is geen 12 karakters lang of dus leeg
							numeriek($levnr_rd) == 1	|| # of levensnummer bevat een letter  
							empty($datum)			|| # of datum is leeg
						    //empty($kzlRas) 		|| # of ras is onbekend of leeg
		  //($modtech == 1 && empty($gewicht))		|| # of gewicht leeg
		  //($modtech == 1 && empty($kzlOoi))		|| # of moeder is onbekend of leeg
		  ($modtech == 1 && empty($kzlHok))			)  # of hoknr is onbekend of leeg
					)
	||
	/*Aanvoer niet moeders of lammeren*/  (   empty($fase) #generatie kan nooit vader of leeg zijn
					)
	) {	$oke = 0;	} else {	$oke = 1;	} // $oke kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.
// EINDE Controleren of ingelezen waardes worden gevonden .  

	 if (isset($_POST['knpVervers_']) && $_POST["laatsteOke_$Id"] == 0 && $oke == 1) /* Als onvolledig is gewijzigd naar volledig juist */ {$cbKies = 1; $cbDel = $_POST["chbDel_$Id"]; }
else if (isset($_POST['knpVervers_'])) { $cbKies = $_POST["chbkies_$Id"];  $cbDel = $_POST["chbDel_$Id"]; } 
   else { $cbKies = $oke; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet ?>

<!--	**************************************
		**	  	 OPMAAK  GEGEVENS			**
		************************************** -->

<tr style = "font-size:14px;">
 <td align = center> 

	<input type = hidden size = 1 name = <?php echo "chbkies_$Id"; ?> value = 0 > <!-- hiddden -->
	<input type = checkbox 		  name = <?php echo "chbkies_$Id"; ?> value = 1 
	  <?php echo $cbKies == 1 ? 'checked' : ''; /* Als voorwaarde goed zijn of checkbox is aangevinkt */

	  if ($oke == 0) /*Als voorwaarde niet klopt */ { ?> disabled <?php } else { ?> class="checkall" <?php } /* class="checkall" zorgt dat alles kan worden uit- of aangevinkt*/ ?> >
	<input type = hidden size = 1 name = <?php echo "laatsteOke_$Id"; ?> value = <?php echo $oke; ?> > <!-- hiddden -->
 </td>
 <td align = center>
	<input type = hidden size = 1 name = <?php echo "chbDel_$Id"; ?> value = 0 >
	<input type = checkbox class="delete" name = <?php echo "chbDel_$Id"; ?> value = 1 <?php if(isset($cbDel)) { echo $cbDel == 1 ? 'checked' : ''; } ?> >
 </td>
 <td>
<?php if (isset($_POST['knpVervers_'])) { $datum = $_POST["txtaanwdm_$Id"]; } ?>
	<input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtaanwdm_$Id"; ?> value = <?php echo $datum; ?> >
 </td>
<?php if ($levnr_db == 0 && strlen($levnr_rd) == 12 && numeriek($levnr_rd) <> 1) { ?> 
 <td>
<?php echo $levnr_rd; } else { ?> <td style = "color : red;" > <?php echo $levnr_rd; } ?>
<!-- <input type = "hidden" name = <p??hp echo " \"txtlevgeb_$Id\" value = \"$levnr_rd\" ;"?> size = 9 style = "font-size : 9px;"> -->
 </td>
 <td>
<!-- HALSKLEUR -->
 <select name= <?php echo "kzlKleur_$Id"; ?> style= "width:63;" > 
<?php
$opties = array('' => '', 'blauw' => 'blauw', 'geel' => 'geel', 'groen' => 'groen', 'oranje' => 'oranje', 'paars' => 'paars', 'rood'=>'rood', 'wit' => 'wit', 'zwart' => 'zwart');
foreach ( $opties as $key => $waarde)
{
   if((!isset($_POST['knpSave']) && $kleur == $key) || (isset($_POST["kzlKleur_$Id"]) && $_POST["kzlKleur_$Id"] == $key) ) {
	echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else {
	echo '<option value="' . $key . '">' . $waarde . '</option>';
  }
} ?>
</select>  </td>
<!-- HALSNR -->
 <td>
	<input type = text name = <?php echo "txtHnr_$Id"; ?> style = "text-align : right" size = 1 value = <?php if(isset($hnr)) { echo $hnr; } ?> > </td>
 <td style = "font-size : 11px;">
<!-- KZLRAS -->
 <select style="width:65;" <?php echo " name=\"kzlras_$Id\" "; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php	$count = count($rasId);	
for ($i = 0; $i < $count; $i++){

	$opties = array($rasId[$i]=>$rasnm[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((!isset($_POST['knpVervers_']) && $ras_rd == $rasRaak[$i]) || (isset($_POST["kzlras_$Id"]) && $_POST["kzlras_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}

 ?> </select>
<?php if( !empty($ras_rd) && empty($ras_db) && !isset($_POST['knpVervers_']) ) {echo $ras_rd; ?> <b style = "color : red;"> ! </b> <?php } ?>
	 <!-- EINDE KZLRAS -->
 </td>
 <td>
<!-- KZLGESLACHT --> 
<select <?php echo " name=\"kzlsekse_$Id\" "; ?> style="width:59; font-size:13px;">

<?php  echo "$row[geslacht]";
$opties = array('' => '', 'ooi' => 'ooi', 'ram' => 'ram');
foreach ( $opties as $key => $waarde)
{
   if((!isset($_POST['knpVervers_']) && $sekse == $key) || (isset($_POST["kzlsekse_$Id"]) && $_POST["kzlsekse_$Id"] == $key) ) {
   echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
 } else {
   echo '<option value"' . $key . '">' . $waarde . '</option>';
   }
}

	?> </select> <!-- EINDE KZLGESLACHT -->
 </td>
 <td style="width:59; font-size:13px;" >
<!-- KZLGENERATIE --> 
<?php //echo "$fase"; ?>
<select <?php echo " name=\"kzlFase_$Id\" "; ?> >

<?php  
$opties = array('' => '', 'lam' => 'lam', 'moeder' => 'moeder', 'vader' => 'vader');
foreach ( $opties as $key => $waarde)
{
   if((!isset($_POST['knpVervers_']) && $fase == $key) || (isset($_POST["kzlFase_$Id"]) && $_POST["kzlFase_$Id"] == $key) ) {
   echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
 } else {
   echo '<option value"' . $key . '">' . $waarde . '</option>';
   }
}

	?> </select> <!-- EINDE KZLGENERATIE -->
 </td>
<?php if($modtech == 1) { ?>
<!-- GEWICHT -->
<?php if(isset($_POST["knpVervers_"])) {	$gewicht = $_POST["txtkg_$Id"];	}?>	
 <td align = center style = "font-size : 11px;"> <input type = "text" name = <?php echo "txtkg_$Id"; ?> size = 1 value = <?php echo $gewicht;?> >
 </td> <!-- EINDE GEWICHT -->
 <!-- GEBOORTE DATUM -->
<?php if(isset($_POST["knpVervers_"])) {	$gebdm = $_POST["txtGebdm_$Id"];	}?>	
 <td> <input type = "text" align = center size = 9 style = "font-size : 11px;" name = <?php echo "txtGebdm_$Id"; ?>  value = <?php echo $gebdm; unset($gebdm); ?> >
 </td> <!-- EINDE GEBOORTE DATUM -->
<?php if(isset($verwijderd_sinds_17_2_2018)) { ?>
 <td style = "font-size : 11px;">
<!-- KZLMOEDER -->
<?php $width = 25+(8*$Karwerk) ; ?>
 <select style= "width:<?php echo $width; ?>;" <?php echo " name=\"kzlooi_$Id\" "; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php	$count = count($wnrOoi);
for ($i = 0; $i < $count; $i++){

	$opties = array($mdrId[$i]=>$wnrOoi[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((!isset($_POST['knpVervers_']) && $mderId == $key) || (isset($_POST["kzlooi_$Id"]) && $_POST["kzlooi_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}
?> </select> 
<?php if( $moeder <> NULL && !isset($mdr_db) && !isset($_POST['knpVervers_']) ) {echo $moeder;  ?> <b style = "color : red;"> ! </b> <?php } unset($mdr_db); ?>
	<!-- EINDE KZLMOEDER --> </td> <?php } ?>

 <td style = "font-size : 9px;">
<!-- KZLHOKNR --> 
 <select style="width:65;" <?php echo " name=\"kzlhok_$Id\" "; ?> value = "" style = "font-size:12px;">
  <option></option>

<?php	$count = count($hoknum);
for ($i = 0; $i < $count; $i++){

	$opties = array($hoknId[$i]=>$hoknum[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((!isset($_POST['knpVervers_']) && $hok_rd == $hokRaak[$i]) || (isset($_POST["kzlhok_$Id"]) && $_POST["kzlhok_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}
?>	</select>
<?php 
if ( !empty($hok_rd) && empty($hok_db) && !isset($_POST['knpVervers_']) ) {echo $hok_rd; ?> <b style = "color : red;"> ! </b>  <?php } ?>
 </td> <!-- EINDE KZLHOKNR -->
<?php } ?>

 <td style = "font-size : 11px;">
<!-- KZLHERKOMST -->
 <select style="width:135;" <?php echo " name=\"kzlherk_$Id\" "; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php	$count = count($relnum);
for ($i = 0; $i < $count; $i++){

	$opties = array($relnId[$i]=>$relnum[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((!isset($_POST['knpVervers_']) && $ubn_rd == $relRaak[$i]) || (isset($_POST["kzlherk_$Id"]) && $_POST["kzlherk_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}
?> </select>
<?php if( isset($ubn_rd) && empty($ubn_db) && !isset($_POST['knpVervers_']) && !isset($bericht) ) {echo $ubn_rd; ?> <b style = "color : red;"> ! </b>  <?php } ?>
 </td> <!-- EINDE KZLHERKOMST -->	

<?php
if ( !empty($levnr_rd) && $levnr_db > 0) 		{ $color = 'red'; $bericht = "Staat al op stallijst."; }
else if (isset($levnr_dupl) ) 					{ $color = 'blue'; $bericht =  "Dubbel in de reader."; }
else if (isset($levnr_rd) && strlen($levnr_rd) <> 12) { $color = 'red'; $bericht = "Levensnummer geen 12 karakters."; }  
else if (numeriek($levnr_rd) == 1) 				{ $color = 'red'; $bericht = "Levensnummer bevat een letter."; } ?>


 <td colspan = 3 style = "color : <?php echo $color; ?> ; font-size : 11px;"> <?php if(isset($bericht)) { echo $bericht; unset($bericht); unset($color); } ?>
<!-- EINDE Als levensnummer uniek is EN 12 karakters lang is EN geen letter bevat --> 
 </td> 
</tr>
<!--	**************************************
	**	EINDE OPMAAK GEGEVENS	**
	************************************** -->

<?php } 
} //einde if(isset($data)) ?>
</table>
</form> 



</TD>
<?php
Include "menu1.php"; } ?>
</tr>

</table>
</center>

</body>
</html>
<SCRIPT language="javascript">
$(function(){

	// add multiple select / deselect functionality
	$("#selectall").click(function () {
		  $('.checkall').attr('checked', this.checked);
	});

	// if all checkbox are selected, check the selectall checkbox
	// and viceversa
	$(".checkall").click(function(){

		if($(".checkall").length == $(".checkall:checked").length) {
			$("#selectall").attr("checked", "checked");
		} else {
			$("#selectall").removeAttr("checked");
		}

	});
});

$(function(){

	// add multiple select / deselect functionality
	$("#selectall_del").click(function () {
		  $('.delete').attr('checked', this.checked);
	});

	// if all checkbox are selected, check the selectall_del checkbox
	// and viceversa
	$(".delete").click(function(){

		if($(".delete").length == $(".delete:checked").length) {
			$("#selectall_del").attr("checked", "checked");
		} else {
			$("#selectall_del").removeAttr("checked");
		}

	});
});
</SCRIPT>