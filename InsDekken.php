<?php 
$versie = '18-12-2021'; /* Gekopieerd van insDracht.php */

 session_start(); ?>
<html>
<head>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Registratie</title>
</head>
<body>

<center>
<?php
$titel = 'Inlezen Dekken';
$subtitel = ''; 
Include "header.php"; ?>
	<TD width = 960 height = 400 valign = "top">
<?php 
$file = "InsDekken.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {

include "vw_kzlOoien.php";

If (isset($_POST['knpInsert_']))  {
	//Include "url.php";
	Include "post_readerDekken.php"; #Deze include moet voor de vervversing in de functie header()
	}

function numeriek($subject) {
	if (preg_match('/([[a-zA-Z])/', $subject, $matches)) {  /*var_dump($matches[1]); */ return 1; }
}

//if($reader == 'Agrident') {
$velden = "rd.Id Id, rd.datum, rd.moeder, mdr.schaapId mdrId, rd.vdrId, vdr.vader";

$tabel = "
impAgrident rd
 left join (
 	SELECT s.schaapId, s.levensnummer
 	FROM tblSchaap s
 	 join tblStal st on (s.schaapId = st.schaapId)
 	WHERE lidId = ".mysqli_real_escape_string($db,$lidId)."
 	) mdr on (mdr.levensnummer = rd.moeder)
 left join (
 	SELECT s.schaapId, s.levensnummer vader
 	FROM tblSchaap s
 	) vdr on (vdr.schaapId = rd.vdrId)
";

$WHERE = "WHERE rd.lidId = ".mysqli_real_escape_string($db,$lidId)." and rd.actId = 18 and isnull(verwerkt) ";

include "paginas.php";

$data = $page_nums->fetch_data($velden, "ORDER BY str_to_date(rd.datum,'%d/%m/%Y'), rd.Id"); ?>

<table border = 0>
<tr> <form action="InsDekken.php" method = "post">
 <td colspan = 2 style = "font-size : 13px;"> 
  <input type = "submit" name = "knpVervers_" value = "Verversen"></td>
 <td colspan = 2 align = center style = "font-size : 14px;"><?php 
echo $page_numbers; ?></td>
 <td colspan = 3 align = left style = "font-size : 13px;"> Regels Per Pagina: <?php echo $kzlRpp; ?> </td>
 <td colspan = 2 align = 'right'><input type = "submit" name = "knpInsert_" value = "Inlezen"> </td>
</tr>
<tr valign = bottom style = "font-size : 12px;">
 <th>Inlezen<br><b style = "font-size : 10px;">Ja/Nee</b><br> <input type="checkbox" id="selectall" checked /> <hr></th>
 <th>Verwij-<br>deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
 <th>Dekdatum<hr></th>
 <th>Moeder<hr></th>
 <th>Vader<hr></th>
 <th><hr></th>

</tr>

