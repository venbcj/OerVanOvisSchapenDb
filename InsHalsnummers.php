<?php 
$versie = '30-9-2020'; /* Gekopieerd van insOmnummeren.php */
$versie = '16-5-2021'; /* sql beveiligd met quotes */

 session_start(); ?>
<html>
<head>
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Registratie</title>
</head>
<body>

<center>
<?php
$titel = 'Inlezen Halsnummers';
$subtitel = '';
Include "header.php"; ?>
	<TD width = 960 height = 400 valign = "top">
<?php
$file = "InsHalsnummers.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { 

if (isset ($_POST['knpInsert_'])) {
	
	Include "post_readerHalsnum.php";
	
	}


$velden = "rd.Id, date_format(rd.datum,'%d-%m-%Y') datum, rd.datum sort, rd.levensnummer, rd.kleur, rd.halsnr,
st.kleur kleur_db, st.halsnr halsnr_db,
lower(h.actie) actie, h.af, date_format(h.datum,'%d-%m-%Y') maxdatum, h.datum datummax";

$tabel = "
impAgrident rd
 left join (
	 SELECT max(h.hisId) hisId, s.schaapId, s.levensnummer, s.geslacht
	 FROM tblSchaap s
	  join tblStal st on (st.schaapId = s.schaapId)
	  join tblHistorie h on (st.stalId = h.stalId)
	 WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.skip = 0
	 GROUP BY s.schaapId, s.levensnummer, s.geslacht
 ) s on (rd.levensnummer = s.levensnummer)
 
 left join tblStal st on (st.schaapId = s.schaapId and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(st.rel_best))
 left join (
	SELECT h.hisId, a.actie, a.af, h.datum
	FROM tblHistorie h
	 join tblActie a on (h.actId = a.actId)
 ) h on (h.hisId = s.hisId)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 14 and h.skip = 0
 ) hu on (hu.schaapId = s.schaapId)
";

$WHERE = "WHERE rd.lidId = '".mysqli_real_escape_string($db,$lidId)."' and rd.actId = 1717 and isnull(rd.verwerkt) ";

include "paginas.php";

$data = $page_nums->fetch_data($velden, "ORDER BY sort, rd.Id");
 ?>
<table border = 0>
<tr> <form action="InsHalsnummers.php" method = "post">
 <td colspan = 2 style = "font-size : 13px;">
  <input type = "submit" name = "knpVervers_" value = "Verversen"></td>
 <td colspan = 2 align = center style = "font-size : 14px;"><?php 
echo $page_numbers; ?></td>
 <td colspan = 3 align = left style = "font-size : 13px;"> Regels Per Pagina: <?php echo $kzlRpp; ?> </td>
 <td colspan = 3 align = 'right'><input type = "submit" name = "knpInsert_" value = "Inlezen">&nbsp &nbsp </td>
 <td colspan = 2 style = "font-size : 12px;"><b style = "color : red;">!</b> = waarde uit reader niet gevonden. </td></tr>
<tr valign = bottom style = "font-size : 12px;">
 <th>Inlezen<br><b style = "font-size : 10px;">Ja/Nee</b><br> <input type="checkbox" id="selectall" checked /> <hr></th>
 <th>Verwij-<br>deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
 <th>datum<hr></th>
 <th>Levensnummer<hr></th>
 <th>Kleur<hr></th>
 <th>nummer<hr></th>
 <th>Huidige halsnummer<hr></th>
 <th><hr></th>
</tr>
<?php

