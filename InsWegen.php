<?php 
$versie = '3-9-2017';  /* aangemaakt */
$versie = '20-3-2018';  /* Meerdere pagina's gemaakt 12-5-2018 : if(isset($data)) toegevoegd. Als alle records zijn verwerkt bestaat $data nl. niet meer !! */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '20-1-2019'; /* alles aan- en uitzetten met javascript */
$versie = '7-3-2019'; /* gewicht gedeeld door 100 ipv 10 */
$versie = '24-4-2020'; /* url Javascript libary aangepast */

 session_start();?>  
<html>
<head>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Registratie</title>
</head>
<body>

<center>
<?php
$titel = 'Inlezen Wegingen';
$subtitel = '';
Include "header.php"; ?>
	<TD width = 960 height = 400 valign = "top">
<?php 
$file = "InsWegen.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {


If (isset($_POST['knpInsert_']))  {
	Include "url.php";
	Include "post_readerWgn.php"; #Deze include moet voor de vervversing in de functie header()
}

// Aantal nog in te lezen WEGINGEN
$wegingen = mysqli_query ($db,"
SELECT count(datum) aant
from impReader 
where lidId = ".mysqli_real_escape_string($db,$lidId)." and teller_sp is not NULL and levnr_weeg is not null and isnull(verwerkt) 
") or die (mysqli_error($db));
 While ($rec_wgn = mysqli_fetch_assoc($wegingen))
 {	$aantwg = $rec_wgn['aant'];	}
// EINDE Aantal nog in te lezen WEGINGEN

$velden = "str_to_date(rd.datum,'%d/%m/%Y') sort , rd.datum, rd.readId, s.schaapId, rd.levnr_weeg levnr, round((rd.weegkg/100),2) kg,
 s.levensnummer, s.geslacht,
 lstday.datum dmafv, lower(lstday.actie) actie, ouder.datum dmaanw";

$tabel = "
impReader rd
 left join (
	 select max(h.hisId) hisId, s.schaapId, s.levensnummer, s.geslacht
	 from tblSchaap s
	  join tblStal st on (st.schaapId = s.schaapId)
	  join tblHistorie h on (st.stalId = h.stalId)
	 where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and h.skip = 0
	 group by s.schaapId, s.levensnummer, s.geslacht
 ) s on (rd.levnr_weeg = s.levensnummer)
 left join (
	select hisId, actie, af
	from tblHistorie h
	 join tblActie a on (h.actId = a.actId)
 ) h on (h.hisId = s.hisId)
 left join (
	select st.schaapId, h.datum
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 3 and h.skip = 0
 ) ouder on (ouder.schaapId = s.schaapId)
 
 left join (
	select s.levensnummer, h.datum, a.actie
	from tblSchaap s 
	 join tblStal st on (st.schaapId = s.schaapId)
	 join tblHistorie h on (st.stalId = h.stalId)
	 join tblActie a on (h.actId = a.actId)
	where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and s.levensnummer is not null and h.skip = 0 and a.af = 1 
 ) lstday on (lstday.levensnummer = rd.levnr_weeg )
";

$WHERE = "where rd.lidId = ".mysqli_real_escape_string($db,$lidId)." and rd.teller_sp is not null and levnr_weeg is not null and isnull(rd.verwerkt)  ";

include "paginas.php";

$data = $page_nums->fetch_data($velden, "ORDER BY sort, rd.readId"); ?>

<table border = 0>
<tr> <form action="InsWegen.php" method = "post">
 <td colspan = 2 style = "font-size : 13px;">
  <input type = "submit" name = "knpVervers_" value = "Verversen"></td>
 <td colspan = 2 align = center style = "font-size : 14px;"><?php 
echo $page_numbers; ?></td>
 <td colspan = 3 align = left style = "font-size : 13px;"> Regels Per Pagina: <?php echo $kzlRpp; ?> </td>
 <td align = 'right'><input type = "submit" name = "knpInsert_" value = "Inlezen">&nbsp &nbsp </td>
 <td colspan = 2 style = "font-size : 12px;"><b style = "color : red;">!</b> = waarde uit reader niet gevonden. </td></tr>
<tr valign = bottom style = "font-size : 12px;">
 <th>Inlezen<br><b style = "font-size : 10px;">Ja/Nee</b><br> <input type="checkbox" id="selectall" checked /> <hr></th>
 <th>Verwij-<br>deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
 <th>Weeg<br>datum<hr></th>
 <th>Levensnummer<hr></th>
 <th>Gewicht<hr></th>
 <th>Generatie<hr></th>
 <th colspan = 2 ><hr></th>
</tr>
<?php

/*
$vw_Reader_wg = "
select str_to_date(rd.datum,'%d/%m/%Y') sort , rd.datum, rd.readId, s.schaapId, rd.levnr_weeg levnr, round((rd.weegkg/100),2) kg,

 s.levensnummer, s.geslacht,

 lstday.datum dmafv, lower(lstday.actie) actie, ouder.datum dmaanw

from impReader rd
 left join (
	 select max(h.hisId) hisId, s.schaapId, s.levensnummer, s.geslacht
	 from tblSchaap s
	  join tblStal st on (st.schaapId = s.schaapId)
	  join tblHistorie h on (st.stalId = h.stalId)
	 where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and h.skip = 0
	 group by s.schaapId, s.levensnummer, s.geslacht
 ) s on (rd.levnr_weeg = s.levensnummer)
 left join (
	select hisId, actie, af
	from tblHistorie h
	 join tblActie a on (h.actId = a.actId)
 ) h on (h.hisId = s.hisId)
 left join (
	select st.schaapId, h.datum
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 3 and h.skip = 0
 ) ouder on (ouder.schaapId = s.schaapId)
 
 left join (
	select s.levensnummer, h.datum, a.actie
	from tblSchaap s 
	 join tblStal st on (st.schaapId = s.schaapId)
	 join tblHistorie h on (st.stalId = h.stalId)
	 join tblActie a on (h.actId = a.actId)
	where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and s.levensnummer is not null and h.skip = 0 and a.af = 1 
 ) lstday on (lstday.levensnummer = rd.levnr_weeg )

where rd.lidId = ".mysqli_real_escape_string($db,$lidId)." and rd.teller_sp is not null and levnr_weeg is not null and isnull(rd.verwerkt) 

order by sort, readId LIMIT 30
 ";

$query = mysqli_query($db,$vw_Reader_wg) or die (mysqli_error($db));


	while($row = mysqli_fetch_assoc($query))*/
if(isset($data))  {	foreach($data as $key => $array)
	{
		$var = $array['datum'];
$date = str_replace('/', '-', $var);
$datum = date('d-m-Y', strtotime($date));
$dm	   = date('Y-m-d', strtotime($date));
	
	$Id = $array['readId'];
	$schaapId = $array['schaapId'];
	$levnr = $array['levnr'];
	$levnr_exist = $array['levensnummer'];
	$kg = $array['kg'];
	$geslacht = $array['geslacht'];
	$dmaanw = $array['dmaanw']; if(isset($dmaanw)) { if($geslacht == 'ooi') {$fase = 'moederdier'; } else if($geslacht == 'ram') { $fase = 'vaderdier';} } 
								else { $fase = 'lam';} 
	$actie = $array['actie']; if(isset($actie)) { $status = $array['actie']; }
	$dmafv = $array['dmafv']; if(isset($dmafv))	{ $afvdm = date('d-m-Y', strtotime($dmafv)); } // weeg datum mag niet na afvoerdatum liggen


// Controleren of ingelezen waardes correct zijn.
if (isset($_POST['knpVervers_'])) { $datum = $_POST["txtWeegdag_$Id"]; $kg = $_POST["txtKg_$Id"];
	$makeday = date_create($_POST["txtWeegdag_$Id"]); $dm =  date_format($makeday, 'Y-m-d');
}

	 If	 
	 (	!isset($schaapId)				|| # levensnummer is onbekend
	 	empty($datum)					|| # of datum is leeg
	 	empty($kg)						|| # of gewicht is leeg
		(isset($dmafv) && $dm > $dmafv)	   # of datum ligt na afvoerdatum
	 											
	 )
	 {	$oke = 0;	} else {	$oke = 1;	} // $oke kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.
// EINDE Controleren of ingelezen waardes corretc zijn.  

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
	<input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtWeegdag_$Id"; ?> value = <?php echo $datum; ?> >
 </td>
 
 <td> <?php echo $levnr; ?> 
 </td>
	
 <td style = "font-size : 9px;"> 
	<input type = "text" size = 3 style = "font-size : 11px;" name = <?php echo "txtKg_$Id"; ?> value = <?php echo $kg; ?> > </td>

 <td align="center"> <?php echo $fase; ?> 
 </td>

	

 <td width = 200 style = "color : red">
<!-- Foutmeldingen --> <?php 
	 if (!isset($schaapId)) 							{ echo "Levensnummer onbekend";}
else if(empty($kg)) 									{ echo "Gewicht is onbekend."; } 
else if(isset($dmafv) && $dm > $dmafv) 					{ echo "Datum ligt na $afvdm ."; } ?>
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