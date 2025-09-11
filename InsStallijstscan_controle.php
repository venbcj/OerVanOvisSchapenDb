<?php


require_once('validation_functions.php');
$versie = '05-08-2023'; /* kopie gemaaky van InsAanvoer 
op 21-8-2023 heeft Rina het volgende verzocht
- Geboren niet verplicht, alleen als er een melding naar RVO moet.
- Geslacht niet verplicht behalve als er voor moeder (ooi) of vader (ram) wordt gekozen
- Generatie verplicht, omdat een lam in een verblijf wordt geplaatst.
- Registratie verplicht als er een melding naar RVO moet.
- Misschien nog het Ras toevoegen? */
$versie = '31-12-2023'; /* and h.skip = 0 toegevoegd bij tblHistorie */
$versie = "11-03-2024"; /* Bij geneste query uit 
join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId) gewijzgd naar
join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
I.v.m. historie van stalId 22623. Dit dier is eerst verkocht en met terugwerkende kracht geplaatst in verblijf Afmest 1 */
$versie = "13-12-2024"; /* Niet gescande dieren onderaan gezet en link naar deze dieren toegevoegd. Ook controle en foutmeldingen samengevoegd, zie onjuist */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */
$versie = '15-07-2025'; /* Veld/keuzelijst ubn toegevoegd */

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Registratie</title>
</head>
<body>

<?php
$titel = 'Inlezen Stallijstcontrole';
$file = "InsStallijstscan_controle.php";
include "login.php"; ?>

			<TD valign = "top">
