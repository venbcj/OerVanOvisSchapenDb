<?php 
$versie = '21-5-2020'; /*Gekopieerd van insOverplaats.php*/
$versie = '04-07-2020'; /* 1 tabel impAgrident gemaakt */
$versie = '31-12-2023'; /* sql beveiligd met quotes */
$versie = "11-03-2024"; /* Bij geneste query uit 
join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId) gewijzgd naar
join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
I.v.m. historie van stalId 22623. Dit dier is eerst verkocht en met terugwerkende kracht geplaatst in verblijf Afmest 1 */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = "top"> 31-12-24 Include "login.php"; voor Include "header.php" gezet */

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Registratie</title>
</head>
<body>

<?php
$titel = 'Inlezen Adoptie';
$file = "InsAdoptie.php";
Include "login.php"; ?>

				<TD valign = "top">
<?php
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { 

If (isset ($_POST['knpInsert_'])) {
	// 	Include "url.php"; Zit al in header.php
	Include "post_readerAdop.php"; #Deze include moet voor de vervversing in de functie header()
	//header("Location: ".$url."InsOverplaats.php");
	}

// Declaratie HOKNUMMER			// lower(if(isnull(scan),'6karakters',scan)) zorgt ervoor dat $raak nooit leeg is. Anders worden legen velden gevonden in legen velden binnen impReader.
$qryHoknummer = mysqli_query($db,"
SELECT hokId, hoknr, lower(if(isnull(scan),'6karakters',scan)) scan
FROM tblHok hb
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actief = 1
ORDER BY hoknr
") or die (mysqli_error($db)); 

$index = 0; 
while ($hnr = mysqli_fetch_array($qryHoknummer)) 
{ 
   $hoknId[$index] = $hnr['hokId']; 
   $hoknum[$index] = $hnr['hoknr'];
   $hokRaak[$index] = $hnr['hokId'];   
   $index++; 
} 
unset($index);
// EINDE Declaratie HOKNUMMER

$velden = "rd.Id, date_format(rd.datum,'%d-%m-%Y') datum, rd.datum sort, rd.levensnummer, rd.moeder,
s.schaapId,
mdr.schaapId mdr_db,
spn.schaapId spn, prnt.schaapId prnt, hg.datum gebdate";

$tabel = "
impAgrident rd
 left join tblSchaap s on (rd.levensnummer = s.levensnummer) 
 left join tblSchaap mdr on (rd.moeder = mdr.levensnummer)
 left join tblStal st on (st.schaapId = s.schaapId and isnull(st.rel_best))
 left join tblUbn u on (st.ubnId = u.ubnId and u.lidId = '".mysqli_real_escape_string($db,$lidId)."')
 left join (
	SELECT st.schaapId, h.datum
	FROM tblHistorie h
	 join tblStal st on (st.stalId = h.stalId)
	WHERE h.actId = 1
 ) hg on (hg.schaapId = s.schaapId)
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
";

$WHERE = "WHERE rd.lidId = '".mysqli_real_escape_string($db,$lidId)."' and rd.actId = 15 and isnull(rd.verwerkt) ";

include "paginas.php";

$data = $page_nums->fetch_data($velden, "ORDER BY sort, rd.Id");
 ?>
<table border = 0>
<tr> <form action="InsAdoptie.php" method = "post">
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
 <th>Adoptie<br>datum<hr></th>
 <th>Levensnummer<hr></th>
 <th>Moeder<hr></th>
 <th>Verblijf<hr></th>
 <th><hr></th>
 <th><hr></th>
</tr>
<?php

if(isset($data))  {	foreach($data as $key => $array)
	{
	$Id = $array['Id'];
	$datum = $array['datum'];
	$date = $array['sort'];
	$schaapId = $array['schaapId'];
	$levnr = $array['levensnummer']; if (strlen($levnr)== 11) {$levnr = '0'.$array['levensnummer'];}
	$moeder = $array['moeder'];
	$mdr_db = $array['mdr_db'];
	$spn = $array['spn'];		
	$prnt = $array['prnt'];
	$dmgeb = $array['gebdate'];

// VERBLIJF MOEDER zoeken
unset($stalId);
$zoek_stalId = mysqli_query($db,"
SELECT st.stalId
FROM tblStal st
 join tblUbn u on (u.ubnId = st.ubnId)
WHERE st.schaapId = '".mysqli_real_escape_string($db,$mdr_db)."' and u.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(st.rel_best)
");

while ($zs = mysqli_fetch_assoc($zoek_stalId)) { $stalId = $zs['stalId']; }

unset($hok_db);

if(isset($stalId)) {
$zoek_verblijf_mdr = mysqli_query($db,"
SELECT b.hokId
FROM (
	SELECT max(hisId) hisId
	FROM tblHistorie h
	 join tblActie a on (a.actId = h.actId)
	WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."' and a.aan = 1
 ) hin
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
	SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE st.stalId = '".mysqli_real_escape_string($db,$stalId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
WHERE isnull(uit.hist)

");

while ($zv = mysqli_fetch_assoc($zoek_verblijf_mdr)) { $hok_db = $zv['hokId']; }

}
// VERBLIJF MOEDER zoeken 


// Controleren of ingelezen waardes worden gevonden .
$dag = $datum ; $dmdag = $date; $kzlHok = $hok_db;
if (isset($_POST['knpVervers_'])) { $dag = $_POST["txtDag_$Id"]; $kzlHok = $_POST["kzlHok_$Id"]; 
	$makeday = date_create($_POST["txtDag_$Id"]); $dmdag =  date_format($makeday, 'Y-m-d');
}

	 If	 
	 ( !isset($schaapId)	|| /*levensnummer moet bestaan*/	
		 empty($dag)			|| # of datum is leeg
		 !isset($mdr_db)	|| # moeder bestaat niet
		 $dmdag < $dmgeb	|| # of datum ligt voor de laatst geregistreerde datum van het schaap
		 empty($kzlHok)		   # Verblijf is leeg 
	 											
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
	<?php echo $dag; ?>
 </td>

<?php if(!empty($schaapId)) { ?> <td> <?php echo $levnr; } else { ?> <td style = "color : red"> <?php echo $levnr;} ?>
 </td>

  <td><?php echo $moeder; ?>
 </td>	
  <td align = center>
<!-- KZLVERBLIJF -->
 <select style="width:65; font-size:12px;" name = <?php echo "kzlHok_$Id"; ?> >
  <option></option>
<?php
$count = count($hoknum);
for ($i = 0; $i < $count; $i++){

	$opties = array($hoknId[$i]=>$hoknum[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((!isset($_POST['knpVervers_']) && $hok_db == $hokRaak[$i]) || (isset($_POST["kzlHok_$Id"]) && $_POST["kzlHok_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}
?> </select>

 <!-- EINDE KZLVERBLIJF -->
</td>
 <td style = "color : red" align="center"><?php 
 		 if (empty($schaapId)) 		{ echo "Levensnummer onbekend"; }
 	else if (!isset($mdr_db)) 		{ echo "Moeder onbekend"; }
 	else if($dmdag < $dmgeb) { echo "Datum ligt voor de geboortedatum ."; }
 ?>
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