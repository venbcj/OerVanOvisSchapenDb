<?php 
$versie = '7-5-2014'; /*voorwaarde levensnummer moet bestaan mag ook geen moeder zijn daardoor 'empty($status) || $status == 'Overleden' ' gewijzigd in*/
$versie = '9-5-2014'; /*voorwaarde "Overplaatsing niet mogelijk. Zie status." uitgebreid met !empty($status). Empty is nl al "levensnummer onbekend"
	        voorwaarde "Datum ligt voor $maxdm ." uitgebreid met $status == 'lam'. Deze voorwaarde geldt immers enkel bij een lam en bijv. niet bij een moederdier.*/
$versie = '30-5-2014'; /*Bij $cntr_hok is de doelgroep varaibel gemaakt.*/
$versie = '8-8-2014'; /*quoutes bij "$status" weggehaald*/
$versie = '23-11-2014'; /*functie header() toegevoegd. In de header wordt het vervevrsen van de pagina verstuurd (request =. response) naar de server 
8-3-2015 : Login toegevoegd */
$versie = '23-11-2016'; /* actId = 3 uit on clause gehaald en als sub query genest.   vw_StatusSchaap verwijderd en gebaseerd op laatste hisId */
$versie = '20-1-2017'; /* $hok_uitgez gewijzigd van Geboren naar 1 en Gespeend naar 2 */
$versie = "22-1-2017"; /* tblBezetting gewijzigd naar tblBezet */
$versie = "12-2-2017"; /* Overplaatsen naar alle actieve hokken mogelijk gemaakt */
$versie = "7-3-2017"; /* subquery hokbezetting (alias hb) verwijderd in query $result */
$versie = '20-3-2018';  /* Meerdere pagina's gemaakt 12-5-2018 : if(isset($data)) toegevoegd. Als alle records zijn verwerkt bestaat $data nl. niet meer !! */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '20-1-2019'; /* alles aan- en uitzetten met javascript */
$versie = '24-4-2020'; /* url Javascript libary aangepast */
$versie = '4-7-2020'; /* 1 tabel impAgrident gemaakt */
$versie = '31-12-2023'; /* and h.skip = 0 toegevoegd bij tblHistorie en sql beveiligd met quotes */
$versie = '10-03-2024'; /* Keuzelijst verblijf breder gemaakt van width:65 naar width:84 */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Registratie</title>
</head>
<body>

<?php
$titel = 'Inlezen Overplaatsen';
$file = "InsOverplaats.php";
include "login.php"; ?>

			<TD valign = "top">
<?php
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { 

If (isset ($_POST['knpInsert_'])) {
	include "post_readerOvp.php"; #Deze include moet voor de vervversing in de functie header()
	//header("Location: ".$url."InsOverplaats.php");
	}


if($reader == 'Agrident') {
$velden = "rd.Id readId, str_to_date(rd.datum,'%Y-%m-%d') sort , rd.datum, rd.verwerkt, rd.levensnummer levnr, rd.hokId hok_rd, hb.hokId hok_db,
rs.Id readId_sp,
h.actie status, h.af, spn.schaapId spn, prnt.schaapId prnt, s.geslacht, date_format(h.datum,'%d-%m-%Y') maxdatum, h.datum datummax";

$tabel = "
impAgrident rd
 left join (
	SELECT levensnummer, Id 
	FROM impAgrident 
	WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actId = 4 and isnull(verwerkt)
 ) rs on (rd.levensnummer = rs.levensnummer)
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
	WHERE h.skip = 0
 ) h on (h.hisId = s.hisId)
 left join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = s.schaapId)
 left join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 14 and h.skip = 0
 ) hu on (hu.schaapId = s.schaapId)
 left join (
	SELECT hokId
	FROM tblHok
	WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actief = 1
 ) hb on (rd.hokId = hb.hokId)
";

