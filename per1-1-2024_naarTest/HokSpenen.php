<?php /* 21-11-2015 Individueel spenen gewijzigd naar heel hok spenen 
23-11-2015 breedte kzlHok flexibel gemaakt via login.php
20-1-2017 $hok_uitgez = 'Geboren' gewijzigd in $hok_uitgez = 1 Speengewicht niet verplicht gemaakt */
$versie = "23-1-2017"; /* 22-1-2017 tblBezetting gewijzigd naar tblBezet 23-1-2017 kalender toegevoegd */
$versie = "6-2-2017"; /* Aanpassing n.a.v. verblijven met verschillende doelgroepen */
$versie = '21-05-2018';  /* Meerdere pagina's gemaakt	16-6 : $hokkeuze laten bestaan na spenen eerste pagina */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '13-4-2019'; /* Volwassendieren kunnen ook uit een verblijf worden gehaald 22-4 javascript alles-selecteren gaat noet samen met javascript kalender vandaar dat include kalender.php niet altijd plaats vindt */
$versie = '20-12-2019'; /* tabelnaam gewijzigd van UIT naar uit tabelnaam */
$versie = '24-4-2020'; /* Controle op speendag is kleiner dan laatste registratiedatum gewijzigd naar speendag is kleiner dan aanvoerdatum 26-4 : txtMindatum weggehaald */
$versie = '24-4-2020'; /* url Javascript libary aangepast */
$versie = '30-12-2023'; /* and h.skip = 0 toegevoegd aan tblHistorie en sql beveiligd met quotes */

 session_start(); ?>
<html>
<head>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Registratie</title>
</head>
<body>

<center>
<?php 

$titel = 'Spenen';
$subtitel = '';
Include "header.php";
?>
		<TD width = 940 height = 400 valign = "top">
