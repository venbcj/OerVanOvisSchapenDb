<?php 
$versie = '23-12-2019'; /* Gekopieerd van HokAfleveren.php */
$versie = '31-12-2023'; /* and h.skip = 0 aangevuld aan tblHistorie en sql beveiligd */

  session_start(); ?>
<html>
<head>
<title>Registratie</title>
</head>
<body>

<center>
<?php
include"kalender.php";
/*$paginanaam = $_SERVER['PHP_SELF']; 
	if($paginanaam == '/LoslopersAfleveren.php') { $pagina = 'Afleveren'; }
	if($paginanaam == '/LoslopersVerkopen.php') { $pagina = 'Verkopen'; }
$titel = $pagina;*/
$titel = 'Verkopen';
$subtitel = '';
Include "header.php";
?>
		<TD width = 940 height = 400 valign = "top">
<?php 
$file = "Bezet.php";
Include "login.php";
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {

if ($modmeld == 1 ) { include "maak_request_func.php"; }


if(isset($_POST['knpVerder_']) && isset($_POST['kzlRelall_']))	{ 
	$datum = $_POST['txtDatumall_']; $_SESSION["DT1"] = $datum;
	$bestkeuze = $_POST['kzlRelall_']; $_SESSION["BST"] = $bestkeuze; }
 else { $bestkeuze = $_SESSION["BST"]; } $sess_dag = $_SESSION["DT1"]; $sess_bestm = $_SESSION["BST"];

if(isset($_POST['knpSave_'])) { $actId = 13; include "save_afleveren.php"; }

// Declaratie RELATIE KEUZE
$qryRelatiekeuze = mysqli_query($db,"SELECT r.relId, p.naam
			FROM tblPartij p
			 join tblRelatie r on (r.partId = p.partId)
			WHERE p.lidId = '".mysqli_real_escape_string($db,$lidId)."' and r.relatie = 'deb' and p.actief = 1 and r.actief = 1
			ORDER BY p.naam") or die (mysqli_error($db)); 

$index = 0; 
while ($rel = mysqli_fetch_array($qryRelatiekeuze)) 
{ 
   $relId[$index] = $rel['relId']; 
   $relnm[$index] = $rel['naam'];
   $relRaak[$index] = $rel['relId']; 
   $index++; 
} 
unset($index);
// EINDE Declaratie RELATIE  KEUZE ?> 

<form action="LoslopersVerkopen.php" method = "post"> 
<?php
// Opbouwen paginanummering 
$velden = "s.schaapId, right(s.levensnummer,".mysqli_real_escape_string($db,$Karwerk).") werknr, s.levensnummer, date_format(h.datum,'%Y-%m-%d') dmlst, date_format(h.datum,'%d-%m-%Y') lstdm";

$tabel = "tblSchaap s
 join (
	SELECT st.schaapId, max(hisId) hisId
	FROM tblStal st 
	 join tblHistorie h on (st.stalId = h.stalId)
	 join tblActie a on (a.actId = h.actId) 
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(st.rel_best) and a.aan = 1 and h.skip = 0
	GROUP BY st.schaapId
 ) hin on (hin.schaapId = s.schaapId)
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
	SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
 left join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = hin.schaapId)
 left join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = hin.schaapId)
 join (
	SELECT st.schaapId, max(hisId) hisId
	FROM tblStal st 
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(st.rel_best) and h.skip = 0
	GROUP BY st.schaapId
 ) hmax on (hmax.schaapId = s.schaapId)
 join tblHistorie h on (hmax.hisId = h.hisId)";

 $WHERE = "WHERE (isnull(b.hokId) or uit.hist is not null) and prnt.schaapId is not null";

include "paginas.php";

$data = $page_nums->fetch_data($velden, "ORDER BY right(s.levensnummer,".mysqli_real_escape_string($db,$Karwerk).")"); 
// Einde Opbouwen paginanummering 
if(!isset($sess_dag) && !isset($sess_bestm)) { $width = 100; } 
else { $width = 200; } ?>
<table border = 0 > <!-- tabel1 --> <tr> <td>
<table border = 0 > <!-- tabel2 -->
<tr> 
<td width = <?php echo $width; ?> rowspan = 2 style = "font-size : 18px;">
  <b> <?php echo $hoknr; ?></b>
</td>

 <?php if(!isset($sess_dag) && !isset($sess_bestm)) { ?>
 	<td width = 750 style = "font-size : 14px;"> 
 &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp Optioneel een datum voor alle schapen 
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
  <input type = submit name = "knpSave_" value = "Verkopen" >&nbsp &nbsp
 </td> <?php } ?>
</tr>