$WHERE = "WHERE rd.lidId = '".mysqli_real_escape_string($db,$lidId)."' and rd.actId = 5 and isnull(rd.verwerkt) and isnull(rs.Id) ";

include "paginas.php";

$data = $page_nums->fetch_data($velden, "ORDER BY sort, rd.Id");
}
else {
$velden = "rd.readId, str_to_date(rd.datum,'%d/%m/%Y') sort , rd.datum, rd.verwerkt, rd.levnr_ovpl levnr, rd.hok_ovpl hok_rd, hb.scan hok_db, 
rs.readId readId_sp,
h.actie status, h.af, spn.schaapId spn, prnt.schaapId prnt, s.geslacht, date_format(h.datum,'%d-%m-%Y') maxdatum, h.datum datummax";

$tabel = "
impReader rd
 left join (
	SELECT levnr_sp, readId 
	FROM impReader 
	WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and teller_sp is not null and isnull(verwerkt)
 ) rs on (rd.levnr_ovpl = rs.levnr_sp)
 left join (
	 SELECT max(h.hisId) hisId, s.schaapId, s.levensnummer, s.geslacht
	 FROM tblSchaap s
	  join tblStal st on (st.schaapId = s.schaapId)
	  join tblHistorie h on (st.stalId = h.stalId)
	 WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.skip = 0
	 GROUP BY s.schaapId, s.levensnummer, s.geslacht
 ) s on (rd.levnr_ovpl = s.levensnummer)
 
 left join tblStal st on (st.schaapId = s.schaapId and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(st.rel_best))
 left join (
	SELECT h.hisId, a.actie, a.af, h.datum
	FROM tblHistorie h
	 join tblActie a on (h.actId = a.actId)
	WHERE h.skip = 0
 ) h on (h.hisId = s.hisId)
 left join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = s.schaapId)
 left join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 14 and h.skip = 0
 ) hu on (hu.schaapId = s.schaapId)
 left join (
	SELECT scan
	FROM tblHok
	WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actief = 1
 ) hb on (rd.hok_ovpl = hb.scan)
";

$WHERE = "WHERE rd.lidId = '".mysqli_real_escape_string($db,$lidId)."' and rd.teller_ovpl is not null and isnull(rd.verwerkt) and isnull(rs.readId) ";

include "paginas.php";

$data = $page_nums->fetch_data($velden, "ORDER BY sort, rd.readId");
}
 ?>
<table border = 0>
<tr> <form action="InsOverplaats.php" method = "post">
 <td colspan = 2 style = "font-size : 13px;">
  <input type = "submit" name = "knpVervers_" value = "Verversen"></td>
 <td colspan = 2 align = "center" style = "font-size : 14px;"><?php 
echo $page_numbers; ?></td>
 <td colspan = 3 align = left style = "font-size : 13px;"> Regels Per Pagina: <?php echo $kzlRpp; ?> </td>
 <td colspan = 3 align = 'right'><input type = "submit" name = "knpInsert_" value = "Inlezen">&nbsp &nbsp </td>
 <td colspan = 2 style = "font-size : 12px;"><b style = "color : red;">!</b> = waarde uit reader niet gevonden. </td></tr>
<tr valign = bottom style = "font-size : 12px;">
 <th>Inlezen<br><b style = "font-size : 10px;">Ja/Nee</b><br> <input type="checkbox" id="selectall" checked /> <hr></th>
 <th>Verwij-<br>deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
 <th>Overplaats<br>datum<hr></th>
 <th>Levensnummer<hr></th>
 <th>Naar verblijf<hr></th>
 <th>Doelgroep<hr></th>
 <th><hr></th>
 <th><hr></th>
</tr>
<?php

