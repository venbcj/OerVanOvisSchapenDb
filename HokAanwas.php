<?php 
$versie = '17-5-2019'; /* gemaakt als kopie van HokAfleveren */
$versie = '20-12-2019'; /* tabelnaam gewijzigd van UIT naar uit tabelnaam */
$versie = '14-08-2020'; /* geslacht toegevoegd */
  session_start(); ?>
<html>
<head>
<title>Registratie</title>
</head>
<body>

<center>
<?php
include"kalender.php";
$titel = 'Aanwas';
$subtitel = '';
Include "header.php";
?>
		<TD width = 940 height = 400 valign = "top">
<?php 
$file = "HokkenBezet.php";
Include "login.php";
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {


if(isset($_GET['pstId'])) 	{ $_SESSION["ID"] = $_GET['pstId']; } $ID = $_SESSION["ID"]; /* zorgt het Id wordt onthouden bij het opnieuw laden van de pagina */
if(isset($_POST['knpVerder_']) )	{ 
	$datum = $_POST['txtDatumall_']; $_SESSION["DT1"] = $datum; }
  $sess_dag = $_SESSION["DT1"];

if(isset($_POST['knpSave_'])) { include "save_aanwas.php"; }

$zoek_hok = mysqli_query ($db,"
select hoknr from tblHok where hokId = ".mysqli_real_escape_string($db,$ID)."
") or die (mysqli_error($db));
	while ($h = mysqli_fetch_assoc($zoek_hok)) { $hoknr = $h['hoknr']; } ?>

<form action="HokAanwas.php" method = "post"><?php
// Opbouwen paginanummering 
$velden = "s.schaapId, s.levensnummer, s.geslacht, date_format(max(h.datum),'%Y-%m-%d') dmlst, date_format(max(h.datum),'%d-%m-%Y') lstdm";

$tabel = "tblSchaap s 
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblBezet b on (b.hisId = h.hisId)
 left join (
	select b.bezId, h1.hisId hisv, min(h2.hisId) hist
	from tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and b.hokId = ".mysqli_real_escape_string($db,$ID)."
	group by b.bezId, h1.hisId
 ) uit on (uit.bezId = b.bezId)
 join (
	select st.schaapId, h.datum
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 4
 ) spn on (spn.schaapId = st.schaapId)
 left join (
	select st.schaapId, h.datum
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 3
 ) prnt on (prnt.schaapId = st.schaapId)";

 $WHERE = "where b.hokId = ".mysqli_real_escape_string($db,$ID)." and isnull(uit.bezId) and h.skip = 0
 and isnull(prnt.schaapId)";

include "paginas.php";

$data = $page_nums->fetch_data($velden, "GROUP BY s.levensnummer ORDER BY s.levensnummer"); 
// Einde Opbouwen paginanummering 
if(!isset($sess_dag)) { $width = 100; } 
else { $width = 200; } ?>
<table border = 0 > <!-- tabel1 --> <tr> <td>
<table border = 0 > <!-- tabel2 -->
<tr> 
<td width = <?php echo $width; ?> rowspan = 2 style = "font-size : 18px;">
  <b> <?php echo $hoknr; ?></b>
</td>

 <?php if(!isset($sess_dag)) { ?>
 	<td width = 750 style = "font-size : 14px;"> 
 &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp Optioneel een datum voor alle schapen 
  <input id = "datepicker1" type = text name = 'txtDatumall_' size = 8 value = <?php if(isset($sess_dag)) { echo $sess_dag; } ?> > &nbsp 
 <?php } else { ?> <td style = "font-size : 14px;">  <?php } ?>
<!-- Opmaak paginanummering -->
 Regels Per Pagina: <?php echo $kzlRpp;
if(isset($sess_dag)) { ?> </td> <td align = center > <?php echo $page_numbers.'<br>'; ?> </td> <td> <?php } 
// Einde Opmaak paginanummering ?>
 </td>
 <td width = 150 align = center>
<?php if(!isset($sess_dag)) { ?>
  &nbsp &nbsp &nbsp <input type = submit name = "knpVerder_" value = "Verder">
 </td>
 <td width = 200 align = 'right'></td>
   <?php }
else { ?>
  <input type = submit name = "knpVervers_" value = "Verversen"> 
 </td>
 <td width = 200 align = 'right'>
  <input type = submit name = "knpSave_" value = "Opslaan">&nbsp &nbsp
 </td> <?php } ?>
</tr>

<tr><td colspan = 7 align = left >
  
</td><td></td></tr>
</table> <!-- einde tabel2 --> </td> </tr>
								<tr> <td>
<table border = 0 align = left > <!-- tabel3 --> 
<?php if(isset($sess_dag)) { ?>
<tr valign = bottom style = "font-size : 12px;">
<th>Aanwas<br><b style = "font-size : 10px;">Ja/Nee</b><hr></th>
<th>Aanwasdatum<hr></th>
<th>Levensnummer<hr></th>
<th>Geslacht<hr></th>
<th>Gewicht<hr></th>
<th><hr></th>
<th colspan = 3 ><hr></th>
</tr>
<?php  
if(isset($data)) {
	foreach($data as $key => $array)
	{
		$Id = $array['schaapId'];
		$levnr = $array['levensnummer'];
		$geslacht = $array['geslacht'];
		$dmmax = $array['dmlst'];
		$maxdm = $array['lstdm'];


if( (isset($_POST['knpVervers_']) || isset($_POST['knpSave_']) ) ) { 
	$cbKies = $_POST["chbkies_$Id"];
	$datum = $_POST["txtDatum_$Id"];
	$kg = $_POST["txtKg_$Id"];
	}
// Bij de eerste keer openen van deze pagina bestaat als enigste keer het veld kzlRelall_ . knpVervers_ bestaat als hidden veld. txtDatum_$levnr en txtGewicht_$levnr bestaan dan nog niet. Variabalen $datum en $kg kunnen enkel worden gevuld als wordt voldaan aan (isset($_POST['knpVervers_']) && !isset($_POST['kzlRelall_']))  !!!
	if(!isset($datum) && isset($sess_dag)) { $datum = $sess_dag; }
	if(isset($datum)) /*$datum kan al bestaan voor isset($_POST['knpVervers_']) */ { $makeday = date_create($datum); $day = date_format($makeday,'Y-m-d'); }
// Controleren of ingelezen waardes correct zijn.
	if( empty($datum)				|| # Aanwasdatum is leeg
		$day < $dmmax				#||  afleverdag is kleiner dan laatste registratiedatum
		/*empty($kg)				||*/ # Aanwasgweicht is leeg

	)
	{$oke = 0; } else { $oke = 1; }
	 
// EINDE Controleren of ingelezen waardes corretc zijn.  
if (isset($_POST['knpVervers_']) ) { $cbKies = $_POST["chbkies_$Id"]; $txtOke = $_POST["txtOke_$Id"]; } else { $cbKies = $oke; $txtOke = $oke; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet ?>

<!--	**************************************
	**	   OPMAAK  GEGEVENS		**
	************************************** -->

<tr style = "font-size:14px;">
	<td align = center>
	<input type = hidden size = 1 name = <?php echo "txtOke_$Id"; ?> 	value = <?php echo $oke; ?> ><!--hiddden Dit veld zorgt ervoor dat chbkies wordt aangevinkt als het ingebruk wordt gesteld -->
	<input type = hidden size = 1 name = <?php echo "chbkies_$Id"; ?> value = 0 > <!-- hiddden -->
	<input type = checkbox 		  name = <?php echo "chbkies_$Id"; ?> 	value = 1 <?php echo $cbKies == 1 ? 'checked' : ''; if ($oke <> 1) { ?> disabled <?php }  else if ($txtOke == 0) {	echo 'checked';} ?> >
</td>
<!-- Aanwasdatum -->
<td align = center>
 <input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtDatum_$Id"; ?> value = <?php if(isset($datum)) { echo $datum; } ?> >
</td>

<td width = 110 align = center> <?php echo $levnr; ?>
</td>

<td width = 50 align = center> <?php echo $geslacht; ?>
</td>
	
<td width = 80 align = center style = "font-size : 9px;"> 
<input type = "text" size = 3 style = "font-size : 11px;" name = <?php echo "txtKg_$Id"; ?> value = <?php if(isset($kg)) { echo $kg; } ?> > </td>

<td width = 100 align = center>	

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
Include "menu1.php"; } ?>
</body>
</html>