<?php
if (is_logged_in()) {

if ($modmeld == 1 ) { include "maak_request_func.php"; }

If (isset($_POST['knpInsert_']))  {
	include "post_readerStalscan.php";
	}

$velden = "rd.actId, rd.Id readId, rd.datum, rd.levensnummer levnr_rd, coalesce(r.ras,'onbekend') ras, stal.lidId, stal.ubn, stal.geslacht,  
 ouder.ouder,
 af.actie,
 hk.hoknr,
stal.levensnummer levnr_stal, hg.gebdm, dup.dubbelen ";

$tabel = "
impAgrident rd
 left join (
	SELECT max(h.hisId) hisId, st.stalId, u.ubn, s.schaapId, s.levensnummer, s.geslacht, s.rasId, st.lidId
	FROM tblSchaap s
	 join tblStal st on (st.schaapId = s.schaapId)
	 join tblUbn u on (st.ubnId = u.ubnId)
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE st.lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and h.skip = 0
	GROUP BY st.stalId, s.schaapId, s.levensnummer, s.geslacht, s.rasId, st.lidId
 ) stal on (rd.levensnummer = stal.levensnummer)
 left join tblRas r on (s.rasId = r.rasId)
 left join (
 	SELECT schaapId ouder
 	FROM tblStal st
 	 join tblHistorie h on (h.stalId = st.stalId)
 	WHERE actId = 3 and h.skip = 0
 ) ouder on (ouder.ouder = s.schaapId)
 left join (
 	SELECT st.stalId, actie
	FROM tblActie a
	 join tblHistorie h on (a.actId = h.actId)
	 join tblStal st on (st.stalId = h.stalId)
	WHERE a.af = 1 and st.lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and h.skip = 0
 ) af on (af.stalId = s.stalId)

 left join (
 		SELECT lsthk.hisId actueel_hisId_hok, lsthk.stalId
		FROM ( 
			 	SELECT max(h.hisId) hisId, h.stalId
			 	FROM tblBezet b
			 	 join tblHistorie h on (h.hisId = b.hisId)
			 	 join tblStal st on (h.stalId = st.stalId)
			 	WHERE st.lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and isnull(st.rel_best) and h.skip = 0
			 	GROUP BY stalId
			 ) lsthk
			  left join 
			 (
				SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
				FROM tblBezet b
				 join tblHistorie h1 on (b.hisId = h1.hisId)
				 join tblActie a1 on (a1.actId = h1.actId)
				 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
				 join tblActie a2 on (a2.actId = h2.actId)
				 join tblStal st on (h1.stalId = st.stalId)
				WHERE st.lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
				GROUP BY b.bezId, st.schaapId, h1.hisId
			 ) uit on (lsthk.hisId = uit.hisv)
			 left join tblBezet b on (b.hisId = lsthk.hisId)
				WHERE lsthk.hisId is not null and isnull(hist)

	) act_b on (act_b.stalId = s.stalId)
 left join tblBezet b on (act_b.actueel_hisId_hok = b.hisId)
 left join tblHok hk on (hk.hokId = b.hokId)

 left join (
	SELECT date_format(h.datum,'%d-%m-%Y') gebdm, schaapId
	FROM tblHistorie h
	 join tblStal st on (st.stalId = h.stalId)
	WHERE h.actId = 1 and h.skip = 0
 ) hg on (s.schaapId = hg.schaapId)
 left join (
 	SELECT rd.Id, count(dup.Id) dubbelen
	FROM impAgrident rd
	 join impAgrident dup on (rd.lidId = dup.lidId and rd.levensnummer = dup.levensnummer and rd.Id <> dup.Id and rd.actId = dup.actId and isnull(dup.verwerkt))
	WHERE rd.actId = 22
	GROUP BY rd.Id
 ) dup on (rd.Id = dup.Id)
";

$WHERE = "WHERE rd.lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and rd.actId = 22 and isnull(rd.verwerkt)";

include "paginas.php";

$data = $page_nums->fetch_data($velden, "ORDER BY s.lidId asc, dup.dubbelen desc, actId desc, rd.datum, rd.Id"); ?>

<form action="InsStallijstscan_controle.php" method = "post">


<?php
$aantal_niet_op_stallijst = mysqli_query($db,"
SELECT count(Id) aant
FROM impAgrident rd
 left join (
 	SELECT s.schaapId, levensnummer
 	FROM tblSchaap s
   join tblStal st on (s.schaapId = st.schaapId)
	WHERE st.lidId = '" . mysqli_real_escape_string($db,$lidId) . "'
 ) s on (s.levensnummer = rd.levensnummer)
WHERE rd.lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and rd.actId = 22 and isnull(rd.verwerkt) and isnull(s.schaapId)
") or die (mysqli_error($db));

$index = 0; 
while ($anos = mysqli_fetch_assoc($aantal_niet_op_stallijst)) { $nieuw = $anos['aant']; } ?>



<table border = 0> 
<tr> 
 <td colspan = 2 style = "font-size : 13px;"> 
  <input type = "submit" name = "knpVervers_" value = "Verversen"></td>
 <td colspan = 2 align = "center" style = "font-size : 14px;"><?php 
echo $page_numbers; ?></td>
 <td colspan = 3 align = left style = "font-size : 13px;"> Regels Per Pagina: <?php echo $kzlRpp; ?> </td>
 <td align = 'right'> <input type = "submit" name = "knpInsert_" value = "Inlezen">&nbsp &nbsp </td>
 <td colspan= 2 style = "font-size : 12px;"><b style = "color : red;">!</b> = waarde uit reader niet herkend. <br><br> * Melding RVO maken Ja/Nee
 </td>
 <td></td>
 <td> <a href="#NietGescand" style="font-size : 12px; color:blue" > Niet gescande schapen</a> </td>
</tr>
<tr style = "font-size : 12px;">
 <th valign = bottom >Inlezen<br><b style = "font-size : 10px;">Ja/Nee</b><br> <input type="checkbox" id="selectall" checked /> <hr></th>
 <th valign = bottom >Verwij-<br>deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
 <th valign = bottom >Scan<br>datum<hr></th>
 <th valign = bottom >Ubn<hr></th>
 <th valign = bottom >Levensnummer<hr></th>

 <th valign = bottom >Geboren<hr></th>
 <th valign = bottom >Geslacht<hr></th>
 <th valign = bottom >Generatie<hr></th>
  <th valign = bottom >Ras<hr></th>
 <?php if($modtech == 1) { ?>
  <th valign = bottom >Verblijf<hr></th>
 <?php } if($nieuw > 0) { ?>
 <th valign = bottom >Registratie<hr></th>
 <th width="75" valign = bottom style = "font-size : 11px;">RVO*<hr></th>
<?php } ?>
 <td valign = "center" align="center" >
 		<a href="exportStallijstScanControle.php?pst=<?php echo $lidId; ?>'"> Export-xlsx </a>

 </td>
</tr>

<?php
// Declaratie ubn
$declaratie_kzlUbn = mysqli_query($db,"
SELECT ubnId, ubn
FROM tblUbn
WHERE lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and actief = 1
ORDER BY ubn
") or die (mysqli_error($db));  

$index = 0; 
while ($du = mysqli_fetch_array($declaratie_kzlUbn)) 
{
   $ubnId[$index] = $du['ubnId']; 
   $ubnnm[$index] = $du['ubn'];
   $ubnRaak[$index] = $du['ubnId'];
   $index++; 
}
unset($index);
// Einde Declaratie ubn

// Declaratie ras
$qryRassen = "
SELECT r.rasId, r.ras, lower(coalesce(isnull(ru.scan),'6karakters')) scan
FROM tblRas r
 join tblRasuser ru on (r.rasId = ru.rasId)
WHERE ru.lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and r.actief = 1 and ru.actief = 1
ORDER BY ras
"; 
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


// Declaratie ACTIE
$qryActie = mysqli_query($db,"
SELECT actId, actie
FROM tblActie
WHERE op = 1
ORDER BY actId
") or die (mysqli_error($db));

$index = 0; 
while ($qa = mysqli_fetch_assoc($qryActie)) 
{ 
   $actieId[$index] = $qa['actId']; 
   $actnm[$index] = $qa['actie'];
   $actRaak[$index] = $qa['actId'];
   $index++; 
} 
unset($index);
// EINDE Declaratie ACTIE

if(isset($data))  {	

//echo count($data);

	foreach($data as $key => $array)
	{
		$scandate = $array['datum'];
$date = str_replace('/', '-', $scandate);
//$gebdatum = date('d-m-Y', strtotime($date)-365*60*60*24);
$scandatum = date('d-m-Y', strtotime($date));
	
	$Id = $array['readId'];
	$ubn_st = $array['ubn']; // ubn van dier dat op de stal aanwezig is
	$levnr_rd = $array['levnr_rd']; //if (strlen($levnr_rd)== 11) {$levnr_rd = '0'.$array['levnr'];}
	$levnr_dupl = $array['dubbelen']; // twee keer in reader bestand
	$levnr_stal = $array['levnr_stal'];
	$ras_stal = $array['ras'];
	$eigenDier = $array['lidId']; // Als levensnummer bestaat maar niet op eigen stallijst dan bestaat lidId niet
	$geslacht_stal = $array['geslacht'];
	$ouder = $array['ouder']; if(isset($ouder) && $geslacht_stal == 'ooi') { $fase = 'moeder'; } else if(isset($ouder) && $geslacht_stal == 'ram') { $fase = 'vader'; } else { $fase = 'lam'; }
 	$afvoer = $array['actie'];
 	$verblijf = $array['hoknr'];
 	$gebdm = $array['gebdm'];


unset($schaapId_db);
unset($gebdag_db);
unset($geslacht_db);
unset($aanwas_db);
unset($fase_db);
unset($ras_db);

if($levnr_stal == 0) {

$zoek_levnr_db = "
SELECT s.schaapId, gebdag, geslacht, his_aanw, r.ras
FROM tblSchaap s
 left join (
 	SELECT schaapId, date_format(h.datum,'%d-%m-%Y') gebdag
 	FROM tblHistorie h
 	 join tblStal st on (st.stalId = h.stalId)
 	WHERE actId = 1 and h.skip = 0
 	) geb on (geb.schaapId = s.schaapId)
 left join (
 	SELECT schaapId, hisId his_aanw
 	FROM tblHistorie h
 	 join tblStal st on (st.stalId = h.stalId)
 	WHERE actId = 3 and h.skip = 0
 	) aanw on (aanw.schaapId = s.schaapId)
 left join tblRas r on (r.rasId = s.rasId)
WHERE s.levensnummer = '" . mysqli_real_escape_string($db,$levnr_rd) . "'
";

/*echo $zoek_levnr_db.'<br>';*/ $zoek_levnr_db = mysqli_query($db,$zoek_levnr_db) or die (mysqli_error($db));

while ($zld = mysqli_fetch_assoc($zoek_levnr_db)) 
{ $schaapId_db = $zld['schaapId'];
  $gebdag_db = $zld['gebdag'];
	$geslacht_db = $zld['geslacht'];
	$aanwas_db = $zld['his_aanw']; if( isset($aanwas_db) && $geslacht_db == 'ooi') { $fase_db = 'moeder'; } else if( isset($aanwas_db) && $geslacht_db == 'ram') { $fase_db = 'vader'; } else { $fase_db = 'lam'; }
	$ras_db = $zld['ras']; }


}

unset($txtDmgeb);

// Controleren of ingelezen waardes worden gevonden .
$kzlRas = $ras_stal; /*$kzlOoi = $mdr_db;*/ 
$kzlHok = $hok_db;  
if (isset($_POST['knpVervers_'])) {
$scandatum = $_POST["txtScandm_$Id"]; 
$txtGebdm  = $_POST["txtGebdm_$Id"]; if(!empty($txtGebdm)) { $txtDmgeb = date_format(date_create($txtGebdm), 'Y-m-d'); }
//$kzlRas = $_POST["kzlras_$Id"]; 
$kzlSekse = $_POST["kzlSekse_$Id"]; 
$kzlFase = $_POST["kzlFase_$Id"]; //echo $kzlFase.'<br>';
$kzlActie = $_POST["kzlActie_$Id"]; //echo $kzlActie.'<br>'.'<br>';
if($modtech == 1) { $kzlHok = $_POST["kzlHok_$Id"]; }
	 }

$date2 = eerste_datum_na_geboortedatum($schaapId_db);
$datum2 = date_format(date_create($date2), 'd-m-Y');

unset($onjuist);
unset($color);

if (isset($levnr_dupl)) 				{ $color = 'blue'; $onjuist = "Dubbel in de reader."; }
else if (empty($scandatum)) 			{ $color = 'red'; $onjuist = "De datum is onbekend."; }
else if (isset($afvoer)) 				{ $color = 'red'; $onjuist = "Dit schaap is ".strtolower($afvoer)."."; }  
else if (isset($levnr_rd) && strlen($levnr_rd) <> 12) { $color = 'red'; $onjuist = "Levensnummer geen 12 karakters."; }  
else if (numeriek($levnr_rd) == 1) 	{ $color = 'red'; $onjuist = "Levensnummer bevat een letter."; } 
else if (isset($txtDmgeb) && isset($date2) && $date2 < $txtDmgeb)	{ $color = 'red'; $onjuist = "De geboortedatum mag niet na ".$datum2." liggen."; }
else if ($levnr_stal == 0) { // Als het levensnummer niet op de stallijst staat

	if($kzlActie == 1 && empty($txtGebdm)) { $color = 'red'; $onjuist = "De geboortedatum is onbekend."; }
	else if(!isset($schaapId_db) && empty($kzlFase)) { $color = 'red'; $onjuist = "De generatie is onbekend."; }
	else if($kzlFase == 'lam' && empty($kzlHok)) { $color = 'red'; $onjuist = "Het verblijf is onbekend."; }
	else if(empty($kzlActie)) 			{ $color = 'red'; $onjuist = "De registratie is onbekend."; }
	else if($kzlActie == 1 && ($kzlFase == 'moeder' || $kzlFase == 'vader') ) { $color = 'red'; $onjuist = "De generatie en registratie is tegenstrijdig."; }
	else if($kzlSekse == 'ooi' && $kzlFase == 'vader') { $color = 'red'; $onjuist = "Het geslacht en generatie is tegenstrijdig."; }
	else if($kzlSekse == 'ram' && $kzlFase == 'moeder') { $color = 'red'; $onjuist = "Het geslacht en generatie is tegenstrijdig."; }

} // Einde else if ($levnr_stal == 0)

if(isset($onjuist)) { $oke = 0; } else { $oke = 1; } // $oke kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.
// EINDE Controleren of ingelezen waardes worden gevonden .  

	 if (isset($_POST['knpVervers_']) && $_POST["laatsteOke_$Id"] == 0 && $oke == 1) /* Als onvolledig is gewijzigd naar volledig juist */ {$cbKies = 1; $cbDel = $_POST["chbDel_$Id"]; }
else if (isset($_POST['knpVervers_'])) { $cbKies = $_POST["chbkies_$Id"];  $cbDel = $_POST["chbDel_$Id"]; } 
   else { $cbKies = $oke; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet
 ?>

<!--	**************************************
		**	  	 OPMAAK  GEGEVENS			**
		************************************** -->

<tr style = "font-size:14px;">
 <td align = "center"> 

	<input type = hidden size = 1 name = <?php echo "chbkies_$Id"; ?> value = 0 > <!-- hiddden -->
	<input type = checkbox 		  name = <?php echo "chbkies_$Id"; ?> value = 1 
	  <?php echo $cbKies == 1 ? 'checked' : ''; /* Als voorwaarde goed zijn of checkbox is aangevinkt */

	  if ($oke == 0) /*Als voorwaarde niet klopt */ { ?> disabled <?php } else { ?> class="checkall" <?php } /* class="checkall" zorgt dat alles kan worden uit- of aangevinkt*/ ?> >
	<input type = hidden size = 1 name = <?php echo "laatsteOke_$Id"; ?> value = <?php echo $oke; ?> > <!-- hiddden -->
 </td>
 <td align = "center">
	<input type = hidden size = 1 name = <?php echo "chbDel_$Id"; ?> value = 0 >
	<input type = checkbox class="delete" name = <?php echo "chbDel_$Id"; ?> value = 1 <?php if(isset($cbDel)) { echo $cbDel == 1 ? 'checked' : ''; } ?> >
 </td>
 <td>
<?php if (isset($_POST['knpVervers_'])) { $scandatum = $_POST["txtScandm_$Id"]; } ?>
	<input type = "text" size = 8 style = "font-size : 11px;" name = <?php echo "txtScandm_$Id"; ?> value = <?php echo $scandatum; ?> >
 </td>
 <td align="center">
<?php if(isset($ubn_st)) { echo $ubn_st; } else { ?>
	<!-- KZLUBN -->
 <select style="width:65;" <?php echo " name=\"kzlUbn_$Id\" "; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php	$count = count($ubnId);	
for ($i = 0; $i < $count; $i++){

	$opties = array($ubnId[$i]=>$ubnnm[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((!isset($_POST['knpVervers_']) && $ubnId_rd == $ubnRaak[$i]) || (isset($_POST["kzlUbn_$Id"]) && $_POST["kzlUbn_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
} ?>
 </select>
	 <!-- EINDE KZLUBN -->
<?php } ?>
 </td>
<?php if ($levnr_stal > 0 && strlen($levnr_rd) == 12 && numeriek($levnr_rd) <> 1) { ?> 
 <td style = "text-align:center;" width= 100>
<?php echo $levnr_rd; } else { ?> <td style = "text-align:center; color : red;" > <?php echo $levnr_rd; } ?>
<!-- <input type = "hidden" name = <p??hp echo " \"txtlevgeb_$Id\" value = \"$levnr_rd\" ;"?> size = 9 style = "font-size : 9px;"> -->
 </td>
 <!--Geboorte datum -->
  <td style = "text-align:center;" width= 80>
 	<?php if($levnr_stal > 0) { echo $gebdm; } else if (isset($gebdag_db)) { echo $gebdag_db; } else { ?>
 		<input type = "text" size = 8 style = "font-size : 11px;" name = <?php echo "txtGebdm_$Id"; ?> value = <?php echo $txtGebdm; ?> >
 	<?php } ?>
 </td>
 
 <td style = "text-align:center;" width= 80>
 <?php if($levnr_stal > 0) { echo $geslacht_stal; } else if(isset($geslacht_db)) { echo $geslacht_db; } else { ?>
<!-- KZLGESLACHT --> 
<select <?php echo " name=\"kzlSekse_$Id\" "; ?> style="width:59; font-size:13px;">

<?php
$opties = array('' => '', 'ooi' => 'ooi', 'ram' => 'ram');
foreach ( $opties as $key => $waarde)
{
   if((!isset($_POST['knpVervers_']) && $kzlSekse == $key) || (isset($_POST["kzlSekse_$Id"]) && $_POST["kzlSekse_$Id"] == $key) ) {
   echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
 } else {
   echo '<option value="' . $key . '">' . $waarde . '</option>';
   }
}

	?> </select> <!-- EINDE KZLGESLACHT -->
<?php } ?>
 </td>
 <td style = "text-align:center; font-size:13px;" width= 80>
<?php if($levnr_stal > 0) { echo $fase; } else if(isset($fase_db)) { echo $fase_db; } else { ?> 	

<!-- KZLGENERATIE --> 
<?php //echo $kzlFase; ?>
<select <?php echo " name=\"kzlFase_$Id\" "; ?> >

<?php  
$opties = array('' => '', 'lam' => 'lam', 'moeder' => 'moeder', 'vader' => 'vader');
foreach ( $opties as $key => $waarde)
{
   if((!isset($_POST['knpVervers_']) && $kzlFase == $key) || (isset($_POST["kzlFase_$Id"]) && $_POST["kzlFase_$Id"] == $key) ) {
   echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
 } else {
   echo '<option value="' . $key . '">' . $waarde . '</option>';
   }
}

	?> </select> <!-- EINDE KZLGENERATIE -->
	<?php } ?>
 </td>

 <td align="center" > <?php 

if($levnr_stal > 0) { echo $ras_stal; } else { ?>

<!-- KZLRAS -->
 <select style="width:65;" <?php echo " name=\"kzlRas_$Id\" "; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php	$count = count($rasId);	
for ($i = 0; $i < $count; $i++){

	$opties = array($rasId[$i]=>$rasnm[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((!isset($_POST['knpVervers_']) && $ras_rd == $rasRaak[$i]) || (isset($_POST["kzlRas_$Id"]) && $_POST["kzlRas_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}

 ?> </select>
<?php } ?>
	 <!-- EINDE KZLRAS -->
 </td>

<?php if($modtech == 1) { ?>
 <td style = "font-size : 11px;" align="center">

 <?php if(isset($verblijf)) { echo $verblijf; } else { ?>

 <!-- KZLHOKNR --> 
 <select style="width:68;" <?php echo " name=\"kzlHok_$Id\" "; ?> value = "" style = "font-size:12px;">
  <option></option>

<?php	$count = count($hoknum);
for ($i = 0; $i < $count; $i++){

	$opties = array($hoknId[$i]=>$hoknum[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((!isset($_POST['knpVervers_']) && $hok_rd == $hokRaak[$i]) || (isset($_POST["kzlHok_$Id"]) && $_POST["kzlHok_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}
?>	</select>

<?php } ?>
 </td> <!-- EINDE KZLHOKNR -->
<?php }

 if(!isset($eigenDier)) { ?>
 <td style = "font-size : 11px;"> 

<!-- KZLACTIE --> 
 <select style="width:150;" <?php echo " name=\"kzlActie_$Id\" "; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php $count = count($actnm);
for ($i = 0; $i < $count; $i++){

	$opties = array($actieId[$i]=>$actnm[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((isset($_POST["kzlActie_$Id"]) && $_POST["kzlActie_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}

 ?> </select> <!-- EINDE KZLACTIE -->
</td>
<td align = "center" > 
	<?php if (isset($_POST['knpVervers_'])) { $cbRvo = $_POST["chbRvo_$Id"]; } ?>
 <input type = checkbox 		  name = <?php echo "chbRvo_$Id"; ?> value = 1 
	  <?php echo $cbRvo == 1 ? 'checked' : ''; ?> >

 </td> 
 <td style = "color : <?php echo $color; ?> ; font-size : 13px;">
<?php if(isset($onjuist)) { echo $onjuist; } ?>
 </td>
<?php } ?>
</tr>
<!--	**************************************
	**	EINDE OPMAAK GEGEVENS	**
	************************************** -->

<?php } 
} //einde if(isset($data)) ?>

</td>
<td id = "NietGescand" valign= "top"> </td>

<?php 
$zoek_aantal_niet_gescand = mysqli_query($db,"
SELECT count(s.schaapId) nietgescand
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 left join (
 	SELECT levensnummer
	FROM impAgrident
	WHERE lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and actId = 22 and isnull(verwerkt)
 ) rd on (rd.levensnummer = s.levensnummer)
WHERE isnull(rd.levensnummer) and isnull(st.rel_best) and st.lidId = '" . mysqli_real_escape_string($db,$lidId) . "'
") or die (mysqli_error($db));

while ($zang = mysqli_fetch_assoc($zoek_aantal_niet_gescand)) 
{ $aant = $zang['nietgescand']; }

if($aant > 0) { $alles = 'Nee'; $tekst = 'Schapen op de stallijst die niet in bovenstaande lijst voorkomen.'; }
else { $alles = 'Ja'; $tekst = 'De hele stallijst is gescand.'; }

?>

<tr height = 100 > 
 <td colspan="5" width="350" align="center" valign="bottom"> <?php echo $tekst; ?> </td>
</tr>

<?php  if($alles == 'Nee') { ?>
<tr valign = bottom style = "font-size : 12px;">
 <th>Levensnummer<hr></th>
 <th>Geboren<hr></th>
 <th>Geslacht<hr></th>
 <th>Generatie<hr></th>
 <th>Laatste<br> controle<hr></th>
</tr>
 <?php
$zoek_niet_gescande_schapen = mysqli_query($db,"
SELECT s.levensnummer, gebdm, s.geslacht, oudr.hisId aanwasId, lastdm
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 left join (
 	SELECT levensnummer
	FROM impAgrident
	WHERE lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and actId = 22 and isnull(verwerkt)
 ) rd on (rd.levensnummer = s.levensnummer)
 left join (
 	SELECT st.schaapId, date_format(h.datum,'%d-%m-%Y') gebdm
 	FROM tblHistorie h
 	 join tblStal st on (h.stalId = st.stalId)
 	WHERE actId = 1 and h.skip = 0
 ) geb on (geb.schaapId = s.schaapId)
 left join (
 	SELECT st.schaapId, h.hisId
 	FROM tblHistorie h
 	 join tblStal st on (h.stalId = st.stalId)
 	WHERE actId = 3 and h.skip = 0
 ) oudr on (oudr.schaapId = s.schaapId)
 left join (
 	SELECT st.schaapId, date_format(h.datum,'%d-%m-%Y') lastdm
 	FROM tblHistorie h
 	 join tblStal st on (h.stalId = st.stalId)
 	 join (
 	 		SELECT max(hisId) hismx, schaapId
 	 		FROM tblHistorie h
		 	 join tblStal st on (h.stalId = st.stalId)
		 	WHERE actId = 22 and h.skip = 0 and lidId = '" . mysqli_real_escape_string($db,$lidId) . "'
		 	GROUP BY schaapId
 		) sc on (sc.hismx = h.hisId and sc.schaapId = st.schaapId)
 ) lstsc on (lstsc.schaapId = s.schaapId)
WHERE isnull(rd.levensnummer) and isnull(st.rel_best) and st.lidId = '" . mysqli_real_escape_string($db,$lidId) . "'
") or die (mysqli_error($db));

while ($zngs = mysqli_fetch_assoc($zoek_niet_gescande_schapen)) 
{ $levnr_rest = $zngs['levensnummer']; 
  $gebdm_rest = $zngs['gebdm']; 
  $geslacht_rest = $zngs['geslacht']; 
  $aanwas_rest = $zngs['aanwasId']; if( isset($aanwas_rest) && $geslacht_rest == 'ooi') { $fase_rest = 'moeder'; } else if( isset($aanwas_rest) && $geslacht_rest == 'ram') { $fase_rest = 'vader'; } else { $fase_rest = 'lam'; }
  $last_datum = $zngs['lastdm']; 
  ?>

<tr style = "font-size:12px;" align="center">
<td> <?php echo $levnr_rest; ?> </td>
<td> <?php echo $gebdm_rest; ?> </td>
<td> <?php echo $geslacht_rest; ?> </td>
<td> <?php echo $fase_rest; ?> </td>
<td> <?php echo $last_datum; ?> </td>
</tr>
<?php } ?>



<?php } // EInde if($alles == 'Nee') ?>

</table>

</form> 



</TD>
<?php
include "menu1.php"; } ?>
</tr>

</table>

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