<tr><td colspan = 7 align = left >
 <?php if(!isset($sess_dag) && !isset($sess_bestm)) { ?>
 Optioneel een bestemming voor alle schapen 
 <!-- KZLBESTEMMING KEUZE-->
 <select style="width:150;" name= 'kzlRelall_' value = "" style = "font-size:12px;">
  <option></option>
<?php
$count = count($relnm);
for ($i = 0; $i < $count; $i++){

	$opties = array($relId[$i]=>$relnm[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((isset($_POST['kzlRelall_']) && $_POST['kzlRelall_'] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}
?> </select> &nbsp

 <!-- EINDE KZLBESTEMMING KEUZE -->
 <?php } ?> 
</td><td></td></tr>
</table> <!-- einde tabel2 --> </td> </tr>
								<tr> <td>
<table border = 0 id="myTable2" align = left > <!-- tabel3 --> 
<?php if(isset($sess_dag) || isset($sess_bestm)) { ?>
<tr valign = bottom style = "font-size : 12px;">
<th>Verkopen<br><b style = "font-size : 10px;">Ja/Nee</b><hr></th>
<th>Verkoopdatum<hr></th>
<th onclick="sortTable(2)" ><u>Werknr</u><hr></th>
<th onclick="sortTable(3)"><u>Levensnummer</u><hr></th>
<th>Gewicht<hr></th>
<th>Bestemming<hr></th>
<th colspan = 3 ><hr></th>
</tr>
<?php  
if(isset($data)) {
	foreach($data as $key => $array)
	{
		$Id = $array['schaapId'];
		$werknr = $array['werknr'];
		$levnr = $array['levensnummer'];
		$dmmax = $array['dmlst'];
		$maxdm = $array['lstdm'];


if( (isset($_POST['knpVervers_']) || isset($_POST['knpSave_']) ) && !isset($_POST['kzlRelall_']) ) { 
	$cbKies = $_POST["chbkies_$Id"];
	$datum = $_POST["txtDatum_$Id"];
	$kg = $_POST["txtKg_$Id"];
	if(!empty($_POST["kzlRel_$Id"])) { $bestkeuze = $_POST["kzlRel_$Id"]; } /*Na afleveren en bij tonen van volgende hoeveelheid dieren is $_POST["kzlRel_$Id"] leeg maar $bestkeuze moet blijven bestaan */
	}
// Bij de eerste keer openen van deze pagina bestaat als enigste keer het veld kzlRelall_ . knpVervers_ bestaat als hidden veld. txtDatum_$levnr en txtGewicht_$levnr bestaan dan nog niet. Variabalen $datum en $kg kunnen enkel worden gevuld als wordt voldaan aan (isset($_POST['knpVervers_']) && !isset($_POST['kzlRelall_']))  !!!
	if(!isset($datum) && isset($sess_dag)) { $datum = $sess_dag; }
	if(isset($datum)) /*$datum kan al bestaan voor isset($_POST['knpVervers_']) */ { $makeday = date_create($datum); $day = date_format($makeday,'Y-m-d'); }
// Controleren of ingelezen waardes correct zijn.
	if( empty($datum)				|| # Afleverdatum is leeg
		$day < $dmmax				|| # afleverdag is kleiner dan laatste registratiedatum
		/*empty($kg)				||*/ # Aflevergweicht is leeg
		empty($bestkeuze) 			   # Relatie is leeg

	)
	{$oke = 0; } else { $oke = 1; }
	 
// EINDE Controleren of ingelezen waardes corretc zijn.  
if (isset($_POST['knpVervers_']) && !isset($_POST['kzlRelall_'])) { $cbKies = $_POST["chbkies_$Id"]; $txtOke = $_POST["txtOke_$Id"]; } else { $cbKies = $oke; $txtOke = $oke; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet ?>

<!--	**************************************
	**	   OPMAAK  GEGEVENS		**
	************************************** -->

<tr style = "font-size:14px;">
	<td align = center>
	<input type = hidden size = 1 name = <?php echo "txtOke_$Id"; ?> 	value = <?php echo $oke; ?> ><!--hiddden Dit veld zorgt ervoor dat chbkies wordt aangevinkt als het ingebruk wordt gesteld -->
	<input type = hidden size = 1 name = <?php echo "chbkies_$Id"; ?> value = 0 > <!-- hiddden -->
	<input type = checkbox 		  name = <?php echo "chbkies_$Id"; ?> 	value = 1 <?php echo $cbKies == 1 ? 'checked' : ''; if ($oke <> 1) { ?> disabled <?php }  else if ($txtOke == 0) {	echo 'checked';} ?> >
</td>
<!-- Speendatum -->
<td align = center>
 <input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtDatum_$Id"; ?> value = <?php if(isset($datum)) { echo $datum; } ?> >
</td>

<td width = 80 align = center> <?php echo $werknr; ?>
</td>
</td>

<td width = 110 align = center> <?php echo $levnr; ?>
</td>
	
<td width = 80 align = center style = "font-size : 9px;"> 
<input type = "text" size = 3 style = "font-size : 11px;" name = <?php echo "txtKg_$Id"; ?> value = <?php if(isset($kg)) { echo $kg; } ?> > </td>

<td width = 100 align = center>

<!-- KZLBESTEMMING -->

 <select style="width:150;" name= <?php echo "kzlRel_$Id"; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php
$count = count($relnm);
for ($i = 0; $i < $count; $i++){

	$opties = array($relId[$i]=>$relnm[$i]);
			foreach($opties as $key => $waarde)
			{
  if (( $bestkeuze == $relRaak[$i]) || (isset($_POST["kzlRel_$Id"]) && $_POST["kzlRel_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}
?> </select>

 <!-- EINDE KZLBESTEMMING -->
	

<td colspan = 3 style = "color : red"> 
<?php if($day < $dmmax) { echo 'De datum mag niet voor '.$maxdm.' liggen.';}
 else if(isset($uitvaldm)) { echo 'Dit schaap is reeds overleden.';}
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
Include "menu1.php"; } //include "table_sort.php"; ?>



</body>
</html>