// Declaratie HOKNUMMER 			// lower(if(isnull(scan),'6karakters',scan)) zorgt ervoor dat $raak nooit leeg is. Anders worden legen velden gevonden in legen velden binnen impReader.
$qryHoknummer = mysqli_query($db,"
SELECT h.hokId, hoknr, lower(coalesce(scan,'6karakters')) scan
FROM tblHok h
WHERE h.lidId = '".mysqli_real_escape_string($db,$lidId)."'
ORDER BY hoknr
") or die (mysqli_error($db)); 

$index = 0; 
while ($hnr = mysqli_fetch_array($qryHoknummer)) 
{ 
   $hoknId[$index] = $hnr['hokId'];
   $hoknum[$index] = $hnr['hoknr'];
   $hokRaak[$index] = $hnr['scan'];   if($reader == 'Agrident') { $hokRaak[$index] = $hnr['hokId']; }
   $index++; 
} 
unset($index);
// EINDE Declaratie HOKNUMMER

if(isset($data))  {	foreach($data as $key => $array)
	{
		$var = $array['datum'];
$date = str_replace('/', '-', $var);
$datum = date('d-m-Y', strtotime($date));
$dm	   = date('Y-m-d', strtotime($date));
	
	$Id = $array['readId'];
	$levnr = $array['levnr']; if (strlen($levnr)== 11) {$levnr = '0'.$array['levnr'];}
	$hok_rd = $array['hok_rd'];
	$hok_db = $array['hok_db'];
	$geslacht = $array['geslacht'];
	$spn = $array['spn'];		if(isset($spn)) { $doelgr = 'Gespeend'; }
	$prnt = $array['prnt'];	if(isset($prnt)) { $doelgr = 'Aanwas'; } if(!isset($doelgr)) { $doelgr = 'Geboren'; }
	$status = $array['status']; 
	$af = $array['af']; 
	$maxdm = $array['maxdatum'];
	$dmmax = $array['datummax'];


// Controleren of ingelezen waardes worden gevonden .
$dag = $datum ; $dmdag = $dm; $kzlHok = $hok_db;
if (isset($_POST['knpVervers_'])) { $dag = $_POST["txtOvpldag_$Id"]; $kzlHok = $_POST["kzlHok_$Id"]; 
	$makeday = date_create($_POST["txtOvpldag_$Id"]); $dmdag =  date_format($makeday, 'Y-m-d');
}

	 If	 
	 ( ((isset($af) && $af == 1) || !isset($status))	|| /*levensnummer moet bestaan*/	
		 empty($dag)				|| # of datum is leeg
		 $dmdag < $dmmax			|| # of datum ligt voor de laatst geregistreerde datum van het schaap
		 empty($kzlHok) 			   # of hok is onbekend of leeg						 
	 											
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
	<input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtOvpldag_$Id"; ?> value = <?php echo $dag; ?> >
 </td>

<?php if(!empty($status)) { ?> <td> <?php echo $levnr; } else { ?> <td style = "color : red"> <?php echo $levnr;} ?>
 </td>
 <td>

<!-- KZLHOKNR -->
<?php  ?>
 <select style="width:84; font-size:12px;" name = <?php echo "kzlHok_$Id"; ?> >
  <option></option>
<?php
$count = count($hoknum);
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

?> </select> 
<?php 




if( isset($hok_rd) && empty($hok_db) && empty($_POST["kzlHok_$Id"]) && $levnr > 0 ) {echo $hok_rd; ?> <b style = "color : red;"> ! </b>  <?php } ?>
 </td> <!-- EINDE KZLHOKNR -->
 <td><?php echo $doelgr ; ?>
 </td>	
 <td style = "color : red" align="center"> 
 <?php 
 		 if (empty($status)) 		{ echo "Levensnummer onbekend"; } 
 	else if(isset($af) && $af == 1) { echo $status; } ?>

	<input type = "hidden" size = 8 style = "font-size : 9px;" name = <?php echo "txtStatus_$Id"; ?> value = <?php echo $status; ?> > <!--hiddden-->
 </td>
 <td style = "color : red"> <?php 
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