if(isset($data))  {	foreach($data as $key => $array)
	{
	$Id = $array['Id'];
	$datum = $array['datum'];
	$date = $array['sort'];
	$levnr = $array['levensnummer']; if (strlen($levnr)== 11) {$levnr = '0'.$array['levensnummer'];}
	$kleur = $array['kleur']; 
	$halsnr = $array['halsnr'];
	$kleur_db = $array['kleur_db']; 
	$halsnr_db = $array['halsnr_db']; 
	$status = $array['actie']; 
	$af = $array['af'];	 
	$maxdm = $array['maxdatum'];
	$dmmax = $array['datummax'];


// Controleren of ingelezen waardes worden gevonden .
$dag = $datum ; $dmdag = $date;

if (isset($_POST['knpVervers_'])) { $dag = $_POST["txtDag_$Id"]; 
	$makeday = date_create($_POST["txtDag_$Id"]); $dmdag =  date_format($makeday, 'Y-m-d');
	$kleur = $_POST["kzlKleur_$Id"];
	$halsnr = $_POST["txtHalsnr_$Id"];
}

$zoek_halsnr_db = mysqli_query($db,"
SELECT schaapId
FROM tblStal
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and kleur = '".mysqli_real_escape_string($db,$kleur)."' and halsnr = ".mysqli_real_escape_string($db,$halsnr)." and isnull(rel_best)
") or die (mysqli_error($db));
	while ($zh = mysqli_fetch_assoc($zoek_halsnr_db)) { $halsnummer_db = $zh['schaapId']; }

	 If	 
	 ( ((isset($af) && $af == 1) || !isset($status))	|| /*levensnummer moet bestaan*/	
		 empty($dag)				|| # of datum is leeg
		 empty($kleur)				|| # of kleur is leeg
		 empty($halsnr)				|| # of halsnr is leeg
		 isset($halsnummer_db)		|| # halsnummer is al ingebruik
		 $dmdag < $dmmax			 # of datum ligt voor de laatst geregistreerde datum van het schaap
	 											
	 )
	 {	$oke = 0;	} else {	$oke = 1;	} // $oke kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.
// EINDE Controleren of ingelezen waardes worden gevonden .  

	 if (isset($_POST['knpVervers_']) && $_POST["laatsteOke_$Id"] == 0 && $oke == 1) /* Als onvolledig is gewijzigd naar volledig juist */ {$cbKies = 1; $cbDel = $_POST["chbDel_$Id"]; }
else if (isset($_POST['knpVervers_'])) { $cbKies = $_POST["chbkies_$Id"];  $cbDel = $_POST["chbDel_$Id"]; } 
   else { $cbKies = $oke; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet ?>


<!--	**************************************
		**	   	 OPMAAK  GEGEVENS			**
		************************************** -->

<tr style = "font-size:13px;">
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
	<input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtDag_$Id"; ?> value = <?php echo $dag; ?> >
 </td>

<?php if(!empty($status)) { ?> <td> <?php echo $levnr; } else { ?> <td style = "color : red"> <?php echo $levnr;} ?>
 </td>

 <td>
 <!-- KZLKLEUR --> 
<select <?php echo " name=\"kzlKleur_$Id\" "; ?> style="width:70; font-size:13px;">

<?php /* echo "$row[geslacht]";*/
$opties = array('' => '', 'Blauw' => 'Blauw', 'Geel' => 'Geel', 'Groen' => 'Groen', 'Rood' => 'Rood', 'Wit' => 'Wit');
foreach ( $opties as $key => $waarde)
{
   if((!isset($_POST['knpVervers_']) && $kleur == $key) || (isset($_POST["kzlKleur_$Id"]) && $_POST["kzlKleur_$Id"] == $key) ) {
   echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
 } else {
   echo '<option value="' . $key . '">' . $waarde . '</option>';
   }
}

	?> </select> <!-- EINDE KZLKLEUR -->
 </td>	

 <td> <input type = "text" size = 5 style = "font-size : 11px;" name = <?php echo "txtHalsnr_$Id"; ?> value = <?php echo $halsnr; ?> >
 </td>	
 <td align = center >
 	<?php echo $kleur_db.' '.$halsnr_db; ?>
 </td>
 <td style = "color : red"><center><?php 
 		 if (empty($status)) 		{ echo "Onbekend levensnummer"; }
 	else if (empty($kleur)) 		{ echo "Kleur is onbekend"; }
 	else if (empty($halsnr)) 		{ echo "Halsnummer is onbekend"; }
 	else if (isset($halsnummer_db))	{ echo "Dit halsnummer is al in gebruik"; }
 	else if(isset($af) && $af == 1) { echo 'Dit dier is '. $status; } 
 ?> </center>
	<input type = "hidden" size = 8 style = "font-size : 9px;" name = <?php echo "txtStatus_$Id"; ?> value = <?php echo $status; ?> > <!--hiddden-->
 </td>
 <td style = "color : red"> <?php 
 unset($halsnummer_db);
if($dmdag < $dmmax) { echo "Datum ligt voor $maxdm ."; } ?>
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