<?php
$versie = '30-08-2025'; /* Gekopieerd van InsAfvoer.php. ActId 12 (zijnde afgeleverd) uit tabel tblActie wordt vanaf nu ook gebruikt om ubn te wijzigen. Zie InsGrWijzigingUbn.php. Als het nieuwe veld ubnId in tabel impAgrident leeg is dan is het een reguliere afvoer van een lam. Is het veld ubnId gevuld dan betreft het een wijziging van ubn van de gebruiker. Dus afvoer oude ubn en aanvoer nieuwe ubn in 1 handeling via deze pagina */


 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Registratie</title>
</head>
<body>

<?php
$titel = 'Inlezen Ubn wijziging';$subtitel = '';
$file = "InsGrWijzigingUbn.php";
include "login.php"; ?>

			<TD valign = "top">
<?php
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { 

include "vw_HistorieDm.php";

if ($modmeld == 1 ) { include "maak_request_func.php"; }

If (isset($_POST['knpInsert_'])) {

	include "post_readerUbn.php";#Deze include moet voor de verversing in de functie header()
	}
	
$velden = "rd.Id readId, rd.datum, right(rd.levensnummer,".mysqli_real_escape_string($db,$Karwerk).") werknr, rd.levensnummer levnr, rd.hokId hok_rd, u_best.ubn ubn_best, rel_best.naam bestemming, rel_best.relId rel_best, gewicht kg, s.schaapId, s.geslacht, u_herk.ubn ubn_herk, rel_herk.naam herkomst, rel_herk.relId rel_herk, ouder.datum dmaanw, lower(haf.actie) actie, haf.af, ho.hokId hok_db, date_format(max.datummax_afv,'%d-%m-%Y') maxdatum_afv, max.datummax_afv, date_format(max.datummax_kg,'%d-%m-%Y') maxdatum_kg, max.datummax_kg ";

$tabel = "
impAgrident rd
 join tblUbn u_best on (u_best.ubnId = rd.ubnId)
 left join (
 	SELECT u.ubnId, p.ubn, p.naam, r.relId
 	FROM tblUbn u
 	 left join tblPartij p on (p.ubn = u.ubn)
 	 left join tblRelatie r on (p.partId = r.partId)
 	WHERE u.lidId = '".mysqli_real_escape_string($db,$lidId)."' and p.lidId = '".mysqli_real_escape_string($db,$lidId)."' and (r.relatie = 'deb' or isnull(r.relatie))
  ) rel_best on (rd.ubnId = rel_best.ubnId)
 left join (
	SELECT s.schaapId, s.levensnummer, s.geslacht
	 FROM tblSchaap s
	  join tblStal st on (st.schaapId = s.schaapId)
	 WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	 GROUP BY s.schaapId, s.levensnummer, s.geslacht
 ) s on (s.levensnummer = rd.levensnummer)
 left join (
 	SELECT max(stalId) stalIdmax, schaapId
 	FROM tblStal
	WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
 	GROUP BY schaapId
 ) st_max on (st_max.schaapId = s.schaapId)
 left join tblStal st on (st.stalId = st_max.stalIdmax)
 left join tblUbn u_herk on (u_herk.ubnId = st.ubnId)
 left join (
 SELECT p.ubn, p.naam, r.relId
 	FROM tblPartij p
 	 left join tblRelatie r on (p.partId = r.partId)
 	WHERE p.lidId = '".mysqli_real_escape_string($db,$lidId)."' and (r.relatie = 'cred' or isnull(r.relatie))
 ) rel_herk on (u_herk.ubn = rel_herk.ubn)
 left join (
	SELECT st.schaapId, h.hisId, a.actie, a.af
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	 join tblActie a on (h.actId = a.actId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a.af = 1 and h.skip = 0
 ) haf on (s.schaapId = haf.schaapId)
 left join (
	SELECT st.schaapId, h.datum
	 FROM tblStal st
	  join tblHistorie h on (st.stalId = h.stalId)
	 WHERE h.actId = 3 and h.skip = 0
 ) ouder on (ouder.schaapId = s.schaapId)
 left join (
	SELECT ho.hokId
	FROM tblHok ho
	WHERE ho.lidId = '" . mysqli_real_escape_string($db,$lidId) . "'
 ) ho on (rd.hokId = ho.hokId)
 left join (
	SELECT schaapId, max(datum) datummax_afv, max(datum_kg) datummax_kg
	FROM (
		SELECT s.schaapId, h.datum, h.datum datum_kg, a.actie, h.actId, h.skip
		FROM tblSchaap s
		 join tblStal st on (st.schaapId = s.schaapId)
		 join tblHistorie h on (h.stalId = st.stalId)
		 join tblActie a on (a.actId = h.actId)
		WHERE a.actId = 1 and h.skip = 0 and s.levensnummer is not null

		Union

		SELECT s.schaapId, h.datum, h.datum datum_kg, a.actie, h.actId, h.skip
		FROM tblSchaap s
		 join tblStal st on (st.schaapId = s.schaapId)
		 join tblHistorie h on (h.stalId = st.stalId)
		 join tblActie a on (a.actId = h.actId)
		WHERE a.actId = 2 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'

		Union

		SELECT s.schaapId, h.datum, NULL datum_kg, a.actie, h.actId, h.skip
		FROM tblSchaap s
		 join tblStal st on (st.schaapId = s.schaapId)
		 join tblHistorie h on (h.stalId = st.stalId)
		 join tblActie a on (a.actId = h.actId)
		WHERE (a.actId = 5 or a.actId = 8 or a.actId = 9 or a.actId = 12 or a.actId = 13 or a.actId = 14) and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'

		Union

		SELECT s.schaapId, h.datum, NULL datum_kg, a.actie, h.actId, h.skip
		FROM tblSchaap s
		 join tblStal st on (st.schaapId = s.schaapId)
		 join tblHistorie h on (h.stalId = st.stalId)
		 join tblActie a on (a.actId = h.actId)
		 left join 
		 (
			SELECT s.schaapId, h.actId, h.datum 
		    FROM tblSchaap s
			 join tblStal st on (st.schaapId = s.schaapId)
			 join tblHistorie h on (h.stalId = st.stalId) 
		    WHERE actId = 2 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
		 ) koop on (s.schaapId = koop.schaapId and koop.datum <= h.datum)
		WHERE a.actId = 3 and h.skip = 0 and (isnull(koop.datum) or koop.datum < h.datum) and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'

		Union

		SELECT s.schaapId, h.datum, NULL datum_kg, a.actie, h.actId, h.skip
		FROM tblSchaap s
		 join tblStal st on (st.schaapId = s.schaapId)
		 join tblHistorie h on (h.stalId = st.stalId)
		 join tblActie a on (a.actId = h.actId)
		WHERE a.actId = 4 and h.skip = 0

		Union

		SELECT  mdr.schaapId, min(h.datum) datum, NULL datum_kg, 'Eerste worp' actie, NULL, 0 skip
		FROM tblSchaap mdr
		 join tblVolwas v on (mdr.schaapId = v.mdrId)
		 join tblSchaap lam on (v.volwId = lam.volwId)
		 join tblStal st on (st.schaapId = lam.schaapId)
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 1 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
		GROUP BY mdr.schaapId

		Union

		SELECT mdr.schaapId, max(h.datum) datum, NULL datum_kg, 'Laatste worp' actie, NULL, 0 skip
		FROM tblSchaap mdr
		 join tblVolwas v on (mdr.schaapId = v.mdrId)
		 join tblSchaap lam on (v.volwId = lam.volwId)
		 join tblStal st on (st.schaapId = lam.schaapId)
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 1 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
		GROUP BY mdr.schaapId, h.actId
		HAVING (max(h.datum) > min(h.datum))

		Union

		SELECT s.schaapId, p.dmafsluit datum, NULL datum_kg, 'Gevoerd' actie, NULL , h.skip
		FROM tblVoeding vd
		 join tblPeriode p on (p.periId = vd.periId)
		 join tblBezet b on (b.periId = p.periId)
		 join tblHistorie h on (h.hisId = b.hisId)
		 join tblStal st on (st.stalId = h.stalId)
		 join tblSchaap s on (s.schaapId = st.schaapId)
		WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' 
		GROUP BY s.schaapId, p.dmafsluit
	) sd
	GROUP BY schaapId
 ) max on (s.schaapId = max.schaapId)
";

$WHERE = "WHERE rd.lidId = '".mysqli_real_escape_string($db,$lidId)."' and rd.actId = 12 and isnull(rd.verwerkt) ";

include "paginas.php";

$data = $page_nums-> fetch_data($velden, "ORDER BY right(rd.levensnummer,".mysqli_real_escape_string($db,$Karwerk).") "); 

?>

<table border = 0>
<tr> <form action="InsGrWijzigingUbn.php" method = "post">
 <td colspan = 3 style = "font-size : 13px;">
  <input type = "submit" name = "knpVervers_" value = "Verversen"></td>
 <td colspan = 2 align = center style = "font-size : 14px;"><?php 
/*echo '<br>'; 
echo '$page_nums->total_pages : '.$page_nums->total_pages.'<br>'; 
echo '$page_nums->total_records : '.$page_nums->total_records.'<br>'; 
echo '$page_nums->rpp : '.$page_nums->rpp.'<br>'; */
echo /*'$page_numbers : '.*/$page_numbers/*.'<br> '.$record_numbers.'<br>'*/; 
/*echo '$page_nums->count_records() : '. $page_nums->count_records();*/ 
//echo '$page_nums->pagina_string : '. $page_nums->pagina_string; ?></td>
 <td colspan = 3 align = left style = "font-size : 13px;"> Regels Per Pagina: <?php echo $kzlRpp; ?> </td>
 <td align = 'right'> <input type = "submit" name = "knpInsert_" value = "Inlezen">&nbsp &nbsp </td>
 <td colspan = 2 style = "font-size : 12px;"><b style = "color : red;">!</b> = waarde uit reader niet gevonden. </td></tr>
<tr valign = bottom style = "font-size : 12px;">
 <th>Afvoeren<br><b style = "font-size : 10px;">Ja/Nee</b><br> <input type="checkbox" id="selectall" checked /> <hr></th>
 <th>Verwij-<br>deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
 <th>Wijzigdatum<hr></th>
 <th>Werknr<hr></th>
 <th>Levensnummer<hr></th>
<?php if($modtech == 1) { // Velden die worden getoond bij module technisch ?>
 <th>Gewicht<hr></th>
 <th>Verblijf<hr></th>
<?php } ?>
 <th>Generatie<hr></th>
 <th>Bestemming<hr></th>
 <th></th>
 <th>Herkomst<hr></th>
 <th></th>
 <th colspan = 2 > <a href="exportInsGrWijzigingUbn.php?pst=<?php echo $lidId; ?> "> Export-xlsx </a> <br><br><hr></th>
 <th ></th>
</tr>
<?php

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
   $hokRaak[$index] = $hknr['hokId'];
   $index++; 
} 
unset($index);
// EINDE Declaratie HOKNUMMER
}

