<?php 
$versie = '13-11-2016'; /* Aangemaakt als kopie van insAanvoer. 
schaap 100214520769 gewijzigd in */
$versie = '20-3-2018';  /* Meerdere pagina's gemaakt 12-5-2018 : if(isset($data)) toegevoegd. Als alle records zijn verwerkt bestaat $data nl. niet meer !! */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '10-11-2018'; /* Inlezen darcht herzien. Rekening gehouden met worp laatste 183 en alleen ooien en rammen op stallijst !! */
$versie = '20-1-2019'; /* alles aan- en uitzetten met javascript */
$versie = '24-4-2020'; /* url Javascript libary aangepast */

 session_start(); ?>
<html>
<head>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Registratie</title>
</head>
<body>

<center>
<?php
$titel = 'Inlezen Dracht';
$subtitel = ''; 
Include "header.php"; ?>
	<TD width = 960 height = 400 valign = "top">
<?php 
$file = "InsDracht.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {

include "vw_kzlOoien.php";

If (isset($_POST['knpInsert_']))  {
	//Include "url.php";
	Include "post_readerDracht.php"; #Deze include moet voor de vervversing in de functie header()
	//header("Location: ".$url."InsDracht.php"); 
	}

function numeriek($subject) {
	if (preg_match('/([[a-zA-Z])/', $subject, $matches)) {  /*var_dump($matches[1]); */ return 1; }
}

