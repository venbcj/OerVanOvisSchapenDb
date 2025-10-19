<?php 
$versie = '03-11-2024'; /* Kopie gemaakt van InsAanvoer.php */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = "top"> 31-12-24 Include "login.php"; voor Include "header.php" gezet */
$versie = '09-08-2025'; /* Veld Ubn toegevoegd. Betreft eigen ubn van gebruiker. Per deze versie kan een gebruiker meerdere ubn's hebben */

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Registratie</title>
</head>
<body>

<?php
$titel = 'Inlezen Terug van uitscharen';
$file = "InsTvUitscharen.php";
Include "login.php"; ?>

			<TD valign = "top">
<?php
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {
 
include "vw_kzlOoien.php";
if ($modmeld == 1 ) { include "maak_request_func.php"; }

If (isset($_POST['knpInsert_']))  {
	//Include "url.php";
	Include "post_readerTvUitsch.php"; #Deze include moet voor de vervversing in de functie header()
	//header("Location: ".$url."InsTvUitscharen.php"); 
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

$velden = "rd.Id, rd.datum, rd.ubnId ubnId_rd, rd.levensnummer levnr_rd, rd.hokId hok_rd,
ho.hokId hok_db, dup.dubbelen ";

$tabel = "
impAgrident rd
 left join (
	SELECT ho.hokId
	FROM tblHok ho
	WHERE ho.lidId = '" . mysqli_real_escape_string($db,$lidId) . "'
 ) ho on (rd.hokId = ho.hokId)
 left join (
 	SELECT rd.Id, count(dup.Id) dubbelen
	FROM impAgrident rd
	 join impAgrident dup on (rd.lidId = dup.lidId and rd.levensnummer = dup.levensnummer and rd.Id <> dup.Id and rd.actId = dup.actId and isnull(dup.verwerkt))
	WHERE rd.actId = 11
	GROUP BY rd.Id
 ) dup on (rd.Id = dup.Id)
";

$WHERE = "WHERE rd.lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and rd.actId = 11 and isnull(rd.verwerkt)";

include "paginas.php";

$data = $page_nums->fetch_data($velden, "ORDER BY rd.datum, rd.Id"); ?>

<table border = 0>
<tr> <form action="InsTvUitscharen.php" method = "post">
 <td colspan = 2 style = "font-size : 13px;"> 
  <input type = "submit" name = "knpVervers_" value = "Verversen"></td>
 <td colspan = 2 align = "center" style = "font-size : 14px;"><?php 
echo $page_numbers; ?></td>
 <td colspan = 3 align = left style = "font-size : 13px;"> Regels Per Pagina: <?php echo $kzlRpp; ?> </td>
 <td colspan = 3 align = 'right'> <input type = "submit" name = "knpInsert_" value = "Inlezen">&nbsp &nbsp </td>
 <td colspan = 3 style = "font-size : 12px;"><b style = "color : red;">!</b> = waarde uit reader niet herkend. <br> 
<?php if($modtech == 1) { ?>* Alleen verplicht bij lammeren. <?php } ?> </td></tr>
<tr valign = bottom style = "font-size : 12px;">
 <th>Inlezen<br><b style = "font-size : 10px;">Ja/Nee</b><br> <input type="checkbox" id="selectall" checked /> <hr></th>
 <th>Verwij-<br>deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
 <th>Aanvoer<br>datum<hr></th>
 <th>Ubn<hr></th>
 <th>Levensnummer<hr></th>
 <th>Geslacht<hr></th>
 <th>Generatie<hr></th>
<?php if($modtech == 1) { ?>
 <th>Verblijf*<hr></th>
<?php } ?>
 <th width="145">Herkomst<hr></th>
 <th><hr></th>

</tr>

<?php
// Declaratie kzlUbn
$qryUbn = mysqli_query($db,"
SELECT ubnId, ubn
FROM tblUbn
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actief = 1
ORDER BY ubn
") or die (mysqli_error($db));

$index = 0; 
while ($qu = mysqli_fetch_assoc($qryUbn)) 
{ 
   $kzlUbnId[$index] = $qu['ubnId'];
   $ubnnm[$index] = $qu['ubn'];
   $ubnRaak[$index] = $qu['ubnId'];
   $index++;
} 
unset($index);

$count = count($kzlUbnId);
// Einde Declaratie kzlUbn

if($modtech == 1) {
// Declaratie kzlMOEDERDIER
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
// EINDE Declaratie kzlMOEDERDIER

// Declaratie kzlVERBLIJF			// lower(if(isnull(scan),'6karakters',scan)) zorgt ervoor dat $raak nooit leeg is. Anders worden legen velden gevonden in legen velden binnen impReader.
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
// EINDE Declaratie kzlVERBLIJF
}

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
	
	$Id = $array['Id'];
	$ubnId_rd = $array['ubnId_rd'];
	$levnr_rd = $array['levnr_rd']; //if (strlen($levnr_rd)== 11) {$levnr_rd = '0'.$array['levnr'];}
	$levnr_dupl = $array['dubbelen']; // twee keer in reader bestand
	$hok_rd = $array['hok_rd'];
	$hok_db = $array['hok_db'];


unset($schaapId);

unset($fase);
unset($sekse);

$zoek_schaapId = mysqli_query($db,"
SELECT schaapId
FROM tblSchaap
WHERE levensnummer = '".mysqli_real_escape_string($db,$levnr_rd)."'
");

while ($zs = mysqli_fetch_assoc($zoek_schaapId)) { $schaapId = $zs['schaapId']; }

#echo '$schaapId = '.$schaapId.'<br>';

$zoek_schaap_gegevens = mysqli_query($db,"
	SELECT geslacht
	FROM tblSchaap
	WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
");

while ($zsg = mysqli_fetch_assoc($zoek_schaap_gegevens)) { $sekse = $zsg['geslacht']; }

// Zoek historie van het schaap om te beoordelen dat het levensnummer van deze gebruiker (is geweest). Een levensnummer van een andere gebruiker wordt nl. gewoon geaccepteerd !!
unset($stalId_gebruiker);
$zoek_stalId = mysqli_query($db,"
	SELECT max(stalId) stalId
	FROM tblStal
	WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and lidId = '".mysqli_real_escape_string($db,$lidId)."'
");

while ($zs = mysqli_fetch_assoc($zoek_stalId)) { $stalId_gebruiker = $zs['stalId']; }


unset($aanwas);
$zoek_aanwas = mysqli_query($db,"
SELECT hisId
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
WHERE actId = 3 and schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
");

while ($za = mysqli_fetch_assoc($zoek_aanwas)) { $aanwas = $za['hisId']; }

If(isset($aanwas)) { if($sekse == 'ooi') { $fase = 'moeder'; } else { $fase = 'vader'; }
}
else { $fase = 'lam'; }

unset($max_his_af);
$zoek_laatste_keer_van_stallijst_af = mysqli_query($db,"
SELECT max(hisId) hisId
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
 join tblActie a on (h.actId = a.actId)
WHERE a.af = 1 and schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and lidId = '".mysqli_real_escape_string($db,$lidId)."'
");

while ($zlksa = mysqli_fetch_assoc($zoek_laatste_keer_van_stallijst_af)) { $max_his_af = $zlksa['hisId']; }


unset($stalId_uitsch);
unset($date_uitsch);
$zoek_uitscharen = mysqli_query($db,"
SELECT stalId, datum date, date_format(datum,'%d-%m-%Y') datum
FROM tblHistorie
WHERE actId = 10 and hisId = '".mysqli_real_escape_string($db,$max_his_af)."'
");

while ($zu = mysqli_fetch_assoc($zoek_uitscharen)) { 
	$stalId_uitsch = $zu['stalId']; 
	$date_uitsch = $zu['date']; }

#echo '$stalId_uitsch = '.$stalId_uitsch.'<br>';

unset($ubn_best);
unset($partij);
$zoek_ubn_bestemming = mysqli_query($db,"
SELECT p.ubn, concat(p.ubn, ' - ', p.naam) naam
FROM tblStal st
 join tblRelatie r on (st.rel_best = r.relId)
 join tblPartij p on (r.partId = p.partId)
WHERE stalId = '".mysqli_real_escape_string($db,$stalId_uitsch)."'
");

while ($zub = mysqli_fetch_assoc($zoek_ubn_bestemming)) { $ubn_best = $zub['ubn']; $partij = $zub['naam']; }


unset($relId_herk);
$zoek_crediteur_van_ubn = mysqli_query($db,"
SELECT relId
FROM tblRelatie r
 join tblPartij p on (r.partId = p.partId)
WHERE r.relatie = 'cred' and p.ubn = '".mysqli_real_escape_string($db,$ubn_best)."' and p.lidId = '".mysqli_real_escape_string($db,$lidId)."'
");

while ($zcu = mysqli_fetch_assoc($zoek_crediteur_van_ubn)) { $relId_herk = $zcu['relId']; }

unset($opStal);
$opStal = zoek_stalId_in_stallijst($lidId,$schaapId);

#echo '$opStal = '.$opStal.'<br>';

unset($afv_status);
$zoek_afvoer /* excl. uitscharen */ = mysqli_query($db,"
SELECT lower(actie) actie
FROM tblHistorie h
 join tblActie a on (a.actId = h.actId)
WHERE h.actId != 10 and h.hisId = '".mysqli_real_escape_string($db,$max_his_af)."'
");

while ($za = mysqli_fetch_assoc($zoek_afvoer)) { $afv_status = $za['actie']; }

$kzlUbn = $ubnId_rd;

if (isset($_POST['knpVervers_'])) {
	$datum = $_POST["txtAanvdm_$Id"];
	$date = date('Y-m-d', strtotime($datum));
	$kzlUbn = $_POST["kzlUbn_$Id"];
 }

// Controleren of ingelezen waardes correct zijn ingevuld.
unset($onjuist);
unset($color);



if (!isset($schaapId) ) 				{ $color = 'red'; $onjuist =  "Het levensnummer bestaat niet."; }
else if (isset($levnr_dupl) ) 		{ $color = 'blue'; $onjuist =  "Dubbel in de reader."; }
else if (empty($datum))					{ $color = 'red'; $onjuist = 'De aanvoerdatum is onbekend'; }
else if (empty($kzlUbn))				{ $color = 'red'; $onjuist = 'Ubn is onbekend'; }
else if (isset($opStal))				{ $color = 'red'; $onjuist = "Dit dier staat op de stallijst."; }
else if ($date < $date_uitsch)		{ $color = 'red'; $onjuist = "De datum ligt voor de datum van uitscharen."; }
else if (isset($afv_status))			{ $color = 'red'; $onjuist = "Dit dier is " . $afv_status; }
else if (isset($partij) && !isset($relId_herk)) { $color = 'red'; $onjuist = $partij ." is geen crediteur."; } 
else if (!isset($stalId_gebruiker)) { $color = 'red'; $onjuist = "Dit dier wordt niet herkend."; } 


if (isset($onjuist)) {	$oke = 0;	} else {	$oke = 1;	} // $oke kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.
// EINDE Controleren of ingelezen waardes correct zijn ingevuld.

	 if (isset($_POST['knpVervers_']) && $_POST["laatsteOke_$Id"] == 0 && $oke == 1) /* Als onvolledig is gewijzigd naar volledig juist */ {$cbKies = 1; $cbDel = $_POST["chbDel_$Id"]; }
else if (isset($_POST['knpVervers_'])) { $cbKies = $_POST["chbKies_$Id"];  $cbDel = $_POST["chbDel_$Id"]; } 
   else { $cbKies = $oke; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet ?>

<!--	**************************************
		**	  	 OPMAAK  GEGEVENS			**
		************************************** -->

<tr style = "font-size:14px;">
 <td align = "center"> 

	<input type = hidden size = 1 name = <?php echo "chbKies_$Id"; ?> value = 0 > <!-- hiddden -->
	<input type = checkbox 		  name = <?php echo "chbKies_$Id"; ?> value = 1 
	  <?php echo $cbKies == 1 ? 'checked' : ''; /* Als voorwaarde goed zijn of checkbox is aangevinkt */

	  if ($oke == 0) /*Als voorwaarde niet klopt */ { ?> disabled <?php } else { ?> class="checkall" <?php } /* class="checkall" zorgt dat alles kan worden uit- of aangevinkt*/ ?> >
	<input type = hidden size = 1 name = <?php echo "laatsteOke_$Id"; ?> value = <?php echo $oke; ?> > <!-- hiddden -->
 </td>
 <td align = "center">
	<input type = hidden size = 1 name = <?php echo "chbDel_$Id"; ?> value = 0 >
	<input type = checkbox class="delete" name = <?php echo "chbDel_$Id"; ?> value = 1 <?php if(isset($cbDel)) { echo $cbDel == 1 ? 'checked' : ''; } ?> >
 </td>
 <td>
<?php if (isset($_POST['knpVervers_'])) { $datum = $_POST["txtAanvdm_$Id"]; } ?>
	<input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtAanvdm_$Id"; ?> value = <?php echo $datum; ?> >
 </td>
   <td>
<!-- KZLUBN -->
 <select style="width:65;" <?php echo " name=\"kzlUbn_$Id\" "; ?> value = "" style = "font-size:10px;">
  <option></option>
<?php	$count = count($kzlUbnId);	
for ($i = 0; $i < $count; $i++){

	$opties = array($kzlUbnId[$i]=>$ubnnm[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((!isset($_POST['knpVervers_']) && $ubnId_rd == $ubnRaak[$i]) || (isset($_POST["kzlUbn_$Id"]) && $_POST["kzlUbn_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}

 ?> </select>
 <!-- Einde KZLUBN -->
 </td>
<?php if (strlen($levnr_rd) == 12 && numeriek($levnr_rd) <> 1) { ?> 
 <td>
<?php echo $levnr_rd; } else { ?> <td style = "color : red;" > <?php echo $levnr_rd; } ?>
<!-- <input type = "hidden" name = <p??hp echo " \"txtlevgeb_$Id\" value = \"$levnr_rd\" ;"?> size = 9 style = "font-size : 9px;"> -->
 </td>


 <td align="center">
<?php echo $sekse; ?>
 </td>
 <td align="center">
<?php echo $fase; ?>
 </td>
<?php if($modtech == 1) { ?>

 <td style = "font-size : 9px;">
<!-- KZLHOKNR --> 
 <select style="width:65;" <?php echo " name=\"kzlHok_$Id\" "; ?> value = "" style = "font-size:12px;">
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
<?php 
if ( !empty($hok_rd) && empty($hok_db) && !isset($_POST['knpVervers_']) ) {echo $hok_rd; ?> <b style = "color : red;"> ! </b>  <?php } ?>
 </td> <!-- EINDE KZLHOKNR -->
<?php } // Einde if($modtech == 1)?>

 <td>
<!-- HERKOMST -->
&nbsp &nbsp <?php echo $partij; ?>
 </td> <!-- EINDE HERKOMST -->	

 <td colspan = 3 style = "color : <?php echo $color; ?> ; font-size : 12px;"> <?php if(isset($onjuist)) { echo $onjuist; } ?>
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