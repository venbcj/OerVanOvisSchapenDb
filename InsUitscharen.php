<?php

require_once("autoload.php");


$versie = '03-11-2024'; /* kopie gemaakt van InsAfvoer.php */
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
$titel = 'Inlezen Uitscharen';
$file = "InsUitscharen.php";
include "login.php"; ?>

			<TD valign = "top">
<?php
if (is_logged_in()) { 

include "vw_HistorieDm.php";

if ($modmeld == 1 ) { include "maak_request_func.php"; }

If (isset($_POST['knpInsert_'])) {

	include "post_readerUitsch.php";#Deze include moet voor de verversing in de functie header()
	}
	
$velden = "rd.Id readId, rd.datum, right(rd.levensnummer,".mysqli_real_escape_string($db,$Karwerk).") werknr, rd.levensnummer levnr, rd.ubn ubn_afv, r.ubn ctrubn, rd.reden redId_rd, s.schaapId, s.geslacht, ouder.datum dmaanw, lower(haf.actie) actie, haf.af, ak.datum dmaankoop, date_format(max.datummax_afv,'%d-%m-%Y') maxdatum_afv, max.datummax_afv, b.bezId ";

$tabel = "
impAgrident rd
 left join (
	SELECT st.stalId, s.schaapId, s.levensnummer, s.geslacht
	 FROM tblSchaap s
	  join tblStal st on (st.schaapId = s.schaapId)
	 WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	 GROUP BY st.stalId, s.schaapId, s.levensnummer, s.geslacht
 ) s on (s.levensnummer = rd.levensnummer)
 join (
 	SELECT max(stalId) stalId, schaapId
 	FROM tblStal
 	WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
 	GROUP BY schaapId
 ) st on (s.stalId = st.stalId)
 left join (
	SELECT st.stalId, st.schaapId, h.hisId, a.actie, a.af
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	 join tblActie a on (h.actId = a.actId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a.af = 1 and h.skip = 0
 ) haf on (s.stalId = haf.stalId)
 left join (
	SELECT st.schaapId, h.datum
	 FROM tblStal st
	  join tblHistorie h on (st.stalId = h.stalId)
	 WHERE h.actId = 3 and h.skip = 0
 ) ouder on (ouder.schaapId = s.schaapId)
 left join (
	SELECT levensnummer, max(datum) datum 
	FROM tblSchaap s
	 join tblStal st on (st.schaapId = s.schaapId)
	 join tblHistorie h on (h.stalId = st.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = 2 and h.skip = 0
	GROUP BY levensnummer
 ) ak on (ak.levensnummer = rd.levensnummer)
 left join (
	SELECT schaapId, max(datum) datummax_afv
	FROM (
		SELECT s.schaapId, h.datum, a.actie, h.actId, h.skip
		FROM tblSchaap s
		 join tblStal st on (st.schaapId = s.schaapId)
		 join tblHistorie h on (h.stalId = st.stalId)
		 join tblActie a on (a.actId = h.actId)
		WHERE a.actId = 1 and h.skip = 0 and s.levensnummer is not null

		Union

		SELECT s.schaapId, h.datum, a.actie, h.actId, h.skip
		FROM tblSchaap s
		 join tblStal st on (st.schaapId = s.schaapId)
		 join tblHistorie h on (h.stalId = st.stalId)
		 join tblActie a on (a.actId = h.actId)
		WHERE a.actId = 2 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'

		Union

		SELECT s.schaapId, h.datum, a.actie, h.actId, h.skip
		FROM tblSchaap s
		 join tblStal st on (st.schaapId = s.schaapId)
		 join tblHistorie h on (h.stalId = st.stalId)
		 join tblActie a on (a.actId = h.actId)
		WHERE (a.actId = 5 or a.actId = 8 or a.actId = 9 or a.actId = 12 or a.actId = 13 or a.actId = 14) and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'

		Union

		SELECT s.schaapId, h.datum, a.actie, h.actId, h.skip
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

		SELECT s.schaapId, h.datum, a.actie, h.actId, h.skip
		FROM tblSchaap s
		 join tblStal st on (st.schaapId = s.schaapId)
		 join tblHistorie h on (h.stalId = st.stalId)
		 join tblActie a on (a.actId = h.actId)
		WHERE a.actId = 4 and h.skip = 0

		Union

		SELECT  mdr.schaapId, min(h.datum) datum, 'Eerste worp' actie, NULL, 0 skip
		FROM tblSchaap mdr
		 join tblVolwas v on (mdr.schaapId = v.mdrId)
		 join tblSchaap lam on (v.volwId = lam.volwId)
		 join tblStal st on (st.schaapId = lam.schaapId)
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 1 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
		GROUP BY mdr.schaapId

		Union

		SELECT mdr.schaapId, max(h.datum) datum, 'Laatste worp' actie, NULL, 0 skip
		FROM tblSchaap mdr
		 join tblVolwas v on (mdr.schaapId = v.mdrId)
		 join tblSchaap lam on (v.volwId = lam.volwId)
		 join tblStal st on (st.schaapId = lam.schaapId)
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 1 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
		GROUP BY mdr.schaapId, h.actId
		HAVING (max(h.datum) > min(h.datum))

		Union

		SELECT s.schaapId, p.dmafsluit datum, 'Gevoerd' actie, NULL , h.skip
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
 left join (
	SELECT p.lidId, p.ubn
	FROM tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	WHERE p.actief = 1 and r.relatie = 'deb' and r.actief = 1
 ) r on(r.ubn = rd.ubn and r.lidId = rd.lidId)
 left join (
	SELECT max(b.bezId) bezId, s.levensnummer
	FROM tblBezet b
	 join tblHistorie h on (b.hisId = h.hisId)
	 join tblStal st on (h.stalId = st.stalId)
	 join tblSchaap s on (st.schaapId = s.schaapId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.skip = 0
	GROUP BY s.levensnummer
 ) b on (rd.levensnummer = b.levensnummer)
";

$WHERE = "WHERE rd.lidId = '".mysqli_real_escape_string($db,$lidId)."' and rd.actId = 10 and isnull(rd.verwerkt) ";

include "paginas.php";

$data = $page_nums-> fetch_data($velden, "ORDER BY right(rd.levensnummer,".mysqli_real_escape_string($db,$Karwerk).") "); 

?>

<table border = 0>
<tr> <form action="InsUitscharen.php" method = "post">
 <td colspan = 3 style = "font-size : 13px;">
  <input type = "submit" name = "knpVervers_" value = "Verversen"></td>
 <td colspan = 2 align = "center" style = "font-size : 14px;"><?php 
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
 <th>Afvoer<br>datum<hr></th>
 <th>Werknr<hr></th>
 <th>Levensnummer<hr></th>
 <th>Bestemming<hr></th>
 <th>Generatie<hr></th>
 <th>Wachtdagen<br>resterend<hr></th>
 <th colspan = 2 > <a href="exportInsUitscharen.php?pst=<?php echo $lidId; ?> "> Export-xlsx </a> <br><br><hr></th>
 <th ></th>
</tr>
<?php

// Declaratie BESTEMMING			// lower(if(isnull(ubn),'6karakters',ubn)) zorgt ervoor dat $raak nooit leeg is. Anders worden legen velden gevonden in legen velden binnen impReader.
$qryRelatie = ("SELECT r.relId, '6karakters' ubn, concat(p.ubn, ' - ', p.naam) naam
			FROM tblPartij p join tblRelatie r on (p.partId = r.partId)	
			WHERE p.lidId = '".mysqli_real_escape_string($db,$lidId)."' and relatie = 'deb' and p.actief = 1 and r.actief = 1
				  and isnull(p.ubn)
			union
			
			SELECT r.relId, p.ubn, concat(p.ubn, ' - ', p.naam) naam
			FROM tblPartij p
			 join tblRelatie r on (p.partId = r.partId)	
			WHERE p.lidId = '".mysqli_real_escape_string($db,$lidId)."' and relatie = 'deb' and p.actief = 1 and r.actief = 1 
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
// Einde Declaratie BESTEMMING

if(isset($data))  {	foreach($data as $key => $array)
	{
		$var = $array['datum'];
$date = str_replace('/', '-', $var);
$datum = date('d-m-Y', strtotime($date));
$date	   = date('Y-m-d', strtotime($date));
	
	$Id = $array['readId'];
	$werknr = $array['werknr'];
	$levnr = $array['levnr'];
	$ubnbest = $array['ubn_afv'];
	$ubn_db = $array['ctrubn'];
	$redId_rd = $array['redId_rd'];
	$schaapId = $array['schaapId'];
	$geslacht = $array['geslacht'];
	$dmaanw = $array['dmaanw']; if(isset($dmaanw)) { if($geslacht == 'ooi') {$fase = 'moederdier'; } else if($geslacht == 'ram') { $fase = 'vaderdier';} } 
								else { $fase = 'lam';}
	$status = $array['actie'];
	$af = $array['af']; if(isset($af) && $af == 1) { $status = $status; }
	$aank = $array['dmaankoop'];
	$bezet = $array['bezId'];
	$dmmax_bij_afvoer = $array['datummax_afv'];
	$maxdm_bij_afvoer = $array['maxdatum_afv'];

// Controleren of ingelezen waardes worden gevonden .
 $kzlRelatie = $ubn_db; 
if (isset($_POST['knpVervers_'])) { 
	$datum = $_POST["txtAfvoerdag_$Id"]; 
	$kzlRelatie = $_POST["kzlBest_$Id"]; 
	$makeday = date_create($_POST["txtAfvoerdag_$Id"]); $date =  date_format($makeday, 'Y-m-d'); 
}

// t.b.v. checkbox Afvoeren
	 If	 
	 ( !isset($schaapId) || isset($status) || /*levensnummer moet aanwezig zijn */
		 empty($datum)							|| # of datum is leeg
		 $date < $dmmax_bij_afvoer							|| # of datum ligt voor de laatst geregistreerde datum van het schaap
		 //($modtech == 1 && !isset($aank) && !isset($bezet))		|| # aankoopdatum ontbreekt van dieren die niet in een hok hebben gezeten.
		 //$status == 'afgeleverd'	15-2-19 : dubbel benoemd			|| # of is reeds afgeleverd
		// $status == 'overleden'	15-2-19 : dubbel benoemd			|| # of is reeds overleden
		empty($kzlRelatie)  		 		   # bestemming is onbekend						 
	 											
	 )
	 {	$oke_afv = 0;	} else { $oke_afv = 1;	} // $oke_afv kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.
// Einde t.b.v. checkbox Afvoeren

// EINDE Controleren of ingelezen waardes worden gevonden . 

	 if (isset($_POST['knpVervers_']) && $_POST["laatsteOke_$Id"] == 0 && $oke_afv == 1) /* Als onvolledig is gewijzigd naar volledig juist wordt checkbox eenmalig automatisch aangevinkt */ {$cbKies = 1; $cbDel = $_POST["chbDel_$Id"]; }
else if (isset($_POST['knpVervers_'])) { $cbKies = $_POST["chbkies_$Id"];  $cbDel = $_POST["chbDel_$Id"]; } 
   else { $cbKies = $oke_afv; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet 


   //if(isset($_POST['knpVervers_'])) {} ?>

<!--	**************************************
		**	   	 OPMAAK  GEGEVENS			**
		************************************** -->

<tr style = "font-size:13px;">
 <td align = "center"> 

	<input type = checkbox 		  name = <?php echo "chbkies_$Id"; ?> value = 1 
	  <?php echo $cbKies == 1 ? 'checked' : ''; /* Als voorwaarde goed zijn of checkbox is aangevinkt */

	  if ($oke_afv == 0) /*Als voorwaarde niet klopt */ { ?> disabled <?php } else { ?> class="checkall" <?php } /* class="checkall" zorgt dat alles kan worden uit- of aangevinkt*/ ?> >
	<input type = hidden size = 1 name = <?php echo "laatsteOke_$Id"; ?> value = <?php echo $oke_afv; ?> > <!-- hiddden -->
 </td>
 <td align = "center">
	<input type = checkbox class="delete" name = <?php echo "chbDel_$Id"; ?> value = 1 <?php if(isset($cbDel)) { echo $cbDel == 1 ? 'checked' : ''; } ?> >
 </td>

 <td>
	<input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtAfvoerdag_$Id"; ?> value = <?php echo $datum; ?> >
 </td>

<?php if(isset($schaapId)) { ?> <td align = "center" > <?php }
	 										else { ?> <td align = "center" style = "color : red"> <?php } 
echo $werknr; ?>
 </td>

 <?php if(isset($schaapId)) { ?> <td> <?php }
											 else { ?> <td align = "center" style = "color : red"> <?php } 
echo $levnr; ?>
 </td>

 <td >

<!-- KZLBESTEMMING -->
 <select style="width:145; font-size:12px;" name = <?php echo "kzlBest_$Id"; ?> >
  <option></option>
<?php	$count = count($relnum);
for ($i = 0; $i < $count; $i++){

	$opties = array($relnId[$i]=>$relnum[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((!isset($_POST['knpVervers_']) && $ubnbest == $relRaak[$i]) || (isset($_POST["kzlBest_$Id"]) && $_POST["kzlBest_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}
?> </select>
<?php if( $ubnbest<> NULL && empty($ubn_db) && empty($_POST["kzlBest_$Id"]) ) {echo $ubnbest; ?> <b style = "color : red;"> ! </b>  <?php } ?>
	</td> <!-- EINDE KZLBESTEMMING -->


 <td width = 80 align="center"> <?php 
if (isset($status)) { echo $fase ;} ?>
 </td> <?php
// Wachtdagen bepalen
if(isset($schaapId)) {
$zoek_pil = mysqli_query($db,"
SELECT date_format(h.datum,'%d-%m-%Y') datum, art.naam, DATEDIFF( (h.datum + interval art.wdgn_v day), '".mysqli_real_escape_string($db,$date)."') resterend
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblActie a on (a.actId = h.actId)
 left join tblNuttig n on (h.hisId = n.hisId)
 left join tblInkoop i on (i.inkId = n.inkId)
 left join tblArtikel art on (i.artId = art.artId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = 8 and h.skip = 0
 and '".mysqli_real_escape_string($db,$date)."' < (h.datum + interval art.wdgn_v day)
") or die (mysqli_error($db));	

$vandaag = date('Y-m-d');
		while($row = mysqli_fetch_array($zoek_pil))
		{ $pildm = $row['datum']; 
		  $pil = $row['naam']; 
		  $wdgn_v = $row['resterend']; }
}
// Einde Wachtdagen bepalen
?>
 <td align = "center" ><?php if(isset($wdgn_v)) { echo $wdgn_v; } ?></td>
<!-- Foutmeldingen -->
 <td colspan = 2 width = 300 style = "color : red"> <?php
	if(!isset($schaapId)) 					  { echo 'Levensnummer onbekend.'; }
	else if( isset($status))   { echo "Dit schaap is reeds $status."; } 
	else if(isset($fase) && $date < $dmmax_bij_afvoer)   { echo "Datum ligt voor $maxdm_bij_afvoer."; } 
	else if(isset($wdgn_v)) { echo $pildm.' - '.$pil; } unset($wdgn_v);
	//else if($modtech == 1 && !isset($aank) && !isset($bezet)) { echo "Dit schaap heeft nog geen aankoopdatum."; } 
	unset($status); ?>
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