// Aantal nog in te lezen DRACHT
$zoek_dracht = mysqli_query($db,"
select count(*) aant 
from impReader 
where lidId = ".mysqli_real_escape_string($db,$lidId)." and moeder_dr is not NULL and isnull(verwerkt)
") or die (mysqli_error($db));
	$row = mysqli_fetch_assoc($zoek_dracht);  $drachtat = $row['aant'];
// EINDE Aantal nog in te lezen DRACHT

$velden = "rd.readId, rd.datum, rd.moeder_dr moeder, mdr.schaapId mdrId, mst.stalId ooi_op_stal, rd.vader_dr, vst.levensnummer vader, vdr.schaapId vdrId, vst.stalId ram_op_stal, rd.uitslag, worp.aantal worpgrootte, mdr_af.actie status_mdr, vdr_af.actie status_vdr";

$tabel = "
impReader rd

 left join tblSchaap mdr on (mdr.levensnummer = rd.moeder_dr)
 left join (
 	SELECT stalId, schaapId
 	FROM tblStal
 	WHERE lidId = ".mysqli_real_escape_string($db,$lidId)."
 ) mst on (mdr.schaapId = mst.schaapId)
 left join (
 	SELECT st.stalId, a.actie
 	FROM tblStal st
 	 join tblHistorie h on (h.stalId = st.stalId)
 	 join tblActie a on (a.actId = h.actId)
 	WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and a.af = 1
 ) mdr_af on (mst.stalId = mdr_af.stalId)

 left join tblSchaap vdr on (vdr.levensnummer = rd.vader_dr)
 left join (
 	SELECT st.stalId, st.scan, s.levensnummer
 	FROM tblStal st
 	 join tblSchaap s on (s.schaapId = st.schaapId)
 	WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and s.geslacht = 'ram'
 ) vst on (rd.vader_dr = vst.scan)
 left join (
 	SELECT st.stalId, a.actie
 	FROM tblStal st
 	 join tblHistorie h on (h.stalId = st.stalId)
 	 join tblActie a on (a.actId = h.actId)
 	WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and a.af = 1
 ) vdr_af on (vst.stalId = vdr_af.stalId)

 left join (
 	select v.mdrId, count(lam.schaapId) aantal
 	from tblVolwas v
 	 join tblSchaap lam on (lam.volwId = v.volwId)
 	where date_add(v.datum,interval 183 day) > CURRENT_DATE()
 	group by v.mdrId 
 ) worp on (worp.mdrId = mdr.schaapId)
";

$WHERE = "where rd.lidId = ".mysqli_real_escape_string($db,$lidId)." and (rd.moeder_dr is not null or rd.vader_dr is not null) and isnull(verwerkt) ";

include "paginas.php";

$data = $page_nums->fetch_data($velden, "ORDER BY vst.levensnummer, str_to_date(rd.datum,'%d/%m/%Y'), rd.readId"); ?>

<table border = 0>
<tr> <form action="InsDracht.php" method = "post">
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
 <th>Dracht<br>datum<hr></th>
 <th>Moeder<hr></th>
 <th>Vader<hr></th>
 <th>Drachtig<hr></th>
 <th><hr></th>

</tr>

<?php
if($modtech == 1) {
// Declaratie MOEDERDIER alleen op stal en niet geworpen laatste 183 dagen
$moederdier = mysqli_query($db,"
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
	group by v.mdrId
 ) v on (s.schaapId = v.mdrId)
where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and s.geslacht = 'ooi' and isnull(haf.hisId) and (isnull(v.worpat) or v.worpat =0) 
order by right(s.levensnummer,$Karwerk)
") or die (mysqli_error($db));


$index = 0; 
while ($mdr = mysqli_fetch_assoc($moederdier)) 
{ 
   $mdrkey[$index] = $mdr['schaapId'];
   $wnrOoi[$index] = $mdr['werknr'];
   $index++; 
} 
unset($index); 
// EINDE Declaratie MOEDERDIER

// Declaratie VADERDIER  ALLEEN OP STAL
$vaderdier = mysqli_query($db,"
SELECT st.schaapId, st.scan, s.levensnummer, right(s.levensnummer,$Karwerk) werknr
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
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and s.geslacht = 'ram' and isnull(haf.hisId)
ORDER BY right(levensnummer,$Karwerk)
") or die (mysqli_error($db)); 


$index = 0; 
while ($vdr = mysqli_fetch_assoc($vaderdier)) 
{ 
   $vdrkey[$index] = $vdr['schaapId'];
   $lvnrRam[$index] = $vdr['levensnummer'];
   $scanRaak[$index] = $vdr['scan'];
   $index++; 
} 
unset($index); 
// EINDE Declaratie VADERDIER
}


if(isset($data))  {	foreach($data as $key => $array)
	{
		$var = $array['datum'];
$date = str_replace('/', '-', $var);
$datum = date('d-m-Y', strtotime($date));
	
	$Id = $array['readId'];
	$ooi_rd = $array['moeder']; // levensnummer uit reader
	$mdrId = $array['mdrId']; // schaapId uit tblSchaap
	$ooi_op_stal = $array['ooi_op_stal']; /* StalId van aanwezig dier */ #echo '$ooi_op_stal '.$ooi_op_stal.'<br>'.'<br>'; 

	$ram_scan = $array['vader_dr']; /* scan vader uit reader */
	$ram_rd = $array['vader']; /* levensnummer via scan uit reader */
	$vdrId = $array['vdrId'];  /* schaapId uit tblSchaap */ #echo '$vdrId = '.$vdrId.'<br>';
	$ram_op_stal = $array['ram_op_stal']; /* StalId van aanwezig dier */ #echo '$ram_op_stal '.$ram_op_stal.'<br><br>';

	$uitslag = $array['uitslag']; if(isset($uitslag)) { $drachtig = 1; } else { $drachtig = 0; }
	$worp_rd = $array['worpgrootte'];

	$status_mdr = $array['status_mdr']; /* Reden van afvoer moeder */
	$status_vdr = $array['status_vdr']; /* Reden van afvoer vader */

// Controleren of ingelezen waardes worden gevonden .
if (isset($_POST['knpVervers_']) ) {


/* if(!empty($_POST["kzlOoi_$Id"])) { $kzlOoi = $_POST["kzlOoi_$Id"]; 

$query_worp = "
SELECT v.mdrId, count(lam.schaapId) worpat
FROM tblVolwas v
 join tblSchaap lam on (lam.volwId = v.volwId)
WHERE date_add(v.datum,interval 183 day) > CURRENT_DATE() and v.mdrId = ".mysqli_real_escape_string($db,$kzlOoi)."
group by v.mdrId
";

/*echo $query_worp.'<br>';*/ /*$query_worp = mysqli_query($db,$query_worp) or die (mysqli_error($db));

while ($w = mysqli_fetch_assoc($query_worp)) { $worp = $w['worpat']; }
} */
 
	$txtDatum = $_POST["txtDracdm_$Id"]; 
	$keuzeOoi = $_POST["kzlOoi_$Id"]; 
	$keuzeRam = $_POST["kzlRam_$Id"]; 
}
else { $txtDatum = $datum; }

if(isset($keuzeOoi)) { unset($worp_rd); }

		/*if(isset($worp_rd) || isset($worp) ) { $fout = 'moederdier heeft laatste 183 al gelammerd'; }
	 	if(				   (isset($txtDatum) && empty($txtDatum)) ) { $fout = ' drachtdatum is onbekend'; }
	if( 	!isset($ooi_rd) || (isset($keuzeOoi) && empty($keuzeOoi)) ) { $fout = ' moeder is onbekend'; }
	if( 	(!isset($ram_rd) && !isset($keuzeRam)) || (isset($keuzeRam) && empty($keuzeRam)) ) { $fout = 'vader '.$keuzeRam.' is onbekend'; }
	if( 	(!isset($ram_op_stal) && empty($keuzeRam))				 ) { $fout = ' vader niet op stallijst'; }
	if( 	(isset($status_mdr) && !isset($keuzeOoi))				 ) { $fout = ' moeder is afgevoerd'; }
	if( 	(isset($status_vdr) && !isset($keuzeRam))  				 ) { $fout = ' vader is afgevoerd'; }*/

	if (
	 	isset($worp_rd) || isset($worp) 									|| // moederdier heeft laatste 183 al gelammerd
	 	(isset($txtDatum) && empty($txtDatum)) 											 || // drachtdatum is onbekend
	 	(!isset($ooi_rd) && !isset($keuzeOoi)) || (isset($keuzeOoi) && empty($keuzeOoi)) || // moeder is onbekend
	 	(!isset($ram_rd) && !isset($keuzeRam)) || (isset($keuzeRam) && empty($keuzeRam)) || // vader is onbekend
	 	(!isset($ooi_op_stal) && empty($keuzeOoi))										 || // moeder niet op stallijst
	 	(!isset($ram_op_stal) && empty($keuzeRam))										 || // vader niet op stallijst
	 	(isset($status_mdr) && !isset($keuzeOoi))										 || // moeder is afgevoerd
	 	(isset($status_vdr) && !isset($keuzeRam))  					 						// vader is afgevoerd
	 												
	   )

	 {	$oke = 0;	} else {	$oke = 1;	} // $oke kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.
// EINDE Controleren of ingelezen waardes worden gevonden . 

	 if (isset($_POST['knpVervers_']) && $_POST["laatsteOke_$Id"] == 0 && $oke == 1) /* Als onvolledig is gewijzigd naar volledig juist */ {$cbKies = 1; $cbDel = $_POST["chbDel_$Id"]; }
else if (isset($_POST['knpVervers_'])) { $cbKies = $_POST["chbkies_$Id"];  $cbDel = $_POST["chbDel_$Id"]; } 
   else { $cbKies = $oke; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet ?>

<!--	**************************************
		**	   	 OPMAAK  GEGEVENS			**
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
 	<input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtDracdm_$Id"; ?> value = <?php echo $txtDatum; ?> >
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
  if ((!isset($_POST['knpVervers_']) && $mdrId == $key) || (isset($_POST["kzlOoi_$Id"]) && $_POST["kzlOoi_$Id"] == $key)){
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
  if ((!isset($_POST['knpVervers_']) && $ram_scan == $scanRaak[$i]) || (isset($_POST["kzlRam_$Id"]) && $_POST["kzlRam_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }	
			}
		
} ?>
 </select>
	<!-- EINDE KZLVADER -->
 </td>
 <td>
	<!-- KZLDRACHTIG -->
	<select name= <?php echo "kzlDracht_$Id"; ?> style="width:50; font-size:12px;" >
<?php
$opties = array('Nee', 'Ja');
foreach ( $opties as $key => $waarde)
{
   $keuze = '';
   if((!isset($_POST['knpVervers_']) && $drachtig == $key) || (isset($_POST["kzlDracht_$Id"]) && $_POST["kzlDracht_$Id"] == $key))
   {
   echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
 } else {
   echo '<option value="' . $key . '">' . $waarde . '</option>';
   }
} ?>
</select>
	<!-- EINDE KZLDRACHTIG -->
 </td>
 <td style = "font-size:12px; color:red;" >
<?php if(isset($worp_rd)) { echo 'Ooi '.$ooi_rd.' heeft afgelopen 183 al '.$worp_rd.' lammeren geworpen'; } 
else if(isset($ooi_rd) && !isset($ooi_op_stal) && !isset($keuzeOoi)) { echo 'Ooi '.$ooi_rd.' staat niet op de stallijst'; } 
else if(isset($ram_scan) && !isset($ram_op_stal) && !isset($keuzeRam)) { echo 'Ram (scan '.$ram_scan.') staat niet op de stallijst'; }
else if (isset($status_mdr) && !isset($keuzeOoi)) 					 { echo 'Ooi '.$ooi_rd.' is '.$status_mdr; }
else if (isset($status_vdr) && !isset($keuzeRam)) 					 { echo 'Ram '.$ram_rd.' is '.$status_vdr; }
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