if(isset($data))  {	foreach($data as $key => $array)
	{
unset($status);

		$var = $array['datum'];
$date = str_replace('/', '-', $var);
$datum = date('d-m-Y', strtotime($date));
$date	   = date('Y-m-d', strtotime($date));
	
	$Id = $array['readId'];
	$werknr = $array['werknr'];
	$levnr = $array['levnr'];
	$ubn_best = $array['ubn_best'];
	$bestemming = $array['bestemming'];
	$rel_best = $array['rel_best'];
	$kg = $array['kg'];
	$schaapId = $array['schaapId'];
	$geslacht = $array['geslacht'];
	$ubn_herk = $array['ubn_herk'];
	$herkomst = $array['herkomst'];
	$rel_herk = $array['rel_herk'];
	$hok_rd = $array['hok_rd'];
	$hok_db = $array['hok_db'];
	$dmaanw = $array['dmaanw']; if(isset($dmaanw)) { if($geslacht == 'ooi') {$fase = 'moederdier'; } else if($geslacht == 'ram') { $fase = 'vaderdier';} } 
								else { $fase = 'lam';}
	$status = $array['actie'];
	$af = $array['af']; if(isset($af) && $af == 1) { $status = $status; } else { $status = $fase; }
	$dmmax_bij_afvoer = $array['datummax_afv'];
	$dmmax_bij_wegen = $array['datummax_kg'];
	$maxdm_bij_afvoer = $array['maxdatum_afv'];
	$maxdm_bij_wegen = $array['maxdatum_kg'];


// Controleren of ingelezen waardes worden gevonden.
unset($onjuist);
unset($color);

if (isset($_POST['knpVervers_'])) { 
	$datum = $_POST["txtAfvoerdag_$Id"]; 
if(isset($_POST["txtKg_$Id"])) { $kg = $_POST["txtKg_$Id"]; } 
	$makeday = date_create($_POST["txtAfvoerdag_$Id"]); $date =  date_format($makeday, 'Y-m-d'); 
}

if(!isset($schaapId)) 					{ $color = 'red';  $onjuist = 'Levensnummer onbekend.'; }
else if(empty($datum))   				{ $color = 'red';  $onjuist = 'De datum onbekend.'; }
else if($status == 'afgeleverd')		{ $color = 'red';  $onjuist = 'Dit schaap is reeds '. $status. '.'; } 
else if($status == 'overleden' || $status == 'uitgeschaard')   { $color = 'red';  $onjuist = 'Dit schaap is '. $status. '.'; }
else if(isset($fase) && $date < $dmmax_bij_afvoer)   { $color = 'red';  $onjuist = 'Datum ligt voor $maxdm_bij_afvoer.'; }	 
else if($ubn_best == $ubn_herk) { $color = 'red';  $onjuist = 'Dit schaap staat al op ubn '.$ubn_best.'.'; }
else if(!isset($rel_best)) { $color = 'red';  $onjuist = 'Bestemming wordt niet gevonden als debiteur.'; }
else if(!isset($rel_herk)) { $color = 'red';  $onjuist = 'Herkomst wordt niet gevonden als crediteur.'; }

if	(isset($onjuist)) { $oke_afv = 0; } else { $oke_afv = 1; }  // $oke_afv kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.

// EINDE Controleren of ingelezen waardes worden gevonden . 

	 if (isset($_POST['knpVervers_']) && $_POST["laatsteOke_$Id"] == 0 && $oke_afv == 1) /* Als onvolledig is gewijzigd naar volledig juist wordt checkbox eenmalig automatisch aangevinkt */ {$cbKies = 1; $cbDel = $_POST["chbDel_$Id"]; }
else if (isset($_POST['knpVervers_'])) { $cbKies = $_POST["chbkies_$Id"];  $cbDel = $_POST["chbDel_$Id"]; } 
   else { $cbKies = $oke_afv; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet 


   //if(isset($_POST['knpVervers_'])) {} ?>

<!--	**************************************
		**	   	 OPMAAK  GEGEVENS			**
		************************************** -->

<tr style = "font-size:13px;">
 <td align = center> 

	<input type = checkbox 		  name = <?php echo "chbkies_$Id"; ?> value = 1 
	  <?php echo $cbKies == 1 ? 'checked' : ''; /* Als voorwaarde goed zijn of checkbox is aangevinkt */

	  if ($oke_afv == 0) /*Als voorwaarde niet klopt */ { ?> disabled <?php } else { ?> class="checkall" <?php } /* class="checkall" zorgt dat alles kan worden uit- of aangevinkt*/ ?> >
	<input type = hidden size = 1 name = <?php echo "laatsteOke_$Id"; ?> value = <?php echo $oke_afv; ?> > <!-- hiddden -->
 </td>
 <td align = center>
	<input type = checkbox class="delete" name = <?php echo "chbDel_$Id"; ?> value = 1 <?php if(isset($cbDel)) { echo $cbDel == 1 ? 'checked' : ''; } ?> >
 </td>

 <td>
	<input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtAfvoerdag_$Id"; ?> value = <?php echo $datum; ?> >
 </td>

<?php if(isset($schaapId)) { echo "<td align = center >".$werknr;} else { ?> <td align = center style = "color : red"> <?php echo $werknr;} ?>
 </td>

 <?php if(isset($schaapId)) { echo "<td>".$levnr;} else { ?> <td align = center style = "color : red"> <?php echo $levnr;} ?>
 </td>

<?php if($modtech == 1) { ?>	
 <td style = "font-size : 9px;"> 

	<input type = "text" size = 3 style = "font-size : 11px;" name = <?php echo "txtKg_$Id"; ?> value = <?php echo $kg; ?> >

 </td>

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
<?php } //Einde if($modtech == 1) ?>	
 <td width = 80 align = "center"><?php 
if (isset($status)) { echo $fase ;} ?>
 </td> 

 <td align="center"> <?php echo $ubn_best.' - '.$bestemming; ?> </td>
 <td width="10"></td>
 <td align="center"> <?php echo $ubn_herk.' - '.$herkomst; ?> </td>
 <td width="10"></td>

<!-- Foutmeldingen -->
 <td colspan = 2 width = 300 style = "color : <?php echo $color; ?>"> <?php
if (isset($onjuist)) { echo $onjuist; } ?>
 </td>	
 <td>	
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