<?php
if($modtech == 1) {
// Declaratie MOEDERDIER alleen op stal en niet geworpen laatste 183 dagen
$zoek_moederdieren = mysqli_query($db,"
SELECT st.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk) werknr
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 left join (
 	SELECT stalId, hisId
 	FROM tblHistorie h
 	 join tblActie a on (h.actId = a.actId)
 	WHERE a.af = 1
 ) haf on (haf.stalId = st.stalId)
 join (
 	SELECT schaapId
 	FROM tblStal st
 	 join tblHistorie h on (st.stalId = h.stalId)
 	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
 left join (
 	SELECT v.mdrId, count(lam.schaapId) worpat
	FROM tblVolwas v
	 left join tblSchaap lam on (lam.volwId = v.volwId)
	WHERE date_add(v.datum,interval 183 day) > CURRENT_DATE()
	GROUP BY v.mdrId
 ) v on (s.schaapId = v.mdrId)
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and s.geslacht = 'ooi' and isnull(haf.hisId) and (isnull(v.worpat) or v.worpat =0) 
ORDER BY right(s.levensnummer,$Karwerk)
") or die (mysqli_error($db));


$index = 0; 
while ($mdr = mysqli_fetch_assoc($zoek_moederdieren)) 
{ 
   $mdrkey[$index] = $mdr['schaapId'];
   $wnrOoi[$index] = $mdr['werknr'];
   $index++; 
} 
unset($index); 
// EINDE Declaratie MOEDERDIER

// Declaratie VADERDIER  ALLEEN OP STAL
$zoek_vaderdieren = mysqli_query($db,"
SELECT st.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk) werknr
FROM tblSchaap s 
 join tblStal st on (st.schaapId = s.schaapId)
 left join (
	SELECT stalId, hisId, datum
 	FROM tblHistorie h
 	 join tblActie a on (h.actId = a.actId)
 	WHERE a.af = 1
 ) haf on (haf.stalId = st.stalId)
 join (
 	SELECT schaapId
 	FROM tblStal st
 	 join tblHistorie h on (st.stalId = h.stalId)
 	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and s.geslacht = 'ram' and ( isnull(haf.hisId) or date_add(haf.datum,interval 2 month) > CURRENT_DATE() )
ORDER BY right(levensnummer,$Karwerk)
") or die (mysqli_error($db)); 


$index = 0; 
while ($vdr = mysqli_fetch_assoc($zoek_vaderdieren)) 
{ 
   $vdrkey[$index] = $vdr['schaapId'];
   $lvnrRam[$index] = $vdr['levensnummer'];
   $vdrRaak[$index] = $vdr['schaapId'];
   $index++; 
} 
unset($index); 
// EINDE Declaratie VADERDIER
}


if(isset($data))  { foreach($data as $key => $array)
	{
		$var = $array['datum'];
$date = str_replace('/', '-', $var);
$datum = date('d-m-Y', strtotime($date)); #echo '$datum = '.$datum.'<br>';
	
	$Id = $array['Id'];
	$moeder_rd = $array['moeder']; // levensnummer uit reader
	$mdrId_rd = $array['mdrId']; // schaapId uit tblStal o.b.v. moeder uit reader
	$vdrId_rd = $array['vdrId']; // schaapId uit reader
	$vader_rd = $array['vader']; // levensnummer ram o.b.v. schaapId uit reader


// Controleren of ingelezen waardes worden gevonden .
if (isset($_POST['knpVervers_']) ) {

	$txtDatum = $_POST["txtDatum_$Id"]; 
	$keuzeOoi = $_POST["kzlOoi_$Id"]; 
//	$keuzeRam = $_POST["kzlRam_$Id"]; 
}
else { 
	$txtDatum = $datum;
	$keuzeOoi = $mdrId_rd;
//	$keuzeRam = $vdrId_rd;
}

if(!empty($keuzeOoi)) {
$zoek_moeder = mysqli_query($db,"
SELECT levensnummer
FROM tblSchaap s
WHERE s.schaapId = ".mysqli_real_escape_string($db,$keuzeOoi)."
") or die (mysqli_error($db));

while ($moe = mysqli_fetch_assoc($zoek_moeder)) 
{ 
   $moeder_db = $moe['levensnummer'];
}

unset($worp);
$zoek_recente_worp = mysqli_query($db,"
SELECT v.mdrId, count(lam.schaapId) aantal
FROM tblVolwas v
 join tblSchaap lam on (lam.volwId = v.volwId)
WHERE date_add(v.datum,interval 183 day) > CURRENT_DATE() and schaapId = ".mysqli_real_escape_string($db,$keuzeOoi)."
") or die (mysqli_error($db));

while ($rw = mysqli_fetch_assoc($zoek_recente_worp)) 
{ 
   $worp = $rw['levensnummer'];
}

unset($afv_status_mdr);
$zoek_afvoerstatus_mdr = mysqli_query($db,"
SELECT a.actie
FROM tblStal st
 join (
 	SELECT max(stalId) stalId
 	FROM tblStal
 	WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." and schaapId = ".mysqli_real_escape_string($db,$keuzeOoi)."
 ) maxst on (maxst.stalId = st.stalId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblActie a on (a.actId = h.actId)
WHERE a.af = 1
") or die (mysqli_error($db));

while ($sm = mysqli_fetch_assoc($zoek_afvoerstatus_mdr)) 
{ 
   $afv_status_mdr = $sm['levensnummer'];
}

/*$zoek_ram_uit_laatste_koppel = mysqli_query($db,"
SELECT v.vdrId, s.levensnummer
FROM tblVolwas v
 join (
 		SELECT max(volwId) volwId
 		FROM tblVolwas vw
 		 join tblStal st on (vw.mdrId = st.schaapId)
 		WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and mdrId = ".mysqli_real_escape_string($db,$keuzeOoi)."
 	) lstvw on (lstvw.volwId = v.volwId)
 join tblSchaap s on (s.schaapId = v.vdrId)
") or die (mysqli_error($db));

while ($rm = mysqli_fetch_assoc($zoek_ram_uit_laatste_koppel)) 
{ 
   $ramId_vw = $rm['vdrId'];
   $ram_vw = $rm['levensnummer'];
}*/

} // Einde if(isset($keuzeOoi)) 


/*if(!empty($keuzeRam))
{

unset($afv_status_vdr);
$zoek_afvoerstatus_vdr = mysqli_query($db,"
SELECT st.stalId, a.actie
FROM tblStal st
 join tblHistorie h on (h.stalId = st.stalId)
 join tblActie a on (a.actId = h.actId)
WHERE st.schaapId = ".mysqli_real_escape_string($db,$keuzeRam)." and a.af = 1	 
") or die (mysqli_error($db));

while ($stat = mysqli_fetch_assoc($zoek_afvoerstatus_vdr)) 
{ 
   $afv_status_vdr = $stat['actie'];
}

}*/

	if (
	 	isset($worp_rd) || isset($worp) 				|| // moederdier heeft laatste 183 al gelammerd
	 	(!isset($txtDatum) && empty($txtDatum))	|| // dekdatum is onbekend
	 	(!isset($moeder_db) || empty($keuzeOoi))		// moeder is onbekend
	 	//(!isset($vader_rd)  )  // vader is afgevoerd
	 												
	   )

	 {	$oke = 0;	} else {	$oke = 1;	} // $oke kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.
// EINDE Controleren of ingelezen waardes worden gevonden . 

	 if (isset($_POST['knpVervers_']) && $_POST["laatsteOke_$Id"] == 0 && $oke == 1) /* Als onvolledig is gewijzigd naar volledig juist */ {$cbKies = 1; $cbDel = $_POST["chbDel_$Id"]; }
else if (isset($_POST['knpVervers_'])) { $cbKies = $_POST["chbKies_$Id"];  $cbDel = $_POST["chbDel_$Id"]; } 
   else { $cbKies = $oke; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet ?>

<!--	**************************************
		**	   	 OPMAAK  GEGEVENS			**
		************************************** -->

<tr style = "font-size:14px;">
 <td align = center>

	<input type = hidden size = 1 name = <?php echo "chbKies_$Id"; ?> value = 0 > <!-- hiddden -->
	<input type = checkbox 		  name = <?php echo "chbKies_$Id"; ?> value = 1 
	  <?php echo $cbKies == 1 ? 'checked' : ''; /* Als voorwaarde goed zijn of checkbox is aangevinkt */

	  if ($oke == 0) /*Als voorwaarde niet klopt */ { ?> disabled <?php } else { ?> class="checkall" <?php } /* class="checkall" zorgt dat alles kan worden uit- of aangevinkt*/ ?> >
	<input type = hidden size = 1 name = <?php echo "laatsteOke_$Id"; ?> value = <?php echo $oke; ?> > <!-- hiddden -->
 </td>
 <td align = center>
	<input type = hidden size = 1 name = <?php echo "chbDel_$Id"; ?> value = 0 >
	<input type = checkbox class="delete" name = <?php echo "chbDel_$Id"; ?> value = 1 <?php if(isset($cbDel)) { echo $cbDel == 1 ? 'checked' : ''; } ?> >
 </td>
 <td>
 	<input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtDatum_$Id"; ?> value = <?php echo $txtDatum; ?> >
 </td>
 <td style = "font-size : 11px;">
<!-- KZLMOEDER -->
<?php $width = 25+(8*$Karwerk) ; ?>
 <select style= "width:<?php echo $width; ?>; font-size:12px;" name = <?php echo "kzlOoi_$Id"; ?> >
  <option></option>
<?php	$count = count($wnrOoi);
for ($i = 0; $i < $count; $i++){

	$opties = array($mdrkey[$i]=>$wnrOoi[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((!isset($_POST['knpVervers_']) && $mdrId_rd == $key) || (isset($_POST["kzlOoi_$Id"]) && $_POST["kzlOoi_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}
?> </select> 
	<!-- EINDE KZLMOEDER --> 
 </td>
 <td> 
	<!-- KZLVADER -->
<?php $width = 25+(8*$Karwerk) ; ?>
 <select style= "width:125; font-size:12px;" name = <?php echo "kzlRam_$Id"; ?> >
 <option></option>	
<?php	$count = count($lvnrRam);
for ($i = 0; $i < $count; $i++){

		
	$opties= array($vdrkey[$i]=>$lvnrRam[$i]);
			foreach ($opties as $key => $waarde)
			{
  if ((!isset($_POST['knpVervers_']) && $vdrId_rd == $vdrRaak[$i]) || (isset($_POST["kzlRam_$Id"]) && $_POST["kzlRam_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }	
			}
		
} ?>
 </select>
	<!-- EINDE KZLVADER -->
 </td>
 <td style = "font-size:12px; color:red;" >
<?php if(isset($worp_rd)) { echo 'Ooi '.$moeder_rd.' heeft afgelopen 183 al '.$worp_rd.' lammeren geworpen'; } 
else if (!isset($moeder_db) || empty($keuzeOoi)) { echo 'Ooi '.$moeder_rd.' onbekend'; }
else if (isset($afv_status_mdr) && !isset($keuzeOoi)) 					 { echo 'Ooi '.$moeder_rd.' is '.$afv_status_mdr; }
//else if (isset($afv_status_vdr) && !isset($keuzeRam)) 					 { echo 'Ram '.$vader_rd.' is '.$afv_status_vdr; }
?>
 </td>
 <td></td>	
 <td></td> 
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