<?php 
$file = "HokkenBezet.php";
Include "login.php";
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {

if(isset($_GET['pstId']))	{ $_SESSION["ID"] = $_GET['pstId']; } $ID = $_SESSION["ID"]; /* zorgt het Id wordt onthouden bij het opnieuw laden van de pagina */
if(isset($_POST['knpVerder_']) && isset($_POST['kzlHokall_']))	{ 
	$datum = $_POST['txtDatumall_']; $_SESSION["DT1"] = $datum;
	$hokkeuze = $_POST['kzlHokall_']; $_SESSION["BST"] = $hokkeuze; } 
 else { $hokkeuze = $_SESSION["BST"];  } $sess_dag = $_SESSION["DT1"]; $sess_bestm = $_SESSION["BST"];

if(isset($_POST['knpSave_'])) { include "save_spenen.php"; }

$zoek_hok = mysqli_query ($db,"
SELECT hoknr
FROM tblHok
WHERE hokId = '".mysqli_real_escape_string($db,$ID)."'
") or die (mysqli_error($db));
	while ($h = mysqli_fetch_assoc($zoek_hok)) { $hoknr = $h['hoknr']; }
	
/*$zoek_nu_in_verblijf_geb = mysqli_query($db,"
SELECT count(b.bezId) aantin
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join 
 (
	SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE b.hokId = '".mysqli_real_escape_string($db,$ID)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4
 ) spn on (spn.schaapId = st.schaapId)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$ID)."' and isnull(uit.bezId)
and isnull(spn.schaapId)
and isnull(prnt.schaapId)
") or die (mysqli_error($db));
		
	while($nu1 = mysqli_fetch_assoc($zoek_nu_in_verblijf_geb))
		{ $nu = $nu1['aantin']; }*/

	
	//while($rij = mysqli_fetch_assoc($nu_in_hok))	{ $nu = $rij['nu']; $hoknr = $rij['hoknr'];}
	// Als laatste schaap is gespeend
	/*if(!isset($hoknr)) { $hokken = mysqli_query($db,"SELECT h.hoknr FROM tblHok h WHERE hokId = '$Id' ") or die(mysqli_error($db)); 
							while (	$hk = mysqli_fetch_assoc($hokken)) { $hoknr = $hk['hoknr']; }	}*/
	// Einde Als laatste schaap is gespeend	
// Declaratie HOKNUMMER KEUZE
$qryHokkeuze = mysqli_query($db,"
SELECT hokId, hoknr
FROM tblHok
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actief = 1
ORDER BY hoknr

") or die (mysqli_error($db)); 

$index = 0; 
while ($hnr = mysqli_fetch_array($qryHokkeuze)) 
{ 
   $hoknId[$index] = $hnr['hokId']; 
   $hoknum[$index] = $hnr['hoknr'];
   $hokRaak[$index] = $hnr['hokId']; 
   $index++; 
} 
unset($index);
// EINDE Declaratie HOKNUMMER  KEUZE ?>

<form action="HokSpenen.php" method = "post"><?php

$aantal_volwassen_dieren = mysqli_query($db,"
SELECT count(*) aant
FROM tblSchaap s 
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblBezet b on (b.hisId = h.hisId)
left join 
(
		SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
		FROM tblBezet b
		 join tblHistorie h1 on (b.hisId = h1.hisId)
		 join tblActie a1 on (a1.actId = h1.actId)
		 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
		 join tblActie a2 on (a2.actId = h2.actId)
		 join tblStal st on (h1.stalId = st.stalId)
		WHERE b.hokId = '".mysqli_real_escape_string($db,$ID)."' and a1.aan = 1
		 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
		GROUP BY b.bezId, st.schaapId, h1.hisId
) uit on (uit.bezId = b.bezId)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 join (
	SELECT st.schaapId, h.datum, h.actId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$ID)."' and isnull(uit.bezId) and h.skip = 0 and prnt.schaapId is not null
") or die (mysqli_error($db));

while ( $av = mysqli_fetch_assoc($aantal_volwassen_dieren)) {
	
	$volwas = $av['aant'];
}

if(isset($_POST['knpVerder_']) && isset($_POST['radVolw']) && ($_POST['radVolw'] == 1 || $_POST['radVolw'] == 2)) {
$fiter = "WHERE b.hokId = '".mysqli_real_escape_string($db,$ID)."' and isnull(uit.bezId) and h.skip = 0 and (isnull(spn.schaapId) or prnt.schaapId is not null)";
}
else
{
$fiter = "WHERE b.hokId = '".mysqli_real_escape_string($db,$ID)."' and isnull(uit.bezId) and h.skip = 0 and isnull(spn.schaapId) and isnull(prnt.schaapId)";
}
// Opbouwen paginanummering 
$velden = "s.schaapId, s.levensnummer, date_format(max(h.datum),'%Y-%m-%d') dmlst, date_format(max(h.datum),'%d-%m-%Y') lstdm, h.actId, prnt.actId nr, s.geslacht ";

$tabel = "tblSchaap s 
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblBezet b on (b.hisId = h.hisId)
 left join 
 (
		SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
		FROM tblBezet b
		 join tblHistorie h1 on (b.hisId = h1.hisId)
		 join tblActie a1 on (a1.actId = h1.actId)
		 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
		 join tblActie a2 on (a2.actId = h2.actId)
		 join tblStal st on (h1.stalId = st.stalId)
		WHERE b.hokId = '".mysqli_real_escape_string($db,$ID)."' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a1.aan = 1
		 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
		GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.bezId = b.bezId)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
	SELECT st.schaapId, h.datum, h.actId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)";

 $WHERE = $fiter ;

include "paginas.php";

$data = $page_nums->fetch_data($velden, "GROUP BY s.schaapId, s.levensnummer ORDER BY prnt.actId, s.levensnummer"); 
// Einde Opbouwen paginanummering
if(!isset($sess_dag) && !isset($sess_bestm)) { $width = 100; } 
else { $width = 200; } ?>
<table border = 0 > <!-- tabel1 --> <tr> <td>
<table border = 0 > <!-- tabel2 -->
<tr> 
<td width = <?php echo $width; ?> rowspan = 2 style = "font-size : 18px;">
  <b> <?php echo $hoknr; ?></b>
</td>
 <?php if(!isset($sess_dag) && !isset($sess_bestm)) { 
 include "kalender.php";	?>
 <td width="420" align="right">Optioneel een datum voor alle schapen 
 </td>
 <td width = 450 style = "font-size : 14px;"> 
  <input id = "datepicker1" type = text name = 'txtDatumall_' size = 8 value = <?php if(isset($sess_dag)) { echo $sess_dag; } ?> > &nbsp 
 <?php } else { ?> <td style = "font-size : 14px;">  <?php } ?>
<!-- Opmaak paginanummering -->
 Regels Per Pagina: <?php echo $kzlRpp;
if(isset($sess_dag) || isset($sess_bestm)) { ?> </td> <td align = center > <?php echo $page_numbers.'<br>'; ?> </td> <td> <?php } 
// Einde Opmaak paginanummering ?>
 </td>
 <td width = 150 align = center>
<?php if(!isset($sess_dag) && !isset($sess_bestm)) { ?>
  &nbsp &nbsp &nbsp <input type = submit name = "knpVerder_" value = "Verder">
</td>
 <td width = 200 align = 'right'></td>
   <?php }
else { ?>
  <input type = submit name = "knpVervers_" value = "Verversen"> 
 </td>
 <td width = 200 align = 'right'>
  <input type = submit name = "knpSave_" value = "Spenen">&nbsp &nbsp
 </td> <?php } ?>
</tr>

<tr><td align = right >
 <?php if(!isset($sess_dag) && !isset($sess_bestm)) { ?>
 
  Optioneel een bestemming voor alle schapen</td><td>
 <!-- KZLVERBLIJF KEUZE-->
 <select style="width:<?php echo $w_hok; ?>;" name= 'kzlHokall_' value = "" style = "font-size:12px;">
  <option></option>
<?php
$count = count($hoknum);
for ($i = 0; $i < $count; $i++){

	$opties = array($hoknId[$i]=>$hoknum[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((isset($_POST['kzlHokall_']) && $_POST['kzlHokall_'] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}
?> </select> &nbsp

 <!-- EINDE KZLVERBLIJF KEUZE -->
 <?php } ?> 
</td></tr>
<?php if($volwas > 0 && !isset($sess_dag) && !isset($sess_bestm)) { ?>
<tr height="30" valign="bottom">
 
 <td colspan="2" align="right" >Opties voor volwassendieren </td>
 <td > 
 	<input type="radio" name="radVolw" value="1" <?php if(!isset($_POST['knpToon']) || $_POST['radHok'] == 1) { echo "checked"; } ?> > Uit het verblijf halen </td>
</tr>
<tr>
 <td colspan="2"> </td>
 <td > 
 	<input type="radio" name="radVolw" value="2" <?php if(isset($_POST['radHok']) && $_POST['radHok'] == 2) { echo "checked"; } ?> > Overplaatsen </td>
</tr>
<tr>
 <td colspan="2"></td>
 <td > 
 	<input type="radio" name="radVolw" value="3" <?php if(isset($_POST['radHok']) && $_POST['radHok'] == 3) { echo "checked"; } ?> > In verblijf laten zitten </td>
</tr>
<?php } ?>
</table> <!-- einde tabel2 --> </td> </tr>
								<tr> <td>
<table border = 0 align = left > <!-- tabel3 --> 
<?php if(isset($sess_dag) || isset($sess_bestm)) { ?>
<tr valign = bottom style = "font-size : 12px;">
<th>Spenen<br><b style = "font-size : 10px;">Ja/Nee</b><br> <input type="checkbox" id="selectall" checked /> <hr></th>
<th>Speendatum<hr></th>
<th>Levensnummer<hr></th>
<th>Gewicht<hr></th>
<th>naar verblijf<hr></th>
<th>status<hr></th>
<th colspan = 3 ><hr></th>
</tr>
<?php
if(isset($data)) {
	foreach($data as $key => $array)
	{
		$Id = $array['schaapId'];
		$levnr = $array['levensnummer'];
		$dmmax = $array['dmlst'];
		$maxdm = $array['lstdm'];
		$actId = $array['actId'];
		$nr = $array['nr'];
		$sekse = $array['geslacht']; if(isset($nr)) { if($sekse == 'ooi'){ $status = 'moederdier'; } else { $status = 'vaderdier'; } } else { $status = 'lam'; }



if( (isset($_POST['knpVervers_']) || isset($_POST['knpSave_']) ) && !isset($_POST['kzlHokall_']) ) { 
	$cbKies = $_POST["chbkies_$Id"];
	$datum = $_POST["txtDatum_$Id"];
	$kg = $_POST["txtKg_$Id"];
	if(!empty($_POST["kzlHok_$Id"])) { $hokkeuze = $_POST["kzlHok_$Id"]; } /*Na spenen en bij tonen van volgende hoeveelheid dieren is $_POST["kzlHok_$Id"] leeg maar $bestkeuze moet blijven bestaan */
}
// Bij de eerste keer openen van deze pagina bestaat als enigste keer het veld kzlHokall_ . knpVervers_ bestaat als hidden veld. txtDatum_$levnr en txtGewicht_$levnr bestaan dan nog niet. Variabalen $datum en $kg kunnen enkel worden gevuld als wordt voldaan aan (isset($_POST['knpVervers_']) && !isset($_POST['kzlHokall_']))  !!!
	if(!isset($datum) && isset($sess_dag)) { $datum = $sess_dag; }
	if(isset($datum)) /*$datum kan al bestaan voor isset($_POST['knpVervers_']) */ { $makeday = date_create($datum); $day = date_format($makeday,'Y-m-d'); }
	
// Controleren of ingelezen waardes correct zijn.
	if( empty($datum)				|| # Speendatum is leeg
		($day < $dmmax && ($actId == 1 || $actId == 2 || $actId == 11))				|| # speendag is kleiner dan aanvoerdatum
		//empty($kg)					|| # Speengweicht is leeg	per 20-1-2017 niet meer verplicht
		empty($hokkeuze) # Hok is leeg
	)
	{$oke = 0; } else { $oke = 1; }
	 
// EINDE Controleren of ingelezen waardes corretc zijn.  
if (isset($_POST['knpVervers_']) && !isset($_POST['kzlHokall_'])) { $cbKies = $_POST["chbkies_$Id"]; $txtOke = $_POST["txtOke_$Id"]; } else { $cbKies = $oke; $txtOke = $oke; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet ?>

<!--	**************************************
	**	   OPMAAK  GEGEVENS		**
	************************************** -->

<tr style = "font-size:14px;">
	<td align = center>
	<input type = hidden size = 1 name = <?php echo "txtOke_$Id"; ?> 	value = <?php echo $oke; ?> ><!--hiddden Dit veld zorgt ervoor dat chbkies wordt aangevinkt als het ingebruk wordt gesteld -->
	<input type = hidden size = 1 name = <?php echo "chbkies_$Id"; ?> value = 0 > <!-- hiddden -->
	<input type = checkbox 		  name = <?php echo "chbkies_$Id"; ?> 	value = 1 <?php echo $cbKies == 1 ? 'checked' : ''; 
	if ($oke == 0) { ?> disabled <?php }  else { ?> class="checkall" <?php } /* class="checkall" zorgt dat alles kan worden uit- of aangevinkt*/  /*else if ($txtOke == 0) {	echo 'checked';}*/ ?> >
</td>
<!-- Speendatum -->
<td align = center>
 <input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtDatum_$Id"; ?> value = <?php if(isset($datum)) { echo $datum; } ?> >
</td>

<td width = 110 align = center> <?php echo $levnr; ?>
</td>
	



<?php if(isset($_POST['radVolw']) && $_POST['radVolw'] == 1 && $status != 'lam') { ?>
 <td colspan = 2 align = center > 
<?php 	echo $hoknr. ' verlaten';
 
  }
else { ?>
<td width = 80 align = center style = "font-size : 9px;"> 
<input type = "text" size = 3 style = "font-size : 11px;" name = <?php echo "txtKg_$Id"; ?> value = <?php if(isset($kg)) { echo $kg; } ?> > </td>
<td width = 100 align = center>

<!-- KZLVERBLIJF -->
 <select style="width:<?php echo $w_hok; ?>;" name= <?php echo "kzlHok_$Id"; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php
$count = count($hoknum);
for ($i = 0; $i < $count; $i++){

	$opties = array($hoknId[$i]=>$hoknum[$i]);
			foreach($opties as $key => $waarde)
			{
  if (( $hokkeuze == $hokRaak[$i]) || (isset($_POST["kzlHok_$Id"]) && $_POST["kzlHok_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}
?> </select>

 <!-- EINDE KZLVERBLIJF -->
	
<?php } ?>
 </td>
 <td align="center"> 
<?php echo $status;
?>
 </td>

 <td colspan = 3 style = "color : red"> 
<?php if($day < $dmmax && ($actId == 1 || $actId == 2 || $actId == 11)) { echo 'De datum mag niet voor '.$maxdm.' liggen.';}
?>
</td>	
</tr>
<!--	**************************************
	**	EINDE OPMAAK GEGEVENS	**
	************************************** -->

<?php } 
	 } // Einde if(isset($data))
	  } ?>
</table> <!-- Einde tabel3 --> </td> </tr>
</table> <!-- Einde tabel1 -->
</form> 


</TD>
<?php	
Include "menu1.php"; } ?>
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